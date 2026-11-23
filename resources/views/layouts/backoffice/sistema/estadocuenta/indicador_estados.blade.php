<script>
  function actualizar_indicador_estados() {
    const indicador = $('#cont_listanegra');
    const estados = [];
    if (indicador.data('observado')) estados.push('Cliente Observado');
    if (indicador.data('castigado')) estados.push('C. Castigado');
    indicador.text(estados.join(' / ')).toggle(estados.length > 0);
  }
</script>
