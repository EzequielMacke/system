<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBrandRequest;
use App\Http\Requests\CreateProductionStageRequest;
use App\Models\Articulo;
use App\Models\Brand;
use App\Models\ProductionStage;
use Illuminate\Http\Request;

class ProductionStageController extends Controller
{
    public function index()
    {
        $stages = ProductionStage::where('status',1)->get();
        return view('pages.production-stage.index',compact('stages'));
    }

    public function create()
    {
        return view('pages.production-stage.create');
    }

    public function store(CreateProductionStageRequest $request)
    {
        ProductionStage::create([
            'name' => request()->name,
            'number' => request()->number,
            'status' => 1
        ]);

        $this->flashMessage('check', 'La Etapa fue registrado correctamente', 'success');

        return redirect()->route('production-stage');
    }
    public function edit(ProductionStage $stages)
    {
        return view('pages.production-stage.edit',compact('stages'));
    }

    public function update(ProductionStage $stages)
    {
            $stages->update([
                                'name'       => request()->name,
                                'number'       => request()->number ,
                            ]);
                            
                                
                                

        return redirect('production-stage');
}
}