<style>
  #table-lista-credito {
    height: 100%;
    margin-bottom: 0;
  }

  #table-lista-credito tbody > tr {
    height: 1px;
  }

  #table-lista-credito tbody::after {
    content: '';
    display: table-row;
    height: 100%;
  }

  #table-lista-credito tfoot {
    position: sticky;
    bottom: 0;
    z-index: 2;
  }

  #table-lista-credito .fila-total {
    position: static !important;
  }

  #table-lista-credito .fila-total input.form-control {
    padding-top: 2px;
    padding-bottom: 2px;
  }

  #table-lista-credito .fila-total > th {
    background-color: #b7b6b7 !important;
    color: #000 !important;
    font-weight: bold;
  }
</style>
<script>
  $('#table-lista-credito').on('input', '.fila-total input', filtrar_cartera_credito);
  function filtrar_cartera_credito() {
    let tabla = $('#table-lista-credito');
    let totales = tabla.children('tbody').children('.fila-total');
    if (totales.length) {
      let pie = tabla.children('tfoot');
      let busquedas = pie.find('input').map(function() { return this.value; }).get();
      if (busquedas.length) {
        totales.find('input').each(function(indice) { this.value = busquedas[indice] || ''; });
      }
      if (!pie.length) { pie = $('<tfoot>').appendTo(tabla); }
      pie.empty().append(totales);
    }
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
