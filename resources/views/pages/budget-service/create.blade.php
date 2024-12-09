@extends('layouts.AdminLTE.index')
@section('title', 'Pedido de Presupuesto')
@section('content')
{{ Form::open(['id' => 'form', 'files' => true]) }}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Agregar Presupuesto</h5>
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
                            <input class="form-control" type="text" name="date" value="{{ old('date', date('d/m/Y')) }}" readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Moneda</label>
                            {{ Form::select('currency', config('constants.currency'), old('currency'), ['class' => 'form-control', 'select2', 'id' => 'currency']) }}
                        </div>
                        <div class="form-group col-md-2">
                            <label>Impuesto</label>
                            {{ Form::select('tax', config('constants.tax'), old('tax'), ['class' => 'form-control', 'select2', 'id' => 'tax']) }}
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
                                            {{-- {{ Form::select('site_id', $construction_sites, old('site_id'), ['class' => 'form-control', 'select2', 'id' => 'site_id']) }} --}}
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
                                        <label>Nroº Pedido</label>
                                            <select class="form-control" name="wish_id" id="wish_id"></select>
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
        <div class="ibox-content table-responsive no-padding" id="detail_product">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-right">Cód</th>
                        <th>Servicio</th>
                        <th>Ensayo</th>
                        <th class="text-right">Metros Cuadrados</th>
                        <th class="text-right">Niveles</th>
                        <th class="text-right">Necesidad por m2</th>
                        <th class="text-right">Cantidad Necesaria</th>
                        <th class="text-right">Precio por Unidad</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right"></th>
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
        <a href="{{ url('budget-service') }}" class="btn btn-sm btn-danger">Cancelar</a>
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
                    url: '{{ route('budget_service.store') }}',
                    type: "POST",
                    data: new FormData(this),
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        redirect ("{{ url('budget-service') }}");
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

            $('#site_id').change(function()
            {
                var client_id = $("select[name='client_id']").val();
                var site_id = $("select[name='site_id']").val();
                // Realizar una solicitud al servidor para obtener el precio del artículo
                $.ajax({
                    url: '{{ url('ajax/wish') }}',
                    method: 'GET',
                    data: { client_id: client_id, site_id: site_id },
                    success: function(response) {
                        var wishSelect = $('#wish_id');
                        wishSelect.empty();
                        wishSelect.append('<option value="">Seleccione un pedido</option>');
                        $.each(response.items, function(index,value){
                            wishSelect.append('<option value="' + value.id + '">' + value.date_budget + '</option>');
                        })
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        // Manejar el error si es necesario
                    }
                });
            });

            $('#wish_id').change(function()
            {
                var wish_id = $("select[name='wish_id']").val();
                // Realizar una solicitud al servidor para obtener el precio del artículo
                $.ajax({
                    url: '{{ url('ajax/wish') }}',
                    method: 'GET',
                    data: { wish_id: wish_id },
                    success: function(response) {
                        $('#tbody_detail').empty();

                        $.each(response.items, function(index,value){
                            addToTable(value.service_id, value.description, value.quantity, value.description,value.service_name,value.level,value.input,value.input_price,value.measurement,value.input_id);
                        })
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        // Manejar el error si es necesario
                    }
                });
            });
        });

        function addProduct()
        {
            var service_name              = $("select[name='service_id'] option:selected").text();
            var service_id                = $("select[name='service_id']").val();
            var product_description       = '';
            var quantity          = $("input[name='quantity']").val().replace(/\./g, '');
            quantity              = (quantity > 0 ? quantity : 1);
            if(service_id!='' && quantity!='') {
                var level = $("input[name='level']").val().replace(/\./g, '');
                level = (level > 0 ? level : 1);
            }
            level              = (level > 0 ? level : 1);

            if(service_id!='' && quantity!='')
            {
                if($.inArray(service_id, invoice_items_array) != '-1')
                {
                    if(confirm('Ya existe el producto, desea continuar?'))
                    {
                        var description = product_description ? product_description : service_name;

                        addToTable(service_id, description, quantity, product_description,level,input_id);
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    var description = product_description ? product_description : service_name;

                    addToTable(service_id, description, quantity,product_description,service_name,level,input_id);
                }

                $('#service_id').val(null).trigger('change');
                $("#products_description").val('');
                $("input[name='quantity']").val('');
                $("input[name='level']").val('');

            }
            else
            {
                swal({
                    title: "SISTEMA",
                    text: "Hay campos vacíos",
                    icon: "warning",
                    button: "OK",
                });
                return false;
            }
        }

        function calculateBasedOnInput(input) {
            // Encuentra la fila actual
            let row = $(input).closest('tr');

            // Obtén los valores de los campos relevantes
            let squareMeters = parseFloat(row.find('#metro').val());
            let quantityPerSquare = parseFloat(row.find('.quantity-per-square-input').val()) || 0; // Cantidad necesaria por metros cuadrados (editable)
            let price = parseFloat(row.find('.price-input').val()) || 150000; // Precio unitario (no editable)
            let level = parseFloat(row.find('input[name="new_level[]"]').val()) || 1; // Obtener el nivel (suponiendo que el nivel se guarda como un campo oculto)

            // Calcula la cantidad necesaria (metros cuadrados / cantidad por metro cuadrado)
            let calculatedQuantity = quantityPerSquare > 0 ? Math.floor(squareMeters * quantityPerSquare) : 0;

            // Calcula el subtotal (cantidad necesaria * precio unitario * nivel)
            let subtotal = calculatedQuantity * price * level; // Multiplica por el nivel
            // Actualiza las celdas con los valores formateados
            row.find('.calculated-quantity').text(formatNumber(calculatedQuantity));
            row.find('.subtotal-cell').text(formatNumber(subtotal));

            // Recalcular el total de subtotales
            updateTotalSubtotal();
        }

        function updateTotalSubtotal() {
            let total = 0;
            // Suma todos los subtotales en la columna correspondiente
            $('#tbody_detail .subtotal-cell').each(function() {
                let value = parseInt($(this).text().replace(/\./g, ''), 10) || 0; // Quita los puntos antes de sumar
                total += value;
            });
            // Actualiza el total en la interfaz (si hay un elemento para mostrar el total)
            $('#total').text(formatNumber(total)); // Asegúrate de tener un elemento con id 'total' para mostrar el total
        }

        // Función para formatear números con separador de miles y sin decimales
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        function addToTable(id, name, quantity, description, service_name, level, input, input_price, measurement, input_id) {
    if (!invoice_items_array.includes(id)) {
        invoice_items_array.push(id);
        $('#tbody_detail').append('<tr>' +
            '<td>' + (++counter) + '</td>' +
            '<td class="text-right">' + id + ' <input type="hidden" name="service_id[]" value="' + id + '"></td>' +
            '<td>' + service_name + '<input type="hidden" name="service_name[]" value="' + service_name + '"></td>' +
            '<td></td>' + // Celda vacía para alinear con el ID
            '<td class="text-right">' + formatNumber(quantity) + '<input type="hidden" class="square-meters-input" name="quantity[]" value="' + quantity + '"></td>' +
            '<input type="hidden" id="metro" value="' + quantity + '">' +
            '<td class="text-right">' + level + '<input type="hidden" name="level[]" value="' + level + '"></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td> </td>' +
            '</tr>');
    }
    $('#tbody_detail').append('<tr>' +
        '<td>' + input_id + '<input type="hidden" name="input_id[]" value="' +id+'-'+input_id+ '"></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td>' + input + '<input type="hidden" name="input_description[]" value="' + input + '"></td>' +
        '<td></td>' +
        '<td></td>' +
        '<input type="hidden" name="new_metro[]" id="metro" value="' + quantity + '">' +
        '<input type="hidden" name="new_level[]" value="' + level + '">' +
        '<td class="text-right">' +
            '<input type="number" class="form-control quantity-per-square-input" name="quantity_per_meter[]" value="" oninput="calculateBasedOnInput(this)" onchange="calculateBasedOnInput(this)">' +
        '</td>' +
        '<td class="text-right calculated-quantity" placeholder="0">' +
            '<input type="number" class="form-control calculated-quantity-input" name="calculated_quantity[]" value="" readonly>'+
        '</td>' +
        '<td class="text-right">' + formatNumber(input_price) + '<input type="hidden" class="price-input" name="price[]" value="' + input_price + '"></td>' +
        '<td class="text-right subtotal-cell">0</td>' +
        '<td></td>' +
        '<td class="text-right"><a href="javascript:;" onClick="removeRow(this, ' + input_id + ');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
        '</tr>');
}

        function formatPrice(input) {
            let value = input.value.replace(/[^0-9.]/g, '');
            let parts = value.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            input.value = parts.join('.');
        }

        function removeRow(t, service_id) {
            $(t).parent().parent().remove();
            invoice_items_array.splice($.inArray(service_id, invoice_items_array), 1 );
            // calculateGrandTotal();
            counter--;
        }
    </script>
@endsection
