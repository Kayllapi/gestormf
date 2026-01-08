@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Confirmar Cierre de Caja',
    'botones'=>[
        'atras:/'.$tienda->id.'/cajaapertura: Ir Atras'
    ]
])
<div id="carga-cierrecaja">
    @if($s_aperturacierre->config_prestamo_tipocierrecaja==1)
       <div class="row">
          <div class="col-md-6">
             <label>Responsable de Asiganción</label>
             <select id="idusersresponsable" disabled>
                 <option></option>
                 @foreach($users as $value)
                 <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
                 @endforeach
             </select>
             <label>Responsable de Cierre</label>
             <select id="idusers" disabled>
                 <option></option>
                 @foreach($users as $value)
                 <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
                 @endforeach
             </select>
          </div>
          <div class="col-md-6">
            @if($s_aperturacierre->config_sistema_moneda_usar==1)
                     <label>Total Caja en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre }}" id="montocierre" disabled/>
            @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                     <label>Total Caja en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" id="montocierre_dolares" disabled/>
            @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
             <div class="row">
                 <div class="col-md-12">
                     <label>Total Caja en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre }}" id="montocierre" disabled/>
                 </div>
                 <div class="col-md-12">
                     <label>Total Caja en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" id="montocierre_dolares" disabled/>
                 </div>
             </div>
            @endif
          </div>
        </div>
        <div class="list-single-main-wrapper fl-wrap">
            <div class="breadcrumbs gradient-bg fl-wrap">
              <span>Detalle</span>
            </div>
        </div>
        @include('app.sistema_efectivo',['tienda'=>$tienda,'idaperturacierre'=>$s_aperturacierre->id])
        <div class="row">
          <div class="col-md-6">
            <form action="javascript:;" 
                  onsubmit="callback({
                      route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura/{{ $s_aperturacierre->id }}',
                      method: 'PUT',
                      carga: '#carga-cierrecaja',
                      data:{
                          view: 'confirmarrecepcioncierre'
                      }
                  },
                  function(resultado){
                      location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}';                                                                            
                  },this)">
                  <button type="submit" class="btn mx-btn-post" style="margin-top: 5px;"><i class="fa fa-check"></i> Recepcionar Cierre de Caja</button>
            </form>  
          </div>
          <div class="col-md-6">
              <form action="javascript:;" 
                    onsubmit="callback({
                      route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura/{{ $s_aperturacierre->id }}',
                      method: 'PUT',
                      carga: '#carga-cierrecaja',
                      data:{
                          view: 'anularenviocierre'
                      }
                  },
                  function(resultado){
                      location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}';                                                                            
                  },this)">
                  <button type="submit" class="btn mx-btn-post" style="margin-top: 5px;"><i class="fa fa-ban"></i> Rechazar Cierre de Caja</button>
              </form>  
          </div>
        </div>
    @elseif($s_aperturacierre->config_prestamo_tipocierrecaja==3)
        <div class="row">
          <div class="col-md-6">
             <label>Responsable de Asiganción</label>
             <select id="idusersresponsable" disabled>
                 <option></option>
                 @foreach($users as $value)
                 <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
                 @endforeach
             </select>
             <label>Responsable de Cierre</label>
             <select id="idusers" disabled>
                 <option></option>
                 @foreach($users as $value)
                 <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
                 @endforeach
             </select>
          </div>
          <div class="col-md-6">
            
            @if($s_aperturacierre->config_sistema_moneda_usar==1)
                     <label>Total Caja en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre }}" id="montocierre" disabled/>
                     <label>Total Caja cerrado en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido }}" id="montocierre" disabled/>
                @if($s_aperturacierre->montocierre<$s_aperturacierre->montocierre_recibido)
                     <label>Total Sobrante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido-$s_aperturacierre->montocierre, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @elseif($s_aperturacierre->montocierre>$s_aperturacierre->montocierre_recibido)
                     <label>Total Faltante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre-$s_aperturacierre->montocierre_recibido, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @endif
            @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                     <label>Total Caja en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" id="montocierre_dolares" disabled/>
                     <label>Total Caja cerrado en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido_dolares }}" id="montocierre_dolares" disabled/>
                @if($s_aperturacierre->montocierre_dolares<$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Sobrante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido_dolares-$s_aperturacierre->montocierre_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @elseif($s_aperturacierre->montocierre_dolares>$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Faltante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_dolares-$s_aperturacierre->montocierre_recibido_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @endif
            @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
             <div class="row">
                 <div class="col-md-6">
                     <label>Total en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre }}" id="montocierre" disabled/>
                 </div>
                 <div class="col-md-6">
                     <label>Total en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" id="montocierre_dolares" disabled/>
                 </div>
             </div>
             <div class="row">
                 <div class="col-md-6">
                     <label>Total cerrado en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido }}" id="montocierre" disabled/>
                 </div>
                 <div class="col-md-6">
                     <label>Total cerrado en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido_dolares }}" id="montocierre_dolares" disabled/>
                 </div>
             </div>
             <div class="row">
                 <div class="col-md-6">
                @if($s_aperturacierre->montocierre<$s_aperturacierre->montocierre_recibido)
                     <label>Total Sobrante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido-$s_aperturacierre->montocierre, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @elseif($s_aperturacierre->montocierre>$s_aperturacierre->montocierre_recibido)
                     <label>Total Faltante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre-$s_aperturacierre->montocierre_recibido, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @endif
                 </div>
                 <div class="col-md-6">
                @if($s_aperturacierre->montocierre_dolares<$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Sobrante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido_dolares-$s_aperturacierre->montocierre_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @elseif($s_aperturacierre->montocierre_dolares>$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Faltante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_dolares-$s_aperturacierre->montocierre_recibido_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @endif
                 </div>
             </div>
            @endif
          </div>
        </div>
        <div class="list-single-main-wrapper fl-wrap">
            <div class="breadcrumbs gradient-bg fl-wrap">
              <span>Detalle</span>
            </div>
        </div>
        <div class="tabs-container" id="tab-billetaje">
            <ul class="tabs-menu">
                <li class="current"><a href="#tab-billetaje-1">Detalle</a></li>
                <li><a href="#tab-billetaje-2">Billetaje</a></li>
            </ul>
            <div class="tab">
                <div id="tab-billetaje-1" class="tab-content" style="display: block;">
                    @include('app.sistema_efectivo',['tienda'=>$tienda,'idaperturacierre'=>$s_aperturacierre->id])
                </div>
                <div id="tab-billetaje-2" class="tab-content" style="display: none;">
                    @if($s_aperturacierre->config_sistema_moneda_usar==1)
                    <div class="row">
                        <div class="col-md-6">
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th colspan="3" style="text-align: center;">SOLES</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <th colspan="3" class="td-tipo">Billetes</th> 
                                </tr> 
                                <?php $cierretotalbilletessoles = 0; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',200)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletessoles = $cierretotalbilletessoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',100)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletessoles = $cierretotalbilletessoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',50)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletessoles = $cierretotalbilletessoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',20)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletessoles = $cierretotalbilletessoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',10)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletessoles = $cierretotalbilletessoles+$s_aperturacierrebilletaje->total; ?>
                                <tr class="warning">
                                  <td colspan="2" class="td-subtotal">Total Billetes</td> 
                                  <td class="td-subtotal" style="text-align: left;">{{number_format($cierretotalbilletessoles, 2, '.', '')}}</td> 
                                </tr>
                               </tbody>
                          </table>
                        </div>
                        <div class="col-md-6">
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th colspan="3" style="text-align: center;">SOLES</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <th colspan="3" class="td-tipo">Monedas</th> 
                                </tr> 
                                <?php $cierretotalmonedassoles = 0; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',5)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',2)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',1)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion','0.50')->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.2)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.1)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <tr class="warning">
                                  <td colspan="2" class="td-subtotal">Total Monedas</td> 
                                  <td id="cierretotalmonedassoles" class="td-subtotal" style="text-align: left;">{{number_format($cierretotalmonedassoles, 2, '.', '')}}</td> 
                                </tr>
                               </tbody>
                          </table>
                        </div>
                    </div>
        
                    <div class="mensaje-success" style="background-color: #096315;">
                      Total Efectivo S/:
                      <div id="cierretotalefectivosoles" style="font-size: 20px;font-weight: bold;">{{number_format($cierretotalbilletessoles+$cierretotalmonedassoles, 2, '.', '')}}</div> 
                    </div>
                    @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                    <div class="row">
                        <div class="col-md-6">
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th colspan="3" style="text-align: center;">DOLARES</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th colspan="3" class="td-tipo">Billetes</th> 
                              </tr> 
                                <?php $cierretotalbilletesdolares = 0; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',100)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',50)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',20)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',10)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',5)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',2)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',1)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <tr class="warning">
                                  <td colspan="2" class="td-subtotal">Total Billetes</td> 
                                  <td class="td-subtotal">{{number_format($cierretotalbilletesdolares, 2, '.', '')}}</td> 
                                </tr>
                               </tbody>
                          </table>
                        </div>
                        <div class="col-md-6">
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th colspan="3" style="text-align: center;">DOLARES</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <th colspan="3" class="td-tipo">Monedas</th> 
                                </tr> 
                                <?php $cierretotalmonedasdolares = 0; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.5)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedasdolares = $cierretotalmonedasdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.25)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedasdolares = $cierretotalmonedasdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.10)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedasdolares = $cierretotalmonedasdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.05)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedasdolares = $cierretotalmonedasdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.01)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedasdolares = $cierretotalmonedasdolares+$s_aperturacierrebilletaje->total; ?>
                                <tr class="warning">
                                  <td colspan="2" class="td-subtotal">Total Monedas</td> 
                                  <td id="cierretotalmonedasdolares" class="td-subtotal">{{number_format($cierretotalmonedasdolares, 2, '.', '')}}</td> 
                                </tr>
                               </tbody>
                          </table>
                        </div>
                    </div>
        
                    <div class="mensaje-success" style="background-color: #096315;">
                      Total Efectivo $:
                      <div id="cierretotalefectivodolares" style="font-size: 20px;font-weight: bold;">{{number_format($cierretotalbilletesdolares+$cierretotalmonedasdolares, 2, '.', '')}}</div> 
                    </div>
                    @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
                    <div class="row">
                        <div class="col-md-6">
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th colspan="3" style="text-align: center;">SOLES</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <th colspan="3" class="td-tipo">Billetes</th> 
                                </tr> 
                                <?php $cierretotalbilletessoles = 0; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',200)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletessoles = $cierretotalbilletessoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',100)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletessoles = $cierretotalbilletessoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',50)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletessoles = $cierretotalbilletessoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',20)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletessoles = $cierretotalbilletessoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',10)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletessoles = $cierretotalbilletessoles+$s_aperturacierrebilletaje->total; ?>
                                <tr class="warning">
                                  <td colspan="2" class="td-subtotal">Total Billetes</td> 
                                  <td class="td-subtotal" style="text-align: left;">{{number_format($cierretotalbilletessoles, 2, '.', '')}}</td> 
                                </tr>
                                <tr>
                                  <th colspan="3" class="td-tipo">Monedas</th> 
                                </tr> 
                                <?php $cierretotalmonedassoles = 0; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',5)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',2)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',1)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.5)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.2)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.1)->where('idmoneda',1)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedassoles = $cierretotalmonedassoles+$s_aperturacierrebilletaje->total; ?>
                                <tr class="warning">
                                  <td colspan="2" class="td-subtotal">Total Monedas</td> 
                                  <td id="cierretotalmonedassoles" class="td-subtotal" style="text-align: left;">{{number_format($cierretotalmonedassoles, 2, '.', '')}}</td> 
                                </tr>
                               </tbody>
                          </table>
              
                          <div class="mensaje-success" style="background-color: #096315;">
                            Total Efectivo 
                            S/.:
                            <div id="cierretotalefectivosoles" style="font-size: 20px;font-weight: bold;">{{number_format($cierretotalbilletessoles+$cierretotalmonedassoles, 2, '.', '')}}</div> 
                          </div>
                        </div>
                        <div class="col-md-6">
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th colspan="3" style="text-align: center;">DOLARES</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th colspan="3" class="td-tipo">Billetes</th> 
                              </tr> 
                                <?php $cierretotalbilletesdolares = 0; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',100)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',50)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',20)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',10)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',5)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',2)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',1)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalbilletesdolares = $cierretotalbilletesdolares+$s_aperturacierrebilletaje->total; ?>
                                <tr class="warning">
                                  <td colspan="2" class="td-subtotal">Total Billetes</td> 
                                  <td class="td-subtotal">{{number_format($cierretotalbilletesdolares, 2, '.', '')}}</td> 
                                </tr>
                                <tr>
                                  <th colspan="3" class="td-tipo">Monedas</th> 
                                </tr> 
                                <?php $cierretotalmonedasdolares = 0; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.5)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedasdolares = $cierretotalmonedasdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.25)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedasdolares = $cierretotalmonedasdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.10)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedasdolares = $cierretotalmonedasdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.05)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedasdolares = $cierretotalmonedasdolares+$s_aperturacierrebilletaje->total; ?>
                                <?php $s_aperturacierrebilletaje = DB::table('s_aperturacierrebilletaje')->where('idtienda',$tienda->id)->where('idaperturacierre',$s_aperturacierre->id)->where('denominacion',0.01)->where('idmoneda',2)->first(); ?>
                                <tr>
                                  <td class="td-text" style="text-align: right;">{{$s_aperturacierrebilletaje->denominacion}}</td> 
                                  <td class="td-text" style="text-align: center;">{{$s_aperturacierrebilletaje->cantidad}}</td> 
                                  <td class="td-text">{{$s_aperturacierrebilletaje->total}}</td>
                                </tr>
                                <?php $cierretotalmonedasdolares = $cierretotalmonedasdolares+$s_aperturacierrebilletaje->total; ?>
                                <tr class="warning">
                                  <td colspan="2" class="td-subtotal">Total Monedas</td> 
                                  <td id="cierretotalmonedasdolares" class="td-subtotal">{{number_format($cierretotalmonedasdolares, 2, '.', '')}}</td> 
                                </tr>
                               </tbody>
                          </table>
                              <div class="mensaje-success" style="background-color: #096315;">
                                Total Efectivo $:
                                <div id="cierretotalefectivodolares" style="font-size: 20px;font-weight: bold;">{{number_format($cierretotalbilletesdolares+$cierretotalmonedasdolares, 2, '.', '')}}</div> 
                              </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <style>
          .td-tipo {
            border: 0px !important;
            background-color: #1176c7;
            color: #fff;
            padding: 10px !important;
            text-align: center;
          }
          .td-subtotal {
            background-color: #11c529;
            color: #fff;
            padding: 10px !important;
            text-align: right;
          }
          .td-total {
            border: 0px !important;
            background-color: #096315;
            color: #fff;
            padding: 10px !important;
            text-align: right;
          }
          .td-text {
            padding: 10px !important;
          } 
        </style>
        <?php $valid_modal_confirmar = 0; ?>     
        @if($s_aperturacierre->config_sistema_moneda_usar==1)
                @if($s_aperturacierre->montocierre<$s_aperturacierre->montocierre_recibido)
                    <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> El "Total Caja cerrado en Soles" es mayor a "Total Caja en Soles".
                    </div>
                    <?php $valid_modal_confirmar = 1; ?>  
                @elseif($s_aperturacierre->montocierre>$s_aperturacierre->montocierre_recibido)
                    <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> El "Total Caja cerrado en Soles" es menor a "Total Caja en Soles".
                    </div>
                    <?php $valid_modal_confirmar = 1; ?> 
                @endif
        @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                @if($s_aperturacierre->montocierre_dolares<$s_aperturacierre->montocierre_recibido_dolares)
                    <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> El "Total Caja cerrado en Dolares" es mayor a "Total Caja en Dolares".
                    </div>
                    <?php $valid_modal_confirmar = 1; ?> 
                @elseif($s_aperturacierre->montocierre_dolares>$s_aperturacierre->montocierre_recibido_dolares)
                    <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> El "Total Caja cerrado en Dolares" es menor a "Total Caja en Dolares".
                    </div>
                    <?php $valid_modal_confirmar = 1; ?> 
                @endif
        @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
                @if($s_aperturacierre->montocierre<$s_aperturacierre->montocierre_recibido)
                    <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> El "Total Caja cerrado en Soles" es mayor a "Total Caja en Soles".
                    </div>
                    <?php $valid_modal_confirmar = 1; ?> 
                @elseif($s_aperturacierre->montocierre>$s_aperturacierre->montocierre_recibido)
                    <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> El "Total Caja cerrado en Soles" es menor a "Total Caja en Soles".
                    </div>
                    <?php $valid_modal_confirmar = 1; ?> 
                @endif
                @if($s_aperturacierre->montocierre_dolares<$s_aperturacierre->montocierre_recibido_dolares)
                    <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> El "Total Caja cerrado en Dolares" es mayor a "Total Caja en Dolares".
                    </div>
                    <?php $valid_modal_confirmar = 1; ?> 
                @elseif($s_aperturacierre->montocierre_dolares>$s_aperturacierre->montocierre_recibido_dolares)
                    <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> El "Total Caja cerrado en Dolares" es menor a "Total Caja en Dolares".
                    </div>
                    <?php $valid_modal_confirmar = 1; ?> 
                @endif
        @endif
        <div class="row">
          <div class="col-md-6">
            @if($valid_modal_confirmar==1)
            <a href="javascript:;" id="modal-faltantesobrante" class="btn mx-btn-post" style="margin-bottom: 5px;"><i class="fa fa-check"></i> Recepcionar Cierre de Caja</a>
            @else
            <form action="javascript:;" 
                  onsubmit="callback({
                      route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura/{{ $s_aperturacierre->id }}',
                      method: 'PUT',
                      carga: '#carga-cierrecaja',
                      data:{
                          view: 'confirmarrecepcioncierre'
                      }
                  },
                  function(resultado){
                      location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}';                                                                            
                  },this)">
                  <button type="submit" class="btn mx-btn-post"><i class="fa fa-check"></i> Recepcionar Cierre de Caja</button>
            </form>  
            @endif
          </div>
          <div class="col-md-6">
              <form action="javascript:;" 
                    onsubmit="callback({
                      route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura/{{ $s_aperturacierre->id }}',
                      method: 'PUT',
                      carga: '#carga-cierrecaja',
                      data:{
                          view: 'anularenviocierre'
                      }
                  },
                  function(resultado){
                      location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}';                                                                            
                  },this)">
                  <button type="submit" class="btn mx-btn-post"><i class="fa fa-ban"></i> Rechazar Cierre de Caja</button>
              </form>  
          </div>
        </div>
    @endif    
</div>                        
@endsection
@section('htmls')
<!--  modal faltantesobrante --> 
<div class="main-register-wrap modal-faltantesobrante" id="modal-faltantesobrante">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Recepcionar Cierre de Caja</h3>
            <div class="mx-modal-cuerpo">
                <div class="custom-form">
                <div class="profile-edit-container">
                    <form action="javascript:;"
                          onsubmit="callback({
                              route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura/{{ $s_aperturacierre->id }}',
                              method: 'PUT',
                              data:{
                                  view: 'confirmarrecepcioncierre',
                                  estado: 'confirmarcierre'
                              }
                          },
                          function(resultado){
                              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}'; 
                          },this)">
            
            @if($s_aperturacierre->config_sistema_moneda_usar==1)
                <label>Total Caja en Soles</label>
                <input type="number" value="{{ $s_aperturacierre->montocierre }}" disabled/>
                <label>Total Caja cerrado en Soles</label>
                <input type="number" value="{{ $s_aperturacierre->montocierre_recibido }}" disabled/>
                @if($s_aperturacierre->montocierre<$s_aperturacierre->montocierre_recibido)
                     <label>Total Sobrante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido-$s_aperturacierre->montocierre, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @elseif($s_aperturacierre->montocierre>$s_aperturacierre->montocierre_recibido)
                     <label>Total Faltante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre-$s_aperturacierre->montocierre_recibido, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @endif
            @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                <label>Total Caja en Dolares</label>
                <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" disabled/>
                <label>Total Caja cerrado en Dolares</label>
                <input type="number" value="{{ $s_aperturacierre->montocierre_recibido_dolares }}" disabled/>
                @if($s_aperturacierre->montocierre_dolares<$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Sobrante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido_dolares-$s_aperturacierre->montocierre_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @elseif($s_aperturacierre->montocierre_dolares>$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Faltante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_dolares-$s_aperturacierre->montocierre_recibido_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @endif
            @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
                <label>Total Caja en Soles</label>
                <input type="number" value="{{ $s_aperturacierre->montocierre }}" disabled/>
                <label>Total Caja cerrado en Soles</label>
                <input type="number" value="{{ $s_aperturacierre->montocierre_recibido }}" disabled/>
                <label>Total Caja en Dolares</label>
                <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" disabled/>
                <label>Total Caja cerrado en Dolares</label>
                <input type="number" value="{{ $s_aperturacierre->montocierre_recibido_dolares }}" disabled/>
                @if($s_aperturacierre->montocierre<$s_aperturacierre->montocierre_recibido)
                     <label>Total Sobrante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido-$s_aperturacierre->montocierre, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @elseif($s_aperturacierre->montocierre>$s_aperturacierre->montocierre_recibido)
                     <label>Total Faltante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre-$s_aperturacierre->montocierre_recibido, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @endif
                @if($s_aperturacierre->montocierre_dolares<$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Sobrante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido_dolares-$s_aperturacierre->montocierre_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @elseif($s_aperturacierre->montocierre_dolares>$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Faltante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_dolares-$s_aperturacierre->montocierre_recibido_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @endif
            @endif
                    <label>Motivo *</label>
                    <textarea id="motivo_cierre" cols="30" rows="10" onkeyup="texto_mayucula(this)"></textarea>
                    <button type="submit" class="btn mx-btn-post"><i class="fa fa-check"></i> Confirmar Cierre de Caja</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal faltantesobrante --> 
@endsection
@section('subscripts')
<script>
  
  modal({click:'#modal-faltantesobrante'});
  tab({click:'#tab-detalledeldia'});
  tab({click:'#tab-billetaje'});
$("#idcaja").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$s_aperturacierre->s_idcaja}}).trigger("change");

$("#idusers").select2({
    placeholder: "---  Seleccionar ---"
}).val({{$s_aperturacierre->idusersrecepcion}}).trigger("change");

$("#idusersresponsable").select2({
    placeholder: "-- Seleccionar --"
}).val({{$s_aperturacierre->idusersresponsable}}).trigger("change");

</script>
@endsection