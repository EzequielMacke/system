<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionSite extends Model
{
    use HasFactory;
    protected $table= 'construction_site';
    protected $fillable = ['description','status','direction','client_id'];

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }
}
