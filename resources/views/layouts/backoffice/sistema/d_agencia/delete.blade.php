@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Eliminar Empresa',
    'botones'=>[
        'atras:/'.$tienda->id.'/agencia: Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/agencia/{{ $s_agencia->id }}',
        method: 'DELETE',
        data:{
            view: 'eliminar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/agencia') }}';                                                                            
    },this)">
          <div class="tabs-container" id="tab-empresa">
              <ul class="tabs-menu">
                  <li class="current"><a href="#tab-empresa-0" id="tab-pedido">General</a></li>
                  <li><a href="#tab-empresa-1" id="tab-entrega">Facturación</a></li>
              </ul>
              <div class="tab">
                  <div id="tab-empresa-0" class="tab-content" style="display: block;">
                      <div class="row">
                         <div class="col-md-6">
                            <label>Ruc</label>
                            <input type="text" id="ruc" value="{{ $s_agencia->ruc }}" disabled/>
                            <label>Nombre Comercial</label>
                            <input type="text" id="nombrecomercial" value="{{ $s_agencia->nombrecomercial }}" disabled/>
                            <label>Razón Social</label>
                            <input type="text" id="razonsocial" value="{{ $s_agencia->razonsocial }}" disabled/>
                            <label>Ubicación (Ubigeo)</label>
                            <select id="idubigeo" disabled>
                                <option value="{{$s_agencia->idubigeo}}">{{$s_agencia->ubigeonombre}}</option>
                            </select>
                            <label>Dirección</label>
                            <input type="text" id="direccion" value="{{ $s_agencia->direccion }}" disabled/>
                         </div>
                         <div class="col-md-6">
                            <label>Logo</label>
                            <div class="fuzone" id="cont-fileupload" style="height: 177px;">
                                <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                                <div id="resultado-logo"></div>
                            </div>
                          </div>
                       </div>
                  </div>
                  <div id="tab-empresa-1" class="tab-content" style="display: none;">
                        <div class="row">
                          <div class="col-md-6">
                            <label>Estado</label>
                            <select id="idestadofacturacion" disabled>
                                <option value="1">Habilitado</option>
                                <option value="2">Desabilitado</option>
                            </select>
                            <div id="cont-facturacion_estado1">
                            <label>Serie </label>
                            <input type="number" value="{{ $s_agencia->facturacion_serie }}" id="facturacion_serie" min="0" disabled>
                            <label>Correlativo Inicial</label>
                            <input type="number" value="{{ $s_agencia->facturacion_correlativoinicial }}" id="facturacion_correlativoinicial" min="0" disabled>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div id="cont-facturacion_estado2">
                            <label>Usuario (SUNAT)</label>
                            <input type="text" value="{{ $s_agencia->facturacion_usuario }}" id="facturacion_usuario" disabled>
                            <label>Clave (SUNAT)</label>
                            <input type="text" value="{{ $s_agencia->facturacion_clave }}" id="facturacion_clave" disabled>
                            <label>Certificado (SUNAT) </label>
                            <input type="file" class="upload" id="facturacion_certificado">
                            @if($s_agencia->facturacion_certificado!='')
                                <div class="mensaje-info">
                                  <i class="fa fa-exclamation-circle" style="font-size: 30px;margin-bottom: 5px;"></i><br>
                                  <b>¿Dese cambiar el certificado?</b><br>
                                  Al cambiar el certificado, se eliminar el certificado actual.<br>
                                  <b>Descargar: <a href="{{ url('public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/certificado/'.$s_agencia->facturacion_certificado) }}" target="_blank">{{$s_agencia->facturacion_certificado}}</a></b> 
                                </div>
                            @endif
                            </div>
                          </div>
                       </div>
                  </div>
              </div>
          </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro Eliminar?</b>
    </div>
    <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>   
@endsection
@section('subscripts')
<script>  
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload",
  result:"#resultado-logo",
  ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
  image: "{{ $s_agencia->logo }}"
});

tab({click:'#tab-empresa'});

  
$("#idubigeo").select2({
    @include('app.select2_ubigeo')
});
  
$("#idestadofacturacion").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-facturacion_estado1').css('display','block');
    $('#cont-facturacion_estado2').css('display','block');
    if(e.currentTarget.value == 2) {
        $('#cont-facturacion_estado1').css('display','none');
        $('#cont-facturacion_estado2').css('display','none');
    }
}).val({{$s_agencia->idestadofacturacion}}).trigger("change");
</script>
@endsection