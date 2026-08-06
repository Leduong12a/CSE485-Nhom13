<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StaffProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'shift',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(TicketCategory::class, 'staff_specialties', 'staff_profile_id', 'category_id');
    }
}
