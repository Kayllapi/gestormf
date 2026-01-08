@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="row">
  <div class="col-md-12">
      <div class="list-single-main-wrapper fl-wrap">
          <div class="breadcrumbs gradient-bg fl-wrap">
            <span>Inicio</span>
          </div>
      </div> 
      @if(configuracion($tienda->id,'sistema_imagenfondosistema')['resultado']=='CORRECTO')
      <div class="list-single-main-media fl-wrap" style="margin-bottom: 5px;">
          <img  src="{{ url('public/backoffice/tienda/'.$tienda->id.'/imagensistema/'.configuracion($tienda->id,'sistema_imagenfondosistema')['valor']) }}" class="respimg" alt="">
      </div>
      @else
      <div class="list-single-main-media fl-wrap" style="margin-bottom: 5px;">
            <img src="https://www.kayllapi.com/public/backoffice/sistema/sitioweb/login/banner-inicio1.jpg" class="respimg" alt="">
            <a href="https://www.youtube.com/embed/lR-8hkr3q-M" class="promo-link gradient-bg image-popup"><i class="fa fa-play"></i><span>Ver Video</span></a>
        </div>
      @endif
  </div>
  <div class="col-md-5">
      <!--div class="list-single-main-wrapper fl-wrap">
          <div class="breadcrumbs gradient-bg fl-wrap">
            <span>Ventas de {{Carbon\Carbon::now()->format("F")}} de {{Carbon\Carbon::now()->format("Y")}}</span>
          </div>
      </div>
      <canvas id="myChart" width="400" height="250"></canvas-->
    <!--div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Productos por vencer</span>
        </div>
    </div>
    <div class="table-responsive">
    <table class="table" id="tabla-contenido">
          <thead class="thead-dark">
            <tr>
              <th width="15%">Código</th>
              <th>Producto</th>
              <th width="60px">Cant.</th>
              <th width="100px">Vencimiento</th>
            </tr>
          </thead>
          <tbody>
          <?php $total = 0; ?>
          @foreach($productosvencidos as $value)
            <?php
            $style="background-color: #008cea;color: #fff;height: 40px;";
            $fechavencimiento = Carbon\Carbon::now()->subDay($value->alertavencimiento)->format("Y-m-d");
            if($value->fechavencimiento<=$fechavencimiento){
                $style="background-color: #fd5656;color: #fff;height: 40px;";
            }
            ?>
            <tr style="<?php echo $style; ?>">
            <td>{{ $value->productocodigo }}</td>
            <td>{{ $value->producto }}</td>
            <td></td>
            <td>{{ $value->fechavencimiento }}</td>
            </tr>
          @endforeach
          </tbody>
      </table>
    </div>
    {{ $productosvencidos->links('app.tablepagination', ['results' => $productosvencidos]) }}
  </div-->
</div>
<?php
$cant_dias = Carbon\Carbon::now()->format("d");
?> 
@endsection
@section('subscripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.2.1/dist/chart.min.js"></script>
<script>
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
          <?php
          for($i=1;$i<=$cant_dias;$i++){
              echo "'".$i."',";
          }
          ?>],
        datasets: [{
            label: 'Ventas',
            data: [
            <?php
            $fecha_inicio = Carbon\Carbon::now()->format("Y-m").'-01';
            for($i=1;$i<=$cant_dias;$i++){
                $total_ventas = DB::table('s_venta')
                    ->where('s_venta.s_idusuarioresponsable',Auth::user()->id)
                    ->where('s_venta.idtienda',$tienda->id)
                    ->where('s_venta.fechaconfirmacion','>=',$fecha_inicio.' 00:00:00')
                    ->where('s_venta.fechaconfirmacion','<=',$fecha_inicio.' 23:59:59')
                    ->where('s_venta.s_idestado',3)
                    ->sum('s_venta.totalredondeado');
                echo "'".$total_ventas."',";
                $fecha_inicio = date("Y-m-d", strtotime($fecha_inicio . " + 1 day"));
            }
            ?>  
            ],
            backgroundColor: [
                '#03a9f440',
                '#03a9f440',
                '#03a9f440',
                '#03a9f440',
                '#03a9f440',
                '#03a9f440',
            ],
            borderColor: [
                '#03A9F4',
                '#03A9F4',
                '#03A9F4',
                '#03A9F4',
                '#03A9F4',
                '#03A9F4'
            ],
            borderWidth: 1
        },{
            label: 'Compras',
            data: [
            <?php
            $fecha_inicio = Carbon\Carbon::now()->format("Y-m").'-01';
            for($i=1;$i<=$cant_dias;$i++){
                $total_compras = DB::table('s_compra')
                    ->where('s_compra.s_idusuarioresponsable',Auth::user()->id)
                    ->where('s_compra.idtienda',$tienda->id)
                    ->where('s_compra.fechaconfirmacion','>=',$fecha_inicio.' 00:00:00')
                    ->where('s_compra.fechaconfirmacion','<=',$fecha_inicio.' 23:59:59')
                    ->where('s_compra.s_idestado',2)
                    ->sum('s_compra.total');
                echo "'".$total_compras."',";
                $fecha_inicio = date("Y-m-d", strtotime($fecha_inicio . " + 1 day"));
            }
            ?>  
            ],
            backgroundColor: [
                '#fb065645',
                '#fb065645',
                '#fb065645',
                '#fb065645',
                '#fb065645',
                '#fb065645',
            ],
            borderColor: [
                '#fb0656',
                '#fb0656',
                '#fb0656',
                '#fb0656',
                '#fb0656',
                '#fb0656'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection