@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Movimiento de Productos</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productomovimiento') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-productomovimiento">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Tipo de Movimiento *</label>
                <select id="idtipomovimiento">
                    <option></option>
                    @foreach($tipomovimientos as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
             </div>
             <div class="col-md-6">
                <label>Motivo *</label>
                <input type="text" id="motivo" onkeyup="texto_mayucula(this)"/>
             </div>
           </div>
          <div class="table-responsive">
            <table class="table" id="tabla-contenido" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th width="15%">Código</th>
                    <th>Producto</th>
                    @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
                    <th width="50px">Stock</th>
                    @endif
                    <th width="60px">Cantidad</th>
                    <!--th width="60px">Fecha de Vencimiento</th-->
                    <th width="10px"></th>
                  </tr>
                  <tr>
                      <td class="mx-td-input"><input type="text" id="buscarcodigoproducto"/></td>
                      <td colspan="{{configuracion($tienda->id,'sistema_estadostock')['valor']==1?'3':'2'}}" class="mx-td-input">
                        <select id="idproducto">
                            <option></option>
                        </select>
                      </td>
                      <!--td></td-->
                      <td width="auto"></td>
                  </tr>
                </thead>
                <tbody num="0"></tbody>

            </table>
          </div>
          <a href="javascript:;" onclick="registrar_productomovimiento()" class="btn mx-btn-post">Registrar</a>
        </div>
    </div>
</div>
@endsection
@section('subscripts')
<script>
$("#idtipomovimiento").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
});
// registrar venta
function registrar_productomovimiento(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/productomovimiento',
        method: 'POST',
        carga: '#carga-productomovimiento',
        data:{
            view: 'registrar',
            productos: selectproductos(),
            idtipomovimiento: $('#idtipomovimiento').val(),
            motivo: $('#motivo').val(),
        }
    },
    function(resultado){
         location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productomovimiento') }}';                                                  
    })
}

  
$("#idproducto").select2({
    @include('app.select2_producto',[
        'idtienda'=>$tienda->id
    ])
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/productomovimiento/showseleccionarproducto')}}",
        type:'GET',
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
              respuesta["producto"].precioalpublico,
              respuesta["producto"].s_idproducto,
            );
          }
        }
    })
});
 
$('#buscarcodigoproducto').select();
  
$('#buscarcodigoproducto').keyup( function(e) {
    if(e.keyCode == 13){
        buscarcodigo('#buscarcodigoproducto');
    }
})
function buscarcodigo(pthis){
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showseleccionarproductocodigo')}}",
        type:'GET',
        data: {
            codigoproducto : $(pthis).val()
        },
        beforeSend: function (data) {
            carga({
                input:'#carga-productomovimiento',
                color:'info',
                mensaje:'Buscado el Producto, Espere por favor...'
            }); 
        },
        success: function (respuesta){
          $('#buscarcodigoproducto').val('');
          if(respuesta["resultado"]=='ERROR'){
              carga({
                  input:'#carga-productomovimiento',
                  color:'danger',
                  mensaje: respuesta['mensaje']
              });
          }else{
              agregarproducto(
                respuesta["producto"].id,
                respuesta["producto"].codigo,
                respuesta["producto"].nombre,
                respuesta["stock"],
                respuesta["producto"].precioalpublico,
                respuesta["producto"].s_idproducto,
              );
              removecarga({input:'#carga-productomovimiento'});
          }
        },
        error:function(respuesta){
              carga({
                  input:'#carga-productomovimiento',
                  color:'danger',
                  mensaje:formerror({dato:respuesta})
              });
        }
    })
}
function agregarproducto(idproducto,codigo,nombre,stock,precioalpublico,s_idproducto){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var style = 'background-color: #0ec529;color: #fff;';
  
      var tdstock = '';
      @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
      if(stock<1){
          style = 'background-color: #ce0e00;color: #fff;';
          
      }
      tdstock = '<td style="text-align: center"> '+stock+' </td>';
      @endif
  
      var num = $("#tabla-contenido tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" nombreproducto="'+codigo+' - '+nombre+'" style="'+style+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>'+tdstock;
          nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" placeholder="0"></td>';   
          //nuevaFila+='<td class="mx-td-input"><input id="fechavencimiento'+num+'" type="date"></td>';      
          nuevaFila+='<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'
          nuevaFila+='</tr>';
      $("#tabla-contenido").append(nuevaFila);
      $("#tabla-contenido tbody").attr('num',parseInt(num)+1);
  
      $('#productCant'+num).select();
      $('#productCant'+num).keyup( function(e) {
          if(e.keyCode == 13){
              $('#buscarcodigoproducto').select();
          }
      })
  
      // modal
      modal({click:'a#modal-registrarproductounidad'});
      // fin modal
}

function selectproductos(){
    var data = '';
    $("#tabla-contenido tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var fechavencimiento = '';//$("#fechavencimiento"+num).val();
        var nombreproducto = $(this).attr('nombreproducto');
        data = data+'/&/'+idproducto+'/,/'+productCant+'/,/'+nombreproducto+'/,/'+fechavencimiento;
    });
    return data;
}
  
function eliminarproducto(num){
    $("#tabla-contenido tbody tr#"+num).remove();
}
  

</script>
@endsection