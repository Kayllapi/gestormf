@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR AGENCIA</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comprobante') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/comprobante/{{ $datosComprobante->id }}',
        method: 'PUT',
    },
    function(resultado){
            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comprobante') }}';                                                            
    },this)"
      autocomplete="off">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>NOMBRE<i class="fa fa-user"></i> </label>
                <input type="text" id="nombre" value="{{$datosComprobante->nombre}} "/>
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
uploadfile({input:"#imagen",result:"#resultado-logo",height:'155px'});
function uploadfile(param){
    $(param['input']).change(function(evt) {
            var files = evt.target.files;
            for (var i = 0, f; f = files[i]; i++) {
              if (!f.type.match('image.*')) {
                  continue;
              }
              var reader = new FileReader();
              reader.onload = (function(theFile) {
                  return function(e) {
                     var inp = param['result'].split('#'); 
                     $(param['result']).html('<img class="thumb" src="'+e.target.result+'" width="'+param['width']+'" height="'+param['height']+'" /><div style="margin-top:-'+param['height']+';margin-left:10px;font-size:18px;background-color:#c12e2e;padding:2px;padding-left:9px;padding-right:9px;border-radius:15px;color:#fff;font-weight:bold;cursor:pointer;position: absolute;z-index: 100;" onclick="removeuploadfile({input:\''+param['input']+'\',result:\''+param['result']+'\',height:\''+param['height']+'\'})">x</div>');
                  };
              })(f);
              reader.readAsDataURL(f);
            }
    });
}
function removeuploadfile(param){
  $(param['result']).html('<input type="hidden" id="imagenant"/>');
}
</script>
@endsection