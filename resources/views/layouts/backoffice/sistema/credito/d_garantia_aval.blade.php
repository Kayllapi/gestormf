<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view : 'garantia_cliente',
              garantias : listar_garantias(),
              tipo_garantia : 'AVAL'
          }
      },
      function(resultado){
        $('#modal-close-garantia-aval').click(); 
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">GARANTIAS AVAL </h5>
        <button type="button" class="btn-close" id="modal-close-garantia-aval" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="row">
        <label class="col-sm-4 col-form-label">TIPO DE GARANTIA:</label>
        <div class="col-sm-6">
          <select class="form-control" id="tipo_garantia_aval" {{ $credito->tipo_garantia_aval != '' ? 'disabled' : '' }}>
            <option value=""></option>
            <option value="GARANTIA_PREFERIDA">Garantía Preferida (G. Real)</option>
            <option value="GARANTIA_NOPREFERIDA">Garantía No Preferida (G. Personales)</option>
            <option value="SIN_GARANTIA">Sin Garantia</option>
          </select>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-sm-12">
          <table class="table table-striped" id="table-garantia-cliente">
            <thead>
              <tr>
                <th>Descripción</th>
                <th>Valor Mercado</th>
                <th>Valor Comercial(Tasador)</th>
                <th>Valor de Realización(Tasador)</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
          <button type="submit" class="btn btn-primary mt-3" {{ $credito->tipo_garantia_aval != '' ? 'disabled' : '' }}><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
        </div>
      </div>
    </div>
    <div class="modal-footer">
    </div>
</form>   

<script>
  @include('app.nuevosistema.select2',['input'=>'#tipo_garantia_aval', 'val' => $credito->tipo_garantia_aval ])
  $("#tipo_garantia_aval").on("change", function(e) {
    carga_garantia_table(e.currentTarget.value);
  }).val('{{ $credito->tipo_garantia_aval }}').trigger('change');
  function remove_tr(e){
    $(e).closest('tr').remove();
  }
  function listar_garantias(){
        let data = [];
        $("#table-garantia-cliente > tbody > tr").each(function() {
            let idgarantia = $(this).attr('idgarantia');    
            let idgarantianoprendataria = $(this).attr('idgarantianoprendataria');    
            let descripcion = $(this).find('td[descripcion] > input').val();
            let valor_mercado = $(this).find('td[valor_mercado] > input').val();
            let valor_comercial = $(this).find('td[valor_comercial] > input').val();
            let valor_realizacion = $(this).find('td[valor_realizacion] > input').val();
          
            data.push({ 
                idgarantia: idgarantia,
                idgarantianoprendataria: idgarantianoprendataria,
                descripcion: descripcion,
                valor_mercado: valor_mercado,
                valor_comercial: valor_comercial,
                valor_realizacion: valor_realizacion,
            });
        });
        return JSON.stringify(data);
    }
  function carga_garantia_table( tipo ){
    $.ajax({
      url:"{{url('backoffice/0/credito/showgarantias')}}",
      type:'GET',
      data: {
          tipo : tipo,
          tipo_garantia : 'AVAL',
          idcredito : '{{ $credito->id }}',
          idcliente : '{{ $credito->idaval }}'
      },
      success: function (res){
        $('#table-garantia-cliente > tbody').html(res);

      }
    })
  }
  
  
</script>