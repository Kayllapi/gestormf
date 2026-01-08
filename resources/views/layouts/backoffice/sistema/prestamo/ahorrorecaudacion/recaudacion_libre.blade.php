<div class="col-sm-6">
  <form action="javascript:;" 
      onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrorecaudacion',
            method: 'POST',
            carga: '#carga-recaudacion',
            data:   {
                view: 'registrar_ahorrolibre',
                idprestamo_ahorro: $('#idcliente').val(),
                monto_efectivo: $('#monto_efectivo').val(),
                formapago_contado_seleccionar: formapago_contado_seleccionar(),
            }
        },
        function(resultado){
          $('#modal-recaudacion-ahorrolibre').css('display','block');
          $('#contenido-recaudacion-ahorrolibre').html('<div class=\'cont-confirm\' style=\'margin-top: 15px;\'>'+
               '<div class=\'confirm\'><i class=\'fa fa-check\'></i></div>'+
               '<div class=\'confirm-texto\'>¡Correcto!</div>'+
               '<div class=\'confirm-subtexto\'>Se ha registrado correctamente.</div></div>'+
               '<div class=\'custom-form\' style=\'text-align: center;margin-bottom: 5px;\'>'+
               '<button type=\'button\' class=\'btn big-btn color-bg flat-btn mx-realizar-pago\' style=\'margin: auto;float: none;\' onclick=\'realizar_nueva_recaudacion()\'>'+
               '<i class=\'fa fa-check\'></i> Realizar Nueva Recaudacion</button></div>'+
               '<div class=\'custom-form\' style=\'text-align: center;margin-bottom: 5px;\'>'+
               '<button type=\'button\' class=\'btn big-btn color-bg flat-btn\' style=\'margin: auto;float: none;\' onclick=\'irarecaudaciones()\'>'+
               '<i class=\'fa fa-check\'></i> Ir a Recaudaciones</button></div>'+
               '<div id=\'iframeventa\'>'+
               '<iframe src=\'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/'.$s_prestamo_ahorro->id.'/edit') }}?view=ticketpdf_pagolibre&idprestamo_ahorrorecaudacionlibre='+resultado['idprestamo_ahorrorecaudacionlibre']+'#zoom=130\' frameborder=\'0\' width=\'100%\' height=\'600px\'></iframe>'+
              '</div>');
          removecarga({input:'#carga-recaudacion'});
          pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/create') }}?view=formapago'+
              '&efectivo=monto_efectivo',
              result:'#cont-formapago'})
        },this)">
    <label>Monto a Ahorrar *</label>
    <input type="number" id="monto_efectivo" min="0" step="0.01" onkeyup="monto_efectivo()" onclick="monto_efectivo()">
    <div id="cont-formapago"></div>
    <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;">Registrar Recaudación</button>
  </form>
</div>  
<div class="col-sm-6">
    <div class="tabs-container" id="tab-tablerecaudacion">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-tablerecaudacion-1">Recaudaciones</a></li>
            <li><a href="#tab-tablerecaudacion-2">Retiros</a></li>
            <li><a href="#tab-tablerecaudacion-3">Resumen</a></li>
        </ul>
        <div class="tab">
            <div id="tab-tablerecaudacion-1" class="tab-content" style="display: block;">
                <div id="cont-pagorealizado"></div>
            </div>
            <div id="tab-tablerecaudacion-2" class="tab-content" style="display: none;">
                <div id="cont-retirorealizado"></div>
            </div>
            <div id="tab-tablerecaudacion-3" class="tab-content" style="display: none;">
                <div id="cont-resumen"></div>
            </div>
        </div>
    </div>
</div>
<script>

        pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/create') }}?view=formapago'+
                                  '&efectivo=monto_efectivo',
                                  result:'#cont-formapago'})

    tab({click:'#tab-tablerecaudacion'});
    pagolibre_index();
    retirolibre_index();
    resumen_index();

    function pagolibre_index(){    
        pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/'.$s_prestamo_ahorro->id.'/edit') }}?view=pagolibre_index',result:'#cont-pagorealizado'});
    }
    function pagolibre_ticket(idprestamo_ahorrorecaudacionlibre){
        pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/'.$s_prestamo_ahorro->id.'/edit') }}?view=pagolibre_ticket&idprestamo_ahorrorecaudacionlibre='+idprestamo_ahorrorecaudacionlibre,result:'#cont-pagorealizado'});
    }
    function pagolibre_anular(idprestamo_ahorrorecaudacionlibre){
        pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/'.$s_prestamo_ahorro->id.'/edit') }}?view=pagolibre_anular&idprestamo_ahorrorecaudacionlibre='+idprestamo_ahorrorecaudacionlibre,result:'#cont-pagorealizado'});
    }
  
    function retirolibre_index(){    
        pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/'.$s_prestamo_ahorro->id.'/edit') }}?view=retirolibre_index',result:'#cont-retirorealizado'});
    }
    function retirolibre_registrar(idprestamo_ahorro){
        pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/'.$s_prestamo_ahorro->id.'/edit') }}?view=retirolibre_registrar&idprestamo_ahorro='+idprestamo_ahorro,result:'#cont-retirorealizado'});
    }
    function retirolibre_ticket(idprestamo_ahorroretirolibre){
        pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/'.$s_prestamo_ahorro->id.'/edit') }}?view=retirolibre_ticket&idprestamo_ahorroretirolibre='+idprestamo_ahorroretirolibre,result:'#cont-retirorealizado'});
    }
    function retirolibre_anular(idprestamo_ahorroretirolibre){
        pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/'.$s_prestamo_ahorro->id.'/edit') }}?view=retirolibre_anular&idprestamo_ahorroretirolibre='+idprestamo_ahorroretirolibre,result:'#cont-retirorealizado'});
    }
  
    function resumen_index(){    
        pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/'.$s_prestamo_ahorro->id.'/edit') }}?view=resumen_index',result:'#cont-resumen'});
    }
  
    function realizar_nueva_recaudacion() {
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrorecaudacion/'+$('#idcliente').val()+'/edit?view=recaudacion',result:'#cont-clienteahorro'});
      $('#modal-recaudacion-ahorrolibre').css('display','none');
    }
    function irarecaudaciones() {
      location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion') }}';
    }
</script>