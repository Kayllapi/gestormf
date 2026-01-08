@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/negocios/{{ $planadquirido->idtienda }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/negocios') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="{{ $planadquirido->id }}" id="idpagotienda"/>
    <input type="hidden" value="confirmacionpago" id="view"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="profile-edit-header fl-wrap">
            <h4>Confirmacion de Pago</h4>
        </div>
        <div class="custom-form">
          <div class="row">
            <div style="display:none;">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <label>Tienda/Negocio <i class="fa fa-briefcase"></i></label>
                  <input type="text" value="{{$planadquirido->tiendanombre}}" readonly/>
                </div>
                <div class="col-md-12">
                  <label>Monto pagado <i class="fa fa-briefcase"></i></label>
                  <input type="text" value="{{$planadquirido->costo}}" readonly/>
                </div>
                <div class="col-md-12">
                  <label>Banco <i class="fa fa-briefcase"></i></label>
                  <input type="text" value="{{$planadquirido->banco}}" readonly/>
                </div>
                <div class="col-md-12">
                  <label>Número de cuenta <i class="fa fa-briefcase"></i></label>
                  <input type="text" value="{{$planadquirido->nrocuenta}}" readonly/>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <label>Voucher de pago *</label>
              <img width="80%" src="<?php echo url('public/backoffice/tienda/'.$planadquirido->idtienda.'/voucherpago/'.$planadquirido->voucher)?>" alt="">
            </div>
            </div>
          </div>
        </div>
    </div>
    <!-- profile-edit-container end-->  										
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Confirmar Activación<i class="fa fa-angle-right"></i></button>
        </div>
    </div>
    <!-- profile-edit-container end-->  
</form>                             
@endsection
