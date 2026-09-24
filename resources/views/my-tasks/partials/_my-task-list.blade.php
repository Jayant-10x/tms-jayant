@if(!empty($my_tasks) && count($my_tasks) > 0)
    <div class="table-responsive">
        <table class="table align-middle text-nowrap table-hover table-centered mb-0">
            <thead class="table-light">
            <tr>
                <th width="5%">Sr. no.</th>
                <th width="20%">Task</th>
                <th width="15%">Project</th>
                <th>Priority</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($my_tasks as $task)
                <tr>
                    <td>{{$loop->iteration}}.</td>
                    <td>
                        {{generate_shorten_string($task->prt_title)}}
                    </td>
                    <td>
                        {{$task->project->pro_name}}
                        {{--@php
                                $assignees_html = '<span>';
                                foreach ($task->assignees as $assignee){
                                    $assignees_html.= '<p>'.$assignee.'</p>';
                                }
                                $assignees_html .= '</span>';
                            @endphp--}}
                        {{--<span data-bs-toggle="tooltip" data-bs-placement="top"
                              data-bs-title="{{$task->assignees}}"
                              data-bs-container="body"
                              class="d-inline-flex align-middle">
                        <iconify-icon icon="solar:info-circle-bold" class="fs-14 text-warning"></iconify-icon>
                    </span>--}}
                    </td>
                    <td>
                    <span
                        class="badge badge-soft-{{$task->prt_priority?->color()}} badge-outline-{{$task->prt_priority?->color()}} rounded-pill me-1 fs-6"><iconify-icon
                            icon="solar:flag-2-broken" class="align-middle fs-7"></iconify-icon>{!! $task->prt_priority?->label() !!}
                    </span>
                    </td>
                    <td>
                        {{get_date_time_format($task->prt_due_date)}}
                    </td>
                    <td>
                    <span
                        class="badge badge-soft-{{$task->prt_status?->color()}} badge-outline-{{$task->prt_status?->color()}} rounded-pill me-1 fs-6">{{$task->prt_status?->label()}}
                    </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            @if(permission_can('all_my_tasks', 'view') || is_admin())
                                <div class="d-flex gap-2">
                                    {!! generate_view_button(route('task.view', ['prt_id' => my_encrypt($task->prt_id),'called_from' => 'my_task', 'return_url' => url()->full()])) !!}
                                </div>
                            @endif
                            {{--{!! generate_edit_button(route('employees.edit', ['emp_id' => my_encrypt($employee->emp_id)])) !!}
                                {!! generate_delete_button(route('employees.list')) !!}--}}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>
        Showing
        {{ $my_tasks->firstItem() }}
        to
        {{ $my_tasks->lastItem() }}
        of
        {{ $my_tasks->total() }}
        tasks
    </div>

    <div>
        {{ $my_tasks->links() }}
    </div>
@else
    {!! generate_no_record_html(class:'p-0') !!}
@endif
