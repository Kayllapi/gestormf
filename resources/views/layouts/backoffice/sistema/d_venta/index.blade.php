@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Ventas</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/create?view=ventarapida') }}"><i class="fa fa-angle-right"></i> Venta rapida</a></a>
    </div>
</div>
 <div class="table-responsive">
            <table class="table" id="tabla-contenido">
                <thead class="thead-dark">
                  <tr>
                    <th width="90px">Código</th>
                    <th>Comprobante</th>
                    <th>Total</th>
                    @if($tienda->idcategoria==30)
                    <th>Mesa</th>
                    @endif
                    <th>Cliente</th>
                    <th>Fecha Registro</th>
                    <th>Fecha Vendida</th>
                    <th width="10px">Comprobante</th>
                    <th width="10px">Estado</th>
                    <th width="10px"></th>
                  </tr>
                </thead>
                <?php
                $buscar = ['codigo','comprobante','','','cliente','date:fecharegistro','date:fechavendida'];
                ?>
                @include('app.tablesearch',[
                    'searchs'=>$buscar,
                    'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/venta')
                ])
                <tbody>
                    @foreach($s_venta as $value)
                    <tr <?php echo $idapertura==$value->s_idaperturacierre?'style="background-color:#ffeea7;"':''?> >
                      <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                      <td>{{ $value->nombreComprobante }}</td>
                      <td>
                        {{$value->totalredondeado}}
                      </td>
                      @if($tienda->idcategoria==30)
                      <?php
                      $ordenpedido = DB::table('s_comida_ordenpedidoventa')
                                ->join('s_comida_ordenpedido','s_comida_ordenpedido.id','s_comida_ordenpedidoventa.s_idcomida_ordenpedido')
                                ->where('s_comida_ordenpedidoventa.idtienda', $tienda->id)
                                ->where('s_comida_ordenpedidoventa.idestado', 1)
                                ->where('s_comida_ordenpedidoventa.s_idventa', $value->id)
                                ->first();
                      $numeromesa = '';
                      if($ordenpedido!=''){
                          $numeromesa = 'Mesa '.str_pad($ordenpedido->numeromesa, 2, "0", STR_PAD_LEFT);
                      }
                      ?>
                      <th>{{$numeromesa}}</th>
                      @endif
                      <td>{{$value->cliente}}</td>
                      <td>{{ ($value->s_idestado==2 or $value->s_idestado==3 or $value->s_idestado==4) ? date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A") : '---' }}</td>
                      <td>{{ ($value->s_idestado==3 or $value->s_idestado==4) ? date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i:s A") : '---' }}</td>
                      <td>
                        <?php
                        $s_facturacionboletafacturas = DB::table('s_facturacionboletafactura')
                        ->where('s_facturacionboletafactura.idventa',$value->id)
                        ->orderBy('s_facturacionboletafactura.venta_correlativo','asc')
                        ->get();
                        ?>
                        @foreach($s_facturacionboletafacturas as $valuefac)
                        <div class="td-badge" style="padding-top: 1px;padding-bottom: 1px;"><span class="badge badge-pill badge-primary">{{$valuefac->venta_serie}} - {{ str_pad($valuefac->venta_correlativo, 8, "0", STR_PAD_LEFT) }}</span></div> 
                        @endforeach
                      </td>
                      <td>
                        @if($value->s_idestado==1)
                          <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
                        @elseif($value->s_idestado==2)
                          <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fas fa-sync-alt"></i> Confirmado</span></div> 
                        @elseif($value->s_idestado==3)
                          <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Vendido</span></div>
                        @elseif($value->s_idestado==4)
                          <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>
                        @endif
                      </td>
                      <td>
                        <div class="header-user-menu menu-option" id="menu-opcion">
                            <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                            <ul>
                                @if($value->s_idestado==1)
                                    <!--li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=editar') }}">
                                      <i class="fa fa-edit"></i> Editar
                                    </a></li-->
                                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=ticket') }}"><i class="fa fa-receipt"></i> Ticket de Venta</a></li>
                                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=eliminar') }}">
                                      <i class="fa fa-trash"></i> Eliminar
                                    </a></li>
                                @elseif($value->s_idestado==2)
                                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=confirmar') }}">
                                      <i class="fa fa-check"></i> Confirmar
                                    </a></li>
                                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=ticket') }}"><i class="fa fa-receipt"></i> Ticket de Venta</a></li>
                                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=rechazar') }}">
                                      <i class="fa fa-ban"></i> Rechazar
                                    </a></li>
                                @elseif($value->s_idestado==3)
                                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
                                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=facturar') }}"><i class="fa fa-receipt"></i> Comprobante</a></li>
                                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=ticket') }}"><i class="fa fa-receipt"></i> Ticket de Venta</a></li>

                                    @if($idapertura==$value->s_idaperturacierre)
                                    <!--li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=anular') }}"><i class="fa fa-ban"></i> Anular</a></li-->
                                    @endif
                                    <!--li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=comprobante') }}"><i class="fa fa-file-pdf"></i> PDF</a></li-->

                                @elseif($value->s_idestado==4)
                                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
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
@section('subscripts')
<script>

</script>
@endsection
