<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/descuentoliquidacion/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              idtienda: {{$tienda->id}}
          }
      },
      function(resultado){
          $('#tabla-garantias').DataTable().ajax.reload();
          //load_edit_garantia();
          lista_garantias_cliente({{ $credito->idcliente }});
      },this)" id="form-editar-garantia">
    <div class="mb-1 mt-1">
      <span class="badge d-block">DATOS DE PRÉSTAMO</span>
    </div>
    <div class="modal-body">
        <table class="table table-bordered">
          <tr>
            <td style="width: 80px;background-color: #efefef !important;">Prestamo S/.</td>
            <td style="width: 60px;background-color: #fff;"><b>{{ $credito->monto_solicitado }}</b></td>
            <td style="width: 50px;background-color: #efefef !important;">Prest., Int., Serv. y Cargo S/.</td>
            <td style="width: 60px;background-color: #fff;"><b>{{ $credito->total_pagar }}</b></td>
            <td style="width: 50px;background-color: #efefef !important;">Venc. Contrato</td>
            <td style="width: 80px;background-color: #fff;"><b>{{ date_format(date_create($credito->fecha_ultimopago),'d-m-Y') }}</b></td>
          </tr>
          <tr>
            <td style="background-color: #efefef !important;">TEM (%)</td>
            <td style="background-color: #fff;"><b>{{ $credito->tasa_tem }}</b></td>
            <td style="background-color: #efefef !important;">TIP (%)</td>
            <?php 
               $tasa_tip = $credito->modalidad_calculo == 'Interes Compuesto' ? '' : $credito->tasa_tip;
            ?>
            <td style="background-color: #fff;"><b>{{ $tasa_tip }}</b></td>
            <td style="background-color: #efefef !important;">F. PAGO</td>
            <td style="background-color: #fff;"><b>{{ $credito->forma_pago_credito_nombre }} ({{ $credito->cuotas }} Cuotas)</b></td>
          </tr>
          <tr>
            <td style="background-color: #efefef !important;">Producto</td>
            <td style="background-color: #fff;"><b>{{ $credito->nombreproductocredito }}</b></td>
            <td style="background-color: #efefef !important;">Modalidad de C.</td>
            <td style="background-color: #fff;"><b>{{ $credito->modalidad_credito_nombre }}</b></td>
            <td style="background-color: #efefef !important;">F. Desembolso</td>
            <td style="background-color: #fff;"><b>{{ date_format(date_create($credito->fecha_desembolso),'d-m-Y') }}</b></td>
          </tr>
        </table>
    </div>
    <div class="modal-body">
        <div class="row">
          <div class="col-md-10"><div id="cont_cronograma" style="overflow-y: scroll;height: 200px;"></div></div>
          <div class="col-md-2">
            <button type="button" class="btn btn-primary text-center" id="btn-create-cliente" 
                    onclick="load_create_descuentocuota()">
              GENERAR <br>DESCUENTO <br>DE CUOTA</button>
          </div>
        </div>
    </div>
    <div class="modal-body">
        <div class="row">
          <div class="col-md-10"><div id="cont_descuentosdecuotas" style="overflow-y: scroll;height: 200px;"></div></div>
          <div class="col-md-2">
            <button type="button" class="btn btn-danger text-center" id="btn-delete-cliente" onclick="load_delete_descuentocuota()">
              ELIMINAR</button>
          </div>
        </div>
    </div>
</form> 
<script>

  show_data_cronograma();
  show_data_descuentodecuotas();
  
  function show_data_cronograma(numerocuota=0) {
    $.ajax({
      url:"{{url('backoffice/'.$tienda->id.'/descuentoliquidacion/show_cronograma')}}",
      type:'GET',
      data: {
          idcredito : {{$credito->id}},
          numerocuota : numerocuota,
      },
      success: function (res){
        $('#cont_cronograma').html(res.html);
        setTimeout(function () { 
          $('#cont_cronograma').scrollTop(res.select_ultimacuotacancelada*30); 
        }, 500);
      }
    }) 
  }
  
  function show_data_descuentodecuotas() {    
    $.ajax({
      url:"{{url('backoffice/'.$tienda->id.'/descuentoliquidacion/show_descuentodecuotas')}}",
      type:'GET',
      data: {
          idcredito : {{$credito->id}},
      },
      success: function (res){
        $('#cont_descuentosdecuotas').html(res.html);
      }
    }) 
  }
  
  function show_select_descuentodecuotas(e) {
    
    let id = $(e).attr('data-valor-columna');
    $('#table-detalle-descuentodecuotas tr.selected').removeClass('selected');
    $(e).addClass('selected');
    
  }
   
  function load_create_descuentocuota(){
    let numerocuota = $('#table-detalle-cronograma > tbody > tr.seleccionar').attr('data-numerocuota');
    if(numerocuota == "" || numerocuota == undefined ){
      alert('Debe de seleccionar mínimo una cuota!!!.');   
      return false;
    }
    
    let url = "{{ url('backoffice/'.$tienda->id) }}/descuentoliquidacion/create?view=registrar&idcredito={{$credito->id}}&numerocuota="+numerocuota;
    modal({ route: url,  size: 'modal-sm' })
  }
   
  function load_delete_descuentocuota(){
    let idcredito_descuentocuota = $('#table-detalle-descuentodecuotas > tbody > tr.selected').attr('data-valor-columna');

    if(idcredito_descuentocuota == "" || idcredito_descuentocuota == undefined ){
      alert('Debe de seleccionar un cuota de descuento.');   
      return false;
    }

    let url = "{{ url('backoffice/'.$tienda->id) }}/descuentoliquidacion/"+idcredito_descuentocuota+"/edit?view=eliminar";
    modal({ route: url,  size: 'modal-sm' })
  }
  
</script>     