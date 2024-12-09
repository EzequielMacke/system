<!DOCTYPE html>
<html>
<head>
    <title>PDF de Insumos Utilizados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Detalles de Insumos Utilizados</h2>
    <table>
        <tr>
            <th>ID</th>
            <td>{{ $input_used->id }}</td>
        </tr>
        <tr>
            <th>Nro° de Orden</th>
            <td>{{ $input_used->order_id }}</td>
        </tr>
        <tr>
            <th>Fecha de Creación</th>
            <td>{{ $input_used->date_created }}</td>
        </tr>
        <tr>
            <th>Cliente</th>
            <td>{{ $input_used->client->razon_social }}</td>
        </tr>
        <tr>
            <th>Obra</th>
            <td>{{ $input_used->construction_site->description }}</td>
        </tr>
        <tr>
            <th>Descripción</th>
            <td>{{ $input_used->description }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ config('constants.input_used_status.' . $input_used->status) }}</td>
        </tr>
    </table>
</body>
</html>
