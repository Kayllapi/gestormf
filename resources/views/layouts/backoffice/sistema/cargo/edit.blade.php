<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cargo/'.$credito->id) }}',
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
            <td style="width: 80px;">Prestamo S/.</td>
            <td style="width: 60px;"><b>{{ $credito->monto_solicitado }}</b></td>
            <td style="width: 50px;">Prest. + Int. S/.</td>
            <td style="width: 60px;"><b>{{ $credito->total_pagar }}</b></td>
            <td style="width: 50px;">Venc. Contrato</td>
            <td style="width: 80px;"><b>{{ date_format(date_create($credito->fecha_ultimopago),'d-m-Y') }}</b></td>
          </tr>
          <tr>
            <td>TEM (%)</td>
            <td><b>{{ $credito->tasa_tem }}</b></td>
            <td>TIP (%)</td>
            <td><b>{{ $credito->tasa_tip }}</b></td>
            <td>F. PAGO</td>
            <td><b>{{ $credito->forma_pago_credito_nombre }}</b></td>
          </tr>
        </table>
        <table class="table table-bordered">
          <tr>
            <td style="width: 80px;">Producto</td>
            <td><b>{{ $credito->nombreproductocredito }}</b></td>
            <td style="width: 80px;">Operación</td>
            <td><b>{{ $credito->modalidad_credito_nombre }}</b></td>
            <td style="width: 90px;">F. Desembolso</td>
            <td><b>{{ date_format(date_create($credito->fecha_desembolso),'d-m-Y') }}</b></td>
          </tr>
        </table>
    </div>
    <div class="modal-body">
        <div class="row">
          <div class="col-md-10"><div id="cont_cronograma"></div></div>
          <div class="col-md-2">
            <div>
            <button type="button" class="btn btn-primary text-center" id="btn-create-cliente" onclick="load_create_descuentocuota()">
              REGISTRAR <br>CARGO</button></div>
            <div class="mt-1"><button type="button" class="btn btn-danger text-center" id="btn-delete-cliente" onclick="load_delete_descuentocuota()">
              ELIMINAR</button></div>
          </div>
        </div>
    </div>
</form> 
<script>

  show_data_cargo();
  
  function show_data_cargo() {
    $.ajax({
      url:"{{url('backoffice/0/cargo/show_cargo')}}",
      type:'GET',
      data: {
          idcredito : {{$credito->id}},
      },
      success: function (res){
        $('#cont_cronograma').html(res.html);
      }
    }) 
  }
  
  function show_data_descuentodecuotas(e) {
    
    let id = $(e).attr('data-valor-columna');
    $('#table-detalle-tipocargo tr.selected').removeClass('selected');
    $(e).addClass('selected');
    
    /*$.ajax({
      url:"{{url('backoffice/0/cargo/show_descuentodecuotas')}}",
      type:'GET',
      data: {
          idcredito_cronograma : id,
      },
      success: function (res){
        $('#cont_descuentosdecuotas').html(res.html);
      }
    }) */
  }
  
  function show_select_descuentodecuotas(e) {
    
    let id = $(e).attr('data-valor-columna');
    $('#table-detalle-descuentodecuotas tr.selected').removeClass('selected');
    $(e).addClass('selected');
    
  }
   
  function load_create_descuentocuota(){
    let url = "{{ url('backoffice/'.$tienda->id) }}/cargo/create?view=registrar&idcredito={{$credito->id}}";
    modal({ route: url,  size: 'modal-sm' })
  }
   
  function load_delete_descuentocuota(){
    let idcredito_descuentocuota = $('#table-detalle-tipocargo > tbody > tr.selected').attr('data-valor-columna');

    if(idcredito_descuentocuota == "" || idcredito_descuentocuota == undefined ){
      alert('Debe de seleccionar un cuota de descuento.');   
      return false;
    }

    let url = "{{ url('backoffice/'.$tienda->id) }}/cargo/"+idcredito_descuentocuota+"/edit?view=eliminar";
    modal({ route: url,  size: 'modal-sm' })
  }
</script>     