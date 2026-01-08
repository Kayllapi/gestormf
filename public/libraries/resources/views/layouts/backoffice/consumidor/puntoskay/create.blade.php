@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Asignar Monedas KAY</span>
      <a class="btn btn-success" href="{{ url('backoffice/consumidor/puntoskay') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>

<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/consumidor/puntoskay',
        method: 'POST',
        data:{
            view:'registrar'        
        }        
    },
    function(resultado){
        location.href = '{{ url('backoffice/consumidor/puntoskay') }}';                                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <label>Usuario *</label>
              <select id="idusuario">
                  <option></option>
                  @foreach($usuarios as $value)
                  <option value="{{ $value->id }}">{{ $value->email }} - {{ $value->apellidos }}, {{ $value->nombre }}</option>
                  @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label>Cantidad *</label>
              <input type="number" id="cantidad" value="1">
            </div>
          </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Asignar</button>
        </div>
    </div>
</form>                           
@endsection
@section('scriptsbackoffice')
<script>
$("#idusuario").select2({
    placeholder: "---  Seleccionar ---"
});
</script>
@endsection