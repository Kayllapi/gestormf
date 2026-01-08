@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="mx-cont-btn">
    <a href="{{ Request::url() }}?view=ofertaindex" class="btn big-btn mx-btn-atras"><i class="fa fa-angle-left"></i> Atras</a>
</div>
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <a href="{{ Request::url() }}?view=ofertaindex">OFERTAS</a><span>Editar Oferta</span>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/{{ $oferta->id }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ Request::url() }}?view=ofertaindex';                                                                            
    },this)">
    <input type="hidden" value="ofertaedit" id="view"/>
     <input type="hidden" value="{{ $oferta->idtienda }}" id="idtienda"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <label>Producto/Servicio *<i class="fa fa-angle-double-right"></i></label>
              <input type="text" id="nombre" value="{{$oferta->nombre}}"/>
              <label>Stock de Oferta (0 = Ilimitado) *</label>
              <div class="quantity fl-wrap">
                  <div class="quantity-item">
                      <input type="button" value="-" class="minus">
                      <input type="text" id="stock" title="Qty" class="qty" min="0" max="100000" step="1" value="{{$oferta->stock}}" style="padding-left: 0px;">
                      <input type="button" value="+" class="plus">
                  </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <label>Precio Normal s/. * <i class="fa fa-tags"></i></label>
                  <input type="text" id="precio" value="{{$oferta->precio}}"/>
                </div>
                <div class="col-md-6">
                  <label>Precio con Oferta s/. * <i class="fa fa-tags"></i></label>
                  <input type="text" id="preciooferta" value="{{$oferta->preciooferta}}"/>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <label>Inicio * <i class="fa fa-calendar-alt"></i></label>
                  <input type="date"  id="fechainicio" value="{{$oferta->fechainicio}}"/>
                </div>
                <div class="col-md-6">
                  <label>Fin * <i class="fa fa-calendar-alt"></i></label>
                  <input type="date" id="fechafin" value="{{$oferta->fechafin}}"/>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <label>Imagen (Oferta)</label>
              <div class="add-list-media-wrap">
                <div class="fuzone">
                    <div class="fu-text">
                        <span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span>
                    </div>
                    <input type="file" class="upload" id="imagen">
                    @if($oferta->imagen!='')
                    <div id="resultado-logo" style="margin-top:-328px;position:inherit;z-index:1;">
                      <img class="thumb" src="{{ url('public/backoffice/tienda/'.$oferta->idtienda.'/oferta/'.$oferta->imagen) }}" width="" height="328px" />
                      <div id="imagenfileclose" 
                       style="float:right;margin-top:-320px;margin-left:10px;font-size:18px;
                              background-color:#c12e2e;padding:2px;padding-left:9px;padding-right:9px;
                              border-radius:15px;color:#fff;font-weight:bold;cursor:pointer;position: absolute;z-index: 1;" 
                       onclick="removeuploadfile({input:'#imagen',result:'#resultado-logo',height:'328px'})">x</div>
                    <input type="hidden" value="{{ $oferta->imagen }}" id="imagenant"/>
                    </div>
                    @else
                    <div id="resultado-logo" style="margin-top:-328px;position:inherit;z-index:1;">
                    <input type="hidden" id="imagenant"/>
                    </div>
                    @endif
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <!-- profile-edit-container end-->  										
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios<i class="fa fa-angle-right"></i></button>
        </div>
    </div>
    <!-- profile-edit-container end-->  
</form>                             
@endsection
@section('subscripts')
<style>
.fuzone {
    margin-top: 0px;
    min-height: 335px;
    margin-bottom: 20px;
    border: 2px dashed #3498db;
}
.fuzone .fu-text {
    margin: 125px 0;
}
.fuzoneportada {
    margin-top: 0px;
    min-height: 238px;
    margin-bottom: 20px;
}
.profile-edit-header h4 {
    font-size: 16px;
    
}
</style>
<script>
uploadfile({input:"#imagen",result:"#resultado-logo",height:'328px'});
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
                     $(param['result']).html('<img class="thumb" src="'+e.target.result+'" width="'+param['width']+'" height="'+param['height']+'" /><div style="margin-top:-320px;margin-left:10px;font-size:18px;background-color:#c12e2e;padding:2px;padding-left:9px;padding-right:9px;border-radius:15px;color:#fff;font-weight:bold;cursor:pointer;position: absolute;z-index: 1000;" onclick="removeuploadfile({input:\''+param['input']+'\',result:\''+param['result']+'\',height:\''+param['height']+'\'})">x</div>');
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