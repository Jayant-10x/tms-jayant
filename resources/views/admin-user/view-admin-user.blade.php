@php
    $callee_title = $called_from =='admin' ? 'View Admin User' : 'My Profile';
    if($called_from =='my_profile'){
        $admin_data = $emp_data;
    }
@endphp

@extends('layouts.vertical', ['title' => $callee_title,'subTitle' => $callee_title])

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row my-4">
                <div class="col-md-9 col-lg-9">
                    <div class="row">
                        <div class="col-lg-12 my-1">
                            <p class="text-dark fw-semibold fs-16 mb-0">Name :</p>
                            <p class="mb-0">{{$admin_data->adm_name}}</p>
                        </div>
                        <div class="col-lg-6 my-1">
                            <p class="text-dark fw-semibold fs-16 mb-0">Role :</p>
                            <p class="mb-0">{!! \App\Enums\UserRoleEnum::tryFrom($admin_data->adm_role)->badge() !!}</p>
                        </div>
                        <div class="col-lg-6 my-1">
                            <p class="text-dark fw-semibold fs-16 mb-0">Status :</p>
                            <p class="mb-0">{!! generate_status_html($admin_data->adm_status) !!}</p>
                        </div>
                        <div class="col-lg-4 my-1">
                            <p class="text-dark fw-semibold fs-16 mb-0">Username :</p>
                            <p class="mb-0">{{$admin_data->adm_user_name}} </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 text-center align-content-center">
                    @if(!empty($admin_data->adm_photo))
                        <a href="{{ asset('storage/admin/' . $admin_data->adm_photo) }}"
                           target="_blank"
                           rel="noopener noreferrer">
                            <img
                                src="{{ asset('storage/admin/' . $admin_data->adm_photo) }}"
                                alt="Admin Photo"
                                class="rounded-circle avatar-xl img-thumbnail object-fit-cover"
                            >
                        </a>
                    @else
                        <img src="{{ asset('images/users/dummy-avatar.jpg') }}"
                             class="rounded-circle avatar-xl img-thumbnail object-fit-cover">
                    @endif
                </div>
            </div>
            {!! generate_created_updated_label($admin_data) !!}
        </div>
    </div>
@endsection

@section('module-right-section')
    @if($called_from == 'admin')
        {!! generate_back_to_list_button(route('admin_user_module.list',request()->query())) !!}
    @else
        {!! generate_edit_button(route('my_profile.edit',request()->query()), text: ' Edit Profile') !!}
    @endif
@endsection

