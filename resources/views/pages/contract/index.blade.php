@extends('layouts.AdminLTE.index')
@section('title', 'Contrato de Servicio')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="btn-group pull-right">
                    <a href="{{ url('contract/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
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
                            <th>Nro° de Contrato</th>
                            <th>Nro° de Presupuesto</th>
                            <th>Fecha de Creacion</th>
                            <th>Fecha de Firma</th>
                            <th>Cliente</th>
                            <th>Obra</th>
                            <th>Descripción</th>
                            <th>Plazo de cumplimiento</th>
                            <th>Tema</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $contract)
                            <tr>
                                <td>{{ $contract->id }}</td>
                                <td>{{ $contract->budget_service->id}}</td>
                                <td>{{ $contract->date_created }}</td>
                                <td>{{ $contract->date_signed }}</td>
                                <td>{{ $contract->client->razon_social }}</td>
                                <td>{{ $contract->construction_site->description }}</td>
                                <td>{{ $contract->issue }}</td>
                                <td>{{ $contract->term }} días</td>
                                <td>{{ $contract->description }}</td>
                                <td>
                                    <span class="label label-{{ config('constants.contract_status_label.' . $contract->status) }}">{{ config('constants.contract_status.'. $contract->status) }}</span>
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
            {{ $contracts->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
