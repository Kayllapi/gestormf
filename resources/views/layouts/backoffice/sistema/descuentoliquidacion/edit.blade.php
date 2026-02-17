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
        <table class="table">
          <tr>
            <td style="width: 90px;"><b>Prestamo S/.</b></td>
            <td style="width:2px;"><b>:</b></td>
            <td >{{ $credito->monto_solicitado }}</td>
            <td style="width: 180px;"><b>Prest., Int., Serv. y Cargo S/.</b></td>
            <td style="width:2px;"><b>:</b></td>
            <td >{{ $credito->total_pagar }}</td>
            <td style="width: 120px;"><b>Venc. Contrato</b></td>
            <td style="width:2px;"><b>:</b></td>
            <td>{{ date_format(date_create($credito->fecha_ultimopago),'d-m-Y') }}</td>
          </tr>
          <tr>
            <td><b>TEM (%)</b></td>
            <td style="width:2px;"><b>:</b></td>
            <td>{{ $credito->tasa_tem }}</td>
            <td><b>TIP (%)</b></td>
            <td style="width:2px;"><b>:</b></td>
            <?php 
               $tasa_tip = $credito->modalidad_calculo == 'Interes Compuesto' ? '' : $credito->tasa_tip;
            ?>
            <td>{{ $tasa_tip }}</td>
            <td><b>F. PAGO</b></td>
            <td style="width:2px;"><b>:</b></td>
            <td>{{ $credito->forma_pago_credito_nombre }} ({{ $credito->cuotas }} Cuotas)</td>
          </tr>
          <tr>
            <td><b>Producto</b></td>
            <td style="width:2px;"><b>:</b></td>
            <td>{{ $credito->nombreproductocredito }}</td>
            <td><b>Modalidad de C.</b></td>
            <td style="width:2px;"><b>:</b></td>
            <td>{{ $credito->modalidad_credito_nombre }}</td>
            <td><b>F. Desembolso</b></td>
            <td style="width:2px;"><b>:</b></td>
            <td>{{ date_format(date_create($credito->fecha_desembolso),'d-m-Y') }}</td>
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