@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>CONFIRMAR PAGO</span>
      <a class="btn btn-success" href="{{ url('backoffice/pagos') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/pagos/{{ $planadquirido->idusers }}',
        method: 'PUT',
        data : {
                idplan : {{$planadquirido->idplan}}
                }
    },
    function(resultado){
        location.href = '{{ url('backoffice/pagos') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="{{ $planadquirido->id }}" id="idplanadquirido"/>
    <input type="hidden" value="confirmacionpago" id="view"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
                <label>Nombre</label>
                <input type="text" value="{{$planadquirido->nombre}} {{$planadquirido->apellidos}}" disabled/>
                <label>Plan</label>
                <input type="text" value="{{$planadquirido->nombreplan}}" disabled/>
                <label>Monto pagado</label>
                <input type="text" value="{{$planadquirido->costo}} KAY" disabled/>
            </div>
            <div class="col-md-6">
                <?php
                $repartir = reparticion_bono($planadquirido->idusers);
                $inicio_fecha = Carbon\Carbon::now();
                // FECHA FREE
                if($planadquirido->idplan==1){
                    $inicio_fecha = '2020-06-01';
                }
                // FIN FECHA FREE
                if($repartir['cantidadveces']==1){
                }else{
                    $planadqui = DB::table('planadquirido') 
                        ->join('red','red.id','planadquirido.idred')
                        ->where('red.idusershijo',$planadquirido->idusers)
                        ->where('planadquirido.fechaanulacion',null)
                        ->select(
                          'planadquirido.*'
                        )
                        ->skip(1)
                        ->limit(1)
                        ->orderBy('planadquirido.id','desc')
                        ->first();
                    if($planadqui!=''){
                        $inicio_fecha = $planadqui->fechafin;
                    }
                    
                } 
                
                $ultima_fecha = date("Y-m-d",strtotime($inicio_fecha."+ 1 month"));
                ?>
                <label>Desde</label>
                <input type="text" value="{{ date_format(date_create($inicio_fecha),'d/m/Y') }}" disabled/>
                <label>Hasta</label>
                <input type="text" value="{{ date_format(date_create($ultima_fecha),'d/m/Y') }}" disabled/>
            </div>
          </div>
        </div>
    </div>
    <!-- profile-edit-container end-->  										
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Confirmar Pago</button>
            <a href="{{ url('backoffice/pagos/'.$planadquirido->id.'/edit?view=anular') }}" class="btn  big-btn  color-bg flat-btn" style="background-color: rgb(229, 0, 0);
margin-left: 10px;
float: left;
padding-bottom: 14px; 
padding-top: 14px;">Anular Pago</a>
        </div>
    </div>
    <!-- profile-edit-container end-->  
</form>                             
@endsection
