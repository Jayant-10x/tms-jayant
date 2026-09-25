<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTaskDoneHistory extends Model {
    public $timestamps = false;
    protected $table = 'sub_task_done_histories';
    protected $primaryKey = null;
    protected $guarded = [];
}
