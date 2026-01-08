<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$idtienda.'/garantiasnoprendario/'.$garantias->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              idtienda: {{$tienda->id}}
          }
      },
      function(resultado){
          $('#cont-ultimamodificacion').addClass('d-none');
          $('#alert-ultimamodificacion').html('');
          lista_garantias_cliente({{ $garantias->idcliente }});
      },this)" id="form-editar-garantia">
    <input type="hidden" id="idresponsable_modificado">
    <div class="modal-header">
        <h5 class="modal-title">Garantia No Prendaria</h5>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-sm-12 col-md-12 d-none">
          <label>Cliente</label>
          <select class="form-control" id="idcliente" disabled>
            <option></option>
          </select>
        </div>


        <div class="col-sm-12 col-md-4">
          <label>Tipo Garantia No Prendaria</label>
          <select class="form-control" id="idtipo_garantia_noprendaria">
            <option></option>
            @foreach($tipo_garantia_noprendaria as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-sm-12 col-md-4">
          <label>Sub Tipo I</label>
          <select class="form-control" id="idsubtipo_garantia_noprendaria">
            <option></option>
          </select>
        </div>
        <div class="col-sm-12 col-md-4">
          <label>Sub Tipo II</label>
          <select class="form-control" id="idsubtipo_garantia_noprendaria_ii">
            <option></option>
          </select>
        </div>
        </div>
        <div class="row">
        <div class="col-sm-6">
          <label>Descripción de garantía en Propuesta</label>
          <textarea class="form-control" id="descripcion" cols="30" rows="5">{{ $garantias->descripcion }}</textarea>
        </div>
        </div>
        <div class="row">
        <div class="col-sm-12 col-md-4">
          <label>Valor de mercado (S/)</label>
          <input type="number" step="any" id="valor_mercado" class="form-control" value="{{ $garantias->valor_mercado }}">
        </div>
        <div class="col-sm-12 col-md-4 garantia-preferida">
          <label>Valor comercial (Tasado) (S/)</label>
          <input type="number" step="any" id="valor_comercial" class="form-control" value="{{ $garantias->valor_comercial }}">
        </div>
        <div class="col-sm-12 col-md-4 garantia-preferida">
          <label>Valor de realización (Tasado) (S/)</label>
          <input type="number" step="any" id="valor_realizacion" class="form-control" value="{{ $garantias->valor_realizacion }}">
        </div>

      </div>
          <div class="mb-1 mt-2">
            <span class="badge d-block">CRÉDITOS PROPIOS</span>
          </div>
              <table class="table">
                <thead>
                  <tr>
                    <th width="10px">Nro</th>
                    <th>DNI - Apellidos y Nombres</th>
                    <th width="80px">Cuenta</th>
                    <th width="80px">Desembolso</th>
                    <th width="80px">Saldo</th>
                  </tr>
                </thead>
                <tbody>
            <?php $i=1 ?>
            @foreach($propios as $value)
                  <?php
                        $credito_descuentocuotas = DB::table('credito_descuentocuota')
                              ->where('credito_descuentocuota.idcredito',$value->idcredito)
                              ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                              ->first();
                        $total_descuento_capital = 0; 
                        $total_descuento_interes = 0; 
                        $total_descuento_comision = 0; 
                        $total_descuento_cargo = 0;  
                        $total_descuento_penalidad = 0; 
                        $total_descuento_tenencia = 0; 
                        $total_descuento_compensatorio = 0; 
                        $total_descuento_total = 0; 
                        if($credito_descuentocuotas){
                            if(1000>=$credito_descuentocuotas->numerocuota_fin){
                                $total_descuento_capital = $credito_descuentocuotas->capital;
                                $total_descuento_interes = $credito_descuentocuotas->interes;
                                $total_descuento_comision = $credito_descuentocuotas->comision;
                                $total_descuento_cargo = $credito_descuentocuotas->cargo;
                                $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                                $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                                $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                                $total_descuento_total = $credito_descuentocuotas->total;
                            }
                        }
                
                        $cronograma = select_cronograma(
                            $tienda->id,
                            $value->idcredito,
                            $value->idforma_credito,
                            $value->modalidadproductocredito,
                            1000,
                            $total_descuento_capital,
                            $total_descuento_interes,
                            $total_descuento_comision,
                            $total_descuento_cargo,
                            $total_descuento_penalidad,
                            $total_descuento_tenencia,
                            $total_descuento_compensatorio
                        );
                        ?>
                  <tr>
                    <td style="text-align: center;">{{ $i }}</td>
                    <td>{{ $value->identificacion }} - {{ $value->nombrecompleto }}</td>
                    <td>
                      @if($value->cuenta!=0)
                      C{{ str_pad($value->cuenta, 8, '0', STR_PAD_LEFT)  }}
                      @else
                      EN PROCESO
                      @endif
                    </td>
                    <td style="text-align: right;">S/. {{ $value->monto_solicitado }}</td>
                    <td style="text-align: right;">S/. {{ $cronograma['saldo_capital'] }}</td>
                  </tr>
            <?php $i++ ?>
            @endforeach
                </tbody>
              </table>
          <div class="mb-1 mt-2">
            <span class="badge d-block">CRÉDITOS AVALADOS</span>
          </div>
              <table class="table">
                <thead>
                  <tr>
                    <th width="10px">Nro</th>
                    <th>DNI - Apellidos y Nombres</th>
                    <th width="80px">Cuenta</th>
                    <th width="80px">Desembolso</th>
                    <th width="80px">Saldo</th>
                  </tr>
                </thead>
                <tbody>
            <?php $i=1 ?>
            @foreach($avales as $value)
                  <?php
                        $credito_descuentocuotas = DB::table('credito_descuentocuota')
                              ->where('credito_descuentocuota.idcredito',$value->idcredito)
                              ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                              ->first();
                        $total_descuento_capital = 0; 
                        $total_descuento_interes = 0; 
                        $total_descuento_comision = 0; 
                        $total_descuento_cargo = 0;  
                        $total_descuento_penalidad = 0; 
                        $total_descuento_tenencia = 0; 
                        $total_descuento_compensatorio = 0; 
                        $total_descuento_total = 0; 
                        if($credito_descuentocuotas){
                            if(1000>=$credito_descuentocuotas->numerocuota_fin){
                                $total_descuento_capital = $credito_descuentocuotas->capital;
                                $total_descuento_interes = $credito_descuentocuotas->interes;
                                $total_descuento_comision = $credito_descuentocuotas->comision;
                                $total_descuento_cargo = $credito_descuentocuotas->cargo;
                                $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                                $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                                $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                                $total_descuento_total = $credito_descuentocuotas->total;
                            }
                        }
                
                        $cronograma = select_cronograma(
                            $tienda->id,
                            $value->idcredito,
                            $value->idforma_credito,
                            $value->modalidadproductocredito,
                            1000,
                            $total_descuento_capital,
                            $total_descuento_interes,
                            $total_descuento_comision,
                            $total_descuento_cargo,
                            $total_descuento_penalidad,
                            $total_descuento_tenencia,
                            $total_descuento_compensatorio
                        );
                        ?>
                  <tr>
                    <td style="text-align: center;">{{ $i }}</td>
                    <td>{{ $value->identificacion }} - {{ $value->nombrecompleto }}</td>
                    <td>
                      @if($value->cuenta!=0)
                      C{{ str_pad($value->cuenta, 8, '0', STR_PAD_LEFT)  }}
                      @else
                      EN PROCESO
                      @endif
                    </td>
                    <td style="text-align: right;">S/. {{ $value->monto_solicitado }}</td>
                    <td style="text-align: right;">S/. {{ $cronograma['saldo_capital'] }}</td>
                  </tr>
            <?php $i++ ?>
            @endforeach
                </tbody>
              </table>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form> 
<script>
  $('#btn-delete-garantia').css("display","inline-block");
  @if($garantia_credito)
    $('#form-editar-garantia').find('select').attr('disabled',true);
    $('#form-editar-garantia').find('input').attr('disabled',true);
    $('#form-editar-garantia').find('textarea').attr('disabled',true);
    $('#btn-autorizar-garantia').addClass("d-none");
    $('#btn-delete-garantia').css("display","none");
  
  @else
    $('#btn-autorizar-garantia').removeClass("d-none");
  @endif
  $('#form-editar-garantia').find('select').attr('disabled',true);
    $('#form-editar-garantia').find('input').attr('disabled',true);
    $('#form-editar-garantia').find('textarea').attr('disabled',true);
  function autorizar_edicion(){
    $('#form-editar-garantia').find('select').removeAttr('disabled');
    $('#form-editar-garantia').find('input').removeAttr('disabled');
    $('#form-editar-garantia').find('textarea').removeAttr('disabled');

  }
  
  
  $('#alert-garantia-1').removeClass("d-none");
  $('#alert-garantia-2').removeClass("d-none");
  $('#alert-garantia-3').removeClass("d-none");
  
  $('#cont-ultimamodificacion').removeClass("d-none");
  $('#alert-ultimamodificacion').html('{{ $garantias->responsablenombrecliente }}');
  
  $('#btn-delete-garantia').removeClass("d-none");
  function eliminar_garantianoprendaria(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/garantiasnoprendario/'.$garantias->id.'/edit?view=eliminar')}}",  size: 'modal-sm' });  
  }
  function modificar_garantianoprendaria(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/garantiasnoprendario/'.$garantias->id.'/edit?view=modificar')}}",  size: 'modal-sm' });  
  }
  
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente', val: '{{ $garantias->idcliente }}' });
  @include('app.nuevosistema.select2',['input'=>'#idtipo_garantia_noprendaria']);
  $("#idtipo_garantia_noprendaria").on("change", function(e) {
    
    if(e.currentTarget.value == 1){
      $('.garantia-preferida').removeClass('d-none');
    }else{
      $('.garantia-preferida').addClass('d-none');
    }
    $('#idsubtipo_garantia_noprendaria').html('');
    $('#idsubtipo_garantia_noprendaria_ii').html('');
    carga_subtipo1(e.currentTarget.value);
  }).val('{{ $garantias->idtipo_garantia_noprendaria }}').trigger('change');;
  
  function carga_subtipo1(idtipo_garantia_noprendaria){
    $.ajax({
      url:"{{url('backoffice/0/garantiasnoprendario/show_subtipo_garantia_noprendaria')}}",
      type:'GET',
      data: {
          idtipo_garantia_noprendaria : idtipo_garantia_noprendaria
      },
      success: function (res){
        let option_select = `<option></option>`;
        $.each(res, function( key, value ) {
          option_select += `<option value="${value.id}">${key+1}.- ${value.nombre}</option>`;
        });
        $('#idsubtipo_garantia_noprendaria').html(option_select);
        sistema_select2({ input:'#idsubtipo_garantia_noprendaria'});
        $("#idsubtipo_garantia_noprendaria").val('{{ $garantias->idsubtipo_garantia_noprendaria }}').trigger('change');

      }
    })
  }
  $("#idsubtipo_garantia_noprendaria").on("change", function(e) {
    $('#idsubtipo_garantia_noprendaria_ii').html('');
    carga_subtipo2(e.currentTarget.value );
  });
  
  function carga_subtipo2(idsubtipo_garantia_noprendaria){
    $.ajax({
      url:"{{url('backoffice/0/garantiasnoprendario/show_subtipo_garantia_noprendaria_ii')}}",
      type:'GET',
      data: {
          idsubtipo_garantia_noprendaria : idsubtipo_garantia_noprendaria
      },
      success: function (res){
        let option_select = `<option></option>`;
        $.each(res, function( key, value ) {
          option_select += `<option value="${value.id}">${key+1}.- ${value.nombre}</option>`;
        });
        $('#idsubtipo_garantia_noprendaria_ii').html(option_select);
        sistema_select2({ input:'#idsubtipo_garantia_noprendaria_ii'});
        $("#idsubtipo_garantia_noprendaria_ii").val('{{ $garantias->idsubtipo_garantia_noprendaria_ii }}').trigger('change');

      }
    })
  }
</script>     