@extends('layouts.vertical', ['title' => 'View Task','subTitle' => 'View Task'])
@push('css')
    @vite(['resources/css/tasks.css','resources/css/choices.css'])
@endpush
@section('content')
    @php
        $all_permission = (get_logged_in_user_role() == \App\Enums\UserRoleEnum::MANAGER->value && (permission_can('all_tasks', 'edit') || permission_can('team_tasks', 'edit'))) || is_admin();
        $employee_permission = get_logged_in_user_role() == \App\Enums\UserRoleEnum::EMPLOYEE->value && permission_can('all_my_tasks', 'edit');
        $loggedInUserId = get_logged_in_user_emp_id();
    @endphp
    <div class="row align-items-center">
        <div class="col-md-8">
            <span
                style="font-size: 17px; font-weight: 450; color: #454446f2 !important;">{{$task_data->prt_title}}</span>
        </div>
        @if($all_permission || ($employee_permission && in_array($loggedInUserId, $project_task_assignee)))
            <div class="col-md-4">
                <div class="row justify-content-end">
                    <div class="col-md-8">
                        <select class="form-control rounded-4" name="task_status" id="task_status_toggle"
                                data-choices data-choices-sorting-false>
                            <option value="">select status</option>
                            <optgroup label="">
                                @foreach(\App\Enums\TaskStatus::cases() as $status)
                                    <option
                                        value="{{ $status->value }}"
                                        @selected(old('task_status', $task_data->prt_status?->value ?? $task_data->prt_status ?? null) === $status->value)>
                                        {{ $status->label()}}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">
                    <div class="card task-main-cards">
                        <div class="card-body">
                            <p class="text-dark fw-semibold fs-16 mb-0">Description</p>
                            <p class="mt-2 mb-0">{{get_dash_on_empty($task_data->prt_description)}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card task-main-cards">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row justify-content-between mb-1">
                                        <div class="col-md-10 text-dark fw-semibold fs-16">
                                            Checklist / Subtask <span
                                                class="badge badge-soft-primary fs-4 rounded-5 badge-outline-primary px-2 ms-1"><span
                                                    id="checklist-completed">0</span> / <span
                                                    id="checklist-total">0</span></span>
                                        </div>
                                        <div class="col-md-2 fs-14 text-primary fw-bold text-end"
                                             id="checklist-percentage">0%
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="progress">
                                                <div id="checklist-progress-bar"
                                                     class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                                                     role="progressbar" style="width: 0%" aria-valuenow="0"
                                                     aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3 align-items-center justify-content-between" id="checklist-container">
                                @foreach($task_data->subTasks as $sub_task)
                                    <div class="col-md-11 text-wrap pb-2">
                                        @if($all_permission || ($employee_permission && in_array(get_logged_in_user_emp_id(), $project_task_assignee)))
                                            <input type="checkbox" class="form-check-input text-dark checklist-item"
                                                   value="{{ my_encrypt($sub_task->pst_id) }}" {{ $sub_task->pst_is_done ? 'checked' : '' }}>
                                        @endif
                                        <span class="checklist-text">
                                            {{ $sub_task->pst_title }}
                                        </span>
                                    </div>

                                    @if($all_permission)
                                        <div class="col-md-1 pb-2 d-flex g-0">
                                            <span data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  data-bs-title="View History">
                                                <a href="javascript:void(0);"
                                                   class="custom-pop-up-view-modal"
                                                   data-bs-section="{{my_encrypt('sub-task-history', true)}}"
                                                   data-bs-mode="{{my_encrypt('view', true)}}"
                                                   data-bs-primary-id="{{my_encrypt($sub_task->pst_id)}}"
                                                   data-bs-toggle="modal" data-bs-target="#viewModalPopup">
                                                    <iconify-icon
                                                        icon="solar:history-broken"
                                                        class="align-middle fs-16 fw-bold text-black"></iconify-icon>
                                                </a>
                                            </span>
                                            <a href="javascript:void(0);" data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               data-bs-title="Delete"
                                               data-sub_task_id="{{my_encrypt($sub_task->pst_id)}}"
                                               class="delete-sub-task ms-3">
                                                <iconify-icon icon="solar:trash-bin-trash-broken"
                                                              class="align-middle fs-16 fw-bold text-danger"></iconify-icon>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @if($all_permission)
                                <div class="row mb-1 mt-2 pe-2">
                                    <div class="col-md-11">
                                        <input type="text" id="sub_task" class="form-control" autocomplete="off"
                                               placeholder="Add a subtask or checklist....">
                                    </div>

                                    <div class="col-md-1 align-content-center text-center">
                                        <button type="button"
                                                class="btn  btn-soft-primary btn-outline-primary rounded"
                                                id="addSubTask">
                                            <iconify-icon icon="solar:add-bold"
                                                          class="align-middle fs-4 fw-b"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card task-main-cards">
                        <div class="card-body">
                            <p class="text-dark fw-semibold fs-16 mb-0">
                                <iconify-icon icon="solar:paperclip-bold" class="align-middle"></iconify-icon>
                                Attachments
                                ({{!empty($task_data->prt_attachments) ? count($task_data->prt_attachments) : 0}})
                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                      data-bs-title="Maximum 10 files get uploaded."
                                      data-bs-container="body"
                                      class="d-inline-flex align-middle">
                                        <iconify-icon icon="solar:info-circle-bold"
                                                      class="fs-14 text-warning"></iconify-icon>
                                    </span>
                            </p>

                            <div class="row p-2" id="task-attachments-container">
                                @if(!empty($task_data->prt_attachments))
                                    @foreach($task_data->prt_attachments as $attachment)
                                        @php
                                            $uploader_data = get_admin_user_data($attachment['uploaded_by']);
                                            if(empty($uploader_data['adm_emp_id'])) {
                                                $designation = \App\Enums\UserRoleEnum::tryFrom($uploader_data['adm_role'])->label();
                                            } else {
                                                $designation = (get_employee_data($uploader_data['adm_emp_id'])['emp_designation'])->label();
                                            }

                                            $uploader_info_content = '<div class="row">
                                                    <div class="col-md-5 fw-bold">Uploaded By : </div>
                                                    <div class="col-md-7">' . $uploader_data['adm_name'] . ' (' . $designation . ')</div>
                                                 </div>
                                                 <div class="row">
                                                    <div class="col-md-5 fw-bold">Uploaded On: </div>
                                                    <div class="col-md-7">' . get_date_time_format($attachment['uploaded_on'], 'd-m-Y H:i:s') . '</div>
                                                 </div>';
                                        @endphp
                                        <div class="col-md-12 attachment-files m-1">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    {{$attachment['file_name']}}
                                                </div>
                                                <div class="col-md-1 text-end">
                                                    <iconify-icon icon="solar:info-square-linear"
                                                                  class="align-middle fs-14 text-black" tabindex="0"
                                                                  data-bs-toggle="popover"
                                                                  data-bs-trigger="hover"
                                                                  data-bs-html="true"
                                                                  data-bs-content="{{$uploader_info_content}}"
                                                                  title="Attachment Info.">
                                                    </iconify-icon>
                                                </div>
                                                <div class="col-md-1">
                                                    <a href="{{ asset('storage/task_attachments/'.$attachment['file_name']) }}"
                                                       download="{{ $attachment['file_name'] }}"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-title="Download" class="attachment-download">
                                                        <iconify-icon icon="solar:download-linear"
                                                                      class="align-middle fs-14 text-black">
                                                        </iconify-icon>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {!! generate_no_record_html('No Attachments Found.', class:'p-0') !!}
                                @endif
                            </div>
                            @if($all_permission || ($employee_permission && in_array($loggedInUserId, $project_task_assignee)))
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="file" id="task_attachment" name="runtime_task_attachment"
                                               accept=".pdf,.jpg,.jpeg,.png" hidden>
                                        <a href="javascript:void(0)"
                                           class="btn btn-soft-primary btn-sm rounded m-1 fw-bold"
                                           data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Upload File"
                                           id="upload-task-attachment">
                                            <iconify-icon icon="solar:upload-linear"
                                                          class="align-middle fs-18"></iconify-icon>
                                            Upload File</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card task-main-cards">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="side-card-label">PROJECT</span>
                        </div>
                        <div class="col-md-12">
                            <span
                                class="fs-5 fw-medium assignee-nameassignee-name">{{$task_data->project->pro_name}}</span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="side-card-label">PRIORITY</span>
                                </div>
                                <div class="col-md-12">
                                    <span
                                        class="badge badge-soft-{{$task_data->prt_priority?->color()}} badge-outline-{{$task_data->prt_priority?->color()}} rounded-pill me-1 fs-6"><iconify-icon
                                            icon="solar:flag-2-broken" class="align-middle fs-7"></iconify-icon>{!! $task_data->prt_priority?->label() !!}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="side-card-label">CATEGORY</span>
                                </div>
                                <div class="col-md-12">
                                    <span
                                        class="fs-5 fw-medium assignee-name">{{$task_data->prt_category->label()}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 mb-1">
                            <span class="side-card-label">TAGS</span>
                        </div>
                        <div class="col-md-12 mb-1">
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($task_data->prt_tags as $tag)
                                    <span class="badge text-wrap tag-pill rounded-pill">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card task-main-cards">
                <div class="card-body">
                    <div class="row mt-2">
                        <div class="col-md-12 mb-1">
                            <span class="side-card-label">ASSIGNED BY</span>
                        </div>
                        <div class="col-md-12 mb-1">
                            <div class="assignee-item">
                                    <span class="assignee-char" style="background: #585f6e !important;">
                                        {{ get_initials_char(array_first($task_assigned_by)) }}
                                    </span>
                                <span class="assignee-name">
                                        {{ array_first($task_assigned_by) }}
                                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 mb-1">
                            <span class="side-card-label">ASSIGNEES</span>
                        </div>
                        @foreach($task_assignees as $assignee)
                            <div class="col-md-6 mb-1">
                                <div class="assignee-item">
                                    <span class="assignee-char">
                                        {{ get_initials_char($assignee) }}
                                    </span>
                                    <span class="assignee-name">
                                        {{ $assignee }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card task-main-cards">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mt-2">
                                <div class="col-md-12 mb-1">
                                    <span class="side-card-label">START DATE</span>
                                </div>
                                <div class="col-md-12 mb-1 d-flex align-items-center">
                                    <iconify-icon icon="solar:calendar-mark-broken"
                                                  class="align-middle fs-5 me-1"></iconify-icon>
                                    <span class="assignee-name">
                                {{ get_date_time_format($task_data->prt_start_date) }}
                            </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mt-2">
                                <div class="col-md-12 mb-1">
                                    <span class="side-card-label">DUE DATE</span>
                                </div>
                                <div class="col-md-12 mb-1 d-flex align-items-center">
                                    <iconify-icon icon="solar:calendar-mark-broken"
                                                  class="align-middle fs-5 text-danger me-1"></iconify-icon>
                                    <span class="assignee-name text-danger">
                                {{ get_date_time_format($task_data->prt_due_date) }}
                            </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mt-2">
                                <div class="col-md-12 mb-1">
                                    <span class="side-card-label">ESTIMATED TIME</span>
                                </div>
                                <div class="col-md-12 mb-1">
                                    <iconify-icon icon="solar:clock-circle-broken"
                                                  class="align-middle fs-4"
                                                  style="color: rgb(95 68 255) !important;"></iconify-icon>
                                    <span class="assignee-name text-dark">
                                {{ $task_data->prt_est_hours }}h
                            </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('module-right-section')
    {!! generate_back_to_list_button(request('return_url', url()->previous())) !!}
@endsection
@push('script')
    <script>
        let task_id = '{{my_encrypt($task_data->prt_id)}}';
        let is_admin = '{{is_admin()}}';
        let sub_task_history = '{{my_encrypt('sub-task-history', true)}}';
        let sub_task_history_mode = '{{my_encrypt('view', true)}}';
    </script>
    @vite(['resources/js/pages/tasks.js' ])
@endpush
