@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR DOMINIO PERSONALIZADO</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/{{ $tienda->id }}',
        method: 'PUT',
        data: {
           view : 'editdominiopersonalizado'
        }
    },
    function(resultado){
        //location.href = '{{ url('backoffice/tienda') }}';      
        location.reload();
    },this)" enctype="multipart/form-data">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <label>Configurar DNS de Dominio</label>
              <table class="table table-bordered">
                  <thead class="thead-dark">
                      <tr>
                          <th>Tipo</th>
                          <th>Nombre</th>
                          <th>Valor</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                          <td>
                            <input type="text" value="A" readonly>
                          </td>
                          <td>
                            <input type="text" value="@" readonly>
                          </td>
                          <td>
                            <input type="text" value="75.102.22.6" readonly>
                          </td>
                      </tr>
                  </tbody>
              </table>
              <label>Dominio Personalizado</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon3">http://</span>
                </div>
                <input type="text" value="{{ $tienda->dominio_personalizado }}" id="dominio_personalizado">
              </div>
              <label>Privacidad *</label>
              <select id="idestadoprivacidad" >
                  <option></option>
                  <option value="1">Público</option>
                  <option value="2">Privado</option>
              </select>
            </div>
            <div class="col-md-6">
              <label>Color Web</label>
              <input type="color" value="{{ $tienda->ecommerce_color }}" id="ecommerce_color">
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
@section('scriptssistema')
<script>
$("#idestadoprivacidad").select2({
    placeholder: "--- Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{ $tienda->idestadoprivacidad }}).trigger("change");
</script>
@endsection