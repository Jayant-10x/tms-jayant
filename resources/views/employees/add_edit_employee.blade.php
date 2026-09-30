@extends('layouts.vertical', ['title' => ($mode == 'add'? 'Add' : 'Edit').' Employee'])

@push('css')
    @vite(['node_modules/choices.js/public/assets/styles/choices.min.css'])
@endpush

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
                @if($mode == 'edit')
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
                    @include('employees.partials.form._personal-info')
                </div>
                @if($mode == 'edit')
                    <div class="tab-pane" id="credential_info">
                        @include('employees.partials.form._credential-info')
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('module-right-section')
    {!! generate_back_to_list_button(route('employees.list', request()->query())) !!}
@endsection

@push('script')
    {{--
    Transferring the php variable from blade file to js file by two ways,
    1. by using data-* and access it in the js file as,
    <div id="employee-page" data-employee-id="{{ $emp_data->emp_id }}" data-mode="{{ $mode }}"></div>
        const employeePage = document.getElementById('employee-page');

            if (employeePage) {
                const employeeId = employeePage.dataset.employeeId;
                const mode = employeePage.dataset.mode;

                console.log(employeeId);
                console.log(mode);
            }
     2. by the window variable with @json as,
             window.employeeData = {
                id: @json($emp_data->emp_id),
                mode: @json($mode),
                name: @json($emp_data->emp_full_name),
            };
    --}}

    <script>
        window.mode = @json($mode);
        @if($mode == 'edit')
            window.employeeConfig = @json($emp_data);
        @endif
    </script>

    @vite(['resources/js/pages/employee.js' ])
@endpush


