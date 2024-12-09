@extends('layouts.AdminLTE.index')
@section('title', 'Reclamos del Cliente')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="btn-group pull-right">
                    <a href="{{ url('customer-complaints/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
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
                            <th>Nro° Reclamo</th>
                            <th>Nro° de Orden </th>
                            <th>Fecha de creación</th>
                            <th>Cliente</th>
                            <th>Obra</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer_complaints as $customer_complaint)
                            <tr>
                                <td>{{ $customer_complaint->id }}</td>
                                <td>{{ $customer_complaint->order_id}}</td>
                                <td>{{ $customer_complaint->date }}</td>
                                <td>{{ $customer_complaint->client->razon_social }}</td>
                                <td>{{ $customer_complaint->construction_site->description }}</td>
                                <td>{{ $customer_complaint->description }}</td>
                                <td>
                                    <span class="label label-{{ config('constants.customer_complaints_status_label.' . $customer_complaint->status) }}">{{ config('constants.customer_complaints_status.'. $customer_complaint->status) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($customer_complaint->status == 1)
                                        <button type="button" class="btn btn-success btn-xs change-status-button" data-id="{{ $customer_complaint->id }}">Resuelto</button>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- <a href="{{ url('wish-service/' . $wishservice->id) }}"><i class="fa fa-info-circle"></i></a> --}}
                                    {{-- <a href="{{ url('wish-service/' . $wishservices->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i></a> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $customer_complaints->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
@section('layout_js')
    <script>
        $(document).ready(function() {
            // Manejar el clic en el botón de cambiar estado
            $(document).on('click', '.change-status-button', function() {
                var id = $(this).data('id');
                var confirmed = confirm('¿Estás seguro de que deseas marcar este reclamo como resuelto?');
                if (confirmed) {
                    $.ajax({
                        url: '{{ url('customer-complaint/change-status') }}/' + id,
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
