<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'config_facturacion','id'=>$configuracion['idfacturacion']])>
        <div class="row">
          <div class="col-sm-6">
                <label>Cliente por Defecto</label>
                <select id="idclientepordefecto">
                    @if($configuracion['clientepordefecto']!=null)
                    <option value="{{ $configuracion['idclientepordefecto'] }}">{{ $configuracion['clientepordefecto'] }}</option>
                    @else
                    <option></option>
                    @endif
                </select>
                <label>Empresa por Defecto</label>
                <select id="idempresapordefecto">
                    <option></option>
                    @foreach($agencias as $value)
                    <option value="{{ $value->id }}">{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                    @endforeach
                </select>
                <label>Comprobante por Defecto</label>
                <select id="idcomprobantepordefecto">
                    <option></option>
                    @foreach($comprobantes as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
          </div>
          <div class="col-sm-6">
                <label>Moneda por defecto *</label>
                <select id="idmonedapordefecto">
                    <option value=""></option>
                    @foreach($monedas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>IGV (%) *</label>
                <input type="number" value="{{ $configuracion['igv']!=null?$configuracion['igv']:'0' }}" id="igv" step="0.01" min="0">
                <label>Ancho de Ticket (centimetro) *</label>
                <input type="number" value="{{ $configuracion['anchoticket']!=null?$configuracion['anchoticket']:'0' }}" id="anchoticket" step="0.01" min="0">
          </div>
        </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>  
<script>
    $("#idclientepordefecto").select2({
        @include('app.select2_cliente')
    });
  
    @if($configuracion['idmonedapordefecto']!=null)
        $("#idmonedapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ $configuracion['idmonedapordefecto'] }}).trigger("change");    
    @else
        $("#idmonedapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif
  
    @if($configuracion['idempresapordefecto']!=null)
        $("#idempresapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
            allowClear: true
        }).val({{ $configuracion['idempresapordefecto'] }}).trigger("change");    
    @else
        $("#idempresapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
            allowClear: true
        });
    @endif

    @if($configuracion['idcomprobantepordefecto']!=null)
        $("#idcomprobantepordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
            allowClear: true
        }).val({{ $configuracion['idcomprobantepordefecto'] }}).trigger("change");   
    @else
        $("#idcomprobantepordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
            allowClear: true
        });
    @endif
</script>