@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuración de Comida',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
        method: 'PUT',
        data:   {
            view: 'config_comida'
        }
    },
    function(resultado){
        location.reload();                                                                        
    },this)">
    <div class="tabs-container" id="tab-menuconfiguracioncomida">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-menuconfiguracioncomida-0">Pedidos</a></li>
        </ul>
        <div class="tab">
            <div id="tab-menuconfiguracioncomida-0" class="tab-content" style="display: block;">
                  <div class="row">
                      <div class="col-sm-6">
                          <label>Cantidad de Mesas</label>
                          <input type="number" value="{{configuracion($tienda->id,'comida_cantidadmesa')['valor']}}" min="1" id="comida_cantidadmesa">
                      </div>
                  </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>
<hr>
<!-- <form action="javascript:;" 
      onsubmit="callback({
        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
        method: 'PUT',
        data:   {
            view: 'config_comida_nuevo',
            piso_ambiente_mesa: seleccionarMesa(),
        }
    },
    function(resultado){
//         location.reload();
    },this)">
    <div class="tabs-container" id="tab-menuconfiguracioncomida1">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-menuconfiguracioncomida1-0">Pedidos</a></li>
        </ul>
        <div class="tab">
            <div id="tab-menuconfiguracioncomida1-0" class="tab-content" style="display: block;">
                  <table class="table" id="tabla-mesa">
                    <thead class="thead-dark" style="padding: 0px;">
                      <tr>
                        <th class="mx-td-input">Piso</th>
                        <th class="mx-td-input">Ambiente</th>
                        <th class="mx-td-input">Mesa</th>
                        <th width="10px" style="padding: 0px;padding-right: 1px;">
                          <a href="javascript:;" class="btn  color-bg flat-btn" onclick="agregarFilaMesa()">
                            <i class="fa fa-plus"></i>
                          </a>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
            </div>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form> -->

<hr>


@endsection
@section('subscripts')
<script>
tab({click:'#tab-menuconfiguracioncomida'});
  
function agregarFilaMesa(){
      var num = $("#tabla-mesa > tbody").attr('num');
      $('#tabla-mesa > tbody').append('<tr id="'+num+'">'+
                                           '<td class="mx-td-input"><input id="piso'+num+'" type="text" value="" onkeyup="texto_mayucula(this)"></td>'+
                                           '<td class="mx-td-input"><input id="ambiente'+num+'" type="text" value="" onkeyup="texto_mayucula(this)"></td>'+
                                           '<td class="mx-td-input"><input id="mesa'+num+'" type="text" value="" onkeyup="texto_mayucula(this)"></td>'+
                                           '<td class="mx-td-input"><a id="del'+num+'" href="javascript:;" onclick="eliminarmesa('+num+')" class="btn btn-danger big-btn" style="padding: 12px 15px;"><i class="fa fa-close"></i></a></td>'+
                                       '</tr>');
      $("#tabla-mesa > tbody").attr('num',parseInt(num)+1);
}
  
function eliminarMesa(num){
    $("#tabla-mesa > tbody > tr#"+num).remove();
}

function seleccionarMesa() {
  var data = [];
  $("#tabla-mesa > tbody > tr").each(function() {
      var num = $(this).attr('id');  
      data.push({
        piso: $("#piso"+num).val(),
        ambiente: $("#ambiente"+num).val(),
        mesa: $("#mesa"+num).val()s,
      });
  });
  
  if(data.length==0){
      return '';
  }else{
      console.log(data);
      return JSON.stringify(data);
  }
}
</script>
@endsection