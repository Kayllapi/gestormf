@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<?php
$usuario_activo = DB::table('users')
            ->where('users.iduserspadre',Auth::user()->id)
            ->count();
$planadquirido = planadquirido(Auth::user()->id);
$usuario_cantidad = $planadquirido['data']->cantuserscine;
$usuario_libre = $usuario_cantidad-$usuario_activo;
?>
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRAR USUARIO</span>
      <a class="btn btn-success" href="{{ url('backoffice/cineusuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
@if($usuario_libre<=0)
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> Ya no dispones de usuarios libres.
    </div>
@else
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
                  route: 'backoffice/cineusuario',
                  method: 'POST',
                  data: {
                      view : 'registrar'
                  }
              },
              function(resultado){
                if (resultado.resultado == 'CORRECTO') {
                  location.href = '{{ url('backoffice/cineusuario') }}';                           
                }
              },this)">
      <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                  <label>Nombre *</label>
                  <input type="text" id="nombre"/>
                  <label>Estado *</label>
                  <select class="form-control" id="idestado">
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                  </select>
              </div>
              <div class="col-md-6">
                  <label>Usuario *</label>
                  <input type="text" id="usuario"/>
                  <label>Contraseña *</label>
                  <input type="password" id="password"/>
              </div>
          </div>
        </div>
    </div>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
    </div>
</form>
@endif
@endsection
@section('scriptsbackoffice')
<script>
$("#idmodalidad").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-modalidad').css('display','none');
    if(e.currentTarget.value==1){
        $('#cont-modalidad').css('display','block');
    }
}).val(1).trigger("change");
$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val(1).trigger("change");
calcularfechainicio();  
function calcularfechainicio(){
  var fechaactual = new Date('{{Carbon\Carbon::now()->format('Y-m-d')}}');
  var dias = parseInt($('#diasprueba').val())+1;
  fechaactual.setDate(fechaactual.getDate() + dias);
  
  var year = fechaactual.getFullYear();
  var month = fechaactual.getMonth()+1;
  var day = fechaactual.getDate();
  fechacambiado = year+'-'+(month<10?'0'+month:month)+'-'+(day<10?'0'+day:day);
  $('#fechainicio').val(fechacambiado);
  
  $('#cont-diasprueba').css('display','none');
  if(dias>=0){
      $('#cont-diasprueba').css('display','block');
  }
}
</script>
@endsection