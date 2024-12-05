@extends('layouts.AdminLTE.index')
@section('title', 'Crear Contrato')
@section('content')
{{ Form::open(['id' => 'form', 'files' => true]) }}
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Agregar Contrato</h5>
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
                    {{-- <div class="form-group col-md-2">
                        <label>Plazo</label>
                        <input class="form-control" type="number" name="term">
                    </div> --}}
                    <div class="form-group col-md-6">
                        <label>Dirección</label>
                        <input class="form-control" type="text" name="placement">
                    </div>
                </div>
                <div class="row">
                    <div id="grafico"></div>
                    <div class="form-group col-md-3">
                        <label>Plazo de cumplimiento (dias)</label>
                        <div class="input-group">
                            <input class="form-control" type="number" name="term">
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Fecha de Finalización de contrato</label>
                        <input class="form-control" type="date" name="date_estimated" id="date_estimated" readonly>
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
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Datos de Presupuesto</h5>
                        </div>
                        <div class="ibox-content pb-0">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Seleccionar Presupuesto</label>
                                    <select class="form-control" name="budget_id" id="budget_id"></select>
                                    {{-- {{ Form::select('site_id', $construction_sites, old('site_id'), ['class' => 'form-control', 'select2', 'id' => 'site_id']) }} --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="grafico"></div>
                    <div class="form-group col-md-12">
                        <label>Tema</label>
                        <textarea class="form-control" name="issue" rows="4" placeholder="Ingrese una breve descripcion de los temas a tratar en el contrato..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h3>Cláusulas</h3>
    </div>
    <div class="ibox-content table-responsive no-padding" id="detail_clauses">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Descripción</th>
                    <th>*</th>
                </tr>
            </thead>
            <tbody id="tbody_detail_clauses"></tbody>
        </table>
    </div>
</div>

<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h3>Obligaciones</h3>
    </div>
    <div class="ibox-content table-responsive no-padding" id="detail_obligations">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Obligaciones</th>
                    <th>*</th>
                </tr>
            </thead>
            <tbody id="tbody_detail_obligations"></tbody>
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
    <a href="{{ url('contracts') }}" class="btn btn-sm btn-danger">Cancelar</a>
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
                    url: '{{ route('contract.store') }}',
                    type: "POST",
                    data: new FormData(this),
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        redirect ("{{ url('contracts') }}");
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
                    url: '{{ url('ajax/contract') }}',
                    method: 'GET',
                    data: { client_id: client_id, site_id: site_id},
                    success: function(response) {
                        console.log(response);
                        var wishSelect = $('#budget_id');
                        wishSelect.empty();
                        wishSelect.append('<option value="">Seleccione un presupuesto</option>');
                        $.each(response.items, function(index,value){
                            wishSelect.append('<option value="' + value.id + '">'+ value.id +'-'+ value.description + '</option>');

                        })
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        // Manejar el error si es necesario
                    }
                });
            });

            $('#budget_id').change(function() {
            var budget_id = $("select[name='budget_id']").val();
            $.ajax({
                url: '{{ url('ajax/contract') }}',
                method: 'GET',
                data: { budget_id: budget_id },
                success: function(response) {
                    $('#tbody_detail').empty();

                    // Manejar los items
                    // $.each(response.items, function(index, value) {
                    //     addToTable(value.service_id, value.description, value.service_name, value.level);
                    // });

                    // Manejar las cláusulas únicas
                    $('#tbody_detail_clauses').empty();
                    $.each(response.clauniqueArray, function(index, clause) {
                        addClauseToTable(clause.id, clause.description, clause.service_id);
                    });

                    // Manejar las obligaciones únicas
                    $('#tbody_detail_obligations').empty();
                    $.each(response.obliuniqueArray, function(index, obligation) {
                        addObligationToTable(obligation.id, obligation.name, obligation.service_id);
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    // Manejar el error si es necesario
                }
            });
        });

            // function addToTable(service_id, description, service_name, level) {
            // $('#tbody_detail').append(
            //     '<tr>' +
            //     '<td>' + service_id + '</td>' +
            //     '<td>' + description + '</td>' +
            //     '<td>' + service_name + '</td>' +
            //     '<td>' + level + '</td>' +
            //     '</tr>'
            // );
            // }

            function addClauseToTable(id, description, service_id) {
            $('#tbody_detail_clauses').append(
                '<tr>' +
                '<td>' + id + '<input type="hidden" name="id-clau[]" value="' + id + '"></td>' +
                '<td>' + description + '<input type="hidden" name="description-clau[]" value="' + description + '"></td>' +
                '<td>'+'<input type="hidden" name="service_id-clau[]" value="' + service_id + '"></td>' +
                '<td class="text-right"><a href="javascript:;" onClick="removeRow(this, ' + id + ');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
                '</tr>'
            );
            }

            function addObligationToTable(id, name, service_id) {
            $('#tbody_detail_obligations').append(
                '<tr>' +
                '<td>' + id + '<input type="hidden" name="id-obli[]" value="' + id + '"></td>' +
                '<td>' + name + '<input type="hidden" name="name-obli[]" value="' + name + '"></td>' +
                '<td>' +'<input type="hidden" name="service_id-obli[]" value="' + service_id + '"></td>' +
                '<td class="text-right"><a href="javascript:;" onClick="removeRow(this, ' + id + ');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
                '</tr>'
            );
            }
        });


        // function addToTable(id, description,service_name,level) {
        //     $('#tbody_detail').append('<tr>' +
        //         '<td>' + (++counter) + '</td>' +
        //         '<td>'+description+'</td>' +
        //         '<td>'+service_name+'</td>' +
        //         '<td>'+level+'</td>' +
        //         '<td></td>' +
        //         '<td></td>' +
        //         '<td></td>' +
        //         '<td></td>' +
        //         '<td></td>' +
        //         '<td></td>' +
        //         '</tr>'
        //     );
        // }



        function formatPrice(input) {
            let value = input.value.replace(/[^0-9.]/g, '');
            let parts = value.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            input.value = parts.join('.');
        }

        function removeRow(t, service_id) {
            // Remover la fila del servicio
            $(t).parent().parent().remove();
            invoice_items_array.splice($.inArray(service_id, invoice_items_array), 1);
            counter--;

            // Remover las filas de cláusulas asociadas
            $('#tbody_detail_clauses tr').each(function() {
                if ($(this).data('service-id') === service_id) {
                    $(this).remove();
                }
            });

            // Remover las filas de obligaciones asociadas
            $('#tbody_detail_obligations tr').each(function() {
                if ($(this).data('service-id') === service_id) {
                    $(this).remove();
                }
            });
        }

            document.addEventListener('DOMContentLoaded', function() {
            const termInput = document.querySelector('input[name="term"]');
            const dateEstimatedInput = document.querySelector('input[name="date_estimated"]');
            console.log(termInput);
            console.log(dateEstimatedInput);
            termInput.addEventListener('input', function() {
                const term = parseInt(termInput.value, 10);
                if (!isNaN(term)) {
                    const currentDate = new Date();
                    currentDate.setDate(currentDate.getDate() + term);
                    const year = currentDate.getFullYear();
                    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                    const day = String(currentDate.getDate()).padStart(2, '0');
                    dateEstimatedInput.value = `${year}-${month}-${day}`;
                } else {
                    dateEstimatedInput.value = '';
                }
            });

            // Inicializar la fecha estimada al cargar la página
            termInput.dispatchEvent(new Event('input'));
             });
    </script>
@endsection
