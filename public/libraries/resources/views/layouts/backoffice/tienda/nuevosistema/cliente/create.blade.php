@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRAR CLIENTE</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cliente') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cliente',
        method: 'POST',
        data:{
            view: 'registrar',
            idtienda: '{{ $tienda->id }}'
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
                <select id="idtipopersona">
                    @foreach($tipopersonas as $value)
                    <option value="{{ $value->id }}" <?php echo $value->id==1 ? 'selected' : '' ?>>{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <div id="cont-juridica" style="display:none;">
                    <label>RUC *</label>
                    <input type="text" id="ruc"/>
                    <label>Nombre Comercial *</label>
                    <input type="text" id="nombrecomercial"/>
                    <label>Razòn Social *</label>
                    <input type="text" id="razonsocial"/>
                </div>
                <div id="cont-natural" style="display:none;">
                  <label>DNI</label>
                  <input type="text" id="dni"/>
                  <label>Nombre *</label>
                  <input type="text" id="nombre"/>
                  <label>Apellidos</label>
                  <input type="text" id="apellidos"/>
                </div>
               
                  <label>Número de Teléfono</label>
                  <input type="text" id="numerotelefono"/>
             </div>
             <div class="col-md-6">
                  <label>Imagen de Perfil</label>
                  <div class="fuzone" id="cont-fileupload">
                      <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
                      <input type="file" class="upload" id="imagen">
                      <div id="resultado-logo"></div>
                  </div>
                  <label>Correo Electrónico</label>
                  <input type="text" id="email"/>
                  <label>Ubicación (Ubigeo) *</label>
                  <select id="idubigeo">
                      <option></option>
                      @foreach($ubigeos as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                  </select>
                  <label>Dirección *</label>
                  <input type="text" id="direccion"/>
             </div>
           </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
        </div>
    </div> 
</form>                             
@endsection
@section('subscripts')
<script>
$("#idtipopersona").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-juridica').css('display','none');
    $('#cont-natural').css('display','none');
    if(e.currentTarget.value == 1) {
        $('#cont-natural').css('display','block');
    }else if(e.currentTarget.value == 2) {
        $('#cont-juridica').css('display','block');
    }
}).val(1).trigger("change");

$("#idubigeo").select2({
    placeholder: "---  Seleccionar ---",
    allowClear: true
});
</script>
@endsection