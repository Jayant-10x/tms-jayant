@php
    $callee_title = $called_from =='employee' ? 'Employee' : 'Profile';
@endphp

@extends('layouts.vertical', ['title' => 'View '.$callee_title,'subTitle' => 'View '.$callee_title])

@section('content')
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a href="#personal_info" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                        <span class="d-block d-sm-none"><i class="bx bx-home"></i></span>
                        <span class="d-none d-sm-block">Personal Info</span>
                    </a>
                </li>
                @if(!empty($emp_data->admin_user_details))
                    <li class="nav-item">
                        <a href="#credential_info" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                            <span class="d-block d-sm-none"><i class="bx bx-user"></i></span>
                            <span class="d-none d-sm-block">Credential Info</span>
                        </a>
                    </li>
                @endif
            </ul>
            <div class="tab-content text-muted">
                <div class="tab-pane show active" id="personal_info">
                    @include('employees.partials.view._personal-info-view')
                </div>
                @if(!empty($emp_data->admin_user_details))
                    <div class="tab-pane" id="credential_info">
                        @include('employees.partials.view._credentials-info-view')
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('module-right-section')
    @if($called_from == 'employee')
        {!! generate_back_to_list_button(route('employees.list', request()->query())) !!}
    @else
        {!! generate_edit_button(route('my_profile.edit', request()->query()), text: ' Edit Profile') !!}
    @endif
@endsection
