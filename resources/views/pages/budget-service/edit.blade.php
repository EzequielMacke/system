@extends('layouts.AdminLTE.index')
@section('title', 'Editar Presupuesto de Servicio')
@section('content')
{{ Form::model($budgetService, ['route' => ['budget_service.update', $budgetService->id], 'method' => 'PUT', 'id' => 'form']) }}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Editar Presupuesto de Servicio</h5>
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
                            {{ Form::date('date_budget', $date_budget, ['class' => 'form-control', 'id' => 'date_budget']) }}
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
                                        <th>Cantidad</th>
                                        <th>Nivel</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="services-table">
                                    @foreach($budgetService->budget_service_detail as $detail)
                                        <tr>
                                            <td>{{ Form::select('service_id[]', $services, $detail->service_id, ['class' => 'form-control service_id']) }}</td>
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
                    <button type="button" class="btn btn-primary btn-sm" id="add-service">Agregar Servicio</button>
                    <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                    <a href="{{ route('budget_service') }}" class="btn btn-sm btn-danger">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}
@endsection

@section('layout_js')
    <script>
        $(document).ready(function() {
            var counter = $('#services-table tr').length;

            $('#add-service').click(function() {
                var newRow = '<tr>' +
                    '<td>{{ Form::select('service_id[]', $services, null, ['class' => 'form-control service_id']) }}</td>' +
                    '<td>{{ Form::number('quantity[]', null, ['class' => 'form-control']) }}</td>' +
                    '<td>{{ Form::text('level[]', null, ['class' => 'form-control']) }}</td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-service">Eliminar</button></td>' +
                    '</tr>';
                $('#services-table').append(newRow);
                counter++;
            });

            $(document).on('click', '.remove-service', function() {
                if (counter > 1) {
                    $(this).closest('tr').remove();
                    counter--;
                } else {
                    alert('Debe haber al menos un servicio.');
                }
            });
        });
    </script>
@endsection
