<style>
    body {
        font-family: 'Inter', sans-serif;
    }

    /* Custom scrollbar for history log list */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<!-- History Logs Stream Container -->
<div id="historyList" class="px-4 py-3 overflow-y-auto custom-scrollbar" style="max-height: 380px;">

    @forelse($history_data as $history)
        @php
            // Fetch employee details using the sdh_done_by ID
            $employee = get_employee_data($history['sdh_done_by']);
            $empName = is_array($employee) ? ($employee['emp_full_name'] ?? 'Unknown User') : ($employee->emp_full_name ?? 'Unknown User');
            $initials = get_initials_char($empName);
            $isChecked = $history['sdh_is_checked'] == 1;
        @endphp

            <!-- Dynamic Log Item -->
        <div
            class="history-item d-flex align-items-start gap-3 p-3 rounded-3 transition-all border border-transparent hover-border"
            data-type="{{ $isChecked ? 'checked' : 'unchecked' }}">

            <div class="position-relative mt-1">
                @if($isChecked)
                    <!-- Checked State Avatar -->
                    <div
                        class="rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 2rem; height: 2rem; font-size: 0.75rem; outline: 4px solid #dcfce7;">
                        {{ $initials }}
                    </div>
                    <div
                        class="position-absolute bg-success text-white rounded-circle p-1 d-flex align-items-center justify-content-center"
                        style="width: 1rem; height: 1rem; outline: 2px solid white; bottom: -3px; right: -6px;">
                        <i class="ri-check-fill" style="font-size: 1rem;"></i>
                    </div>
                @else
                    <!-- Unchecked State Avatar -->
                    <div
                        class="rounded-circle bg-warning-subtle text-warning fw-bold d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 2rem; height: 2rem; font-size: 0.75rem; outline: 4px solid #fef3c7; color: #b45309 !important;">
                        {{ $initials }}
                    </div>
                    <div
                        class="position-absolute bg-warning text-white rounded-circle p-1 d-flex align-items-center justify-content-center"
                        style="width: 1rem; height: 1rem; outline: 2px solid white; background-color: #d97706 !important; bottom: -3px; right: -6px;">
                        <i class="ri-close-fill" style="font-size: 1rem;"></i>
                    </div>
                @endif
            </div>

            <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="mb-0 text-sm fw-semibold text-dark">{{ $empName }}</p>

                </div>

                @if($isChecked)
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="text-muted m-0" style="font-size: 0.75rem;">
                            Marked subtask as <span
                                class="fw-medium text-success bg-success-subtle px-2 py-0.7 rounded">Checked</span>
                        </p>
                        <span class="text-muted" style="font-size: 0.75rem;">
                            {{ \Carbon\Carbon::parse($history['sdh_updated_on'])->format('M d, Y \a\t g:i A') }}
                        </span>
                    </div>

                    <div
                        class="text-secondary bg-light p-2 rounded-3 border border-light-subtle d-flex align-items-center gap-2"
                        style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle text-muted"></i>
                        <span>Changed status from Unchecked <i class="ri-arrow-right-double-fill"></i> Checked</span>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="text-muted m-0" style="font-size: 0.75rem;">
                            Marked subtask as <span class="fw-medium text-warning bg-warning-subtle px-2 py-0.7 rounded"
                                                    style="color: #b45309 !important;">Unchecked</span>
                        </p>
                        <span class="text-muted" style="font-size: 0.75rem;">
                            {{ \Carbon\Carbon::parse($history['sdh_updated_on'])->format('M d, Y \a\t g:i A') }}
                        </span>
                    </div>
                    <div
                        class="text-secondary bg-light p-2 rounded-3 border border-light-subtle d-flex align-items-center gap-2"
                        style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle text-muted"></i>
                        <span>Changed status from Checked <i class="ri-arrow-right-double-fill"></i> Unchecked</span>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-4 text-muted" style="font-size: 0.85rem;">
            No history records found for this checklist / subtask.
        </div>
    @endforelse

</div>
