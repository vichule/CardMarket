<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'Name',
        'Description',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
