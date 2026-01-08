@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Compras</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th width="90px">Código</th>
        <th width="100px">Comprobante</th>
        <th width="110px">Correlativo</th>
        <th width="70px">Moneda</th>
        <th>Total</th>
        <th>Proveedor</th>
        <th>Fecha de registro</th>
        <th>Responsable</th>
        <th width="10px">Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['codigo','comprobante','seriecorrelativo','moneda','','proveedor','date:fecharegistro'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/compra')
    ])
    <tbody>
        @foreach($s_compra as $value)
        <tr <?php echo $idapertura==$value->s_idaperturacierre?'style="background-color:#ffeea7;"':''?>>
          <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ $value->nombreComprobante }}</td>
          <td>{{ $value->seriecorrelativo }}</td>
          <td>{{ $value->monedacodigo }}</td>
          <td>
          @if($value->totalredondeado==0)
            <?php $montototal = DB::table('s_compradetalle')->where('s_idcompra',$value->id)->sum('preciototal'); ?>
            {{ number_format($montototal, 2, '.', '') }}
          @else
            {{$value->totalredondeado}}
          @endif
          </td>
          <td>{{ $value->nombreProveedor }} {{$value->apellidoProveedor}}</td>
          <td>{{ date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A") }}</td>
          <td>{{$value->responsablenombre}}</td>
          <td>
            @if($value->s_idestado==1)
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div>
            @elseif($value->s_idestado==2)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Comprado</span></div>
            @elseif($value->s_idestado==3)
              <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fas fa-ban"></i> Anulado</span></div> 
            @endif
          </td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    @if($value->s_idestado==1)
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra/'.$value->id.'/edit?view=editar') }}">
                      <i class="fa fa-edit"></i> Editar
                    </a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra/'.$value->id.'/edit?view=eliminar') }}">
                      <i class="fa fa-trash"></i> Eliminar
                    </a></li>
                    @elseif($value->s_idestado==2)
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
                    @if($idapertura==$value->s_idaperturacierre)
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra/'.$value->id.'/edit?view=anular') }}">
                      <i class="fa fa-ban"></i> Anular
                    </a></li>
                    @endif
                    @elseif($value->s_idestado==3)
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
                    @endif 
                  
                    
                </ul>
            </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $s_compra->links('app.tablepagination', ['results' => $s_compra]) }}
@endsection
