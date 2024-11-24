@extends('layouts.AdminLTE.index')
@section('title', 'Editar Presupuesto de Produccion')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            {{ Form::open(['route' => ['budget-production.update', $budget_production->id], 'method' => 'PUT']) }}
            <div class="ibox-content">
                @include('partials.messages')
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Cliente</label>
                        <input type="text" name="client" value="" id="client" class="form-control" readonly>
                        <input type="hidden" name="client_id" value="" id="client_id">
                        <input type="hidden" name="wish_production_id" value="" id="wish_production_id">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Sucursal</label>
                        <input type="text" name="branch" value="" id="branch" class="form-control" readonly>
                        <input type="hidden" name="branch_id" value="" id="branch_id" >
                    </div>
                    <div class="form-group col-md-2">
                        <label>Fecha Pedido</label>
                        <input class="form-control" type="text" name="date_ped" id="date_ped" readonly>
                        <input class="form-control" type="hidden" name="total_amount" id="total" readonly>
                    </div>
                </div>

                <div class="ibox-title">
                    <h3>Items a Recepcionar</h3>
                </div>
                <div class="ibox-content pb-0">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Producto</label>
                            {{ Form::select('articulo_id', $articulos, old('articulo_id'), ['id' => 'articulo_id', 'placeholder' => 'Seleccione Articulo', 'class' => 'form-control', 'select2']) }} 
                            <input type="hidden" id="price">
                            <span class="red" id="text_last_purchases"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Descripción</label>
                            <input class="form-control" type="text" id="products_description" value="{{ old('products_description') }}" placeholder="Concepto Diferenciado">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Cantidad</label>
                            <input class="form-control" type="text" name="products_quantity" value="{{ old('products_quantity') }}" placeholder="Cantidad">
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
                                <th class="text-right">Cód</th>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Precio</th>
                                <th class="text-right">SubTotal</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_detail"></tbody>
                    </table>
                </div>
            </div>
            <div class="ibox-footer">
                <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                <a href="{{ url('budget-production') }}" class="btn btn-sm btn-danger">Cancelar</a>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@section('layout_css')
<style>
    #div_provider_data, #div_invoice_detail, #div_invoice_header, #div_image, #div_note_credits{
            position: relative;
            margin: auto;
            width: 100%;
            border: 3px solid #C8C4C4;
            padding: 10px;
            border-radius: 5px;
        }
</style>
@endsection

@section('layout_js')
    <script>
        var invoice_items_array = [];
        $(document).ready(function ()
        {
            @foreach($budget_production->budget_production_details as $budget)

            addToTable(
                        {{$budget->articulo_id}}, 
                        '{{config('constants.description.' . $budget->description)}}', 
                        {{$budget->quantity}},
                        '{{$budget->description}}',
                        '{{$budget->articulo->name}}',
                        {{$budget->articulo->price}});


            @endforeach
            $('#form').submit(function(e)
            {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('budget-production-store') }}',
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(data) {
                        redirect ("{{ url('budget-production') }}");
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                        $('input[type="submit"]').prop('disabled', false);
                    }
                });
            });

            $('#articulo_id').change(function() {
                var articuloId = $(this).val();
                // Realizar una solicitud al servidor para obtener el precio del artículo
                $.ajax({
                    url: '{{ url('ajax/articulo') }}',
                    method: 'GET',
                    data: { articulo_id: articuloId },
                    success: function(response) {
                        if(response.items)
                        {
                            $('#price').val(response.items.price);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        // Manejar el error si es necesario
                    }
                });
            });

            $("#button_search").click(function() {
                Search_Ped();
            });

            $("#btn_expiration_date").click(function() {
                AddExpirationDetail();
            });

            $('#number_ped').keypress(function(e){
                if (e.keyCode == 13)
                {
                    $("#button_search").click();
                    e.preventDefault();
                    return false;
                }
            });

            $("#button_add_product").click(function() {
                addProduct();
            });

            loadDate();
        });

        function loadDate()
        {
            $(".date").datepicker({
                format: 'dd/mm/yyyy',
                language: 'es',
                autoclose: true,
                todayBtn: true,
                todayBtn: "linked",
                daysOfWeekDisabled: [0]
            });

            $("[period-data-mask]").inputmask({
                alias: 'decimal',
                groupSeparator: '.',
                radixPoint: ',',
                autoGroup: true,
                allowMinus: false,
                rightAlign: false,
                digits: 0,
                removeMaskOnSubmit: true,
            });

            $("[date-mask]").inputmask({
                alias: 'date'
            });

            $(".date").datepicker({
                format: 'dd/mm/yyyy hh:ii',
                language: 'es',
                autoclose: true,
                todayBtn: true,
            });

            $("[date-mask]").inputmask({
                alias: 'date'
            });
        }

         function addProduct()
        {
            var product_name              = $("select[name='articulo_id'] option:selected").text();
            var product_id                = $("select[name='articulo_id']").val();
            var price                     = $("#price").val();
            var product_description       = $("#products_description").val();
            var product_quantity          = $("input[name='products_quantity']").val().replace(/\./g, '');
            product_quantity              = (product_quantity > 0 ? product_quantity : 1);

            if(product_id!='' && product_quantity!='')
            {
                if($.inArray(product_id, invoice_items_array) != '-1')
                {
                    if(confirm('Ya existe el producto, desea continuar?'))
                    {
                        var description = product_description ? product_description : product_name;
                        var subtotal = product_quantity * price;
                        addToTable(product_id, description, product_quantity, product_description,price,subtotal);
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    var description = product_description ? product_description : product_name;
                    var subtotal = product_quantity * price;
                    addToTable(product_id, description, product_quantity,product_description,product_name,price,subtotal);
                }

                $('#articulo_id').val(null).trigger('change');
                $("#products_description").val('');
                $("input[name='products_quantity']").val('');

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

        function addToTable(id, name, quantity, description,product_name,price,subtotal)
        {
            invoice_items_array.push(id);

            $('#tbody_detail').append('<tr>' +
                    '<td class="text-right">' + id +' <input type="hidden" name="detail_product_id[]" value="' + id + '"></td>' +
                    '<td>' + product_name + '<input type="hidden" name="detail_product_name[]" value="' + product_name + '"></td>' +
                    '<td class="text-center"> <input type="text" name="quantity_product[]" value="' + $.number(quantity, 0, ',', '.') + '" onkeyup="updateSubtotal($(this))"></td>' +
                    '<td class="text-center"><input style="width:150px;" type="text" name="detail_product_amount[]" onchange="addToTable($(this))" value="' + $.number(price, 0, ',', '.')  + '" onkeyup="updateSubtotal($(this))" autocomplete="off"></td>'+
                    '<td class="text-right subtotal">'  + $.number(subtotal, 0, ',', '.') + '</td>'+
                    '<td class="text-right"><a href="javascript:;" onClick="removeRow(this, '+ id +');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
                '</tr>');
                calculateTotal();
        }

        function removeRow(t, product_id)
        {
            $(t).parent().parent().remove();
            invoice_items_array.splice($.inArray(product_id, invoice_items_array), 1 );
        }

        function loadPeriodDataMaskDecimal()
        {
            $("[period-data-mask-decimal]").inputmask({
                alias: 'decimal',
                groupSeparator: '.',
                radixPoint: ',',
                autoGroup: true,
                allowMinus: false,
                rightAlign: true,
                digits: 2,
                removeMaskOnSubmit: true,
            });
        }

        function removeRow(detail_id)
        {
            invoice_items_array = jQuery.grep(invoice_items_array, function(value) {
                return value != detail_id;
            });

            $('input[name^="order_detail_id[]"]').each(function ()
            {
                if($(this).val() == detail_id)
                {
                    $(this).parent().remove();
                }
            });

            calculateIva();
        }

        changeStatus();
        function changeStatus()
        {
            $("#div_details, #div_deposito, #div_footer").hide();

            $("#number_ped").prop("readonly", false);
            $("#button_search").show();
        }
        function Search_Ped()
        {
            var number_ped        = $("#number_ped").val();
            var conteo           = 0;
            $('#tbody_detail').html('');

            if(number_ped != '')
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('ajax.budget-production') }}',
                    type: "GET",
                    data: { number_ped : number_ped},
                    success: function(data) {
                        $.each(data.items, function(index, element) {
                            invoice_items_array.push(element.product_id);

                            $('#tbody_detail').append('<tr>' +
                                '<td class="text-right">' + element.product_id + '</td>' +
                                '<td>' + element.product_name + '</td>' +
                                '<td class="text-center"> <input type="text" name="quantity_product[]" value="' + $.number(element.quantity, 0, ',', '.') + '" onkeyup="updateSubtotal($(this))"></td>' +
                                '<td class="text-center"><input style="width:150px;" type="text" name="detail_product_amount[]" onchange="addToTable($(this))" value="' + $.number(element.amount, 0, ',', '.')  + '" onkeyup="updateSubtotal($(this))" autocomplete="off"></td>'+
                                '<td class="text-right subtotal">'  + $.number(element.subtotal, 0, ',', '.') + '</td>'+
                                '<td class="text-right"><a href="javascript:;" onClick="removeRow(this, '+ element.product_id +');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
                                '<input type="hidden" name="detail_id[]" value="' + element.id + '">' +
                                '<input type="hidden" name="detail_product_id[]" value="' + element.product_id + '">' +
                                '<input type="hidden" name="detail_product_name[]" value="' + element.product_name + '">' +
                            '</tr>');
                            conteo++;
                            $('#branch_id').val(element.branch_id);
                            $('#branch').val(element.branch);
                            $('#date_ped').val(element.date);
                            $('#client_id').val(element.client_id);
                            $('#wish_production_id').val(element.wish_production_id);
                            $('#client').val(element.client);
                        });
                        if(conteo>0)
                        {
                            
                            $("#div_details, #div_footer").show();
                            calculateTotal();
                            $("#number_ped").prop("readonly", true);
                            $("#button_search").hide();
                            $("[select2]").select2({
                                language: 'es'
                            });
                        }else
                        {
                            swal({
                                title: "SISTEMA",
                                text: "No existe Pedido!!",
                                icon: "info",
                                button: "OK",
                            });
                            return false;
                        }
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                    }
                });

                loadProductsSelect();
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

        function updateSubtotal(input) 
        {
            var quantity = parseFloat(input.closest('tr').find('input[name="quantity_product[]"]').val().replace(/\./g, '').replace(',', '.')) || 0;
            var amount = parseFloat(input.closest('tr').find('input[name="detail_product_amount[]"]').val().replace(/\./g, '').replace(',', '.')) || 0;
            var subtotal = quantity * amount;
            input.closest('tr').find('.subtotal').text($.number(subtotal, 0, ',', '.'));
            calculateTotal();
        }
        function calculateTotal() {
            var total = 0;
            $('.subtotal').each(function() {
                var subtotalText = $(this).text().replace(/\./g, '').replace(',', '.').replace(/[^\d.-]/g, '');
                var subtotal = parseFloat(subtotalText) || 0;
                total += subtotal;
            });
            console.log(total);
            $('#total').val($.number(total, 0, ',', '.'));
        }
        function loadProductsSelect()
        {
            $("#div_emergency_mobile").hide();
            $("#emergency_mobile").val('');

            $("#articulo_id").select2({
                language: 'es',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ url('ajax/purchases-products') }}?purchase_order_number="+$("#number_ped").val(),
                    dataType: 'json',
                    method: 'GET',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term, social_reason_id: $('#social_reason_id').val() };
                    },
                    success: function(data) {
                        $(data.items).each(function(index, element) {

                            if(element.requires_mobile==1)
                            {
                                $("#div_emergency_mobile").show();
                                $("#emergency_mobile").val(element.requires_mobile);
                            }
                        });
                    },
                    processResults: function (data, params) {
                        return { results: data.items };
                    }
                },
                escapeMarkup: function (markup) { return markup; },
                templateResult: function (repo) {
                    if (repo.loading) return repo.text;
                    var markup = repo.name;
                    return markup;
                },
                templateSelection: function (repo) {
                    return repo.name;
                }
            }).on("select2:select", function (e) {
                var data_item = e.params.data;
                $('#articulo_id option:selected').attr('data-accounting-plan', data_item.accounting_plan_id);
                $('#articulo_id option:selected').attr('data-requires-mobile', data_item.requires_mobile);
                $('#articulo_id option:selected').attr('data-text', data_item.name);
            });
        }
        function removeRow(t, product_id)
        {
            $(t).parent().parent().remove();
            invoice_items_array.splice($.inArray(product_id, invoice_items_array), 1 );
            calculateTotal();
        }

    </script>
@endsection

