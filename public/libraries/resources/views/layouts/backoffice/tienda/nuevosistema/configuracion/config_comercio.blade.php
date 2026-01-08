<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'config_comercio','id'=>$configuracion['idcomercio']])>
        <div class="row">
          <div class="col-sm-6">
                <label>Tipo de entrega por Defecto</label>
                <select id="idtipoentregapordefecto">
                    <option></option>
                    @foreach($tipoentregas as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Estado de Stock *</label>
                <select id="estadostock">
                    <option></option>
                    <option value="1">Habilitado</option>
                    <option value="2">Desabilitado</option>
                </select>
                <label>Nivel de Venta *</label>
                <select id="nivelventa">
                    <option></option>
                    <option value="1">1.- (Confirmar Pedido, Realizar Venta)</option>
                    <option value="2">2.- (Realizar Pedido, Confirmar Pedido, Realizar Venta)</option>
                </select>
          </div>
          <div class="col-sm-6">
                <label>Estado de Venta por Defecto *</label>
                <select id="estadoventa">
                    <option></option>
                </select>
                <label>Estado de Unidad Medida *</label>
                <select id="estadounidadmedida">
                    <option value="1">Habilitado</option>
                    <option value="2">Desabilitado</option>
                </select>
                <label>Estado de Descuentos *</label>
                <select id="estadodescuento">
                    <option value="1">Habilitado</option>
                    <option value="2">Desabilitado</option>
                </select>
          </div>
        </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>  
<script>
  
    @if($configuracion['idtipoentregapordefecto']!=null)
        $("#idtipoentregapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
            allowClear: true
        }).on("change", function(e) {
            $('#cont-tipo-delivery-info').css('display','none');
            $('#cont-tipo-delivery-mapa').css('display','none');
            $('#cont-costoenvio').css('display','none');
            if(e.currentTarget.value == 2) {
                $('#cont-tipo-delivery-info').css('display','block');
                $('#cont-tipo-delivery-mapa').css('display','block');
                $('#cont-costoenvio').css('display','block');
            }
        }).val({{ $configuracion['idtipoentregapordefecto'] }}).trigger("change");   
    @else
        $("#idtipoentregapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
            allowClear: true
        }).on("change", function(e) {
            $('#cont-tipo-delivery-info').css('display','none');
            $('#cont-tipo-delivery-mapa').css('display','none');
            $('#cont-costoenvio').css('display','none');
            if(e.currentTarget.value == 2) {
                $('#cont-tipo-delivery-info').css('display','block');
                $('#cont-tipo-delivery-mapa').css('display','block');
                $('#cont-costoenvio').css('display','block');
            }
        });
    @endif
  
    @if($configuracion['estadostock']!=null)
        $("#estadostock").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ $configuracion['estadostock'] }}).trigger("change");   
    @else
        $("#estadostock").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
  
    @if($configuracion['nivelventa']!=null)
        $("#nivelventa").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).on("change", function(e) {
            if(e.currentTarget.value==1){
                $('#estadoventa').html('<option></option><option value="2">Confirmar Pedido</option><option value="3">Realizar Venta</option>');
            }else if(e.currentTarget.value==2){
                $('#estadoventa').html('<option></option><option value="1">Realizar Pedido</option><option value="2">Confirmar Pedido</option>');
            }
        }).val({{ $configuracion['nivelventa'] }}).trigger("change");   
    @else
        $("#nivelventa").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).on("change", function(e) {
            if(e.currentTarget.value==1){
                $('#estadoventa').html('<option></option><option value="2">Confirmar Pedido</option><option value="3">Realizar Venta</option>');
            }else if(e.currentTarget.value==2){
                $('#estadoventa').html('<option></option><option value="1">Realizar Pedido</option><option value="2">Confirmar Pedido</option>');
            }
        });
    @endif  
  
    @if($configuracion['estadoventa']!=null)
        $("#estadoventa").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ $configuracion['estadoventa'] }}).trigger("change");   
    @else
        $("#estadoventa").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
  
    @if($configuracion['estadounidadmedida']!=null)
        $("#estadounidadmedida").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ $configuracion['estadounidadmedida'] }}).trigger("change");   
    @else
        $("#estadounidadmedida").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
  
    @if($configuracion['estadodescuento']!=null)
        $("#estadodescuento").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ $configuracion['estadodescuento'] }}).trigger("change");   
    @else
        $("#estadodescuento").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif

</script>