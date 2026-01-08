@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte Guías de Remisión</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionguiaremision') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
          <div class="col-md-6">
            <label>Empresa</label>
             <select id="idagencia" name="idagencia">
                <option></option>
                @foreach($agencia as $value)
                <option value="{{ $value->id }}"?>{{ $value->nombrecomercial }}</option>
                @endforeach
              </select>
            <div class="row">
            
          <div class="col-md-6">
                <label>Serie</label>
                  <input class="form-control" type="text"  name="serie" id="serie" value="{{isset($_GET['serie'])?($_GET['serie']!=''?$_GET['serie']:''):''}}">
          </div>
                  <div class="col-md-6">
                <label>Correlativo</label>
                  <input class="form-control" type="text"  name="correlativo" id="correlativo" value="{{isset($_GET['correlativo'])?($_GET['correlativo']!=''?$_GET['correlativo']:''):''}}">
          </div>
            </div>
            <label>Cliente Destinatario</label>
              <select name="destinatario" id="destinatario" >
                 <option></option>
              </select>
            
                
             
          </div>
          <div class="col-md-6">
            <label>Transportista</label>
              <select id="idtransportista" name="idtransportista">
                  @if(isset($_GET['idtransportista']))
                  @if($_GET['idtransportista']!='')
                  <?php $users = DB::table('users')->where('idtienda',$tienda->id)->where('id',$_GET['idtransportista'])->first();?>
                  <option value="{{$users->id}}">{{$users->identificacion}} - {{$users->apellidos}}, {{$users->nombre}}</option>
                  @else
                  <option></option>
                  @endif
                  @else
                  <option></option>
                  @endif
            </select>
             <label>Responsable</label>
              <select id="idresponsable" name="idresponsable">
                  @if(isset($_GET['idresponsable']))
                  @if($_GET['idresponsable']!='')
                  <?php $users = DB::table('users')->where('idtienda',$tienda->id)->where('id',$_GET['idresponsable'])->first();?>
                  <option value="{{$users->id}}">{{$users->identificacion}} - {{$users->apellidos}}, {{$users->nombre}}</option>
                  @else
                  <option></option>
                  @endif
                  @else
                  <option></option>
                  @endif
                </select>
            <div class="row">
            <div class="col-md-6">
              <label>Fecha inicio</label>
                 <input type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
              </div>
              <div class="col-md-6">
                <label>Fecha fin</label>
                  <input type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
              </div>
            </div>
         </div>
       </div>
      <div class="col-md-6">
          <div class="row">
            <div class="col-md-6">
                <a href="javascript:;" onclick="reporte('reporte')" class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
            </div>
            <div class="col-md-6">
                  <a href="javascript:;" onclick="reporte('excel')"class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-file-excel"></i>  Exportar Excel</a>
            </div>
          </div>
        </div>
    </div>
</form>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
                <th>Serie</th>
                <th>Correlativo</th>
                <th>Fecha de Emisión</th>
                <th>RUC/DNI</th>
                <th>Remitente</th>
                <th>RUC</th>
                <th>Destinatario</th>
                <th>Motivo</th>
                <th>Traslado</th>
                <th>RUC/DNI</th>
                <th>Transportista</th>
                <th>Responsable</th>
        </tr>
      </thead>
      <tbody>
        @foreach($facturacionguiaremision as $value)
          <tr>
            <td>{{ $value->despacho_serie }}</td>
                    <td>{{ str_pad($value->despacho_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>{{ date_format(date_create($value->despacho_fechaemision), 'd/m/Y h:i:s A') }}</td>
                    <td>{{ $value->emisor_ruc }}</td>
                    <td>{{ $value->emisor_nombrecomercial }}</td>
                    <td>{{ $value->despacho_destinatario_numerodocumento }}</td>
                    <td>{{ $value->despacho_destinatario_razonsocial }}</td>
                    <td>{{ $value->motivotrasladonombre}}</td>
                    <td>{{ $value->envio_fechatraslado }}</td>
                    <td>{{ $value->transporte_choferdocumento }}</td>
                    <td>{{ $value->transportista }}</td>
                    <td>{{ $value->responsablenombre}}</td>
            
           </tr>
         @endforeach
      </tbody>
  </table>
</div>
<style>
  td{
    padding: 10px !important;
  }
</style>
{{ $facturacionguiaremision->links('app.tablepagination', ['results' => $facturacionguiaremision]) }}
@endsection
@section('subscripts')
<script>
  function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionguiaremision')}}?'+
                                'tipo='+tipo+
                                '&idagencia='+($('#idagencia').val()!=null?$('#idagencia').val():'')+
                                '&idtransportista='+($('#idtransportista').val()!=null?$('#idtransportista').val():'')+
                                '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
                                '&destinatario='+($('#destinatario').val()!=null?$('#destinatario').val():'')+
                                '&correlativo='+$('#correlativo').val()+
                                '&serie='+$('#serie').val()+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }
  $("#idagencia").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
  }).val({{isset($_GET['idagencia'])?($_GET['idagencia']!=''?$_GET['idagencia']:'0'):'0'}}).trigger("change");
  
  $('#destinatario').select2({
  ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionguiaremision/showlistarusuario')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2,
    allowClear: true
    });
  
    $("#idtransportista").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionguiaremision/showlistarusuario')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2,
    allowClear: true
  });
   $("#idresponsable").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionguiaremision/showlistarusuario')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2,
    allowClear: true
  });
  
</script>
@endsection