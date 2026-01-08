@include('app.nuevosistema.tabla',[
    'tabla' => 'tabla-'.$_GET['name_modulo'],
    'tbody' => [
        ['data' => 'id'],
        ['data' => 'buscador','type'=>'text'],
        ['data' => 'titulo','type'=>'title'],
        ['data' => 'nombre','type'=>'text'],
        ['data' => 'titulo2','type'=>'title'],
        ['data' => 'nombre2'],
        ['data' => 'option','type'=>'btn'],
    ]
])
<form action="{{ url('backoffice/tienda/nuevosistema/'.$tienda->id.'/reportekardex') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
         <div class="col-md-12">
            <label>Producto</label>
            <select id="idproducto" name="idproducto">
                <?php
                $s_producto = DB::table('s_producto')->whereId(isset($_GET['idproducto'])?$_GET['idproducto']:'0')->first();
                $value = '';
                $name = '';
                if($s_producto!=''){
                    $value = $s_producto->id;
                    $name = $s_producto->nombre.' / '.$s_producto->precioalpublico;
                }
                ?>
                <option value="{{$value}}">{{$name}}</option>
            </select>
         </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-6">
                <a href="javascript:;" onclick="reporte('reporte')" class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
            </div>
            <div class="col-md-6">
                  <a href="javascript:;" onclick="reporte('excel')"class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-file-excel"></i>  Exportar Excel</a>
            </div>
          </div>
        </div>
         
       </div>
    </div>
</form>
@if($productosaldos->total()==0)
  @if(isset($_GET['idproducto']))
   <div class="mensaje-warning">
      <i class="fa fa-warning"></i> No hay ningun producto con ese filtro, ingrese nuevo filtro de reporte.
    </div>
  @endif
@else
<div class="table-responsive">
<table class="table" id="tabla-contenido" style="margin-bottom: 5px;">
    <thead class="thead-dark">
      <tr style="text-align: center;">
        <th>Fecha / Hora</th>
        <th>Concepto</th>
        <th>Producto</th>
        <th>Cant.</th>
        <th>P. Unitario</th>
        <th>P. Total</th>
        <th>Saldo</th>
        <th>Restante</th>
      </tr>
    </thead>
    <tbody>
        @foreach($productosaldos as $value)
        <?php
        $class_dato = 'td_dato';
        $class_es = 'td_es';
        $class_saldo = 'td_saldo';
        if($value->concepto=='SALDO INICIAL'){
            $class_dato = 'td_reset_dato';
            $class_es = 'td_reset_es';
            $class_saldo = 'td_reset_saldo';
        }
        ?>
        <tr>
          @if($value->concepto=='SALDO INICIAL')
          <td class="{{$class_dato}}" colspan="3">{{ $value->concepto }}</td>
          @else
          <td class="{{$class_dato}}">{{ $value->concepto=='SALDO INICIAL'?'': date_format(date_create($value->fecharegistro), 'd/m/Y h:i:s A') }}</td>
          <td class="{{$class_dato}}">{{ $value->concepto }}</td>
          <?php $list_produtc = explode('/<br>/',$value->producto) ?>
          <td class="{{$class_dato}}">
            @if(count($list_produtc)>1)
            @for($i=1;$i<count($list_produtc);$i++)
            {{ $list_produtc[$i] }}<br>
            @endfor
            @else
            {{$value->producto}}
            @endif
          </td>
          @endif
          <td class="{{$class_es}}">{{ $value->cantidad }} - {{ $value->cantidadrestante }}</td>
          <td class="{{$class_es}}">{{ $value->preciounitario }}</td>
          <td class="{{$class_es}}">{{ $value->preciototal }}</td>
          <td class="{{$class_saldo}}">{{ $value->saldo_cantidad }}</td>
          <td class="{{$class_saldo}}">{{ $value->saldo_cantidadrestante }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endif
<style>
  .td_reset_dato{
    background-color: #838ea9;
    color: white;
    text-align: right;
    padding: 10px !important;
  }
  .td_reset_es{
    background-color: #838ea9;
    color: white;
    text-align: right;
    padding: 10px !important;
  }
  .td_reset_saldo{
    background-color: #838ea9;
    color: white;
    text-align: right;
    padding: 10px !important;
  }
  .td_dato{
    background-color: #bec2cc;
    text-align: left;
    padding: 10px !important;
  }
  .td_es{
    background-color: #99d1f7;
    text-align: right;
    padding: 10px !important;
  }
  .td_saldo {
    background-color: #9ee0a1;
    text-align: right;
    padding: 10px !important;
  }
</style>
<script>
      function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportekardex')}}?'+
                                'tipo='+tipo+
                                '&idproducto='+($('#idproducto').val()!=null?$('#idproducto').val():'');
    }
$("#idproducto").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportekardex/showlistarproducto')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar Producto --",
    allowClear: true,
    minimumInputLength: 2,
    templateResult: function (state) {
        if (!state.id) {
            return state.text;
        }
        var urlimagen = '{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}';
        if(state.imagen!=null){
            urlimagen = '{{ url('public/backoffice/tienda') }}/'+state.idtienda+'/producto/40/'+state.imagen;
        }
        return $('<div>'+
                 '<div style="background-image: url('+urlimagen+');'+
                            'background-repeat: no-repeat;'+
                            'background-size: contain;'+
                            'background-position: center;'+
                            'width: 40px;'+
                            'height: 40px;'+
                            'float: left;'+
                            'margin-right: 5px;'+
                            'margin-top: -5px;">'+
                          '</div><div>'+state.nombre+'</div><div>'+state.unidadmedida+' - '+state.precioalpublico+'</div>');
    }
});
</script>
