@extends('layouts.vertical', ['title' => 'Add Project','subTitle' => 'Add Project'])

@push('css')
    @vite(['node_modules/choices.js/public/assets/styles/choices.min.css'])
@endpush
@section('content')
    <div class="card">
        <div class="card-body">
            <form
                action="{{$mode == 'edit' ? route('admin_user_module.update', array_merge(['adm_id' => my_encrypt($admin_data->adm_id)],request()->query())) : route('projects.save',request()->query())}}"
                method="post" id="project_form" enctype="multipart/form-data">
                @csrf
                @if($mode == 'edit')
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label for="project_name" class="form-label">Project Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="project_name" name="project_name"
                                   class="form-control @error('project_name') is-invalid @enderror"
                                   autocomplete="off"
                                   value="{{$admin_data->adm_name ?? old('project_name')}}">
                            @error('project_name')
                            <span class="validation-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror"
                                    id="status" data-choices data-choices-sorting-false
                                    data-placeholder="Select Status" name="status">
                                <option value="">Select Status</option>
                                @foreach(\App\Enums\ProjectStatus::cases() AS $pro_status)
                                    <option value="{{$pro_status->value}}"
                                        @selected(old('status', $emp_data->emp_department?->value ?? $emp_data->emp_department ?? null) === $pro_status->value)>{{$pro_status->label()}}</option>
                                @endforeach
                            </select>
                            @error('status')
                            <span class="validation-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Deadline <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="project_deadline" id="project_deadline"
                                   class="form-control @error('project_deadline') is-invalid @enderror"
                                   autocomplete="off"
                                   value="{{$emp_data->emp_joining_date ?? old('project_deadline')}}">
                            @error('project_deadline')
                            <span class="validation-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label for="project_desc" class="form-label">Description</label>
                            <textarea class="form-control" id="project_desc" rows="5"
                                      name="project_desc">{{old('project_desc')}}</textarea>
                            @error('project_desc')
                            <span class="validation-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                {!! generate_submit_reset_button() !!}
            </form>
        </div>
    </div>
@endsection
@section('module-right-section')
    {!! generate_back_to_list_button(route('projects.list',request()->query())) !!}
@endsection
@push('script')
    <script>
        let called_from = 'add_project';
        let pro_id = null;
    </script>
    @vite(['resources/js/pages/projects.js' ])
@endpush
