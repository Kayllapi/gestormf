@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Editar Movimiento de Saldo</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimientosaldo') }}">
            <i class="fa fa-angle-left"></i> Ir Atras</a>
        </a>
    </div>
</div>

    <form action="javascript:;" 
          onsubmit="callback({
              route: 'backoffice/tienda/sistema/{{ $tienda->id }}/movimientosaldo/{{ $movimientosaldo->id }}',
              method: 'PUT',
              data: { 
                    view: 'editar' 
              }
          },
          function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimientosaldo') }}';
          },this)"> 
           <div class="row">
               <div class="col-sm-6">
                  <label>Tipo de Movimiento *</label>
                  <select id="idtipomovimiento">
                      <option></option>
                      @foreach($tipomovimientos as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                  </select>
                  <label>Caja *</label>
                  <select id="idcaja">
                      <option></option>
                      @foreach($cajas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                  </select>
                  <label>Moneda *</label>
                  <select id="idmoneda">
                      <option></option>
                      @foreach($monedas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                  </select>
               </div>
               <div class="col-sm-6">
                  <label>Monto *</label>
                  <input type="number" value="{{ $movimientosaldo->monto }}" id="monto" placeholder="0.00" step="0.01" min="0"/>
                  <label>Motivo *</label>
                  <input type="text" value="{{ $movimientosaldo->motivo }}" id="motivo" onkeyup="texto_mayucula(this)"/>
               </div>
           </div>
           <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
    </form>

@endsection
@section('subscripts')
<script>
    $('#idtipomovimiento').select2({
        placeholder: '--Seleccionar--',
        minimumResultsForSearch: -1
    }).val({{ $movimientosaldo->idtipomovimiento }}).trigger('change'); 
    $('#idcaja').select2({
        placeholder: '--Seleccionar--',
        minimumResultsForSearch: -1
    }).val({{ $movimientosaldo->idcaja }}).trigger('change'); 
    $('#idmoneda').select2({
        placeholder: '--Seleccionar--',
        minimumResultsForSearch: -1
    }).val({{ $movimientosaldo->idmoneda }}).trigger('change'); 
</script>
@endsection