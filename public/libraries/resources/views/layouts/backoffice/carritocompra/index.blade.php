@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Mis Pedidos (Carrito de compra)</span>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th width="90px">Código</th>
        <th width="100px">Comprobante</th>
        <th>Facturación - Cliente</th>
        <th>Fecha de pedido</th>
        <th>Fecha de venta</th>
        <th>Monto</th>
        <th width="10px">Entrega</th>
        <th width="10px">Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['codigo','comprobante','cliente'],
        'search_url'=> url('backoffice/carritocompra')
    ])
    <tbody>
        @foreach($s_venta as $value)
        <tr>
          <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ $value->nombreComprobante }}</td>
          <td>{{ $value->clienteidentificacion }} - {{$value->clientenombre}}</td>
          <td>{{ $value->fecharegistro }}</td>
          <td>{{ $value->s_idestado==3 ? $value->fechaconfirmacion : '---' }}</td>
          <td>
            <?php $montototal = DB::table('s_ventadetalle')->where('s_idventa',$value->id)->sum(DB::raw('CONCAT((preciounitario*cantidad)-descuento)')); ?>
            {{ number_format($montototal, 2, '.', '') }}
          </td>
          <td>
            {{ $value->tipoentreganombre }}
          </td>
          <td>
            @if($value->s_idestado==3)
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Vendido</span></div>
            @elseif($value->s_idestado==2)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> En proceso</span></div> 
            @endif
          </td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    @if($value->s_idestado==3)
                    <li><a href="{{ url('backoffice/carritocompra/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
                    <li><a href="{{ url('backoffice/carritocompra/'.$value->id.'/edit?view=ticket') }}"><i class="fa fa-receipt"></i> Ticket</a></li>
                    <li><a href="{{ url('backoffice/carritocompra/'.$value->id.'/edit?view=comprobante') }}"><i class="fa fa-file-pdf"></i>  PDF</a></li>
                    @elseif($value->s_idestado==2)
                    <li><a href="{{ url('backoffice/carritocompra/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
                    @if($value->s_idtipoentrega==2)
                    <li><a href="{{ url('backoffice/carritocompra/'.$value->id.'/edit?view=ruta') }}"><i class="fa fa-map-marker-alt"></i> Ruta</a></li>
                    @endif
                    @else
                    <li><a href="{{ url('backoffice/carritocompra/'.$value->id.'/edit?view=eliminar') }}">
                      <i class="fa fa-trash"></i> Eliminar
                    </a></li>
                    @endif   
                </ul>
            </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $s_venta->links('app.tablepagination', ['results' => $s_venta]) }} 
@endsection
