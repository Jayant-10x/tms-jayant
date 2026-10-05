<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="tab-retain-token" content="{{ hash_hmac('sha256', session()->getId(), config('app.key')) }}">
    @include('layouts.partials/title-meta', ['title' => $title])
    @include('layouts.partials/head-css')
</head>

<body>

<div class="wrapper">

    @include("layouts.partials/topbar")
    @include("layouts.partials/main-nav")

    <div class="page-content">

        <div class="container-fluid">

            {{--@include("layouts.partials/page-title",['title' => $title,'subTitle' => $subTitle])--}}
            <div class="row mb-2">
                <div class="col-12">
                    <div class="page-title-box justify-content-end">
                        {{--<h4 class="mb-0 fw-semibold">{{ $title }}</h4>--}}
                        @yield('module-right-section')
                        {{--<ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $subTitle }}</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>--}}
                    </div>
                </div>
            </div>

            @yield('content')

            {{--{!! generate_ajax_button(
                 tooltip_title: 'Another Task',
                 data: [
                    'section' => my_encrypt('another_hello', true),
                    'primary-id' => my_encrypt(1),
                    'mode' => my_encrypt('add', true),
                ],
                class: 'custom-pop-up-add-edit-modal',
                title: 'Another Add Task',
                text: 'Another Add Task',
                ajax_btn_type: 'edit'
            ) !!}

            {!! generate_ajax_button(
                tooltip_title: 'Task',
                data: [
                    'section' => my_encrypt('hello', true),
                    'primary-id' => my_encrypt(20),
                    'mode' => my_encrypt('edit', true),
                ],
                class: 'custom-pop-up-add-edit-modal',
                title: 'Add Task',
                text: 'Add Task',
            ) !!}
            {!! generate_ajax_button(
                tooltip_title: 'Task',
                data: [
                    'section' => my_encrypt('hello_view', true),
                    'primary-id' => my_encrypt(20),
                    'mode' => my_encrypt('edit', true),
                ],
                class: 'custom-pop-up-view-modal',
                title: 'View Task',
                text: 'View Task',
                ajax_btn_type: 'view'
            ) !!}--}}

            @include('layouts.partials._modals._modal_add_edit_popup')
            @include('layouts.partials._modals._modal_view_popup')

        </div>

        @include("layouts.partials/footer")

        @yield('modal')

    </div>

</div>

@include("layouts.partials/right-sidebar")
@include('layouts.partials.toastify')
@include('layouts.partials/footer-scripts')
<script>
    $(function () {
        const currentToken = $('meta[name="tab-retain-token"]').attr('content');
        const key = 'active_tab_' + window.location.pathname;

        if (localStorage.getItem('tab_sync_token') !== currentToken) {
            localStorage.clear();
            localStorage.setItem('tab_sync_token', currentToken);
        }

        const activeTab = localStorage.getItem(key);
        if (activeTab) {
            $(`[data-bs-target="${activeTab}"], [href="${activeTab}"]`).tab('show');
        }

        $('a[data-bs-toggle="tab"], button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('data-bs-target') || $(e.target).attr('href');
            localStorage.setItem(key, target);
        });
    });
</script>
@stack('script')
</body>

</html>
