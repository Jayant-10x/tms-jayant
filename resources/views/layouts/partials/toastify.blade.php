<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notifications = [
            {type: 'success', text: @json(session('success')) },
            {type: 'error', text: @json(session('error')) },
            {type: 'info', text: @json(session('info')) },
            {type: 'warning', text: @json(session('warning')) },
        ];

        notifications.forEach(({type, text}) => {
            if (text) {
                window.showToast(text, type);
            }
        });
    });
</script>
