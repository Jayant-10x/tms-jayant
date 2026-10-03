<div class="card task-main-cards">
    <div class="card-header pb-1 fs-16 fw-bold text-dark"
         style="border-bottom: #ededed 3px solid !important;">
        <i class="bi bi-chat-dots"></i>
        Comments
    </div>
    <div class="card-body" style="padding-top: -10px;">
        @if(count($task_data->comments) > 0)
            <!-- Chat Conversation List -->
            <ul class="chat-conversation-list p-3 chatbox-height overflow-y-auto"
                style="max-height: 500px;">
                @foreach($task_data->comments as $item)
                    @php
                        $commenter_avtar = asset('images/users/avatar-1.jpg');
                        $isMyMessage = $item->user_id == auth()->user()->adm_id;
                        $commenter_name = $item->user->adm_name;
                        $commentDate = \Carbon\Carbon::parse($item->created_at);

                        if(empty($item->user->employee) && !empty($item->user->adm_photo)) {
                            $commenter_avtar = asset('/storage/admin/'.$item->user->adm_photo);
                        }
                        if(!empty($item->user->employee) && !empty($item->user->employee->emp_photo)) {
                            $commenter_avtar = asset('/storage/employees/'.$item->user->employee->emp_photo);
                        }
                    @endphp
                    <li class="d-flex gap-2 clearfix mb-3 {{ $isMyMessage ? 'justify-content-end odd' : '' }}">
                        <!-- Avatar (Left for Others) -->
                        @if(!$isMyMessage)
                            <div class="chat-avatar text-center">
                                <img
                                    src="{{ $commenter_avtar }}"
                                    alt="{{ $item->user->name ?? 'User' }}"
                                    class="avatar rounded-circle"
                                    style="width: 36px; height: 36px; object-fit: cover;"
                                >
                            </div>
                        @endif

                        <!-- Conversation Text Box -->
                        <div class="chat-conversation-text {{ $isMyMessage ? 'ms-0' : '' }}">
                            <!-- Header: Author & Time -->
                            <div>
                                <p class="mb-2">
                                    @if(!$isMyMessage)
                                        <span>
                                            <span class="text-dark fw-medium me-1">
                                                {{ $commenter_name}}
                                            </span>

                                            <span class="text-muted fs-12">
                                                @if($commentDate->isToday())
                                                    Today, {{ $commentDate->format('h:i A') }}
                                                @elseif($commentDate->isYesterday())
                                                    Yesterday, {{ $commentDate->format('h:i A') }}
                                                @else
                                                    {{ $commentDate->format('d M Y, h:i A') }}
                                                @endif
                                            </span>
                                        </span>
                                    @else
                                        <span class="d-flex justify-content-end">
                                            <span class="text-muted fs-12 me-1">
                                                @if($commentDate->isToday())
                                                    Today, {{ $commentDate->format('h:i A') }}
                                                @elseif($commentDate->isYesterday())
                                                    Yesterday, {{ $commentDate->format('h:i A') }}
                                                @else
                                                    {{ $commentDate->format('d M Y, h:i A') }}
                                                @endif
                                            </span>

                                            <span class="text-dark fw-medium ms-1">
                                                You
                                            </span>
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <!-- Message Bubble with Hover Copy Icon -->
                            <div
                                class="d-flex align-items-center gap-1 message-bubble-wrapper {{ $isMyMessage ? 'flex-row-reverse' : '' }}">
                                <!-- Chat Message Bubble -->
                                <div class="chat-ctext-wrap">
                                    <p class="mb-0">
                                        {{ $item->comment ?? $item->message ?? $item->prt_comment }}
                                    </p>
                                </div>

                                <!-- Hover Copy Button -->
                                <button
                                    type="button"
                                    class="btn btn-link btn-copy-icon p-0 text-muted shadow-none opacity-0"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="Copy message"
                                    onclick="copyMessage(this)"
                                    data-message="{{ $item->comment ?? $item->message ?? $item->prt_comment }}"
                                >
                                    <i class="ri-file-copy-line fs-16"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Avatar (Right for Logged-In User) -->
                        @if($isMyMessage)
                            <div class="chat-avatar text-center">
                                <img
                                    src="{{ $commenter_avtar }}"
                                    alt="You"
                                    class="avatar rounded-circle"
                                    style="width: 36px; height: 36px; object-fit: cover;"
                                >
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center">
                <img src="{{asset('images/task-comment-init.gif')}}" alt="task-comment-init" style="width: 30%">
            </div>
        @endif
        <!-- Input Form -->
        <div class="bg-opacity-50 p-2 border-top">
            <form method="POST" action="{{ route('tasks.comments.store', $task_data->prt_id) }}"
                  name="task-chat-form" id="task-chat-form">
                @csrf
                <div class="row align-items-start">
                    <div class="col mb-2 mb-sm-0">
                        <div class="input-group">
                            <input
                                type="text"
                                name="task_comment"
                                class="form-control border-0 bg-primary-subtle"
                                placeholder="Enter your comment......"
                                autocomplete="off"
                                required
                                style="border-radius: 50rem 20rem 20rem 50rem;"
                            >
                        </div>

                        <!-- Dedicated container for jQuery/Blade validation errors -->
                        <div id="task-comment-error-container">
                            @error('task_comment')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-auto ps-0">
                        <div class="d-flex gap-2">
                            <button
                                type="submit"
                                class="btn btn-primary chat-send d-flex align-items-center justify-content-center"
                                data-bs-toggle="tooltip"
                                data-bs-title="Send"
                                style="border-radius: 20rem 50rem 50rem 20rem; padding: 0.6rem 1rem"
                            >
                                <iconify-icon icon="ri:send-ins-line" class="fs-16"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function copyMessage(button) {
        const message = button.getAttribute('data-message');

        if (!message) {
            showToast('Nothing to copy', 'warning');
            return;
        }

        navigator.clipboard.writeText(message)
            .then(() => {
                showToast('Comment copied to clipboard!', 'success');
            })
            .catch(err => {
                showToast('Something went wrong.', 'error');
            });
    }
</script>
