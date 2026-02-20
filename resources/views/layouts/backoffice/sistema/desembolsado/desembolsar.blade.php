<div id="carga_cambiar_estado">
<form action="javascript:;" id="form_cambiar_estado">
    <style>
      .form-check-label {
          margin-top: 5px;
          margin-left: 5px;
      }
    </style>
    <div class="modal-header">
        <h5 class="modal-title">DESEMBOLSO</h5>
        <button type="button" class="btn-close" id="modal-close-cambiar-estado" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="row" style="font-size: 14px;padding: 7px;">
        <div class="col-md-6"><b>CLIENTE:</b> <span style="font-weight: normal;">{{ $usuario->nombrecompleto }}</span></div>
        <div class="col-md-6" style="text-align: right;"><b>PRODUCTO:</b> <span style="font-weight: normal;">{{ $credito->nombreproductocredito }}</span></div>
      </div>
       <div class="col-sm-12 mt-2 mb-1 text-center">
            <button type="button" class="btn btn-primary" onclick="verpdf('pdf_ticket')"> TICKET DE DESEMBOLSO</button>
            <button type="button" class="btn btn-primary" onclick="verpdf('pdf_cronograma')"> CRONOGRAMA</button>
            <button type="button" class="btn btn-primary" onclick="verpdf('pdf_contrato')"> CONTRATO</button>
            <button type="button" class="btn btn-primary" onclick="verpdf('pdf_resumen')"> H. RESUMEN</button>
            @if($credito->idforma_credito==1)
            <button type="button" class="btn btn-primary" onclick="verpdf('pdf_declaracion')"> DECLARACIÓN JURADA</button>
            @endif
            <button type="button" class="btn btn-warning" onclick="verpdf('pdf_pagare')"> PAGARÉ</button>
            @if($credito->idforma_credito==1)
            <hr style="margin-top: 8px;margin-bottom: 8px;">
            <?php $i=1 ?>
            @foreach($garantias as $value)
            <button type="button" class="btn btn-warning1" onclick="verpdf('pdf_ticketprendario',{{$value->id}},{{$i}})"> TICKET DE GARANTIA {{ $i }}</button>
            <?php $i++ ?>
            @endforeach
            @endif
       </div>
       <iframe id="iframe_acta_aprobacion" src="{{ url('/backoffice/'.$tienda->id.'/desembolso/'.$credito->id.'/edit?view=pdf_cronograma') }}#zoom=100" frameborder="0" width="100%" height="600px"></iframe>
      </div>
</form>   
</div>
<script>
function verpdf(valor,idgarantia,num){
    $('#iframe_acta_aprobacion').attr('src',"{{ url('/backoffice/'.$tienda->id.'/desembolso/'.$credito->id.'/edit?view=') }}"+valor+'&idgarantia='+idgarantia+'&num='+num+'#zoom=100');
}
</script>