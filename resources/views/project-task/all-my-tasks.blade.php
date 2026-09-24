@extends('layouts.vertical', ['title' => 'All My Tasks','subTitle' => 'All My Tasks'])

@section('content')
    <div class="card">
        {{--<div class="card-header d-flex justify-content-between align-items-center border-bottom">
            <!--Filters-->
        </div>--}}
        <div class="card-body p-0">
            @if(!empty($all_assigned_to_me) && count($all_assigned_to_me) > 0)
                <div class="table-responsive">
                    <table class="table align-middle text-nowrap table-hover table-centered mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Sr. no.</th>
                            <th width="20%">Task</th>
                            <th width="10%">Project</th>
                            <th width="12%" class="text-center">Assigned By</th>
                            <th width="8%">Priority</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($all_assigned_to_me as $task)
                            <tr>
                                <td>{{$loop->iteration}}.</td>
                                <td>
                                    {{generate_shorten_string($task->prt_title, 40)}}
                                </td>
                                <td>{{$task->project->pro_name}}</td>
                                <td class="text-center">
                                    {{--<span data-bs-toggle="tooltip" data-bs-placement="top"
                                          data-bs-title="{{$task->assignees}}"
                                          data-bs-container="body"
                                          class="d-inline-flex align-middle">
                                        <iconify-icon icon="solar:info-circle-bold"
                                                      class="fs-14 text-warning"></iconify-icon>
                                    </span>--}}
                                    {{$task->emp_full_name}}
                                </td>
                                <td><span
                                        class="badge badge-soft-{{$task->prt_priority?->color()}} badge-outline-{{$task->prt_priority?->color()}} rounded-pill me-1 fs-6"><iconify-icon
                                            icon="solar:flag-2-broken" class="align-middle fs-7"></iconify-icon>{!! $task->prt_priority?->label() !!}</span>
                                </td>
                                <td>{!! get_date_time_format($task->prt_due_date) !!}</td>
                                <td>
                                    <span
                                        class="badge badge-soft-{{$task->prt_status?->color()}} badge-outline-{{$task->prt_status?->color()}} rounded-pill me-1 fs-6">{{$task->prt_status?->label()}}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(permission_can('all_my_tasks', 'view'))
                                            {!! generate_view_button(route('task.view', [ 'prt_id' => my_encrypt($task->prt_id), 'called_from'=> 'all_my_tasks','return_url' => url()->full()])) !!}
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
            @else
                {!! generate_no_record_html() !!}
            @endif
            <!-- end table-responsive -->
        </div>
        @if(!empty($all_assigned_to_me) && count($all_assigned_to_me) > 0)
            <div class="card-footer">
                <div>
                    Showing
                    {{ $all_assigned_to_me->firstItem() }}
                    to
                    {{ $all_assigned_to_me->lastItem() }}
                    of
                    {{ $all_assigned_to_me->total() }}
                    tasks
                </div>

                <div>
                    {{ $all_assigned_to_me->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
