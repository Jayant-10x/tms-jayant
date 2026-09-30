@extends('layouts.vertical', ['title' => 'Employees','subTitle' => 'Employees'])

@push('css')
    @vite(['node_modules/choices.js/public/assets/styles/choices.min.css'])
@endpush
@section('content')
    <div class="card">
        <div class="card-header justify-content-between align-items-center border-bottom">
            <form action="{{route('employees.list')}}" method="get">
                <div class="row">
                    <div class="col-md-2">
                        <label for="emp_name" class="form-label">Name</label>
                        <input type="text" id="emp_name" name="emp_name"
                               class="form-control"
                               autocomplete="off"
                               value="{{ request('emp_name') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="emp_email" class="form-label">Email</label>
                        <input type="text" id="emp_email" name="emp_email"
                               class="form-control"
                               autocomplete="off"
                               value="{{ request('emp_email') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="emp_dept" class="form-label">Department</label>
                        <select class="form-control"
                                id="emp_dept" data-choices data-choices-sorting-false
                                data-placeholder="Select Department" name="emp_dept">
                            <option value="">Select Department</option>
                            @foreach(\App\Enums\DepartmentsEnum::all_departments() AS $department)
                                <option value="{{$department->value}}"
                                    @selected(request('emp_dept') === $department->value)>{{$department->label()}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="emp_sub_dept" class="form-label">Sub Department</label>
                        <select id="emp_sub_dept" name="emp_sub_dept"
                                class="form-control" data-choices data-choices-sorting-false
                                data-placeholder="Select Sub Dept.">
                            <option value="">Select Sub Dept.</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="emp_status" class="form-label">Status</label>
                        <select class="form-control"
                                id="emp_status" data-choices data-choices-sorting-false
                                data-placeholder="Select Status" name="emp_status">

                            <option value="" @selected(request('emp_status') === null || request('emp_status') === '')>
                                Select Status
                            </option>

                            <option value="1" @selected(request('emp_status') === '1')>
                                Active
                            </option>

                            <option value="0" @selected(request('emp_status') === '0')>
                                Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 mt-4">
                        <div class="d-flex g-2 justify-content-around">
                            <div>
                                {!! generate_filter_search_button() !!}
                            </div>
                            @if(!empty($filter_arr))
                                <div>
                                    {!! generate_filter_clear_button(route('employees.list')) !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            @if(!empty($all_employees) && count($all_employees) > 0)
                <div class="table-responsive">
                    <table class="table align-middle text-nowrap table-hover table-centered mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Sr. no.</th>
                            <th width="20%">Employee Photo & Name</th>
                            <th width="10%">Email</th>
                            <th width="15%">Department</th>
                            <th width="12%">Sub Department</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($all_employees as $employee)
                            <tr>
                                <td>{{$loop->iteration}}.</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            @if(!empty($employee->emp_photo))
                                                <a href="{{ asset('storage/employees/' . $employee->emp_photo) }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer">
                                                    <img
                                                        src="{{asset('storage/employees/'.$employee->emp_photo)}}"
                                                        class="avatar-sm rounded-circle object-fit-cover">
                                                </a>
                                            @else
                                                <img src="{{asset('images/users/dummy-avatar.jpg')}}"
                                                     class="avatar-sm rounded-circle object-fit-cover ">
                                            @endif
                                        </div>
                                        <div class="text-dark fw-medium">
                                            {{$employee->emp_full_name}}
                                        </div>
                                    </div>

                                </td>
                                <td>{{$employee->emp_email}}</td>
                                <td>{{$employee->emp_department?->label() ?? '-' }}</td>
                                <td>{{$employee->emp_sub_department?->label() ?? '-' }}</td>
                                <td>{!! generate_status_html($employee->emp_status) !!}</td>
                                <td>{{get_date_time_format($employee->emp_created_on)}}</td>
                                <td>
                                    @php
                                        $request_merged_arr = array_merge(['emp_id' => my_encrypt($employee->emp_id)], request()->query());
                                    @endphp
                                    <div class="d-flex gap-2">
                                        {!! generate_view_button(route('employees.view', $request_merged_arr)) !!}
                                        {!! generate_edit_button(route('employees.edit', $request_merged_arr)) !!}
                                        {{--                                        {!! generate_delete_button(route('employees.list')) !!}--}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {!! generate_pagination($all_employees,"Employees") !!}
            @else
                {!! generate_no_record_html() !!}
            @endif
            <!-- end table-responsive -->
        </div>
    </div>
@endsection

@section('module-right-section')
    {!! generate_add_button(route('employees.add', request()->query()), title:' Employee', text: ' Employee') !!}
@endsection

