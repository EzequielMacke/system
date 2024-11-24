@extends('layouts.AdminLTE.index')
@section('title', 'Costo Produccion')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
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
                            {{-- <th>Cliente</th> --}}
                            <th>Sucursal</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($costs as $cost)
                            <tr>
                                <td>{{ $cost->id }}</td>
                                {{-- <td>{{ $cost->quality_control->id }}</td> --}}
                                <td>{{ $cost->branch->name }}</td>
                                <td>{{ $cost->date }}</td>
                                <td>
                                    <span class="label label-{{ config('constants.purchase-status-label.' . $cost->status) }}">{{ config('constants.purchase-status.'. $cost->status) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('production-cost/' . $cost->id) }}"><i class="fa fa-info-circle"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $costs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
