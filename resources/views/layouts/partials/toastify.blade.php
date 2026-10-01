@if (session()->has('success') || session()->has('error') || session()->has('info') || session()->has('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notifications = [
                {type: 'success', text: @json(session('success')), bg: '#10B981'},
                {type: 'error', text: @json(session('error')), bg: '#EF4444'},
                {type: 'info', text: @json(session('info')), bg: '#3B82F6'},
                {type: 'warning', text: @json(session('warning')), bg: '#F59E0B'}
            ];

            notifications.forEach(item => {
                if (item.text) {
                    Toastify({
                        text: item.text,
                        duration: 4000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: item.bg,
                            borderRadius: "6px",
                            boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)"
                        }
                    }).showToast();
                }
            });
        });
    </script>
@endif
<script>
    // Global helper function to trigger toasts dynamically anywhere in JS / Alpine
    window.showToast = function(message, bg = '#10B981') {
        if (typeof Toastify === 'function') {
            Toastify({
                text: message,
                duration: 4000,
                close: true,
                gravity: "top",
                position: "right",
                stopOnFocus: true,
                style: {
                    background: bg,
                    borderRadius: "6px",
                    boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)"
                }
            }).showToast();
        }
    };
</script>
