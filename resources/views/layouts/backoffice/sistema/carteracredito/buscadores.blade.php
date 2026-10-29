<style>
  #table-lista-credito .fila-total > th {
    background-color: #b7b6b7 !important;
    color: #000 !important;
    font-weight: bold;
  }
</style>
<script>
  $('#table-lista-credito').on('input', '.fila-total input', filtrar_cartera_credito);
  function filtrar_cartera_credito() {
    let filtros = $('#table-lista-credito .fila-total input').map(function() {
      return {columna: Number(this.dataset.columna), texto: this.value.trim().toLowerCase()};
    }).get();
    $('#table-lista-credito tbody tr[idcredito]').each(function() {
      let celdas = $(this).children('td');
      let mostrar = filtros.every(function(filtro) {
        return celdas.eq(filtro.columna).text().toLowerCase().includes(filtro.texto);
      });
      $(this).toggle(mostrar);
    });
  }

</script>
