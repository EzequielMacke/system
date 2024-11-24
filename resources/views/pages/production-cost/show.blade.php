@extends('layouts.AdminLTE.index')
@section('title', 'Mermas')
@section('content')
<div class="row">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a href="{{ url('production-control-quality') }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
                    </div>
                </div>
                <br>
                <div class="row">                        
                    <div class="col-md-12"> 
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-3"><b>Nro°:</b></div>
                                <div class="col-md-9">{{ $production_cost->id }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Sucursal:</b></div>
                                <div class="col-md-9">{{ $production_cost->branch->name }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Fecha:</b></div>
                                <div class="col-md-9">{{ $production_cost->date}}</div>
                            </div>                                
                            <div class="row">
                                <div class="col-md-3"><b>Fecha Creación:</b></div>
                                <div class="col-md-9">{{ $production_cost->created_at->format('d/m/Y H:m:s') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">                
        <div class="col-md-12">
            <div class="ibox-content table-responsive no-padding">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Articulo</th>
                            <th>Material</th>
                            <th>Cant. Perdida</th>
                            <th>Costo Unitario</th>
                            <th>Costo Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($production_cost->production_cost_detail as $detail)
                            <tr>
                                <td>{{$detail->articulo->name}}</td>
                                <td>{{$detail->material->description}}</td>
                                <td>{{$detail->quantity}}</td>
                                <td>{{number_format(($detail->price_cost / $detail->quantity),0,',','.')}}</td>
                                <td>{{number_format($detail->price_cost,0,',','.')}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
