@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Aprobar Solicitud de Descuento de Mora</span>
      <a class="btn btn-success" href="{{ redirect()->getUrlGenerator()->previous() }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-mora">
    <form action="javascript:;" 
          onsubmit="callback({
                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamomoraaprobacion/{{ $s_prestamo_mora->id }}',
                method: 'PUT',
                carga:  '#carga-mora',
                data:   {
                    view: 'aprobar',
                    moras: selectmoras()
                }
            },
            function(resultado){
              location.href = '{{ redirect()->getUrlGenerator()->previous() }}';
            }, this)">
            <div class="col-sm-12">
              <div class="row">
                <div class="col-sm-6">
                  <label>Código de Crédito</label>
                  <input type="text" value="{{ str_pad($s_prestamo_mora->creditocodigo, 8, "0", STR_PAD_LEFT) }}" disabled>
                  <label>Código de Mora</label>
                  <input type="text" value="{{ str_pad($s_prestamo_mora->codigo, 8, "0", STR_PAD_LEFT) }}" disabled>
                </div>
                <div class="col-sm-6">
                  <label>Asesor</label>
                  <input type="text" value="{{ $s_prestamo_mora->asesorapellidos }}, {{ $s_prestamo_mora->asesornombre }}" disabled>
                  <label>Cliente</label>
                  <input type="text" value="{{ $s_prestamo_mora->clienteidentificacion }} - {{ $s_prestamo_mora->clienteapellidos }}, {{ $s_prestamo_mora->clientenombre }}" disabled>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
                <div class="table-responsive">
                  <table class="table" id="tabla-contenido">
                      <thead class="thead-dark">
                        <tr>
                          <th>Fecha registro</th>
                          <th width="100px">Solicitado</th>
                          <th width="100px">Aprobado</th>
                          <th width="100px">Pendiente</th>
                          <th>Motivo</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $pend = 0;
                        $total = 0;
                        $totaldescontar = 0;
                        $totaldescuento = 0;
                        ?>
                        @foreach ($s_prestamo_moradetalles as $value)
                          <tr id="{{$value->id}}" idmora="{{$value->id}}">
                            <td>{{ date_format(date_create($value->fecharegistro),"d/m/Y h:i A") }}</td>
                            <td><input type="number" id="morapagar{{$value->id}}" value="{{ $value->morapagar }}" disabled></td>
                            <td><input type="number" id="descontar{{$value->id}}" value="{{ $value->moradescontar }}" value="0.00" disabled></td>
                            <td><input type="number" id="descuento{{$value->id}}" value="{{ $value->moradescuento }}" disabled></td>
                            <td><input type="text" id="motivo{{$value->id}}" value="{{ $value->motivo }}" disabled></td>
                          </tr>
                        <?php 
                        $total = $total+$value->morapagar;
                        $totaldescontar = $totaldescontar+$value->moradescontar;
                        $totaldescuento = $totaldescuento+$value->moradescuento;
                        ?>
                        @endforeach
                      </tbody>
                  </table>
                </div>
            </div>
                <div class="col-sm-4">
                </div>
                <div class="col-sm-4">
                  <label>Total Solicitado</label>
                  <input type="number" id="totalapagar" value="{{ number_format($total, 2, '.', '') }}" disabled>
                  <label>Total Aprobado</label>
                  <input type="number" value="{{ number_format($totaldescontar, 2, '.', '') }}" id="totaladescontar" onclick="descontar()" onkeyup="descontar()" step="0.01" min="0">
                  <label>Total Pendiente</label>
                  <input type="number" id="totaldescuento"  value="{{ number_format($totaldescuento, 2, '.', '') }}" disabled>
                </div>
            <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;"><i class="fa fa-check"></i> Aprobar</button> 
    </form>
</div>
@endsection
@section('subscripts')
<script>
  function selectmoras(){
      var data = '';
      $("#tabla-contenido > tbody > tr").each(function() {
          var num = $(this).attr('id');        
          var idmora = $(this).attr('idmora');
          var motivo = $("#motivo"+num).val();
          var descontar = $("#descontar"+num).val();
          var descuento = $("#descuento"+num).val();
          var morapagar = $("#morapagar"+num).val();
          data = data+'/&/'+idmora+'/,/'+motivo+'/,/'+descontar+'/,/'+descuento+'/,/'+morapagar;
      });
      return data;
  } 
  function descontar(){
      var totalapagar = $("#totalapagar").val();
      var totaladescontar = parseFloat($("#totaladescontar").val()!=''?$("#totaladescontar").val():0);
      var totaldescuento = parseFloat(totalapagar)-parseFloat(totaladescontar);
      $("#totaldescuento").val(totaldescuento.toFixed(2));
    
      
      $("#tabla-contenido > tbody > tr").each(function() {
          var num = $(this).attr('id');        
          var morapagar = parseFloat($("#morapagar"+num).val());
          $("#descontar"+num).val('0.00');
          $("#descuento"+num).val('0.00');
          if(totaladescontar>=morapagar){
              $("#descontar"+num).val(morapagar.toFixed(2));
              $("#descuento"+num).val('0.00');
              totaladescontar = totaladescontar-morapagar;
          }else if(totaladescontar<morapagar && totaladescontar>=0){
              $("#descontar"+num).val(totaladescontar.toFixed(2));
              $("#descuento"+num).val((morapagar-totaladescontar).toFixed(2));
              totaladescontar = 0;
          }
      });
  } 
</script>
@endsection