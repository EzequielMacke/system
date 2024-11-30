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
                <div class="form-group col-md-4">
                    <label>Servicios</label>
                    {{ Form::select('service_id', $services, old('service_id'), ['id' => 'service_id', 'placeholder' => 'Seleccione Servicio', 'class' => 'form-control', 'select2']) }}
                    <span class="red" id="text_last_purchases"></span>
                </div>
                <div class="form-group col-md-2">
                    <label>Metros Cuadrados</label>
                    <input class="form-control" type="text" name="quantity" value="{{ old('quantity') }}" placeholder="Cantidad">
                </div>
                <div class="form-group col-md-2">
                    <label>Niveles</label>
                    <input class="form-control" type="text" name="level" value="{{ old('level') }}" placeholder="Niveles">
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
                        <th class="text-right">Cantidad</th>
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
        <a href="{{ url('purchases-orders') }}" class="btn btn-sm btn-danger">Cancelar</a>
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

            $("#button_add_product").click(function() {
                addProduct();
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
        });

        function addProduct() {
    var service_name = $("select[name='service_id'] option:selected").text();
    var service_id = $("select[name='service_id']").val();
    var product_description = '';
    var quantity = $("input[name='quantity']").val().replace(/\./g, '');
    var level = $("input[name='level']").val(); // Obtener el valor del nivel
    quantity = (quantity > 0 ? quantity : 1);

    if (service_id != '' && quantity != '' && level != '') { // Verificar que el nivel no esté vacío
        if ($.inArray(service_id, invoice_items_array) != '-1') {
            if (confirm('Ya existe el producto, desea continuar?')) {
                var description = product_description ? product_description : service_name;
                addToTable(service_id, description, quantity, product_description, level); // Pasar el nivel
            } else {
                return false;
            }
        } else {
            var description = product_description ? product_description : service_name;
            addToTable(service_id, description, quantity, product_description, level); // Pasar el nivel
        }
        $('#service_id').val(null).trigger('change');
        $("#products_description").val('');
        $("input[name='quantity']").val('');
        $("input[name='level']").val(''); // Limpiar el campo de nivel
    } else {
        swal({
            title: "SISTEMA",
            text: "Hay campos vacíos",
            icon: "warning",
            button: "OK",
        });
        return false;
    }
}

function addToTable(service_id, description, quantity, product_description, level) {
    // Aquí puedes agregar la lógica para agregar la fila a la tabla
    $('#tbody_detail').append('<tr>' +
        '<td>' + counter + '</td>' +
        '<td class="text-right">' + service_id + ' <input type="hidden" name="service_id[]" value="' + service_id + '"></td>' +
        '<td>' + description + '<input type="hidden" name="service_name[]" value="' + description + '"></td>' +
        '<td class="text-right"><input type="text" class="form-control" name="quantity[]" value="' + quantity + '"></td>' +
        '<td class="text-right">' + level + '<input type="hidden" name="level[]" value="' + level + '"></td>' + // Nueva columna para nivel
        '<td class="text-right"><a href="javascript:;" onClick="removeRow(this, ' + service_id + ');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
    '</tr>');
}

        function removeRow(t, service_id)
        {
            $(t).parent().parent().remove();
            invoice_items_array.splice($.inArray(service_id, invoice_items_array), 1 );
            // calculateGrandTotal();
            counter--;
        }

    </script>
@endsection
