
          @if(configuracion($tienda->id,'sistema_imagenfondosistema')['resultado']=='CORRECTO')
          <div class="list-single-main-media fl-wrap" style="margin-bottom: 5px;">
              <img  src="{{ url('public/backoffice/tienda/'.$tienda->id.'/imagensistema/'.configuracion($tienda->id,'sistema_imagenfondosistema')['valor']) }}" class="respimg" width="100%">
          </div>
          @else
          <!--div class="list-single-main-media fl-wrap" style="margin-bottom: 5px;">
                <img src="https://www.ceupe.com/images/easyblog_articles/1683/adentro-ahorros-apple-908288.jpg" class="respimg" width="100%" style="    height: 550px;
    border-radius: 10px;
    object-fit: cover;"-->
            </div>
          @endif