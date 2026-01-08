@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>CONFIGURACIÓN DE VISITAS PARA NEGOCIO</span>
      <a class="btn btn-success" href="{{ url('backoffice/configkayvisitastienda') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/configkayvisitastienda/{{ $configkayvisitastienda->id }}',
        method: 'PUT',
        data:{
            view:'editar'        
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/configkayvisitastienda') }}';            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
                <label>Titulo * <i class="fa fa-check"></i></label>
                <input type="text" id="titulo" value="{{ $configkayvisitastienda->titulo }}">
                <label>Cantidad<span id="titulotexto"></span> *</label>
                <div class="quantity fl-wrap">
                  <div class="quantity-item">
                      <input type="button" value="-" class="minus">
                      <input type="text" id="cantidad" class="qty" min="1" max="100000" step="1" value="{{ $configkayvisitastienda->cantidad }}" style="padding-left: 0px;">
                      <input type="button" value="+" class="plus">
                  </div>
                </div>
                <label>Cantidad de Monedas KAY *</label>
                <div class="quantity fl-wrap">
                    <div class="quantity-item">
                        <input type="button" value="-" class="minus">
                        <input type="text" id="puntoskay" class="qty" min="1" max="100000" step="1" value="{{ $configkayvisitastienda->puntoskay }}" style="padding-left: 0px;">
                        <input type="button" value="+" class="plus">
                    </div>
                </div>
                <label>Estado *</label>
                <select id="idestado">
                    <option value="1" <?php echo $configkayvisitastienda->idestado==1 ? 'selected' : '' ?>>Activado</option>
                    <option value="2" <?php echo $configkayvisitastienda->idestado==2 ? 'selected' : '' ?>>Desactivado</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Detalle</label>
                <textarea id="detalle" style="height: 200px;">{{ $configkayvisitastienda->detalle }}</textarea>
            </div>
          </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Actualizar</button>
        </div>
    </div> 
</form>                           
@endsection
@section('scriptsbackoffice')
<script>
$('#idestado').niceSelect();
</script>
@endsection