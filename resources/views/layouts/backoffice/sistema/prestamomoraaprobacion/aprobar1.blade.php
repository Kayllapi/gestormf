@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Aprobar Solicitud de Descuento de Mora</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomoraaprobacion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
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
                    documento: '{{$s_prestamo_mora->documento}}'
                }
            },
            function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomoraaprobacion') }}';
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
                  <label>DNI</label>
                  <input type="text" value="{{ $s_prestamo_mora->clienteidentificacion }}" disabled>
                  <label>Cliente</label>
                  <input type="text" value="{{ $s_prestamo_mora->clienteapellidos }}, {{ $s_prestamo_mora->clientenombre }}" disabled>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
                <div class="table-responsive">
                  <table class="table" id="tabla-contenido">
                      <thead class="thead-dark">
                        <tr>
                          <th>Fecha registro</th>
                          <th>Descuento</th>
                          <th>Motivo</th>
                          <th width = '10px'>Estado</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($s_prestamo_moradetalles as $value)
                          <tr id="{{$value->id}}" idmora="{{$value->id}}">
                            <td>{{ date_format(date_create($value->fecharegistro),"d/m/Y h:i A") }}</td>
                            <td>{{ $value->moradescuento }}</td>
                            <td><input type="text" id="motivo{{$value->id}}" value="{{ $value->motivo }}" <?php echo $value->motivo!=''? 'disabled':'' ?> onkeyup="texto_mayucula(this)"></td>
                            <td>
                              @if ($value->idestadomoradetalle == 1)
                                  @if ($value->motivo!='')
                                  <span class="badge badge-pill badge-primary"><i class="fa fa-sync-alt"></i> Solicitando</span>
                                  @else
                                  <span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span>
                                  @endif
                              @elseif ($value->idestadomoradetalle == 2)
                                  <span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aprobado</span>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                  </table>
                </div>
            </div>
            <!--div class="col-sm-6">
                  <label>Foto de sustento</label>
                  <div id="resultado-imagendocumento" style="display: none;"></div>
            </div-->
            <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;"><i class="fa fa-check"></i> Aprobar</button> 
    </form>
</div>
<style>

  #cont-imagendocumento {
      height:293px;
  }
  #resultado-imagendocumento {
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height:293px;
      width:100%;
      background-color: #eae7e7;
      border-radius: 5px;
      border: 1px solid #aaa;
      float: left;
      margin-bottom: 10px;
  }
</style>
@endsection
@section('subscripts')
<script>
  @if($s_prestamo_mora->documento!='')
          mostrar_documento("{{url('/public/backoffice/tienda/'.$tienda->id.'/prestamomora/'.$s_prestamo_mora->documento)}}");
  @endif
  
  function mostrar_documento(archivo){
          $('#cont-imagendocumento').css('display','none');
          $('#resultado-imagendocumento').attr('style','background-image: url('+archivo+')');
  }
</script>
@endsection