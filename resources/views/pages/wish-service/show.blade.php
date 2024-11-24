@extends('layouts.AdminLTE.index')
@section('title', 'Pedido Produccion')
@section('content')
<div class="row">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a href="{{ url('wish-production') }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
                    </div>
                </div>
                <br>
                <div class="row">                        
                    <div class="col-md-12">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-3"><b>Nro° Pedido:</b></div>
                                <div class="col-md-9">{{ $wish_production->number }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Sucursal:</b></div>
                                <div class="col-md-9">{{ $wish_production->branch->name }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Solicitado por:</b></div>
                                <div class="col-md-9">{{ $wish_production->user->name }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Cliente:</b></div>
                                <div class="col-md-9">{{ $wish_production->client->fullname}}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Fecha:</b></div>
                                <div class="col-md-9">{{ $wish_production->date}}</div>
                            </div>                                
                            <div class="row">
                                <div class="col-md-3"><b>Fecha Creación:</b></div>
                                <div class="col-md-9">{{ $wish_production->created_at->format('d/m/Y H:m:s') }}</div>
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
                            <th class="text-center">Producto</th>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wish_production->wish_production_details as $details)
                            <tr>
                                <td class="text-center"> {{ $details->articulo->name }}</td>
                                <td class="text-center">{{ $details->observation ?? '' }}</td>
                                <td class="text-center">{{ number_format($details->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>        
</div>
@endsection
