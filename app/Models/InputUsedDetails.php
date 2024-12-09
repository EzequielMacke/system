<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputUsedDetails extends Model
{
    use HasFactory;
    protected $fillable = [
                            'input_used_id',
                            'input_id',
                            'input_quantity',
                            'id_material',
                            'material_quantity',
                            'measurement',
                            'total_quantity',
                        ];

        public function input()
    {
        return $this->belongsTo('App\Models\Input');
    }
        public function material()
    {
        return $this->belongsTo('App\Models\Material');
    }
    public function input_used()
    {
        return $this->belongsTo('App\Models\InputUseds');
    }

}
