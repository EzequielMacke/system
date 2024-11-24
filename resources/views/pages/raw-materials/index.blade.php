@extends('layouts.AdminLTE.index')
@section('title', 'Materia Prima ')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-tools">
                <div class="btn-group pull-right">
                    <a href="{{ url('raw-materials/create') }}" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                </div>
            </div>
            <table class="table table-striped table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materiap as $materia)
                        <tr>
                            <td>{{ $materia->description }}</td>
                            <td class="text-center"><span class="label label-{{ config('constants.status-label.' . $materia->status) }}">{{ config('constants.status.' . $materia->status) }}</td>
                            <td class="text-center">
                                <a href="{{ url('raw-materials/' . $materia->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>
                                <a href="{{ url('raw-materials/' . $materia->id) }}"><i class="fa fa-info-circle"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
