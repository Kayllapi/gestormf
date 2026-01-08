@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Devolución de Compras</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compradevolucion/create') }}"><i class="fa fa-angle-right"></i>Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th width="90px">Cod.Compra</th>
        <th>Cod.Impresión</th>
        <th>Total</th>
        <th>Fecha Emisión</th>
        <th>Responsable</th>
        <th>Conprobante</th>
        <th>DNI/RUC</th>
        <th>Proveedor</th>
        <th>Motivo</th>
        <th width="10px">Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    <tbody>
       @foreach($compradevolucion as $value)
        <tr>
          <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
           <td>{{ str_pad($value->codigoimpresion, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{$value->totalredondeado}}</td>
          <td>{{date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A")}}</td>
          <td>{{$value->nombreresponsable}}</td>
          <td>{{$value->comprobantenombre}}</td>
          <td>{{$value->identificacionproveedor}}</td>
          <td>{{$value->apellidosproveedor}},{{$value->nombreproveedor}}</td>
          <td>{{$value->motivo}}</td>
          <td>
            @if($value->s_idestado==1)
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
            @elseif($value->s_idestado==2)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fas fa-check"></i> Confirmado</span></div> 
            @elseif($value->s_idestado==3)
              <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>
            @endif
          </td>
           <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    @if($value->s_idestado==1)
                   <!--    <li>
                          <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compradevolucion/'.$value->id.'/edit?view=editar') }}">
                          <i class="fa fa-edit"></i> Editar
                          </a>
                       </li>
                       <li>
                         <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compradevolucion/'.$value->id.'/edit?view=eliminar') }}">
                          <i class="fa fa-trash"></i> Eliminar
                         </a>
                       </li>-->
                       <li>
                          <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compradevolucion/'.$value->id.'/edit?view=ticket') }}">
                            <i class="fa fa-receipt"></i> Ticket
                          </a>
                        </li>
                    @elseif($value->s_idestado==2)
                        <li>
                          <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compradevolucion/'.$value->id.'/edit?view=detalle') }}">
                          <i class="fa fa-list-alt"></i> Detalle
                          </a>
                        </li>
                        <li>
                          <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compradevolucion/'.$value->id.'/edit?view=ticket') }}">
                            <i class="fa fa-receipt"></i> Ticket
                          </a>
                       </li>
                        @if($idapertura==$value->s_idaperturacierre)
                        <li>
                          <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compradevolucion/'.$value->id.'/edit?view=anular') }}">
                            <i class="fa fa-ban"></i> Anular</a>
                        </li>
                        @endif
                    @elseif($value->s_idestado==3)
                        <li>
                          <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compradevolucion/'.$value->id.'/edit?view=detalle') }}">
                            <i class="fa fa-list-alt"></i> Detalle</a>
                        </li>
                    @endif   
                </ul>
            </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
       {{ $compradevolucion->links('app.tablepagination', ['results' => $compradevolucion]) }}
@endsection
