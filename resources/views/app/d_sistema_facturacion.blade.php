<?php
$agencias = DB::table('s_agencia')->where('s_agencia.idtienda', $idtienda)->get();
$tipocomprobantes = DB::table('s_tipocomprobante')->get();
$monedas = DB::table('s_moneda')->get();
?>
<div class="row">
    <div class="col-sm-6">
            <label>Persona *</label>
            <div class="row">
               <div class="col-md-9">
                  <select id="facturacion_cliente">
                      @if(isset($idcliente))
                          <?php $cliente = DB::table('users')->whereId($idcliente)->first(); ?>
                          <option value="{{ $cliente->id }}">
                            @if($cliente->idtipopersona==1 or $cliente->idtipopersona==2)
                            {{ $cliente->apellidos }}, {{ $cliente->nombre }}
                            @else
                            {{ $cliente->nombre }}
                            @endif
                          </option>
                          @elseif(configuracion($tienda->id,'facturacion_clientepordefecto')['resultado']=='CORRECTO')
                          <?php $cliente = DB::table('users')->whereId(configuracion($tienda->id,'facturacion_clientepordefecto')['valor'])->first(); ?>
                          <option value="{{ $cliente->id }}">
                            @if($cliente->idtipopersona==1 or $cliente->idtipopersona==2)
                            {{ $cliente->apellidos }}, {{ $cliente->nombre }}
                            @else
                            {{ $cliente->nombre }}
                            @endif
                          </option>
                      @else
                          <option></option>
                      @endif
                  </select>
               </div>
               <div class="col-md-3">
                  <a href="javascript:;" id="modal-registrarcliente" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
               </div>
            </div>
            <label>Dirección</label>
            @if(isset($direccion))
            <input type="text" id="facturacion_cliente_direccion" value="{{$direccion}}"/>
            @else
            <input type="text" id="facturacion_cliente_direccion"/>
            @endif
            <label>Ubicación (Ubigeo)</label>
            <select id="facturacion_cliente_ubigeo">
                @if(isset($idubigeo))
                    <?php $ubigeo = DB::table('ubigeo')->whereId($idubigeo)->first(); ?>
                    <option value="{{ $ubigeo->id }}">{{ $ubigeo->nombre }}</option>
                @else
                    <option></option>
                @endif
            </select>
    </div>
    <div class="col-sm-6">
        <label>Agencia *</label>
        <select id="facturacion_agencia">
          <option></option>
          @foreach ($agencias as $value)
          <option value="{{ $value->id }}">{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
          @endforeach
        </select>
        <label>Moneda *</label>
        <select id="facturacion_moneda">
          <option></option>
          @foreach ($monedas as $value)
          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
          @endforeach
        </select>
        <label>Tipo de Comprobante *</label>
        <select id="facturacion_tipocomprobante">
          <option></option>
          @foreach ($tipocomprobantes as $value)
          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
          @endforeach
        </select>
    </div>
</div>