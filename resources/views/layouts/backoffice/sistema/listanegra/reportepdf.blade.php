<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LISTA NEGRA</title>
    <style>
        *{
            margin:0;
            padding:0;
        }
        body{
            font-family:helvetica;
            font-size:12px;
        }
        .container{
            padding-left:32px;
            padding-right:32px;
        }
        table{
            width:100%;
            border-collapse: collapse;
        }
        table > tbody > tr > td{
            padding:2px;
            vertical-align: top;
        }
        html, body {
          margin-top: 38px;
          margin-bottom: 28px;
      }
    </style>
</head>
<body>
    @include('app/nuevosistema/cabecerapdf_a4')
    <div class="container">
        <h3 align="center">LISTA NEGRA</h3>
        <br>
        <table style="width:100%;">
            <thead class="table-dark">
                <tr>
                    <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" width="10px">NRO</th>
                    <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" width="80px">DNI/CE/RUC</th>
                    <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" width="350px">CLIENTE</th>
                    <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">MOTIVO</th>
                    <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" width="100px">REG. POR</th>
                    <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" width="120px">F. REGISTRO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listanegra as $key => $value)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $value->identificacion }}</td>
                    <td>{{ $value->db_cliente }}</td>
                    <td>{{ $value->motivo }}</td>
                    <td>{{ $value->codigo }}</td>
                    <td>{{ date_format(date_create($value->fecharegistro),'d-m-Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>