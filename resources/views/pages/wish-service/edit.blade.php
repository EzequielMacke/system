@extends('layouts.AdminLTE.index')
@section('title', 'Editar Pedido Servicio')
@section('content')
{{ Form::model($wishService, ['route' => ['wish_service.update', $wishService->id], 'method' => 'PUT', 'id' => 'form']) }}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Editar Pedido Servicio</h5>
                </div>
                <div class="ibox-content pb-0">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Cliente</label>
                            {{ Form::select('client_id', $clients, null, ['class' => 'form-control', 'select2', 'id' => 'client_id']) }}
                        </div>
                        <div class="form-group col-md-6">
                            <label>Obra</label>
                            {{ Form::select('construction_site_id', $construction_sites, null, ['class' => 'form-control', 'select2', 'id' => 'construction_site_id']) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Fecha</label>
                            {{ Form::date('date_wish', null, ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6">
                            <label>Descripción</label>
                            {{ Form::textarea('observation', null, ['class' => 'form-control', 'rows' => 4]) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Servicios</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th>Metros Cuadrados</th>
                                        <th>Nivel</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="services-table">
                                    @foreach($wishService->wish_service_detail as $detail)
                                        <tr>
                                            <td>{{ Form::select('service_id[]', $services, $detail->services_id, ['class' => 'form-control']) }}</td>
                                            <td>{{ Form::number('quantity[]', $detail->quantity, ['class' => 'form-control']) }}</td>
                                            <td>{{ Form::text('level[]', $detail->level, ['class' => 'form-control']) }}</td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-service">Eliminar</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <div class="ibox-footer">
                    <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                    <a href="{{ route('wish_service') }}" class="btn btn-sm btn-danger">Cancelar</a>
                    <button type="button" class="btn btn-primary btn-sm" id="add-service">Agregar Servicio</button>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}
@endsection

@section('layout_js')
    <script>
        $(document).ready(function() {
            $('#client_id').change(function() {
                var client_id = $(this).val();
                if (client_id) {
                    $.ajax({
                        url: '{{ route('ajax.sites2') }}',
                        method: 'GET',
                        data: { client_id: client_id },
                        success: function(response) {
                            var constructionSiteSelect = $('#construction_site_id');
                            constructionSiteSelect.empty();
                            $.each(response, function(index, site) {
                                constructionSiteSelect.append('<option value="' + site.id + '">' + site.description + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
            });

            // Agregar nuevo servicio
            $('#add-service').click(function() {
                var newRow = '<tr>' +
                    '<td>{{ Form::select('service_id[]', $services, null, ['class' => 'form-control']) }}</td>' +
                    '<td>{{ Form::number('quantity[]', null, ['class' => 'form-control']) }}</td>' +
                    '<td>{{ Form::text('level[]', null, ['class' => 'form-control']) }}</td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-service">Eliminar</button></td>' +
                    '</tr>';
                $('#services-table').append(newRow);
            });

            // Eliminar servicio
            $(document).on('click', '.remove-service', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endsection
