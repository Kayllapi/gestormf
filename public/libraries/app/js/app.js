function raiz() {
  var url = $('body').attr('url');
  if(url!=''){
      return url+'/';
  }else{
      return '';
  }
  
}
/*function table(param) {
		var route = param['route'].split('/');
		var pagina = param['route'];
		if(route.length>0) {
			 	pagina = route[0];
		}
		var getArray = param['route'].split('?');
    var arrayand = '?';
    if(getArray.length > 1) {
        arrayand = '&';
    }
  
    var table = $(param['idclass']).DataTable({
			processing: true,
      serverSide: true,
			dom: 'tlpi',
			lengthMenu: [[10, 25, 50, 10000], ["10 Filas", "25 Filas", "50 Filas", "Todas las Filas"]],
			ajax: raiz()+'/admin/'+param['route']+arrayand+'pagina='+pagina,
			bSort: false,
			columnDefs: [
				{ className:"mx-td-input","targets":param['btnclass'] },
				{ className:"mx-td-img","targets":param['imgclass'] }
			],
			destroy : true,
			drawCallback: function (settings ) {
         	$('.tablepopover').popover({
             	html: true,
             	trigger: 'manual',
             	placement: 'left',
             	content: function () {
                 	return '<div>'+$(this).attr('text')+'</div>';
             	}
         	})
     	}
		});
    $(param['idclass']+' tfoot th').each(function() {
        var title = $(this).text();
				if(title!='') {
						$(this).html('<input type="text" class="form-control mx-search-table" placeholder="Buscar...">');
				}
    });

    table.columns().every(function() {
        var that = this;
        $('input',this.footer()).on('keyup change',function() {
            if (that.search() !== this.value) {
                that
                    .search( this.value )
                    .draw();
            }
        });
    });
	
		$(param['idclass']).on('click', function(e){
        if($('.tablepopover').length>1){
           	$('.tablepopover').popover('hide');
						$(e.target).popover('toggle');
				}else{
				}
		});
}*/
function carga(param) {
        removecarga({input:param['input']});
        var inp = param['input'].split('#');
        if (param['color']=='danger') {
            $(param['input']).addClass('mx-carga').append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert"><div class="mx-contenedor-subcarga alert-'+param['color']+'" data-dismiss="alert" aria-label="Close"><i class="fa fa-times-circle" id="preloader-icon"></i><div id="mx-mensaje-subcarga-icon">'+param['mensaje']+'</div></div></div>');
        }else if (param['color']=='success') {
            $(param['input']).addClass('mx-carga').append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert"><div class="mx-contenedor-subcarga alert-'+param['color']+'" data-dismiss="alert" aria-label="Close"><i class="fa fa-check-circle" id="preloader-icon"></i><div id="mx-mensaje-subcarga-icon">'+param['mensaje']+'</div></div>');
        }else if (param['color']=='warning') {
            $(param['input']).addClass('mx-carga').append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert"><div class="mx-contenedor-subcarga alert-'+param['color']+'" data-dismiss="alert" aria-label="Close"><i class="fa fa-times-circle" id="preloader-icon"></i><div id="mx-mensaje-subcarga-icon">'+param['mensaje']+'</div></div></div>');
        }else if (param['color']=='info'){
            $(param['input']).addClass('mx-carga').append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert"><div class="mx-contenedor-subcarga alert-'+param['color']+'" data-dismiss="alert" aria-label="Close"><div id="preloader_3"></div><div id="mx-mensaje-subcarga">'+param['mensaje']+'</div></div></div>');
        }else if (param['color']=='default'){
            $(param['input']).addClass('mx-carga').append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga"><div class="mx-contenedor-subcarga alert-'+param['color']+'"></div></div>');
        }
}
function removecarga(param) {
        $(param['input']).removeClass('mx-carga');
        var inp = param['input'].split('#');
        $('#mx-subcarga'+inp[1]).remove();
}
function forminput(param) {   
				var formData = new FormData();
				if(param['form']!=undefined) {
						$(param['form']+' input[type=file]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
                  var file_this = this;
                  //if($(file_this).prop('files').length>1){
                    $($(file_this).prop('files')).each(function() {
                        formData.append($(file_this).attr('id')+'[]', this);
                    });
                  //}else{
                    //formData.append($(file_this).attr('id'), $(file_this).prop('files')[0]);
                  //}
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=file]')).each(function() {
											arrayelemet.push($(this).prop('files')[0]);
									});
								  formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=color]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=color]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=text]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
		//                element[$(this).attr('id')] = $(this).val();
									formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=text]')).each(function() {
											arrayelemet.push($(this).val());
									});
		//               element[$(this).attr('id')]  = arrayelemet;
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=hidden]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=hidden]')).each(function() {

											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=password]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=password]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=number]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=number]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=date]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=date]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=month]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=month]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=time]').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=time]')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=radio]:checked').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=radio]:checked')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' input[type=checkbox]:checked').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' input#'+$(this).attr('id')+'[type=checkbox]:checked')).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' select').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 0) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' select#'+$(this).attr('id'))).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
						$(param['form']+' textarea').each(function() {
								var countelemet = $('input#'+$(this).attr('id'));
								if( countelemet.length == 1) {
									 formData.append($(this).attr('id'), $(this).val());
								}else{
									var arrayelemet = [];
									$($(param['form']+' textarea#'+$(this).attr('id'))).each(function() {
											arrayelemet.push($(this).val());
									});
									formData.append($(this).attr('id'), arrayelemet);
								} 
						});
				}
					 
				$.each(param['data'], function( key, value ) {
            if(Array.isArray(value)){
                $.each(value, function( key0, value0 ) {
                    formData.append(key+'[]', value0.num);
                    $.each(value0, function( key1, value1 ) {
                        var numi = '';
                        if(value0.num!=undefined){
                            numi = value0.num;
                        }
                        if(value1==undefined){
                            value1 = '';
                        }
                        formData.append(key1+numi, value1);
                    });
                });
            }else{
                formData.append(key, value);
            }
        });
				$.each(param['dataimage'], function( key, value ) {
						var countelemet = $('input'+value).prop('files')[0];
            formData.append(key, countelemet);
        });
        return formData;
}
function formerror(param) {
        var errorsHtml= '';
        var i=0
        $('.class-input').removeAttr('style');
        $('.error-input').remove();
        $.each(param['dato'].responseJSON.errors, function( key, value ) {
            $('input#'+key).addClass('class-input').css('border','1px solid #f54708');
            $('input#'+key).after('<span class="error-input" style="color: #e22d02;float: left;margin-top: -10px;margin-bottom: 10px;width: 100%;text-align: left;">'+value+'</span>');
            $('select#'+key+' + span > span > span').addClass('class-input').css('border','1px solid #f54708');
            $('select#'+key+' + span > span > span > span > span').css('color','#f54708');
            $('select#'+key+' + span').after('<span class="error-input" style="color: #e22d02;float: left;margin-top: -10px;margin-bottom: 10px;width: 100%;text-align: left;">'+value+'</span>');
            if (i==0) {
               errorsHtml += value; 
               $('#'+key).focus();
            }
            i=i+1;
        });
        return errorsHtml;
}
function callback(param={},callback,thisp=null) {
    console.log(param)
        $('.class-input').removeAttr('style');
        $('.error-input').remove();
        var pathArray = param['route'].split( '/' );
        var formnew = 'form'+Math.floor((Math.random() * 100) + 1);
  
        if(param['data']==undefined) {
            param['data'] = {};
        }
        if(param['dataimage']==undefined) {
            param['dataimage'] = {};
        }
		
		if(thisp!=null){
			$(thisp).attr('id',formnew);
		}
		param['dato'] = forminput({form:'#'+formnew,data:param['data'],dataimage:param['dataimage']});
				
        if(param['carga']==undefined) {
			      param['carga'] = '#mx-carga';
          	var countcarga = $(param['carga']).length;
          	if(countcarga==0){
	            var mxcarga = param['carga'].split('#');
	            $('#'+formnew).after('<div id="'+mxcarga[1]+'"></div>');
	            $('#'+formnew).appendTo(param['carga']);
          	}
		}
				if(param['method']=='PUT') {
						param['dato'].append('_method','PUT');
				}else if(param['method']=='DELETE') {
						param['dato'].append('_method','DELETE');
				}
	
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: raiz()+param['route'],
            type: 'POST',
            data: param['dato'],
			      processData: false,
            contentType: false ,
            beforeSend: function (data) {
               carga({
                    input:param['carga'],
                    color:'info',
                    mensaje:'Procesando información, Espere por favor...'
                }); 
            },
            success:function(respuesta)
            {       
                if (respuesta.resultado=='CORRECTO') {
                    carga({
                        input:param['carga'],
                        color:'success',
                        mensaje:respuesta.mensaje
                    });
                    callback(respuesta);
                }else if (respuesta.resultado=='ERROR') {
                    carga({
                        input:param['carga'],
                        color:'danger',
                        mensaje:respuesta.mensaje
                    });
                }else{
                    callback(respuesta);
                }                    
            },
            error:function(respuesta)
            {
               if(respuesta.responseJSON.message=='The given data was invalid.'){
                  carga({
                      input:param['carga'],
                      color:'danger',
                      mensaje:formerror({dato:respuesta})
                  });
               }else if(respuesta.responseJSON.message=='Your email address is not verified.'){
                  carga({
                        input:param['carga'],
                        color:'success',
                        mensaje:'Ingresando, Espere por favor...'
                  });
                  callback({
                      resultado: 'ERRORCONFIRMEMAIL'
                  });
               }else{
                  carga({
                      input:param['carga'],
                      color:'danger',
                      mensaje:formerror({dato:respuesta})
                  });
               }
               
            }
        });
}
function pagina(param) {
        if(param['route']=='') {
            return false;
        }
        if(param['result']==undefined) {
            param['result'] = '#cuerpo';
        }
  
        /*var pathArray = param['route'].split( '/' );
        var ulrmodulo = "";
        var urlpagina = "";
        var urlestado = "error";
        for (i = 0; i < pathArray.length; i++) {
            if(pathArray.length-2 == i){
                ulrmodulo = pathArray[i];
            }
            if(pathArray.length-1 == i){
                urlpagina = pathArray[i];
            }
          
            if(pathArray.length-1 == i && pathArray[i]=='index'){
                urlpagina = pathArray[i];
                urlestado = 'correcto';
            }
            else if(pathArray.length-1 == i && pathArray[i]=='create'){
                urlpagina = pathArray[i];
                urlestado = 'correcto';
            }
            else if(pathArray.length-1 == i && pathArray[i]=='show'){
                urlpagina = pathArray[i];
                urlestado = 'correcto';
            }
            else if(pathArray.length-1 == i && pathArray[i]=='edit'){
                urlpagina = pathArray[i];
                urlestado = 'correcto';
            }
        }
  
        if(urlestado=='error'){
            ulrmodulo = urlpagina;
            urlpagina = 'index';
        }
        
        var pagina = ulrmodulo;
        var view = urlpagina;
  
        var get = window.location.search;
        var getlist = get.split('?');
        if(getlist.length>1){
            var getlistdata = getlist[1].split('&');
            for (i = 0; i < getlistdata.length; i++) {
                 console.log(getlistdata[i]) 
            }
        }else{
            //get = '?pagina='+pagina+'&view='+view;
        }*/
  
        /*var pathArray = param['route'].split('?');
        if(pathArray.length>1){
            history.replaceState(null, null, window.location.pathname +'?'+ pathArray[1]);
        }*/
        //console.log(param['route'])
        $.ajax({
            url: param['route'],
            type:"GET",
            beforeSend: function (data) {
                load(param['result'])
            },
            success:function(respuesta){  
                $(param['result']).html(respuesta);
            }
        });
}	
function confirm(param){
	if(param['resultado']=='CORRECTO'){
		$(param['input']).html('<div class="cont-confirm">'+
                           '<div class="confirm"><i class="fa fa-check"></i></div>'+
                           '<div class="confirm-texto">¡Correcto!</div>'+
                           '<div class="confirm-subtexto">'+param['mensaje']+'</div></div>'+
                           '<div class="custom-form" style="text-align: center;">'+
                           '<button type="button" class="btn big-btn color-bg flat-btn" style="margin: auto;float: none;" onclick="confirm_cerrar(\''+param['cerrarmodal']+'\')">'+
                           '<i class="fa fa-check"></i> Aceptar</button></div>'); 
	}
}
function confirm_cerrar(cerrarmodal){
  $(cerrarmodal+' .close-reg').click();
}
function load(result){
    $(result).html('<div><img src="https://kayllapi.com/public/libraries/app/img/loading.gif"></div>');  
}  

function subir_archivo(param={},callback) {
    	$(param['input']).change(function(evt) {
            var files = evt.target.files;
            for (var i = 0, f; f = files[i]; i++) {
              if (!f.type.match('image.*')) {
                  continue;
              }
              var reader = new FileReader();
              reader.onload = (function(theFile) {
                  return function(e) {
                      callback({
                          'archivo' : e.target.result
                      });
                  };
              })(f);
              reader.readAsDataURL(f);
            }
    	});
}

function uploadfile(param){
	var imagen = param['image'];
	var style = 'style="'+
					'margin-top:10px;'+
                    'margin-left:10px;'+
					'font-size:18px;'+
					'background-color:#c12e2e;'+
					'padding:2px;'+
					'padding-left:9px;'+
					'padding-right:9px;'+
					'border-radius:15px;'+
					'color:#fff;'+
					'font-weight:bold;'+
					'cursor:pointer;'+
					'position: absolute;'+
					'z-index: 100;"';
	if(imagen!=undefined){
		if(imagen!=''){
			var src = param['ruta']+'/'+param['image'];
			var width = $(param['cont']).width();
	        var height = $(param['cont']).height();
      var imgant = param['input'].split('#');
			$(param['result'])
                      .html('<div '+style+' class="uploadfile-imagen-close" onclick="removeuploadfile(\''+param['result']+'\')">x</div>'+
                          	'<img src="'+src+'" style="max-width:'+width+'px;max-height:'+height+'px;position: relative;z-index: 1;">'+
                      		'<input type="hidden" value="'+param['image']+'" id="'+imgant[1]+'ant">');
		}
	}

    	$(param['input']).change(function(evt) {
            var files = evt.target.files;
            for (var i = 0, f; f = files[i]; i++) {
              if (!f.type.match('image.*')) {
                  continue;
              }
              var reader = new FileReader();
              reader.onload = (function(theFile) {
                  return function(e) {
                  	var width = $(param['cont']).width();
        			var height = $(param['cont']).height();
                    $(param['result'])
                      .html('<div '+style+' class="uploadfile-imagen-close" onclick="removeuploadfile(\''+param['result']+'\')">x</div>'+
                      		'<img src="'+e.target.result+'" style="max-width:'+width+'px;max-height:'+height+'px;position: relative;z-index: 1;">');
                  };
              })(f);
              reader.readAsDataURL(f);
            }
    	});
}
function removeuploadfile(result){
  $(result).html('<input type="hidden" id="imagenant"/>');
}
/* ---------------------------- scripts ----------------------------------------*/
function modal(param){
	  var idmodal = param['click'].split('#');
    var modal = {};
    modal.hide = function () {
        $('.'+idmodal[1]).fadeOut();
        $("html, body").removeClass("hid-body");
    };
    var a = 'a';
    if(idmodal[0]!=''){
        a = idmodal[0];
    }

    $(a+'#'+idmodal[1]).on("click", function (e) {
        e.preventDefault();
        $('.'+idmodal[1]).fadeIn();
        $("html, body").addClass("hid-body");
        if(param['screen']=='fullscreen'){
            $('.'+idmodal[1]+' .main-register-holder').addClass('mx-modal-fullscreen');
        }else if(param['screen']=='normal'){
            $('.'+idmodal[1]+' .main-register-holder').addClass('mx-modal-normal');
        }else if(param['screen']=='alert'){
            $('.'+idmodal[1]+' .main-register-holder').addClass('mx-modal-alert');
        }
      
        /*$.ajax({
            url: param['route'],
            type: 'GET',
            beforeSend: function () {
                load(param['result'])               
            },
            success:function(dato){
                $(param['result']).html(dato);
            }
        });*/
    });
    $('.'+idmodal[1]+' .close-reg').on("click", function () {
        modal.hide();
        $('#modal_cuerpo_'+idmodal[1]).html(''); 
    });
}
function file(param){
  var click = param['click'].split('#');
  if(click.length==0){
      click = param['click'].split('.');
  }
  const file = document.querySelector(param['click']);
  file.addEventListener('change', (e) => {
    const [file] = e.target.files;
    const { name: fileName, size } = file;
    const fileSize = (size / 1000).toFixed(2);
    const fileNameAndSize = `(${fileName} - ${fileSize}KB)`;
    document.querySelector('#file-result-'+click[1]).textContent = fileNameAndSize;
  });
}
function tab(param){
  var i=0;
  $(param['click']+" > .tabs-menu > li").each(function() {
      $(this).attr("id",i);
      var href = $(param['click']+" > .tabs-menu > li#"+i+" > a").attr("href");
      //$(href).css("display",'none');
      //if(i==0 /*&& Cookies.get('current')==undefined*/){
          //$(this).attr("class",'current');
          //$(href).css("display",'block');
      //}
      /*else if(Cookies.get('current')==href){
          $(this).attr("class",'current');
          $(href).css("display",'block');
      }*/
      i++;
  });
	$(param['click']+" > .tabs-menu a").on("click", function (e) {
        e.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var b = $(this).attr("href");
        $(param['click']+" > .tab > .tab-content").not(b).css("display", "none");
        $(b).fadeIn();
      
        Cookies.set('current',b);
    });
}

function singleMap(param) {
        var latitud = -12.071871667822409;
        var longitud = -75.21026847919165;
        if(param['lat']!=undefined && param['lat']!=''){
            latitud = parseFloat(param['lat']);
        }
        if(param['lng']!=undefined && param['lat']!=''){
            longitud = parseFloat(param['lng']);
        }
        var myLatLng = {
            lat: latitud,
            lng: longitud,
        };
        var mapa = param['map'].split('#');
        var single_map = new google.maps.Map(document.getElementById(mapa[1]), {
            zoom: 16,
            center: myLatLng,
            scrollwheel: false,
            zoomControl: false,
            mapTypeControl: false,
            scaleControl: false,
            panControl: false,
            navigationControl: false,
            streetViewControl: false,
            styles: [{
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [{
                    "color": "#f2f2f2"
                }]
            }]
        });
        var markerIcon2 = {
            url: raiz()+'/public/backoffice/sistema/marker.png',
        }
        
        var result_draggable = param['draggable'];
        if(param['draggable']==undefined){
            result_draggable = true
        }
        

            var marker = new google.maps.Marker({
                position: myLatLng,
                draggable: result_draggable,
                map: single_map,
                icon: markerIcon2,
                title: 'Your location'
            });
            google.maps.event.addListener(marker, 'dragend', function (event) {
                $(param['result_lat']).val(event.latLng.lat());
                $(param['result_lng']).val(event.latLng.lng());
            });

    
        var zoomControlDiv = document.createElement('div');
        var zoomControl = new ZoomControl(zoomControlDiv, single_map);

        function ZoomControl(controlDiv, single_map) {
            zoomControlDiv.index = 1;
            single_map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(zoomControlDiv);
            controlDiv.style.padding = '5px';
            var controlWrapper = document.createElement('div');
            controlDiv.appendChild(controlWrapper);
            var zoomInButton = document.createElement('div');
            zoomInButton.className = "mapzoom-in";
            controlWrapper.appendChild(zoomInButton);
            var zoomOutButton = document.createElement('div');
            zoomOutButton.className = "mapzoom-out";
            controlWrapper.appendChild(zoomOutButton);
            google.maps.event.addDomListener(zoomInButton, 'click', function () {
                single_map.setZoom(single_map.getZoom() + 1);
            });
            google.maps.event.addDomListener(zoomOutButton, 'click', function () {
                single_map.setZoom(single_map.getZoom() - 1);
            });
        }
        
      
    }    
function singleMap_address(param) {
        var myLatLng = {
            lat: -12.071871667822409,
            lng: -75.21026847919165,
        };
        var mapa = param['map'].split('#');
        var single_map = new google.maps.Map(document.getElementById(mapa[1]), {
            zoom: 16,
            center: myLatLng,
            scrollwheel: false,
            zoomControl: false,
            mapTypeControl: false,
            scaleControl: false,
            panControl: false,
            navigationControl: false,
            streetViewControl: false,
            styles: [{
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [{
                    "color": "#f2f2f2"
                }]
            }]
        });
        var markerIcon2 = {
            url: raiz()+'/public/backoffice/sistema/marker.png',
        }
        
        var result_draggable = param['draggable'];
        if(param['draggable']==undefined){
            result_draggable = true
        }
        

            var address = 'HUANCAYO';
            if(param['address']!=undefined && param['address']!=''){
                address = param['address'];
            }
  
            geocoder = new google.maps.Geocoder();
            geocoder.geocode( { 'address': address}, function(results, status) {
              if (status == 'OK') {
                  single_map.setCenter(results[0].geometry.location);
                  var marker = new google.maps.Marker({
                      position: results[0].geometry.location,
                      draggable: result_draggable,
                      map: single_map,
                      icon: markerIcon2,
                      title: 'Your location'
                  });
                      $(param['result_lat']).val('');
                      $(param['result_lng']).val('');
                  // Marcador
                  google.maps.event.addListener(marker, 'dragend', function (event) {
                      $(param['result_lat']).val(event.latLng.lat());
                      $(param['result_lng']).val(event.latLng.lng());
                  });
              } else {
                alert('Geocode no tuvo éxito por la siguiente razón: ' + status);
              }
            });

    
        var zoomControlDiv = document.createElement('div');
        var zoomControl = new ZoomControl(zoomControlDiv, single_map);

        function ZoomControl(controlDiv, single_map) {
            zoomControlDiv.index = 1;
            single_map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(zoomControlDiv);
            controlDiv.style.padding = '5px';
            var controlWrapper = document.createElement('div');
            controlDiv.appendChild(controlWrapper);
            var zoomInButton = document.createElement('div');
            zoomInButton.className = "mapzoom-in";
            controlWrapper.appendChild(zoomInButton);
            var zoomOutButton = document.createElement('div');
            zoomOutButton.className = "mapzoom-out";
            controlWrapper.appendChild(zoomOutButton);
            google.maps.event.addDomListener(zoomInButton, 'click', function () {
                single_map.setZoom(single_map.getZoom() + 1);
            });
            google.maps.event.addDomListener(zoomOutButton, 'click', function () {
                single_map.setZoom(single_map.getZoom() - 1);
            });
        }
        
      
    }    
/* ------------------- redondear numero --------------------- */
  function decimalAdjust(type, value, exp) {
    // Si el exp no está definido o es cero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // Si el valor no es un número o el exp no es un entero...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
      return NaN;
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
  }

  // Decimal round
  if (!Math.round10) {
    Math.round10 = function(value, exp) {
      return decimalAdjust('round', value, exp);
    };
  }
  // Decimal floor
  if (!Math.floor10) {
    Math.floor10 = function(value, exp) {
      return decimalAdjust('floor', value, exp);
    };
  }
  // Decimal ceil
  if (!Math.ceil10) {
    Math.ceil10 = function(value, exp) {
      return decimalAdjust('ceil', value, exp);
    };
  }


/* ------------------- texto ayuscula --------------------- */

function texto_mayucula(pthis){
  pthis.value = pthis.value.toUpperCase();
}
    