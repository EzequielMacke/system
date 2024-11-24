@extends('layouts.AdminLTE.index')
@section('title', 'Materia Prima')
@section('content')
<div class="row">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a href="{{ url('raw-materials') }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
                    </div>
                </div>
                <br>
                <div class="row">                        
                    <div class="col-md-12">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-3"><b>Nro° Materia:</b></div>
                                <div class="col-md-9">{{ $materiap->id }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Fecha Creacion:</b></div>
                                <div class="col-md-9">{{ $materiap->created_at}}</div>
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
