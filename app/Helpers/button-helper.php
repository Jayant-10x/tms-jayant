<?php
if (!function_exists('generate_view_button')) {
    function generate_view_button(string $url, string $class = '', string $text = ''): string {
        return sprintf(config('buttons.view'), $url, $class, $text);
    }
}

if (!function_exists('generate_edit_button')) {
    function generate_edit_button(string $url, string $class = '', string $text = ''): string {
        return sprintf(config('buttons.edit'), $url, $class, $text);
    }
}

if (!function_exists('generate_delete_button')) {
    function generate_delete_button(string $url, string $class = '', string $text = ''): string {
        return sprintf(config('buttons.delete'), $url, $class, $text);
    }
}

if (!function_exists('generate_back_to_list_button')) {
    function generate_back_to_list_button(string $url, string $class = '', string $text = ''): string {
        return sprintf(config('buttons.back_to_list'), $url, $class, $text);
    }
}

if (!function_exists('generate_add_button')) {
    function generate_add_button(string $url, string $class = '', string $title = '', string $text = ''): string {
        return sprintf(config('buttons.add_new'), $url, $class, $title, $text);
    }
}
if (!function_exists('generate_filter_search_button')) {
    function generate_filter_search_button(): string {
        return config('buttons.filter_search_btn');
    }
}

if (!function_exists('generate_filter_clear_button')) {
    function generate_filter_clear_button($url): string {
        return sprintf(config('buttons.filter_close_btn'), $url);
    }
}

if (!function_exists('generate_submit_reset_button')) {
    function generate_submit_reset_button(): string {
        return '<div class="mb-3 rounded">
                <div class="row justify-content-start g-2">
                <div class="col-md-2">' .
            config('buttons.submit_btn') . '</div><div class="col-md-2">' . config('buttons.reset_btn')
            . '
                </div></div></div>';
    }
}
function generate_ajax_button(
    string $tooltip_title,
    array  $data,
    string $class,
    string $title,
    string $text,
    string $ajax_btn_type = 'add'
): string {
    $attributes = '';

    foreach ($data as $key => $value) {
        if (in_array(strtolower($key), ['toggle', 'target', 'backdrop', 'modal'])) {
            continue;
        }
        $attributes .= 'data-bs-' . e($key) . '="' . e($value) . '" ';
    }
    $attributes = trim($attributes);

    $configKey = "buttons.{$ajax_btn_type}_ajax_btn";
    $template = config($configKey, config('buttons.add_ajax_btn'));

    return sprintf(
        $template,
        e($tooltip_title), // %1$s -> Outer span tooltip title
        $attributes,       // %2$s -> Extra data-bs-* attributes on button
        e($class),         // %3$s -> Extra CSS classes
        e($title),         // %4$s -> Modal title
        e($text)           // %5$s -> Button inner text
    );
}
