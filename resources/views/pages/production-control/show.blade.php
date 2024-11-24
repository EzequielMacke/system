@extends('layouts.AdminLTE.index')
@section('title', 'Control de Produccion')
@section('content')
<div class="row">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a href="{{ url('production-control') }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
                    </div>
                </div>
                <br>
                <div class="row">                        
                    <div class="col-md-12"> 
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-3"><b>Nro° Pedido:</b></div>
                                <div class="col-md-9">{{ $control->id }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Cilente:</b></div>
                                <div class="col-md-9">{{ $control->client->fullname }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Sucursal:</b></div>
                                <div class="col-md-9">{{ $control->branch->name }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Solicitado por:</b></div>
                                <div class="col-md-9">{{ $control->user->name }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Fecha:</b></div>
                                <div class="col-md-9">{{ $control->date}}</div>
                            </div>                                
                            <div class="row">
                                <div class="col-md-3"><b>Fecha Creación:</b></div>
                                <div class="col-md-9">{{ $control->created_at->format('d/m/Y H:m:s') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox float-e-margins" id="div_details">
        <div class="ibox-title">
            <h3>Items</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs fs-3">
                        <li class="active"><a data-toggle="tab" href="#seccion1" onclick="ChangeTab1();"><h5>Primera Etapa </h5></a></li>
                        <li class=""><a data-toggle="tab" href="#seccion2" onclick="ChangeTab2();"><h5>Segunda Etapa </h5></a></li>
                        <li class=""><a data-toggle="tab" href="#seccion3" onclick="ChangeTab3();"><h5>Tercera Etapa </h5></a></li>
                    </ul>
           
                </div>
            </div>
        </div>        
    </div>
@endsection
