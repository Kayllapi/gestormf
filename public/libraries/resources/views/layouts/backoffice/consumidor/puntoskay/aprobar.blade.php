@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Aprobar Monedas KAY</span>
      <a class="btn btn-success" href="{{ url('backoffice/consumidor/puntoskay') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>

<div id="carga-puntoskay">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <label>Fecha de Registro</label>
              <input type="text" value="{{ date_format(date_create($puntoskay->fecharegistro),"d/m/Y h:i:s A") }}" id="correo" disabled>
              <label>Correo Electrónico</label>
              <input type="text" value="{{ $puntoskay->usersemail }}" id="correo" disabled>
              <label>Apellidos y Nombres</label>
              <input type="text" value="{{ $puntoskay->usersapellidos }}, {{ $puntoskay->usersnombre }}" id="nombre" disabled>
              <label>Cantidad de Puntos KAY</label>
              <input type="text" value="{{ $puntoskay->cantidad }}" id="cantidad" disabled>
              <label>Total a Pagar</label>
              <input type="text" value="{{ $puntoskay->total }}" id="monto" disabled>
            </div>
            <div class="col-md-6">
              <img src="{{ url('public/backoffice/consumidor/voucher/'.$puntoskay->voucher) }}" style="margin-bottom: 5px;">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
            <button type="button" class="btn  big-btn  color-bg flat-btn" onclick="confirmar_puntokay()" style="width: 100%;margin-bottom: 5px;">Aprobar</button>
            </div>
            <div class="col-md-6">
            <button type="button" class="btn  big-btn  color-bg flat-btn" onclick="rechazar_puntokay()" style="width: 100%;background-color: #ab1e1e;">Rechazar</button>
            </div>
          </div>
        </div>
    </div> 
</div>                           
@endsection
@section('scriptsbackoffice')
<script>
function confirmar_puntokay(){
    callback({
        route: 'backoffice/consumidor/puntoskay/{{ $puntoskay->id }}',
        method: 'PUT',
        carga: '#carga-puntoskay',
        data:{
            view:'aprobar'        
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/consumidor/puntoskay') }}';                                                                            
    })
}  
function rechazar_puntokay(){
    callback({
        route: 'backoffice/consumidor/puntoskay/{{ $puntoskay->id }}',
        method: 'PUT',
        carga: '#carga-puntoskay',
        data:{
            view:'rechazar'        
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/consumidor/puntoskay') }}';                                                                            
    })
}
</script>
@endsection