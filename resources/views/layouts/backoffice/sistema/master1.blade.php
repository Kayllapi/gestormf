<?php
$moneda_soles = DB::table('s_moneda')->whereId(1)->first();
$moneda_dolares = DB::table('s_moneda')->whereId(2)->first();
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="robots" content="index, follow"/>
        <title>{{ $tienda->nombre }}</title>
        <link rel="shortcut icon" href="{{ $imagenfavicon }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"/>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"/>
        <link rel="stylesheet" href="{{ url('public/nuevosistema/librerias/app/app.css') }}"/>
</head>
<body url="{{ url('/') }}">
    <nav class="navbar navbar-expand-lg bg-body-tertiary" style="background-color: #343a40 !important;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">@if($tienda->imagen!='')
          <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }}" 
               style="height: 40px;float: left;margin-top: 7px;margin-right: 7px;">
          @endif
      {{ $tienda->nombre }}</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          @if(Auth::user()->idtienda==0)
            <?php
            $modulos = DB::table('modulo')
              ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
              ->join('roles','roles.id','rolesmodulo.idroles') 
              ->where('roles.idcategoria',$tienda->idcategoria)
              ->where('roles.name','administrador')
              ->where('modulo.idmodulo',7)
              ->where('modulo.idestado',1)
              ->select('modulo.*')
              ->orderBy('modulo.orden','asc')
              ->get();
            ?>
            <?php $i = 1  ; ?>
            <?php $cantmodulos = count($modulos); ?>
            @foreach($modulos as $value)
               <li class="nav-item dropdown">
                    <a href="javascript:;" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="{{ $value->icono }}"></i> {{ $value->nombre }}</a>
                    <ul class="dropdown-menu">
                    <?php
                    $submodulos = DB::table('modulo')
                      ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
                      ->join('roles','roles.id','rolesmodulo.idroles')
                      ->where('roles.idcategoria',$tienda->idcategoria)
                      ->where('roles.name','administrador')
                      ->where('modulo.idmodulo',$value->id)
                      ->where('modulo.idestado',1)
                      ->select('modulo.*')
                      ->orderBy('modulo.orden','asc')
                      ->get();
                    ?>
                    @foreach($submodulos as $subvalue)
                    @if($subvalue->vista!='' && $subvalue->controlador!='')
                          <?php $href = str_replace('{idtienda}', $tienda->id, $subvalue->vista); ?>
                          <li><a href="javascript:;" class="dropdown-item" onclick="pagina({route:'{{url($href)}}?view=tabla',result:'#cuerposistema'})"><i class="{{ $subvalue->icono }}"></i> {{ $subvalue->nombre }}</a></li> 
                    @endif
                    @endforeach
                    </ul>
                </li>
                <?php $i++; ?>
            @endforeach
          @else
            <?php
            $modulos = DB::table('modulo')
              ->join('usersrolesmodulo','usersrolesmodulo.idmodulo','modulo.id')
              ->where('usersrolesmodulo.idusers',Auth::user()->id)
              ->where('modulo.idmodulo',7)
              ->where('modulo.idestado',1)
              ->select('modulo.*')
              ->orderBy('modulo.orden','asc')
              ->get();

            $prestamo_estadocreditogrupal = 'prestamosolicitudgrupal';
            if(configuracion($tienda->id,'prestamo_estadocreditogrupal')['valor']==1){
                $prestamo_estadocreditogrupal = '';
            };
            $facturacion_estadofacturacion = 'Facturación';
            if(configuracion($tienda->id,'facturacion_estadofacturacion')['valor']==1){
                $facturacion_estadofacturacion = '';
            };

            $prestamo_estadocreditoprendario = '';
            $prestamo_estadoahorro = '';
            $prestamo_estadoahorro_1 = '';
            $prestamo_estadoahorro_2 = '';
            $ecommerce = '';
            if($tienda->idcategoria==13){
                $prestamo_estadocreditoprendario = 'Inventario';
                $ecommerce = 'Ecommerce';
                if(configuracion($tienda->id,'prestamo_estadocreditoprendario')['valor']==1){
                    $prestamo_estadocreditoprendario = '';
                    $ecommerce = '';
                };
                $prestamo_estadoahorro = 'Ahorros';
                $prestamo_estadoahorro_1 = 'ahorroconfirmacion';
                $prestamo_estadoahorro_2 = 'ahorrorecaudacion';
                if(configuracion($tienda->id,'prestamo_ahorro_estadoahorro')['valor']==1){
                    $prestamo_estadoahorro = '';
                    $prestamo_estadoahorro_1 = '';
                    $prestamo_estadoahorro_2 = '';
                };
            }
            ?>
            <?php $i = 1  ; ?>
            <?php $cantmodulos = count($modulos); ?>
            @foreach($modulos as $value)
              @if($prestamo_estadocreditoprendario==$value->nombre or 
                  $prestamo_estadoahorro==$value->nombre or 
                  $facturacion_estadofacturacion==$value->nombre or 
                  $ecommerce==$value->nombre)   
              @else 
               <li><a href="javascript:;"><i class="{{ $value->icono }}"></i> {{ $value->nombre }} <i class="fa fa-sort-desc"></i></a>
                    <ul <?php echo (Auth::user()->idtienda==0 && $cantmodulos==$i)? 'style="right: 10px;"':'' ?>>
                    <?php
                    $submodulos = DB::table('modulo')
                      ->join('usersrolesmodulo','usersrolesmodulo.idmodulo','modulo.id')
                      ->where('usersrolesmodulo.idusers',Auth::user()->id)
                      ->where('modulo.idmodulo',$value->id)
                      ->where('modulo.idestado',1)
                      ->select('modulo.*')
                      ->orderBy('modulo.orden','asc')
                      ->get();
                    ?>
                    @foreach($submodulos as $subvalue)
                          <?php $href = str_replace('{idtienda}', $tienda->id, $subvalue->vista); ?>       
                          <li><a href="javascript:;" onclick="pagina({route:'{{url($href)}}',result:'#cuerposistema'})"><i class="{{ $subvalue->icono }}"></i> {{ $subvalue->nombre }}</a></li>                  
                    @endforeach
                    </ul>
                </li>    
                <?php $i++; ?>              
                @endif
            @endforeach
          @endif
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled"><div id="aperturacaja"></div></a>
        </li>
      </ul>
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>
    <div class="profile-edit-page-header">
        <a href="{{ $tienda->idestadoprivacidad==1?url($tienda->link):'javascript:;' }}">
          @if($tienda->imagen!='')
          <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen) }}" 
               style="height: 40px;float: left;margin-top: 7px;margin-right: 7px;">
          @endif
          <h2 style="margin-top: 17px;">{{ $tienda->nombre }}</h2></a>
          <div id="aperturacaja"></div>
          <nav class="menu-sistema">
            <div>
              <i class="fa fa-bars"></i>
            </div>
            <ul>
              @if(Auth::user()->idtienda==0)
                <?php
                $modulos = DB::table('modulo')
                  ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
                  ->join('roles','roles.id','rolesmodulo.idroles') 
                  ->where('roles.idcategoria',$tienda->idcategoria)
                  ->where('roles.name','administrador')
                  ->where('modulo.idmodulo',7)
                  ->where('modulo.idestado',1)
                  ->select('modulo.*')
                  ->orderBy('modulo.orden','asc')
                  ->get();
                ?>
                <?php $i = 1  ; ?>
                <?php $cantmodulos = count($modulos); ?>
                @foreach($modulos as $value)
                   <li><a href="javascript:;"><i class="{{ $value->icono }}"></i> {{ $value->nombre }} <i class="fa fa-sort-desc"></i></a>
                        <ul <?php echo (Auth::user()->idtienda==0 && $cantmodulos==$i)? 'style="right: 10px;"':'' ?>>
                        <?php
                        $submodulos = DB::table('modulo')
                          ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
                          ->join('roles','roles.id','rolesmodulo.idroles')
                          ->where('roles.idcategoria',$tienda->idcategoria)
                          ->where('roles.name','administrador')
                          ->where('modulo.idmodulo',$value->id)
                          ->where('modulo.idestado',1)
                          ->select('modulo.*')
                          ->orderBy('modulo.orden','asc')
                          ->get();
                        ?>
                        @foreach($submodulos as $subvalue)
                        @if($subvalue->vista!='' && $subvalue->controlador!='')
                              <?php $href = str_replace('{idtienda}', $tienda->id, $subvalue->vista); ?>
                              <li><a href="javascript:;" onclick="pagina({route:'{{url($href)}}?view=tabla',result:'#cuerposistema'})"><i class="{{ $subvalue->icono }}"></i> {{ $subvalue->nombre }}</a></li> 
                        @endif
                        @endforeach
                        </ul>
                    </li>
                    <?php $i++; ?>
                @endforeach
              @else
                <?php
                $modulos = DB::table('modulo')
                  ->join('usersrolesmodulo','usersrolesmodulo.idmodulo','modulo.id')
                  ->where('usersrolesmodulo.idusers',Auth::user()->id)
                  ->where('modulo.idmodulo',7)
                  ->where('modulo.idestado',1)
                  ->select('modulo.*')
                  ->orderBy('modulo.orden','asc')
                  ->get();

                $prestamo_estadocreditogrupal = 'prestamosolicitudgrupal';
                if(configuracion($tienda->id,'prestamo_estadocreditogrupal')['valor']==1){
                    $prestamo_estadocreditogrupal = '';
                };
                $facturacion_estadofacturacion = 'Facturación';
                if(configuracion($tienda->id,'facturacion_estadofacturacion')['valor']==1){
                    $facturacion_estadofacturacion = '';
                };

                $prestamo_estadocreditoprendario = '';
                $prestamo_estadoahorro = '';
                $prestamo_estadoahorro_1 = '';
                $prestamo_estadoahorro_2 = '';
                $ecommerce = '';
                if($tienda->idcategoria==13){
                    $prestamo_estadocreditoprendario = 'Inventario';
                    $ecommerce = 'Ecommerce';
                    if(configuracion($tienda->id,'prestamo_estadocreditoprendario')['valor']==1){
                        $prestamo_estadocreditoprendario = '';
                        $ecommerce = '';
                    };
                    $prestamo_estadoahorro = 'Ahorros';
                    $prestamo_estadoahorro_1 = 'ahorroconfirmacion';
                    $prestamo_estadoahorro_2 = 'ahorrorecaudacion';
                    if(configuracion($tienda->id,'prestamo_ahorro_estadoahorro')['valor']==1){
                        $prestamo_estadoahorro = '';
                        $prestamo_estadoahorro_1 = '';
                        $prestamo_estadoahorro_2 = '';
                    };
                }
                ?>
                <?php $i = 1  ; ?>
                <?php $cantmodulos = count($modulos); ?>
                @foreach($modulos as $value)
                  @if($prestamo_estadocreditoprendario==$value->nombre or 
                      $prestamo_estadoahorro==$value->nombre or 
                      $facturacion_estadofacturacion==$value->nombre or 
                      $ecommerce==$value->nombre)   
                  @else 
                   <li><a href="javascript:;"><i class="{{ $value->icono }}"></i> {{ $value->nombre }} <i class="fa fa-sort-desc"></i></a>
                        <ul <?php echo (Auth::user()->idtienda==0 && $cantmodulos==$i)? 'style="right: 10px;"':'' ?>>
                        <?php
                        $submodulos = DB::table('modulo')
                          ->join('usersrolesmodulo','usersrolesmodulo.idmodulo','modulo.id')
                          ->where('usersrolesmodulo.idusers',Auth::user()->id)
                          ->where('modulo.idmodulo',$value->id)
                          ->where('modulo.idestado',1)
                          ->select('modulo.*')
                          ->orderBy('modulo.orden','asc')
                          ->get();
                        ?>
                        @foreach($submodulos as $subvalue)
                              <?php $href = str_replace('{idtienda}', $tienda->id, $subvalue->vista); ?>       
                              <li><a href="javascript:;" onclick="pagina({route:'{{url($href)}}',result:'#cuerposistema'})"><i class="{{ $subvalue->icono }}"></i> {{ $subvalue->nombre }}</a></li>                  
                        @endforeach
                        </ul>
                    </li>    
                    <?php $i++; ?>              
                    @endif
                @endforeach
              @endif
                  <li>
                    <a href="javascript:;" style="background-color: #31353d;padding-top: 14px;padding-bottom: 12px;">
                        <?php $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/sistema/'.Auth::user()->imagen; ?>
                        @if(file_exists($rutaimagen) AND Auth::user()->imagen!='')
                            <img class="thumb" src="{{ url('/public/backoffice/tienda/'.$tienda->id.'/sistema/'.Auth::user()->imagen) }}" class="imglogousuario" style="width: 30px;height: 30px;border-radius: 15px;">
                        @else
                            <img class="thumb" src="{{ url('public/backoffice/sistema/sin_imagen_redondo.png') }}" class="imglogousuario" style="width: 30px;height: 30px;border-radius: 15px;">
                        @endif
                     {{ Auth::user()->nombre }} <i class="fa fa-sort-desc" style="float: right;margin-top: 5px;"></i></a>
                    <ul style="right: 10px;background: #31353d;">
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio') }}"><i class="fa fa-home"></i> Inicio</a></li>
                      @if($tienda->idestadoprivacidad==1)
                      <li><a href="{{ url($tienda->link) }}"><i class="fa fa-store"></i> Tienda Virtual</a></li>
                      @endif
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/1/edit?view=editperfil') }}"><i class="fa fa-edit"></i> Editar Perfil</a></li>
                      <!--li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/1/edit?view=editmetodopago') }}"><i class="fa fa-money-check-alt"></i> Método de Pago</a></li-->
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/1/edit?view=editcambiarclave') }}"><i class="fa fa-unlock-alt"></i> Cambiar Contraseña</a></li>
                       <!--li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/create') }}"><i class="fa fa-clock-o"></i>Horario Ingreso y Salida</a></li-->
                      <li><a href="javascript:;" onclick="document.getElementById('logout-form-sistema').submit()"><i class="fa fa-power-off"></i> Cerrar Sesión</a></li>
                      <form method="POST" id="logout-form-sistema" action="{{ route('logout') }}">
                        @csrf 
                        <input type="hidden" value="{{ $tienda->id }}" name="logoutidtienda">
                        <input type="hidden" value="{{ Auth::user()->idtipousuario}}" name="logoutidtipousuario">
                        <input type="hidden" value="{{ url($tienda->link) }}/login" name="logoutlink">
                      </form>
                    </ul>
                  </li>
            </ul>
          </nav> 
    </div>
    <div class="mx-subcuerpo">
        <div class="profile-edit-container">
            <div class="custom-form" id="cuerposistema">
            </div>
        </div> 
    </div>
      
        <script src="{{ url('public/nuevosistema/librerias/jquery/3.6.3/jquery.min.js') }}"></script>
        <!--script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.min.js"></script-->
        <!--script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script-->

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
      
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ"></script>
        <script src="{{ url('public/layouts/js/map_infobox.js') }}"></script>
        <script src="{{ url('public/layouts/js/markerclusterer.js') }}"></script>  
        
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/scroller/2.0.3/js/dataTables.scroller.min.js"></script>
        <script src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
        <script src="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.js"></script>
        <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
        <script src="https://cdn.datatables.net/keytable/2.6.4/js/dataTables.keyTable.min.js"></script>
      
        <script src="{{ url('public/nuevosistema/librerias/app/app.js') }}"></script>
        <script src="{{ url('public/nuevosistema/librerias/app/scripts.js') }}"></script>


    <script>
    pagina({route:'{{url('backoffice/'.$tienda->id.'/inicio/create?view=inicio')}}',result:'#cuerposistema'});
    </script>
</body>
</html>