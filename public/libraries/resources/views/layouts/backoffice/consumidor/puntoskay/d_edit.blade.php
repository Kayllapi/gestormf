@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR PUNTOS</span>
      <a class="btn btn-success" href="{{ url('backoffice/puntoskay') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>

<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/puntoskay/{{ $puntoskay->id }}',
        method: 'PUT',
        data:{
            view:'editar'        
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/puntoskay') }}';                                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <label>Usuario *</label>
              <select id="idusuario">
                  <option value="" selected>-- Seleccionar --</option>
                  @foreach($usuarios as $value)
                  <option value="{{ $value->id }}" <?php echo $value->id==$puntoskay->idusers ? 'selected':'' ?>>{{ $value->email }} - {{ $value->apellidos }}, {{ $value->nombre }}</option>
                  @endforeach
              </select>
              <label>Cantidad *</label>
              <div class="quantity fl-wrap">
                  <div class="quantity-item">
                      <input type="button" value="-" class="minus" onclick="calcularmonto()">
                      <input type="text" id="cantidad" title="Qty" class="qty" min="1" max="100000" step="1" value="{{ $puntoskay->cantidad }}" onkeyup="calcularmonto()" style="padding-left: 0px;">
                      <input type="button" value="+" class="plus" onclick="calcularmonto()">
                  </div>
              </div>
              <label>Total s/. <i class="fa fa-tags"></i></label>
              <input type="text" value="0.00" id="monto" disabled>
            </div>
            <div class="col-md-6">
              <div class="table-responsive">
                  <table class="table" id="tabla-contenido">
                      <thead class="thead-dark">
                        <tr>
                          <th width="10px">#</th>
                          <th>PUNTOS	</th>
                          <th>PRECIO</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $i=1 ?>
                        @foreach($planpuntoskays as $value)
                          <tr>
                            <td>{{ $i }}</td>
                            <td><div style="padding: 8px;">
                              {{ $value->inicio }} - 
                              @if($value->fin>=1000)
                              SIN LIMITE
                              @else
                              {{ $value->fin }}
                              @endif
                              </div></td>
                            <td>S/. {{ $value->precio }}</td>
                          </tr>
                        <?php $i=$i+1 ?>
                        @endforeach
                      </tbody>
                  </table>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios<i class="fa fa-angle-right"></i></button>
        </div>
    </div> 
</form>                           
@endsection
@section('scriptsbackoffice')
<script>
$('#idusuario').niceSelect();
$('#idmotivopuntoskay').niceSelect();
  calcularmonto()
function calcularmonto(){
    var cantidad = parseInt($('#cantidad').val());
    var planpuntoskay = <?php echo utf8_encode($planpuntoskays) ?>;
    var precio = 0;
    $.each(planpuntoskay, function( key, value ) {
        if(value.inicio<=cantidad && value.fin>=cantidad){
            precio = value.precio;
            return false;
        }
    });
  
    $('#monto').val((precio*cantidad).toFixed(2));
}
</script>
@endsection