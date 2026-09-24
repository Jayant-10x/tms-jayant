@extends('layouts.vertical', ['title' => 'Employees','subTitle' => 'Employees'])

@section('content')
    <div class="card">
        {{--<div class="card-header d-flex justify-content-between align-items-center border-bottom">
            <!--Filters-->
        </div>--}}
        <div class="card-body p-0">
            @if(!empty($all_employees) && count($all_employees) > 0)
                <div class="table-responsive">
                    <table class="table align-middle text-nowrap table-hover table-centered mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Sr. no.</th>
                            <th width="20%">Employee Photo & Name</th>
                            <th width="10%">Email</th>
                            <th width="12%">Contact</th>
                            <th width="15%">Department</th>
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
                                <td>{{$employee->emp_phone_number}}</td>
                                <td>{{$employee->emp_department?->label() ?? '-' }}</td>
                                <td>{!! generate_status_html($employee->emp_status) !!}</td>
                                <td>{{get_date_time_format($employee->emp_created_on)}}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        {!! generate_view_button(route('employees.view', ['emp_id' => my_encrypt($employee->emp_id)])) !!}
                                        {!! generate_edit_button(route('employees.edit', ['emp_id' => my_encrypt($employee->emp_id)])) !!}
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
    {!! generate_add_button(route('employees.add'), title:' Employee', text: ' Employee') !!}
@endsection
