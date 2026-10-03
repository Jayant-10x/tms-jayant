import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import Alpine from 'alpinejs';

window.Pusher = Pusher;
window.Alpine = Alpine;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 80),
    wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 443),
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
});


document.addEventListener('alpine:init', () => {
    Alpine.data('taskChat', (taskId, currentUserId, initialComments) => ({
        taskId: taskId,
        currentUserId: currentUserId,
        comments: initialComments || [],
        newComment: '',
        isSubmitting: false,

        init() {
            // Scroll to bottom on initial load
            this.scrollToBottom();

            // Listen to WebSockets via Laravel Echo
            if (window.Echo) {
                window.Echo.private(`task.${this.taskId}`)
                    .listen('.comment.created', (e) => {
                        // Avoid duplicate messages if the current user sent it
                        const isDuplicate = this.comments.some(c =>
                            (c.id && e.comment.id && c.id === e.comment.id) ||
                            (c.prt_comment_id && e.comment.prt_comment_id && c.prt_comment_id === e.comment.prt_comment_id)
                        );

                        if (!isDuplicate) {
                            this.comments.push(e.comment);
                            this.scrollToBottom();
                        }
                    });
            } else {
                console.warn('Laravel Echo is not initialized on window.Echo');
            }
        },

        getMessageText(item) {
            return item.comment || item.comment_text || item.prt_comment || item.message || '';
        },

        isMyMessage(item) {
            const authorId = item.user_id || item.adm_id || (item.user ? item.user.adm_id : null);
            return parseInt(authorId) === parseInt(this.currentUserId);
        },

        getUserName(item) {
            if (this.isMyMessage(item)) return 'You';
            if (item.user) {
                return item.user.adm_name || item.user.name || item.user.username || 'User';
            }
            return 'User';
        },

        getUserAvatar(item) {
            const user = item.user || item;
            if (!user) return '/images/users/dummy-avatar.jpg';

            const employee = user.employee || {};
            const empId = user.adm_emp_id || employee.id || employee.emp_id;
            const empPhoto = employee.emp_photo || user.emp_photo;
            const admPhoto = user.adm_photo;

            if (empId && empPhoto) {
                if (empPhoto.startsWith('http://') || empPhoto.startsWith('https://') || empPhoto.startsWith('/')) {
                    return empPhoto;
                }
                if (empPhoto.startsWith('employees/')) {
                    return `/storage/${empPhoto}`;
                }
                return `/storage/employees/${empPhoto}`;
            }

            if (admPhoto) {
                if (admPhoto.startsWith('http://') || admPhoto.startsWith('https://') || admPhoto.startsWith('/')) {
                    return admPhoto;
                }
                if (admPhoto.startsWith('admin/')) {
                    return `/storage/${admPhoto}`;
                }
                return `/storage/admin/${admPhoto}`;
            }

            return '/images/users/dummy-avatar.jpg';
        },

        formatTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
        },

        async copyToClipboard(item) {
            const text = this.getMessageText(item);
            if (!text) return;

            try {
                await navigator.clipboard.writeText(text);
                console.log(window.showToast);
                if (typeof window.showToast === 'function') {
                    window.showToast('Copied to clipboard!', '#10B981');
                }
            } catch (err) {
                console.error('Failed to copy text: ', err);
            }
        },

        async sendMessage() {
            if (!this.newComment.trim() || this.isSubmitting) return;

            this.isSubmitting = true;
            const content = this.newComment;
            this.newComment = '';

            try {
                const response = await fetch(`/tasks/${this.taskId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ comment: content })
                });

                if (response.ok) {
                    const data = await response.json();

                    // Push immediately if not already added by Echo broadcast
                    const isAlreadyAdded = this.comments.some(c =>
                        (c.id && data.id && c.id === data.id) ||
                        (c.prt_comment_id && data.prt_comment_id && c.prt_comment_id === data.prt_comment_id)
                    );

                    if (!isAlreadyAdded) {
                        this.comments.push(data);
                        this.scrollToBottom();
                    }
                } else {
                    this.newComment = content;
                    if (typeof window.showToast === 'function') {
                        window.showToast('Failed to send comment', '#EF4444');
                    }
                }
            } catch (error) {
                console.error('Error sending comment:', error);
                this.newComment = content;
            } finally {
                this.isSubmitting = false;
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const chatBox = this.$refs.chatBox;
                if (chatBox) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            });
        }
    }));
});

Alpine.start();
