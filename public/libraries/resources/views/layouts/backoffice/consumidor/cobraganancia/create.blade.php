@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>COBRAR GANANCIA</span>
      <a class="btn btn-success" href="{{ url('backoffice/cobraganancia') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/configkayvisitastienda',
        method: 'PUT',
        data:{
            view:'editar'        
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/configkayvisitastienda') }}';            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
                <?php $planadquirido = planadquirido(Auth::user()->id); ?>
                <div class="table-responsive">
                  <table class="table">
                    <thead class="thead-dark">
                      <tr>
                        <th>Requisito</th>
                        <th width="100px"></th>
                        <th width="10px"></th>
                        <th width="30px">Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Tener un Plan de Inversión Activa</td>
                        <td>
                          <?php
                          $adquirir = '<a href="'.url('/backoffice/red').'" class="btn btn-warning"><i class="fa fa-plus"></i> Adquirir un Plan</a>';
                          $estado = '<a href="javascript:;" style="color:#00c853;font-size:25px;"><i class="fa fa-check"></i></a>';
                          ?>
                          @if($planadquirido['estado']=='NINGUNO')
                              <div class="td-badge"><span class="badge badge-pill badge-info">Ninguno</span></div>
                              <?php $estado = '<a href="javascript:;" style="color:#e80f19;font-size:25px;"><i class="fa fa-close"></i></a>'; ?>
                          @elseif($planadquirido['estado']=='PENDIENTE' or $planadquirido['estado']=='PENDIENTE_ADELANTADO')
                              <div class="td-badge"><span class="badge badge-pill badge-info">Pendiente</span></div>
                              <?php $adquirir = ''; ?>
                              <?php $estado = '<a href="javascript:;" style="color:#e80f19;font-size:25px;"><i class="fa fa-close"></i></a>'; ?>
                          @elseif($planadquirido['estado']=='CORRECTO')
                              <div class="td-badge"><span class="badge badge-pill badge-success">Correcto</span></div>
                              <?php $adquirir = ''; ?>
                          @elseif($planadquirido['estado']=='CORRECTO_ADELANTADO')
                              <div class="td-badge"><span class="badge badge-pill badge-success">Correcto</span></div>
                              <?php $adquirir = ''; ?>
                          @elseif($planadquirido['estado']=='VENCIDO')
                              <div class="td-badge"><span class="badge badge-pill badge-danger">Vencido</span></div>
                              <?php $estado = '<a href="javascript:;" style="color:#e80f19;font-size:25px;"><i class="fa fa-close"></i></a>'; ?>
                          @endif 
                        </td>
                        <td><?php echo $adquirir ?></td>
                        <td style="text-align: center;"><?php echo $estado ?></td>
                      </tr>
                      <tr>
                        <?php 
                        $directos = DB::table('red')
                             ->where('iduserspatrocinador',Auth::user()->id)
                             ->count();
                        $totalafiliados = contar_red(Auth::user()->id,0);
                        $nivel = '1';
                        $req_persona = '0';
                        if($totalafiliados>3){
                            $nivel = '2';
                            $req_persona = '1';
                        }elseif($totalafiliados>3){
                            $nivel = '3';
                            $req_persona = '3';
                        }elseif($totalafiliados>3){
                            $nivel = '4';
                            $req_persona = '6';
                        }elseif($totalafiliados>3){
                            $nivel = '5';
                            $req_persona = '10';
                        }elseif($totalafiliados>3){
                            $nivel = '6';
                            $req_persona = '15';
                        }elseif($totalafiliados>3){
                            $nivel = '7';
                            $req_persona = '21';
                        }elseif($totalafiliados>3){
                            $nivel = '8';
                            $req_persona = '28';
                        }elseif($totalafiliados>3){
                            $nivel = '9';
                            $req_persona = '36';
                        }elseif($totalafiliados>3){
                            $nivel = '10';
                            $req_persona = '45';
                        }
                         ?>
                        <td>Tener Personas Activas (Nivel {{ $nivel }} = {{ $req_persona }} Personas)</td>
                        <td><div class="td-badge"><span class="badge badge-pill badge-danger">{{ $req_persona-$directos }} Personas</span></div></td>
                        <td>
                          @if(($req_persona-$directos)>0)
                              <a href="{{url('/backoffice/red')}}" class="btn btn-warning"><i class="fa fa-plus"></i> Afiliar Personas</a>
                          @else
                          @endif
                        </td>
                        <td style="text-align: center;">
                          @if(($req_persona-$directos)>0)
                              <a href="javascript:;" style="color:#e80f19;font-size:25px;"><i class="fa fa-close"></i></a>
                          @else
                              <a href="javascript:;" style="color:#00c853;font-size:25px;"><i class="fa fa-check"></i></a>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <td>Completar Monedas KAY (Plan de Inversión {{ $planadquirido['data']->nombre }} = {{ $planadquirido['data']->objetivokayusers }} KAY)</td>
                        <td><div class="td-badge"><span class="badge badge-pill badge-danger">{{ $planadquirido['data']->objetivokayusers-totalpuntoskay() }} KAY</span></div></td>
                        <td>
                          @if(($req_persona-$directos)>0)
                              <a href="{{url('/backoffice/red')}}" class="btn btn-warning"><i class="fa fa-plus"></i> Generar KAY</a>
                          @else
                          @endif
                        </td>
                        <td style="text-align: center;"><a href="javascript:;" style="color:#e80f19;font-size:25px;"><i class="fa fa-close"></i></a></td>

                    </tbody>
                  </table>
                </div>
            </div>
            <div class="col-md-6">
                <label>Banco a Depositar *</label>
                    <select id="idbanco" class="chosen-select" style="display:none;width:100%;" onchange="selectcuenta(this.value)">
                      <option value="">-- Seleccionar Banco --</option>
                    </select>
                <label>Titulo * <i class="fa fa-check"></i></label>
                <input type="text" id="titulo" >
                <label>Cantidad<span id="titulotexto"></span> *</label>
                <div class="quantity fl-wrap">
                  <div class="quantity-item">
                      <input type="button" value="-" class="minus">
                      <input type="text" id="cantidad" class="qty" min="1" max="100000" step="1" value="" style="padding-left: 0px;">
                      <input type="button" value="+" class="plus">
                  </div>
                </div>
                <label>Cantidad de Monedas KAY *</label>
                <div class="quantity fl-wrap">
                    <div class="quantity-item">
                        <input type="button" value="-" class="minus">
                        <input type="text" id="puntoskay" class="qty" min="1" max="100000" step="1" value="" style="padding-left: 0px;">
                        <input type="button" value="+" class="plus">
                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Solicitar Cobro</button>
        </div>
    </div> 
</form>  
@endsection
@section('scriptsbackoffice')
  <style>
    .mx-header-search-select-item{
      width:100%;
    }
  </style>
@endsection