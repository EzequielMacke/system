@extends('layouts.AdminLTE.index')
@section('title', 'Orden de Servicio')
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
                            <label>Fecha de Inicio</label>
                            <input class="form-control" type="date" name="start_date" id="start_date">
                        </div>
                        {{-- <div class="form-group col-md-2">
                            <label>Impuesto</label>
                            {{ Form::select('tax', config('constants.tax'), old('tax'), ['class' => 'form-control', 'select2', 'id' => 'tax']) }}
                        </div> --}}
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
                                <h5>Datos de Presupuesto</h5>
                            </div>
                            <div class="ibox-content pb-0">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Nroº Presupuesto</label>
                                            <select class="form-control" name="budget_id" id="budget_id"></select>
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
                        <th>Producto</th>
                        <th class="text-right">Cantidad</th>
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
            var today = new Date();
            var day = String(today.getDate()).padStart(2, '0');
            var month = String(today.getMonth() + 1).padStart(2, '0');
            var year = today.getFullYear();
            var formattedDate = year + '-' + month + '-' + day;
            $('#start_date').val(formattedDate);

            $('#form').submit(function(e) {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('order_service.store') }}',
                    type: "POST",
                    data: new FormData(this),
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        redirect ("{{ url('order-service') }}");
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
                    data: { client_id: client_id, site_id: site_id,type:'presupuesto' },
                    success: function(response) {
                        var wishSelect = $('#budget_id');
                        wishSelect.empty();
                        wishSelect.append('<option value="">Seleccione un presupuesto</option>');
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

            $('#budget_id').change(function()
            {
                var budget_id = $("select[name='budget_id']").val();
                // Realizar una solicitud al servidor para obtener el precio del artículo
                $.ajax({
                    url: '{{ url('ajax/wish') }}',
                    method: 'GET',
                    data: { budget_id: budget_id },
                    success: function(response) {
                        $('#tbody_detail').empty();

                        $.each(response.items, function(index,value){
                            addToTable(value.service_id, value.description, value.quantity, value.description,value.service_name);
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

            if(service_id!='' && quantity!='')
            {
                if($.inArray(service_id, invoice_items_array) != '-1')
                {
                    if(confirm('Ya existe el producto, desea continuar?'))
                    {
                        var description = product_description ? product_description : service_name;

                        addToTable(service_id, description, quantity, product_description);
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    var description = product_description ? product_description : service_name;

                    addToTable(service_id, description, quantity,product_description,service_name);
                }

                $('#service_id').val(null).trigger('change');
                $("#products_description").val('');
                $("input[name='quantity']").val('');

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

        function addToTable(id, name, quantity, description,service_name)
        {
            counter++;
            invoice_items_array.push(id);

            $('#tbody_detail').append('<tr>' +
                    '<td>' + counter + '</td>' +
                    '<td class="text-right">' + id +' <input type="hidden" name="service_id[]" value="' + id + '"></td>' +
                    '<td>' + service_name + '<input type="hidden" name="service_name[]" value="' + service_name + '"></td>' +
                    '<td style="width:3%;" class="text-right">' + quantity + '<input type="hidden" name="quantity[]" value="' + quantity + '"></td>' +
                    '<input type="hidden" class="form-control" name="detail_product_description[]" value="' + description + '">'+
                    '<td class="text-right"><a href="javascript:;" onClick="removeRow(this, '+ id +');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
                '</tr>');

        }

        function formatPrice(input)
        {
            let value = input.value.replace(/[^0-9.]/g, '');
            let parts = value.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            input.value = parts.join('.');
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
