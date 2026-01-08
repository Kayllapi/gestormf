@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRAR ACCESO</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/usuario',
        method: 'POST',
        data:{
            view: 'registrar',
            idtienda: '{{ $tienda->id }}'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuario') }}';                                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Usuario *</label>
                <select id="idtipopersona" onchange="select_tipopersona()">
                    @foreach($tipopersonas as $value)
                    <option value="{{ $value->id }}" <?php echo $value->id==1 ? 'selected' : '' ?>>{{ $value->nombre }}</option>
                    @endforeach
                </select>
                
             </div>
             <div class="col-md-6">
                <label>Correo Electrónico<i class="fa fa-envelope"></i>  </label>
                <input type="text" id="correo"/>
                <label>Ubicación (Ubigeo)</label>
                <select id="idubigeo">
                    <option value="0" selected>-- Seleccionar --</option>
                    @foreach($ubigeos as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Dirección<i class="fa fa-map-marker"></i>  </label>
                <input type="text" id="direccion"/>
                <label>Estado *</label>
                <select id="idestado">
                    <option value="1" selected>Activado</option>
                    <option value="2">Desactivado</option>
                </select>
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
<script>
$('#idtipopersona').niceSelect();
$('#idubigeo').niceSelect();
$('#idestado').niceSelect();
select_tipopersona();
function select_tipopersona(){
    var idtipopersona = $('#idtipopersona option:selected').val();
    $('#cont-juridica').css('display','none');
    $('#cont-natural').css('display','none');
    if(idtipopersona==1){
        $('#cont-natural').css('display','block');
    }else{
        $('#cont-juridica').css('display','block');
    }
}
</script>
@endsection