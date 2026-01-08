<?php
// script input +
$e_inicio = strpos($input, '/+');
$e_final  = strpos($input, '+/');
$e_data   = substr($input, $e_inicio, $e_final);
$e_input  = '';
if(strlen($e_data)>0){
    $input  = str_replace($e_data,'',$input);
    $e_input  = '+'.substr($e_data, 2, strlen($e_data)-4);
}

// script val +
$v_val  = '';
if(isset($val)){
if($val!=''){
//$val = (string) $val;
$v_inicio = strpos($val, '/+');
$v_final  = strpos($val, '+/');
$v_data   = substr($val, $v_inicio, $v_final);
if(strlen($v_data)>0){
    $val  = str_replace($v_data,'',$val);
    $val  = str_replace('+/','',$val);
    $v_val  = '+'.substr($v_data, 2, strlen($v_data)-2);
}
}
}else{
    $val = '';
}
/*$v_val = '';
if(isset($val)){
if($val!=''){
$v_inicio  = strpos($val, '/+');
$v_cant = substr($val,0,$v_inicio);
$v_val = substr($val,$v_inicio);
if(strlen($v_cant)>0){
    $v_val  = str_replace('/+','+',$v_val);
}
$new_val  = str_replace($v_val,'',$val);
if($new_val!=''){
    $new_val = "'".$new_val."'";
}
$v_val  = str_replace('/+','',$v_val);
$v_val  = str_replace('+/','',$v_val);
}
}*/
?>
@if(isset($json))  
    <?php 
    $jsondata = explode(':',$json); 
    $urltienda = '';
    $modulo = $json;
    if(count($jsondata)>1){
        $urltienda = $jsondata[0];
        $modulo = $jsondata[1];
    }
    ?>
    @if($urltienda=='tienda')
      @if($modulo=='facturacionboletafactura' or $modulo=='ventacredito')
          $.getJSON('{{url('public/backoffice/tienda/'.$tienda->id.'/sistema_json/'.$modulo.'_'.Auth::user()->idsucursal.'.json')}}?token='+Math.floor((Math.random() * 100) + 1)).done(function(data) {
      @else
          $.getJSON('{{url('public/backoffice/tienda/'.$tienda->id.'/sistema_json/'.$modulo.'.json')}}?token='+Math.floor((Math.random() * 100) + 1)).done(function(data) {
      @endif
    @else
        $.getJSON('{{url('public/nuevosistema/librerias/json/'.$modulo.'.json')}}?token='+Math.floor((Math.random() * 100) + 1)).done(function(data) {
    @endif
        $('{{$input}}'{{$e_input}}).select2({
            data: data.data,
            matcher: function (params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }

                terms=(params.term).split(' ');

                for (var i = 0; i < terms.length; i++) {
                    if (((data.text).toUpperCase()).indexOf((terms[i]).toUpperCase()) == -1) 
                    return null;
                }
                return data;
            },
            placeholder: '-- Seleccionar --',
            @if($modulo=='categoria' or 
                $modulo=='marca' or 
                $modulo=='unidadmedida')
            theme: 'bootstrap-5',
            //dropdownParent: $('{{$input}}').parents('.modal .modal-content'),
            dropdownParent: $('.modal .modal-content').html()!=undefined?$('.modal .modal-content'):$('{{$input}}'{{$e_input}}).parent().parent(),
            @elseif($modulo=='usuario' or 
                $modulo=='usuarioacceso' or 
                $modulo=='ubigeo' or 
                $modulo=='facturacionboletafactura')
            minimumInputLength: 2,
            theme: 'bootstrap-5',
            dropdownParent: $('.modal .modal-content').html()!=undefined?$('.modal .modal-content'):$('{{$input}}'{{$e_input}}).parent().parent(),
            @elseif($modulo=='producto')
            minimumInputLength: 2,
            theme: 'bootstrap-5',
            dropdownParent: $('.modal .modal-content').html()!=undefined?$('.modal .modal-content'):$('{{$input}}'{{$e_input}}).parent().parent(),
            templateResult: function (state) {
                if (!state.id) {
                    return state.text;
                }
                return $('<div>'+
                         '<div style=\'background-image: url('+state.imagen+');'+
                                    'background-repeat: no-repeat;'+
                                    'background-size: contain;'+
                                    'background-position: center;'+
                                    'width: 44px;'+
                                    'height: 44px;'+
                                    'float: left;'+
                                    'margin-right: 5px;'+
                                    'margin-top: -10px;\'>'+
                                  '</div><div>'+(state.codigo!=''?state.codigo+' - ':'')+state.nombre+'</div><div>'+state.preciopublico+' '+state.unidadmedidanombre+'</div>');
            },
            templateSelection: function (repo) {
                if (!repo.id) {
                    return repo.text;
                }
                if(repo.codigo!=''){
                    if(repo.codigo==undefined){
                        return $('<span>'+repo.text+'</span>');
                    }
                    return $('<span>'+(repo.codigo!=''?repo.codigo+' - ':'')+repo.nombre+'</span>');
                }else{
                    return $('<span>'+repo.nombre+'</span>');
                }
            },
            @else
            minimumResultsForSearch: -1,
            theme: 'bootstrap-5',
            dropdownParent: $('.modal .modal-content').html()!=undefined?$('.modal .modal-content'):$('{{$input}}'{{$e_input}}).parent().parent(),
            @endif  
        });    
        @if(isset($val) or isset($v_val))
        @if($val!='' or $v_val!='')
            $('{{$input}}'{{$e_input}}).val('{{$val}}'{{$v_val}}).trigger('change');
        @endif
        @endif
    });
@else
    $('{{$input}}'{{$e_input}}).select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
        theme: 'bootstrap-5',
        dropdownParent: $('{{$input}}'{{$e_input}}).parent().parent()
    });
    @if(isset($val) or isset($v_val))
    @if($val!='' or $v_val!='')
        $('{{$input}}'{{$e_input}}).val('{{$val}}'{{$v_val}}).trigger('change');
    @endif
    @endif
@endif
