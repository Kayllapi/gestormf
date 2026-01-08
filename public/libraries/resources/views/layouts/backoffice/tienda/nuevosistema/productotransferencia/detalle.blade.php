<style>
  th{
    padding: 13px 8px;
  }
  table:nth-child(odd){
    background:#afaeae;
  }
</style>

<table class="tabla-detalle">
    <tr>
        <th colspan="3" style="background-color: #afaeae;"width="100%">GENERAL</th> 
    <tr>
      <td width="15%">Estado de Transferencia</td>
      <td width="1px">:</td>
      @if($productotransferencia->idestado==1)
          <td>Solicitar Productos</td>
      @elseif($productotransferencia->idestado==2)
          <td>Enviar Productos</td>
      @else
                                    <td>Recepcionar Productos</td>
      @endif
    </tr>
    <tr>
      <td>Tienda de Origen</td>
      <td>:</td>
      <td>{{ $tiendas->nombre }}</td>
    </tr>
    <tr>
      <td>Tienda de destino</td>
      <td>:</td>
      <td>{{ $tienda->nombre }}</td>
    </tr>
    </tr>
</table>
  <?php $cont=1 ?>
  @foreach($detalletransferencia as $value)
       <table class="table" style="margin-bottom:8px">
         <td colspan="3">TABLA{{' '.$cont}}</td>
                  <tr>
         <td width="13%">Codigo</td>
         <td width="1px">:</td>
         <td width="500px">{{str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT)}} </td>
         <td width="13%">Enviado</td>
         <td width="1px">:</td>
         <td>{{$value->cantidadenviado }}</td>
                  </tr>
                  <tr>
                    <td width="13%">Producto</td>
                    <td width="1px">:</td>
                    <td width="500px">{{ $value->productonombre }}</td>
                    <td width="13%">Recepcionado</td>
                    <td width="1px">:</td>
                    <td width="">{{$value->cantidadrecepcion }}</td>
                  </tr>
                  <tr>
                    <td width="13%">Unidad de Medida</td>
                    <td width="1px">:</td>
                    <td width="500px">{{ $value->unidadmedidanombre }}</td>
                    <td width="13%">Motivo</td>
                    <td width="1px">:</td>
                    <td width="">{{$value->motivo }}</td>
                  </tr>
                  <tr>
                    <td width="13%">Cantidad</td>
                    <td width="1px">:</td>
                    <td width="400px">{{ $value->cantidad }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>
            </table>
<?php $cont++ ?>
  @endforeach
  
            
