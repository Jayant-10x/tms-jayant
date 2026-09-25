<form action="{{route('task.create')}}" method="post" id="task_add_edit_form" enctype="multipart/form-data">
    @csrf
    <div class="row">
        {{-- Task Title --}}
        <div class="col-md-12">
            <div class="mb-3">
                <label for="task_title" class="form-label">Task Title<span class="text-danger">*</span></label>
                <input type="text" id="task_title" name="task_title"
                       class="form-control @error('task_title') is-invalid @enderror" autocomplete="off"
                       value="{{ old('task_title') }}">
                @error('task_title')
                <span class="validation-message">
                {{ $message }}
            </span>
                @enderror
            </div>
        </div>
    </div>
    {{-- Project + Category --}}
    <div class="row">
        {{-- Project --}}
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="project" class="form-label">Project<span class="text-danger">*</span></label>
                <select id="project" name="project" class="form-select js-task-select">
                    <option value="">Select Project</option>
                    @foreach($projects as $pro_id => $pro_name)
                        <option
                            value="{{ my_encrypt($pro_id) }}" {{ old('project') == my_encrypt($pro_id) ? 'selected' : '' }}>{{ $pro_name }}</option>
                    @endforeach
                </select>
                @error('project')
                <span class="validation-message">
                {{ $message }}
            </span>
                @enderror
            </div>
        </div>

        {{-- Category --}}
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                <select class="form-select js-task-select" id="category" name="category">
                    <option value="">Select Category</option>
                    @foreach(\App\Enums\TaskCategoryEnum::cases() as $category)
                        <option
                            value="{{ $category->value }}" {{ old('category') == $category->value ? 'selected' : '' }}>{{ $category->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>


    {{-- Priority + Status --}}
    <div class="row">
        {{-- Priority --}}
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                <select id="priority" name="priority" class="form-select js-task-select">
                    <option value="">Select Priority</option>
                    @foreach(\App\Enums\TaskPriority::cases() as $priority)
                        <option
                            value="{{ $priority->value }}" {{ old('priority') == $priority->value ? 'selected' : '' }}>{{ $priority->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Initial Status --}}
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="status" class="form-label">Initial Status <span class="text-danger">*</span></label>
                <select id="status" name="status" class="form-select js-task-select">
                    <option value="">Select Status</option>
                    @foreach(\App\Enums\TaskStatus::cases() as $status)
                        @if(!in_array($status->value,['review', 'completed', 'cancelled']))
                            <option
                                value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </div>


    {{-- Dates + Estimated Hours --}}
    <div class="row">
        {{-- Start Date --}}
        <div class="col-lg-4">
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                <input type="text" name="start_date" id="start_date"
                       class="form-control @error('start_date') is-invalid @enderror" autocomplete="off"
                       value="{{ old('start_date') }}">
                @error('start_date')
                <span class="validation-message">
                    {{ $message }}
                </span>
                @enderror
            </div>
        </div>

        {{-- Due Date --}}
        <div class="col-lg-4">
            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                <input type="text" name="due_date" id="due_date"
                       class="form-control @error('due_date') is-invalid @enderror" autocomplete="off"
                       value="{{ old('due_date') }}">
                @error('due_date')
                <span class="validation-message">
                    {{ $message }}
                </span>
                @enderror
            </div>
        </div>

        {{-- Estimated Hours --}}
        <div class="col-lg-4">
            <div class="mb-3">
                <label for="estimated_hours" class="form-label">Est. Hours <span class="text-danger">*</span></label>
                <input type="number" name="estimated_hours" id="estimated_hours"
                       class="form-control @error('estimated_hours') is-invalid @enderror" autocomplete="off" min="0"
                       step="0.5" value="{{ old('estimated_hours') }}">
                @error('estimated_hours')
                <span class="validation-message">
                    {{ $message }}
                </span>
                @enderror
            </div>
        </div>
    </div>


    {{-- Assignees --}}
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label">Assign To <span class="text-danger">*</span></label>
                <div class="border rounded p-2" style="max-height: 145px; overflow-y: auto;" id="assign_to_container">
                    <div class="row g-2" style="max-height: 120px !important;overflow-y: auto; overflow-x: hidden;">
                        <div class="col-md-12">
                            <input type="text" id="assignees_search" class="form-control" autocomplete="off"
                                   value="" placeholder="Search Assignee........">
                        </div>
                        @if(get_logged_in_user_role() == \App\Enums\UserRoleEnum::MANAGER->value)
                            @php
                                $manager_details = get_logged_in_user_employee_data();
                            @endphp
                            <div class="col-md-6">
                                <label class="d-flex align-items-center gap-2 border rounded p-2 bg-light mb-0 w-100"
                                       style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input flex-shrink-0" name="assign_to[]"
                                           value="{{ $manager_details['emp_id'] }}">

                                    {{-- Avatar --}}
                                    <span
                                        class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 25px; height: 25px; font-size: 10px;">
                                    {{ get_initials_char($manager_details['emp_full_name']) }}
                                </span>

                                    {{-- Employee Details --}}
                                    <span class="d-flex flex-column lh-sm overflow-hidden">
                                    <span class="fw-medium text-dark text-truncate">
                                        {{ $manager_details['emp_full_name'] }}
                                    </span>
                                    <small class="text-muted text-truncate">
                                        {{$manager_details['emp_designation']->label()}}
                                    </small>
                                </span>
                                </label>
                            </div>
                        @endif
                        @foreach($team_members as $assignee)
                            <div class="col-md-6">
                                <label class="d-flex align-items-center gap-2 border rounded p-2 bg-light mb-0 w-100"
                                       style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input flex-shrink-0" name="assign_to[]"
                                           value="{{ $assignee['emp_id'] }}">

                                    {{-- Avatar --}}
                                    <span
                                        class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 25px; height: 25px; font-size: 10px;">
                                    {{ get_initials_char($assignee['emp_full_name']) }}
                                </span>

                                    {{-- Employee Details --}}
                                    <span class="d-flex flex-column lh-sm overflow-hidden">
                                    <span class="fw-medium text-dark text-truncate">
                                        {{ $assignee['emp_full_name'] }}
                                    </span>
                                    <small class="text-muted text-truncate">
                                        {{\App\Enums\DesignationEnum::tryFrom($assignee['emp_designation'])->label()}}
                                    </small>
                                </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tags -->
    <div class="row">
        <div class="col-md-10">
            <div class="mb-3">
                <label for="task_tags" class="form-label">
                    Tags <span class="text-danger">*</span>
                    <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Maximum 10 tags are allowed."
                          data-bs-container="body" class="d-inline-flex align-middle">
                    <iconify-icon
                        icon="solar:info-circle-bold"
                        class="fs-14 text-warning">
                    </iconify-icon>
                </span>
                </label>
                <input type="text" id="task_tags" class="form-control" autocomplete="off" name="check_tags">
                <input type="hidden" name="task_tags_hid" id="task_tags_hid" value="">
            </div>
        </div>

        <div class="col-md-2 align-content-center text-center">
            <button type="button" class="btn btn-xs btn-outline-primary rounded" id="add_tags">
                <iconify-icon icon="solar:add-bold" class="align-middle fs-14"></iconify-icon>
                Add
            </button>
        </div>
    </div>

    <div id="input_tags">
        <!-- Tags will appear here -->
    </div>


    <!-- Checklist / Subtasks -->
    <div class="row">
        <div class="col-md-11">
            <div class="mb-3">
                <label for="sub_tasks" class="form-label">
                    Checklist / Subtasks <span class="text-danger">*</span>
                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                          data-bs-title="Maximum 10 Checklist / Subtasks are allowed." data-bs-container="body"
                          class="d-inline-flex align-middle">
                    <iconify-icon icon="solar:info-circle-bold" class="fs-14 text-warning"></iconify-icon>
                </span>
                </label>
                <input type="text" id="sub_tasks" class="form-control" autocomplete="off" name="check_sub_task">
                <input type="hidden" name="sub_task_hid" id="sub_task_hid" value="">
            </div>
        </div>

        <div class="col-md-1 align-content-center text-center">
            <button type="button" class="btn btn-xs btn-outline-primary rounded" id="add_sub_tasks">
                <iconify-icon
                    icon="solar:add-bold"
                    class="align-middle fs-14">
                </iconify-icon>
            </button>
        </div>
    </div>

    <div id="sub_task_tags">
        <!-- Subtasks will appear here -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="mb-3">
                <label for="attachments" class="form-label">Attachment(s)
                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                          data-bs-title="Maximum 10 media files are allowed to upload." data-bs-container="body"
                          class="d-inline-flex align-middle">
                    <iconify-icon icon="solar:info-circle-bold" class="fs-14 text-warning"></iconify-icon>
                    </span>
                </label>
                <input class="form-control" type="file" id="attachments" name="attachments[]"
                       accept="image/jpg, image/jpeg, image/png, application/pdf" multiple>
                @error('attachments')
                <span class="validation-message">
                    {{ $message }}
                </span>
                @enderror
            </div>
        </div>
    </div>

    <div id="task_attachments" style="margin-top: -18px !important; margin-bottom: 10px">
        <!-- Task attachment appear here -->
    </div>

    {{-- Description --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="mb-3">
                <label for="desc" class="form-label">Description</label>
                <textarea class="form-control" id="project_desc" rows="5"
                          name="desc">{{ old('desc') }}</textarea>
                @error('desc')
                <span class="validation-message">
                    {{ $message }}
                </span>
                @enderror
            </div>
        </div>
    </div>

    {!! generate_submit_reset_button() !!}
</form>
<script>
    $(document).ready(function () {
        let task_tags = [];
        let sub_tasks = [];

        let taskDivNode = $('#input_tags');
        let subtaskDivNode = $('#sub_task_tags');

        let task_tags_hid = $('#task_tags_hid');
        let sub_task_hid = $('#sub_task_hid');

        /*
        |--------------------------------------------------------------------------
        | Add Task Tag
        |--------------------------------------------------------------------------
        */
        $('#add_tags').on('click', function () {
            let cur_task_tag = $('#task_tags').val().trim();
            if (cur_task_tag !== '' && !task_tags.includes(cur_task_tag) && task_tags.length < 10) {
                task_tags.push(cur_task_tag);
                $('#task_tags').val('');
                renderTaskTags();
                task_tags_hid.val(JSON.stringify(task_tags));
                task_tags_hid.valid();
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Add Sub Task
        |--------------------------------------------------------------------------
        */
        $('#add_sub_tasks').on('click', function () {
            let curr_sub_task = $('#sub_tasks').val().trim();

            if (curr_sub_task !== '' && !sub_tasks.includes(curr_sub_task) && sub_tasks.length < 10) {
                sub_tasks.push(curr_sub_task);
                $('#sub_tasks').val('');
                renderSubTasks();
                sub_task_hid.val(JSON.stringify(sub_tasks));
                sub_task_hid.valid();
            }
        });

        $('#attachments').on('change', function () {
            const files = this.files;
            const $target = $('#task_attachments');

            if (files.length > 0) {
                const fileNames = Array.from(files).map(file => file.name);
                $target.html(fileNames.join('<span class="text-secondary fw-bold"> | </span>'));
                $target.css('border-bottom', '1px solid gray');
                $target[0].style.setProperty('padding-bottom', '5px', 'important');
                $target[0].style.setProperty('margin-bottom', '15px', 'important');
            } else {
                $target.html('').css('border-bottom', 'none');
                $target[0].style.removeProperty('padding-bottom');
                $target[0].style.removeProperty('margin-bottom');
            }
        });


        /*
        |--------------------------------------------------------------------------
        | Render Task Tags
        |--------------------------------------------------------------------------
        */
        function renderTaskTags() {
            taskDivNode.html('');
            task_tags.forEach(function (tag, index) {
                let tagHtml = `
                <span class="badge bg-primary me-1 mb-1 fs-7 text-wrap">
                    ${tag}
                    <button type="button" class="btn-close btn-close-white ms-1 remove-task-tag" data-index="${index}" aria-label="Remove"></button>
                </span>`;
                taskDivNode.append(tagHtml);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Render Sub Tasks
        |--------------------------------------------------------------------------
        */
        function renderSubTasks() {
            subtaskDivNode.html('');
            sub_tasks.forEach(function (subTask, index) {
                let subTaskHtml = `
                <div class="d-flex align-items-center justify-content-between
                             px-2 py-1 mb-1 text-wrap">
                    <span class="text-dark">
                    <input type="checkbox" class="form-check-input flex-shrink-0"  disabled value="${subTask}">
                        ${subTask}
                    </span>
                    <button type="button" class="btn-close ms-2 remove-sub-task" data-index="${index}" aria-label="Remove"></button>
                </div>`;
                subtaskDivNode.append(subTaskHtml);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Remove Task Tag
        |--------------------------------------------------------------------------
        */
        $(document).on('click', '.remove-task-tag', function () {
            let index = $(this).data('index');
            task_tags.splice(index, 1);
            renderTaskTags();
            task_tags_hid.val(JSON.stringify(task_tags));
        });

        /*
        |--------------------------------------------------------------------------
        | Remove Sub Task
        |--------------------------------------------------------------------------
        */
        $(document).on('click', '.remove-sub-task', function () {
            let index = $(this).data('index');
            sub_tasks.splice(index, 1);
            renderSubTasks();
            sub_task_hid.val(JSON.stringify(sub_tasks));
        });

        $('#task_add_edit_form').validate({
            ignore: [],
            rules: {
                task_title: {
                    required: true,
                    minlength: 2,
                    maxlength: 100,
                },
                project: {
                    required: true,
                },
                category: {
                    required: true,
                },
                priority: {
                    required: true,
                },
                status: {
                    required: true,
                },
                start_date: {
                    required: true,
                    dateISO: true
                },
                due_date: {
                    required: true,
                    dateISO: true,
                    dateGreaterThan: $('#start_date'),
                },
                estimated_hours: {
                    required: true,
                    number: true
                },
                desc: {
                    minlength: {{MIN_LENGTH_10}},
                    maxlength: {{MAX_LENGTH_2000}}
                },
                "attachments[]": {
                    required: false,
                    allowedFiles: true,
                    maxFileCount: 10,
                    maxFileSize: 5 * 1024 * 1024 // 5MB limit per file
                },
                task_tags_hid: {
                    required: true
                },
                check_tags: {
                    minlength: {{MIN_LENGTH}},
                    maxlength: {{MAX_LENGTH_50}}
                },
                sub_task_hid: {
                    required: true
                },
                check_sub_task: {
                    minlength: {{MIN_LENGTH}},
                    maxlength: {{MAX_LENGTH}}
                },
                "assign_to[]": {
                    required: true
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") === "assign_to[]") {
                    error.insertAfter('#assign_to_container');
                    return;
                }

                // 1. FLATPICKR HANDLING
                if (element.attr("name") === "start_date" || element.attr("name") === "due_date" || element.hasClass("flatpickr-input")) {
                    // Find the visible input generated by Flatpickr (the one after the hidden input)
                    var visibleInput = element.nextAll('input:not([type="hidden"])').first();

                    if (visibleInput.length) {
                        error.insertAfter(visibleInput);
                    } else {
                        error.insertAfter(element);
                    }
                } else {
                    error.insertAfter(element);
                }
            },
            messages: {
                task_name: {
                    required: 'Task name is required.'
                },
                task_tags_hid: {
                    required: 'Please add at least one tag.'
                },
                sub_task_hid: {
                    required: 'Please add at least one Checklist / Subtasks.'
                },
                'assign_to[]': {
                    required: 'Please select at least one team member.'
                },
            }
        });

        $('#assignees_search').on('input', function () {
            var query = $(this).val().toLowerCase().trim();

            // Target all assignee column cards
            $('#assign_to_container .col-md-6').each(function () {
                var nameText = $(this).find('.fw-medium').text().toLowerCase();

                // Toggle visibility based on matching name text
                $(this).toggle(nameText.indexOf(query) !== -1);
            });
        });

        $.validator.addMethod("dateGreaterThan", function (value, element, param) {
            // $(param) targets the start date element
            var startDate = $(param).val();

            // If either field is empty, let 'required' rule handle it
            if (!value || !startDate) {
                return true;
            }

            // Compare date strings/timestamps directly
            return new Date(value) >= new Date(startDate);
        }, "Due date cannot be earlier than the start date.");
    });
</script>
