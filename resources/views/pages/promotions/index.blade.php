@extends('layouts.AdminLTE.index')
@section('title', 'Promociones')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="btn-group pull-right">
                    <a href="{{ url('promotions/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
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
                            <th>Nro° de promoción</th>
                            <th>Fecha de inicio</th>
                            <th>Fecha de fin</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotions as $promotion)
                            <tr>
                                <td>{{ $promotion->id }}</td>
                                <td>{{ $promotion->start_date }}</td>
                                <td>{{ $promotion->end_date }}</td>
                                <td>{{ $promotion->description }}</td>
                                <td>
                                    <span class="label label-{{ config('constants.promotions_label.' . $promotion->status) }}">{{ config('constants.promotions.'. $promotion->status) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($promotion->status == 1)
                                        <button type="button" class="btn btn-danger btn-xs change-status-button" data-id="{{ $promotion->id }}">Eliminar</button>
                                        <a href="{{ route('promotions.edit', $promotion->id) }}" class="btn btn-warning btn-xs">Modificar</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $promotions->appends(request()->query())->links() }}
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
                var confirmed = confirm('¿Estás seguro de que deseas cambiar el estado a eliminado?');
                if (confirmed) {
                    $.ajax({
                        url: '{{ url('promotions/change-status') }}/' + id,
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
