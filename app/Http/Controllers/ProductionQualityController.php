<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductionQualityRequest;
use App\Models\Articulo;
use App\Models\Brand;
use App\Models\ProductionQuality;
use App\Models\ProductionStage;
use Illuminate\Http\Request;

class ProductionQualityController extends Controller
{
    public function index()
    {
        $qualitys = ProductionQuality::where('status',1)->get();
        return view('pages.production-quality.index',compact('qualitys'));
    }

    public function create()    
    {
        return view('pages.production-quality.create');
    }

    public function store(CreateProductionQualityRequest $request)
    {
        ProductionQuality::create([
            'name' => request()->name,
            'number' => request()->number,
            'status' => 1
        ]);

        $this->flashMessage('check', 'La Calidad fue registrado correctamente', 'success');

        return redirect()->route('production-quality');
    }
    public function edit(ProductionQuality $qualitys)
    {
        return view('pages.production-quality.edit',compact('qualitys'));
    }

    public function update(ProductionQuality $qualitys)
    {
            $qualitys->update([
                                'name'       => request()->name,
                                'number'       => request()->number ,
                            ]);
                            
                                
                                

        return redirect('production-quality');
}
}
