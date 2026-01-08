@extends('layouts.backoffice.master')
@section('cuerpobackoffice')  
<style>
  .custom-form textarea, .custom-form input[type="text"], .custom-form input[type=date], .custom-form input[type=password], .custom-form input[type=button] {
      float: left;
      border: 1px solid #eee;
      background: #f9f9f9;
      width: 100%;
      padding: 15px 20px 15px 55px;
      border-radius: 6px;
      color: #666;
      font-size: 13px;
      -webkit-appearance: none;
  }
  table.width200,table.rwd_auto {border:1px solid #4db7fe;width:100%;margin:0 0 50px 0}
      .width200 th,.rwd_auto th {    background: #4db7fe;padding: 5px;text-align: center;color: white;}
      .width200 td,.rwd_auto td {border-bottom:1px solid #ccc;padding:10px;text-align:center}
      .width200 tr:last-child td, .rwd_auto tr:last-child td{border:0}

    .rwd {width:100%;overflow:auto;}
      .rwd table.rwd_auto {width:auto;min-width:100%}
        .rwd_auto th,.rwd_auto td {white-space: nowrap;}

    @media only screen and (max-width: 760px), (min-width: 768px) and (max-width: 1024px)  
    {

      table.width200, .width200 thead, .width200 tbody, .width200 th, .width200 td, .width200 tr { display: block; }

      .width200 thead tr { position: absolute;top: -9999px;left: -9999px; }

      .width200 tr { border: 1px solid #ccc; }

      .width200 td { border: none;border-bottom: 1px solid #ccc; position: relative;padding-left: 50%;text-align:left }

      .width200 td:before {  position: absolute; top: 6px; left: 6px; width: 45%; padding-right: 10px; white-space: nowrap;}

      .width200 td:nth-of-type(1):before { content: "Nombre"; }
      .width200 td:nth-of-type(2):before { content: "Apellidos"; }
      .width200 td:nth-of-type(3):before { content: "Cargo"; }
      .width200 td:nth-of-type(4):before { content: "Twitter"; }
      .width200 td:nth-of-type(5):before { content: "ID"; }

      .descarto {display:none;}
      .fontsize {font-size:10px}
    }

    /* Smartphones (portrait and landscape) ----------- */
    @media only screen and (min-width : 320px) and (max-width : 480px) 
    {
      body { width: 320px; }
      .descarto {display:none;}
    }

    /* iPads (portrait and landscape) ----------- */
    @media only screen and (min-width: 768px) and (max-width: 1024px) 
    {
      body { width: 495px; }
      .descarto {display:none;}
      .fontsize {font-size:10px}
    }
    .btn {
      padding: 5px 10px;
      border-radius: 6px;
      background: #4db7fe;
      color: #ffffff;
      font-weight: 600;
    }
</style>


    <div class="profile-edit-container">
        <div class="profile-edit-header fl-wrap">
            <h4>TTTTTTTTTTTTTTTTTTTT</h4>
        </div>
        <div class="custom-form">
          <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-3">
                  <label>Fecha Inicio</label>
                  <input type="date" id="fechainicio"/>
                </div>
                <div class="col-md-3">
                  <label>Fecha Fin</label>
                  <input type="date" id="fechafin"/>
                </div>
                <div class="col-md-3">
                  <label>Categoría</label>
                  <select name="" id="idcategoria">
                    @foreach($categoria as $value)
                      <option value="{{$value->id}}"> {{$value->nombre}} </option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <button class="btn big-btn" style="cursor:pointer;" onclick="realiza_consulta()">Consultar</button>
    <div class="container">
      <table class="rwd_auto fontsize">
        <thead>
          <tr>
            <th>TIENDA</th>
            <th>DIRECCIÓN</th>
            <th>CATEGORIA</th>
            <th>CORREO</th>
            <th>TELÉFONO</th>
          </tr>
        </thead>
        <tbody> 
          @foreach($tiendas as $value)
          
          <tr>
            <td>{{ $value->nombreTienda }}</td>
            <td>{{ $value->direccionTienda }}</td>
            <td>{{ $value->nombreCategoria }}</td>
            <td>{{ $value->correoTienda }}</td>
            <td>{{ $value->numerotelefonoTienda }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

@endsection

@section('scriptsbackoffice')
<script>
$('#idcategoria').niceSelect();
  
function realiza_consulta(){
  var fechainicio = $('#fechainicio').val();
  var fechafin = $('#fechafin').val();
  var idcategoria = $('#idcategoria').val();
  console.log("INIT =>"+fechainicio);
  console.log("END =>"+fechafin);
  console.log("CATEGORY =>"+idcategoria);
  $.ajax({
      url: "{{ url('backoffice/reportetienda') }}",
      type:"POST",
    data:{ fechainicio:fechainicio, fechafin:fechafin, idcategoria:idcategoria} ,
      success:function(respuesta){
        console.log(respuesta);
//         $('#data-reporte').html(respuesta.data);
      },
  });

}
</script>
@endsection