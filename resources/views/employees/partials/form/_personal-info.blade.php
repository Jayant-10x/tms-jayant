<form
    action="{{$mode == 'edit' ? route('employees.update', array_merge(['emp_id' => my_encrypt($emp_data->emp_id)], request()->query())) : route('employees.save', request()->query())}}"
    method="post" id="employee_add_edit_form" enctype="multipart/form-data">
    @csrf
    @if($mode == 'edit')
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-lg-2">
                    <div class="mb-3">
                        <label for="emp_id" class="form-label">Employee ID <span
                                class="text-danger">*</span></label>
                        <input type="text" id="emp_id" name="emp_id"
                               class="form-control @error('emp_id') is-invalid @enderror"
                               autocomplete="off"
                               value="{{$emp_data->emp_internal_id ?? old('emp_id')}}">
                        @error('emp_id')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name <span
                                class="text-danger">*</span></label>
                        <input type="text" name="full_name" id="full_name"
                               class="form-control @error('full_name') is-invalid @enderror"
                               autocomplete="off"
                               value="{{$emp_data->emp_full_name ?? old('full_name')}}">
                        @error('full_name')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number <span
                                class="text-danger">*</span></label>
                        <input type="text" name="phone_number" id="phone_number"
                               class="form-control @error('phone_number') is-invalid @enderror"
                               autocomplete="off"
                               value="{{$emp_data->emp_phone_number ?? old('phone_number')}}">
                        @error('phone_number')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span
                                class="text-danger">*</span></label>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               autocomplete="off"
                               value="{{$emp_data->emp_email ?? old('email')}}">
                        @error('email')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="employment_type" class="form-label">Employment Type <span
                                class="text-danger">*</span></label>
                        <select class="form-control @error('employment_type') is-invalid @enderror"
                                name="employment_type" id="employment_type"
                                data-choices data-choices-sorting-false
                                data-placeholder="Select employment">
                            <option value="">select employment</option>
                            <optgroup label="">
                                @foreach(\App\Enums\EmploymentType::cases() AS $employment_type)
                                    <option
                                        value="{{ $employment_type->value }}"
                                        @selected(old('employment_type', $emp_data->emp_employment_type?->value ?? $emp_data->emp_employment_type ?? null) === $employment_type->value)>
                                        {{ $employment_type->label()}}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('employment_type')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="designation" class="form-label">Designation <span
                                class="text-danger">*</span></label>
                        <select class="form-control @error('designation') is-invalid @enderror"
                                name="designation" id="designation"
                                data-choices data-choices-sorting-false
                                data-placeholder="Select Designation">
                            <option value="">select designation</option>
                            @foreach(\App\Enums\DesignationEnum::cases() AS $designation)
                                <option value="{{$designation->value}}"
                                    @selected(old('designation', $emp_data->emp_designation?->value ?? $emp_data->emp_designation ?? null) === $designation->value)>{{$designation->label()}}</option>
                            @endforeach
                        </select>
                        @error('designation')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="department" class="form-label">Department <span
                                class="text-danger">*</span></label>
                        <select class="form-control @error('department') is-invalid @enderror"
                                id="department" data-choices data-choices-sorting-false
                                data-placeholder="Select Department" name="department">
                            <option value="">Select Department</option>
                            @foreach(\App\Enums\DepartmentsEnum::all_departments() AS $department)
                                <option value="{{$department->value}}"
                                    @selected(old('department', $emp_data->emp_department?->value ?? $emp_data->emp_department ?? null) === $department->value)>{{$department->label()}}</option>
                            @endforeach
                        </select>
                        @error('department')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="sub_department" class="form-label">Sub Department{{-- <span
                                                    class="text-danger">*</span>--}}</label>
                        <select id="sub_department" name="sub_department"
                                class="form-control @error('sub_department') is-invalid @enderror">
                            <option value="">Select Sub Department</option>
                        </select>
                        @error('sub_department')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="reporting_to" class="form-label">Reporting To</label>
                        <select class="form-control @error('reporting_to') is-invalid @enderror"
                                id="reporting_to" name="reporting_to">
                            <option value="">Select Reporting To</option>
                        </select>
                        @error('reporting_to')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="joining_date" class="form-label">Joining Date <span
                                class="text-danger">*</span></label>
                        <input type="text" name="joining_date" id="joining_date"
                               class="form-control @error('joining_date') is-invalid @enderror"
                               autocomplete="off"
                               value="{{$emp_data->emp_joining_date ?? old('joining_date')}}">
                        @error('joining_date')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span
                                class="text-danger">*</span></label>
                        <select class="form-control @error('status') is-invalid @enderror"
                                id="status" data-choices data-choices-sorting-false
                                data-placeholder="Select Status" name="status">

                            <option value="" @selected(old('status', $emp_data->emp_status ?? '') == '')>
                                Select Status
                            </option>

                            <option
                                value="1" @selected(old('status', $emp_data->emp_status ?? '') == '1')>
                                Active
                            </option>

                            <option
                                value="0" @selected(old('status', $emp_data->emp_status ?? '') == '0')>
                                Inactive
                            </option>
                        </select>
                        @error('status')
                        <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="employee_photo" class="form-label">Employee Photo {{--<span
                        class="text-danger">*</span>--}}</label>
                <input type="file" id="employee_photo" name="employee_photo"
                       class="filepond form-control"/>
            </div>
            <div>
                @error('employee_photo')
                <span class="validation-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    {!! generate_submit_reset_button() !!}
</form>
<script>
    let empIdUniqueCheckUrl = '{{route('check_unique_emp_internal_id_via_ajax')}}';
    let empEmailUniqueCheckUrl = '{{route('check_unique_emp_email_via_ajax')}}'
    let emp_form_mode = '{{my_encrypt($mode, true)}}';
    let emp_id = null;

    @if($mode == 'edit')
        emp_id = '{{my_encrypt($emp_data->emp_id)}}';
    @endif
</script>
