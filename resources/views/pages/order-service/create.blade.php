@extends('layouts.AdminLTE.index')
@section('title', 'Orden de Servicio')
@section('content')
{{ Form::open(['id' => 'form', 'files' => true]) }}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Agregar Orden</h5>
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

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label>Nro Orden</label>
                        <input class="form-control" type="text" name="order" value="{{ $newOrderNumber }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>Plazo de finalización</label>
                        <input class="form-control" type="text" name="term" id="term" oninput="actualizarFechaEstimacion()" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>Fecha de finalización</label>
                        <input class="form-control" type="Date" name="date_ending" id="date_ending" readonly>
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
                                <h5>Datos de Contrato</h5>
                            </div>
                            <div class="ibox-content pb-0">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Nroº Contrato</label>
                                            <select class="form-control" name="contract_id" id="contract_id"></select>
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
                                        <input class="form-control" type="text" name="budget_service_id" id="budget_service_id" readonly>
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
            <h3>Seleccionar funcionarios</h3>
        </div>
        <div class="ibox-content pb-0">
            <div class="row">
                <div class="form-group col-md-2">
                    <label>Funcionario</label>
                    <select class="form-control" name="funcionario_id" id="funcionario_id">
                        <option value="">Seleccione un funcionario</option>
                        @foreach($oficial as $of)
                            <option value="{{ $of->id }}" data-role="{{ config('constants.posts.' . $of->post) }}" data-document="{{ $of->document_nr }}">{{ $of->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Rol</label>
                    <input class="form-control" type="text" name="funcionario_role" id="funcionario_role" readonly>
                </div>
                <div class="form-group col-md-2">
                    <label>Documento</label>
                    <input class="form-control" type="text" name="funcionario_document" id="funcionario_document" readonly>
                </div>
                <div class="form-group col-md-2">
                    <label>Agregar funcionario</label><br>
                    <button type="button" class="btn btn-primary" onclick="addFuncionario()">Agregar</button>
                </div>
            </div>
        </div>
        <div class="ibox-content table-responsive no-padding">
            <table class="table table-hover table-striped mb-0" id="funcionarios_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Nro de Documento</th>
                        <th>Acciones</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody ></tbody>
            </table>
        </div>
        <div class="ibox-title">
            <h3>Trabajos a realizar</h3>
        </div>
        <div class="ibox-content table-responsive no-padding">
            <table class="table table-hover table-striped mb-0" id="service_table">
                <thead>
                    <tr>
                        <th>Código de ensayo</th>
                        <th>Ensayo</th>
                        <th>Contidad de Ensayos</th>
                        <th>Servicio</th>
                        <th>Subtotal</th>
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
        <a href="{{ url('order-service') }}" class="btn btn-sm btn-danger">Cancelar</a>
    </div>
{{ Form::close() }}
@endsection

@section('layout_js')
    <script>
        var counter = 0;
        var invoice_items_array = [];
        var addedFuncionarios = [];

        $(document).ready(function ()
        {
            $('#funcionario_id').change(function() {
                var selectedOption = $(this).find('option:selected');
                var role = selectedOption.data('role');
                var document = selectedOption.data('document');

                $('#funcionario_role').val(role);
                $('#funcionario_document').val(document);
            });
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

            $('#site_id').change(function() {
                var client_id = $("select[name='client_id']").val();
                var site_id = $("select[name='site_id']").val();
                // Realizar una solicitud al servidor para obtener los contratos
                $.ajax({
                    url: '{{ url('ajax/order') }}',
                    method: 'GET',
                    data: { client_id: client_id, site_id: site_id },
                    success: function(response) {
                        var contractSelect = $('#contract_id');
                        contractSelect.empty();
                        contractSelect.append('<option value="">Seleccione un contrato</option>');
                        $.each(response.items, function(index, value) {
                            contractSelect.append('<option value="' + value.id + '">' + value.id + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        // Manejar el error si es necesario
                    }
                });
            });

            $('#contract_id').change(function() {
                var contract_id = $("select[name='contract_id']").val();
                $.ajax({
                    url: '{{ url('ajax/order') }}',
                    method: 'GET',
                    data: { contract_id: contract_id },
                    success: function(response) {
                        $('#tbody_detail').empty();
                        $('#budget_service_id').val(response.budget_service_id);
                        $('#term').val(response.term);
                        $('#term').trigger('input');
                        $.each(response.budget_service_detail, function(index, value) {
                            addToServiceTable(index + 1,
                            value.budget_service_id,
                            value.service_id,
                            value.quantity,
                            value.price,
                            value.level,
                            value.total_price,
                            value.quantity_per_meter,
                            value.input_id,
                            value.input_name,
                            value.service_description,
                            );
                        });

                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });


        });
        function addFuncionario() {
            var funcionario_name = $("select[name='funcionario_id'] option:selected").text();
            var funcionario_id = $("select[name='funcionario_id']").val();
            var funcionario_role = $("input[name='funcionario_role']").val();
            var funcionario_document = $("input[name='funcionario_document']").val();

            if (funcionario_id != '' && funcionario_role != '' && funcionario_document != '') {
                if (addedFuncionarios.includes(funcionario_id)) {
                    swal({
                        title: "SISTEMA",
                        text: "El funcionario ya ha sido agregado.",
                        icon: "warning",
                        button: "OK",
                    });
                    return false;
                } else {
                    addToTable(funcionario_id, funcionario_name, funcionario_role, funcionario_document);
                    addedFuncionarios.push(funcionario_id);
                }
                $('#funcionario_id').val(null).trigger('change');
                $("input[name='funcionario_role']").val('');
                $("input[name='funcionario_document']").val('');
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

        function addToTable( id, name, role, document) {
            var table = $('#funcionarios_table tbody');
            var row = '<tr>' +
                '<td>' + id + '<input type="hidden" name="id_oficial[]" id="id_oficial" value="'+id+'"></td>' +
                '<td>' + name + '</td>' +
                '<td>' + role + '</td>' +
                '<td>' + document + '</td>' +
                '<td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this, ' + id + ')">Eliminar</button></td>' +
                '</tr>';
            table.append(row);
        }

        function addToServiceTable(id, budget_service_id, service_id, quantity, price, level, total_price, quantity_per_meter, input_id, input_name, service_description) {
            var table = $('#service_table tbody');
            var row = '<tr>' +
                '<td>' + input_id + '<input type="hidden" name="input_id[]" id="input_id" value="'+input_id+'"></td>' +
                '<td>' + input_name + '</td>' +
                '<td>' + quantity + '<input type="hidden" name="quantity[]" id="quantity" value="'+quantity+'"></td>' +
                '<td>' + service_description + '<input type="hidden" name="service_id[]" id="service_id" value="'+service_id+'"></td>' +
                '<td>' + total_price + '</td>' +
                '</tr>';
            table.append(row);
        }


        function removeRow(button, id) {
            $(button).closest('tr').remove();
            var index = addedFuncionarios.indexOf(id);
            if (index > -1) {
                addedFuncionarios.splice(index, 1);
            }
        }

        function formatPrice(input)
        {
            let value = input.value.replace(/[^0-9.]/g, '');
            let parts = value.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            input.value = parts.join('.');
        }

        function actualizarFechaEstimacion() {
            const termInput = document.querySelector('input[name="term"]');
            const dateEstimatedInput = document.querySelector('input[name="date_ending"]');
            const term = parseInt(termInput.value, 10);
            if (!isNaN(term)) {
                const currentDate = new Date();
                currentDate.setDate(currentDate.getDate() + term);

                const day = String(currentDate.getDate()).padStart(2, '0');
                const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                const year = currentDate.getFullYear();

                // Formato: día/mes/año
                dateEstimatedInput.value = `${year}-${month}-${day}`;
            } else {
                dateEstimatedInput.value = '';
            }
        }

        // Inicializar la fecha estimada al cargar la página
        // document.addEventListener('DOMContentLoaded', function() {
        //     actualizarFechaEstimacion();
        // });

        function removeRow(t, service_id)
        {
            $(t).parent().parent().remove();
            invoice_items_array.splice($.inArray(service_id, invoice_items_array), 1 );
            // calculateGrandTotal();
            counter--;
        }

    </script>
@endsection
