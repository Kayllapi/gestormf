@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<?php
$idestadotransferencia = 'null';
$title = '';
$estadodisabled = '';
$estadorequired = '';
$disabled = '';
$required = '';
$view = 'editar';
if($productotransferencia->idestadotransferencia==1){
    if($productotransferencia->idtiendaorigen){
        $view = 'enviar';
        $title = 'Enviar';
        $estadodisabled = 'disabled';
        $disabled = 'disabled';
        $idestadotransferencia = 2;
        $required = '*';
    }else{
        $view = 'editar';
        $title = 'Editar';
        $idestadotransferencia = 1;
        $estadorequired = '*';
        $required = '*';
    }
}elseif($productotransferencia->idestadotransferencia==2){
    if($productotransferencia->idtiendadestino){
        $view = 'recepcionar';
        $title = 'Recepcionar';
        $estadodisabled = 'disabled';
        $disabled = 'disabled';
        $idestadotransferencia = 3;
    }else{
        $view = 'editar';
        $title = 'Editar';
        $estadorequired = '*';
        $required = '*';
        $idestadotransferencia = 2;
    }
}elseif($productotransferencia->idestadotransferencia==3){
   
}  
?>
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>{{$title}} Transferencia de Productos</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-formproductotransferencia">
      <form action="javascript:;" 
              id="formproductotransferencia"
              onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/productotransferencia/{{$productotransferencia->id}}',
                    method: 'PUT',
                    carga: '#carga-formproductotransferencia',
                    idform: 'formproductotransferencia',
                    data: {
                       view: '{{$view}}',
                       productos: selectproductos()
                    }
                },
                function(resultado){
                     location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia') }}';                                                                            
                },this)"> 
      <div class="profile-edit-container">
        <div class="custom-form">
           <div class="row">
            <div class="col-sm-6">
               <label>Estado <?php echo $estadorequired ?></label>
                  <select class="form-control" id="idestadotransferencia" <?php echo $estadodisabled ?>>
                      <option value="1">Solicitar Productos</option>
                      <option value="2">Enviar Productos</option>
                      <option value="3">Recepcionar Productos</option>
                  </select>
                <label >Motivo</label>
                  <input type="text" class="form-control" value="{{$productotransferencia->motivo}}" id="motivo"  <?php echo $disabled ?>>
            </div>
            <div class="col-sm-6">
               <label >De <?php echo $required ?></label>
                  <select class="form-control" id="idtiendaorigen">
                      <option></option>
                    @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                <label >Para <?php echo $required ?></label>
                  <select class="form-control" id="idtiendadestino">
                      <option></option>
                    @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
              </div>
         </div>
      </div>
     </div>
   </form>
       
        <div class="table-responsive">
            <table class="table" id="tabla-productotransferencia" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>U. Medida</th>
                    <th width="80px">Cantidad</th>
                    @if($title=='Recepcionar')
                    <th width="90px">Enviado</th>
                    <th width="90px">Recepcionar</th>
                    @elseif($title=='Enviar')
                    <th width="90px">Enviar</th>
                    
                    @endif
                    <th width="200px">Motivo</th>
                  </tr>
                </thead>
                <tbody num="{{count($detalletransferencia)}}">
                    <?php $i=0 ?>
                    @foreach($detalletransferencia as $value)
                    <tr id="{{$i}}" idproducto="{{$value->idproducto}}">
                      <td>{{str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT)}}</td>
                      <td>{{$value->productonombre}}</td>
                      <td ><select id="idunidadmedida{{$i}}" disabled><option value="{{$value->idunidadmedida}}">{{$value->unidadmedidanombre}}</option></select></td>
                      <td ><input class="form-control" id="productCant{{$i}}" style="width: 70px;" type="number"  value="{{$value->cantidad}}" <?php echo $disabled ?>></td>
                      @if($title=='Recepcionar')
                      <td ><input class="form-control" id="productEnviar{{$i}}" type="text" value="{{$value->cantidadenviado}}" style="width: 70px;" disabled></td>
                      <td ><input class="form-control" id="productRecepcionar{{$i}}" type="number" value="{{$value->cantidadenviado}}" style="width: 70px;"></td>
                      @elseif($title=='Enviar')
                      <td  class="with-form-control"><input class="form-control" id="productEnviar{{$i}}" type="number" value="{{$value->cantidad}}" ></td>
                      @endif
                      <td ><input class="form-control" id="productMotivo{{$i}}" type="text" value="{{$value->motivo}}" <?php echo $disabled ?>></td> 
                      @if($disabled=='' or $title=='Enviar')
                      <td class="with-btn"><a id="del{{$i}}" href="javascript:;" onclick="eliminarproducto({{$i}})" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>
                      @endif
                    </tr>
                   <script>
                      $("select#idunidadmedida{{$i}}").select2({
                          placeholder: "--  Seleccionar --",
                          minimumResultsForSearch: -1
                      });
                    </script>
                    <?php $i++ ?>
                    @endforeach
                </tbody>
            </table>
        </div>
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formproductotransferencia').submit();">
          @if($title=='Editar')
          Guardar Cambios
          @elseif($title=='Enviar')
          Enviar
          @elseif($title=='Recepcionar')
          Recepcionar
          @endif
        </a>
    </div>
  </div>

@endsection
@section('subscripts')
<script>
$('#idestadotransferencia').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    if(e.currentTarget.value==1){
        $('#idtiendaorigen').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val({{$productotransferencia->idtiendaorigen}}).trigger('change'); 
        $('#idtiendadestino').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val({{$productotransferencia->idtiendadestino}}).trigger('change'); 
      
        $('#idtiendaorigen').removeAttr('disabled');
        $('#idtiendadestino').attr('disabled','true');
    }else if(e.currentTarget.value==2){
        $('#idtiendaorigen').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val({{$productotransferencia->idtiendaorigen}}).trigger('change'); 
        $('#idtiendadestino').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val({{$productotransferencia->idtiendadestino}}).trigger('change'); 
      
        $('#idtiendaorigen').attr('disabled','true');
        $('#idtiendadestino').attr('disabled','true');
    }else if(e.currentTarget.value==3){
        $('#idtiendaorigen').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val({{$productotransferencia->idtiendaorigen}}).trigger('change'); 
        $('#idtiendadestino').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val({{$productotransferencia->idtiendadestino}}).trigger('change'); 
      
        $('#idtiendaorigen').attr('disabled','true');
        $('#idtiendadestino').attr('disabled','true');
    }
}).val({{$idestadotransferencia}}).trigger('change'); 
  
$('#idtiendaorigen').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}); 
  
$('#idtiendadestino').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
});
  
$('#codigoproducto').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        var codigoproducto = $('#codigoproducto').val();
        var pastedText = codigoproducto.split(/\r?\n/g);
        if(pastedText.length>2){
            $.each(pastedText, function( key, value ) {
                if(value!=''){
                    var dataText = value.split('	');
                    var codigoproducto = dataText[0];
                    var cantidadproducto = dataText[1];
                    $.ajax({
                        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showseleccionarproductocodigo')}}",
                        type:'GET',
                        data: {
                            codigoimpresion : codigoproducto
                        },
                        success: function (respuesta){
                          if(respuesta["producto"]!=null){
                            var validexist = 0;
                            $("#tabla-productotransferencia tbody tr").each(function() {
                                var num = $(this).attr('id');        
                                var idproducto = $(this).attr('idproducto');
                                if(idproducto==respuesta["producto"].id){
                                    validexist = 1;
                                    alert('Ya existe en la lista!');
                                }
                            });
                            if(validexist==0){
                                agregarproducto(
                                  respuesta["producto"].id,
                                  respuesta["producto"].codigo,
                                  respuesta["producto"].nombre,
                                  respuesta["stock"],
                                  respuesta["producto"].idunidadmedida,
                                  respuesta["producto"].unidadmedidanombre,
                                );
                            }
                                
                          }
                        }
                    }) 
                }
            });
        }else{
            $.ajax({
                url:"{{url('backoffice/productotransferencia/show-agregarproductocodigo')}}",
                type:'GET',
                data: {
                    codigoimpresion : $('#codigoproducto').val()
                },
                success: function (respuesta){
                  if(respuesta["producto"]!=null){
                    var validexist = 0;
                    $("#tabla-productotransferencia tbody tr").each(function() {
                        var num = $(this).attr('id');        
                        var idproducto = $(this).attr('idproducto');
                        if(idproducto==respuesta["producto"].id){
                            validexist = 1;
                            alert('Ya existe en la lista!');
                        }
                    });
                    if(validexist==0){
                        agregarproducto(
                          respuesta["producto"].id,
                        respuesta["producto"].codigo,
                        respuesta["producto"].nombre,
                        respuesta["stock"],
                        respuesta["producto"].idunidadmedida,
                        respuesta["producto"].unidadmedidanombre,
                        );
                    }
                        
                  }
                }
            })  
        } 
    }     
});
function agregarproducto(idproducto,codigo,nombre,idunidadmedida,unidadmedidanombre,cantidad,motivo){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var style="background-color:#abfbab;color: #000;";
      if(stock<=0){
          var style="background-color:#ffafaf;color: #000;";
      }
      var num = $("#tabla-productotransferencia tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="'+style+'" ">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td class="with-form-control"><select id="idunidadmedida'+num+'" disabled><option value="'+idunidadmedida+'">'+unidadmedidanombre+'</option></select></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number"  value="'+cantidad+'"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productMotivo'+num+'" type="text" value="'+motivo+'"></td>'; 
          nuevaFila+='<td class="with-btn"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
          nuevaFila+='</tr>';
      $("#tabla-productotransferencia tbody").append(nuevaFila);
      $("#tabla-productotransferencia tbody").attr('num',parseInt(num)+1);
  
      $("select#idunidadmedida"+num).select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
}
  
function calcularstock(){
        $("#tabla-productotransferencia tbody tr").each(function() {
            var num = $(this).attr('id');        
            var stock = parseFloat($(this).attr('stock'));   
            var productCant = parseFloat($("#productCant"+num).val());
            var style="background-color:#abfbab;color: #000;";
            if(stock<productCant){
                var style="background-color:#ffafaf;color: #000;";
            }
            $(this).attr('style',style);
        });
}
  
function selectproductos(){
    var data = '';
    $("#tabla-productotransferencia tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var idunidadmedida = $("#idunidadmedida"+num).val();
        var productMotivo = $("#productMotivo"+num).val();
        var productEnviar = $("#productEnviar"+num).val();
        var productRecepcionar = $("#productRecepcionar"+num).val();
        data = data+'&'+idproducto+','+productCant+','+idunidadmedida+','+productMotivo+','+productEnviar+','+productRecepcionar;
    });
    return data;
}
  
function eliminarproducto(num){
    $("#tabla-productotransferencia tbody tr#"+num).remove();
}
</script>
@endsection