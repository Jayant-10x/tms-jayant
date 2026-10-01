@php
    $callee_title = $called_from =='admin' ? 'Admin User' : 'Profile';
@endphp
@extends('layouts.vertical', ['title' => ($mode == 'add'? 'Add' : 'Edit').' '.$callee_title])

@push('css')
    <style>
        .filepond {
            height: 210px !important;
            width: 210px !important;
        }
    </style>
    @vite(['resources/css/choices.css'])
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <form
                action="{{$mode == 'edit' ? route('admin_user_module.update', array_merge(['adm_id' => my_encrypt($admin_data->adm_id)],request()->query())) : route('admin_user_module.store',request()->query())}}"
                method="post" id="admin_add_edit_form" enctype="multipart/form-data">
                @csrf
                @if($mode == 'edit')
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="admin_name" class="form-label">Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="admin_name" name="admin_name"
                                           class="form-control @error('admin_name') is-invalid @enderror"
                                           autocomplete="off"
                                           value="{{$admin_data->adm_name ?? old('admin_name')}}">
                                    @error('admin_name')
                                    <span class="validation-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <input type="text" id="role" name="role" class="form-control"
                                           autocomplete="off" value="Admin" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="status" data-choices data-choices-sorting-false
                                            data-placeholder="Select Status" name="status"
                                            @if(!empty($admin_data) && get_logged_in_user_id() == $admin_data->adm_id) disabled @endif>

                                        <option value="" @selected(old('status', $admin_data->adm_status ?? '') == '')>
                                            Select Status
                                        </option>

                                        <option
                                            value="1" @selected(old('status', $admin_data->adm_status ?? '') == '1')>
                                            Active
                                        </option>

                                        <option
                                            value="0" @selected(old('status', $admin_data->adm_status ?? '') == '0')>
                                            Inactive
                                        </option>
                                    </select>
                                    @error('status')
                                    <span class="validation-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="user_name" class="form-label">User Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="user_name" name="user_name"
                                           class="form-control @error('user_name') is-invalid @enderror"
                                           autocomplete="off"
                                           value="{{$admin_data->adm_user_name ?? old('user_name')}}">
                                    @error('user_name')
                                    <span class="validation-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password @empty($admin_data)
                                            <span
                                                class="text-danger">*</span>
                                        @endempty</label>
                                    <input type="password" id="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           autocomplete="off">
                                    @error('password')
                                    <span class="validation-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mb-3" style="justify-items: center">
                            <label for="admin_photo" class="form-label">Admin Photo </label>
                            <input type="file" id="admin_photo" name="admin_photo"
                                   class="filepond form-control"/>
                        </div>
                        <div>
                            @error('admin_photo')
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
    @if($called_from == 'admin')
        {!! generate_back_to_list_button(route('admin_user_module.list',request()->query())) !!}
    @else
        {!! generate_back_to_list_button(route('my_profile',request()->query())) !!}
    @endif
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {

            const inputElement = document.querySelector('#admin_photo');

            let existingPhoto = false;

            @if($mode === 'edit' && !empty($admin_data?->adm_photo))
                existingPhoto = true;
            @endif

            const pond = await window.initFilePond(inputElement, {
                labelIdle: 'Drag & Drop admin photo or <span class="filepond--label-action">Browse</span>',
                imagePreviewHeight: 100,
                imageCropAspectRatio: '1:1',
                imageResizeTargetWidth: 100,
                imageResizeTargetHeight: 100,
                stylePanelLayout: 'compact circle',
                styleLoadIndicatorPosition: 'center bottom',
                styleProgressIndicatorPosition: 'right bottom',
                styleButtonRemoveItemPosition: 'center bottom',
                styleButtonProcessItemPosition: 'right bottom',
                storeAsFile: true,
                allowReplace: true,
                maxFileSize: '5MB',

                @if(!empty($admin_data))
                files: existingPhoto
                    ? [
                        {
                            source: '{{ asset('storage/admin/' . $admin_data->adm_photo) }}',
                            options: {
                                // type: 'local',
                                metadata: {
                                    poster: '{{ asset('storage/admin/' . $admin_data->adm_photo) }}'
                                }
                            }
                        }
                    ]
                    : []
                @endif
            });

            pond.on('addfile', () => {
                const fileItem = document.querySelector('.filepond--item');

                if (fileItem) {
                    fileItem.addEventListener('click', (e) => {
                        e.stopPropagation();
                        pond.browse();
                    });
                }
            });
        });

        $(document).ready(function () {
            $('#admin_add_edit_form').validate({
                ignore: [],
                rules: {
                    admin_name: {
                        required: true,
                        minlength: 2,
                        maxlength: 100,
                        alphaspace: true
                    },
                    user_name: {
                        required: true,
                        minlength: 6,
                        maxlength: 20,
                        usernameRule: true
                    },
                    status: {
                        required: function () {
                            return {{!(!empty($admin_data) && get_logged_in_user_id() == $admin_data->adm_id)}};
                        },
                    },
                    password: {
                        required: function () {
                            return @json($mode !== 'edit');
                        },
                        minlength: function () {
                            // Enforce minlength in 'add' mode OR if user typed something in 'edit' mode
                            var mode = @json($mode);
                            var passwordVal = $('#password').val();
                            return (mode !== 'edit' || passwordVal.length > 0) ? 8 : false;
                        },
                        maxlength: 20
                    }
                },
                errorPlacement: function (error, element) {
                    if (element.is("select")) {
                        var choicesContainer = element.closest(".choices");
                        if (choicesContainer.length) {
                            error.insertAfter(choicesContainer);
                        } else {
                            error.insertAfter(element);
                        }
                    }
                    // 3. STANDARD INPUTS
                    else {
                        error.insertAfter(element);
                    }
                },
            })
        });

        document.querySelectorAll('select').forEach(function (selectElement) {
            selectElement.addEventListener('change', function () {
                $(this).valid(); // Clear validation error instantly upon selecting an option
            });
        });
    </script>
@endpush
