@extends('layouts.AdminLTE.index')
@section('title', 'Mermas')
@section('content')
<div class="row">
    {{ Form::open(['id' => 'form']) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Agregar Mermas</h5>
                        </div>
                        <div class="ibox-content pb-0">
                            <div class="row">
                                <div class="form-group col-md-2">
                                    <label>N° Control de Calidad</label>
                                    <input class="form-control" type="text" name="number_control" id="number_control" placeholder="N° Control de Calidad" autofocus>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Fecha</label>
                                    <input class="form-control" type="text" name="date" value="{{ old('date', date('d/m/Y')) }}" readonly>
                                </div>
                                <div class="form-group col-md-2">
                                    <br>
                                    <button type="button" class="btn btn-primary" name="button_search" id="button_search"><i class="fa fa-search"></i> BUSCAR CONTROL CALIDAD</button>
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
            <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                    <label for="etapa">Calidad Verificada:</label>
                                    <br>
                                    <td><span id="qualities_name0"></span></td>
                                    <input type="checkbox" id="etapa0" name="etapa0">
                                    <input type="hidden" id="quality_id0" name="quality_id0">

                                </div>
                                <div class="col-md-6">
                                    <label for="total">Cantidad Total:</label>
                                    <input type="text" id="total_quantity0" name="total0" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="observacion">Observación:</label>
                                    <input type="text" id="observacion0" name="observacion0" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="cantidad_controlada">Cantidad Controlada:</label>
                                    <input type="text" id="cantidad_controlada0" name="cantidad_controlada0" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            {{-- <button type="button" class="btn btn-primary" id="updateForm">Actualizar</button> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                    <label for="etapa">Calidad Verificada:</label>
                                    <br>
                                    <td><span id="qualities_name1"></span></td>
                                    <input type="checkbox" id="etapa1" name="etapa1">
                                    <input type="hidden" id="quality_id1" name="quality_id1">
                                </div>
                                <div class="col-md-6">
                                    <label for="total">Cantidad Total:</label>
                                    <input type="text" id="total_quantity1" name="total1" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="observacion">Observación:</label>
                                    <input type="text" id="observacion1" name="observacion1" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="cantidad_controlada">Cantidad Controlada:</label>
                                    <input type="text" id="cantidad_controlada1" name="cantidad_controlada1" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            {{-- <button type="button" class="btn btn-primary" id="updateForm">Actualizar</button> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                    <label for="etapa">Calidad Verificada:</label>
                                    <br>
                                    <td><span id="qualities_name2"></span></td>
                                    <input type="checkbox" id="etapa2" name="etapa2">
                                    <input type="hidden" id="quality_id2" name="quality_id2">
                                </div>
                                <div class="col-md-6">
                                    <label for="total">Cantidad Total:</label>
                                    <input type="text" id="total_quantity2" name="total2" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="observacion">Observación:</label>
                                    <input type="text" id="observacion2" name="observacion2" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="cantidad_controlada">Cantidad Controlada:</label>
                                    <input type="text" id="cantidad_controlada2" name="cantidad_controlada2" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            {{-- <button type="button" class="btn btn-primary" id="updateForm">Actualizar</button> --}}
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
                    url: '{{ route('production-control-quality-store') }}',
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(data) {
                        redirect ("{{ url('production-control-quality') }}");
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
                $(document).on('click', '.open-modal1', function () {
                    var stageName = $(this).data('stage');
                    var stageId = $(this).data('production_qualities_id');
                    var quantity = $(this).data('quantity');

                    // Poblar el modal con los datos
                    $('#qualities_name0').text(stageName);
                    $('#quality_id0').val(stageId);
                    $('#total_quantity0').val(quantity);
                });

                $(document).on('click', '.open-modal2', function () {
                    var stageName = $(this).data('stage');
                    var stageId = $(this).data('production_qualities_id');
                    var quantity = $(this).data('quantity');
                    // Poblar el modal con los datos
                    $('#qualities_name1').text(stageName);
                    $('#quality_id1').val(stageId);
                    $('#total_quantity1').val(quantity);
                });

                $(document).on('click', '.open-modal3', function () {
                    var stageName = $(this).data('stage');
                    var stageId = $(this).data('production_qualities_id');
                    var quantity = $(this).data('quantity');

                    // Poblar el modal con los datos
                    $('#qualities_name2').text(stageName);
                    $('#quality_id2').val(stageId);
                    $('#total_quantity2').val(quantity);
                });
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
                    data: { number_control : number_control, sesion:1},
                    success: function(data) {
                        $.each(data.items, function(index, element) {
                            invoice_items_array.push(element.product_id);

                            $('#tbody_detail1').append(     
                            '<tr>' +
                                '<td>' + element.product_id + '</td>' +
                                '<td>' + element.product_name + '</td>' +
                                '<td>' + $.number(element.quantity, 0, ',', '.') + '</td>' +
                                '<td>' + element.qualities_name + '</td>' +
                                '<td></td>' +
                                '<td><i class="fa fa-info-circle open-modal1" data-toggle="modal" data-target="#myModal1" data-stage="' + element.qualities_name + '" data-production_qualities_id="' + element.production_qualities_id + '" data-quantity="' + element.quantity + '"></i></td>' +
                                '<input type="hidden" name="detail_id[]" value="' + element.id + '">' +
                                '<input type="hidden" name="detail_product_id[]" value="' + element.product_id + '">' +
                                '<input type="hidden" name="detail_product_name[]" value="' + element.product_name + '">' +
                            '</tr>');
                            conteo++;
                            $('#branch_id').val(element.branch_id);
                            $('#branch').val(element.branch);
                            $('#date_ped').val(element.date);
                            $('#client_id').val(element.client_id);
                            $('#client').val(element.client);
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
                                text: "No existe Control de Produccion!!",
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

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('ajax.control-calidad') }}',
                    type: "GET",
                    data: { number_control : number_control, sesion:2},
                    success: function(data) {
                        $.each(data.items, function(index, element) {
                            invoice_items_array.push(element.product_id);

                            $('#tbody_detail2').append(     
                            '<tr>' +
                                '<td>' + element.product_id + '</td>' +
                                '<td>' + element.product_name + '</td>' +
                                '<td>' + $.number(element.quantity, 0, ',', '.') + '</td>' +
                                '<td>' + element.qualities_name + '</td>' +
                                '<td></td>' +

                                '<td><i class="fa fa-info-circle open-modal2" data-toggle="modal" data-target="#myModal2" data-stage="' + element.qualities_name + '" data-production_qualities_id="' + element.production_qualities_id + '" data-quantity="' + element.quantity + '"></i></td>' +
                                '<input type="hidden" name="detail_id[]" value="' + element.id + '">' +
                                '<input type="hidden" name="detail_product_id[]" value="' + element.product_id + '">' +
                                '<input type="hidden" name="detail_product_name[]" value="' + element.product_name + '">' +
                            '</tr>');
                        });
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                    }
                });

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('ajax.control-calidad') }}',
                    type: "GET",
                    data: { number_control : number_control, sesion:3},
                    success: function(data) {
                        $.each(data.items, function(index, element) {
                            invoice_items_array.push(element.product_id);
                            $('#tbody_detail3').append(     
                            '<tr>' +
                                '<td>' + element.product_id + '</td>' +
                                '<td>' + element.product_name + '</td>' +
                                '<td>' + $.number(element.quantity, 0, ',', '.') + '</td>' +
                                '<td>' + element.qualities_name + '</td>' +
                                '<td></td>' +
                                '<td><i class="fa fa-info-circle open-modal3" data-toggle="modal" data-target="#myModal3" data-stage="' + element.qualities_name + '" data-production_qualities_id="' + element.production_qualities_id + '" data-quantity="' + element.quantity + '"></i></td>' +
                                '<input type="hidden" name="detail_id[]" value="' + element.id + '">' +
                                '<input type="hidden" name="detail_product_id[]" value="' + element.product_id + '">' +
                                '<input type="hidden" name="detail_product_name[]" value="' + element.product_name + '">' +
                            '</tr>');
                        });
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

