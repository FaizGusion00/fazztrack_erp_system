<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineJobLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'action',
        'action_time',
        'notes',
        'offline_data',
        'synced',
        'synced_at'
    ];

    protected $casts = [
        'action_time' => 'datetime',
        'synced_at' => 'datetime',
        'offline_data' => 'array',
        'synced' => 'boolean'
    ];

    /**
     * Get the job that this log belongs to
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get unsynced logs
     */
    public function scopeUnsynced($query)
    {
        return $query->where('synced', false);
    }

    /**
     * Scope to get synced logs
     */
    public function scopeSynced($query)
    {
        return $query->where('synced', true);
    }

    /**
     * Scope to get logs by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
}
