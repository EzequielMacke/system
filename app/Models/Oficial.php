<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oficial extends Model
{
    use HasFactory;
    protected $fillable = [
                            'name',
                            'document_nr',
                            'email',
                            'phone',
                            'address',
                            'status',
                            'post',
                            'user_id'
                        ];
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
