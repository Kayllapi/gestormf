@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuración General',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])


<form action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
          method: 'PUT',
          data:{
              view: 'config_general'
          }
      },
      function(resultado){
          location.reload();
      },this)">
        <div class="tabs-container" id="tab-menuconfiguracion">
            <ul class="tabs-menu">
                <li class="current"><a href="#tab-menuconfiguracion-1">General</a></li>
                <li><a href="#tab-menuconfiguracion-2">Imagenes</a></li>
            </ul>
            <div class="tab">
                <div id="tab-menuconfiguracion-1" class="tab-content" style="display: block;">
                    <label>Color de Sistema</label>
                    <input type="color" value="{{ configuracion($tienda->id,'sistema_color')['valor'] }}" id="sistema_color" step="0.01" min="0">
                    <label>Ancho de Ticket (centimetro)</label>
                    <input type="number" value="{{ configuracion($tienda->id,'sistema_anchoticket')['valor'] }}" id="sistema_anchoticket" step="0.01" min="0">
                    <label>Ancho de Tarjeta de Pago (centimetro)</label>
                    <input type="number" value="{{ configuracion($tienda->id,'sistema_anchotarjetapago')['valor'] }}" id="sistema_anchotarjetapago" step="0.01" min="0">
                    <label>Tipo de Letra de Ticket</label>
                    <select id="sistema_tipoletra">
                       <option></option>
                         <option value="Arial,Helvetica,sans-serif">Arial</option>
                         <option value="Courier New,Courier,monospace">Courier New</option>
                         <!--option value="Comic Sans MS,cursive">Comic Sans MS</option-->
                         <!--option value="Georgia,serif">Georgia</option-->
                         <!--option value="Lucida Sans Unicode,Lucida Grande,sans-serif">Lucida Sans Unicode</option-->
                         <!--option value="Tahoma,Geneva,sans-serif">Tahoma</option-->
                         <!--option value="Times New Roman,Times,serif">Times New Roman</option-->
                         <!--option value="Trebuchet MS,Helvetica,sans-serif">Trebuchet MS</option-->
                         <!--option value="Verdana,Geneva,sans-serif">Verdana</option-->
                    </select>
                </div>
                <div id="tab-menuconfiguracion-2" class="tab-content" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Imagen de Fondo para Login</label>
                            <div class="fuzone" id="cont-fileupload" style="height: 177px;">
                                <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                                <input type="file" class="upload" id="imagen">
                                <div id="resultado-imagen"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Imagen de Fondo para Sistema (2000x750)</label>
                            <div class="fuzone" id="cont-fileupload-portada" style="height: 177px;">
                                <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                                <input type="file" class="upload" id="imagenportada">
                                <div id="resultado-portada"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>
@endsection
@section('subscripts')
<script>
    tab({click:'#tab-menuconfiguracion'});
  
    @if(configuracion($tienda->id,'sistema_imagenfondologin')['resultado']=='CORRECTO')
        uploadfile({
          input:"#imagen",
          cont:"#cont-fileupload",
          result:"#resultado-imagen",
          ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/imagenlogin/')}}",
          image: "{{ configuracion($tienda->id,'sistema_imagenfondologin')['valor'] }}"
        });
    @else
        uploadfile({
          input:"#imagen",
          cont:"#cont-fileupload",
          result:"#resultado-imagen"
        }); 
    @endif
  
    @if(configuracion($tienda->id,'sistema_imagenfondosistema')['resultado']=='CORRECTO')
        uploadfile({
          input:"#imagenportada",
          cont:"#cont-fileupload-portada",
          result:"#resultado-portada",
          ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/imagensistema/')}}",
          image: "{{ configuracion($tienda->id,'sistema_imagenfondosistema')['valor']}}"
        });
    @else
        uploadfile({
          input:"#imagenportada",
          cont:"#cont-fileupload-portada",
          result:"#resultado-portada"
        }); 
    @endif
  
    @if(configuracion($tienda->id,'sistema_tipoletra')['resultado']=='CORRECTO')
        $("#sistema_tipoletra").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val('{{ configuracion($tienda->id,'sistema_tipoletra')['valor'] }}').trigger("change");    
    @else
        $("#sistema_tipoletra").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif

</script>
@endsection