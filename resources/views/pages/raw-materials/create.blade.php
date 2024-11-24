@extends('layouts.AdminLTE.index')
@section('title', 'Agregar Materia Prima')
@section('content')
  <div class="row">
      <div class="col-lg-12">
          <div class="ibox float-e-margins">
              {{ Form::open(['route' => 'raw-materials.store']) }}
                <div class="ibox-content">
                    @include('partials.messages')
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Nombre</label><br>
                            <input id="description" class="form-control" name="description" type="text"  value="{{--{{ old('name', $brand->name) }}--}}">
                        </div>
                    </div>
                </div>
                  <div class="ibox-footer">
                      <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                      <a href="{{ url('raw-materials') }}" class="btn btn-sm btn-danger">Cancelar</a>
                  </div>
                {{ Form::close() }}
          </div>
      </div>
  </div>
@endsection
