<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
        'name',
        'email',
        'role',
        'phase',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'phase' => 'string',
            'is_active' => 'boolean',
        ];
    }

    // Role checking methods
    public function isSuperAdmin(): bool
    {
        return $this->role === 'SuperAdmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['SuperAdmin', 'Admin']);
    }

    public function isSalesManager(): bool
    {
        return $this->role === 'Sales Manager';
    }

    public function isDesigner(): bool
    {
        return $this->role === 'Designer';
    }

    public function isProductionStaff(): bool
    {
        return $this->role === 'Production Staff';
    }

    public function canAccessPhase(string $phase): bool
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }
        
        if ($this->isProductionStaff()) {
            return $this->phase === $phase;
        }
        
        return false;
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        // Check the is_active column if it exists
        return $this->is_active ?? true;
    }

    /**
     * Get user's last login time
     */
    public function getLastLoginAttribute()
    {
        return $this->updated_at;
    }

    public function getAccessibleSections(): array
    {
        if ($this->isSuperAdmin()) {
            return ['clients', 'orders', 'jobs', 'users', 'reports'];
        }
        
        if ($this->isAdmin()) {
            return ['clients', 'orders', 'jobs', 'reports'];
        }
        
        if ($this->isSalesManager()) {
            return ['clients', 'orders'];
        }
        
        if ($this->isDesigner()) {
            return ['orders'];
        }
        
        if ($this->isProductionStaff()) {
            return ['jobs'];
        }
        
        return [];
    }

    public function assignedJobs()
    {
        return $this->hasMany(Job::class, 'assigned_user_id', 'id');
    }
}
