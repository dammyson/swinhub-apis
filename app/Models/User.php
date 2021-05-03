<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\UuidTrait;
use App\Support\Enum\UserStatus;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Company;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use Notifiable, HasApiTokens,HasRoles, UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'email', 'password', 'first_name', 'last_name', 'phone', 'avatar',
        'address', 'last_login', 'confirmation_token', 'status', 'remember_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * Always encrypt password when it is updated.
     *
     * @param $value
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
    public function isUnconfirmed()
    {
        return $this->status == UserStatus::UNCONFIRMED;
    }

    public function isActive()
    {
        return $this->status == UserStatus::ACTIVE;
    }

    public function isBanned()
    {
        return $this->status == UserStatus::BANNED;
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function companyIdList()
    {
        return $this->companies()->get()->pluck("id")->toArray();
    }
    public function assignRole($roles, string $guard = null)
    {
        $roles = \is_string($roles) ? [$roles] : $roles;
        $guard = $guard ? : $this->getDefaultGuardName();

        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) use ($guard) {
                return $this->getStoredRole($role, $guard);
            })
            ->each(function ($role) {
                $this->ensureModelSharesGuard($role);
            })
            ->all();

        $this->roles()->saveMany($roles);

        $this->forgetCachedPermissions();

        return $this;
    }
    protected function getStoredRole($role, string $guard): Role
    {
        if (\is_string($role)) {
            return app(Role::class)->findByName($role, $guard);
        }else{
            return app(Role::class)->findById($role, $guard);
        }

        return $role;
    }

    public function syncRoles($roles, $guard)
    {
        $this->roles()->detach();

        return $this->assignRole($roles, $guard);
    }
}
