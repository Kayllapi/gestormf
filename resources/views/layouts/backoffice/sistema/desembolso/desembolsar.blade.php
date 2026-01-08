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
          <div class="col-md-6"><b>CLIENTE:</b> {{ $usuario->nombrecompleto }}</div>
          <div class="col-md-6" style="text-align: right;"><b>PRODUCTO:</b> {{ $credito->nombreproductocredito }}</div>
      </div>
       <div class="col-sm-12 mt-2 text-center">
            <div id="cont_botonesdesembolso" style="display:none;">
            <button type="button" class="btn btn-danger" style="background-color: #144081;border-color: #144081;" onclick="verpdf('pdf_ticket')"> TICKET DE DESEMBOLSO</button>
            <button type="button" class="btn btn-danger" style="background-color: #144081;border-color: #144081;" onclick="verpdf('pdf_cronograma')"> CRONOGRAMA</button>
            <button type="button" class="btn btn-danger" style="background-color: #144081;border-color: #144081;" onclick="verpdf('pdf_contrato')"> CONTRATO</button>
            <button type="button" class="btn btn-danger" style="background-color: #144081;border-color: #144081;" onclick="verpdf('pdf_resumen')"> H. RESUMEN</button>
            @if($credito->idforma_credito==1)
            <button type="button" class="btn btn-danger" style="background-color: #144081;border-color: #144081;" onclick="verpdf('pdf_declaracion')"> DECLARACIÓN JURADA</button>
            @endif
            <button type="button" class="btn btn-warning" onclick="verpdf('pdf_pagare')"> PAGARÉ</button>
            </div>
            <button type="button" class="btn btn-success" onclick="realizar_desembolso()" id="btn_desembolsar"><i class="fa-solid fa-check"></i> DESEMBOLSAR</button>
            <div id="cont_garantias" style="display:none;">
            @if($credito->idforma_credito==1)
            <hr style="margin-top: 8px;margin-bottom: 8px;">
            <?php $i=1 ?>
            @foreach($garantias as $value)
            <button type="button" class="btn btn-danger" style="background-color: #1e69d9;border-color: #144081;" onclick="verpdf('pdf_ticketprendario',{{$value->id}},{{$i}})"> TICKET DE GARANTIA {{ $i }}</button>
            <?php $i++ ?>
            @endforeach
            @endif
            </div>
       </div>
       <div class="col-sm-12 mt-2">
        <iframe id="iframe_acta_aprobacion" src="{{ url('/backoffice/'.$tienda->id.'/desembolso/'.$credito->id.'/edit?view=pdf_cronograma') }}#zoom=90" frameborder="0" width="100%" height="600px"></iframe>
      </div>
      </div>
</form>   
</div>
<script>
function verpdf(valor,idgarantia,num){
    $('#iframe_acta_aprobacion').attr('src',"{{ url('/backoffice/'.$tienda->id.'/desembolso/'.$credito->id.'/edit?view=') }}"+valor+'&idgarantia='+idgarantia+'&num='+num+'#zoom=90');
}
   
   function realizar_desembolso(){

          modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/desembolso/{{$credito->id}}/edit?view=desembolsarticket",size:'modal-sm' });
    //var opcion = confirm("¿Esta seguro de eliminar?");
    //if (opcion == true) {
       
      
          /*callback({
              route: '{{ url('backoffice/'.$tienda->id.'/desembolso/'.$credito->id) }}',
              method: 'PUT',
              id: '#form_cambiar_estado',
              carga: '#carga_cambiar_estado',
              data:{
                  view: 'realizar_desembolo',
              }
          },
          function(res){*/
              /*lista_credito();
              verpdf('pdf_ticket');
              $('#cont_botonesdesembolso').css('display','block'); 
              $('#btn_desembolsar').css('display','none'); 
              modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/desembolso/"+id+"/edit?view=ticket" }); 
              $('#modal-close-cambiar-estado').click();*/
          //})
    //}
     
          
  }
</script>