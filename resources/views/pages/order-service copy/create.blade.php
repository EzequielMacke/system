@extends('layouts.AdminLTE.index')
@section('title', 'Promociones')
@section('content')
{{ Form::open(['id' => 'form', 'files' => true]) }}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Agregar Promociones</h5>
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
                            <label>Fecha inicio</label>
                            <input class="form-control" type="date" name="start_date" id="start_date" value="" >
                        </div>
                        <div class="form-group col-md-2">
                            <label>Fecha fin</label>
                            <input class="form-control" type="date" name="end_date" id="end_date" value="" >
                        </div>
                    </div>
                </div>
            </div>
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
        <a href="{{ url('promotions') }}" class="btn btn-sm btn-danger">Cancelar</a>
    </div>
{{ Form::close() }}
@endsection






