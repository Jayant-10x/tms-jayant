@php
    $taskStatusEnum = \App\Enums\TaskStatus::class;
@endphp
<style>
    /* Kanban Column Base Styles */
    .kanban-column {
        background-color: #f4f5f7;
        border-radius: 12px;
        padding: 16px;
        border-top: 4px solid transparent;
        min-height: 400px;
    }

    /* Column Top Border Colors */
    .column-todo {
        border-top-color: #9aa5b5;
    }

    .column-in-progress {
        border-top-color: #2563eb;
    }

    .column-review {
        border-top-color: #a855f7;
    }

    .column-completed {
        border-top-color: #22c55e;
    }

    /* Drop Zone Area */
    .drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        color: #6b7280;
        font-weight: 500;
        font-size: 0.9rem;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Task Card Styles */
    .kanban-card {
        background: #ffffff;
        border-radius: 10px;
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }

    .kanban-card .drag-handle {
        color: #9ca3af;
        cursor: grab;
    }

    .avatar-circle {
        width: 28px;
        height: 28px;
        background-color: #1d4ed8;
        color: #ffffff;
        border-radius: 50%;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .badge-count {
        background-color: #e5e7eb;
        color: #4b5563;
        font-size: 0.75rem;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .badge-urgent {
        color: #dc2626;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-reporting {
        background-color: #f3f4f6;
        color: #6b7280;
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 4px;
    }
</style>
@php
    $columns = [
        [
            'title' => 'To Do',
            'status' => $taskStatusEnum::TODO->value,
            'class' => 'column-todo',
        ],
        [
            'title' => 'In Progress',
            'status' => $taskStatusEnum::IN_PROGRESS->value,
            'class' => 'column-in-progress',
        ],
        [
            'title' => 'Review',
            'status' => $taskStatusEnum::REVIEW->value,
            'class' => 'column-review',
        ],
        [
            'title' => 'Completed',
            'status' => $taskStatusEnum::COMPLETED->value,
            'class' => 'column-completed',
        ],
    ];
@endphp

<div class="row g-3">
    @foreach($columns as $col)
        @php
            $tasks = $tasks_by_status[$col['status']] ?? collect();
        @endphp
        <div class="col-12 col-sm-6 col-md-3">
            <div class="kanban-column {{ $col['class'] }}" data-status="{{ $col['status'] }}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark">{{ $col['title'] }}</h6>
                    <span class="badge-count">{{ count($tasks) }}</span>
                </div>

                @forelse($tasks as $task)
                    @php
                        $completed_subtask = $task->subTasks->where('pst_is_done', 1)->count();
                    @endphp
                    <div class="kanban-card mb-2" id="card-{{ my_encrypt($task->prt_id) }}"
                         data-task-id="{{ my_encrypt($task->prt_id) }}"
                         draggable="true">
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <iconify-icon icon="bx:dots-vertical-rounded" class="drag-handle fs-5 mt-0"></iconify-icon>
                            <p class="fw-bold mb-0 text-dark lh-sm fs-6">
                                <a href="{{ route('task.view', ['prt_id' => my_encrypt($task->prt_id),'called_from'=> 'my_task'])}}?return_url={{ urlencode(url()->full()) }}"
                                   target="_blank">
                                    {{ generate_shorten_string($task->prt_title) }}
                                </a>
                            </p>
                        </div>

                        <p class="text-muted small mb-2 ps-4">{{ generate_shorten_string($task->project->pro_name ?? '') }}</p>

                        <div class="d-flex align-items-center gap-2 mb-3 ps-4">
                            @if($task->prt_priority)
                                <span
                                    class="badge badge-soft-{{ $task->prt_priority->color() }} badge-outline-{{ $task->prt_priority->color() }} rounded-pill me-1 fs-6">
                                    <iconify-icon icon="solar:flag-2-broken" class="align-middle fs-7"></iconify-icon>
                                    {!! $task->prt_priority->label() !!}
                                </span>
                            @endif
                            @if($task->prt_category)
                                <span class="badge-reporting">{{ $task->prt_category->label() }}</span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 ps-4 pe-1">
                            <span class="text-danger small d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="bx:calendar"></iconify-icon>
                                {{ get_date_time_format($task->prt_due_date) }}
                            </span>
                            <span class="text-muted small d-inline-flex align-items-center gap-1">
                                {{ $completed_subtask }} / {{ count($task->subTasks) }}
                                <iconify-icon icon="bx:check"></iconify-icon>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="drop-zone">Drop tasks here</div>
                @endforelse
            </div>
        </div>
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const columns = document.querySelectorAll('.kanban-column');

        // 1. Drag Events
        document.addEventListener('dragstart', (e) => {
            const card = e.target.closest('.kanban-card');
            if (card) {
                e.dataTransfer.setData('text/plain', card.id);
                card.classList.add('opacity-50');
            }
        });

        document.addEventListener('dragend', (e) => {
            const card = e.target.closest('.kanban-card');
            if (card) {
                card.classList.remove('opacity-50');
            }
        });

        // 2. Drop Zone handling
        columns.forEach(column => {
            column.addEventListener('dragover', (e) => {
                e.preventDefault();
                column.style.backgroundColor = '#e5e7eb';
            });

            column.addEventListener('dragleave', () => {
                column.style.backgroundColor = '#f4f5f7';
            });

            column.addEventListener('drop', async (e) => {
                e.preventDefault();
                column.style.backgroundColor = '#f4f5f7';

                const cardId = e.dataTransfer.getData('text/plain');
                const draggedCard = document.getElementById(cardId);

                if (draggedCard) {
                    const taskId = draggedCard.dataset.taskId;
                    const newStatus = column.dataset.status;

                    // Move UI elements first
                    const dropZone = column.querySelector('.drop-zone');
                    if (dropZone) dropZone.remove();

                    column.appendChild(draggedCard);
                    updateBadgeCounts();

                    // Send AJAX update to Laravel backend
                    try {
                        const response = await fetch('/update-task-status/' + taskId, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{csrf_token()}}'
                            },
                            body: JSON.stringify({
                                task_id: taskId,
                                status: newStatus
                            })
                        });

                        const data = await response.json();

                        if (!data.status) {
                            showNotification(data.message, 'error');
                        } else {
                            showNotification(data.message, 'success');
                            setTimeout(function () {
                                window.location.reload();
                            }, 5000);

                        }
                    } catch (error) {
                        console.error('Error updating task status:', error);
                    }
                }
            });
        });

        function updateBadgeCounts() {
            columns.forEach(col => {
                const countBadge = col.querySelector('.badge-count');
                const cardCount = col.querySelectorAll('.kanban-card').length;

                if (countBadge) countBadge.textContent = cardCount;

                if (cardCount === 0 && !col.querySelector('.drop-zone')) {
                    const emptyZone = document.createElement('div');
                    emptyZone.className = 'drop-zone';
                    emptyZone.textContent = 'Drop tasks here';
                    col.appendChild(emptyZone);
                }
            });
        }
    });
</script>
