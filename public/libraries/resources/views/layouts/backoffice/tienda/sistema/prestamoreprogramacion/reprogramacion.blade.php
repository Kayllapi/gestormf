<form action="javascript:;" 
      onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamoreprogramacion',
            method: 'POST',
            data:   {
                view: 'registrar',
                idprestamo_credito: $('#idcliente').val()
            }
        },
        function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion') }}';
        }, this)">
<div class="col-sm-6">
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Reprogramar Crédito</span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label>Frecuencia</label>
            <select id="idfrecuencia" disabled>
                <option></option>
                @foreach($frecuencias as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
            </select>
            <label>Fecha de Inicio *</label>
            <input type="date" id="fechainicio" onchange="mostrar_credito_reprogramado()"/>
              <label>Motivo de reprogramación *</label>
              <textarea id="reprogramar_motivo" style="height:85px;"></textarea>
        </div>
        <div class="col-md-6">
              <label>Documento</label>
              <div class="mensaje-warning">
                <i class="fa fa-warning"></i> Debe subir un Documento <b>PDF</b>.
              </div>
              <input type="file" class="upload" id="reprogramar_documento">
        </div>
    </div> 
    <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;">Guardar Reprogramación</button>
</div>
<div class="col-sm-6">
    <div id="cont-credito_reprogramado"></div>
</div>  
</form>

<script>
    tab({click:'#tab-credito'});
  
    $('#idfrecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --',
        minimumResultsForSearch: -1,
    }).val({{ $s_prestamo_credito->idprestamo_frecuencia }}).trigger('change');
  
    mostrar_credito_reprogramado();
  
    function mostrar_credito_reprogramado(){
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion/'.$s_prestamo_credito->id.'/edit') }}",
            type: 'GET',
            data: {
                view: 'credito_reprogramado',
                fechainicio: $('#fechainicio').val()
            },
            beforeSend: function (data) {
                load('#cont-credito_reprogramado');
            },
            success: function (res) {
                $('#cont-credito_reprogramado').html(res);
            }
        });
    }
</script>