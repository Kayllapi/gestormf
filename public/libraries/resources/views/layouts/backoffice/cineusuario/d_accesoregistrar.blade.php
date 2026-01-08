@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Ampliar Acceso / {{ $usuario->nombre }}</span>
      <a class="btn btn-success" href="{{ url('backoffice/cineusuario/'.$usuario->id.'/edit?view=acceso') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
                  route: 'backoffice/cineusuario/{{$usuario->id}}',
                  method: 'PUT',
                  data: {
                      view : 'accesoregistrar'
                  }
              },
              function(resultado){
                if (resultado.resultado == 'CORRECTO') {
                  location.href = '{{ url('backoffice/cineusuario/'.$usuario->id.'/edit?view=acceso') }}';                           
                }
              },this)">
      <div class="profile-edit-container">
        <div class="custom-form">
                  <div class="row">
                    <div class="col-md-3">
                      @if($userscine!='')
                        <label>Ultima Fecha</label>
                        <input type="date" value="{{$userscine->fechafin}}" id="ultimafecha" disabled/>
                      @else
                        <label>Fecha de Actual</label>
                        <input type="date" value="{{Carbon\Carbon::now()->format('Y-m-d')}}" id="ultimafecha" disabled/>
                      @endif
                    </div>
                    <div class="col-md-3">
                    <label>Días de regalo *</label>
                    <input type="number" value="0" id="diasprueba" onkeyup="calcularfechainicio()"/>
                    </div>
                    <div id="cont-diasprueba" style="display:none;">
                    <div class="col-md-3">
                        <label>Fecha de inicio</label>
                        <input type="date" value="" id="fechainicio" disabled/>
                    </div>
                    <div class="col-md-3">
                        <label>Fecha fin</label>
                        <input type="date" value="" id="fechafin" disabled/>
                    </div>
                    </div>
                  </div>
        </div>
    </div>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%">Ampliar Acceso</button>
    </div>
</form>
@endsection
@section('scriptsbackoffice')
<script>
calcularfechainicio();  
function calcularfechainicio(){
  var fechaactual = new Date($('#ultimafecha').val());
  var dias = parseInt($('#diasprueba').val())+1;
  fechaactual.setDate(fechaactual.getDate() + dias);
  var year = fechaactual.getFullYear();
  var month = fechaactual.getMonth()+1;
  var day = fechaactual.getDate();
  fechainicio = year+'-'+(month<10?'0'+month:month)+'-'+(day<10?'0'+day:day);
  $('#fechainicio').val(fechainicio);
  
  //fecha fin
  fechaactual.setMonth(fechaactual.getMonth() + 1);
  var year1 = fechaactual.getFullYear();
  var month1 = fechaactual.getMonth()+1;
  var day1 = fechaactual.getDate();
  fechafin = year1+'-'+(month1<10?'0'+month1:month1)+'-'+(day1<10?'0'+day1:day1);
  $('#fechafin').val(fechafin);
  
  $('#cont-diasprueba').css('display','none');
  if(dias>=0){
      $('#cont-diasprueba').css('display','block');
  }
}
</script>
@endsection