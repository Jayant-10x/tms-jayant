@extends('layouts.vertical', ['title' => 'dashboard','subTitle' => 'Dashboard'])

@push('css')
    <style>
        /* Greeting Header Styles */
        .greeting-header {
            background: transparent !important;
        }

        .greeting-header h1 {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .greeting-header p {
            font-size: 14px;
            color: #000000;
        }

        /* Cards Grid Layout */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
        }

        /* Individual Card Style */
        .stat-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        /* Icon Badge Base */
        .icon-badge {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-badge i {
            width: 18px;
            height: 18px;
        }

        /* Custom Pastel Colors per Card */
        .badge-light-blue {
            background-color: #eff6ff;
            color: #2563eb;
        }

        .badge-yellow {
            background-color: #fefce8;
            color: #ca8a04;
        }

        .badge-green {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .badge-red {
            background-color: #fef2f2;
            color: #dc2626;
        }

        /* Content Area */
        .card-content {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .card-value {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .card-label {
            font-size: 13px;
            font-weight: 500;
            color: #475569;
        }

        .card-subtitle {
            font-size: 12px;
            color: #94a3b8;
        }

        /* Responsive Grid for Smaller Screens */
        @media (max-width: 1024px) {
            .cards-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 640px) {
            .cards-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    @php
        $hour = date('G');
        if ($hour >= 5 && $hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour >= 12 && $hour < 17) {
            $greeting = 'Good afternoon';
        } elseif ($hour >= 17 && $hour < 21) {
            $greeting = 'Good evening';
        } else {
            $greeting = 'Good night';
        }
    @endphp
    <div class="dashboard-section">
        <!-- Greeting Header Section -->
        <div class="greeting-header">
            <div class="row">
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-12">
                            {!! get_date_time_format(today(),'l, F d') !!}
                        </div>
                        <div class="col-md-12">
                            <h1>{{$greeting}}, {!! get_logged_in_adm_name() !!} </h1>
                        </div>
                        <div class="col-md-12">
                            <p>Welcome back! Here is your personal task summary and workspace overview.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <img src="{{asset('images/welcome.gif')}}" style="width: 34%;height: 107%;">
                </div>
            </div>
        </div>

        @php
            if(!is_admin()) {
                $loggedInUserId = get_logged_in_user_emp_id();
                $user_statistics = $user_statistics[$loggedInUserId];
            }

            $total_tasks = !empty($user_statistics) && $user_statistics['total_tasks'] ? $user_statistics['total_tasks'] : 0;
            $in_progress_tasks = !empty($user_statistics) && $user_statistics['progress_tasks'] ? $user_statistics['progress_tasks'] : 0;
            $pending_tasks = !empty($user_statistics) && $user_statistics['pending_dashboard_count'] ? $user_statistics['pending_dashboard_count'] : 0;
            $completed_tasks = !empty($user_statistics) && $user_statistics['completed_tasks'] ? $user_statistics['completed_tasks'] : 0;
            $overdue_tasks = !empty($user_statistics) && $user_statistics['overdue_tasks'] ? $user_statistics['overdue_tasks'] : 0;
        @endphp
            <!-- Stat Cards Section -->
        <div class="cards-grid">

            <!-- Card 1: Total Tasks -->
            <div class="stat-card">
                <div class="icon-badge badge-soft-primary">
                    <iconify-icon icon="ri-list-check-3" class="align-middle fs-3 text-primary"></iconify-icon>
                </div>
                <div class="card-content">
                    <span class="card-value">{{$total_tasks}}</span>
                    <span class="card-label">Total Tasks</span>
                    <span class="card-subtitle">From all lists</span>
                </div>
            </div>

            <!-- Card 2: In Progress -->
            <div class="stat-card">
                <div class="icon-badge badge-light-blue">
                    <iconify-icon icon="ri:progress-5-line" class="align-middle fs-3"></iconify-icon>
                </div>
                <div class="card-content">
                    <span class="card-value">{{$in_progress_tasks}}</span>
                    <span class="card-label">In Progress</span>
                    <span class="card-subtitle">Active now</span>
                </div>
            </div>

            <!-- Card 3: Pending -->
            <div class="stat-card">
                <div class="icon-badge badge-yellow">
                    <iconify-icon icon="ic:outline-watch-later" class="align-middle fs-3"></iconify-icon>
                </div>
                <div class="card-content">
                    <span class="card-value">{{$pending_tasks}}</span>
                    <span class="card-label">Pending</span>
                    <span class="card-subtitle">Not started</span>
                </div>
            </div>

            <!-- Card 4: Completed -->
            <div class="stat-card">
                <div class="icon-badge badge-green">
                    <iconify-icon icon="mdi:check-circle-outline" class="align-middle fs-3"></iconify-icon>
                </div>
                <div class="card-content">
                    <span class="card-value">{{$completed_tasks}}</span>
                    <span class="card-label">Completed</span>
                    <span class="card-subtitle">{{$total_tasks != 0 ? ($completed_tasks / $total_tasks) * 100 : 0}}% completion</span>
                </div>
            </div>

            <!-- Card 5: Overdue -->
            <div class="stat-card">
                <div class="icon-badge badge-red">
                    <iconify-icon icon="solar:danger-triangle-outline" class="align-middle fs-3"></iconify-icon>
                </div>
                <div class="card-content">
                    <span class="card-value">{{$overdue_tasks}}</span>
                    <span class="card-label">Overdue</span>
                    <span class="card-subtitle">Needs attention</span>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('script')
    {{-- @vite(['resources/js/pages/dashboard-analytics.js']) --}}
@endsection
