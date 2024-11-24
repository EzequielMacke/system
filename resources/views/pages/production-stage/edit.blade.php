@extends('layouts.AdminLTE.index')
@section('title', 'Editar Marca')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            {{ Form::open(['route' => ['production-stage.update', $stages->id], 'method' => 'PUT']) }}
            <div class="ibox-content">
                @include('partials.messages')
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Descripcion</label><br>
                        <input id="name" class="form-control" name="name" type="text"  value="{{ old('name', $stages->name) }}">
                        </div>
                        <div class="form-group col-md-4">
                        <label>Sesion</label><br>
                        <input id="number" class="form-control" name="number" type="text"  value="{{ old('name', $stages->number) }}">
                        </div>

                    </div>  
                </div>
            </div>
            <div class="ibox-footer">
                <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                <a href="{{ url('production-stage') }}" class="btn btn-sm btn-danger">Cancelar</a>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
