@extends('layouts.AdminLTE.index')
@section('title', 'Pedido de Servicio')
@section('content')
{{ Form::open(['id' => 'form', 'files' => true]) }}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Agregar Pedido de Servicio</h5>
                </div>
                <div class="ibox-content pb-0">
                    <div class="row">
                        <div id="grafico"></div>
                        <div class="form-group col-md-4">
                            <label>Usuario</label>
                            <input class="form-control" type="text" name="requested_by" value="{{auth()->user()->name}}" disabled>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Sucursal</label>
                            {{ Form::select('branch_id', $branches, old('branch_id'), ['class' => 'form-control', 'select2', 'id' => 'branch_id']) }}
                        </div>
                        <div class="form-group col-md-2">
                            <label>Fecha</label>
                            <input class="form-control" type="text" name="date" value="{{ old('date', date('d/m/Y')) }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
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
                                            {{-- {{ Form::select('site_id', $construction_sites, old('site_id'), ['class' => 'form-control', 'select2', 'id' => 'site_id']) }} --}}
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
            <h3>Items</h3>
        </div>
        <div class="ibox-content pb-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Crear Pedido Servicio</h5>
                        </div>
                        <div class="ibox-content pb-0">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Servicio</label>
                                    {{ Form::select('service_id', $services, null, ['class' => 'form-control', 'id' => 'service_id']) }}
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Metros Cuadrados</label>
                                    <input class="form-control" type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" placeholder="Cantidad">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Niveles</label>
                                    <input class="form-control" type="text" name="level" id="level" value="{{ old('level') }}" placeholder="Niveles">
                                </div>
                                <div class="form-group col-md-1">
                                    <label>Agregar</label>
                                    <button type="button" class="btn btn-success" id="button_add_product"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-content table-responsive no-padding" id="detail_product">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-right">Cód</th>
                                        <th>Producto</th>
                                        <th class="text-right">Metros Cuadrados</th>
                                        <th class="text-right">Niveles</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_detail"></tbody>
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
                        <a href="{{ url('wish-service') }}" class="btn btn-sm btn-danger">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
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
                    url: '{{ route('wish_service.store') }}',
                    type: "POST",
                    data: new FormData(this),
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        redirect ("{{ url('wish-service') }}");
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

                $('#button_add_product').click(function() {
                var service_id = $('#service_id').val();
                var service_name = $('#service_id option:selected').text();
                var quantity = $('#quantity').val();
                var level = $('#level').val();

                // Validar que los campos no estén vacíos
                if (!service_id) {
                    alert('Por favor, seleccione un servicio.');
                    return;
                }
                if (!quantity || quantity <= 0) {
                    alert('Por favor, ingrese una cantidad válida.');
                    return;
                }
                if (!level) {
                    alert('Por favor, ingrese el nivel.');
                    return;
                }

                // Validar que el servicio no se agregue dos veces
                var exists = false;
                $('#tbody_detail tr').each(function() {
                    var existing_service_id = $(this).find('.service_id').val();
                    if (existing_service_id == service_id) {
                        exists = true;
                        return false; // salir del bucle
                    }
                });

                if (exists) {
                    alert('El servicio ya ha sido agregado.');
                    return;
                }

                var newRow = '<tr>' +
                    '<td>' + (++counter) + '</td>' +
                    '<td class="text-right">' + service_id + '</td>' +
                    '<td>' + service_name + '</td>' +
                    '<td class="text-right">' + quantity + '</td>' +
                    '<td class="text-right">' + level + '</td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-product">Eliminar</button></td>' +
                    '<input type="hidden" name="service_id[]" class="service_id" value="' + service_id + '">' +
                    '<input type="hidden" name="quantity[]" value="' + quantity + '">' +
                    '<input type="hidden" name="level[]" value="' + level + '">' +
                    '</tr>';
                $('#tbody_detail').append(newRow);

                // Limpiar los campos de entrada
                $('#service_id').val('');
                $('#quantity').val('');
                $('#level').val('');
            });

            // Eliminar servicio
            $(document).on('click', '.remove-product', function() {
                $(this).closest('tr').remove();
                counter--;
            });
        });


    </script>
@endsection
