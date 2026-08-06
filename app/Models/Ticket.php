<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'category_id',
        'requester_id',
        'current_assignee_id',
        'status',
        'priority',
        'sla_deadline',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'sla_deadline' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_assignee_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(TicketStatusLog::class);
    }

    public function survey(): HasOne
    {
        return $this->hasOne(SatisfactionSurvey::class);
    }
}
