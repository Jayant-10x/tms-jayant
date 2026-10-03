<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;


class AdminUser extends Authenticatable {
    use HasFactory, Notifiable;

    public $timestamps = false;
    protected $table = 'admin_users';
    protected $primaryKey = 'adm_id';
    protected $fillable = [
        'adm_id',
        'adm_name',
        'adm_user_name',
        'adm_role',
        'adm_password',
        'adm_panel_active',
        'adm_panel_last_seen_at',
        'adm_last_activity_at',
    ];
    protected $hidden = [
        'adm_password',
    ];

    protected $casts = [
        'adm_panel_active' => 'boolean',
        'adm_panel_last_seen_at' => 'datetime',
        'adm_last_activity_at' => 'datetime',
    ];

    public function getAuthPasswordName() {
        return 'adm_password';
    }

    public function getAuthPassword() {
        return $this->adm_password;
    }

    public function notifications(): MorphMany {
        return $this->morphMany(Notification::class, 'notifiable', 'not_notifiable_type', 'not_notifiable_id'
        );
    }

    public function taskComments() {
        return $this->hasMany(TaskComment::class);
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class, 'adm_emp_id', 'emp_id');
    }

    protected function admPassword(): Attribute {
        return Attribute::make(
            set: fn($value) => Hash::make($value),
        );
    }
}
