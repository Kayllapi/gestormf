@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Asignar Negocios</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/logisticaruta') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/logisticaruta/{{ $s_logisticaruta->id }}',
        method: 'PUT',
        data:{
            view: 'editar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/logisticaruta') }}';                                                                            
    },this)">
        <div class="row">
          <div class="col-sm-3">
          </div>
          <div class="col-sm-6">
              <label>Persona *</label>
              <select id="idcliente">
                  <option></option>
              </select>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table" id="tabla-contenido">
              <thead class="thead-dark">
                <tr>
                  <th width="100px">RUC/DNI</th>
                  <th>Apellidos y Nombres</th>
                  <th>Teléfono</th>
                  <th>Distrito - Provincia - Departamento</th>
                  <th>Dirección</th>
                  <th width="10px"></th>
                </tr>
              </thead>
              <tbody num="0" id="tbody"></tbody>
              <tbody id="tbodycarga"></tbody>
          </table>
        </div>
        <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                             
@endsection
@section('subscripts')
<script> 
$("#idcliente").select2({
    @include('app.select2_cliente')
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showseleccionarusuario')}}",
        type:'GET',
        data: {
            idusuario : e.currentTarget.value
        },
        success: function (respuesta){
          $('#direccion').val(respuesta['usuario'].direccion);
          if(respuesta['usuario'].idubigeo!=0){
              $("#idubigeo").html('<option value="'+respuesta['usuario'].idubigeo+'">'+respuesta['usuario'].ubigeonombre+'</option>');
          }else{
              $("#idubigeo").html('<option></option>');
          }
          // delivery
          /*$('#delivery_pernonanombre').val(respuesta['usuario'].nombre);
          $('#delivery_numerocelular').val(respuesta['usuario'].numerotelefono);
          $('#delivery_direccion').val(respuesta['usuario'].direccion);*/
        }
    })
});        
</script>                
@endsection