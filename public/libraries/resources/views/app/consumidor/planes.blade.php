<?php
$bancos = DB::table('banco')->get();
$planes = DB::table('consumidor_plan')
  /*->where('id',2)
  ->orWhere('id',3)
  ->orWhere('id',4)*/
  ->where('consumidor_plan.idestado',1)
  ->orderBy('consumidor_plan.id','asc')
  ->get();

$nom1 = 'Kay';
$nom2 = 'llapi';
$urlimagen = 'public/backoffice/sistema/unete/1.png';
if(Request::path()=='register'){
    if(isset($_GET['user'])){
        $patrocinador = DB::table('users')
            ->where('usuario','<>','')
            ->where('id','<>',1)
            ->where('usuario',$_GET['user'])
            ->first();
        if($patrocinador!=''){
            $nom1 = '';
            $nom2 = $patrocinador->nombre;
            $rutaimagen = getcwd().'/public/backoffice/usuario/'.$patrocinador->id.'/perfil/'.$patrocinador->imagen;
            if(file_exists($rutaimagen) AND $patrocinador->imagen!=''){
                $urlimagen = 'redimensionar/usuario/perfil/250/250/'.$patrocinador->id.'/'.$patrocinador->imagen;
            }
        }
    }
}
//$fecha_actual = Carbon\Carbon::now()->format('Y-m-d');
?>
    <section class="color-bg" style="padding: 20px 0px;padding-bottom: 50px;">
        <div class="shapes-bg-big"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="images-collage fl-wrap">
                      @if(Auth::user())
                            <div class="images-collage-title">KAY<span>LLAPI</span></div>
                            <div class="images-collage-main images-collage-item"><img src="{{url('public/backoffice/sistema/unete/1.png')}}" alt=""></div>
                            <div class="images-collage-other images-collage-item" data-position-left="23" data-position-top="10" data-zindex="2">
                              <img src="{{url('public/backoffice/sistema/unete/2.png')}}" alt=""></div>
                            <div class="images-collage-other images-collage-item" data-position-left="62" data-position-top="54" data-zindex="5">
                              <img src="{{url('public/backoffice/sistema/unete/3.png')}}" alt=""></div>
                            <div class="images-collage-other images-collage-item anim-col" data-position-left="18" data-position-top="70" data-zindex="11">
                              <img src="{{url('public/backoffice/sistema/unete/4.png')}}" alt=""></div>
                            <div class="images-collage-other images-collage-item" data-position-left="37" data-position-top="90" data-zindex="1">
                              <img src="{{url('public/backoffice/sistema/unete/5.png')}}" alt=""></div>
                      @else
                        @if(isset($patrocinador))
                            <div class="images-collage-title">{{ $nom1 }}<span>{{ $nom2 }}</span></div>
                            <div class="images-collage-main images-collage-item"><img src="{{url($urlimagen)}}" alt=""></div>

                            <style>
                              .images-collage:before {
                                  top: 40%;
                              }
                              .images-collage-title {
                                  right: 10%;
                                  top: 60px;
                              }
                              .images-collage-main {
                                  width: 260px;
                                  height: 260px;
                              }
                              .images-collage:before {
                                  width: 350px;
                                  height: 350px;
                              }
                            
                              </style>
                        @else
                            <div class="images-collage-title">KAY<span>LLAPI</span></div>
                            <div class="images-collage-main images-collage-item"><img src="{{url('public/backoffice/sistema/unete/1.png')}}" alt=""></div>
                            <div class="images-collage-other images-collage-item" data-position-left="23" data-position-top="10" data-zindex="2">
                              <img src="{{url('public/backoffice/sistema/unete/2.png')}}" alt=""></div>
                            <div class="images-collage-other images-collage-item" data-position-left="62" data-position-top="54" data-zindex="5">
                              <img src="{{url('public/backoffice/sistema/unete/3.png')}}" alt=""></div>
                            <div class="images-collage-other images-collage-item anim-col" data-position-left="18" data-position-top="70" data-zindex="11">
                              <img src="{{url('public/backoffice/sistema/unete/4.png')}}" alt=""></div>
                            <div class="images-collage-other images-collage-item" data-position-left="37" data-position-top="90" data-zindex="1">
                              <img src="{{url('public/backoffice/sistema/unete/5.png')}}" alt=""></div>
                        @endif
                      @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="color-bg-text" style="padding-top: 30px;">
                        <h3>Kayllapi Grow</h3>
                        <p style="text-align: justify;font-size: 15px;">Hola {{ Auth::user()->nombre }}, te damos la bienvenida a la mejor plataforma de publicidad y marketing en linea que te paga por ayudar a publicitar y promover a los negocios locales mediante internet, registrate gratis.</p>
                        @if(!Auth::user())
                        <a href="javascript:;" id="modal-iniciarsesion-master" class="color-bg-link" style="margin-right: 20px;font-size: 14px;">Registrarte Gratis</a>
                        @endif
                        <a href="{{url('public/backoffice/consumidor/kayllapi-consumidor.pdf')}}" target="_blank" class="color-bg-link">Ver más</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--section>
        <div class="container">
                <div class="row">
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-5">
                       <div class="resp-video" style="padding-top: 0px;margin-bottom: 0px;">
                           <iframe src="https://www.youtube.com/embed/X70WN9sWVg4" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                       </div>         
                    </div>
                    <div class="col-md-5">
                       <div class="resp-video" style="padding-top: 0px;margin-bottom: 0px;">
                           <iframe src="https://www.youtube.com/embed/Nxc-LtTrUYM" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                       </div>         
                    </div>
                </div>
        </div>
    </section-->
 @if(Auth::user())
    <div class="pricing-wrap fl-wrap">
    <br><br>
    @foreach($planes as $value)
      <?php 
        $recomendado = ($value->recomendado==1) ? "best-price":"";
      ?>
      <div class="price-item {{$recomendado}}">
          <div class="price-head op1">
              <h3>{{$value->nombre}}</h3>
          </div>
          <div class="price-content fl-wrap">
              <div class="price-num fl-wrap">
                  <span class="price-num-item">{{number_format($value->costo,0)}}</span> 
                  <span class="curen">KAY</span>
                  <div class="price-num-desc">Por mes</div>
              </div>
              <div class="price-desc fl-wrap">
                  <ul>
                      <?php 
                      $plandetalles = DB::table('consumidor_plandetalle')
                          ->where('idplan',$value->id)
                          ->orderBy('orden','asc')
                          ->get();
                      ?>
                      @foreach($plandetalles as $valueplan)
                    <li style="text-align: center;padding: 15px;"><?php echo $valueplan->contenido ?></li>
                      @endforeach
                  </ul>
                    <!--a href="javascript:;" class="price-link" style="background: #5fceb1;">Plan Comprado</a-->
                    <a href="javascript:;" id="modal-pagoplan" onclick="confirmacion({{$value->id}})" 
                       class="price-link" style="background: #f1c40f;margin-top: 20px;">Adquirir Ahora</a>
                    

                  @if($value->recomendado==1)
                  <div class="recomm-price">
                    <i class="fa fa-check"></i> 
                    <span class="clearfix"></span>
                    Recomendado
                  </div>
                  @endif
              </div>
          </div>
      </div>
    @endforeach
    </div>
<style>
.price-item {
    width: 33%;
}
@media only screen and (max-width: 768px){
    .price-item {
        width: 50%;
    }  
}
@media only screen and (max-width: 500px){
    .price-item {
        width: 100%;
    }  
}
</style>
 @endif
    <section style="padding-top: 0px;">
        <div class="container">
            <div class="process-wrap fl-wrap">
                                    <ul>
                                        <li>
                                            <div class="process-item">
                                                <span class="process-count">01 . </span>
                                                <div class="time-line-icon"><i class="fa fa-map"></i></div>
                                                <h4>la Inversión se destinará para registrar a todas las tiendas locales y así puedan vender sus productos en internet.</h4>
                                            </div>
                                            <span class="pr-dec"></span>
                                        </li>
                                        <li>
                                            <div class="process-item">
                                                <span class="process-count">02 .</span>
                                                <div class="time-line-icon"><i class="fa fa-envelope-open"></i></div>
                                                <h4>Las tiendas locales podrán adquirir planes de publicidad, lo cual será distribuido para los socios.</h4>
                                            </div>
                                            <span class="pr-dec"></span>
                                        </li>
                                        <li>
                                            <div class="process-item">
                                                <span class="process-count">03 .</span>
                                                <div class="time-line-icon"><i class="fa fa-hand-peace-o"></i></div>
                                                <h4>Los socios podrán ganar dinero, compartiendo y promoviendo a todas las tiendas registradas.</h4>
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="process-end"><i class="fa fa-check"></i></div>
                                </div>
        </div>
    </section>
@section('htmls1')
<div class="main-register-wrap modal-pagoplan">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3> <span>Adquirir <strong> Plan</strong></span></h3>
      
              <div class="mx-modal-cuerpo">
                  @include('app.consumidor.puntoskay')
                  <form  class="custom-form" 
                        action="javascript:;" 
                        onsubmit="callback({
                             route: 'backoffice/consumidor/puntoskay',
                             method: 'POST'
                        },
                        function(resultado){
                             location.href = '{{ Request::fullUrl() }}';    
                        },this)" name="registerform" class="main-inscripcion-form" id="main-inscripcion">
                    
                      <input type="hidden" id="idplan" value="">
                      <input type="hidden" id="view" value="pagousuario">
                      <div class="price-head op2" 
                              style="border-radius: 0px;
                              font-size: 20px;
                              color: #fff;
                              font-weight: bold;
                              background-color: #f1c40f;
                              margin-bottom: 15px;" id="cont-plan"></div>
                    <div id="plan-pago" style="display:none;">
                        <label>Correo Electrónico (Usuario) de Patrocinador *</label>
                        <input id="email_patrocinador" type="text" style="background-color: #e5fff0;border: 1px dashed green;" disabled>
                        <button type="submit" class="price-link" style="width: 100%;"> Adquirir Plan</button>
                        <!--label>Método de Pago *</label>
                        <div class="accordion">
                            <a class="toggle" href="#"> Oficina<i class="fa fa-angle-down"></i></a>
                            <div class="accordion-inner" style="display: none;">
                                <label>Voucher ó Comprobante *</label>
                                <div class="fuzone" id="cont-fileupload">
                                    <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
                                    <input type="file" class="upload" id="imagen">
                                    <div id="resultado-logo"></div>
                                </div>
                                <button type="submit" style="float: none;background-color: #2ecc71;" class="price-link"> Pagar Ahora</button>
                            </div>
                            <a class="toggle" href="#"> Visa / Mastercard<i class="fa fa-angle-down"></i></a>
                            <div class="accordion-inner" style="display: none;">
                                <button type="button" id="pagaconculqi" style="float: none;background-color: #2ecc71;" class="price-link"> Pargar con CULQI</button>
                            </div>
                        </div-->
                    </div>
                  </form>
              </div>      
      
        </div>
    </div>
</div>
@endsection
@section('scriptsbackoffice1')
<script>
modal({click:'#modal-pagoplan'});
</script>
  <script>
    function confirmacion(id){
      $('#idplan').val(id);
      $('#plan-pago').css('display','none');
      $('#cont-plan').html('...');
      $.ajax({
          url: raiz()+'/backoffice/consumidor/puntoskay/showplan?id='+id,
          type:"GET",
          success:function(respuesta){
            $('#cont-plan').html(respuesta.plan);
            if(respuesta.planestado==1){
                $('#plan-pago').css('display','block');
                $('#mx-cont-adquirirplan-input').css('display','none');
        
                if(respuesta.planadquirido['estado']=='RED'){
                    var patrocinador = respuesta.planadquirido['red'].userspadreusuario;
                    $('#email_patrocinador').val(patrocinador);
                    if(patrocinador!=''){
                        $('#email_patrocinador').removeAttr('disabled');
                    }else{
                        $('#email_patrocinador').attr('disabled','true');
                    }
                }else if(respuesta.planadquirido['estado']=='NINGUNO'){
                    $('#email_patrocinador').removeAttr('disabled');
                }else if(respuesta.planadquirido['estado']=='VENCIDO'){
                    $('#mx-cont-adquirirplan-input').css('display','block');
                    var patrocinador = respuesta.planadquirido['data'].userspadreusuario;
                    $('#email_patrocinador').val(patrocinador);
                    if(patrocinador!=''){
                        $('#email_patrocinador').removeAttr('disabled');
                    }else{
                        $('#email_patrocinador').attr('disabled','true');
                    }
                }
                
            }
          },
      });
      
    }
  </script>
  <style>
  .close-reg > .fa {
      display: inline-block;
      font: normal normal normal 14px/1 FontAwesome;
      line-height: 40px;
      font-size: inherit;
      text-rendering: auto;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
  }
    .custom-form button {
    margin-top: 5px;
}
  </style>
@endsection