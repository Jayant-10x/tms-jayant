// import $ from 'jquery';
import Choices from 'choices.js';

$(window).on('load', function () {
    if (typeof window.initFlatpickr === 'function') {
        window.initFlatpickr('#joining_date', {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            onChange: function (selectedDates, dateStr, instance) {
                $(instance.element).valid(); // Force validate on selection
            },
            onClose: function (selectedDates, dateStr, instance) {
                $(instance.element).valid(); // Force validate when picker closes
            },
            onReady: function (selectedDates, dateStr, instance) {
                // Ensure the visible alt input triggers validation on keyup/clear
                $(instance.altInput).on('blur keyup', function () {
                    $(instance.element).valid();
                });
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    const inputElement = document.querySelector('#employee_photo');
    let existingPhoto = window.mode === 'edit' && window.employeeConfig?.emp_photo != null;

    // Initialize FilePond with custom options (overriding defaults if needed)
    const pond = await window.initFilePond(inputElement, {
        labelIdle: 'Drag & Drop employee photo or <span class="filepond--label-action">Browse</span>',
        imagePreviewHeight: 100,
        imageCropAspectRatio: '1:1', // Force square crop for profile pictures
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


        /*server: {
            // CSRF Token header configuration for Laravel endpoints
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            // process: '{{ route("upload.process") }}',
            revert: '{{ route("upload.revert") }}'
        }*/

        files: existingPhoto
            ? [
                {
                    source: `/storage/employees/${window.employeeConfig?.emp_photo}`,
                    options: {
                        // type: 'local',
                        metadata: {
                            poster: `/storage/employees/${window.employeeConfig?.emp_photo}`
                        }
                    }
                }
            ]
            : []
    });

    pond.on('addfile', () => {
        const fileItem = document.querySelector('.filepond--item');
        if (fileItem) {
            fileItem.addEventListener('click', (e) => {
                // Prevent default events and trigger file picker
                e.stopPropagation();
                pond.browse();
            });
        }
    });
});

function getChoicesInstance(element) {
    if (element._choices) {
        return element._choices;
    }

    // Fallback: If Choices wasn't initialized on page load because it was empty
    if (typeof Choices !== 'undefined') {
        element._choices = new Choices(element, {
            searchEnabled: true,
            removeItemButton: false,
            itemSelectText: '',
            shouldSort: false,
        });
        return element._choices;
    }

    // Secondary Fallback: Check if Choices is attached to window
    if (typeof window.Choices !== 'undefined') {
        element._choices = new window.Choices(element, {
            searchEnabled: true,
            removeItemButton: false,
            itemSelectText: '',
            shouldSort: false,
        });
        return element._choices;
    }

    return null;
}

$(document).ready(function () {
    const $subDepartmentNode = $('#sub_department');
    const $reportingToNode = $('#reporting_to');

    $('#department').on('change', function () {
        const departmentId = $(this).val();
        const baseUrl = $(this).data('url') || '/sub-department';
        const subDeptElement = $subDepartmentNode[0];

        if (!subDeptElement) return;

        // Reset if no department selected
        if (!departmentId) {
            const choicesInstance = getChoicesInstance(subDeptElement);
            if (choicesInstance) {
                choicesInstance.clearStore();
                choicesInstance.setChoices([
                    {value: '', label: 'Select Sub Department', selected: true}
                ], 'value', 'label', true);
            }
            return;
        }

        // Fetch sub-departments via AJAX
        $.ajax({
            url: baseUrl + '/' + departmentId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                const options = (response.data || []).map(function (item) {
                    return {
                        value: item.value || item.id,
                        label: item.label || item.name
                    };
                });

                const choicesInstance = getChoicesInstance(subDeptElement);

                if (choicesInstance) {
                    // Reset store and load new options
                    choicesInstance.clearStore();
                    choicesInstance.setChoices([
                        {value: '', label: 'Select Sub Department', selected: true},
                        ...options
                    ], 'value', 'label', true);

                    choicesInstance.removeActiveItems();

                    /*
                    * EDIT MODE
                    */
                    if (window.mode === 'edit' && window.employeeConfig?.emp_sub_department != null) {
                        const subDepartment = window.employeeConfig.emp_sub_department.toString();
                        choicesInstance.setChoiceByValue(subDepartment);

                        get_employee_reporting_to(departmentId, subDepartment);
                    } else {
                        get_employee_reporting_to(departmentId, '');
                    }
                }
            },
            error: function (xhr) {
                console.error('Error fetching sub-departments:', xhr);
            }
        });

        // get_employee_reporting_to(departmentId, '');
    });

    $('#sub_department').on('change', function () {
        let sub_department_val = $(this).val();
        let department_val = $('#department').val();
        get_employee_reporting_to(department_val, sub_department_val);
    });


    if (window.mode === 'edit') {
        $('#department').trigger('change');
        $('#sub_department').trigger('change');
    }

    function get_employee_reporting_to(department, sub_department) {
        let emp_id = 0;
        if (window.mode === 'edit') {
            emp_id = window.employeeConfig.emp_id;
        }
        if (department != '' || sub_department != '') {
            $.ajax({
                url: '/employees-reporting-to',
                type: 'GET',
                dataType: 'json',
                data: {
                    department: department,
                    sub_department: sub_department,
                    emp_id: emp_id,
                },
                success: function (response) {
                    const options = (response.data || []).map(function (item) {
                        return {
                            value: item.value,
                            label: item.label
                        };
                    });

                    const reportingToElement = $reportingToNode[0];
                    if (!reportingToElement) {
                        return;
                    }

                    const choicesInstance = getChoicesInstance(reportingToElement);

                    if (choicesInstance) {
                        choicesInstance.clearStore();
                        choicesInstance.setChoices([
                            {
                                value: '',
                                label: 'Select Reporting To',
                                selected: true
                            },
                            ...options
                        ], 'value', 'label', true);
                        choicesInstance.removeActiveItems();

                        /*
                        * EDIT MODE
                        * Select existing reporting employee
                        */
                        if (window.mode === 'edit' && window.employeeConfig?.emp_reporting_to != null) {
                            choicesInstance.setChoiceByValue(
                                window.employeeConfig.emp_reporting_to
                            );
                        }
                    }
                },
                error: function (xhr) {
                    console.error('Error fetching sub-departments:', xhr);
                }
            });
        }
    }


    $("#employee_add_edit_form").validate({
        ignore: [],
        rules: {
            emp_id: {
                required: true,
                number: true,
                minlength: 3,
                maxlength: 5,
                remote: {
                    url: empIdUniqueCheckUrl,
                    type: "get",
                    dataType: "json",
                    data: {
                        internal_id: function () {
                            return $('#emp_id').val();
                        },
                        mode: function () {
                            return emp_form_mode;
                        },
                        emp_id: function () {
                            return emp_id;
                        }
                    }
                }
            },
            full_name: {
                required: true,
                minlength: 2,
                maxlength: 100,
                alphaspace: true
            },
            phone_number: {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 15
            },
            email: {
                required: true,
                strictEmail: true,
                minlength: 8,
                maxlength: 250,
                remote: {
                    url: empEmailUniqueCheckUrl,
                    type: "get",
                    dataType: "json",
                    data: {
                        email_id: function () {
                            return $('#email').val();
                        },
                        mode: function () {
                            return emp_form_mode;
                        },
                        emp_id: function () {
                            return emp_id;
                        }
                    }
                }
            },
            employment_type: {
                required: true,
            },
            designation: {
                required: true,
            },
            department: {
                required: true,
            },
            joining_date: {
                required: true,
                dateISO: true
            },
            status: {
                required: true,
            },
        },
        errorPlacement: function (error, element) {
            // 1. FLATPICKR HANDLING
            if (element.attr("name") === "joining_date" || element.hasClass("flatpickr-input")) {
                // Find the visible input generated by Flatpickr (the one after the hidden input)
                var visibleInput = element.nextAll('input:not([type="hidden"])').first();

                if (visibleInput.length) {
                    error.insertAfter(visibleInput);
                } else {
                    error.insertAfter(element);
                }
            }
            // 2. CHOICES.JS HANDLING
            else if (element.is("select")) {
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
        messages: {
            emp_id: {
                remote: "Employee with this employee ID already exists."
            },
            email: {
                remote: "Employee with this email already exists."
            },
            joining_date: {
                required: "Please select a joining date."
            }
        }
    });

    $('#credential_form').validate({
        ignore: [],
        rules: {
            user_name: {
                required: true,
                minlength: 6,
                maxlength: 20,
                usernameRule:true
            },
            role: {
                required: true
            },
            password: {
                required: function () {
                    if ($('#user_name').val() == '' || !hasPassword) {
                        return true;
                    }
                    return false;
                },
                minlength: 8,
                maxlength: 20,
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



