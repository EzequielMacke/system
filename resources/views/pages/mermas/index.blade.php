@extends('layouts.AdminLTE.index')
@section('title', 'Mermas')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="btn-group pull-right">
                    <a href="{{ url('losses/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                </div>
            </div>
            <div class="ibox-content pb-0">
                <div class="row">
                    <form method="GET">
                        <div class="form-group col-md-2">
                            <input type="text" class="form-control" name="o" placeholder="Buscar" value="{{ request()->o }}">
                        </div>
                    </form>
                </div>
            </div>
            <div class="ibox-content table-responsive no-padding">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nro°</th>
                            <th>Cliente</th>
                            <th>Sucursal</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($losses as $losse)
                            <tr>
                                <td>{{ $losse->id }}</td>
                                <td>{{ $losse->quality_control->id }}</td>
                                <td>{{ $losse->branch->name }}</td>
                                <td>{{ $losse->date }}</td>
                                <td>
                                    <span class="label label-{{ config('constants.purchase-status-label.' . $losse->status) }}">{{ config('constants.purchase-status.'. $losse->status) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('losses/' . $losse->id) }}"><i class="fa fa-info-circle"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $losses->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
