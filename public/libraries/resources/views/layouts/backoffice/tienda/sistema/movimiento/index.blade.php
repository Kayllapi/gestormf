@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Movimientos</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th width="90px">Código</th>
        <th>Tipo</th>
        <th>Concepto</th>
        <th>Descripción</th>
        <th>Monto</th>
        <th>Fecha de registro</th>
        <th>Responsable</th>
        <th width="10px">Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['codigo','select:tipo/1=Egreso,2=Ingreso','concepto','descripcion','','date:fecharegistro'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento')
    ])
    <tbody>
        @foreach($s_movimientos as $value)
        <tr <?php echo $idapertura==$value->s_idaperturacierre?'style="background-color:#ffeea7;"':''?>>
          <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{$value->conceptomovimientotipo}}</td>
          <td>{{$value->conceptomovimientonombre}}</td>
          <td>{{$value->concepto}}</td>
          <td>{{$value->monedasimbolo}} {{$value->monto}}</td>
          <td>{{ date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A") }}</td>
          <td>{{$value->responsablenombre}}</td>
          <td>
            @if($value->idestadomovimiento==1)
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div>
            @elseif($value->idestadomovimiento==2)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
            @elseif($value->idestadomovimiento==3)
              <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fas fa-ban"></i> Anulado</span></div> 
            @endif
          </td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                  @if($value->idestadomovimiento==1)
                      <!--li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento/'.$value->id.'/edit?view=confirmar') }}"><i class="fa fa-check"></i> Confirmar</a></li>
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li-->
                  @elseif($value->idestadomovimiento==2)
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento/'.$value->id.'/edit?view=ticket') }}"><i class="fa fa-receipt"></i> Ticket</a></li>
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
                      @if($idapertura==$value->s_idaperturacierre)
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento/'.$value->id.'/edit?view=anular') }}"><i class="fa fa-ban"></i> Anular</a></li>
                      @endif
                  @elseif($value->idestadomovimiento==3)
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
                  @endif
                </ul>
            </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $s_movimientos->links('app.tablepagination', ['results' => $s_movimientos]) }}
@endsection
