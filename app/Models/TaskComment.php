<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'prt_id',
        'user_id',
        'comment',
    ];

    /**
     * Get the task that owns the comment.
     */
    public function task() {
        return $this->belongsTo(ProjectTask::class);
    }

    /**
     * Get the user who posted the comment.
     */
    public function user() {
        return $this->belongsTo(AdminUser::class, 'user_id', 'adm_id');
    }
}
