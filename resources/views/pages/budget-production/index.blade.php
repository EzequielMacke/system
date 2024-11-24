@extends('layouts.AdminLTE.index')
@section('title', 'Presupuesto Produccion')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="btn-group pull-right">
                    <a href="{{ url('budget-production/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                </div>
            </div>
            <div class="ibox-content pb-0">
                <div class="row">
                    <form method="GET">
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="">Buscar</label>
                                <input type="text" class="form-control" name="s" placeholder="Buscar" value="{{ request()->s }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="">Nro Pedido</label>
                                <input type="text" class="form-control" name="wish_production_number" placeholder="Buscar Nro. Perdido" value="{{ request()->wish_production_number }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Sucursal</label>
                                {{ Form::select('branch_id', $branches, request()->branch_id, ['placeholder' => 'Seleccione Sucursal', 'class' => 'form-control', 'select2']) }}
                            </div>
                            <div class="form-group col-md-2">
                                <button type="submit" class="btn btn-primary" name="filter" value="1"><i class="fa fa-search"></i></button>
                                @if(request()->filter)
                                <a href="{{ url('budget-production') }}" class="btn btn-warning"><i class="fa fa-times"></i></a>
                                @endif   
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="ibox-content table-responsive no-padding">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="text-center">Numero Pedido</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Sucursal</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($budget_productions as $budget_production)
                            <tr>
                                <td>{{ $budget_production->id }}</td>
                                <td class="text-center">{{ $budget_production->budget_production_details()->first() ? $budget_production->budget_production_details()->first()->wish_production->id : ''}}</td>
                                <td class="text-center">{{ $budget_production->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $budget_production->branch->name }}</td>
                                <td class="text-center">
                                    <a href="{{ url('budget-production/' . $budget_production->id) }}"><i class="fa fa-info-circle"></i></a>
                                    <a href="{{ url('budget-production/' . $budget_production->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>
                                    {{-- @permission('purchases-movements.delete') --}}
                                            <a href="{{ url('budget-production/' . $budget_production->id . '/delete') }}"><i class="fa fa-trash"></i></a>
                                    {{-- @endpermission --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $budget_productions->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
