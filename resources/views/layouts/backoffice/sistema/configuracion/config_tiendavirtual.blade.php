@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Configuración de Tienda Virtual</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
          <div class="tabs-container" id="tab-tiendavirtual">
              <ul class="tabs-menu">
                  <li class="current"><a href="#tab-tvportada-0" id="tab-pedido">Portadas</a></li>
              </ul>
              <div class="tab">
                  <div id="tab-tvportada-0" class="tab-content" style="display: block;">
                      
                  </div>
              </div>
          </div> 
@endsection
@section('subscripts')
<script>
tab({click:'#tab-tiendavirtual'});
</script>
@endsection