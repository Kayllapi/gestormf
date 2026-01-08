@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Solicitar Descuento de Mora</span>
      <a class="btn btn-success" href="{{ redirect()->getUrlGenerator()->previous() }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-mora">

    <form action="javascript:;" 
          onsubmit="callback({
                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamomorasolicitud/{{ $s_prestamo_mora->id }}',
                method: 'PUT',
                carga:  '#carga-mora',
                data:   {
                    view: 'confirmar',
                    moras: selectmoras()
                }
            },
            function(resultado){
              location.href = '{{ redirect()->getUrlGenerator()->previous() }}';
            }, this)">
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
                            <td><input type="text" id="motivo{{$value->id}}" value="{{ $value->motivo }}" <?php echo $value->motivo!=''? 'disabled':'' ?> onkeyup="texto_mayucula(this)"></td>
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

          <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;"><i class="fa fa-check"></i> Solicitar</button>  
 
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
          data = data+'/&/'+idmora+'/,/'+motivo;
      });
      return data;
  } 
</script>
@endsection