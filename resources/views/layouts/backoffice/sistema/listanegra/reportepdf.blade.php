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
            padding:5px 10px;
        }
        table{
            width:100%;
            border-collapse: collapse;
        }
        table > tbody > tr > td{
            padding:2px;
            vertical-align: top;
        }

        .container-informacion {
            border-collapse: collapse;
            width: 100%; 
        }
        .ficha-titulo{
            padding-bottom:2px;
        }
        .container-informacion > p {
            font-size:10px;
            border: 0.5px solid black; 
            padding: 8px; 
            margin: 0; 
        }
        html, body {
          margin-top: 38px;
          margin-bottom: 28px;
      }
      /* PDF A4 */
      .header { 
          position: fixed; 
          top: -38px; 
          left: 0px; 
          right: 0px; 
          height: 20px; 
          margin:15px;
          /* margin-left: 50px; */
          /* margin-right: 50px; */
          padding-bottom:5px;
          border-bottom: 2px solid #31353d ; 
      }
      .footer { 
          position: fixed; 
          left: 0px; 
          bottom: -25px; 
          right: 0px; 
          height: 25px;   
          margin:15px;
          margin-left: 50px;
          margin-right: 50px;
          padding-top:5px;
          border-top: 2px solid #31353d;
      }
      .page {
          float: right;
      }
      .content {
          width:100%;
          margin-left: 50px;
          margin-right: 50px;
      }
      .content_pdf {
          width:100%;
          margin-left: 50px;
          margin-right:-8px;
      }
      .content_pdf table {
          margin:0px;
          padding:0px;
          border-collapse: collapse;
          margin-right: 55px;
      }
      .content_pdf table td {
          padding:3px;
          text-align:left;
      }
      .footer .page:after { content: counter(page, decimal-leading-zero); }
      .header_agencia_logo {
          height: 50px;
          text-align: center;
          float: left;
          margin-right:10px;
      }
      .header_agencia_logo > img {
          display: block;
          max-width: 100%;
          height: 50px;
      }
      .header_agencia_informacion {
          float: right;
          width: 100%;
          text-align: right;
      }
      .header_agencia_nombrecomercial {
          font-size: 13px;
          font-weight: bold;
      }
      .header_agencia_ruc {
      }

    </style>
</head>
<body>
    
    <div class="header">
        <div class="header_agencia_informacion">
            <div class="header_agencia_nombrecomercial">{{ $tienda->nombre }} - {{ $tienda->nombreagencia }} | {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}</div>
        </div>
    </div>
    <div class="footer">
        <p class="page">Página </p>
    </div>
    <div class="container">
        <h3 align="center">LISTA NEGRA</h3>
        <br>
        <table border=1>
            <thead>
                <tr>
                    <th width="10px">NRO</th>
                    <th width="80px">DNI/CE/RUC</th>
                    <th width="350px">CLIENTE</th>
                    <th>MOTIVO</th>
                    <th width="100px">RESPOSANBLE</th>
                    <th width="120px">F.REGISTRO</th>
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