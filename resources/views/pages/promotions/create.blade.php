@extends('layouts.AdminLTE.index')
@section('title', 'Crear Promoción')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Crear Promoción</h5>
            </div>
            <div class="ibox-content pb-0">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('promotions.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Fecha de Inicio</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Fecha de Fin</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Descripción</label>
                            <textarea name="description" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Estado</label>
                            <select name="status" class="form-control" required>
                                <option value="1">Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="ibox-footer">
                        <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                        <a href="{{ route('promotions.index') }}" class="btn btn-sm btn-danger">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
