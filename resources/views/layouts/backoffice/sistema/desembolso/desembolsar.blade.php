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
       <div class="col-sm-12 mt-2 text-center">
            <div id="cont_botonesdesembolso" style="display:none;">
                <button type="button" class="btn btn-primary" onclick="verpdf('pdf_ticket',0,0,{{$credito->id}})"> TICKET DE DESEMBOLSO</button>
                <button type="button" class="btn btn-primary" onclick="verpdf('pdf_cronograma',0,0,{{$credito->id}})"> CRONOGRAMA</button>
                <button type="button" class="btn btn-primary" onclick="verpdf('pdf_contrato',0,0,{{$credito->id}})"> CONTRATO</button>
                <button type="button" class="btn btn-primary" onclick="verpdf('pdf_resumen',0,0,{{$credito->id}})"> H. RESUMEN</button>
                @if($credito->idforma_credito==1)
                    <button type="button" class="btn btn-primary" onclick="verpdf('pdf_declaracion',0,0,{{$credito->id}})"> DECLARACIÓN JURADA</button>
                @endif
                <button type="button" class="btn btn-warning" onclick="verpdf('pdf_pagare',0,0,{{$credito->id}})"> PAGARÉ</button>
                <button type="button"
                    class="btn btn-info"
                    id="btn_compartir"
                    style="padding: 2.5px 8px;"
                    onclick="abrirCompartir()">
                    <i class="fa-solid fa-share-nodes" style="width: 30px; font-size: 20px;"></i>
                </button>
            </div>
            <button type="button" class="btn btn-success" onclick="realizar_desembolsoTicket({{ $credito->id }})" id="btn_desembolsar"><i class="fa-solid fa-check"></i> DESEMBOLSAR {{ $credito->id }}</button>
            <div id="cont_garantias" style="display:none;">
                @if($credito->idforma_credito==1)
                    <hr style="margin-top: 8px;margin-bottom: 8px;">
                    <?php $i=1 ?>
                    @foreach($garantias as $value)
                        <button type="button" class="btn btn-warning1" onclick="verpdf('pdf_ticketprendario',{{$value->id}},{{$i}},{{$credito->id}})"> TICKET DE GARANTIA {{ $i }}</button>
                        <?php $i++ ?>
                    @endforeach
                @endif
            </div>
       </div>
        <iframe id="iframe_acta_aprobacion" src="{{ url('/backoffice/'.$tienda->id.'/desembolso/'.$credito->id.'/edit?view=pdf_cronograma') }}#zoom=100" frameborder="0" width="100%" height="600px"></iframe>
      </div>
</form>   
</div>
<script>
const pdfs_compartibles = ['pdf_ticket','pdf_cronograma','pdf_contrato','pdf_resumen'];
let pdf_activo = 'pdf_ticket';

function verpdf(valor,idgarantia,num,idcredito){
    pdf_activo = valor;
    // mostrar u ocultar botón compartir
    if(pdfs_compartibles.includes(valor)){
        $('#btn_compartir').show();
    } else {
        $('#btn_compartir').hide();
    }
    $('#iframe_acta_aprobacion').attr('src',"{{ url('/backoffice') }}/{{$tienda->id}}/desembolso/"+idcredito+"/edit?view="+valor+"&idgarantia="+idgarantia+"&num="+num+"#zoom=100");

}
$('#btn_compartir').show();

function realizar_desembolsoTicket(idcredito){
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/desembolso/"+idcredito+"/edit?view=desembolsarticket",size:'modal-sm' });
  }
function imprimirTicketPdf(){
    let iframe = document.getElementById('iframe_acta_aprobacion');
    iframe.onload = function () {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    };
}
function abrirCompartir() {
    let url_voucher = encodeURIComponent(
        "{{ url('backoffice/'.$tienda->id.'/desembolso/'.$credito->id.'/edit') }}"
        + "?view=" + pdf_activo
    );
    // Firmar desde el backend... ver nota abajo
    $.get("{{ url('backoffice/'.$tienda->id.'/desembolso/'.$credito->id.'/edit') }}", {
        view: 'generar_url_firmada',
        pdf: pdf_activo,
    }, function(res){
        let url_voucher = encodeURIComponent(res.url_firmada);
        let idcliente = {{ $credito->idcliente }};
        modal({ 
            route: "{{ url('backoffice/'.$tienda->id.'/inicio/create?view=compartir_opcion') }}"
                 + "&url_voucher=" + url_voucher
                 + "&clt=" + idcliente,
            size: 'modal-sm' 
        });
    });
}
</script>