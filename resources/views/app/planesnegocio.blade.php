<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <a href="{{ url('backoffice/tienda') }}">TIENDAS</a><span>Terminos y Condiciones</span>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/inicio',
        method: 'POST'
    },
    function(resultado){
       location.href = '{{ Request::url() }}?view=ofertaindex';                                                                            
    },this)">
    <input type="hidden" id="view" value="pagotienda">
    <input type="hidden" id="idtienda" value="{{ $tienda->id }}">
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
              <div style="display:none;">
            <div class="col-md-6">
               <div class="form-group">
                 <label for="inputEmail4">Banco a Depositar *</label>
                 <?php
                 $bancos = DB::table('banco')->get();
                 $htmlbancos = '<option value="">-- Seleccionar Banco --</option>';
                 foreach($bancos as $value){
                     $htmlbancos = $htmlbancos.'<option value="'.$value->nombre.'">'.$value->nombre.'</option>';
                 }
                 ?>
                 <div class="header-search-select-item mx-header-search-select-item">
                     <select id="idbanco" class="chosen-select" style="display:none;width:100%;" onchange="selectcuenta(this.value)">
                       <?php echo $htmlbancos ?>
                     </select>
                 </div>
               </div>
               <div class="form-group">
                 <label for="inputPassword4">Nro de cuenta *</label>
                 <input type="text" id="nrocuenta" readonly>
               </div>
            </div>
            <div class="col-md-6">
              <label>Voucher *</label>
              <div class="add-list-media-wrap">
                <div class="fuzone">
                    <div class="fu-text">
                        <span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span>
                    </div>
                    <input type="file" class="upload" id="imagen">
                    <div id="resultado-logo" style="margin-top:-140px;position:inherit;z-index:1000;">
                    </div>
                </div>
              </div>
            </div>
              @if($empresa=='')
                <div class="col-md-12">
                  <label>RUC * <i class="fa fa-address-card"></i></label>
                  <input type="text" id="empresaruc"/>
                  <label>Nombre Comercial * <i class="fa fa-address-card"></i></label>
                  <input type="text" id="empresanombrecomercial"/>
                  <label>Razón Social * <i class="fa fa-address-card"></i></label>
                  <input type="text" id="empresarazonsocial"/>
                  <label>Dirección Fiscal * <i class="fa fa-map-marker-alt"></i></label>
                  <input type="text" id="empresadireccion"/>
                </div>
              @endif
              </div>
          </div>
              <iframe src="{{ url('public/backoffice/sistema/sitioweb/pdf/Negocio-TerminosYCondiciones.pdf') }}" frameborder="0" width="100%" height="500px" style="margin-bottom:5px;"></iframe>
        </div>
    </div>
    <!-- profile-edit-container end-->  										
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form" style="margin-bottom: 10px;">
            <div class="add-list-media-header" style="margin-bottom:10px;border: 1px dashed #3eaafd;">
                <label class="radio inline"> 
                <input type="radio" id="terminosycondiciones">
                <span>Aceptar los Terminos y Condiciones</span> 
                </label>
            </div>
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Enviar Activación<i class="fa fa-angle-right"></i></button>
        </div>
    </div>
    <!-- profile-edit-container end-->  
</form>     
@section('subscripts')
<style>
.fuzone {
    margin-top: 0px;
    min-height: 138px;
    margin-bottom: 20px;
    border: 2px dashed #3498db;
}
.fuzoneportada {
    margin-top: 0px;
    min-height: 238px;
    margin-bottom: 20px;
}
.fuzoneportada .fu-text {
    margin: 77px 0;
}
.profile-edit-header h4 {
    font-size: 16px;
    
}
</style>
  <style>
    .mx-header-search-select-item{
      width:100%;
    }
 
  </style>
<script>
  function selectcuenta(id){
      var cuenta = $('#idbanco option:selected').attr('cuenta');
      $('#nrocuenta').val(cuenta);
    }
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
                     $(param['result']).html('<img class="thumb" src="'+e.target.result+'" width="'+param['width']+'" height="'+param['height']+'" /><div style="margin-top:-'+param['height']+';margin-left:10px;font-size:18px;background-color:#c12e2e;padding:2px;padding-left:9px;padding-right:9px;border-radius:15px;color:#fff;font-weight:bold;cursor:pointer;position: absolute;z-index: 1000;" onclick="removeuploadfile({input:\''+param['input']+'\',result:\''+param['result']+'\',height:\''+param['height']+'\'})">x</div>');
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