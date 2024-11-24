@extends('layouts.AdminLTE.index')
@section('title', 'Orden de Produccion')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="btn-group pull-right">
                    <a href="{{ url('production-order/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
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
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order as $ord)
                            <tr>
                                <td>{{ $ord->id }}</td>
                                <td>{{ $ord->client->fullname }}</td>
                                <td>{{ $ord->branch->name }}</td>
                                <td>{{ $ord->date }}</td>
                                <td>
                                    <span class="label label-{{ config('constants.purchase-status-label.' . $ord->status) }}">{{ config('constants.purchase-status.'. $ord->status) }}</span>
                                </td>
                                <td class="text-center"><a href="{{ url('production-order/' . $ord->id) }}"><i class="fa fa-info-circle"></i></a>
                                <a href="{{ url('production-order/' . $ord->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $order->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
