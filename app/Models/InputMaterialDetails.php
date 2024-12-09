<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class InputMaterialDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'input_id',
        'matiral_id',
        'quantity',
    ];
    public function input()
    {
        return $this->hasMany('App\Models\Inputs');
    }
    public function material()
    {
        return $this->hasMany('App\Models\Materials');
    }
}
