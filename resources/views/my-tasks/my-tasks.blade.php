@extends('layouts.vertical', ['title' => 'My Task','subTitle' => 'My Task'])
@push('css')
    @vite('resources/css/project.css')
@endpush
@section('content')
    @php
        $task_statistics = $task_statistics[get_logged_in_user_emp_id()];
        $total_task = $task_statistics['total_tasks'] ?? 0;
        $completed_task = $task_statistics['completed_tasks'] ?? 0;
        $overall_progress = $total_task > 0 ? ($completed_task / $total_task) * 100 : 0;
    @endphp
    <div>
        <div class="row">
            <div class="col-md-3 mb-sm-3">
                <div class="mini-card card-purple">

                    <div class="mini-icon">
                        <i class="ri-target-line"></i>
                    </div>

                    <div class="mini-number">
                        {{$task_statistics['today_tasks'] ?? 0}}
                    </div>

                    <div class="mini-label">
                        Today
                    </div>

                </div>
            </div>
            <div class="col-md-3 mb-sm-3">
                <div class="mini-card card-yellow">
                    <div class="mini-icon">
                        <i class="ri-calendar-schedule-line"></i>
                    </div>
                    <div class="mini-number">
                        {{$task_statistics['upcoming_tasks']}}
                    </div>
                    <div class="mini-label">
                        Upcoming
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-sm-3">
                <div class="mini-card card-pink">
                    <div class="mini-icon">
                        <i class="bi bi-fire"></i>
                    </div>
                    <div class="mini-number">
                        {{$task_statistics['overdue_tasks']}}
                    </div>
                    <div class="mini-label">
                        Overdue
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-sm-3">
                <div class="mini-card card-green">
                    <div class="mini-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="mini-number">
                        {{$task_statistics['completed_tasks'] ?? 0}}
                    </div>
                    <div class="mini-label">
                        Completed
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4 p-2">
            <div class="progress-card card">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row justify-content-between mb-1">
                            <div class="col-md-4 fs-5 text-dark">Overall Progress</div>
                            <div class="col-md-2 fs-5 text-primary fw-bold text-end"
                                 id="task-percentage">{{$overall_progress}}%
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="progress">
                                    <div
                                        id="task-progress-bar"
                                        class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                                        role="progressbar" style="width: {{$overall_progress}}%"
                                        aria-valuenow="{{$overall_progress}}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between mt-2">
                            <div class="col-md-4 fs-6 text-dark">{{$completed_task}}
                                of {{$total_task}} tasks completed
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs" style="margin-top: -10px !important;">
                            <li class="nav-item">
                                <a href="#my_task_list" data-bs-toggle="tab" aria-expanded="false"
                                   class="nav-link active">
                                    <i class="ri-list-ordered"
                                       style="margin-right: 5px; padding: 5px; font-size: 17px"></i>
                                    <span>List</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#my_task_kanban" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                    <i class="ri-dashboard-line"
                                       style="margin-right: 5px; padding: 5px; font-size: 17px"></i><span>Board</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#my_task_calendar" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                    <i class="ri-calendar-event-line"
                                       style="margin-right: 5px; padding: 5px; font-size: 17px"></i>
                                    <span>Calendar</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content text-muted">
                            <div class="tab-pane show active" id="my_task_list">
                                @include('my-tasks.partials._my-task-list')
                            </div>
                            <div class="tab-pane" id="my_task_kanban">
                                @include('my-tasks.partials._my-task-kanban')
                            </div>
                            <div class="tab-pane" id="my_task_calendar">
                                @include('my-tasks.partials._my-task-calendar')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('module-right-section')
{{--    {!! generate_back_to_list_button(route('projects.list')) !!}--}}
@endsection

@push('script')
    {{--@vite(['resources/js/pages/app-calendar.js'])--}}
@endpush
