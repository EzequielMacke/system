@extends('layouts.AdminLTE.index')
@section('title', 'Presupuesto Servicio')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="btn-group pull-right">
                    <a href="{{ url('budget-service/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                </div>
            </div>
            <div class="ibox-content pb-0">
                <div class="row">
                    <form method="GET">
                        <div class="form-group col-md-2">
                            <input type="text" class="form-control" name="s" placeholder="Buscar..." value="{{ request()->s }}">
                        </div>
                    </form>
                </div>
            </div>
            <div class="ibox-content table-responsive no-padding">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nro° de Presupuesto</th>
                            <th>Nro° Pedido</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Obra</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($budgetservices as $budgetservice)
                            <tr>
                                <td>{{ $budgetservice->id }}</td>
                                <td>{{ $budgetservice->wish_service->id}}</td>
                                <td>{{ $budgetservice->date_budgets }}</td>
                                <td>{{ $budgetservice->client->razon_social }}</td>
                                <td>{{ $budgetservice->construction_site->description }}</td>
                                <td>{{ $budgetservice->description }}</td>
                                <td>
                                    <span class="label label-{{ config('constants.budget_service_status_label.' . $budgetservice->status) }}">{{ config('constants.budget_service_status.'. $budgetservice->status) }}</span>
                                </td>
                                <td class="text-center">
                                    {{-- <a href="{{ url('wish-service/' . $wishservice->id) }}"><i class="fa fa-info-circle"></i></a> --}}
                                    {{-- <a href="{{ url('wish-service/' . $wishservices->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i></a> --}}
                </div>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $budgetservices->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
