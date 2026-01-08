@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>INVERTIR EN VISITAS PARA NEGOCIO</span>
      <a class="btn btn-success" href="{{ url('backoffice/kayvisitastiendanegocio') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
@include('app.puntoskay')
<form class="js-validation-signin px-30" 
                          action="javascript:;" 
                          onsubmit="callback({
                            route: 'backoffice/kayvisitastiendanegocio',
                            method: 'POST',
                            data:{
                                view:'registrar'
                            }        
                        },
                        function(resultado){
                            location.href = '{{ url('backoffice/kayvisitastiendanegocio') }}';                                                                            
                        },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div style="background-color: #3498db;padding: 10px;color: #fff;font-size: 20px;margin-bottom: 10px;">
            {{ $configkayvisitastiendanegocio->puntoskay }} KAY = {{ $configkayvisitastiendanegocio->cantidad }} Visitas
          </div>
          <div class="row">
              <div class="col-md-7">
                  <label>Tienda *</label>
                  <select id="idtienda">
                      <option value="">-- Seleccionar --</option>
                      @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-md-5">
                  <label>Invertir Monedas KAY *</label>
                  <div class="quantity fl-wrap">
                      <div class="quantity-item">
                          <input type="button" value="-" class="minus">
                          <input type="text" id="totalpuntoskay" class="qty" min="1" max="100000" step="1" value="1" style="padding-left: 0px;">
                          <input type="button" value="+" class="plus">
                      </div>
                  </div>                    
              </div>
          </div>  
      </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Registrar</button>
        </div>
    </div>
</form>                         
@endsection
@section('scriptsbackoffice')
<script>
$('#idtienda').niceSelect();
</script>
@endsection