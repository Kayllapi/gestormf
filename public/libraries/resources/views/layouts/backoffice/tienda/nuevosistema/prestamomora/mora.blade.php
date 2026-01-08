<form action="javascript:;" 
      onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamomora',
            method: 'POST',
            carga:  '#carga-mora',
            data:   {
                view: 'registrar-mora',
                idprestamo_credito: $('#idcliente').val()
            }
        },
        function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora') }}';
        }, this)">
  <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-6">
              <label>Total de Moras</label>
              <input type="number" id="cuotas_total_mora" value="0.00" min="0" step="0.01" disabled>
              <label>Mora Pagada</label>
              <input type="number" id="mora_pagadas" value="0.00" min="0" step="0.01" disabled>
              <label>Mora a Pagar</label>
              <input type="number" id="total_moraapagar" value="0.00" min="0" step="0.01" disabled>
              <label>Mora a Descontar *</label>
              <input type="number" id="moradescuento" placeholder="0.00" min="0" step="0.01" disabled>
              <label>Mora Pendiente</label>
              <input type="number" id="mora_pendiente" value="0.00" min="0" step="0.01" disabled>
        </div>
        <div class="col-sm-6">
              <label>Motivo de descuento *</label>
              <textarea id="moradescuento_detalle" style="height:85px;" disabled></textarea>
              <label>Documento</label>
              <div class="mensaje-warning">
                <i class="fa fa-warning"></i> Debe subir un Documento <b>PDF</b>.
              </div>
              <input type="file" class="upload" id="documento">
        </div>
      </div>
      <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;">Registrar Mora</button>
  </div>  
</form>

<div class="col-sm-6">
    <div id="cont-cobranzapendiente"></div>
</div>

<!-- Detalle  -->
<script>
    mostrar_cuotapendiente();
  
    let time_moradescuento;
    document.getElementById("moradescuento").addEventListener('keydown', () => {
      clearTimeout(time_moradescuento)
      time_moradescuento = setTimeout(() => {
        mostrar_cuotapendiente();
        clearTimeout(time_moradescuento)
      },700)
    });
  
    function mostrar_cuotapendiente(){
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora/'.$s_prestamo_credito->id.'/edit') }}",
            type: 'GET',
            data: {
                view: 'cuotapendiente',
                moradescuento: $('#moradescuento').val()
            },
            beforeSend: function (data) {
                load('#cont-cobranzapendiente');
            },
            success: function (res) {
                $('#cont-cobranzapendiente').html(res);
            }
        });
    }
</script>