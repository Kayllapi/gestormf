
    <div class="tabs-container" id="tab-resultado">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-resultado-3" onclick="cualitativopdf_index()">Integrantes de Comité</a></li>
            <li><a href="#tab-resultado-4" onclick="evaluacionpdf_index()">Lista de Integrantes</a></li>
            <li><a href="#tab-resultado-6" onclick="garantiapdf_index()">Apertura de Cuenta de Ahorro</a></li>
        </ul>
        <div class="tab">
            <div id="tab-resultado-3" class="tab-content" style="display: none;">
                <div id="cont-cualitativopdf"></div>
            </div>
            <div id="tab-resultado-4" class="tab-content" style="display: none;">
                <div id="cont-evaluacionpdf"></div>
            </div>
            <div id="tab-resultado-6" class="tab-content" style="display: none;">
                <div id="cont-garantiapdf"></div>
            </div>
        </div>
    </div>   

<style>
  .resultado-aprobado {
    background-color: #179a4f;
    padding: 5px;
    border-radius: 5px;
    color: rgb(255 255 255);
    font-weight: bold;
    font-size: 20px;
    margin-bottom: 5px;
    float: left;
    width: 100%;
  }
  .resultado-desaprobado {
    background-color: #8c1329;
    padding: 5px;
    border-radius: 5px;
    color: rgb(255 255 255);
    font-weight: bold;
    font-size: 20px;
    margin-bottom: 5px;
    float: left;
    width: 100%;
  }
</style>  
<script>
  tab({click:'#tab-resultado'});
  
   function creditopdf_index(){
        $('#cont-creditopdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocreditogrupal->id.'/edit?view=creditopdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   function cualitativopdf_index(){
        $('#cont-cualitativopdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocreditogrupal->id.'/edit?view=cualitativopdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   function evaluacionpdf_index(){
        $('#cont-evaluacionpdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocreditogrupal->id.'/edit?view=evaluacionpdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   function negociopdf_index(){
        $('#cont-negociopdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocreditogrupal->id.'/edit?view=negociopdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   function garantiapdf_index(){
        $('#cont-garantiapdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocreditogrupal->id.'/edit?view=garantiapdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
   function domiciliopdf_index(){
        $('#cont-domiciliopdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocreditogrupal->id.'/edit?view=domiciliopdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
   }
</script>