<form action="javascript:;" 
      onsubmit="callback({
                            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
                            method: 'POST',
                            data:   {
                              view: 'registrar-sustento',
                              idprestamo_credito: {{ $prestamocredito->id }}
                            }
                          },
                          function(resultado){
                            sustento_index();
                          },this)">
  
    <div class="row">
                <div class="col-sm-6">
                  <label>Calificación Crediticio</label>
                  <select id="idcalificacion">
                      <option></option>
                      @foreach($calificaciones as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                  </select>
                  <label>Experiencia en Créditos Diarios/Semanales</label>
                  <select id="idexperienciacredito">
                      <option></option>
                      <option value="1">MAYOR A UNA TARJETA</option>
                      <option value="2">IGUAL A 1 TARJETA</option>
                      <option value="3">NINGUNA TARJETA</option>
                  </select>
                  <label>Endeudamiento en el Sistema Financiera (ultimos 6 meses)</label>
                  <select id="idendeudamientosistema">
                      <option></option>
                      <option value="1">AUMENTO DE DEUDA</option>
                      <option value="2">DISMINUCIÓN NO SIGNIFICATIVA</option>
                      <option value="3">DIMINUYE DEUDA</option>
                  </select>
                  <label>Inventario, Muebles y Enseres</label>
                  <select id="idinventario">
                      <option></option>
                      <option value="1">POCA MERCADERIA</option>
                      <option value="2">REGULAR MERCADERIA</option>
                      <option value="3">NEGOCIO BIEN IMPLEMENTADO</option>
                  </select>
                  <label>Comentario del Asesor sobre el Negocio</label>
                  <textarea name="comentarioasesor" id="comentarioasesor" cols="30" rows="10" onkeyup="texto_mayucula(this)">{{ $sustento->comentarioasesor ?? '' }}</textarea>
                </div>
                <div class="col-sm-6">
                  <label>Descripción del Destino del Crédito</label>
                  <textarea name="destinocredito" id="destinocredito" cols="30" rows="10" onkeyup="texto_mayucula(this)">{{ $sustento->destinocredito ?? '' }}</textarea>
                  <label>Riesgos que presenta el Negocio</label>
                  <textarea name="riesgonegocio" id="riesgonegocio" cols="30" rows="10" onkeyup="texto_mayucula(this)">{{ $sustento->riesgonegocio ?? '' }}</textarea>
                  <label>¿El Cliente, a qué destina el ingreso excedente del Negocio?</label>
                  <textarea name="destinoexcendete" id="destinoexcendete" cols="30" rows="10" onkeyup="texto_mayucula(this)">{{ $sustento->destinoexcendete ?? '' }}</textarea>
                  <label>Sustento de la Propuesta</label>
                  <textarea name="sustentopropuesta" id="sustentopropuesta" cols="30" rows="10" onkeyup="texto_mayucula(this)">{{ $sustento->sustentopropuesta ?? '' }}</textarea>
                </div>
              </div>
    
              <button type="submit" class="btn mx-btn-post">Guardar Sustento</button>

</form>
<script>
  
  tab({click:'#tab-sustento'});
  
  @if($sustento!='')
  $('#idcalificacion').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1,
  }).val({{ $sustento->idprestamo_calificacion }}).trigger('change');
  @else
  $('#idcalificacion').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1,
  });
  @endif
  @if($sustento!='')
  $('#idexperienciacredito').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1,
  }).val({{ $sustento->idprestamo_experienciacredito }}).trigger('change');
  @else
  $('#idexperienciacredito').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1,
  });
  @endif
  @if($sustento!='')
  $('#idendeudamientosistema').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1,
  }).val({{ $sustento->idprestamo_endeudamientosistema }}).trigger('change');
  @else
  $('#idendeudamientosistema').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1,
  });
  @endif
  @if($sustento!='')
  $('#idinventario').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1,
  }).val({{ $sustento->idprestamo_inventario }}).trigger('change');
  @else
  $('#idinventario').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1,
  });
  @endif

  function sumaRespuesta() {
    var totalBueno = 0;
    var totalRegular = 0;
    var totalMalo = 0;
    var totalGeneral = 0;
    $("#tabla-contenidopregunta-sustento > tbody > tr").each(function() {
        var num = $(this).attr('num');
        var valor = $('#valor'+num).val();
        var malo = 0;
        var regular = 0;
        var bueno = 0;
        $('#select_malo'+num).css({'background-color':'#fff','color':'#212529'});
        $('#select_regular'+num).css({'background-color':'#fff','color':'#212529'});
        $('#select_bueno'+num).css({'background-color':'#fff','color':'#212529'});
        if (valor == 1) {
            var malo = parseInt($('#select_malo'+num).attr('valor')!=''?$('#select_malo'+num).attr('valor'):0);
            $('#select_malo'+num).css({'background-color':'rgb(214 2 31)','color':'#fff'});
        } else if (valor == 2) {
            var regular = parseInt($('#select_regular'+num).attr('valor')!=''?$('#select_regular'+num).attr('valor'):0);
            $('#select_regular'+num).css({'background-color':'#3498db','color':'#fff'});
        } else if (valor == 3) {
            var bueno = parseInt($('#select_bueno'+num).attr('valor')!=''?$('#select_bueno'+num).attr('valor'):0);
            $('#select_bueno'+num).css({'background-color':'#2ecc71','color':'#fff'});
        }
        totalMalo = parseInt(totalMalo + malo);
        totalRegular = parseInt(totalRegular + regular);
        totalBueno = parseInt(totalBueno + bueno);
        totalGeneral = parseFloat((totalBueno + totalRegular + totalMalo)/3);
    });
    $('#totalMalo').html(totalMalo);
    $('#totalRegular').html(totalRegular);
    $('#totalBueno').html(totalBueno);
    $('#totalGeneral').html(totalGeneral.toFixed(2));
  }
  function selectRespuesta() {
    var data = [];
    $("#tabla-contenidopregunta-sustento > tbody > tr").each(function() {
      var num = $(this).attr('num');
      var idsustentopregunta = $(this).attr('idsustentopregunta');
      var select_valor = $('#valor'+num+' :selected').val();
      data.push({
        select_valor: select_valor,
        idsustentopregunta: idsustentopregunta
      });
    });
    return JSON.stringify(data);
  }
  
</script>