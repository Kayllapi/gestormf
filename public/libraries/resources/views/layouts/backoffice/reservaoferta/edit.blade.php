@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="profile-edit-header fl-wrap">
            <h4>Escanear Código QR</h4>
        </div>
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
                <div id="scanned-QR">
                    <div class="mensaje-success">
                        <i class="fa fa-check"></i>  Selecione o Capture el Código QR.
                    </div>
                </div>
                <select class="form-control" id="camera-select" style="display:none;"></select>
                <button class="btn  big-btn  color-bg flat-btn" id="play" type="button" data-toggle="tooltip" 
                        style="margin-right: 10px;margin-top: 0px;padding-left: 15px; padding-right: 20px;background-color: #9b59b6;"><i class="fa fa-play"></i> Escanear</button>
                <button class="btn  big-btn  color-bg flat-btn modal-modalocodigoqr" id="play" type="button" data-toggle="tooltip" 
                        style="margin-right: 10px;margin-top: 0px;padding-left: 15px; padding-right: 20px;background-color: #2c3b5a;"><i class="fa fa-edit"></i> Escribir</button>
                <button class="btn  big-btn  color-bg flat-btn" 
                        id="decode-img" type="button" 
                        data-toggle="tooltip" 
                        style="margin-right: 10px;margin-top: 0px;padding-left: 15px; padding-right: 20px;">
                  <i class="fa fa-upload"></i> Imagen</button> 
                <div id="cont-camaraqr">
                <canvas id="webcodecam-canvas" style="background-color: #ccc;width: 100%; margin-top: 10px;margin-bottom: 10px;"></canvas>
                </div>  
            </div>
            <div class="col-md-6">
              <label>Imagen (Oferta)</label>
                    @if($reservaoferta->ofertaimagen!='')
                    <img class="thumb" src="{{ url('public/backoffice/tienda/'.$reservaoferta->idtienda.'/oferta/'.$reservaoferta->ofertaimagen) }}" width="100%"/>
                    @else
                    <img src="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}" style="width: 100%;">
                    @endif
            </div>
          </div>
        </div>
    </div>
    <!-- profile-edit-container end-->  	

<div id="carga-camaraqr">
</div>


    
@endsection
@section('htmls')
        <!--  modal oferta --> 
        <div class="main-register-wrap modalocodigoqr" id="modalocodigoqr">
            <div class="main-overlay"></div>
            <div class="main-register-holder">
                <div class="main-register fl-wrap">
                    <div class="close-reg" id="cerrarmodalocodigoqr"><i class="fa fa-times"></i></div>
                    <h3>Digitar Código</h3>
                    <div id="tabs-container">
                        <div class="custom-form">
                            <div id="cont-oferta">
                                    <form action="javascript:;" onsubmit="enviarvalidarqr()">
                                      <input type="hidden" id="idoferta">
                                      <label>Código *</label>
                                      <input type="text" id="txtcodigoqr"/>
                                        <button type="submit"  class="log-submit-btn"><span>Validar</span></button>
                                      </div>
                                    </form>
                                </div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  fin modal oferta --> 
@endsection
@section('scriptsbackoffice')
<script type="text/javascript" src="{{ url('public/libraries/webcodecamjs/js/filereader.js') }}"></script>
<script src="{{ url('public/libraries/webcodecamjs/js/qrcodelib.js') }}"></script>
<script src="{{ url('public/libraries/webcodecamjs/js/webcodecamjquery.js') }}"></script>
<script type="text/javascript">
/*
 var arg = {
     resultFunction: function(result) {
        $('#carga-camaraqr').html('<div class="mx-alert-load"><img src="'+raiz()+'public/libraries/app/img/loading.gif"></div>');  
        $.ajax({
            url: raiz()+'/backoffice/reservaoferta/showlectorcodigoqr?codigo='+result.code+'&idreservaoferta={{ $reservaoferta->id }}',
            type:"GET",
            success:function(respuesta){
                if(respuesta['resultado']=='CORRECTO'){
                    location.href = '{{ url('backoffice/reservaoferta') }}';  
                }else{
                    alert('error!');
                }
            },
        }); 
     }
 };
  
 var decoder = $("#webcodecam-canvas").WebCodeCamJQuery(arg).data().plugin_WebCodeCamJQuery;
 decoder.buildSelectMenu("#camera-select",1).init();

 setTimeout(function() {
   decoder.play();
 },1000);
*/
function enviarvalidarqr(){
    $('#modalocodigoqr').hide();
    var codigo = $('#txtcodigoqr').val();
    validarqr(codigo);
    $('#txtcodigoqr').val('');
}
function validarqr(codigo){
    $("#scanned-QR").html('<div class="mensaje-warning"><i class="fa fa-sync-alt"></i>  Validando...</div>');
    $('#cont-camaraqr').css('display','none');
    $('#carga-camaraqr').html('<div class="mx-alert-load"><img src="'+raiz()+'public/libraries/app/img/loading.gif"></div>');  
    $.ajax({
        url: raiz()+'/backoffice/reservaoferta/showlectorcodigoqr?codigo='+codigo+'&idreservaoferta={{ $reservaoferta->id }}',
        type:"GET",
        success:function(respuesta){
            if(respuesta['resultado']=='CORRECTO'){
                location.href = '{{ url('backoffice/reservaoferta') }}';  
            }else{
                $('#carga-camaraqr').html('');
                $('#cont-camaraqr').css('display','block');
                $("#scanned-QR").html('<div class="mensaje-danger"><i class="fa fa-close"></i>  Hay un error, Intente otra vez!.</div>');
                //alert('error!');
            }
        },
    });
}
  
/*!
 * WebCodeCamJQuery 2.1.0 javascript Bar-Qr code decoder 
 * Author: Tóth András
 * Web: http://atandrastoth.co.uk
 * email: atandrastoth@gmail.com
 * Licensed under the MIT license
 */
(function(undefined) {
    var scannerLaser = $(".scanner-laser"),
        imageUrl = $("#image-url"),
        decodeLocal = $("#decode-img"),
        play = $("#play"),
        scannedImg = $("#scanned-img"),
        scannedQR = $("#scanned-QR"),
        grabImg = $("#grab-img"),
        pause = $("#pause"),
        stop = $("#stop"),
        contrast = $("#contrast"),
        contrastValue = $("#contrast-value"),
        zoom = $("#zoom"),
        zoomValue = $("#zoom-value"),
        brightness = $("#brightness"),
        brightnessValue = $("#brightness-value"),
        threshold = $("#threshold"),
        thresholdValue = $("#threshold-value"),
        sharpness = $("#sharpness"),
        sharpnessValue = $("#sharpness-value"),
        grayscale = $("#grayscale"),
        grayscaleValue = $("#grayscale-value"),
        flipVertical = $("#flipVertical"),
        flipVerticalValue = $("#flipVertical-value"),
        flipHorizontal = $("#flipHorizontal"),
        flipHorizontalValue = $("#flipHorizontal-value");
    var args = {
        autoBrightnessValue: 100,
        resultFunction: function(res) {
            [].forEach.call(scannerLaser, function(el) {
                $(el).fadeOut(300, function() {
                    $(el).fadeIn(300);
                });
            });
            /*scannedImg.attr("src", res.imgData);
            scannedQR.text(res.format + ": " + res.code);*/
          
                
            // --- ENVIAR CODIGO //
            decoder.stop();
            validarqr(res.code);
            // --- FIN ENVIAR CODIGO //
          
        },
        getDevicesError: function(error) {
            var p, message = "Error detected with the following parameters:\n";
            for (p in error) {
                message += (p + ": " + error[p] + "\n");
            }
            alert(message);
        },
        getUserMediaError: function(error) {
            var p, message = "Error detected with the following parameters:\n";
            for (p in error) {
                message += (p + ": " + error[p] + "\n");
            }
            alert(message);
        },
        cameraError: function(error) {
            var p, message = "Error detected with the following parameters:\n";
            if (error.name == "NotSupportedError") {
                var ans = confirm("Your browser does not support getUserMedia via HTTP!\n(see: https://goo.gl/Y0ZkNV).\n You want to see github demo page in a new window?");
                if (ans) {
                    window.open("https://andrastoth.github.io/webcodecamjs/");
                }
            } else {
                for (p in error) {
                    message += p + ": " + error[p] + "\n";
                }
                alert(message);
            }
        },
        cameraSuccess: function() {
            grabImg.removeClass("disabled");
        }
    };
    var decoder = $("#webcodecam-canvas").WebCodeCamJQuery(args).data().plugin_WebCodeCamJQuery;
    decoder.buildSelectMenu("#camera-select", 1).init();
    decodeLocal.on("click", function() {
        Page.decodeLocalImage();
    });
    play.on("click", function() {
        $('#cont-camaraqr').css('display','block');
        scannedQR.html('<div class="mensaje-info"><i class="fa fa-sync-alt"></i>  Escaneando...</div>');
        grabImg.removeClass("disabled");
        decoder.play();
    });
    grabImg.on("click", function() {
        scannedImg.attr("src", decoder.getLastImageSrc());
    });
    pause.on("click", function(event) {
        scannedQR.text("Paused");
        decoder.pause();
    });
    stop.on("click", function(event) {
        grabImg.addClass("disabled");
        scannedQR.text("Stopped");
        decoder.stop();
    });
    Page.changeZoom = function(a) {
        if (decoder.isInitialized()) {
            var value = typeof a !== "undefined" ? parseFloat(a.toPrecision(2)) : zoom.val() / 10;
            zoomValue.text(zoomValue.text().split(":")[0] + ": " + value.toString());
            decoder.options.zoom = value;
            if (typeof a != "undefined") {
                zoom.val(a * 10);
            }
        }
    };
    Page.changeContrast = function() {
        if (decoder.isInitialized()) {
            var value = contrast.val();
            contrastValue.text(contrastValue.text().split(":")[0] + ": " + value.toString());
            decoder.options.contrast = parseFloat(value);
        }
    };
    Page.changeBrightness = function() {
        if (decoder.isInitialized()) {
            var value = brightness.val();
            brightnessValue.text(brightnessValue.text().split(":")[0] + ": " + value.toString());
            decoder.options.brightness = parseFloat(value);
        }
    };
    Page.changeThreshold = function() {
        if (decoder.isInitialized()) {
            var value = threshold.val();
            thresholdValue.text(thresholdValue.text().split(":")[0] + ": " + value.toString());
            decoder.options.threshold = parseFloat(value);
        }
    };
    Page.changeSharpness = function() {
        if (decoder.isInitialized()) {
            var value = sharpness.prop("checked");
            if (value) {
                sharpnessValue.text(sharpnessValue.text().split(":")[0] + ": on");
                decoder.options.sharpness = [0, -1, 0, -1, 5, -1, 0, -1, 0];
            } else {
                sharpnessValue.text(sharpnessValue.text().split(":")[0] + ": off");
                decoder.options.sharpness = [];
            }
        }
    };
    Page.changeGrayscale = function() {
        if (decoder.isInitialized()) {
            var value = grayscale.prop("checked");
            if (value) {
                grayscaleValue.text(grayscaleValue.text().split(":")[0] + ": on");
                decoder.options.grayScale = true;
            } else {
                grayscaleValue.text(grayscaleValue.text().split(":")[0] + ": off");
                decoder.options.grayScale = false;
            }
        }
    };
    Page.changeVertical = function() {
        if (decoder.isInitialized()) {
            var value = flipVertical.prop("checked");
            if (value) {
                flipVerticalValue.text(flipVerticalValue.text().split(":")[0] + ": on");
                decoder.options.flipVertical = value;
            } else {
                flipVerticalValue.text(flipVerticalValue.text().split(":")[0] + ": off");
                decoder.options.flipVertical = value;
            }
        }
    };
    Page.changeHorizontal = function() {
        if (decoder.isInitialized()) {
            var value = flipHorizontal.prop("checked");
            if (value) {
                flipHorizontalValue.text(flipHorizontalValue.text().split(":")[0] + ": on");
                decoder.options.flipHorizontal = value;
            } else {
                flipHorizontalValue.text(flipHorizontalValue.text().split(":")[0] + ": off");
                decoder.options.flipHorizontal = value;
            }
        }
    };
    Page.decodeLocalImage = function() {
        if (decoder.isInitialized()) {
            decoder.decodeLocalImage(imageUrl.val());
        }
        imageUrl.val(null);
    };
    var getZomm = setInterval(function() {
        var a;
        try {
            a = decoder.getOptimalZoom();
        } catch (e) {
            a = 0;
        }
        if (!!a && a !== 0) {
            Page.changeZoom(a);
            clearInterval(getZomm);
        }
    }, 500);
    $("#camera-select").on("change", function() {
        if (decoder.isInitialized()) {
            decoder.stop().play();
        }
    });
}).call(window.Page = window.Page || {});
</script>

@endsection
