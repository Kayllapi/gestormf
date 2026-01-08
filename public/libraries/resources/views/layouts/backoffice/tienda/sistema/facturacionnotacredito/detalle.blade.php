@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Nota de Credito</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotacredito') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
 <div class="profile-edit-container">
     <div class="custom-form">
         <div class="row">
            <div class="col-md-12"> 
              <div class="form-group row">
                <div class="col-sm-6">
                  <label>Cliente</label>
                    <input   type="text" value="{{$facturacionnotacredito->cliente_numerodocumento}} - {{$facturacionnotacredito->cliente_razonsocial}}" disabled>
                </div>
                <div class="col-sm-6">
                  <label>Agencia</label>
                    <input type="text" value="{{$facturacionnotacredito->emisor_ruc}}-{{$facturacionnotacredito->emisor_nombrecomercial}}" disabled/>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-sm-6">
                   <label>Dirección</label>
                    <input type="text" value="{{$facturacionnotacredito->cliente_direccion}}" disabled/> 
                </div>
                <div class="col-sm-6">
                  <label>Moneda</label>
                    @if($facturacionnotacredito->notacredito_tipomoneda=='PEN')
                      <input type="text" value="SOLES" disabled>
                    @elseif($facturacionnotacredito->notacredito_tipomoneda=='USD')
                      <input type="text" value="DOLARES" disabled>
                    @endif 
                </div>
              </div>
              <div class="form-group row">
                <div class="col-sm-6">
                  <label>Ubigeo</label>
                    <input type="text" value="{{$facturacionnotacredito->cliente_departamento}}/{{$facturacionnotacredito->cliente_provincia}}/{{$facturacionnotacredito->cliente_distrito}}" disabled/> 
                </div>
                <div class="col-sm-6">
                   <label>Motivo</label>
                    <input type="text" value="{{$facturacionnotacredito->notacredito_descripcionmotivo}}" disabled>
                </div>
              </div>    
            </div>
         </div>
         <div class="table-responsive">
                <table class="table" id="tabla-contenido">
                    <thead class="thead-dark">
                        <tr>
                           <th width="60px">Código</th>
                           <th>descripción de Producto</th>
                           <th width="60px">Cantidad</th>
                           <th width="110px">P. Unitario</th>
                           <th width="110px">P. Total</th> 
                           <th width="10px"></th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($facturacionnotacreditodetalles as $value)
                         <tr style="background-color: #a53b93;color: #fff;height: 40px;">
                           <td>{{$value->productocodigo}}</td>
                           <td>{{$value->productonombre}}</td>
                           <td>{{$value->cantidad}}</td>
                           <td>{{$value->montopreciounitario}}</td>
                           <td>{{number_format($value->montopreciounitario*$value->cantidad, 2, '.', '')}}</td> 
                           <td></td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
         </div>
       <div class="row">
              <div class="col-md-4"></div>
              <div class="col-md-4"> 
                 <label>Sub Total</label>
                  <input type="text"  value="{{$facturacionnotacredito->notacredito_valorventa}}"  disabled>
                 <label>IGV(18%)</label>
                  <input type="text" value="{{$facturacionnotacredito->notacredito_totalimpuestos}}"  disabled>
                 <label>Total</label>
                  <input type="text"  value="{{$facturacionnotacredito->notacredito_montoimpuestoventa}}"  disabled>
              </div>
              <div class="col-md-4"></div>
         </div> 
   </div>
</div>          
@endsection
@section('subscripts')
@endsection