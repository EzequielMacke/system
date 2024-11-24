<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'status'];

    public function scopeGetAllCached()
    {
        return Cache::rememberForever('models.all.branches', function(){
            return self::where('status', true)->get();
        });
    }

}
