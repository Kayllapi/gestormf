<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'editar','id'=>$usuario->id,'estado_numtelefono'=> ($modulo_prestamo!=''? 'required':'')])> 
<div class="tabs-container" id="tab-usuariomenu">
    <ul class="tabs-menu">
        <li class="current"><a href="#tab-usuariomenu-1">General</a></li>
        <li><a href="#tab-usuariomenu-2">Adicional</a></li>
        <li><a href="#tab-usuariomenu-3">Contacto</a></li>
    </ul>
    <div class="tab">
        <div id="tab-usuariomenu-1" class="tab-content" style="display: block;">
                <div class="row">
                    <div class="col-md-6">
                        <label>Tipo de Persona *</label>
                        <select id="idtipopersona" disabled>
                            @foreach($tipopersonas as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                        <div id="cont-juridica" style="display:none;">
                            <label>RUC *</label>
                            <input type="text" value="{{ $usuario->identificacion }}" id="ruc"/>
                        </div>
                        <div id="cont-natural" style="display:none;">
                            <label>DNI *</label>
                            <input type="text" value="{{ $usuario->identificacion }}" id="dni"/>
                        </div>
                        <div id="cont-carnetextranjeria" style="display:none;">
                            <label>Carnet Extranjería *</label>
                            <input type="text" value="{{ $usuario->identificacion }}" id="carnetextranjeria">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="cont-juridica1" style="display:none;">
                            <label>Nombre Comercial *</label>
                            <input type="text" value="{{ $usuario->nombre }}" id="nombrecomercial"/>
                            <label>Razòn Social *</label>
                            <input type="text" value="{{ $usuario->apellidos }}" id="razonsocial"/>
                        </div>
                        <div id="cont-natural1" style="display:none;">
                            <label>Nombre *</label>
                            <input type="text" value="{{ $usuario->nombre }}" id="nombre"/>
                            <label>Apellidos *</label>
                            <input type="text" value="{{ $usuario->apellidos }}" id="apellidos"/>
                        </div>
                        <div id="cont-carnetextranjeria1" style="display:none;">
                            <label>Nombre *</label>
                            <input type="text" value="{{ $usuario->nombre }}" id="nombre_carnetextranjeria">
                            <label>Apellidos *</label>
                            <input type="text" value="{{ $usuario->apellidos }}" id="apellidos_carnetextranjeria">
                        </div>
                    </div>
                </div>
        </div>
        <div id="tab-usuariomenu-2" class="tab-content" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <label>Imagen de Perfil</label>
                        <div class="fuzone" id="cont-fileupload">
                            <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
                            <input type="file" class="upload" id="imagen">
                            <div id="resultado-logo"></div>
                        </div>
                        <label>Fecha de Nacimiento</label>
                        <input type="date" id="fechanacimiento" value="{{ $usuario->fechanacimiento }}">

                        <label>Lugar de Nacimiento</label>
                        <select id="idubigeo_nacimiento">
                            @if($usuario->idubigeo_nacimiento!=0 or $usuario->idubigeo_nacimiento!='')
                            <option value="{{ $usuario->idubigeo_nacimiento }}">{{ $usuario->ubigeonacimientonombre }}</option>
                            @else
                            <option></option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">

                        <label>Genero</label>
                        <select id="idgenero">
                            <option value="1">Masculino</option>
                            <option value="2">Femenino</option>
                        </select>
                      
                        <label>Estado Civil</label>
                        <select id="idestadocivil">
                            <option></option>
                            @foreach ($estadocivil as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>

                        <label>Nivel de Estudio</label>
                        <select id="idnivelestudio">
                            <option></option>
                            @foreach ($nivelestudio as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                      
                        <label>Ocupación</label>
                        <input type="text" value="{{ $usuario->ocupacion }}" id="ocupacion"/>
                    </div>
                </div>
        </div>
        <div id="tab-usuariomenu-3" class="tab-content" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <label>Número de Teléfono {{$modulo_prestamo!=''? '*':''}}</label>
                        <input type="text" value="{{ $usuario->numerotelefono }}" id="numerotelefono"/>
                        <label>Correo Electrónico</label>
                        <input type="text" value="{{ $usuario->email }}" id="email"/>
                        <label>Referencia</label>
                        <input type="text" value="{{ $usuario->referencia }}" id="referencia"/>
                        <label>Ubicación (Ubigeo) *</label>
                        <select id="idubigeo">
                            <option value="{{$usuario->idubigeo}}">{{$usuario->ubigeonombre}}</option>
                        </select>
                        <label>Dirección *</label>
                        <input type="text" value="{{ $usuario->direccion }}" id="direccion"/>
                    </div>
                    <div class="col-md-6">
                      <label>Ubicación (Mover Marcador)</label>
                      <div id="domicilio_mapa" style="height: 317px;width: 100%;margin-bottom: 5px;border-radius: 5px;border: 1px solid #aaa;"></div>
                      <input type="hidden" value="{{$usuario->mapa_latitud}}" id="domicilio_mapa_latitud"/>
                      <input type="hidden" value="{{$usuario->mapa_longitud}}" id="domicilio_mapa_longitud"/>
                    </div>
                </div>
        </div>
    </div>
</div>
<button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>              
<script>
    // Tabulador de pestañas
    tab({click:'#tab-usuariomenu'});

    $('#idubigeo_nacimiento').select2({
        @include('app.select2_ubigeo')
    });
    $('#idgenero').select2({
        placeholder: '-- Seleccionar Genero --',
        minimumResultsForSearch: -1
    }).val({{ $usuario->idgenero ?? 0 }}).trigger("change");
    $('#idestadocivil').select2({
        placeholder: '-- Seleccionar Estado Civil --',
        minimumResultsForSearch: -1
    }).val({{ $usuario->idestadocivil ?? 0 }}).trigger("change");
    $('#idnivelestudio').select2({
        placeholder: '-- Seleccionar Nivel de Estudio --',
        minimumResultsForSearch: -1
    }).val({{ $usuario->idnivelestudio ?? 0 }}).trigger("change");

    $("#idtipopersona").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        $('#cont-juridica').css('display','none');
        $('#cont-natural').css('display','none');
        $('#cont-carnetextranjeria').css('display','none');
        $('#cont-juridica1').css('display','none');
        $('#cont-natural1').css('display','none');
        $('#cont-carnetextranjeria1').css('display','none');
        if(e.currentTarget.value == 1) {
            $('#cont-natural').css('display','block');
            $('#cont-natural1').css('display','block');
        }else if(e.currentTarget.value == 2) {
            $('#cont-juridica').css('display','block');
            $('#cont-juridica1').css('display','block');
        }else if(e.currentTarget.value == 3) {
            $('#cont-carnetextranjeria').css('display','block');
            $('#cont-carnetextranjeria1').css('display','block');
        }
    }).val({{$usuario->idtipopersona}}).trigger("change");

    $("#idubigeo").select2({
        @include('app.select2_ubigeo')
    });

    uploadfile({
        input: "#imagen",
        cont: "#cont-fileupload",
        result: "#resultado-logo",
        ruta: "{{ url('/public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
        image: "{{ $usuario->imagen }}"
    });
  
    singleMap({
        'map' : '#domicilio_mapa',
        'lat' : parseFloat( {{$usuario->mapa_latitud ?? '-12.071871667822409'}} ),
        'lng' : parseFloat( {{$usuario->mapa_longitud ?? '-75.21026847919165'}} ),
        'result_lat' : '#domicilio_mapa_latitud',
        'result_lng' : '#domicilio_mapa_longitud'
    });
</script>