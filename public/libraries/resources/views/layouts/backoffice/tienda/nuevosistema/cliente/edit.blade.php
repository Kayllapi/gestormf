@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR USUARIO</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cliente') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cliente/{{ $usuario->id }}',
        method: 'PUT',
        data:{
            view: 'editar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cliente') }}';          
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Tipo de Persona *</label>
                <select id="idtipopersona" onchange="select_tipopersona()">
                    @foreach($tipopersonas as $value)
                    <option value="{{ $value->id }}" <?php echo $value->id==$usuario->s_idtipopersona ? 'selected' : '' ?>>{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <div id="cont-juridica" style="display:none;">
                    <label>Nombre Comercial * <i class="fa fa-user"></i></label>
                    <input type="text" value="{{ $usuario->nombre }}" id="nombrecomercial"/>
                    <label>Razòn Social * <i class="fa fa-user"></i></label>
                    <input type="text" value="{{ $usuario->apellidos }}" id="razonsocial"/>
                    <label>RUC *<i class="fa fa-address-card"></i></label>
                    <input type="text" value="{{ $usuario->identificacion }}" id="ruc"/>
                </div>
                <div id="cont-natural" style="display:none;">
                    <label>Nombre * <i class="fa fa-user"></i></label>
                    <input type="text" value="{{ $usuario->nombre }}" id="nombre"/>
                    <label>Apellidos * <i class="fa fa-user"></i></label>
                    <input type="text" value="{{ $usuario->apellidos }}" id="apellidos"/>
                    <label>DNI *<i class="fa fa-address-card"></i></label>
                    <input type="text" value="{{ $usuario->identificacion }}" id="dni"/>
                </div>
               
                <label>Número de Teléfono *<i class="fa fa-phone"></i>  </label>
                <input type="text" value="{{ $usuario->telefono }}" id="telefono"/>
             </div>
             <div class="col-md-6">
                <label>Correo Electrónico * <i class="fa fa-envelope"></i>  </label>
                <input type="text" value="{{ $usuario->correo }}" id="correo"/>
                <label>Ubicación (Ubigeo) *</label>
                <select id="idubigeo">
                    @foreach($ubigeos as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Dirección *<i class="fa fa-map-marker"></i>  </label>
                <input type="text" value="{{ $usuario->direccion }}" id="direccion"/>
                <label>Estado *</label>
                <select id="idestado">
                    <option value="1" selected>Activado</option>
                    <option value="2">Desactivado</option>
                </select>
             </div>
           </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios <i class="fa fa-angle-right"></i></button>
        </div>
    </div> 
</form>                             
@endsection
@section('subscripts')
<style>
</style>
<script>
$('#idtipopersona').niceSelect();
$('#idubigeo').niceSelect();
$('#idestado').niceSelect();
select_tipopersona();
function select_tipopersona(){
    var idtipopersona = $('#idtipopersona option:selected').val();
    $('#cont-juridica').css('display','none');
    $('#cont-natural').css('display','none');
    if(idtipopersona==1){
        $('#cont-natural').css('display','block');
    }else{
        $('#cont-juridica').css('display','block');
    }
}
</script>
@endsection