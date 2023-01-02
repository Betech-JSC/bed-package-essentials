<?php

namespace JamstackVietnam\Core\Models;

use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\BouncerFacade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'type',
        'status',

        'google2fa_secret',
        'google2fa_ts'
    ];

    protected $appends = [
        'verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $rules = [
        'password' => 'confirmed|nullable|min:8|max:255'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->google2fa_secret = generate_code(16, 'uppercase');
        });

        static::saving(function ($model) {
            if (request()->route()?->getName() !== 'admin.admins.store') return;

            if ($password = request()->input('password')) {
                $model->password = Hash::make($password);
            } else {
                unset($model->password);
            }
        });

        static::saved(function ($model) {
            if (request()->route()?->getName() !== 'admin.admins.store') return;
            $role = request()->input('role');
            BouncerFacade::sync($model)->roles([$role]);
        });
    }

    public function getRoleNamesAttribute()
    {
        return $this->getRoleNames();
    }

    public function getVerifiedAttribute()
    {
        return $this->google2fa_ts !== 0;
    }

    public function scopeVerified($query)
    {
        $query->where('google2fa_ts', '<>', 0);
    }

    public function transformCurrentAdmin()
    {
        if (current_admin_id() === $this->id) {
            $this->name .= ' (Đang đăng nhập)';
        }
        return $this;
    }
}
