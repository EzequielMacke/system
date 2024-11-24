<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ProductionQuality extends Model
{
    use HasFactory;
    protected $fillable = ['name','number', 'status'];

    public function scopeFilter($query)
    {
        return $query->where('status', true)->orderBy('name')->pluck('name', 'id');
    }
}
