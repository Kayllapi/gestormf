@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Detalle de Cierre de Caja',
    'botones'=>[
        'atras:/'.$tienda->id.'/cajaapertura: Ir Atras'
    ]
])
 
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
                     <label>Total en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre }}" id="montocierre" disabled/>
            @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                     <label>Total en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" id="montocierre_dolares" disabled/>
            @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
             <div class="row">
                 <div class="col-md-12">
                     <label>Total en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre }}" id="montocierre" disabled/>
                 </div>
                 <div class="col-md-12">
                     <label>Total en Dolares</label>
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
    @elseif($s_aperturacierre->config_prestamo_tipocierrecaja==2)
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
                     <label>Total en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre }}" id="montocierre" disabled/>
                     <label>Monto cerrado en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido }}" id="montocierre" disabled/>
            @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                     <label>Total en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" id="montocierre_dolares" disabled/>
                     <label>Monto cerrado en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido_dolares }}" id="montocierre_dolares" disabled/>
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
                     <label>Monto cerrado en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido }}" id="montocierre" disabled/>
                 </div>
                 <div class="col-md-6">
                     <label>Monto cerrado en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido_dolares }}" id="montocierre_dolares" disabled/>
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
                     <label>Total en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre }}" id="montocierre" disabled/>
                     <label>Monto cerrado en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido }}" id="montocierre" disabled/>
            @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                     <label>Total en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" id="montocierre_dolares" disabled/>
                     <label>Monto cerrado en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido_dolares }}" id="montocierre_dolares" disabled/>
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
                     <label>Monto cerrado en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido }}" id="montocierre" disabled/>
                 </div>
                 <div class="col-md-6">
                     <label>Monto cerrado en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido_dolares }}" id="montocierre_dolares" disabled/>
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
                                <?php $s_aperturacierrebilletaj