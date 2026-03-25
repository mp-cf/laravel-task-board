<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['board_id', 'title', 'status'];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'done';
    }
}
