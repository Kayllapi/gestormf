<?php
$userscine = DB::table('userscine')
    ->where('userscine.idusers',Auth::user()->id)
    ->where('userscine.idestadouserscine',1)
    ->orWhere('userscine.idusers',Auth::user()->id)
    ->where('userscine.idestadouserscine',2)
    ->orderBy('userscine.id','DESC')
    ->limit(1)
    ->first();
?>
<div id="carga-enviopatrocinador">
    <section class="color-bg" style="background-color: #050b1b;margin-bottom: 5px;">
        <div class="shapes-bg-big"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="https://elordenmundial.com/wp-content/uploads/2021/02/cine-pandemia-plataformas-streamming-netflix-politica-cultura-mundo-coronavirus.jpg" style="width: 100%;">
                </div>
                <div class="col-md-6">
                    <div class="color-bg-text">
                        <h3>Kayllapi Entretenimiento</h3>
                        <p style="text-align: justify;font-size: 15px;">Disfruta de las Peliculas, Series y Cursos Online.</p>
                        @if($userscine!='')
                        @if($userscine->idestadouserscine==1)
                        <div class="mensaje-success">
                          <i class="fa fa-check"></i> Se ha enviado correctamente el "Voucher ó Comprobante de Deposito", espere por favor la confirmación del Patrocinador, para poder acceder a todo las Peliculas, Series y Cursos Online.</b>
                        </div>
                        @elseif($userscine->idestadouserscine==2)
                          <input id="cineusuario_cineusuario_email_patrocinador" type="text" placeholder="Ingrese el Usuario de Patrocinador" style="background-color: #e5fff0;
                                border: 1px dashed green;
                                padding: 15px;
                                font-size: 16px;
                                border-radius: 30px;
                                float: left;
                                width: 100%;    margin-top: 20px;box-shadow: 0px 0px 0px 7px rgb(255 255 255 / 40%);
                            }">
                          <a href="javascript:;" class="color-bg-link" style="margin-top: 20px;" onclick="mostrarpatrocinador()">Solicitar Accceso</a>
                        @endif
                        @else
                          <input id="cineusuario_cineusuario_email_patrocinador" type="text" placeholder="Ingrese el Usuario de Patrocinador" style="background-color: #e5fff0;
                                border: 1px dashed green;
                                padding: 15px;
                                font-size: 16px;
                                border-radius: 30px;
                                float: left;
                                width: 100%;    margin-top: 20px;box-shadow: 0px 0px 0px 7px rgb(255 255 255 / 40%);
                            }">
                          <a href="javascript:;" class="color-bg-link" style="margin-top: 20px;" onclick="mostrarpatrocinador()">Solicitar Accceso</a>
                        @endif
                          <a href="javascript:;" id="modal-accesoentretenimiento"></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@section('htmls2')
<div class="main-register-wrap modal-accesoentretenimiento">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3> <span>Solicitar <strong> Acceso</strong></span></h3>
      
              <div class="mx-modal-cuerpo">
                  <div class="custom-form" >
                      
                          <div id="carga-cineusuario_patrocinador"></div>
                          <div id="cont-cineusuario_patrocinador" style="display:none;">
                              <form  action="javascript:;" 
                                    onsubmit="callback({
                                         route: 'backoffice/cine',
                                         method: 'POST',
                                         data: {
                                              iduserspatrocinador : $('#iduserspatrocinador').val()
                                         }    
                                    },
                                    function(resultado){
                                       if (resultado.resultado == 'CORRECTO') {
                                         location.href = '{{ Request::fullUrl() }}';     
                                       }
                                    },this)">
                              <input type="hidden" id="iduserspatrocinador">
                              <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap" style="background-color: #31353D;">
                                  <span>Cuentas Bancaria</span>
                                </div>
                              </div>
                                  <label style="margin-bottom: 5px;font-size: 18px;">
                                    <b>Nombre:</b> <span id="cineusuario_nombre"></span><br>
                                    <b>Banco:</b> <span id="cineusuario_banco"></span><br>
                                    <b>Nº de Cuenta:</b> <span id="cineusuario_cuenta"></span>
                                  </label>
                        <div class="price-head op2" 
                              style="font-size: 20px;
                                    color: #fff;
                                    font-weight: bold;
                                    background-color: #a39f14;
                                    margin-bottom: 15px;
                                    padding: 12px;
                                    border-radius: 5px;" id="cont-preciototal">Depositar: $ 10.00</div>
                                  <label>Voucher ó Comprobante de Deposito *</label>
                                  <div class="fuzone" id="cont-fileupload-imagenvoucherdeposito">
                                      <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
                                      <input type="file" class="upload" id="imagenvoucherdeposito">
                                      <div id="resultado-fileupload-imagenvoucherdeposito"></div>
                                  </div>
                                  <button type="submit" style="float: none;background-color: #2ecc71;" class="price-link"> Enviar Solicitud</button>
                              </form>
                          </div>
                          <div id="cont-cineusuario_patrocinador_mensaje" style="display:none;">
                          </div>
                       </div>   
              </div>      
      
        </div>
    </div>
</div>
@endsection
@section('scriptsbackoffice3')
<script>
modal({click:'#modal-accesoentretenimiento'});
uploadfile({input:"#imagenvoucherdeposito",cont:"#cont-fileupload-imagenvoucherdeposito",result:"#resultado-fileupload-imagenvoucherdeposito"});
    function mostrarpatrocinador(){
      $('#cont-cineusuario_patrocinador_mensaje').css('display','none');
      var patrocinador = $('#cineusuario_cineusuario_email_patrocinador').val();
      if(patrocinador!=''){
          load('#carga-cineusuario_patrocinador');
          $('#cont-cineusuario_patrocinador').css('display','none');
          carga({
              input:'#carga-enviopatrocinador',
              color:'info',
              mensaje:'Procesando información, Espere por favor...'
          });
          $.ajax({
              url: raiz()+'/backoffice/cine/showpatrociandorcine?patrocinador='+$('#cineusuario_cineusuario_email_patrocinador').val(),
              type:"GET",
              success:function(respuesta){
                  $('#carga-cineusuario_patrocinador').html('');
                  if(respuesta['patrocinador']!=null){
                      if(respuesta['patrocinador'].banconombre!=null && respuesta['patrocinador'].numerocuenta!=null){
                          if(respuesta['patrocinador'].iduserspatrocinador=={{Auth::user()->id}}){
                              carga({
                                  input:'#carga-enviopatrocinador',
                                  color:'danger',
                                  mensaje:'El Patrocinador, no puede ser tu mismo Usuario.'
                              });
                          }else{
                              removecarga({input:'#carga-enviopatrocinador'});
                              $( "#modal-accesoentretenimiento" ).click();
                              $('#cont-cineusuario_patrocinador').css('display','block');
                              $('#iduserspatrocinador').val(respuesta['patrocinador'].iduserspatrocinador);
                              $('#cineusuario_nombre').html(respuesta['patrocinador'].usersapellidos+', '+respuesta['patrocinador'].usersnombre);
                              $('#cineusuario_banco').html(respuesta['patrocinador'].banconombre);
                              $('#cineusuario_cuenta').html(respuesta['patrocinador'].numerocuenta);
                          }
                              
                      }else{
                          carga({
                              input:'#carga-enviopatrocinador',
                              color:'danger',
                              mensaje:'El Patrocinador aun no ha actualizado su número de cuenta bancaria!.'
                          });
                      }
                  }else{
                      carga({
                          input:'#carga-enviopatrocinador',
                          color:'danger',
                          mensaje:'El Patrocinador No exite, ingrese otro por favor!.'
                      });
                  }

              },
          });
      }else{
          carga({
              input:'#carga-enviopatrocinador',
              color:'danger',
              mensaje:'Ingrese el Usuario del Patrocinador.'
          });
      }
          
      
    }
</script>
@endsection