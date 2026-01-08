@extends('layouts.backoffice.master')
@section('cuerpobackoffice')  

<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anuncios</span>
    </div>
</div>
<div class="profile-edit-container" id="cont-anuncio-ver">
    <div class="custom-form">
        <div class="row">
            <div class="col-md-12">
                <div class="faltananuncios">Faltan ver {{$cantidad_anuncio_faltante}} Anuncios</div>
            </div>
            <div class="col-md-12">
                <div class="imagenrandom"></div>
            </div>
            <div class="col-xs-3 col-md-5">
            </div>
            <div class="col-xs-6 col-md-2">
                <div id="cont_contador_siguiete"><div id="contador_siguiete">0</div></div>
            </div>
        </div>
    </div>
</div>
<style>
  .faltananuncios {
      background-color: #313b57;
      margin-bottom: 5px;
      border-radius: 5px;
      padding: 10px;
      font-size: 15px;
      font-weight: bold;
      color: white;
  }
  .imagenrandom {
      background-image: url({{ url('public/backoffice/consumidor/anuncio/'.$anunciorandom->imagen) }});
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      width:100%;
      height: 600px;
      border: 5px dashed #32353c;
      border-radius: 5px;
      background-color: #32353c;
      margin-bottom: 5px;
  }
  .td-siguiente {
      border-radius: 22px;
  }
  #contador_siguiete {
      font-size: 14px;
      border-radius: 20px;
      padding: 12px;
      padding-left: 20px;
      padding-right: 20px;
      background-color: #32353b;
      color:#fff;
      font-weight: bold;
  }
    
  @media only screen and (max-width: 1064px){
      .imagenrandom {
          height: 400px;
      }
  }
</style>
@endsection
@section('scriptsbackoffice')
<script>
    var numero = 0;
    var contador_siguiete = document.getElementById("contador_siguiete");
    var cont_contador_siguiete = document.getElementById("cont_contador_siguiete");
    window.setInterval(function(){
        contador_siguiete.innerHTML = numero;
        if(numero>=10){
            cont_contador_siguiete.innerHTML = '<a class="btn btn-info td-siguiente" onclick="siguiente({{$anunciorandom->id}})" href="javascript:;"><i class="fa fa-angle-right"></i> Siguiente</a>';
        }
        numero++;
    },1000);
  
    function siguiente(idanuncio){
        load('#cont-anuncio-ver');
        $.ajax({
            url:"{{url('backoffice/consumidor/anuncio/show-anuncio')}}",
            type:'GET',
            data: {
                idanuncio : idanuncio
            },
            success: function (respuesta){
                location.href = '{{ url('backoffice/consumidor/anuncio') }}';
            }
        })
    }
</script>
@endsection