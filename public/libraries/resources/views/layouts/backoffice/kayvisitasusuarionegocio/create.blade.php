@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>OBTENER VISITAS</span>
      <a class="btn btn-success" href="{{ url('backoffice/kaygenerarvisitasusuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Opción KAY</th>
          <th>Tienda - Producto</th>
          <th>Cantidad</th>
          <th width="10px"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($kaygenerarvisitas as $value)
          <tr>
            <td>{{$value->opcionkaynombre}}</td>
            <td>
                  <?php 
                  $link = url($value->tiendalink);
                  $tiendaproducto = $value->tiendanombre; 
                  ?>
                  <a href="{{ $link }}" target="_blank" style="color: #1e7dbd;border-bottom: 1px dashed;"><i class="fa fa-link"></i> {{ $tiendaproducto }}</a>
            </td>
            <td>
              <?php $cantidad = $value->cantidad.' visitas = '.floatval($value->puntoskay).' KAY'; ?>
              {{$cantidad}}</td>
            <td>
              <a href="javascript:;" id="btneliminar1" 
                 onclick="seleccionarpuntoskay_tienda('{{ $value->id }}','{{ $value->opcionkaynombre }}','{{ $link }}','{{ $tiendaproducto }}','{{ $cantidad }}')" 
                 class="btn btn-success open-modalkaygenerarvisitasusuarioconfirmar"><i class="fa fa-check"></i> Obtener KAY</a>
            </td>
          </tr>   
        @endforeach
      </tbody>
  </table>
  {{ $kaygenerarvisitas->links('app.tablepagination', ['results' => $kaygenerarvisitas]) }}
</div>
@endsection
@section('htmls')
<!--  modal confirmar --> 
<div class="main-register-wrap modalkaygenerarvisitasusuarioconfirmar">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg" id="close-modalkaygenerarvisitasusuarioconfirmar"><i class="fa fa-times"></i></div>
            <h3>Obtener Monedas KAY</h3>
            <div id="tabs-container" style="margin-top: 0px;">
                <div class="custom-form">
                    <form action="javascript:;" 
                          onsubmit="callback({
                               route: 'backoffice/kaygenerarvisitasusuario',
                               method: 'POST',
                               data: {
                                 view: 'confirmar'
                               }
                           },
                           function(resultado){
                               if(resultado['resultado']=='CORRECTO'){
                                   $('#modalkaygenerarvisitasusuarioconfirmar').hide();
                               }
                           },this)">
                      <input type="hidden" id="idoferta">
                      <div class="box-widget-content" style="padding:0px;padding-bottom: 10px;">
                          <div class="list-author-widget-contacts list-item-widget-contacts">
                              <ul>
                                  <li><span><i class="fa fa-tags"></i> Opción Kay:</span> <a href="javascript:;" style="cursor: inherit;" id="confirm-opcionkay"></a></li>
                                  <li><span><i class="fa fa-list-ol"></i> Tienda - Producto:</span> <a href="javascript:;" style="cursor: inherit;" id="confirm-tiendaproducto"></a></li>
                                  <li><span><i class="fa fa-calendar-alt"></i> Cantidad:</span> <a href="javascript:;" style="cursor: inherit;" id="confirm-cantidad"></a></li>
                              </ul>
                          </div>
                      </div>
                      <div style="margin-bottom: 10px;margin-top: 10px;float: left;">Compartir Link para Ganar Monedas KAY:</div>
                      <div style="border: 1px dashed #666;
                                  float: left;
                                  width: 100%;
                                  padding: 10px;">
                          <div style="font-weight: bold;" id="confirm-link"></div>
                      </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal confirmar --> 
@endsection

@section('scriptsbackoffice')
<script>
// modal confirmar ---------
var modalkaygenerarvisitasusuarioconfirmar = {};
modalkaygenerarvisitasusuarioconfirmar.hide = function () {
    $('.modalkaygenerarvisitasusuarioconfirmar').fadeOut();
    $("html, body").removeClass("hid-body");
};
$('.open-modalkaygenerarvisitasusuarioconfirmar').on("click", function (e) {
    e.preventDefault();
    $('.modalkaygenerarvisitasusuarioconfirmar').fadeIn();
    $("html, body").addClass("hid-body");
});
$('#close-modalkaygenerarvisitasusuarioconfirmar').on("click", function () {
    modalkaygenerarvisitasusuarioconfirmar.hide();
});
function seleccionarpuntoskay_tienda(id,opcionkay,link,tiendaproducto,cantidad){
    $('#confirm-opcionkay').html(opcionkay);
    $('#confirm-tiendaproducto').html(tiendaproducto);
    $('#confirm-cantidad').html(cantidad);
    $('#confirm-link').html(link+'?cod='+id);
}
</script>
@endsection