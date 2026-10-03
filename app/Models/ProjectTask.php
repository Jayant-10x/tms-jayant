<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectTask extends Model {
    public $timestamps = false;
    protected $table = 'project_tasks';
    protected $primaryKey = 'prt_id';
    protected $guarded = [];
    protected $casts = [
        'prt_category' => \App\Enums\TaskCategoryEnum::class,
        'prt_priority' => \App\Enums\TaskPriority::class,
        'prt_status' => \App\Enums\TaskStatus::class,
    ];

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class, 'prt_pro_id', 'pro_id');
    }

    public function projectTaskAssignments(): HasMany {
        return $this->hasMany(ProjectTaskAssignment::class, 'pta_prt_id', 'prt_id');
    }

    public function subTasks(): HasMany {
        return $this->hasMany(ProjectSubTask::class, 'pst_prt_id', 'prt_id');
    }

    public function comments() {
        return $this->hasMany(TaskComment::class, 'prt_id', 'prt_id');
    }

    protected function casts(): array {
        return [
            'prt_tags' => 'array',
            'prt_attachments' => 'array',
        ];
    }
}
