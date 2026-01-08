@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Realizar la Venta</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
@if(Auth::user()->idtienda==0)
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Con el usuario Master no puede realizar una venta, ingrese con un usuario de esta tienda!
    </div>
@else

              <div class="row">
                  <div class="col-md-4">
                  </div>
                  <div class="col-md-4">
                      <input type="number" id="codigo_pedido" placeholder="Código" style="text-align: center;font-size: 14px;border-radius: 20px;">
                  </div>
              </div>
              <div id="load_buscarcodigopedido"></div>
@endif
@endsection

@section('htmls')
<!--  modal registrarcliente --> 
<div class="main-register-wrap modal-registrarcliente">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Registrar Cliente</h3>
            <div class="mx-modal-cuerpo" id="contenido-registrarcliente">
              <div id="mx-carga-cliente">
              <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/venta',
                    method: 'POST',
                    carga: '#mx-carga-cliente',
                    data:{
                        view: 'registrarcliente'
                    }
                },
                function(resultado){
                    $('#idcliente').html('<option value=\''+resultado['cliente'].id+'\'>'+resultado['cliente'].identificacion+' - '+resultado['cliente'].apellidos+', '+resultado['cliente'].nombre+'</option>');
                    $('#direccion').val(resultado['cliente'].direccion);
                    $('#idubigeo').html('<option></option>');
                    if(resultado['cliente'].idubigeo!=0){
                        $('#idubigeo').html('<option value=\''+resultado['ubigeocliente'].id+'\'>'+resultado['ubigeocliente'].nombre+'</option>');                                                             
                    }
                    $('#contenido-registrarcliente').css('display','none');
                    confirm({
                        input:'#contenido-confirmar-registrarcliente',
                        resultado:'CORRECTO',
                        mensaje:'Se ha registrado correctamente!.',
                        cerrarmodal:'.modal-registrarcliente'
                    });       
                },this)">
                <div class="profile-edit-container">
                    <div class="custom-form">
                            <label>Tipo de Persona *</label>
                            <select id="cliente_idtipopersona">
                                @foreach($tipopersonas as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="cont-juridica" style="display:none;">
                                <label>RUC *</label>
                                <input type="text" id="cliente_ruc"/>
                                <label>Nombre Comercial *</label>
                                <input type="text" id="cliente_nombrecomercial"/>
                                <label>Razòn Social *</label>
                                <input type="text" id="cliente_razonsocial"/>
                            </div>
                            <div id="cont-natural" style="display:none;">
                              <label>DNI *</label>
                              <input type="text" id="cliente_dni"/>
                              <label>Nombre *</label>
                              <input type="text" id="cliente_nombre"/>
                              <label>Apellidos *</label>
                              <input type="text" id="cliente_apellidos"/>
                            </div>
                              <label>Número de Teléfono</label>
                              <input type="text" id="cliente_numerotelefono"/>
                              <label>Correo Electrónico</label>
                              <input type="text" id="cliente_email"/>
                              <label>Ubicación (Ubigeo) *</label>
                              <select id="cliente_idubigeo">
                                  <option></option>
                              </select>
                              <label>Dirección *</label>
                              <input type="text" id="cliente_direccion"/>
                    </div>
                </div>
                <div class="profile-edit-container">
                    <div class="custom-form">
                        <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
                    </div>
                </div> 
            </form> 
            </div>
            </div>
            <div class="mx-modal-cuerpo" id="contenido-confirmar-registrarcliente"></div>
        </div>
    </div>
</div>
<!--  fin modal registrarcliente --> 
<!--  modal ventarealizada --> 
<div class="main-register-wrap modal-ventarealizada" id="modal-ventarealizada">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div id="contenido-producto"></div>
        </div>
    </div>
</div>
<!--  fin modal ventarealizada --> 

@endsection
@section('subscripts')
<script>
  $('#codigo_pedido').select();
  $('#codigo_pedido').keyup( function(e) {
    if(e.keyCode == 13 && $('#codigo_pedido').val()!=''){
        buscarcodigopedido();
    }
  })
function buscarcodigopedido(){
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/venta/'+$("#codigo_pedido").val()+'/edit?view=buscarcodigo',result:'#load_buscarcodigopedido'});
}
</script>
@endsection