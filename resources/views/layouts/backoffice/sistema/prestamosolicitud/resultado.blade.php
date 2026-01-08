 
    @include('app.prestamo_creditodetalle',[
      'idtienda'=>$tienda->id,
      'idprestamocredito'=>$prestamocredito->id
    ])   

<script>
    tab({click:'#tab-resultado'});
</script>