@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Productos Vencidos</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio') }}"><i class="fa fa-angle-left"></i> Ir a Inicio</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
          <th>Código</th>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Marca</th>
          <th>U. Medida</th>
          <th>Precio</th>
          <th>Vencimiento</th>
          <th>Vencimiento de Producto</th>
          <th>Alerta</th>
          <th>Alerta de Vencimiento</th>
      </tr>
    </thead>
    <tbody>
        <?php $fechaactual  = new DateTime(Carbon\Carbon::now()->format("Y-m-d")); ?>
        @foreach($productos as $value)
            <?php
            // calcular diferencia de fecha
            $fechavencimiento = new DateTime($value->fechavencimiento);
            $intvl = $fechaactual->diff($fechavencimiento);
            $ano = $intvl->y>0 ? $intvl->y.($intvl->y==1 ? " año".($intvl->m==0 ?" y ":", "):" años".($intvl->m==0 ?" y ":", ")):'';
            $mes = $intvl->m>0 ? $intvl->m.($intvl->m==1 ? " mes y ":" meses y "):'';
            $dia = $intvl->d>0 ? $intvl->d.($intvl->d==1 ? " día ":" días "):'';
            
      
            $tiempo = '';
            $style = '';
            if($fechaactual<=$fechavencimiento){
                $tiempo = 'Falta: '.$ano.$mes.$dia; 
            }else{
                $tiempo = 'Vencido: '.$ano.$mes.$dia; 
            }
      
            // actualizar fecha vencimeinto, rentando con alerta de vencimiento
            $alertavencimiento = date("Y-m-d",strtotime(date($value->fechavencimiento)."- ".$value->alertavencimiento." days")); 
            $alertavencimiento = new DateTime($alertavencimiento);
            $intvl = $fechaactual->diff($alertavencimiento);
            $ano = $intvl->y>0 ? $intvl->y.($intvl->y==1 ? " año".($intvl->m==0 ?" y ":", "):" años".($intvl->m==0 ?" y ":", ")):'';
            $mes = $intvl->m>0 ? $intvl->m.($intvl->m==1 ? " mes y ":" meses y "):'';
            $dia = $intvl->d>0 ? $intvl->d.($intvl->d==1 ? " día ":" días "):'';
            
      
            $alerta = 'Falta: '.$ano.$mes.$dia; 
            $style = '';
            if($fechaactual>$alertavencimiento){
                $alerta = 'Vencido: '.$ano.$mes.$dia; 
                $style = 'class="select_vencimiento"';
            }

            ?>
        <tr <?php echo $style ?>>
          <td>{{ $value->codigo }}</td>
          <td style="padding: 5px;padding-top: 12px;padding-bottom: 12px;">{{ $value->nombre }}</td>
          <td>{{ $value->categorianombre }}</td>
          <td>{{ $value->marcanombre }}</td>
          <td>{{ $value->unidadmedida }}</td>
          <td>{{ $value->precioalpublico }}</td>
          <td>{{ date_format(date_create($value->fechavencimiento),"d/m/Y") }}</td>
          <td>{{ $tiempo }}</td>
          <td>{{ date_format($alertavencimiento,"d/m/Y") }} ({{ $value->alertavencimiento>0?'-'.$value->alertavencimiento:$value->alertavencimiento }} días)</td>
          <td>{{ $alerta }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $productos->links('app.tablepagination', ['results' => $productos]) }}
<style>
  .select_vencimiento {
      background-color:#ffcccb;
  }
</style>
@endsection