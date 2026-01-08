@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>CONFIRMAR VISITAS PARA FONTPAGE</span>
      
      <a class="btn btn-success" href="{{ url('backoffice/kayvisitastiendafontpage') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
      <form class="js-validation-signin px-30" 
                action="javascript:;" 
                onsubmit="callback({
                          
                  route: 'backoffice/kayvisitastiendafontpage/{{ $kayvisitastiendafontpage->id }}',
                  method: 'PUT',
                  data:{
                      view:'confirmacion'       
                  }        
              },
              function(resultado){
                          
                 location.href = '{{ url('backoffice/kayvisitastiendafontpage') }}';                                                                            
                          
              },this)">
             <div class="text-center">
               <p style="font-size:20px !important;text-align:center;">¿Está seguro de confirmar la asignación de monedas KAY?</p>
        </div>
              <div class="profile-edit-container text-center">

                <center>
                  <button type="submit" class="btn  big-btn  color-bg flat-btn">Si, Confirmar</button>
                </center>

              </div>
          </form>   

                                                 
@endsection
@section('scriptsbackoffice')
<script>

</script>
@endsection