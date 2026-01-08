<?php 
$totalpuntoskay = DB::table('consumidor_puntoskay')
            ->where('consumidor_puntoskay.idusers',Auth::user()->id)
            ->where('consumidor_puntoskay.fechaconfirmacion','<>','')
            ->where('consumidor_puntoskay.idestado','<>',4)
            ->sum('consumidor_puntoskay.cantidad');
      
$totalpendientepuntoskay = DB::table('consumidor_puntoskay')
            ->where('consumidor_puntoskay.idusers',Auth::user()->id)
            ->where('consumidor_puntoskay.fechaconfirmacion',null)
            ->where('consumidor_puntoskay.idestado','<>',4)
            ->sum('consumidor_puntoskay.cantidad');

?>
<div class="mx-contpuntskay">
  <i class="fa fa-tags"></i> {{ consumidor_puntoskay()['total'] }} Monedas KAY <a href="{{ url('backoffice/perfil/0/edit?view=monedaskay_registrar') }}" class="td-badge" style="display: block;padding-top: 8px;padding-bottom: 0px;"><span class="badge badge-pill badge-warning" style="padding-right: 1.6em;
    padding-left: 1.6em;
    border-radius: 20px;
    font-size: 14px;"><i class="fa fa-money"></i> Comprar Monedas</span></a>
</div>