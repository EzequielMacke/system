<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clauses extends Model
{
    use HasFactory;
    protected $fillable = [
                            'description',
                            'status',
                            'type_id'
                        ];
    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }
}

