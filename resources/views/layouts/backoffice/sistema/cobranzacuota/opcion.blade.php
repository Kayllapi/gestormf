<div id="carga_cambiar_estado">
<form action="javascript:;" id="form_cambiar_estado">
    <style>
      .form-check-label {
          margin-top: 5px;
          margin-left: 5px;
      }
    </style>
    <div class="modal-header">
        <h5 class="modal-title">TICKETS</h5>
        <button type="button" class="btn-close" id="modal-close-cambiar-estado" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
       <div class="col-sm-12 mt-2 text-center">
         
            <button type="button" class="btn btn-primary"
            onclick="verpdf('pdf_pago')"> VOUCHER DE PAGO</button>
            @if($idestadocredito==2 && $credito->idforma_credito==1 && $entregargarantia=='on')
            <button type="button" class="btn btn-warning"
            onclick="verpdf('pdf_garantia',{{$credito->id}})"> V. ENTREGA DE GARANTIA</button>
            @endif
       <div class="col-sm-12 mt-2">
        <iframe id="iframe_acta_aprobacion" 
        src="{{ url('/backoffice/'.$tienda->id.'/cobranzacuota/'.$credito->id.'/edit?view=pdf_pago&idcobranzacuota='.$idcobranzacuota) }}#zoom=100" 
        frameborder="0" width="100%" height="600px"></iframe>
      </div>
      </div>
</form>   
</div>
<script>
function verpdf(valor,idgarantia=0,num=0){
    $('#iframe_acta_aprobacion').attr('src',"{{ url('/backoffice/'.$tienda->id.'/cobranzacuota/'.$credito->id.'/edit?view=') }}"+valor+'&idcobranzacuota={{$idcobranzacuota}}&idgarantia='+idgarantia+'&num='+num+'#zoom=100');
}
</script>