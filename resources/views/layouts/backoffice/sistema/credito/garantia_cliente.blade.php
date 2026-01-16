<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view : 'garantia_cliente',
              garantias : listar_garantias('cliente'),
              garantias_aval : listar_garantias('aval'),
              monto_cobertura_garantia : $('#total_cobertura_cliente').text(),             
          }
      },
      function(resultado){
        $('#modal-close-garantia-cliente').click(); 
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">GARANTIAS </h5>
        <button type="button" class="btn-close" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="mb-1 mt-2">
        <span class="badge d-block">CLIENTE: <span style="font-weight: normal;">{{ $credito->nombreclientecredito }}</span></span>
      </div>
      <div class="row mt-1">
        <div class="col-sm-12">
          <table garantias class="table table-striped" id="table-garantia-cliente">
            <thead>
              <tr>
                <th width="90px">Tipo</th>
                <th>Descripción</th>
                <th width="90px">Valor Mercado</th>
                <th width="90px">Valor Comercial<br>(Tasador)</th>
                <th width="90px">Cobertura<br>(Valor Realización)</th>
                <th width="10px"></th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
        </div>
      </div>
      @if($credito->idaval != 0)
      <div class="mb-1 mt-2">
        <span class="badge d-block">AVAL: {{ $credito->nombreavalcredito }}</span>
      </div>
      <div class="row mt-1">
        <div class="col-sm-12">
          <table garantias class="table table-striped" id="table-garantia-aval">
            <thead>
              <tr>
                <th width="90px">Tipo</th>
                <th>Descripción</th>
                <th width="90px">Valor Mercado</th>
                <th width="90px">Valor Comercial<br>(Tasador)</th>
                <th width="90px">Cobertura<br>(Valor Realización)</th>
                <th width="10px"></th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      @endif
      <br>
      <span class="badge bg-success">
          <b>TOTAL COBERTURA:</b> S/. <span id="total_cobertura_cliente">{{ $credito->monto_cobertura_garantia }}</span>
      </span>
      @if($view_detalle!='false')
      <br>
      <button type="submit" class="btn btn-primary mt-3"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
      @endif
    </div>
    <div class="modal-footer">
    </div>
</form>   

<script>

 carga_garantia_table('CLIENTE','{{ $credito->idcliente }}');
 carga_garantia_table('AVAL','{{ $credito->idaval }}');
  function listar_garantias(tabla){
        let data = [];
        $("#table-garantia-"+tabla+" > tbody > tr").each(function() {
            let idgarantia = $(this).attr('idgarantia');    
            let idgarantianoprendataria = $(this).attr('idgarantianoprendataria');    
            let idcliente = $(this).attr('idcliente');    
            let descripcion = $(this).find('td[descripcion]').attr('value');
            let valor_mercado = $(this).find('td[valor_mercado]').attr('value');
            let valor_comercial = $(this).find('td[valor_comercial]').attr('value');
            let valor_realizacion = $(this).find('td[valor_realizacion]').attr('value');
            let estado_check = $(this).find('td[estado_check] > label > input:checkbox:checked').val();
            
            if(estado_check == 'on'){
              
              data.push({ 
                idgarantia: idgarantia,
                idgarantianoprendataria: idgarantianoprendataria,
                idcliente: idcliente,
                descripcion: descripcion,
                valor_mercado: valor_mercado,
                valor_comercial: valor_comercial,
                valor_realizacion: valor_realizacion,
              });
            }
            
        });
    
        return JSON.stringify(data);
  }
  function suma_garantias(){
    let monto_cobertura = 0;
    $("table[garantias] > tbody > tr").each(function() {
      let valor_realizacion = $(this).find('td[valor_realizacion]').attr('value');
      let estado_check = $(this).find('td[estado_check] > label > input:checkbox:checked').val();
      let idgarantia = parseInt($(this).attr('idgarantia'));
      if(estado_check == 'on' && idgarantia > 0){
        monto_cobertura += parseFloat(valor_realizacion);
      }
    });
    $('#total_cobertura_cliente').text(monto_cobertura.toFixed(2));
  }
  function carga_garantia_table(tipo, usuario){
    $.ajax({
      url:"{{url('backoffice/0/credito/showgarantias')}}",
      type:'GET',
      data: {
          tipo_garantia : tipo,
          idcredito : '{{ $credito->id }}',
          idcliente : usuario,
          detalle : '{{$view_detalle}}'
      },
      success: function (res){
        let tabla = tipo.toLowerCase();
        $('#table-garantia-'+tabla+' > tbody').html(res);
        plugins_popover();
      }
    })
  }
  
  
</script>