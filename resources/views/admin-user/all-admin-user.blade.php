@extends('layouts.vertical', ['title' => 'Admin Users','subTitle' => 'Admin Users'])

@push('css')
    @vite(['resources/css/choices.css'])
@endpush
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header justify-content-between align-items-center border-bottom">
                    <form action="{{route('admin_user_module.list')}}" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="adm_name" class="form-label">Name</label>
                                <input type="text" id="adm_name" name="adm_name"
                                       class="form-control"
                                       autocomplete="off"
                                       value="{{ request('adm_name') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="adm_username" class="form-label">Username</label>
                                <input type="text" id="adm_username" name="adm_username"
                                       class="form-control"
                                       autocomplete="off"
                                       value="{{ request('adm_username') }}">
                            </div>

                            <div class="col-md-3">
                                <label for="adm_status" class="form-label">Status</label>
                                <select class="form-control"
                                        id="adm_status" data-choices data-choices-sorting-false
                                        data-placeholder="Select Status" name="adm_status">

                                    <option value="" @selected(request('adm_status') === null || request('adm_status') === '')>
                                        Select Status
                                    </option>

                                    <option value="1" @selected(request('adm_status') === '1')>
                                        Active
                                    </option>

                                    <option value="0" @selected(request('adm_status') === '0')>
                                        Inactive
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2 mt-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        {!! generate_filter_search_button() !!}
                                    </div>
                                    @if(!empty($filter_arr))
                                        <div class="col-md-3">
                                            {!! generate_filter_clear_button(route('admin_user_module.list')) !!}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    @if(!empty($all_users) && count($all_users) > 0)
                        <div class="table-responsive">
                            <table class="table align-middle text-nowrap table-hover table-centered mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th width="5%">Sr. no.</th>
                                    <th width="30%">Admin Photo & Name</th>
                                    <th width="10%">Username</th>
                                    <th width="10%">Status</th>
                                    <th width="20%">Created On</th>
                                    <th width="10%" class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($all_users as $user)
                                    <tr>
                                        <td>{{$loop->iteration}}.</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    @if(!empty($user->adm_photo))
                                                        <a href="{{ asset('storage/admin/' . $user->adm_photo) }}"
                                                           target="_blank"
                                                           rel="noopener noreferrer">
                                                            <img
                                                                src="{{asset('storage/admin/'.$user->adm_photo)}}"
                                                                class="avatar-sm rounded-circle object-fit-cover">
                                                        </a>
                                                    @else
                                                        <img src="{{asset('images/users/dummy-avatar.jpg')}}"
                                                             class="avatar-sm rounded-circle object-fit-cover">
                                                    @endif
                                                </div>
                                                <div class="text-dark fw-medium">
                                                    {{$user->adm_name}}
                                                </div>
                                            </div>

                                        </td>
                                        <td>{{$user->adm_user_name}}</td>
                                        <td>{!! generate_status_html($user->adm_status) !!}</td>
                                        <td>{{get_date_time_format($user->adm_created_on)}}</td>
                                        <td class="text-center">
                                            {!! generate_view_button(route('admin_user_module.view', ['adm_id' => my_encrypt($user->adm_id)])) !!}
                                            {!! generate_edit_button(route('admin_user_module.edit', ['adm_id' => my_encrypt($user->adm_id)])) !!}
                                            {{--                                            {!! generate_delete_button(route('employees.list')) !!}--}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {!! generate_pagination($all_users,"Admin Users") !!}
                    @else
                        {!! generate_no_record_html() !!}
                    @endif
                    <!-- end table-responsive -->
                </div>
            </div>
        </div>

    </div>
@endsection

@section('module-right-section')
    {!! generate_add_button(route('admin_user_module.add'), title:' Admin User', text: ' Admin User') !!}
@endsection
