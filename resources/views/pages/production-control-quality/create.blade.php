@extends('layouts.AdminLTE.index')
@section('title', 'Control de Calidad')
@section('content')
<div class="row">
    {{ Form::open(['id' => 'form']) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Agregar Control de Calidad</h5>
                        </div>
                        <div class="ibox-content pb-0">
                            <div class="row">
                                <div class="form-group col-md-2">
                                    <label>Numero Control</label>
                                    <input class="form-control" type="text" name="number_control" id="number_control" placeholder="N° Control de Produccion" autofocus>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Fecha</label>
                                    <input class="form-control" type="text" name="date" value="{{ old('date', date('d/m/Y')) }}" readonly>
                                </div>
                                <div class="form-group col-md-2">
                                    <br>
                                    <button type="button" class="btn btn-primary" name="button_search" id="button_search"><i class="fa fa-search"></i> BUSCAR CONTROL PRODUCCION</button>
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
                    <h3>Items</h3>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs fs-3">
                                <li class="active"><a data-toggle="tab" href="#seccion1" onclick="ChangeTab1();"><h5>Primera Calidad </h5></a></li>
                                <li class=""><a data-toggle="tab" href="#seccion2" onclick="ChangeTab2();"><h5>Segunda Calidad </h5></a></li>
                                <li class=""><a data-toggle="tab" href="#seccion3" onclick="ChangeTab3();"><h5>Tercera Calidad </h5></a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="seccion1">
                                    <div class="panel-body table-responsive" id="div_sec1">
                                        <table class="table table-stripped" >
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Articulo</th>
                                                    <th>Cantidad</th>
                                                    <th>Calidad</th>
                                                    <th>OBS:</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_detail1"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div> 
                            <div class="tab-content">
                                <div class="tab-pane" id="seccion2">
                                    <div class="panel-body table-responsive" id="div_sec2">
                                        <table class="table table-stripped" data-limit-navigation="8" data-sort="true" data-paging="true" data-filter=#filter1>
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Articulo</th>
                                                    <th>Cantidad</th>
                                                    <th>Calidad</th>
                                                    <th>OBS:</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_detail2"></tbody>

                                        </table>
                                    </div>
                                </div>
                            </div> 
                            <div class="tab-content">                                              
                                <div class="tab-pane" id="seccion3">
                                    <div class="panel-body table-responsive" id="div_sec3">
                                        <table class="table table-stripped" data-limit-navigation="8" data-sort="true" data-paging="true" data-filter=#filter1>
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Articulo</th>
                                                    <th>Cantidad</th>
                                                    <th>Calidad</th>
                                                    <th>OBS:</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_detail3"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>         
                        </div>
                    </div>
                </div>
            </div>
            <div class="ibox-footer" id="div_footer">
                <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                <a href="{{ url('production-control-quality') }}" class="btn btn-sm btn-danger">Cancelar</a>
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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        var invoice_items_array = [];
    
        $(document).ready(function ()
        {
            window.addEventListener('beforeunload', limpiarLocalStorage);
            $('#form').submit(function(e)
            {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('production-control-quality-store') }}',
                    type: "POST",
                    data: $('#form').serialize(), // Serializar el formulario completo
                    success: function(data) {
                        // redirect ("{{ url('production-control-quality') }}");
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                        $('input[type="submit"]').prop('disabled', false);
                    }
                });
            });

            $("#button_search").click(function() {
                Search_order();
            });


            $('#number_ped').keypress(function(e){
                if (e.keyCode == 13)
                {
                    $("#button_search").click();
                    e.preventDefault();
                    return false;
                }
            });

            $(document).ready(function(){
                function poblarModal(numeroModal) {
                    $(document).on('click', '.open-modal' + numeroModal, function () {
                        var stageName = $(this).data('stage');
                        var stageId = $(this).data('stage_id');
                        var quantity = $(this).data('quantity');
                        var product_id = $(this).data('product');
                        // Poblar el modal con los datos
                        $('#stage_name' + numeroModal).text(stageName);
                        $('#stage_id' + numeroModal).val(stageId);
                        $('#total_quantity' + numeroModal).val(quantity);
                        $('.product_id' + numeroModal).val(product_id);
                    });
                }

                for (var i = 0; i < 3; i++) {
                    poblarModal(i);
                }
            });

            // Variables para almacenar los valores temporales de observación y cantidad controlada para cada producto
            var observacionTemporal = {};
            var cantidadControladaTemporal = {};

            $(document).on('click', '.open-modal-btn', function() {
                var productId = $(this).data('product');
                var stageName = $(this).data('stage');
                var stageId = $(this).data('stage_id');
                var quantity = $(this).data('quantity');
                generarModal(productId, stageName, stageId, quantity);
            });

        });

        function ChangeTab1()
        {
                $('#div_sec1').show();
                $('#div_sec3').hide();
                $('#div_sec2').hide();
                
        }
        function ChangeTab2()
        {
                $('#div_sec2').show();
                $('#div_sec1').hide();
                $('#div_sec3').hide();
                
        }
        
        function ChangeTab3()
        {
                $('#div_sec3').show();
                $('#div_sec1').hide();
                $('#div_sec2').hide();
                
        }

        changeStatus();
        function changeStatus()
        {
            $("#div_details, #div_footer").hide();

            $("#number_ped").prop("readonly", false);
            $("#button_search").show();
        }

        function Search_order()
        {
            var number_control    = $("#number_control").val();
            var conteo           = 0;
            $('#tbody_detail1').html('');

            if(number_control != '')
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('ajax.control-calidad') }}',
                    type: "GET",
                    data: { number_control: number_control, sesion: [1, 2, 3] }, // Envía las sesiones en un array
                    success: function(data) {
                        $.each(data.items, function(sesion, values) {
                            $.each(values, function(index, element) {
                                    invoice_items_array.push(element.product_id);
                                    var session = sesion; // Obtiene el número de sesión
                                    var tbody = '#tbody_detail' + session; // Genera el ID del tbody correspondiente
                                    console.log(element);
                                    $(tbody).append(     
                                        '<tr>' +
                                        '<td>' + element.product_id + '</td>' +
                                        '<td>' + element.product_name + '</td>' +
                                        '<td>' + $.number(element.quantity, 0, ',', '.') + '</td>' +
                                        '<td>' + element.qualities_name + '</td>' +
                                        '<td></td>' +
                                        '<td><button type="button" class="open-modal-btn btn btn-primary recuperar' + element.product_id + '_' + element.production_qualities_id + '" data-product="' + element.product_id + '" data-stage="' + element.qualities_name + '" data-stage_id="' + element.production_qualities_id + '" data-quantity="' + element.quantity + '"><i class="fa fa-info-circle"></i></button></td>' +
                                        '<input type="hidden" name="detail_id[]" value="' + element.id + '">' +
                                        '<input type="hidden" name="detail_product_id[]" value="' + element.product_id + '">' +
                                        '<input type="hidden" name="detail_stage_id[]" value="' + element.product_id+'_'+element.production_qualities_id + '">' +
                                        '<input type="hidden" name="detail_product_name[]" value="' + element.product_name + '">' +
                                        '</tr>');
                                conteo++;
                                $('#branch_id').val(element.branch_id);
                                $('#branch').val(element.branch);
                                $('#date_ped').val(element.date);
                                $('#client_id').val(element.client_id);
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

        function limpiarLocalStorage() 
        {
            localStorage.clear();
        }
        function guardarTemporal(product,stage) 
        {
            cargarDatosModal('.myModal'+product+'_'+stage,product,stage);
        }
        function cargarDatosModal(modalClass,product,stage) 
        {
            $(modalClass).on('click', '.guardar-temporal', function(event) {
                var productId = $(this).closest(modalClass).find('.product_id'+product+'_'+stage).val();
                var observacionValue = $(this).closest(modalClass).find('.observacion-input').val();
                var cantidadControladaValue = $(this).closest(modalClass).find('.cantidad-controlada-input').val();
                var isChecked = $(this).closest(modalClass).find('#etapa'+product+'_'+stage).prop('checked');
                // Guarda los valores temporalmente en localStorage
                localStorage.setItem('checkbox_' + product+'-'+stage, isChecked);
                localStorage.setItem('observacion_' + product+'-'+stage, observacionValue);
                localStorage.setItem('cantidad_controlada_' + product+'-'+stage, cantidadControladaValue);
                $('#observacion' + product + '_' + stage).val(observacionValue);
                $('#cantidad_controlada' + product + '_' + stage).val(cantidadControladaValue);
                $('#etapa' + product + '_' + stage).prop('checked', isChecked === 'true');

                // También puedes asignar los valores al atributo 'value' de los elementos correspondientes
                $('#observacion' + product + '_' + stage).attr('value', observacionValue);
                $('#cantidad_controlada' + product + '_' + stage).attr('value', cantidadControladaValue);
                $('#etapa' + product + '_' + stage).attr('value', isChecked);
            });

        }

        function generarModal(product_id, stage_name,stage_id,quantity)
        {
            if ($(`.myModal${product_id}_${stage_id}`).length) 
            {
                $(`.myModal${product_id}_${stage_id}`).modal('show');
            } else 
            {
                var modalHtml = `
                    <div class="modal fade in myModal${product_id}_${stage_id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel">Control</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="etapa">Etapa Verificada:</label>
                                            <br>
                                            <span id="stage_name${product_id}_${stage_id}">${stage_name}</span>
                                            <input type="checkbox" id="etapa${product_id}_${stage_id}" name="etapa${product_id}_${stage_id}">
                                            <input type="hidden" id="stage_id${product_id}_${stage_id}" name="stage_id${product_id}_${stage_id}" value="${stage_id}">
                                            <input type="hidden" name="product_id${product_id}_${stage_id}" class="form-control product_id${product_id}_${stage_id}" value="${product_id}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="total">Cantidad Total:</label>
                                            <input type="text" id="total_quantity${product_id}_${stage_id}" name="total${product_id}_${stage_id}" class="form-control" readonly value="${quantity}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="observacion">Observación:</label>
                                            <input type="text" id="observacion${product_id}_${stage_id}" name="observacion${product_id}_${stage_id}" class="form-control observacion-input">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="cantidad_controlada">Cantidad Controlada:</label>
                                            <input type="text" id="cantidad_controlada${product_id}_${stage_id}" name="cantidad_controlada${product_id}_${stage_id}" class="form-control cantidad-controlada-input">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary guardar-temporal" onclick="guardarTemporal(${product_id}, ${stage_id})">Guardar Temporalmente</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Filtrar los campos con atributo 'name' y agregarlos al formulario
                var $modal = $(modalHtml);
                var $fields = $modal.find('[name], input');

                $fields.each(function() {
                    var $fieldClone = $(this).clone();
                    $fieldClone.attr('type', 'hidden');
                    $('#form').append($fieldClone);
                });

                    // Agregar el modal generado al cuerpo del documento
                $('body').append(modalHtml);

                // Mostrar el modal
                $(`.myModal${product_id}_${stage_id}`).modal('show');
            }
        }
    </script>
@endsection

