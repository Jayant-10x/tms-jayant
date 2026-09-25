<?php

use Illuminate\Support\Collection;
use Vinkla\Hashids\Facades\Hashids;

function my_encrypt(int|string $value, bool $is_text = false): string {
    if (!$is_text) {
        return Hashids::encode($value);
    }
    return Crypt::encryptString($value);
}

function my_decrypt(int|string $value, bool $is_text = false): string|null {
    if (!$is_text) {
        $decoded = Hashids::decode($value);
        return $decoded[0] ?? null;
    }
    return Crypt::decryptString($value);
}

function get_dash_on_empty($value) {
    return !empty($value) ? $value : '-';
}

function generate_custom_html_title($title): string {
    return "<hr><div class='custom-html-title'>" . $title . "</div><hr>";
}

function get_created_updated_by_db_column($table, $prefix, $only_updated = false) {
    $ret_val = [];

    // Add created columns only if $only_updated is false
    if (!$only_updated) {
        $ret_val['created_by'] = $table->string($prefix . 'created_by')->nullable();
        $ret_val['created_on'] = $table->timestamp($prefix . 'created_on')->nullable();
    }

    // Always add updated columns
    $ret_val['updated_by'] = $table->string($prefix . 'updated_by')->nullable();
    $ret_val['updated_on'] = $table->timestamp($prefix . 'updated_on')->nullable();

    return $ret_val;
}

function setCreatedUpdatedBy() {
    return str_replace(' ', '_', get_logged_in_adm_name()) . '~#~' . get_logged_in_user_id() . '~#~' . $_SERVER['REMOTE_ADDR'];
}

function get_date_time_format($datetime, $format = null) {
    $format = is_null($format) ? config('constants.DB_DATE_FORMAT_VIEW') : $format;
    return date($format, strtotime($datetime));
}

function generate_status_html($status) {
    switch ($status) {
        case 0:
            return '<span class="badge badge-soft-danger rounded-pill me-1 fs-6">Inactive</span>';
        case 1:
        default:
            return '<span class="badge badge-soft-success rounded-pill me-1 fs-6">Active</span>';

    }
}

function generate_no_record_html($message = '', $class = ''): string {
    $message = !empty($message) ? $message : 'No Record Found.';
    return '<div class="p-3' . $class . '">
                <div class="alert alert-warning alert-icon text-center" role="alert">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bx bx-info-circle text-amber-600"></i>
                        <span class="p-1">' . $message . '</span>
                    </div>
                </div>
            </div>';
}

function generate_created_updated_label($created_updated_data): string {
    $updated_by_on = '-';
    $exploded_array = explode('~#~', $created_updated_data['created_by']);
    $username = str_replace('_', ' ', $exploded_array[0]);

    $ip_address = $exploded_array[2];
    if (!empty($created_updated_data['updated_by'])) {
        $updating_exploded_array = explode('~#~', $created_updated_data['updated_by']);
        $updating_username = str_replace('_', ' ', $updating_exploded_array[0]);
        $updating_ip_address = $updating_exploded_array[2];
        $updated_by_on = $updating_username . ' ' . date('d-m-Y H:i', strtotime($created_updated_data["updated_on"])) . ' | ' . $updating_ip_address;
    }
    return '<div class="created_updated_main_div">
                <div class="row p-2">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-3 created_updated_label align-content-center">Created By: </div>
                            <div class="col-md-9 created_updated">' . $username . '
                                <span>' . date('d-m-Y H:i', strtotime($created_updated_data["created_on"])) . '</span>
                                <span>| ' . $ip_address . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-3 created_updated_label align-content-center">Updated By: </div>
                            <div class="col-md-9 created_updated">' . $updated_by_on . '</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
}

function generate_shorten_string($text, $char_length = 25) {
    if (mb_strlen($text) <= $char_length) {
        return $text;
    }
    return mb_substr($text, 0, $char_length) . '...';
}

function get_initials_char(string $text, $returned_limit = 2): string {
    $words = preg_split('/\s+/', trim($text));
    return collect($words)->filter()->take($returned_limit)->map(fn($word) => strtoupper(mb_substr($word, 0, 1)))->implode('');
}

function generate_pagination($data_collection, string $item_name, string $custom_class = ''): string {
    if ($data_collection->total() <= config('constants.PER_PAGE_ITEM_COUNT')) {
        return '<div class="card-footer ' . $custom_class . '">
                    <div class="small text-muted" >
                        Showing
                            <span class="fw-bold" >' . $data_collection->firstItem() . '</span >
                        to
                            <span class="fw-bold" >' . $data_collection->lastItem() . '</span >
                        of
                            <span class="fw-bold" >' . $data_collection->total() . '</span > '
            . $item_name . '</div >
                </div>';
    }

    return '<div class="card-footer' . $custom_class . '"><div>' . $data_collection->links() . '</div></div>';
}
