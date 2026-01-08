@extends('layouts.buscador.master')
@section('cuerpotienda')
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
                                  <form action="javascript:;" onsubmit="callback({
                                            route: '{{$tienda->link}}',
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
                                                url:'{{url('/')}}/{{$tienda->link}}/sunat/comprobante/showmostrarcomprobante',
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
                                              <input type="number"  name="documento_ruc" id="documento_ruc" value="{{ old('documento_ruc') }}">
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
                                              <select class="select" name="tipo_comprobante" id="tipo_comprobante" value="{{ old('tipo_comprobante') }}">
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
                                              <input type="text"  name="facturador_serie" id="facturador_serie" value="{{ old('facturador_serie') }}"/>
                                              @if ($errors->has('facturador_serie'))
                                                  <span class="invalid-feedback" style="color: red; text-align: left; font-weight: bold;" role="alert">
                                                      <strong>{{ $errors->first('facturador_serie') }}</strong>
                                                  </span> 
                                              @endif
                                           </div>
                                           <div class="col-md-6">
                                              <label>Correlativo *</label> 
                                              <input type="text"  name="facturador_correlativo" id="facturador_correlativo" value="{{ old('facturador_correlativo') }}"/>
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
                                              <input type="date"  name="facturador_fechaemision" id="facturador_fechaemision" placeholder="Fecha de emisión" value="{{ old('facturador_fechaemision') }}"/>
                                               @if ($errors->has('facturador_fechaemision'))
                                                  <span class="invalid-feedback" style="color: red; text-align: left; font-weight: bold;" role="alert">
                                                      <strong>{{ $errors->first('facturador_fechaemision') }}</strong>
                                                  </span> 
                                               @endif
                                           </div>
                                        </div>
                                        <button class="btn  big-btn  color-bg flat-btn" type="submit" style="width:100%;">Consultar</button>
                                  </form>
                         </div>
                         <div class="col-md-3"></div>
                        </div>
                    </div>
                </div>
                </div>
                <div id="btnnuevaconsulta" style="display:none;">
                    <a class="btn  big-btn  color-bg flat-btn" href="javascript:;" onclick="nuevaconsulta()" style="background: #2f3b59;margin-bottom: 5px;float: left;width: 100%;">Nueva Consulta</a></a>
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
});
function nuevaconsulta(){
    $('#carga-formconsultasulat').css('display','block');
    $('#btnnuevaconsulta').css('display','none');
  
    removecarga({'input':'#carga-formconsultasulat'});
    $('#documento_ruc').val('');
    $("#tipo_comprobante").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    }).val(null).trigger("change");
    $('#facturador_serie').val('');
    $('#facturador_correlativo').val('');
    $('#facturador_fechaemision').val('');
}
</script>
@endsection
