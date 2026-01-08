@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte Resúmenes Diarios</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionresumendiario') }}" method="GET"> 
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
            <label>Correlativo</label>
                <input class="form-control" type="text"  name="correlativo" id="correlativo" value="{{isset($_GET['correlativo'])?($_GET['correlativo']!=''?$_GET['correlativo']:''):''}}">
            <label>Cliente</label>
              <select id="idcliente" name="idcliente">
                  @if(isset($_GET['idcliente']))
                  @if($_GET['idcliente']!='')
                  <?php $users = DB::table('users')->where('idtienda',$tienda->id)->where('id',$_GET['idcliente'])->first();?>
                  <option value="{{$users->id}}">{{$users->identificacion}} - {{$users->apellidos}}, {{$users->nombre}}</option>
                  @else
                  <option></option>
                  @endif
                  @else
                  <option></option>
                  @endif
              </select>
            
         </div>
         <div class="col-md-6">
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
           <label>Estado</label>
            <select id="idestado" name="idestado">
                <option></option>
                <option value="aceptada">Aceptada</option>
                <option value="observaciones">Observaciones</option>
                <option value="rechazada">Rechazada</option>
                <option value="excepción">Excepción</option>
                <option value="error">No enviado</option>
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
    </div>
</form>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Correlativo</th>
          <th>Fecha de Generación</th>
          <th>Fecha de Resumen</th>
          <th>RUC</th>
          <th>Emisor	</th>
          <th>Comprobante</th>
          <th>Serie-Correlativo</th>
          <th>DNI/RUC</th>
          <th>Cliente</th>
          <th>Sub Total</th>
          <th>IGV</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Responsable</th>
          <th>SUNAT</th>
        </tr>
      </thead>
      <tbody>
      @foreach($facturacionresumendiario as $value)
        <tr>
          <td>{{ str_pad($value->resumen_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ date_format(date_create($value->resumen_fechageneracion), 'd/m/Y h:i:s A') }}</td>
          <td>{{ date_format(date_create($value->resumen_fecharesumen), 'd/m/Y h:i:s A') }}</td>
          <td>{{$value->emisor_ruc}}</td>
          <td>{{$value->emisor_nombrecomercial}}</td>
          <td>{{ $value->tipodocumento=='03'?'BOLETA':($value->tipodocumento=='07'?'NOTA DE CRÉDITO':'---') }}</td>
          <td>{{ $value->serienumero }}</td>
          <td>{{ $value->clientenumero }}</td>
          <td>{{ $value->cliente }}</td>
          <td>{{ $value->operacionesgravadas }}</td>
          <td>{{ $value->montoigv }}</td>
          <td>{{ $value->total }}</td>
          <td>{{ $value->estado==1?'Adicionado':($value->estado==2?'Modificado':($value->estado==3?'Anulado':'---')) }}</td>
          <td>{{ $value->responsablenombre }}</td>
          <td>
            @if($value->respuestaestado=='ACEPTADA')
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aceptada</span></div>
            @elseif($value->respuestaestado=='OBSERVACIONES')
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Observaciones</span></div> 
            @elseif($value->respuestaestado=='RECHAZADA')
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Rechazada</span></div> 
            @elseif($value->respuestaestado=='EXCEPCION')
              <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-sync-alt"></i> Excepción</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> No enviado</span></div>
            @endif
          </td>
        </tr>
       @endforeach
      </tbody>
  </table>
</div>
<style>
  td{
    padding: 7px !important;
  }
</style>
{{ $facturacionresumendiario->links('app.tablepagination', ['results' => $facturacionresumendiario]) }}
@endsection
@section('subscripts')
<script>
   function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionresumendiario')}}?'+
                                'tipo='+tipo+
                                '&idagencia='+($('#idagencia').val()!=null?$('#idagencia').val():'')+
                                '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
                                '&idcliente='+($('#idcliente').val()!=null?$('#idcliente').val():'')+
                                '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
                                '&correlativo='+$('#correlativo').val()+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }
  $("#idcliente").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionboletafactura/showlistarusuario')}}",
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
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionboletafactura/showlistarusuario')}}",
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
  $("#idagencia").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idagencia'])?($_GET['idagencia']!=''?$_GET['idagencia']:'0'):'0'}}).trigger("change");
  $("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");
</script>
@endsection