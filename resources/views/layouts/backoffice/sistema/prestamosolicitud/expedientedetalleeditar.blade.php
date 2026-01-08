<div id="carga-credito">
    <div class="box" style="width: 300px;">
      <input class="Switcher__checkbox sr-only" id="idestadoexpediente" type="checkbox" <?php echo ($prestamocredito->estadoexpediente=='si' or $prestamocredito->estadoexpediente=='')? 'checked="checked"':'' ?>>
      <label class="Switcher" for="idestadoexpediente">
        <div class="Switcher__trigger" data-value="Sin Expediente"></div>
        <div class="Switcher__trigger" data-value="Con Expediente"></div>
      </label>
    </div>
    <div id="cont-estadoexpediente1" <?php echo ($prestamocredito->estadoexpediente=='si' or $prestamocredito->estadoexpediente=='')=='si'? 'style="display:block;"': 'style="display:none;"' ?>>
    </div>
    <div id="cont-estadoexpediente2" <?php echo ($prestamocredito->estadoexpediente=='si' or $prestamocredito->estadoexpediente=='')=='si'? 'style="display:none;"': 'style="display:block;"' ?>>
        <!--div class="resultado-aprobado">CRÉDITO APROBADO</div-->
    </div> 
    
        <div id="resultado-credito"></div>   
        <div class="tabs-container" id="tab-detalle-cliente-editar">
            <ul class="tabs-menu">
              <li class="current"><a href="#tab-detalle-cliente-editar-1">Domicilio</a></li>
              <li><a href="#tab-detalle-cliente-editar-2">Ingresos</a></li>
              <li><a href="#tab-detalle-cliente-editar-3">Garantias</a></li>
              <li><a href="#tab-detalle-cliente-editar-5">Sustento</a></li>
              <li><a href="#tab-detalle-cliente-editar-6">Resultado</a></li>
            </ul>
            <div class="tab">
              <div id="tab-detalle-cliente-editar-1" class="tab-content" style="display: block;">
                <div id="cont-domicilios"></div>
              </div>
              <div id="tab-detalle-cliente-editar-2" class="tab-content" style="display: none;">
                <div id="cont-laborales"></div>
              </div>
              <div id="tab-detalle-cliente-editar-3" class="tab-content" style="display: none;">
                <div id="cont-bienes"></div>
              </div>
              <div id="tab-detalle-cliente-editar-5" class="tab-content" style="display: none;">
                <div id="cont-sustento"></div>
              </div>
              <div id="tab-detalle-cliente-editar-6" class="tab-content" style="display: none;">
                <div id="cont-resultado"></div>
              </div>
            </div>
          </div> 
</div>            
<!-- Tabulador de pestañas -->
<script>
  tab({click:'#tab-detalle-cliente-editar'});
  $("#idestadoexpediente").change(function() {
      var idestadoexpediente = $("#idestadoexpediente:checked").val();
      if(idestadoexpediente=='on'){
          $('#cont-estadoexpediente1').css('display','block');
          $('#cont-estadoexpediente2').css('display','none');
      }else{
          $('#cont-estadoexpediente1').css('display','none');
          $('#cont-estadoexpediente2').css('display','block');
      }
      guardar_creditoexpediente(idestadoexpediente);
  });
  
  function guardar_creditoexpediente(idestadoexpediente){
      //$( ".form-guardar_creditoexpediente" ).submit();
    
      callback({
          route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{$prestamocredito->id}}',
          method: 'PUT',
          carga: '#carga-credito',
          data:   {
              view: 'editar-expediente',
              idestadoexpediente: idestadoexpediente
          }
      },
      function(resultado){
          removecarga({input:'#carga-credito'});
          //location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud') }}';
      })
  }
</script>      
<script>
    domicilio_edit();
    function domicilio_edit(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=domicilioedit',result:'#cont-domicilios'});
    }
    laboral_edit();
    function laboral_edit(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=laboraledit',result:'#cont-laborales'});
    }
    bien_index();
    function bien_index(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=bien',result:'#cont-bienes'});
    }
    function bien_create(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=biencreate',result:'#cont-bienes'});
    }
    function bien_edit(idbien){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=bienedit&idbien='+idbien,result:'#cont-bienes'});
    }
    function bien_detalle(idbien){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=biendetalle&idbien='+idbien,result:'#cont-bienes'});
    }
    function bien_imagen(idbien){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=bienimagen&idbien='+idbien,result:'#cont-bienes'});
    }
    function bien_eliminar(idbien){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=bieneliminar&idbien='+idbien,result:'#cont-bienes'});
    }
  sustento_index();
  function sustento_index(){
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=sustento',result:'#cont-sustento'});
  }
    resultado_index();
    function resultado_index(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=resultado',result:'#cont-resultado'});
    }
</script>   