@extends('layouts.AdminLTE.index')
@section('title', 'Reclamos del Cliente')
@section('content')
{{ Form::open(['id' => 'form', 'files' => true]) }}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Registrar Reclamos del Cliente</h5>
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
                            <label>Nro Reclamo</label>
                            <input class="form-control" type="text" name="input" value="{{ $newCompla }}" readonly>
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
            <h3>Detalle de Trabajos Realizados</h3>
        </div>
        <div class="ibox-content table-responsive no-padding">
            <h5>Detalles del Servicio y Deseos</h5>
            <table class="table table-hover table-striped mb-0" id="details_table">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Metros Cuadrados</th>
                        <th>Niveles</th>
                        <th>Cod</th>
                        <th>Ensayo</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Las filas se agregarán aquí -->
                </tbody>
            </table>
        </div>
        <div class="ibox-content table-responsive no-padding">
            <h5>Detalles de los Funcionarios</h5>
            <table class="table table-hover table-striped mb-0" id="oficial_details_table">
                <thead>
                    <tr>
                        <th>Cod</th>
                        <th>Funcionario</th>
                        <th>Cédula</th>
                        <th>Cargo</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Las filas se agregarán aquí -->
                </tbody>
            </table>
        </div>
    </div>
    <div class="ibox-content pb-0">
        <div class="row">
            <div class="form-group col-md-7">
                <label>Descripción</label>
                <textarea class="form-control" name="observation" rows="4">{{ old('observation') }}</textarea>
            </div>
        </div>
    </div>
    <div class="ibox-footer">
        <input type="submit" class="btn btn-sm btn-success" value="Guardar">
        <a href="{{ url('customer-complaints') }}" class="btn btn-sm btn-danger">Cancelar</a>
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
                    url: '{{ route('customer_complaints.store') }}',
                    type: "POST",
                    data: new FormData(this),
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        redirect ("{{ url('customer-complaints') }}");
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

            $('#client_id, #site_id').change(function() {
                var client_id = $('#client_id').val();
                var site_id = $('#site_id').val();
                if (client_id && site_id) {
                    $.ajax({
                        url: '{{ route('ajax.customer') }}',
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
                        url: '{{ route('ajax.customer') }}',
                        method: 'GET',
                        data: {
                            order_id: order_id
                        },
                        success: function(response) {
                            console.log(response);
                            var detailsTable = $('#details_table tbody');
                            var oficialDetailsTable = $('#oficial_details_table tbody');
                            detailsTable.empty();
                            oficialDetailsTable.empty();

                            // Agrupar detalles por service_id
                            var groupedDetails = {};
                            $.each(response.wish_details, function(index, value) {
                                if (!groupedDetails[value.service_id]) {
                                    groupedDetails[value.service_id] = {
                                        wish_details: [],
                                        service_details: []
                                    };
                                }
                                groupedDetails[value.service_id].wish_details.push(value);
                            });

                            $.each(response.service_details, function(index, value) {
                                if (!groupedDetails[value.service_id]) {
                                    groupedDetails[value.service_id] = {
                                        wish_details: [],
                                        service_details: []
                                    };
                                }
                                groupedDetails[value.service_id].service_details.push(value);
                            });

                            // Agregar detalles a la tabla de manera intercalada, comenzando con wish_details
                            $.each(groupedDetails, function(service_id, details) {
                                var maxLength = Math.max(details.wish_details.length, details.service_details.length);
                                for (var i = 0; i < maxLength; i++) {
                                    if (i < details.wish_details.length) {
                                        var wish = details.wish_details[i];
                                        var wishRow = '<tr>' +
                                            '<td>' + wish.service_name + '</td>' +
                                            '<td>' + wish.quantity + '</td>' +
                                            '<td>' + wish.level + '</td>' +
                                            '<td></td>' + // Celda vacía para alinear con service_details
                                            '<td></td>' + // Celda vacía para alinear con service_details
                                            '<td></td>' + // Celda vacía para alinear con service_details
                                            '<td></td>' + // Celda vacía para alinear con service_details
                                            '</tr>';
                                        detailsTable.append(wishRow);
                                    }

                                    if (i < details.service_details.length) {
                                        var service = details.service_details[i];
                                        var serviceRow = '<tr>' +
                                            '<td></td>' + // Celda vacía para alinear con wish_details
                                            '<td></td>' + // Celda vacía para alinear con wish_details
                                            '<td></td>' + // Celda vacía para alinear con wish_details
                                            '<td>' + service.input_id + '</td>' +
                                            '<td>' + service.input_name + '</td>' +
                                            '<td>' + service.input_quantity + '</td>' +
                                            '</tr>';
                                        detailsTable.append(serviceRow);
                                    }
                                }
                            });

                            // Agregar detalles de los funcionarios a la tabla
                            $.each(response.oficial_details, function(index, value) {
                                var row = '<tr>' +
                                    '<td>' + value.order_id + '</td>' +
                                    '<td>' + value.oficial_id + '</td>' +
                                    '<td>' + value.name + '</td>' +
                                    '<td>' + value.document + '</td>' +
                                    '<td>' + value.role + '</td>' +
                                    '</tr>';
                                oficialDetailsTable.append(row);
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
