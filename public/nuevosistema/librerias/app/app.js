/*$(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
});*/

function raiz() {
  var url = $('body').attr('url');
  if(url!=''){
      return url+'/';
  }else{
      return '';
  }
  
}
function plugins_popover() {
    //const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    //const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
  
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl)
    })

}	
function plugins_mayuscula() {
    /*$("input[type=text],textarea").keyup(function() {
        $(this).val($(this).val().toUpperCase());
    });*/
}	
function carga(param) {
    removecarga({input:param['input']});
    var inp = param['input'].split('#');
    if (param['color']=='success') {
        $(param['input'])
          .addClass('mx-carga')
          .append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert">'+
                  '<div class="mx-contenedor-subcarga alert-'+param['color']+'" data-dismiss="alert" aria-label="Close">'+
                      '<i class="fa-solid fa-check"></i>'+
                      '<div id="mx-mensaje-subcarga-icon">'+param['mensaje']+'</div>'+
                  '</div></div>');
    }else if (param['color']=='danger' || param['color']=='warning') {
        $(param['input'])
          .addClass('mx-carga')
          .append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert">'+
                  '<div class="mx-contenedor-subcarga alert-'+param['color']+'" data-dismiss="alert" aria-label="Close">'+
                      '<i class="fa-solid fa-circle-exclamation"></i>'+
                      '<div id="mx-mensaje-subcarga-icon">'+param['mensaje']+'</div>'+
                  '</div></div>');
    }else if (param['color']=='info'){
        $(param['input'])
          .addClass('mx-carga')
          .append('<div onclick="removecarga({input:\''+param['input']+'\'})" id="mx-subcarga'+inp[1]+'" class="mx-subcarga alert">'+
                  '<div class="mx-contenedor-subcarga alert-primary" data-dismiss="alert" aria-label="Close">'+
                      '<div id="preloader"></div>'+
                      '<div id="mx-mensaje-subcarga">'+param['mensaje']+'</div>'+
                  '</div></div>');
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
                      //arrayelemet[$(this).attr('id')] = $(this).val();
                      /*arrayelemet.push({ 
                          id    : $(this).attr('id'), 
                          value : $(this).val(),
                      });*/
									});
		//               element[$(this).attr('id')]  = arrayelemet;
									formData.append($(this).attr('id'), JSON.stringify(arrayelemet));
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
				if(param['data']!=undefined) {
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
        }
        if(param['dataimage']!=undefined) {
				$.each(param['dataimage'], function( key, value ) {
						var countelemet = $('input'+value).prop('files')[0];
            formData.append(key, countelemet);
        });
        }
        return formData;
}
function formerror(param) {
        var errorsHtml= '';
        var i=0
        $('.class-input').removeAttr('style');
        //$('.error-input').remove();
        $('.errors').remove();
        $.each(param['dato'].responseJSON.errors, function( key, value ) {
            $('input#'+key).addClass('class-input').css('border','1px solid #f54708');
            $('input#'+key).after('<a href="javascript:;" class="errors" data-bs-toggle="popover" data-bs-placement="right" data-bs-content=\''+value+'\'><i class="fa-solid fa-circle-exclamation"></i></a>');
            $('select#'+key+' + span > span > span').addClass('class-input').css('border','1px solid #f54708');
            $('select#'+key+' + span > span > span > span > span').css('color','#f54708');
            $('select#'+key+' + span').after('<a href="javascript:;" class="errors" data-bs-toggle="popover" data-bs-placement="right" data-bs-content=\''+value+'\'><i class="fa-solid fa-circle-exclamation"></i></a>');
            if (i==0) {
               errorsHtml += value; 
               $('#'+key).focus();
            }
            i=i+1;
        plugins_popover();
        });
        return errorsHtml;
}

function callback(param={},callback,thisp=null) {
    
    //$('.class-input').removeAttr('style');
    //$('.error-input').remove();
    var pathArray = param['route'].split( '/' );
    if(param['form']==undefined){
        param['form'] = '#form'+Math.floor((Math.random() * 100) + 1);
    }
    // asigar para carga
    if(thisp!=null){
        var form = param['form'].split('#');
        $(thisp).attr('id',form[1]);
    }

    if(param['data']==undefined) {
        param['data'] = {};
    }
    if(param['dataimage']==undefined) {
        param['dataimage'] = {};
    }
  
    param['dato'] = forminput({form:param['form'],data:param['data'],dataimage:param['dataimage']});

    if(param['carga']==undefined) {
        param['carga'] = '#mx-carga';
        var countcarga = $(param['carga']).length;
        if(countcarga==0){
            var mxcarga = param['carga'].split('#');
            $(param['form']).after('<div id="'+mxcarga[1]+'"></div>');
            $(param['form']).appendTo(param['carga']);
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
        url: param['route'],
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
            //resetear_cierresesion();
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
           if(respuesta.responseJSON.message=='Your email address is not verified.'){
              carga({
                    input:param['carga'],
                    color:'success',
                    mensaje:'Ingresando, Espere por favor...'
              });
              callback({
                  resultado: 'ERRORCONFIRMEMAIL'
              });
           }else{
              removecarga({input:param['carga']});
              //formerror({dato:respuesta});
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
    $.ajax({
        url: param['route'],
        type:"GET",
        beforeSend: function (data) {
            if(param['carga']!='false') {
                load(param['result']);
            }
            //resetear_cierresesion();
        },
        success:function(respuesta){  
            $(param['result']).html(respuesta);
        }
    });
}	

function modal(param) {
    var key = $('.modal').length;
  
    if(param['size']==undefined) {
        param['size'] = 'modal-lg';
    }
  
    $('body')
    .append('<div class="modal fade" id="mx-modal'+key+'" aria-labelledby="exampleModalLabel" aria-hidden="true">'+
        '<div class="modal-dialog '+param['size']+'">'+
              '<div class="modal-content">'+
                '<div id="mx-modal-cuerpo'+key+'" style="margin-left: 5px;margin-right: 5px;"></div>'+
              '</div>'+
        '</div> '+
    '</div>');
  
    var myModal = new bootstrap.Modal(document.getElementById('mx-modal'+key), {
        backdrop: 'static',
        keyboard: false,
    });
    myModal.show();
  
    $(".modal").on('shown.bs.modal', function() {
        const zIndex = 1040 + 10 * $('.modal:visible').length;
        $(this).css('z-index', zIndex);
        setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
    });

    $.ajax({
        url: param['route'],
        type: 'GET',
        beforeSend: function () {
            load('#mx-modal-cuerpo'+key);
            //resetear_cierresesion();
        },
        success:function(dato){
            $('#mx-modal-cuerpo'+key).html(dato);
            //plugins_popover();
            //plugins_mayuscula();
        }
    });
  
    $('#mx-modal'+key).on('hide.bs.modal', function (e) {
      $(this).remove();
      //$('.modal-backdrop').remove();
    })
}

function json(param={},json,thisp=null) {
    var context;
    $.ajax({
        url: param['route']+'?token='+Math.floor((Math.random() * 100) + 1),
        //async: false,
        dataType: 'json',
        success: function (json) {   
            assignVariable(json);
        }
    });
  
    function assignVariable(data) {
        var resultado = [];
        const responseData = data.data;
        if(param['search']!=undefined){
            $.each(param['search'], function( key_search, value_search ) {
                $.each(responseData, function( key, value ) {
                    if(value[key_search]==param['search'][key_search]){
                        resultado.push(value);
                    }
                });
            });
            json(resultado[0]);
        }else{
            json(responseData);
        }
    }
}

function uploadfile(param){
    
	  if(param['image']!=undefined){
        if(param['image']!=''){
            var src = param['ruta']+'/'+param['image'];
            var imgant = param['input'].split('#');
            $(param['result']).addClass('fuzone-image');
            $(param['result']).attr('style','background-image:url('+src+')');
            $(param['result']).html('<div class="fuzone-close" onclick="removeuploadfile(\''+param['result']+'\',\''+imgant[1]+'ant\')">x</div>'+
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
                    $(param['result']).addClass('fuzone-image');
                    $(param['result']).attr('style','background-image:url('+e.target.result+')');
                    $(param['result']).html('<div class="fuzone-close" onclick="removeuploadfile(\''+param['result']+'\',\''+imgant[1]+'ant\')">x</div>');
                };
            })(f);
            reader.readAsDataURL(f);
        }
    });
}
function removeuploadfile(result,imageant){
  $(result).removeClass('fuzone-image');
  $(result+' .fuzone-close').remove();
  $(result).html('<input type="hidden" id="'+imageant+'"/>');
}

function load(result){
    $(result).html('<div style="text-align: center;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');  
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
            /*scrollwheel: false,
            zoomControl: false,
            mapTypeControl: false,
            scaleControl: false,
            panControl: false,
            navigationControl: false,
            streetViewControl: false,*/
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
        //scrollwheel: false,
        /*zoomControl: false,
        mapTypeControl: false,
        scaleControl: false,
        panControl: false,
        navigationControl: false,
        streetViewControl: false,*/
        styles: [{
            "featureType": "landscape",
            "elementType": "all",
            "stylers": [{
                "color": "#f2f2f2"
            }]
        }]
    });
    var markerIcon2 = {
        url: raiz()+'/public/backoffice/nuevosistema/icono/marker.png',
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
            alert('Geocode no tuvo Ã©xito por la siguiente razÃ³n: ' + status);
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
function stock_presentacion(p_stock,p_por){
    let stock = parseFloat(p_stock/p_por);
    let stock_entero = parseInt(stock);
    let stock_decimal = (stock-stock_entero).toFixed(3);
    let stock_restante = (stock_decimal*p_por).toFixed(3);
    let stock_unidad = '';
    if(stock_restante!=0){
        stock_unidad = '('+stock_restante+')'
    }
  
    return stock_entero+' '+stock_unidad;
}
/* ------------------- redondear numero --------------------- */
  function decimalAdjust(type, value, exp) {
    // Si el exp no estÃ¡ definido o es cero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // Si el valor no es un nÃºmero o el exp no es un entero...
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



/* ------------------- contador regresivo de sesion --------------------- */
 /*  var timeLimit = 5; //tiempo en minutos
   var conteo_nuevo = new Date(timeLimit * 60000);
   var conteo = new Date(timeLimit * 60000);
  
   contador_cierresesion();

   function contador_cierresesion(){
      intervaloRegresivo = setInterval("regresiva_cierresesion()", 1000);
   }

   function regresiva_cierresesion(){
      if(conteo.getTime() > 0){
         conteo.setTime(conteo.getTime() - 1000);
      }else{
         clearInterval(intervaloRegresivo);
          // cerrar sesion
         document.getElementById('logout-form-sistema').submit();
      }

      $('#contador_cierresesion').html((conteo.getMinutes()).toString().padStart(2, '0') + ":" + (conteo.getSeconds()).toString().padStart(2, '0'));
   }
   function resetear_cierresesion(){
      conteo.setTime(conteo_nuevo.getTime());
      //pagina({route:'/create',result:''});
   }*/

