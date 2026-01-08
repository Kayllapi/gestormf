
    <div class="tabs-container" id="tab-resultado">
        <ul class="tabs-menu">
            <li  class="current"><a href="#tab-resultado-2" onclick="ahorropdf_index()">Solicitud de Ahorro</a></li>
        </ul>
        <div class="tab">
            <div id="tab-resultado-2" class="tab-content" style="display: block;">
                <div id="cont-ahorropdf"></div>
            </div>
        </div>
    </div>   

<script>
  tab({click:'#tab-resultado'});
    ahorropdf_index();
   function ahorropdf_index(){
        $('#cont-ahorropdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrosolicitud/'.$idprestamoahorro.'/edit?view=ahorropdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
</script>