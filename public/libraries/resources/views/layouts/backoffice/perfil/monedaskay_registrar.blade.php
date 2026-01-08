@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Monedas Kay</span>
      <a class="btn btn-success" href="{{ url('backoffice/perfil/0/edit?view=monedaskay') }}"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
</div>
<div class="custom-form">
    <form class="js-validation-signin px-30" 
          action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/perfil',
            method: 'POST',
            data:{
                view:'monedaskay_registrar',
                idmotivopuntoskay : 1
            }        
        },
        function(resultado){
            location.href = '{{ url('backoffice/perfil/0/edit?view=monedaskay') }}';                                                                            
        },this)">
        <div class="row">
            <div class="col-md-6">
                <label>Cantidad de Kays *</label>
                <input type="number" value="50" min="50" id="mendakay-cantidad" onclick="calcularmonto()" onkeyup="calcularmonto()">
            </div>
            <div class="col-md-6">
                <label>Total</label>
                <input type="text" value="0.00" id="mendakay-monto" disabled>
            </div>
            <div class="col-md-12">
                <div class="box" style="width: 340px;">
                  <input class="Switcher__checkbox sr-only" id="idestadopago" type="checkbox">
                  <label class="Switcher" for="idestadopago">
                    <div class="Switcher__trigger" style="padding-left: 20px;" data-value="Pago en Efectivo"></div>
                    <div class="Switcher__trigger" style="padding-left: 20px;" data-value="Pago con Tarjeta"></div>
                  </label>
                </div>
                  <div id="cont-pagooficina">
                          <div class="mensaje-info">
                            <i class="fa fa-exclamation-circle"></i> Subir Voucher ó Comprobante de Pago realizado.
                          </div>
                          <label>Voucher ó Comprobante *</label>
                          <div class="fuzone" id="cont-fileupload">
                              <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
                              <input type="file" class="upload" id="mendakayimagen">
                              <div id="resultado-fileupload"></div>
                          </div>
                          <button type="submit" style="float: none;background-color: #2ecc71;" class="price-link"> Comprar Monedas KAY</button>
                  </div>
                  <div id="cont-pagotarjeta" style="display:none;">
                          <div class="mensaje-info">
                            <i class="fa fa-exclamation-circle"></i> Pagar con tarjeta genera el 8% más de comisión del monto a pagar.
                          </div>
                          <label>Total a Pagar S/.</label>
                          <input type="text" value="0.00" id="mendakay-monto-total" disabled>
                          <button type="button" id="pagaconculqi" style="float: none;background-color: #2ecc71;" class="price-link"> Comprar Monedas KAY</button>
                  </div>
              
                          
            </div>
        </div>    
    </form>  
</div>
@endsection
@section('scriptsbackoffice')
<script>
$("#idestadopago").change(function() {
    var idestado = $("#idestadopago:checked").val();
    if(idestado=='on'){
        $('#cont-pagooficina').css('display','none');
        $('#cont-pagotarjeta').css('display','block');
    }else{
        $('#cont-pagooficina').css('display','block');
        $('#cont-pagotarjeta').css('display','none');
    }
});
  
uploadfile({input:"#mendakayimagen",cont:"#cont-fileupload",result:"#resultado-fileupload"});

calcularmonto();
function calcularmonto(){
    var cantidad = parseInt($('#mendakay-cantidad').val());
    $('#mendakay-monto').val((cantidad).toFixed(2));
    var mendakaymonto = cantidad;
    var monto = (mendakaymonto+((mendakaymonto*8)/100));
    var monto = Math.round10(monto, -2);
    $('#mendakay-monto-total').val(monto.toFixed(2));
}
</script>
<script src="https://checkout.culqi.com/js/v3"></script>
<script>
// CULQI
            $('#pagaconculqi').on('click', function(e) {
                var mendakaymonto = parseFloat($('#mendakay-monto').val());
                var monto = (mendakaymonto+((mendakaymonto*8)/100))*100;
                var monto = Math.round10(monto, -2);
                console.log(monto)
                Culqi.publicKey = 'pk_live_FKfkgTUBL9ln7nMG';
                Culqi.settings({
                    title: 'COMPRAR MONEDAS KAY',
                    currency: 'PEN',
                    description: 'Recarga de '+parseInt($('#mendakay-cantidad').val())+' Monedas KAY',
                    amount: monto
                });
                Culqi.open();
                e.preventDefault();
            });
  
            function culqi() {
                if (Culqi.token) {
                    var token = Culqi.token.id;
                    callback({
                        route: 'backoffice/perfil',
                        method: 'POST',
                        data:{
                            view : 'monedaskay_registrarculqui',
                            token : token,
                            mendakay_monto : $('#mendakay-monto').val(),
                            mendakay_cantidad : $('#mendakay-cantidad').val(),
                            idmotivopuntoskay : 2
                        }
                    },
                    function(resultado){
                        if (resultado.resultado == 'CORRECTO') {
                            location.href = '{{ Request::fullUrl() }}';      
                        }
                    });
                } else { // ¡Hubo algún problema!
                    alert(Culqi.error.user_message);
                }
            };

            // FIN CULQI
</script>
@endsection