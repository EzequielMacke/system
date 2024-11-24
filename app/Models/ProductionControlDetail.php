<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionControlDetail extends Model
{
    use HasFactory;
    protected $fillable = [
                            'observation',
                            'quantity',
                            'residue',
                            'stage',
                            'articulo_id',
                            'production_control_id',
                            'stage_id',
                        ];
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function production_control()
    {
        return $this->belongsTo('App\Models\ProductionControl','production_control_id');
    }
    public function articulo()
    {
        return $this->belongsTo('App\Models\Articulo','articulo_id');
    }
    public function production_stage()
    {
        return $this->belongsTo('App\Models\ProductionStage','stage_id');
    }
    

}
