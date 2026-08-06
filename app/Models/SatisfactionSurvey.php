<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SatisfactionSurvey extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'ticket_id',
        'rating_stars',
        'comment',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
