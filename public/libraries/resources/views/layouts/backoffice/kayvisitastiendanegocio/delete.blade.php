@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>ELIMINAR VISITAS PARA FONTPAGE</span>
      
      <a class="btn btn-success" href="{{ url('backoffice/kayvisitastiendafontpage') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
      <form class="js-validation-signin px-30" 
                action="javascript:;" 
                onsubmit="callback({
                          
                  route: 'backoffice/kayvisitastiendafontpage/{{ $kayvisitastiendafontpage->id }}',
                  method: 'DELETE',
                  data:{
                      view:'eliminar'       
                  }        
              },
              function(resultado){
                 location.href = '{{ url('backoffice/kayvisitastiendafontpage') }}';                                                                            
              },this)">
              <div class="box-widget-content" style="padding:0px;padding-bottom: 10px;">
                 <div class="list-author-widget-contacts list-item-widget-contacts">
                     <p style="font-size:20px !important;text-align:center;color:red;">¿Está seguro de eliminar el consumo de <br><b>{{$kayvisitastiendafontpage->totalpuntoskay}} KAY</b> por <b>{{$kayvisitastiendafontpage->cantidad}} visitas</b>?</p>
                 </div>
             </div>
              <div class="profile-edit-container">

                <center><button type="submit" class="btn  big-btn color-bg flat-btn">Si, Eliminar</button></center>

              </div>
          </form>   
                                                 
@endsection
@section('scriptsbackoffice')
<script>

</script>
@endsection