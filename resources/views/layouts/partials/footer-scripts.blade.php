<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/additional-methods.js') }}"></script>
@vite(['resources/js/app.js','resources/js/layout.js','resources/js/pages/project-task-form.js'])

@stack('script')
<script type="module" src="{{ asset('js/custom.js') }}"></script>
@yield('script-bottom')
