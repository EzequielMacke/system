@extends('layouts.AdminLTE.index')
@section('title', 'Orden de Produccion')
@section('content')
<div class="row">
    {{ Form::open(['id' => 'form']) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Agregar Orden de Produccion</h5>
                        </div>
                        <div class="ibox-content pb-0">
                            <div class="row">
                                <div class="form-group col-md-2">
                                    <label>Numero Presupuesto</label>
                                    <input class="form-control" type="text" name="number_budget" id="number_budget" placeholder="Numero Presupuesto" autofocus>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Fecha</label>
                                    <input class="form-control" type="text" name="date" value="{{ old('date', date('d/m/Y')) }}" readonly>
                                </div>
                                <div class="form-group col-md-2">
                                    <br>
                                    <button type="button" class="btn btn-primary" name="button_search" id="button_search"><i class="fa fa-search"></i> BUSCAR PRESUPUESTO</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins" id="div_details">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Cliente</label>
                            <input type="text" name="client" value="" id="client" class="form-control" readonly>
                            <input type="hidden" name="client_id" value="" id="client_id">
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
                </div><br><br>
                <div class="ibox-title">
                    <h3>Items a Fabricar</h3>
                </div>
                <div class="ibox-content table-responsive no-padding" id="detail_product">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Cód</th>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody_detail"></tbody>
                    </table>
                </div>
            </div>
            
            <div class="ibox-footer" id="div_footer">
                <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                <a href="{{ url('budget-production') }}" class="btn btn-sm btn-danger">Cancelar</a>
            </div>
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Materia Prima</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Materia Prima</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody id="modalBody">
                                </tbody>
                                <tfoot id="modalfoot">
                                </tfoot>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        {{ Form::close() }}
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

            $('#form').submit(function(e)
            {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('production-order-store') }}',
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(data) {
                        redirect ("{{ url('production-order') }}");
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
            $(document).ready(function(){
                $(document).on('click', '.open-modal', function() {
                    var number_budget = $("#number_budget").val();
                    var productId = $(this).closest('tr').find('td:first').text();
                    console.log(number_budget, productId);
                    $.ajax({
                        url: '{{ route('ajax.modal-material') }}',
                        method: 'GET',
                        data: { product_id: productId, number_budget: number_budget },
                        success: function(response) {
                            var modalBody = $('#modalBody');

                            modalBody.empty();
                   
                            response.items.forEach(function(item) {
                                console.log(item);
                                var rowHtml = '<tr>';
                                rowHtml += '<td>' + item.raw_material + '</td>';
                                rowHtml += '<td>' + item.quantity + '</td>';
                                rowHtml += '<input type="hidden" name="detail_material_quantity_'+productId+'[]" value="' + $.number(item.quantity, 0, ',', '.') + '">';
                                rowHtml += '<input type="hidden" name="detail_articulo_id_'+productId+'[]" value="' + $.number(item.raw_articulo_id, 0, ',', '.') + '">';

                                rowHtml += '</tr>';
                                modalBody.append(rowHtml);
                            });

                        },
                        error: function(xhr, status, error) {
                            // Maneja cualquier error de la solicitud AJAX
                            console.error(error);
                        }
                    });
                });
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
            var number_budget    = $("#number_budget").val();
            var conteo           = 0;
            $('#tbody_detail').html('');

            if(number_budget != '')
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('ajax.order-production') }}',
                    type: "GET",
                    data: { number_budget : number_budget},
                    success: function(data) {
                        $.each(data.items, function(index, element) {
                            invoice_items_array.push(element.product_id);

                            $('#tbody_detail').append('<tr>' +
                                '<td>' + element.product_id + '</td>' +
                                '<td class="text-center">' + element.product_name + '</td>' +
                                '<td class="text-center">' + $.number(element.quantity, 0, ',', '.') + '</td>' +
                                '<td><i class="fa fa-info-circle open-modal" data-toggle="modal" data-target="#myModal"></i></td>' +
                                '<input type="hidden" name="detail_id[]" value="' + element.id + '">' +
                                '<input type="hidden" name="detail_product_id[]" value="' + element.product_id + '">' +
                                '<input type="hidden" name="detail_product_name[]" value="' + element.product_name + '">' +
                                '<input type="hidden" name="detail_product_quantity[]" value="' + $.number(element.quantity, 0, ',', '.') + '">' +
                            '</tr>');
                            conteo++;
                            $('#branch_id').val(element.branch_id);
                            $('#branch').val(element.branch);
                            $('#date_ped').val(element.date);
                            $('#client_id').val(element.client_id);
                            $('#client').val(element.client);

                            var selectedMaterials = []; 
                            var selectedProducts = element.product_id; 
                
                            $.ajax({
                                url: '{{ route('ajax.modal-material') }}',
                                method: 'GET',
                                data: { product_id: selectedProducts,number_budget:number_budget,envio:1 },
                                success: function(response) {
                                    response.items.forEach(function(material) {
                                        $('#modalfoot').append(
                                            '<input type="hidden" name="selected_materials_'+selectedProducts+'[]" value="' + material.raw_articulo_id + '">' +
                                            '<input type="hidden" name="selected_materials_quantity_'+selectedProducts+'[]" value="' + material.quantity + '">'
                                        );
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                }
                            });
                        });

                        if(conteo>0)
                        {
                            
                            $("#div_details, #div_footer").show();
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


    </script>
@endsection

