 
    @include('app.prestamo_ahorrodetalle',[
      'idtienda'=>$tienda->id,
      'idprestamoahorro'=>$prestamoahorro->id
    ])   

<script>
    tab({click:'#tab-resultado'});
</script>