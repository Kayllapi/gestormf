@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Transferencia de Productos</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
 <div id="carga-transferencia">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-sm-6">
               <label>Estado *</label>
                 <select class="form-control" id="idestadotransferencia">
                     <option></option>
                     <option value="1">Solicitar Productos</option>
                     <option value="2">Enviar Productos</option>
                 </select>
               <label>Motivo</label>
                 <input type="text" id="motivo"/>
            </div>
            <div class="col-sm-6">
              <label>De *</label>
                  <select class="form-control" id="idtiendaorigen">
                    <option></option>
                   @foreach($tiendas as $value)
                    <option value="{{$value->id}}" >{{$value->nombre }}</option>
                   @endforeach
                  </select>
              <label>Para *</label>
                  <select class="form-control" id="idtiendadestino">
                     <option></option>
                    @foreach($tiendas as $value)
                     <option value="{{$value->id}}" >{{ $value->nombre }}</option>
                    @endforeach
                  </select>
             </div>
          </div>
          <div class="table-responsive">
                <table class="table" id="tabla-contenido">
                    <thead class="thead-dark">
                        <tr>
                          <th width="15%">Código</th>
                            <th >Producto</th>
                            @if($configuracion!='')
                                @if($configuracion->venta_estadostock==1)
                                <th width="50px">Stock</th>
                                @endif
                            @endif
                           <th width="110px">Unidad de Medida</th>
                          <th width="110px">Cantidad</th>
                          <th width="110px">Motivo</th>
                          <th width="10px"></th>
                        </tr>
                        <tr>
                            <td class="mx-td-input">
                               <input type="text" id="buscarcodigoproducto"/>
                            </td>
                            <td colspan="{{$configuracion!=''?($configuracion->venta_estadostock==1?'5':'4'):'5'}}" class="mx-td-input">
                                <select id="idproducto">
                                    <option></option>
                                </select>
                            </td>
                          <td width="auto"></td>
                        </tr>
                    </thead>
                    <tbody num="0" id="tbody"></tbody>
                    <tbody num="0" id="tbodycarga"></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <a href="javascript:;" onclick="registrar_transferencia()" id="registrar_transferencia" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</a>
        </div>
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
        }).val(null).trigger('change'); 
        $('#idtiendadestino').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val({{ $tienda->id }}).trigger('change'); 
      
        $('#idtiendaorigen').removeAttr('disabled');
        $('#idtiendadestino').attr('disabled','true');
    }else if(e.currentTarget.value==2){
        $('#idtiendaorigen').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val({{ $tienda->id }}).trigger('change'); 
        $('#idtiendadestino').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val(null).trigger('change'); 
      
        $('#idtiendaorigen').attr('disabled','true');
        $('#idtiendadestino').removeAttr('disabled');
    }
}).val(2).trigger('change'); 
  
$('#idtiendaorigen').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}); 
  
$('#idtiendadestino').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
});
   
  
   //Seleccionar y  Busqueda Producto
 $("#idproducto").select2({
    @include('app.select2_producto',[
        'idtienda'=>$tienda->id
    ])
      }).on("change", function(e) {
        $.ajax({
            url:  "{{url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/showseleccionarproducto')}}",
            type: 'GET',
            data: {
                idproducto : e.currentTarget.value
            },
            success: function (respuesta){
                if(respuesta["producto"]!=null){
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
        })
    });
  
   //Buscador de Codigo
   $('#buscarcodigoproducto').select();
  
   $('#buscarcodigoproducto').keyup( function(e) {
        if(e.keyCode == 13){
            buscarcodigo('#buscarcodigoproducto');
        }
      if(e.keyCode == 27){
        $('#registrar_transferencia').focus();
        $('#registrar_transferencia').select();
    }
    })

  //Buscar Codigo 
  function buscarcodigo(pthis){
    if($(pthis).val()!=''){
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showseleccionarproductocodigo')}}",
            type:'GET',
            data: {
                codigoproducto : $(pthis).val()
            },
            beforeSend: function (data) {
                var nuevaFila='<tr style="background-color: #008cea;color: #fff;">';
                                nuevaFila+='<td id="tdcargaproducto"" colspan="9" class="tddescuento"></td>';
                                nuevaFila+='</tr>';
                $("#tabla-contenido > tbody#tbodycarga").html(nuevaFila);
                load('#tdcargaproducto');
            },
            success: function (respuesta){
              $("#tabla-contenido > tbody#tbodycarga").html('');
              $('#buscarcodigoproducto').val('');
              if(respuesta["resultado"]=='ERROR'){
                  carga({
                      input:'#carga-transferencia',
                      color:'danger',
                      mensaje: respuesta['mensaje']
                  });
              }else{
                  agregarproducto(
                    respuesta["producto"].id,
                     respuesta["producto"].codigo,
                     respuesta["producto"].nombre,
                     respuesta["stock"],
                     respuesta["producto"].idunidadmedida,
                     respuesta["producto"].unidadmedidanombre,
                  );
                  removecarga({input:'#carga-transferencia'});
              }
            },
            error:function(respuesta){
                  carga({
                      input:'#carga-transferencia',
                      color:'danger',
                      mensaje:formerror({dato:respuesta})
                  });
            }
        })
      }      
    }
  function registrar_transferencia(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/productotransferencia',
        method: 'POST',
        carga: '#carga-transferencia',
        data:{
            view: 'registrar',
            productos: selectproductos(),
            idestadotransferencia: $('#idestadotransferencia').val(),
            motivo: $('#motivo').val(),
            idtiendaorigen: $('#idtiendaorigen').val(),
            idtiendadestino: $('#idtiendadestino').val()
          
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia') }}';                                                                            
    })
}
  
   // funcion agregarproducto(), agregando en una nueva fila el producto
    function agregarproducto(idproducto,codigo,nombre,stock,idunidadmedida,unidadmedidanombre,cantidad ){
        $("#codigoproducto").val('');
        $("#idproducto").html('');
        var num = $("#tabla-contenido > tbody#tbody").attr('num');
        var style   = 'background-color: #0ec529;color: #fff;';
        @if($configuracion!='')
            @if($configuracion->venta_estadostock==1)
                if(stock<1){
                    style = 'background-color: #ce0e00;color: #fff;';
                }
            @endif
        @endif
        var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" nombreproducto="'+codigo+' - '+nombre+'" style="'+style+'">';
            nuevaFila+='<td>'+codigo+'</td>';
            nuevaFila+='<td>'+nombre+'</td>';
            nuevaFila+='<td class="with-form-control"><select id="idunidadmedida'+num+'" disabled><option value="'+idunidadmedida+'">'+unidadmedidanombre+'</option></select></td>';
            nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'"></td>';
            nuevaFila+='<td class="with-form-control"><input class="form-control" id="productMotivo'+num+'" type="text"></td>'; 
            nuevaFila+='<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'
            nuevaFila+='</tr>';
         $("#tabla-contenido > tbody#tbody").append(nuevaFila);
         $("#tabla-contenido > tbody#tbody").attr('num',parseInt(num)+1);  
      
       $("select#idunidadmedida"+num).select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });

          setTimeout(function(){ $('#productCant'+num).select(); }, 100);
          $('#productFechavencimiento'+num).keyup( function(e) {
          if(e.keyCode == 13){
              $('#buscarcodigoproducto').select();
          }
          if(e.keyCode == 27){
              $('#registrar_transferencia').focus();
              $('#registrar_transferencia').select();
          }
      })
    }
  function selectproductos(){
    var data = '';
    $("#tabla-contenido tbody tr").each(function() {
        var num           = $(this).attr('id');        
        var idproducto    = $(this).attr('idproducto');
        var productCant   = $("#productCant"+num).val();
        var idunidadmedida = $("#idunidadmedida"+num).val();
              var productMotivo = $("#productMotivo"+num).val();
        data = data+'/&/'+idproducto+'/,/'+productCant+'/,/'+idunidadmedida+'/,/'+productMotivo;
    });
    return data;
}
    // Funcion para eliminar una fila de los productos
 function eliminarproducto(num){
        $("#tabla-contenido tbody tr#"+num).remove();
    }
</script>
@endsection