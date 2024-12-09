@extends('layouts.AdminLTE.index')
@section('title', 'Insumos Utilizados')
@section('content')
{{ Form::open(['id' => 'form', 'files' => true]) }}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Generar Insumos Utilizados</h5>
                </div>
                <div class="ibox-content pb-0">
                    <div class="row">
                        <div id="grafico"></div>
                        <div class="form-group col-md-2">
                            <label>Usuario</label>
                            <input class="form-control" type="text" name="requested_by" value="{{auth()->user()->name}}" disabled>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Sucursal</label>
                            {{ Form::select('branch_id', $branches, old('branch_id'), ['class' => 'form-control', 'select2', 'id' => 'branch_id']) }}
                        </div>

                        <div class="form-group col-md-2">
                            <label>Fecha</label>
                            <input class="form-control" type="text" name="date" id="date" value="{{ old('date', date('d/m/Y')) }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label>Nro Insumo</label>
                            <input class="form-control" type="text" name="input" value="{{ $newInputNumber }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Datos del Cliente</h5>
                            </div>
                            <div class="ibox-content pb-0">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Cliente</label>
                                        <select class="form-control" name="client_id" id="client_id"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Datos de Obra</h5>
                            </div>
                            <div class="ibox-content pb-0">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Obra</label>
                                            <select class="form-control" name="site_id" id="site_id"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Datos de Orden</h5>
                            </div>
                            <div class="ibox-content pb-0">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Nroº Orden de Servicio</label>
                                            <select class="form-control" name="order_id" id="order_id"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h3>Detalle de Insumos Utilizados</h3>
        </div>
        <div class="ibox-content table-responsive no-padding">
            <table class="table table-hover table-striped mb-0" id="details_table">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Ensayo</th>
                        <th>Cantidad de Ensayos (a)</th>
                        <th>Materiales</th>
                        <th>Cantidad de Materiales (b)</th>
                        <th>Total (a x b)</th>
                        <th>Unidad de Medida</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="ibox-content pb-0">
        <div class="row">
            <div class="form-group col-md-7">
                <label>Observación</label>
                <textarea class="form-control" name="observation" rows="4">{{ old('observation') }}</textarea>
            </div>
        </div>
    </div>
    <div class="ibox-footer">
        <input type="submit" class="btn btn-sm btn-success" value="Guardar">
        <a href="{{ url('input-used') }}" class="btn btn-sm btn-danger">Cancelar</a>
    </div>
{{ Form::close() }}
@endsection

@section('layout_js')
    <script>
        var counter = 0;
        var invoice_items_array = [];

        $(document).ready(function ()
        {

            $('#form').submit(function(e) {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('input_used.store') }}',
                    type: "POST",
                    data: new FormData(this),
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        redirect ("{{ url('input-used') }}");
                    },
                    error: function(data){

                        laravelErrorMessages(data);

                        $('input[type="submit"]').prop('disabled', false);
                    }
                });
            });

            $("#client_id").select2({
                language: 'es',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ url('ajax/clients') }}',
                    dataType: 'json',
                    // cache: true,
                    method: 'GET',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: data.items
                        };
                    }
                },
                escapeMarkup: function (markup) { return markup; },
                templateResult: function (client) {
                    if (client.loading) return client.text;

                    var markup = client.name + "<br>" +
                            "<i class='fa fa-id-card'></i> " + client.ruc ;

                    return markup;
                },
                templateSelection: function (client) {
                    return client.name + ' | ' + client.ruc;
                }
            });

            $('#client_id').change(function()
            {
                var client_id = $("select[name='client_id']").val();
                // Realizar una solicitud al servidor para obtener el precio del artículo
                $.ajax({
                    url: '{{ url('ajax/sites') }}',
                    method: 'GET',
                    data: { client_id: client_id },
                    success: function(response) {
                        var siteSelect = $('#site_id');
                        siteSelect.empty();
                        siteSelect.append('<option value="">Seleccione una obra</option>');
                        $.each(response.items, function(index,value){
                            siteSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                        })
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        // Manejar el error si es necesario
                    }
                });
            });

            $('#site_id').change(function() {
                var client_id = $('#client_id').val();
                var site_id = $('#site_id').val();
                if (client_id && site_id) {
                    $.ajax({
                        url: '{{ route('ajax.inputused') }}',
                        method: 'GET',
                        data: {
                            client_id: client_id,
                            site_id: site_id
                        },
                        success: function(response) {
                            console.log(response);
                            var orderSelect = $('#order_id');
                            orderSelect.empty();
                            orderSelect.append('<option value="">Seleccione una orden</option>');
                            $.each(response.items, function(index, value) {
                                orderSelect.append('<option value="' + value.id + '">'+'Cod:' + value.id + ' - Fecha:' + value.date_created + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            // Manejar el error si es necesario
                        }
                    });
                }
            });
            $('#order_id').change(function() {
                var order_id = $(this).val();

                if (order_id) {
                    $.ajax({
                        url: '{{ route('ajax.inputused') }}',
                        method: 'GET',
                        data: {
                            order_id: order_id
                        },
                        success: function(response) {
                            var detailsTable = $('#details_table tbody');
                            detailsTable.empty();

                            $.each(response.items, function(index, value) {
                                var row = '<tr>' +
                                    '<td>' + value.input_id + '<input class="form-control" type="hidden" name="input_id[]" id="input_id"  value="'+value.input_id+'" ></td>' +
                                    '<td>' + value.description + '</td>' +
                                    '<td><input class="form-control" type="text" name="input_quantity[]" id="input_quantity"  value="'+value.quantity+'" readonly></td>' +
                                    '<td></td>' + // Celda vacía para alinear los materiales
                                    '</tr>';
                                detailsTable.append(row);
                                var regi = 0;
                                $.each(value.materials, function(i, material) {
                                    var subtotal = value.quantity * material.quantity;
                                    var regi = regi + 1;
                                    var materialRow = '<tr>' +
                                        '<td><input class="form-control" type="hidden" name="regi[]" id="regi"  value="'+regi+'" ></td>' + // Celda vacía para alinear con input_id
                                        '<td></td>' + // Celda vacía para alinear con description
                                        '<td></td>' + // Celda vacía para alinear con quantity
                                        '<td>' + material.description + '<input class="form-control" type="hidden" name="material_id[]" id="material_id"  value="'+material.material_id+'" ><input class="form-control" type="hidden" name="input_idaux[]" id="input_idaux"  value="'+value.input_id+'" ></td>' +
                                    '<td><input class="form-control" type="text" name="material_quantity[]" id="material_quantity"  value="'+material.quantity+'" readonly><input class="form-control" type="hidden" name="input_quantityaux[]" id="input_quantityaux"  value="'+value.quantity+'" ></td>' +
                                    '<td><input class="form-control" type="text" name="subtotal[]" id="subtotal"  value="'+subtotal+'" readonly></td>' +
                                        '<td>' + material.measurementl + '<input class="form-control" type="hidden" name="mesarument_id[]" id="mesarument_id"  value="'+material.measurement+'" ></td>' +
                                        '</tr>';
                                    detailsTable.append(materialRow);
                                });
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            // Manejar el error si es necesario
                        }
                    });
                }
            });
        });


    </script>
@endsection
