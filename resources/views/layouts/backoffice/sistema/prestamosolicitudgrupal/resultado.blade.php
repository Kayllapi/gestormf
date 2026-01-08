 
    @include('app.prestamo_creditogrupaldetalle',[
      'idtienda'=>$tienda->id,
      'idprestamocreditogrupal'=>$prestamocreditogrupal->id
    ])   

<script>
    tab({click:'#tab-resultado'});
</script>