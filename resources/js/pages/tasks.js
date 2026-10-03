// import $ from 'jquery';

$(document).ready(function () {
    let _subTaskNode = $('#sub_task');

    function updateChecklistProgress() {
        let total = $('.checklist-item').length;
        let completed = $('.checklist-item:checked').length;

        let percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
        $('#checklist-completed').text(completed);
        $('#checklist-total').text(total);
        $('#checklist-percentage').text(percentage + '%');

        $('#checklist-progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage);
    }

    function updateChecklistText() {
        $('.checklist-item').each(function () {
            let checkbox = $(this);
            let text = checkbox.siblings('.checklist-text');
            text.toggleClass(
                'checklist-completed',
                checkbox.is(':checked')
            );
        });
    }


    function updateSubTaskIsDone(pst_id, currentNode) {
        if (pst_id === '') {
            return;
        }
        $.ajax({
            url: '/update-sub-task-via-ajax/is-done/' + pst_id,
            type: 'POST',
            dataType: 'json',
            data: {
                task_id: task_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status) {
                    // Update progress
                    updateChecklistProgress();
                    // Update current checklist text
                    let textNode = currentNode.siblings('.checklist-text');
                    textNode.toggleClass('checklist-completed', response.is_done);

                    showNotification(response.message, 'success');
                } else {
                    showNotification(response.message, 'error');
                }
            },

            error: function (xhr) {
                console.error('Error updating sub-task:', xhr);
                // Optional:
                // If saving failed, revert checkbox
                currentNode.prop('checked', !currentNode.is(':checked'));
                updateChecklistProgress();
                updateChecklistText();
            }
        });
    }


    // Checkbox change
    $(document).on('change', '.checklist-item', function () {
        let currentNode = $(this);
        let pst_id = currentNode.val();
        updateChecklistProgress();
        updateChecklistText();
        updateSubTaskIsDone(pst_id, currentNode);
    });

    // Initial page load
    updateChecklistProgress();
    updateChecklistText();

    $(document).on('click', '#addSubTask', function () {
        if (_subTaskNode.val().trim() === '') {
            return;
        }
        if (_subTaskNode.val().trim().length < 2) {
            return showNotification('Please enter at least 2 characters.', 'error');
        }
        if (_subTaskNode)
            $.ajax({
                url: '/add-sub-task-via-ajax/' + task_id,
                type: 'POST',
                dataType: 'json',
                data: {
                    sub_task: _subTaskNode.val().trim(),
                    task_id: task_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status) {
                        let subTaskData = response.data;

                        let html = `
                    <div class="col-md-11 text-wrap pb-2">
                        <input type="checkbox"
                               class="form-check-input text-dark checklist-item"
                               value="${subTaskData.pst_id}">

                        <span class="checklist-text">
                            ${subTaskData.pst_title}
                        </span>
                    </div>


                    <div class="col-md-1 pb-2 d-flex g-0">
                        <span data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              data-bs-title="View History">
                            <a href="javascript:void(0);"
                               class="custom-pop-up-view-modal"
                               data-bs-section=sub_task_history
                               data-bs-mode=sub_task_history_mode
                               data-bs-primary-id="${subTaskData.pst_id}"
                               data-bs-toggle="modal" data-bs-target="#viewModalPopup">
                                <iconify-icon
                                    icon="solar:history-broken"
                                    class="align-middle fs-16 fw-bold text-black"></iconify-icon>
                            </a>
                        </span>
                        <a href="javascript:void(0);" data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           data-bs-title="Delete"
                           data-sub_task_id="${subTaskData.pst_id}"
                           class="delete-sub-task ms-3">
                            <iconify-icon icon="solar:trash-bin-trash-broken"
                                          class="align-middle fs-16 fw-bold text-danger"></iconify-icon>
                        </a>
                    </div>`;

                        $('#checklist-container').append(html);

                        // Clear input
                        _subTaskNode.val('');
                        updateChecklistProgress()
                        showNotification(response.message, 'success');
                    } else {
                        showNotification(response.message, 'error');
                    }
                },

                error: function (xhr) {
                    console.error('Error updating sub-task:', xhr);
                }
            });
    });

    $(document).on('click', '.delete-sub-task', function (e) {
        e.preventDefault();

        let currentNode = $(this);
        let pstId = currentNode.data('sub_task_id');

        if (!confirm('Are you sure you want to delete this sub task?')) {
            return;
        }

        $.ajax({
            url: '/delete-sub-task-via-ajax/' + pstId + '/' + task_id,
            type: 'DELETE',
            dataType: 'json',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },

            success: function (response) {

                if (!response.status) {
                    showNotification(response.message, 'error');
                    return;
                }

                currentNode.closest('.col-md-1').prev('.col-md-11').remove();
                currentNode.closest('.col-md-1').remove();

                updateChecklistProgress();

                showNotification(response.message, 'success');
            },

            error: function (xhr) {
                let message = 'Unable to delete sub task.';

                if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                showNotification(message, 'error');
            }
        });
    });

    $(document).on('change', '#task_status_toggle', function () {
        if ($(this).val() !== '') {
            $.ajax({
                url: '/update-task-status/' + task_id,
                type: 'POST',
                dataType: 'json',
                data: {
                    status: $(this).val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function (response) {
                    if (!response.status) {
                        showNotification(response.message, 'error');
                        return;
                    }
                    showNotification(response.message, 'success');
                },

                error: function (xhr) {
                    let message = 'Unable to change task status.';
                    if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }
                    showNotification(message, 'error');
                }
            });
        } else {
            let message = 'Status is required.';
            showNotification(message, 'error');
        }
    });

    $(document).on('click', '#upload-task-attachment', function () {
        $('#task_attachment').trigger('click');
    });

    $(document).on('click', '#upload-task-attachment', function () {
        $('#task_attachment').trigger('click');
    });

    $(document).on('change', '#task_attachment', function () {
        let file = this.files[0];
        if (!file) {
            return;
        }

        let allowedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png'
        ];

        if (!allowedTypes.includes(file.type)) {
            showNotification('Only PDF, JPG, JPEG and PNG files are allowed.', 'error');
            $(this).val('');
            return;
        }

        let formData = new FormData();

        formData.append('task_attachment', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: '/upload-task-attachment/' + task_id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                showNotification('Uploading file...', 'info');
            },

            success: function (response) {
                if (!response.status) {
                    showNotification(response.message, 'error');
                    return;
                }
                showNotification(response.message, 'success');
                setTimeout(function () {
                    location.reload();
                }, 3000);
            },

            error: function (xhr) {
                let message = 'Unable to upload file.';
                if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                if (xhr.responseJSON?.errors?.task_attachment) {
                    message = xhr.responseJSON.errors.task_attachment[0];
                }
                showNotification(message, 'error');
                $('#task_attachment').val('');
            }
        });
    });

    $('#task-chat-form').validate({
        rules: {
            task_comment: {
                required: true,
                minlength: 1,
                maxlength: 2000,
            },
        },
        messages: {
            task_comment: {
                required: "Please enter your comment before proceeding.",
            },
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") === "task_comment") {
                error.appendTo("#task-comment-error-container");
            } else {
                error.insertAfter(element);
            }
        },
    });
});
