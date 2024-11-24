<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishServiceDetail extends Model
{
    use HasFactory;
    protected $fillable = [
                            'wish_services_id',
                            'services_id',
                            'quantity',
                            'observation'
                        ];

    public function service()
    {
        return $this->belongsTo('App\Models\Service','services_id');
    }

    public function wish_services()
    {
        return $this->belongsTo('App\Models\WishService','wish_services_id');
    }

}
