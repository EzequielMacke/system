@extends('layouts.AdminLTE.index')
@section('title', 'Pedido Servicio')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="btn-group pull-right">
                    <a href="{{ url('wish-service/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
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
                            <th>Nro°</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Obra</th>
                            <th>Descripcion</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wishservices as $wishservice)
                            <tr>
                                <td>{{ $wishservice->id }}</td>
                                <td>{{ $wishservice->date_wish }}</td>
                                <td>{{ $wishservice->client->razon_social }}</td>
                                <td>{{ $wishservice->construction_site->description }}</td>
                                <td>{{ $wishservice->observation }}</td>
                                <td>
                                    <span class="label label-{{ config('constants.wish_service_status_label.' . $wishservice->status) }}">{{ config('constants.wish_service_status.'. $wishservice->status) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($wishservice->status != 3 && $wishservice->status != 2)
                                        <button type="button" class="btn btn-danger btn-xs change-status-button" data-id="{{ $wishservice->id }}">Eliminar</button>
                                    @endif
                                    @if($wishservice->status == 1)
                                        <a href="{{ url('wish-service/' . $wishservice->id . '/edit') }}" class="btn btn-warning btn-xs">Modificar</a>
                                    @endif
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
            {{ $wishservices->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
@section('layout_js')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.change-status-button', function() {
                var id = $(this).data('id');
                var confirmed = confirm('¿Estás seguro de que deseas cambiar el estado?');
                if (confirmed) {
                    $.ajax({
                        url: '{{ url('wish-service/change-status') }}/' + id,
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Error al cambiar el estado.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            alert('Error al cambiar el estado.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
