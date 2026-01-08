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
  <i class="fa fa-tags"></i> {{ consumidor_puntoskay()['total'] }} Monedas KAY 
</div>