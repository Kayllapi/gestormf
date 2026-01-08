@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Anular Aprobación',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamoaprobacion: Ir Atras'
    ]
])
<form action="javascript:;"
      onsubmit="callback({
          route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamodesembolso/{{ $prestamodesembolso->id }}',
          method: 'PUT',
          data:   {view: 'anularaprobacion'}
      },
      function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamodesembolso') }}';
      },this)">
    <div id="resultado-credito"></div>  
    @include('app.prestamo_creditodetalle',[
      'idtienda'=>$tienda->id,
      'idprestamocredito'=>$prestamodesembolso->id
    ])    
    <div class="mensaje-warning">
        <i class="fa fa-exclamation-circle"></i>
        <b>¿Esta seguro de Anular la Aprobación?</b><br>          
    </div>
    <button type="submit" class="btn mx-btn-post"><i class="fa fa-ban"></i> Anular Aprobación</button>
</form>  

@endsection

@section('subscripts')
<script>
    tab({click:'#tab-resultado'});
</script>
@endsection

