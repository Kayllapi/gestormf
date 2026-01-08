<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'inventario_activos',
              inventario: json_productos('inventario-producto'),
              inmuebles: json_productos('activos-inmuebles'),
              muebles: json_productos('activos-muebles'),
          }
      },
      function(res){
        removecarga({input:'#mx-carga'})
        $('#success-message').removeClass('d-none');
        $('#success-message').text(res.mensaje);
        setTimeout(function() {
          $('#success-message').addClass('d-none');
        }, 5000);
        lista_credito();
        load_nuevo_credito();
                
        $('#boton_imprimir').attr('disabled',false);
      },this)"> 

  
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">INVENTARIO Y ACTIVOS </h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    @php
  
      $inventario = $credito_cuantitativa_inventario ? ( $credito_cuantitativa_inventario->inventario == "" ? [] : json_decode($credito_cuantitativa_inventario->inventario) ) : [];
      $inmuebles = $credito_cuantitativa_inventario ? ( $credito_cuantitativa_inventario->inmuebles == "" ? [] : json_decode($credito_cuantitativa_inventario->inmuebles) ) : [];
      $muebles = $credito_cuantitativa_inventario ? ( $credito_cuantitativa_inventario->muebles == "" ? [] : json_decode($credito_cuantitativa_inventario->muebles) ) : [];
      
    @endphp
    <div class="modal-body modal-body-cualitativa">
      <div class="row">
        <div class="col-sm-12 col-md-5">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">AGENCIA/OFICINA:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $tienda->nombreagencia }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">CLIENTE/RAZON SOCIAL:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreclientecredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">GIRO ECONÓMICO:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombregiro_economico_evaluacion : '' }}" disabled>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_cuantitativa_inventario!=''?date_format(date_create($credito_cuantitativa_inventario->fecha),'Y-m-d') : '' }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">DNI/RUC:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->docuementocliente }}" disabled>
            </div>
          </div>
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">DNI:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $users_prestamo->dni_pareja }}" disabled>
            </div>
          </div>
          @endif
          
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">EJERCICIO:</label>
            <div class="col-sm-7">
              @if($users_prestamo->db_idforma_ac_economica!='')
                <input type="text" step="any" class="form-control" id="ejercicio_giro_economico" value="{{ $users_prestamo->db_idforma_ac_economica }}" disabled>
              @else
                <input type="text" step="any" class="form-control" id="ejercicio_giro_economico" value="{{ $users_prestamo->db_idforma_ac_economica }}" disabled>
              @endif
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-4">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">NRO SOLICITUD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">PRODUCTO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreproductocredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE CAMBIO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ configuracion($tienda->id,'tipo_cambio_dolar')['valor'] }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE CLIENTE:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->tipo_operacion_credito_nombre }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">MODALIDAD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->modalidad_credito_nombre }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">DESTINO DE CRÉDITO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->tipo_destino_credito_nombre}}" disabled>
            </div>
          </div>
        </div>


      
    </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">V. INVENTARIO Y  ACTIVOS FIJOS - NEGOCIO PRINCIPAL</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-inventario-producto" table="inventario-producto">
            <thead>
              <tr>
                <th>Inventario de Productos</th>
                <th width="80px">Unid. Med.</th>
                <th width="60px">Cantidad</th>
                <th width="100px">Precio de compra</th>
                <th width="100px">Total</th>
                @if($view_detalle!='false')
                <th width="10px"><button type="button" class="btn btn-success" onclick="agregar_producto(this)"><i class="fa fa-plus"></i></button></th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($inventario as $value)
                <tr id="{{ $value->id }}">
                <td nombre><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->nombre }}"></td>
                <td medida>
                  <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                    @foreach($unidadmedida_credito as $unidad)
                      <option value="{{ $unidad->nombre }}" {{ $unidad->nombre == $value->medida ? "selected" : "" }}>{{ $unidad->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td cantidad><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->cantidad }}"></td>
                <td precio><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->precio }}"></td>
                <td subtotalventa><input type="text" class="form-control campo_moneda" disabled value="{{ $value->subtotalventa }}"></td>
                @if($view_detalle!='false')
                <td><button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales" style="text-align:right;" colspan=4>Inventario total de productos  (S/.)</td>
                <td class="color_totales"><input type="text" id="total-inventario-producto" class="form-control campo_moneda" disabled 
                                                 value="{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_inventario : '0.00' }}"></td>
                @if($view_detalle!='false')
                <td class="color_totales"></td>
                @endif
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-activos-inmuebles" table="activos-inmuebles">
            <thead>
              <tr>
                <th>Activos Inmuebles</th>
                <th width="80px">Unid. Med.</th>
                <th width="60px">Cantidad</th>
                <th width="100px">Valor estimado</th>
                <th width="100px">Total</th>
                @if($view_detalle!='false')
                <th width="10px"><button type="button" class="btn btn-success" onclick="agregar_producto(this)"><i class="fa fa-plus"></i></button></th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($inmuebles as $value)
                <tr id="{{ $value->id }}">
                <td nombre><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->nombre }}"></td>
                <td medida>
                  <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                    @foreach($unidadmedida_credito as $unidad)
                      <option value="{{ $unidad->nombre }}" {{ $unidad->nombre == $value->medida ? "selected" : "" }}>{{ $unidad->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td cantidad><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} valida_input_vacio onkeyup="calcula_subtotales(this)" class="form-control campo_moneda color_cajatexto" value="{{ $value->cantidad }}"></td>
                <td precio><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} valida_input_vacio onkeyup="calcula_subtotales(this)" class="form-control campo_moneda color_cajatexto" value="{{ $value->precio }}"></td>
                <td subtotalventa><input type="text" class="form-control campo_moneda" disabled value="{{ $value->subtotalventa }}"></td>
                @if($view_detalle!='false')
                <td><button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales" style="text-align:right;" colspan=4>Total de activos inmuebles  (S/.)</td>
                <td class="color_totales"><input type="text" id="total-activos-inmuebles" class="form-control campo_moneda" disabled 
                                                 value="{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_inmuebles : '0.00' }}"></td>
            
                @if($view_detalle!='false')
                <td class="color_totales"></td>
                @endif
              </tr>
            </tfoot>
          </table><br>
          <table class="table table-bordered" id="table-activos-muebles" table="activos-muebles">
            <thead>
              <tr>
                <th>Activos Muebles</th>
                <th width="80px">Unid. Med.</th>
                <th width="60px">Cantidad</th>
                <th width="100px">Valor estimado (como usado)</th>
                <th width="100px">Total</th>
                @if($view_detalle!='false')
                <th width="10px"><button type="button" class="btn btn-success" onclick="agregar_producto(this)"><i class="fa fa-plus"></i></button></th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($muebles as $value)
                <tr id="{{ $value->id }}">
                <td nombre><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->nombre }}"></td>
                <td medida>
                  <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                    @foreach($unidadmedida_credito as $unidad)
                      <option value="{{ $unidad->nombre }}" {{ $unidad->nombre == $value->medida ? "selected" : "" }}>{{ $unidad->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td cantidad><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} valida_input_vacio onkeyup="calcula_subtotales(this)" class="form-control campo_moneda color_cajatexto" value="{{ $value->cantidad }}"></td>
                <td precio><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} valida_input_vacio onkeyup="calcula_subtotales(this)" class="form-control campo_moneda color_cajatexto" value="{{ $value->precio }}"></td>
                <td subtotalventa><input type="text" class="form-control campo_moneda" disabled value="{{ $value->subtotalventa }}"></td>
                @if($view_detalle!='false')
                <td><button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales" style="text-align:right;" colspan=4>Total de activos muebles (S/.)</td>
                <td class="color_totales"><input type="text" id="total-activos-muebles" class="form-control campo_moneda" disabled 
                                                 value="{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_muebles : '0.00' }}"></td>
                @if($view_detalle!='false')
                <td class="color_totales"></td>
                @endif
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      
      <div class="row mt-1">
        @if($view_detalle!='false')
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
        </div>
        @endif
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" 
                  class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_inventario_activos')}}', size: 'modal-fullscreen' })"
                  id="boton_imprimir"
                  >
            <i class="fa-solid fa-file-pdf"></i> IMPRIMIR</button>
        </div>
        <div class="col" style="flex: 1 0 0%;">
          <div id="success-message" class="alert alert-success d-none" style="text-align:left;"></div>
        </div>
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> SALIR</button>
        </div>
      </div>
</form> 
<style>
  .modal-body-cualitativa .form-check-input[type=checkbox],
  .modal-body-cualitativa .select2-container--bootstrap-5 .select2-selection {
      background-color: #ffffb5;
  }
  .form-check-input:checked {
      background-color: #585858 !important;
      border-color: #585858 !important;
  }
</style>
<script>
 
valida_input_vacio();
  $('input').on('blur', function() {
      $('#boton_imprimir').attr('disabled',true);
  });
function agregar_producto(e){
  let idtable = $(e).closest('table.table').attr('id');
  let btn_eliminar = `<button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
  let option_select = ``;
  @foreach($unidadmedida_credito as $value)
    option_select += `<option value="{{ $value->nombre }}">{{ $value->nombre }}</option>`
  @endforeach
  let id = generarIDUnico();
  let tabla = `<tr id="${id}">
                <td nombre><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto"></td>
                <td medida>
                <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                    <option></option>
                    ${option_select}
                  </select>
                </td>
                <td cantidad><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                <td precio><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                <td subtotalventa><input type="number" class="form-control campo_moneda" disabled value="0.00"></td>
                <td>${btn_eliminar}</td>
              </tr>`;
  
  $(`#${idtable} > tbody`).append(tabla);
  valida_input_vacio();
}
function eliminar_producto(e){
  let idtable = $(e).closest('table').attr('id');
  let path = $(e).closest('tr');
  path.remove();
  calcula_total(idtable);
}

function calcula_subtotales(e){
  let idtable = $(e).closest('table').attr('id');
  let path = $(e).closest('tr');
  let cantidad = parseFloat($(path).find('td[cantidad] input').val());
  let precioventa = parseFloat($(path).find('td[precio] input').val());
  let subtotalventa = cantidad * precioventa;
  $(path).find('td[subtotalventa] input').val(subtotalventa.toFixed(2));
  calcula_total(idtable);
}

function calcula_total(idtable){
  
  let table_name = $(`#${idtable}`).attr('table');
  let total_venta = 0;
  $(`#${idtable} > tbody > tr`).each(function() {
    let subtotalventa = parseFloat($(this).find('td[subtotalventa] input').val());
    total_venta += subtotalventa;
  });
  $(`#total-${table_name}`).val(total_venta.toFixed(2))
  
}

function json_productos(table){
  let data = [];
  $(`#table-${table} > tbody > tr`).each(function() {
      let id            = $(this).attr('id');
      let nombre        = $(this).find('td[nombre] input').val();
      let medida        = $(this).find('td[medida] select').val();
      let cantidad      = $(this).find('td[cantidad] input').val();
      let precio        = $(this).find('td[precio] input').val();
      let subtotalventa = $(this).find('td[subtotalventa] input').val();
      data.push({ 
          id: id,
          nombre: nombre,
          medida: medida,
          cantidad: cantidad,
          precio: precio,
          subtotalventa: subtotalventa,
      });
  });
  return JSON.stringify(data);
}
  
  
function generarIDUnico() {
    // Generar un ID único utilizando un timestamp y un número aleatorio
    var timestamp = new Date().getTime();
    var numeroAleatorio = Math.floor(Math.random() * 1000);
    var idUnico = "id" + timestamp + numeroAleatorio;
    return idUnico;
}
</script>    