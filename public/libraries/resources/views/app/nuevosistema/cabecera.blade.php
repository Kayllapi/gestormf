
      <input type="hidden" id="idmodulotabla">
      @if(isset($botones))
      @foreach($botones as $value)
      <?php 
      $view = explode(':',$value);
      ?>
      @if(count($view)>1)
          
          <?php 
          $viewmodulo = $view[0];
          $titulo = '';
          $view = explode('/',$view[1]);
          if(count($view)>1){
              $viewmodulo1 = $view[0];
              $titulo = $view[1];
          }
          ?>

          @if($viewmodulo1=='registrar')
              <a href="javascript:;" id="modal-registrar-submodulo" onclick="modulo_create('{{$_GET['nombre_modulo']}}','{{$_GET['imagen_modulo']}}',{{$_GET['idmodulo']}},'{{$_GET['nombre_submodulo']}}','{{$_GET['imagen_submodulo']}}',{{$_GET['idsubmodulo']}},'{{$_GET['name_modulo']}}','{{$titulo}}','{{$viewmodulo1}}')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
                  <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/registrar.png')}}" class="category-bubble-icon">
                <h2 data-v-b789b216="" class="category-bubble-title">{{$titulo}}</h2>
              </a>
              @include('app.nuevosistema.modal',[
                  'name'=>'modal-registrar-submodulo',
                  'screen'=>'fullscreen'
              ])
          @elseif($viewmodulo1=='editar')
              <a href="javascript:;" id="modal-editar-submodulo" onclick="modulo_edit('{{$_GET['nombre_modulo']}}','{{$_GET['imagen_modulo']}}',{{$_GET['idmodulo']}},'{{$_GET['nombre_submodulo']}}','{{$_GET['imagen_submodulo']}}',{{$_GET['idsubmodulo']}},'{{$_GET['name_modulo']}}','{{$titulo}}','{{$viewmodulo1}}')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
                  <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/editar.png')}}" class="category-bubble-icon">
                <h2 data-v-b789b216="" class="category-bubble-title">{{$titulo}}</h2>
              </a>
              @include('app.nuevosistema.modal',[
                  'name'=>'modal-editar-submodulo',
                  'screen'=>'fullscreen'
              ])
          @elseif($viewmodulo1=='eliminar')
              <a href="javascript:;" id="modal-eliminar-submodulo" onclick="modulo_delete('{{$_GET['nombre_modulo']}}','{{$_GET['imagen_modulo']}}',{{$_GET['idmodulo']}},'{{$_GET['nombre_submodulo']}}','{{$_GET['imagen_submodulo']}}',{{$_GET['idsubmodulo']}},'{{$_GET['name_modulo']}}','{{$titulo}}','{{$viewmodulo1}}')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
                  <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/eliminar.png')}}" class="category-bubble-icon">
                <h2 data-v-b789b216="" class="category-bubble-title">Eliminar</h2>
              </a>
              @include('app.nuevosistema.modal',[
                  'name'=>'modal-eliminar-submodulo',
                  'screen'=>'fullscreen'
              ])
  
          @else
              <a href="javascript:;" id="modal-otro-submodulo" onclick="modulo_other('{{$_GET['nombre_modulo']}}','{{$_GET['imagen_modulo']}}',{{$_GET['idmodulo']}},'{{$_GET['nombre_submodulo']}}','{{$_GET['imagen_submodulo']}}',{{$_GET['idsubmodulo']}},'{{$_GET['name_modulo']}}','{{$titulo}}','{{$viewmodulo1}}')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
                  <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/otro.png')}}" class="category-bubble-icon">
                <h2 data-v-b789b216="" class="category-bubble-title">{{$titulo}}</h2>
              </a>
              @include('app.nuevosistema.modal',[
                  'name'=>'modal-otro-submodulo',
                  'screen'=>'fullscreen'
              ])
          @endif
      @else
          @if($value=='atrasmodulo')
              <a href="javascript:;" id="cont-submodulo{{$_GET['idmodulo']}}" onclick="ir_modulo()" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo">
                <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/atras.png')}}" class="category-bubble-icon">
                <h2 data-v-b789b216="" class="category-bubble-title">Ir a Inicio</h2>
              </a>
          @elseif($value=='atrassubmodulo')
              <a href="javascript:;" onclick="ir_submodulo({{$_GET['idmodulo']}},'{{$_GET['nombre_modulo']}}')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-modulo">
                <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/atras.png')}}" class="category-bubble-icon">
                <h2 data-v-b789b216="" class="category-bubble-title">Atras</h2>
              </a> 
          @elseif($value=='actualizar')
              <a href="javascript:;" id="modal-actualizar-submodulo" onclick="modulo_actualizar('{{$_GET['name_modulo']}}')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
                  <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/actualizar_tabla.png')}}" class="category-bubble-icon">
                <h2 data-v-b789b216="" class="category-bubble-title">Actualizar</h2>
              </a>
          @elseif($value=='buscar')
              <a href="javascript:;" id="modal-buscar-submodulo" onclick="modulo_buscar('{{$_GET['name_modulo']}}')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
                  <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/buscar.png')}}" class="category-bubble-icon">
                <h2 data-v-b789b216="" class="category-bubble-title">Buscar</h2>
              </a>
          @endif
      @endif
      @endforeach
      @endif



                                    