@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Nota de Credito</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionresumendiario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
 <div class="profile-edit-container">
     <div class="custom-form">
         <div class="row">
            <div class="col-md-12"> 
              <div class="col-md-6">
                  <label>Agencia</label>
                  <input type="text"  value="{{$facturacionresumen->emisor_ruc}}-{{$facturacionresumen->emisor_nombrecomercial}}" disabled/>   
                  <label>Correlativo</label>
                  <input type="text" value="{{ $facturacionresumen->resumen_correlativo }}" disabled>
              </div>
            <div class="col-sm-6">
                <label>Fecha de Generación</label>
                <input type="text" value="{{ $facturacionresumen->resumen_fechageneracion }}" disabled>
                <label>Fecha de Comunicación</label>
                <input type="text" value="{{ $facturacionresumen->resumen_fecharesumen }}" disabled>
            </div>
            </div>
         </div>
         <div class="table-responsive">
                <table class="table" id="tabla-contenido">
                    <thead class="thead-dark">
                        <tr>
                            <th width="15%">Serie</th> 
                            <th>Documento-Cliente</th>
                            <th width="60px">OP. Gravadas</th>
                            <th width="110px">P. Unitario</th>
                            <th width="110px">P. Total</th> 
                            <th width="10px"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($facturacionresumendetalle as $value)
                       <?php
                          $facturacion = DB::table('s_facturacionboletafactura')->where('s_facturacionboletafactura.id', $value->idfacturacionboletafactura)->first();
                       ?>
                            <tr style="background-color: #a53b93;color: #fff;height: 40px;">
                            <td>{{ $value->serienumero }}</td>
                            <td>{{ $facturacion->cliente_numerodocumento }} - {{ $facturacion->cliente_razonsocial }}</td>
                            <td>{{ $value->operacionesgravadas }}</td>
                            <td>{{ $value->montoigv }}</td>
                            <td>{{ $value->total }}</td>
                            <td></td>
                           </tr>
                     @endforeach
                     </tbody>
                </table>
         </div>

   </div>
</div>          
@endsection
@section('subscripts')
@endsection