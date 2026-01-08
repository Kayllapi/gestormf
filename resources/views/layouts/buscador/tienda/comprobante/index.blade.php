@extends('layouts.buscador.tienda.master')
@section('cuerpotienda')
<?php 
$identificacion = !is_null($data['identificacion']) ? $data['identificacion'] : old('documento_ruc');
$tipo_comprobante = !is_null($data['tipocomprobante']) ? $data['tipocomprobante'] : old('tipo_comprobante');
$serie = !is_null($data['serie']) ? $data['serie'] : old('facturador_serie');
$correlativo = !is_null($data['correlativo']) ? $data['correlativo'] : old('facturador_correlativo');
$fechaemision = !is_null($data['fechaemision']) ? $data['fechaemision'] : old('facturador_fechaemision');
?>
<div class="row">
    <div class="col-md-12">
        <div class="list-single-main-wrapper">
            <div class="breadcrumbs gradient-bg fl-wrap">
              <span>Consulta de Comprobante</span>
            </div>
            <div class="list-single-main-item fl-wrap">
                <div style="float: left;width: 100%;">
                <div id="carga-formconsultasulat">
                <div class="profile-edit-container">
                    <div class="custom-form">
                        <div class="row">
                         <div class="col-md-3"></div>
                         <div class="col-md-6">
                                  <form action="javascript:;" id="form-consultar" onsubmit="callback({
                                            route: '{{$tienda->link}}/pagina/comprobante',
                                            method: 'POST',
                                            carga: '#carga-formconsultasulat',
                                            data:{
                                                view: 'consultasunat'
                                            }
                                        },
                                        function(resultado){
                                            load('#resultado-comprobante');
                                            $('#btn_contenido-consulta').css('display', 'block'); 
                                            $('#carga-formconsultasulat').css('display', 'none');
                                            $('#cont-formconsultasulat').css('display','none');
                                            $('#btnnuevaconsulta').css('display','block');

                                            $.ajax({
                                                url:'{{url('/')}}/{{$tienda->link}}/pagina/comprobante/showmostrarcomprobante',
                                                type:'GET',
                                                data: {
                                                    tipo_comprobante : resultado['tipo_comprobante'],
                                                    idcomprobante : resultado['idcomprobante'],
                                                    idtienda : resultado['idtienda'],
                                                },
                                                success: function (respuesta){
                                                     $('#resultado-comprobante').html(respuesta);
                                                }
                                            })
                                        },this)">
                                        <label>Número de RUC/DNI *</label>
                                        <div class="row">
                                           <div class="col-md-12">
                                              <input type="number"  name="documento_ruc" id="documento_ruc" value="{{ $identificacion }}">
                                              @if($errors->has('documento_ruc'))
                                                  <span class="invalid-feedback" style="color: red; text-align: left; font-weight: bold;" role="alert" >
                                                      <strong>{{ $errors->first('documento_ruc') }}</strong>
                                                  </span> 
                                              @endif
                                           </div>
                                        </div>
                                        <label>Tipo de comprobante *</label>
                                        <div class="row">
                                           <div class="col-md-12">
                                              <select class="select" name="tipo_comprobante" id="tipo_comprobante" value="{{ $tipo_comprobante }}">
                                                 <option></option>
                                                 <option value="01">Factura</option>
                                                 <option value="03">Boleta</option>
                                                 <option value="07">Nota de Credito</option>
                                                 <option value="08">Nota de Debito</option>
                                                 <option value="09">Guia Remisión</option>
                                              </select>
                                              @if ($errors->has('tipo_comprobante'))
                                                 <span class="invalid-feedback" style="color: red; text-align: left; font-weight: bold;" role="alert">
                                                      <strong>{{ $errors->first('tipo_comprobante') }}</strong>
                                                  </span> 
                                              @endif
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="col-md-6">
                                              <label>Serie *</label> 
                                              <input type="text"  name="facturador_serie" id="facturador_serie" value="{{ $serie }}"/>
                                              @if ($errors->has('facturador_serie'))
                                                  <span class="invalid-feedback" style="color: red; text-align: left; font-weight: bold;" role="alert">
                                                      <strong>{{ $errors->first('facturador_serie') }}</strong>
                                                  </span> 
                                              @endif
                                           </div>
                                           <div class="col-md-6">
                                              <label>Correlativo *</label> 
                                              <input type="text"  name="facturador_correlativo" id="facturador_correlativo" value="{{ $correlativo }}"/>
                                              @if ($errors->has('facturador_correlativo'))
                                                  <span class="invalid-feedback" style="color: red; text-align: left; font-weight: bold;" role="alert">
                                                      <strong>{{ $errors->first('facturador_correlativo') }}</strong>
                                                  </span> 
                                              @endif
                                           </div>
                                        </div>
                                        <label>Fecha de emisión *</label>
                                        <div class="row">
                                           <div class="col-md-12">
                                              <input type="date"  name="facturador_fechaemision" id="facturador_fechaemision" placeholder="Fecha de emisión" value="{{ $fechaemision }}"/>
                                               @if ($errors->has('facturador_fechaemision'))
                                                  <span class="invalid-feedback" style="color: red; text-align: left; font-weight: bold;" role="alert">
                                                      <strong>{{ $errors->first('facturador_fechaemision') }}</strong>
                                                  </span> 
                                               @endif
                                           </div>
                                        </div>
                                        <button class="btn  big-btn  color-bg flat-btn" onclick="consultar()" id="btn-consultar" type="submit" style="width:100%;">Consultar</button>
                                  </form>
                         </div>
                         <div class="col-md-3"></div>
                        </div>
                    </div>
                </div>
                </div>
                <div id="btnnuevaconsulta" style="display:none;">
                    <a class="btn  big-btn  color-bg flat-btn" href="javascript:;" onclick="nuevaconsulta()" style="background: #2f3b59;margin-bottom: 5px;float: left;width: 100%;">Realizar Nueva Consulta</a></a>
                    <div id="resultado-comprobante"></div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('tiendascripts')
<script>
$("#tipo_comprobante").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val("{{ $tipo_comprobante }}").trigger('cahnbge');
function nuevaconsulta(){
    $('#carga-formconsultasulat').css('display','block');
    $('#btnnuevaconsulta').css('display','none');
  
    removecarga({'input':'#carga-formconsultasulat'});
    $('#documento_ruc').val('');
    $("#tipo_comprobante").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    }).val(null).trigger('change');
    $('#facturador_serie').val('');
    $('#facturador_correlativo').val('');
    $('#facturador_fechaemision').val('');
  
    history.replaceState(null, null, window.location.pathname);
}
function consultar() {
  $('#form-consultar').submit();
}
@if ($identificacion != '' && $tipo_comprobante != '' && $serie != '' && $correlativo != '' && $fechaemision != '')
  consultar();
@endif
</script>
@endsection