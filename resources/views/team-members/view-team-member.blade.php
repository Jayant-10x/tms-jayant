@extends('layouts.vertical', ['title' => 'View Team Member','subTitle' => 'View Team Member'])
@push('css')
    @vite('resources/css/team-members.css')
@endpush
@section('content')
    @php
        $member_photo = asset('images/users/dummy-avatar.jpg');
        if(!empty($member_info['emp_photo'])) {
            $member_photo = asset('storage/employees/'. $member_info['emp_photo']);
        }
        $total_task_count = $emp_task_statistics['total_task'];
        $completed_task_count = $emp_task_statistics['completed_task'];

        $remaining_tasks_count = $total_task_count - $completed_task_count;
        $completion_percentage = $total_task_count > 0 ? round(($completed_task_count / $total_task_count) * 100) : 0;
    @endphp

    <div class="card p-2" style="box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;">
        <div class="card-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="position-relative d-inline-block">
                                @if(!empty($member_info['emp_photo']))
                                    <a href="{{$member_photo}}" target="_blank">
                                        @endif
                                        <img src="{{$member_photo}}"
                                             class="avatar-lg rounded-3 border border-light border-3 object-fit-cover"
                                             style="height: 120px; width: 120px; border-radius: 50% !important;">
                                        @if(!empty($member_info['emp_photo']))
                                    </a>
                                @endif
                                @isset($member_info['admin_user_details'])
                                    {!! user_online_status_dot($member_info['admin_user_details']['adm_id']) !!}
                                @endisset
                            </div>
                        </div>
                        <div class="col-md-10 align-content-center">
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="fs-4 text-dark fw-bold">{{$member_info['emp_full_name']}}</span>
                                </div>
                                <div class="col-md-12">
                                    <span
                                        class="fs-5">{{\App\Enums\DesignationEnum::tryFrom($member_info['emp_designation'])->label()}}</span>
                                </div>
                                <div class="col-md-12">
                                    @php
                                        $departmentEnum = \App\Enums\DepartmentsEnum::class;
                                    @endphp
                                    <span
                                        class="fs-5">{{$departmentEnum::tryFrom($member_info['emp_department'])->label() . (!empty($member_info['emp_sub_department']) ? ' (' . $departmentEnum::tryFrom($member_info['emp_sub_department'])->label() .')' : '')}}</span>
                                </div>
                                <div class="col-md-12"><span style="font-size: 14px"><iconify-icon
                                            icon="solar:letter-broken" class="align-middle fs-5"></iconify-icon> {{$member_info['emp_email']}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 text-end align-content-center">
                    @isset($member_info['admin_user_details'])
                        <div class="mb-2">
                            {!! \App\Enums\UserRoleEnum::tryFrom($member_info['admin_user_details']['adm_role'])->badge() !!}
                        </div>
                        {!! user_online_status_badge($member_info['admin_user_details']['adm_id']) !!}
                    @endisset
                </div>
            </div>
        </div>
    </div>
    {{--    Statics card   --}}
    <div class="row g-3 mb-4">
        <!-- Active -->
        <div class="col-6 col-lg-3">
            <div class="team-stat-card stat-purple">
                <div class="team-stat-icon">
                    <i class="bi bi-list-task"></i>
                </div>
                <div class="team-stat-number">
                    {{$total_task_count}}
                </div>
                <div class="team-stat-label">
                    Active Tasks
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="col-6 col-lg-3">
            <div class="team-stat-card stat-green">
                <div class="team-stat-icon">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="team-stat-number">
                    {{$completed_task_count}}
                </div>
                <div class="team-stat-label">
                    Completed
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-6 col-lg-3">
            <div class="team-stat-card stat-yellow">
                <div class="team-stat-icon">
                    <i class="bi bi-fire"></i>
                </div>
                <div class="team-stat-number">
                    {{$remaining_tasks_count}}
                </div>
                <div class="team-stat-label">
                    Pending
                </div>
            </div>
        </div>

        <!-- Completion -->
        <div class="col-6 col-lg-3">
            <div class="team-stat-card stat-pink">
                <div class="team-stat-icon">
                    <i class="bi bi-percent"></i>
                </div>
                <div class="team-stat-number">
                    {{$completion_percentage}}
                </div>
                <div class="team-stat-label">
                    Completion Rate
                </div>
            </div>
        </div>
    </div>

    {{--  Task Table  --}}
    <div class="card">
        <div class="card-header">
            <span class="fs-5 fw-bold">Assigned Tasks</span>
        </div>
        <div class="card-body pt-0">
            @if(!empty($emp_tasks) && count($emp_tasks) > 0)
                <div class="table-responsive">
                    <table class="table align-middle text-nowrap table-hover table-centered mb-0">
                        <thead class="table-light">
                        <tr>
                            <th width="4%">Sr. no.</th>
                            <th width="30%">Task</th>
                            <th width="15%">Project</th>
                            <th width="12%">Priority</th>
                            <th width="12%">Due Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($emp_tasks as $emp_task)
                            <tr>
                                <td>{{$loop->iteration}}.</td>
                                <td>
                                    {{generate_shorten_string($emp_task->prt_title)}}
                                </td>
                                <td>{{$emp_task->pro_name}}</td>
                                <td>
                                    <span
                                        class="badge badge-soft-{{$emp_task->prt_priority?->color()}} badge-outline-{{$emp_task->prt_priority?->color()}} rounded-pill me-1 fs-6"><iconify-icon
                                            icon="solar:flag-2-broken" class="align-middle fs-7"></iconify-icon>{!! $emp_task->prt_priority?->label() !!}
                                    </span>
                                </td>
                                <td>{{get_date_time_format($emp_task->prt_due_date)}}</td>
                                <td>
                                    <span
                                        class="badge badge-soft-{{$emp_task->prt_status?->color()}} badge-outline-{{$emp_task->prt_status?->color()}} rounded-pill me-1 fs-6">{{$emp_task->prt_status?->label()}}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(permission_can('all_tasks', 'view') || is_admin())
                                            {!! generate_view_button(route('task.view', array_merge(['prt_id' => my_encrypt($emp_task->prt_id), 'called_from'=> 'team_member', 'return_url' => url()->full()],request()->query()))) !!}
                                        @else
                                            -
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

                {!! generate_pagination($emp_tasks,"Tasks") !!}
            @else
                {!! generate_no_record_html(class: 'p-0') !!}
            @endif
        </div>
    </div>
@endsection

@section('module-right-section')
    @php
        if($called_from == 'project_team' && !empty($pro_id))
            $back_url = route('project.view',array_merge(['pro_id' => $pro_id],request()->query()));
        else
            $back_url = route('team_members.list',request()->query());
    @endphp

    {!! generate_back_to_list_button($back_url) !!}
@endsection
