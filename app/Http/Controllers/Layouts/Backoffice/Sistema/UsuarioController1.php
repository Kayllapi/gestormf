<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Auth;
use Hash;
use DB;
use Image;
use PDF;

class UsuarioController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/usuario/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }

    public function create(Request $request, $idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            $tipopersonas = DB::table('tipopersona')->get();
            $tipoinformacion = DB::table('f_tipoinformacion')->get();
            $fuenteingreso = DB::table('f_fuenteingreso')->get();
            $genero = DB::table('f_genero')->get();
            $estadocivil = DB::table('f_estadocivil')->get();
            $nivelestudio = DB::table('f_nivelestudio')->get();
            $ocupacion = DB::table('f_ocupacion')->get();
            $condicionviviendalocal = DB::table('f_condicionviviendalocal')->get();
            //$tiporeferencia = DB::table('f_tiporeferencia')->get();
            $formactividadeconomica = DB::table('f_formaactividadeconomica')->get();
            $contratolaboral = DB::table('f_contratolaboral')->get();
            return view(sistema_view().'/usuario/create',[
                'tienda' => $tienda,
                'tipoinformacion'   => $tipoinformacion,
                'fuenteingreso'     => $fuenteingreso,
                'genero'            => $genero,
                'estadocivil'       => $estadocivil,
                'nivelestudio'      => $nivelestudio,
                'ocupacion'         => $ocupacion,
                'condicionviviendalocal' => $condicionviviendalocal,
                'formactividadeconomica' => $formactividadeconomica,
                'contratolaboral'   => $contratolaboral,
                'tipopersonas'      => $tipopersonas,
            ]);
        }
        else if($request->view == 'autorizacion'){
            return view(sistema_view().'/usuario/autorizacion',[
                'tienda' => $tienda,
            ]);
        }
    }

    public function store(Request $request, $idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->input('view') == 'registrar') {
            
            /* =================================================  VALIDAR CAMPOS */
            $rules = [];
            $rules['idfuenteingreso']   = 'required';
            $rules['idtipoinformacion'] = 'required';
            $rules['idtipopersona'] = 'required';
          
            $identificacion   = 0;
            $nombre           = '';
            $apellidopaterno  = '';
            $apellidomaterno  = '';
            $razonsocial      = '';
            $nombrecompleto   = '';
            if ($request->idtipopersona == 1) {
                $rules = array_merge($rules,[
                    'dni' => 'required|numeric',
                ]);
                
                if($request->dni!=0){
                    $rules = array_merge($rules,[
                        'dni' => 'digits:8'
                    ]);
                    if ($request->dni != ''){
                        $rules = array_merge($rules,[
                            'nombre' => 'required',
                            'apellidopaterno' => 'required',
                            'apellidomaterno' => 'required',
                        ]);
                    }
                }else{
                    if ($request->dni != '' && $request->dni == 0) {
                        $rules = array_merge($rules,[
                            'nombre' => 'required',
                        ]);
                    }elseif ($request->dni != ''){
                        $rules = array_merge($rules,[
                            'nombre' => 'required',
                            'apellidopaterno' => 'required',
                            'apellidomaterno' => 'required',
                        ]);
                    }
                }
                    
                $identificacion   = $request->dni;
                $nombre           = $request->input('nombre');
                $apellidopaterno  = $request->input('apellidopaterno')!=null ? $request->input('apellidopaterno') : '';
                $apellidomaterno  = $request->input('apellidomaterno')!=null ? $request->input('apellidomaterno') : '';
                $nombrecompleto   = ($apellidopaterno!=''?($apellidopaterno.' '.$apellidomaterno.', '):'').$nombre;
            }
            elseif ($request->idtipopersona == 2) {
                $rules = array_merge($rules,[
                    'ruc' => 'required|numeric',
                ]);
                
                if($request->ruc!=0){
                    $rules = array_merge($rules,[
                        'ruc' => 'digits:11'
                    ]);
                    if ($request->ruc != ''){
                        $rules = array_merge($rules,[
                            'razonsocial'     => 'required',
                        ]);
                    }
                }else{
                    if ($request->ruc !='' && $request->ruc == 0) {
                        $rules = array_merge($rules,[
                            'razonsocial' => 'required',
                        ]);
                    }elseif ($request->ruc != ''){
                        $rules = array_merge($rules,[
                            'razonsocial'     => 'required',
                        ]);
                    }
                }

                $identificacion   = $request->input('ruc');
                $nombre           = $request->input('nombrecomercial')!=null ? $request->input('nombrecomercial') : '';
                $razonsocial      = $request->input('razonsocial')!=null ? $request->input('razonsocial') : '';
                $nombrecompleto   = $razonsocial;
            }
            elseif ($request->idtipopersona == 3) {
                $rules = array_merge($rules,[
                    'carnetextranjeria' => 'required',
                    'nombre_carnetextranjeria' => 'required',
                ]);
                $identificacion   = $request->input('carnetextranjeria');
                $nombre           = $request->input('nombre_carnetextranjeria');
                $apellidopaterno  = $request->input('apellidopaterno_carnetextranjeria')!=null ? $request->input('apellidopaterno_carnetextranjeria') : '';
                $apellidomaterno  = $request->input('apellidomaterno_carnetextranjeria')!=null ? $request->input('apellidomaterno_carnetextranjeria') : '';
                $nombrecompleto   = $apellidopaterno.' '.$apellidomaterno.', '.$nombre;
            }
            $messages = [
                'idtipoinformacion.required' => 'El "Tipo de Información" es Obligatorio.',
                'idfuenteingreso.required' => 'El "Fuente de Ingreso" es Obligatorio.',
                'dni.required' => 'El "DNI" es Obligatorio.',
                'dni.required' => 'El "DNI" es Obligatorio.',
                'dni.numeric'   => 'El "DNI" debe ser Númerico.',
                'dni.digits'   => 'El "DNI" debe ser de 8 Digitos.',
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'apellidopaterno.required' => 'El "Apellido Paterno" es Obligatorio.',
                'apellidomaterno.required' => 'El "Apellido Materno" es Obligatorio.',
                'ruc.required' => 'El "RUC" es Obligatorio.',
                'ruc.numeric'   => 'El "RUC" debe ser Númerico.',
                'ruc.digits'   => 'El "RUC" debe ser de 11 Digitos.',
                //'nombrecomercial.required' => 'El "Nombre Comercial" es Obligatorio.',
                'razonsocial.required' => 'El "Razón Social" es Obligatorio.',
                'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                'numerotelefono.digits' => 'El "Número de Teléfono" debe ser de 9 números.',
                'carnetextranjeria.required' => 'El "Carnet Extranjería" es Obligatorio.',
                'nombre_carnetextranjeria.required' => 'El "Nombre" es Obligatorio.',
                'domicilio_mapa_latitud.required' => 'La "Ubicación" es Obligatorio.<br>(Mover el marcador del mapa para seleccionar una ubicación)',
                'domicilio_mapa_longitud.required' => '',
            ];

            //INICIO NUEVAS VALIDACIONDES
            $rules['direccion']   = 'required';
            $rules['idubigeo']   = 'required';
            //$rules['correo_electronico'] = 'required';

            $rules['referencia_direccion']  = 'required';
            //$rules['suministro_electrocentro']  = 'required';
            $rules['idcondicionviviendalocal']  = 'required';
            $rules['idtipodocumento'] = 'required';
            $rules['fechanacimientocreacion'] = 'required';
            //$rules['correo_electronico'] = 'required';
          

                $rules['celular-clientetexto0'] = 'required';
                $messages['celular-clientetexto0.required'] = 'El campo "Telf./Celular" es obligatorio.';

                //$rules['celular-parejatexto0'] = 'required';
                //$messages['celular-parejatexto0.required'] = 'El campo "Telf./Celular de PAREJA" es obligatorio.';

              
                $rules['celular0'] = 'required';
                $messages['celular0.required'] = 'El campo "Telf./Celular" es obligatorio.';
              
                $rules['vinculo0'] = 'required';
                $messages['vinculo0.required'] = 'El campo "Nombres y Apellidos" es obligatorio.';
              
                $rules['referencia0'] = 'required';
                $messages['referencia0.required'] = 'El campo "	Vinculo Familiar/Personas/Otros" es obligatorio.';
          
            // INDEPENDIENTE
            if( $request->idtipoinformacion == 1 && $request->idfuenteingreso == 1){
                if( $request->idtipodocumento != 2 ){
                    $rules['idgenero'] = 'required';
                    $rules['idestadocivil'] = 'required';
                    $rules['idnivelestudio'] = 'required';
                    //$rules['profesion'] = 'required';
                }
                if( $request->idtipodocumento == 2 ){
                    $rules['documento_representantelegal'] = 'required';
                    $rules['nombrecompelto_representantelegal'] = 'required';
                }
                // $rules['direccion'] = 'required';
                // $rules['idubigeo'] = 'required';
                $rules['idforma_ac_economica'] = 'required';
                $rules['idgiro_ac_economica'] = 'required';
                $rules['descripcion_ac_economica']      = 'required';
                // OTROS
                if($request->casanegocio != 'on'){
                    $rules['direccion_ac_economica']        = 'required';
                    $rules['idubigeo_ac_economica']         = 'required';
                    $rules['referencia_ac_economica']       = 'required';
                    $rules['idlocalnegocio_ac_economica']   = 'required';
                }
                /*if( $request->idforma_ac_economica == 1 ){
                    $rules['ruc_ac_economica']              = 'required';
                    $rules['razonsocial_ac_economica']      = 'required';
                }*/
                
            }
            else if( $request->idtipoinformacion == 1 && $request->idfuenteingreso == 2 ){
                if( $request->idtipodocumento != 2 ){
                    $rules['idgenero'] = 'required';
                    $rules['idestadocivil'] = 'required';
                    $rules['idnivelestudio'] = 'required';
                    //$rules['profesion'] = 'required';
                }
                if( $request->idtipodocumento == 2 ){
                    $rules['documento_representantelegal'] = 'required';
                    $rules['nombrecompelto_representantelegal'] = 'required';
                }
                $rules['direccion'] = 'required';
                $rules['idubigeo'] = 'required';
                //$rules['ruc_laboral_cliente']               = 'required';
                //$rules['razonsocial_laboral_cliente']       = 'required';
                //$rules['fechainicio_laboral_cliente']       = 'required';
                //$rules['antiguedad_laboral_cliente']        = 'required';
                //$rules['cargo_laboral_cliente']             = 'required';
                //$rules['area_laboral_cliente']              = 'required';
                //$rules['idtipocontrato_laboral_cliente']    = 'required';
            }
            else if( $request->idtipoinformacion == 2 && $request->idfuenteingreso == 1 ){ 
                if( $request->idtipodocumento != 2 ){
                    $rules['idgenero'] = 'required';
                    $rules['idestadocivil'] = 'required';
                    $rules['idnivelestudio'] = 'required';
                    //$rules['profesion'] = 'required';
                }
                if( $request->idtipodocumento == 2 ){
                    $rules['razonsocial'] = 'required';
                    $rules['documento_representantelegal'] = 'required';
                    $rules['nombrecompelto_representantelegal'] = 'required';
                }

                $rules['idforma_ac_economica'] = 'required';
                $rules['idgiro_ac_economica'] = 'required';
                $rules['descripcion_ac_economica']      = 'required';
                
                // OTROS
                if($request->casanegocio != 'on'){
                    $rules['direccion_ac_economica']        = 'required';
                    $rules['idubigeo_ac_economica']         = 'required';
                    $rules['referencia_ac_economica']       = 'required';
                    $rules['idlocalnegocio_ac_economica']   = 'required';
                }
                
                /*if( $request->idforma_ac_economica == 1 ){
                    $rules['ruc_ac_economica']              = 'required';
                    $rules['razonsocial_ac_economica']      = 'required';
                }*/

                /*$rules['ruc_laboral_cliente']               = 'required';
                $rules['razonsocial_laboral_cliente']       = 'required';
                $rules['fechainicio_laboral_cliente']       = 'required';
                $rules['antiguedad_laboral_cliente']        = 'required';
                $rules['cargo_laboral_cliente']             = 'required';
                $rules['area_laboral_cliente']              = 'required';
                $rules['idtipocontrato_laboral_cliente']    = 'required';*/
            }
            else if( $request->idtipoinformacion == 2 && $request->idfuenteingreso == 2 ){
                if( $request->idtipodocumento != 2 ){
                    $rules['idgenero'] = 'required';
                    $rules['idestadocivil'] = 'required';
                    $rules['idnivelestudio'] = 'required';
                    //$rules['profesion'] = 'required';
                }
                /*$rules['ruc_laboral_cliente']               = 'required';
                $rules['razonsocial_laboral_cliente']       = 'required';
                $rules['fechainicio_laboral_cliente']       = 'required';
                $rules['antiguedad_laboral_cliente']        = 'required';
                $rules['cargo_laboral_cliente']             = 'required';
                $rules['area_laboral_cliente']              = 'required';
                $rules['idtipocontrato_laboral_cliente']    = 'required';*/

            }

            if( $request->idestadocivil == 2 || $request->idestadocivil == 4){
                
                $telefono_pareja = json_decode($request->telefono_pareja);
                if(empty($telefono_pareja)){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Debe agregar al menos un nro de telefono de la pareja.'
                    ]);
                }

                $rules['dni_pareja']                        =  'required';
                $rules['nombres_pareja']                    =  'required';
                $rules['ap_paterno_pareja']                 =  'required';
                $rules['ap_materno_pareja']                 =  'required';
                $rules['idocupacion_pareja']                =  'required';
                //$rules['profesion_pareja']                  =  'required';
                //$rules['idnivelestudio_pareja']             =  'required';

                //$rules['ruc_laboral_pareja']                =  'required';
                //$rules['fechainicio_laboral_pareja']        =  'required';
                //$rules['area_laboral_pareja']               =  'required';
              
                if($request->idocupacion_pareja==1){
                    $rules['descripcion_negocio_pareja']        =  'required';
                    $rules['direccion_negocio_pareja']          =  'required';
                    $rules['idempresa_negocio_pareja']          =  'required';
                    $rules['idforma_negocio_pareja']            =  'required';
                    $rules['idgiro_negocio_pareja']             =  'required';
                    $rules['idlocalnegocio_negocio_pareja']     =  'required';
                    $rules['idubigeo_negocio_pareja']           =  'required';
                }elseif($request->idocupacion_pareja==2){
                    $rules['razonsocial_laboral_pareja']        =  'required';
                    $rules['antiguedad_laboral_pareja']         =  'required';
                    $rules['cargo_laboral_pareja']              =  'required';
                    $rules['idtipocontrato_laboral_pareja']     =  'required';
                }
              
                //$rules['referencia_negocio_pareja']         =  'required';
                /*if($request->idforma_negocio_pareja == 1){
                    $rules['ruc_negocio_pareja']         =  'required';
                    $rules['razonsocial_negocio_pareja'] =  'required';
                    $messages['ruc_negocio_pareja.required']            = 'El campo "RUC" es obligatorio. ';
                    $messages['razonsocial_negocio_pareja.required']    = 'El campo "Razón Social" es obligatorio. ';
                }*/
                

                // $messages['referencia_negocio_pareja.required']     = 'El campo "Referencia de Ubicación" es obligatorio. ';
                
            }


            $messages['idtipopersona.required'] = 'El campo "Tipo de Persona" es obligatorio.';
            $messages['idtipodocumento.required'] = 'El campo "Tipo de Documento" es obligatorio.';
            $messages['idgenero.required'] = 'El campo "Genero" es obligatorio.';
            $messages['fechanacimientocreacion.required'] = 'El campo "Fecha Nac./Creación" es obligatorio.';
            $messages['idestadocivil.required'] = 'El campo "Estado Civil" es obligatorio.';
            $messages['idnivelestudio.required'] = 'El campo "Nivel de Estudios" es obligatorio.';
            //$messages['profesion.required'] = 'El campo "Profesión" es obligatorio.';
            
            $messages['documento_representantelegal.required'] = 'El campo "Nro Documento Representante Legal" es obligatorio.';
            $messages['nombrecompelto_representantelegal.required'] = 'El campo "Nombre Completo Representante Legal" es obligatorio.';
            
            $messages['idubigeo.required'] = 'El campo "(Ubigeo) Distrito – Provincia – Departamento" es obligatorio.';
            $messages['idforma_ac_economica.required'] = 'El campo "Forma de Activ. Econom" es obligatorio.';
            $messages['idgiro_ac_economica.required'] = 'El campo "Giro Económico" es obligatorio.';
            $messages['descripcion_ac_economica.required'] = 'El campo "Descripción" es obligatorio.';
            $messages['direccion_ac_economica.required'] = 'El campo "Direccion" es obligatorio.';
            $messages['idubigeo_ac_economica.required'] = 'El campo "Ubicación (Ubigeo)" es obligatorio.';
            $messages['referencia_ac_economica.required'] = 'El campo "Referencia de Ubicación" es obligatorio.';
            $messages['idlocalnegocio_ac_economica.required'] = 'El campo "Local Negocio" es obligatorio.';
            //$messages['ruc_ac_economica.required'] = 'El campo "Empresa de Transporte" es obligatorio.';
            //$messages['razonsocial_ac_economica.required'] = 'El campo "Empresa de Transporte" es obligatorio.';
            /*$messages['ruc_laboral_cliente.required'] = 'El campo "RUC" es obligatorio.';
            $messages['razonsocial_laboral_cliente.required'] = 'El campo "Razón Social" es obligatorio.';
            $messages['fechainicio_laboral_cliente.required'] = 'El campo "Fecha Incio" es obligatorio.';
            $messages['antiguedad_laboral_cliente.required'] = 'El campo "Antiguedad (en años)" es obligatorio.';
            $messages['cargo_laboral_cliente.required'] = 'El campo "Cargo" es obligatorio.';
            $messages['area_laboral_cliente.required'] = 'El campo "Área" es obligatorio.';
            $messages['idtipocontrato_laboral_cliente.required'] = 'El campo "Contrato Laboral" es obligatorio.';*/
            $messages['direccion.required']  = 'El campo "Direccion" es obligatorio.';
            $messages['idfuenteingreso.required']  = 'El campo "Fuente de Ingreso" es obligatorio.';
            $messages['idtipoinformacion.required']     = 'El campo "Tipo de Información" es obligatorio.';
            // $messages['profesion.required']             = 'El campo "Profesión" es obligatorio.';
            $messages['correo_electronico.required']    = 'El campo "Email" es obligatorio.';
            $messages['referencia_direccion.required']      = 'El campo "Referencia Ubicación" es obligatorio.';
            //$messages['suministro_electrocentro.required']  = 'El campo "Suministro Elect(Caso no exista N° Domicilio)" es obligatorio.';
            $messages['idcondicionviviendalocal.required']  = 'El campo "Condición de Vivienda" es obligatorio.';
            $messages['dni_pareja.required']                    = 'El campo "DNI" es obligatorio. ';
            $messages['nombres_pareja.required']                = 'El campo "Nombres" es obligatorio. ';
            $messages['ap_paterno_pareja.required']             = 'El campo "Apellido Paterno" es obligatorio. ';
            $messages['ap_materno_pareja.required']             = 'El campo "Apellido Materno" es obligatorio. ';
            $messages['idocupacion_pareja.required']            = 'El campo "Ocupación" es obligatorio. ';
            //$messages['profesion_pareja.required']              = 'El campo "Profesión" es obligatorio. ';
            //$messages['idnivelestudio_pareja.required']         = 'El campo "Nivel de Estudios" es obligatorio. ';
            //$messages['ruc_laboral_pareja.required']            = 'El campo "RUC" es obligatorio. ';
            $messages['razonsocial_laboral_pareja.required']    = 'El campo "Razón Social" es obligatorio. ';
            //$messages['fechainicio_laboral_pareja.required']    = 'El campo "Fecha Incio" es obligatorio. ';
            $messages['antiguedad_laboral_pareja.required']     = 'El campo "Antiguedad (en años)" es obligatorio. ';
            $messages['cargo_laboral_pareja.required']          = 'El campo "Cargo" es obligatorio. ';
            //$messages['area_laboral_pareja.required']           = 'El campo "Área" es obligatorio. ';
            $messages['idtipocontrato_laboral_pareja.required'] = 'El campo "Contrato Laboral" es obligatorio. ';

                    $messages['descripcion_negocio_pareja.required']    = 'El campo "Descripción" es obligatorio. ';
                    $messages['direccion_negocio_pareja.required']      = 'El campo "Direccion" es obligatorio. ';
                    $messages['idempresa_negocio_pareja.required']      = 'El campo "sssss" es obligatorio. ';
                    $messages['idforma_negocio_pareja.required']        = 'El campo "Forma de Activ. Econom" es obligatorio. ';
                    $messages['idgiro_negocio_pareja.required']         = 'El campo "Giro Económico" es obligatorio. ';
                    $messages['idubigeo_negocio_pareja.required']       = 'El campo "Ubicación (Ubigeo) " es obligatorio. ';
                    $messages['idlocalnegocio_negocio_pareja.required'] = 'El campo "Local Negocio" es obligatorio. ';

            $this->validate($request,$rules,$messages);
            // dd("------");
            // FIN NUEVAS VALIDACIONES
          
            $usuario = DB::table('users')
                ->where('identificacion',$identificacion)
                ->where('idtienda',$idtienda)
                ->where('idestado',1)
                ->first();

            if($usuario!='' and $identificacion!=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "RUC/DNI" ya existe, Ingrese Otro por favor.'
                ]);
            }
            $db_idgenero        = '';
            $db_idestadocivil   = '';
            $db_idnivelestudio  = '';
            $db_idubigeo        = '';

            $db_idtipodocumento = '';
            $db_idtipoinformacion = '';
            // $db_idrepresentantelegal = '';
            $db_idpareja = '';
            $db_idocupacion_pareja = '';
            $db_idnivelestudio_pareja = '';
            $db_idcondicionviviendalocal = '';
            $db_idfuenteingreso = '';
            $db_idforma_ac_economica = '';
            $db_idgiro_ac_economica = '';
            // $db_idempresa_ac_economica = '';
            $db_idubigeo_ac_economica = '';
            $db_idlocalnegocio_ac_economica = '';
            // $db_idempresa_laboral_cliente = '';
            $db_idtipocontrato_laboral_cliente = '';
            // $db_idempresa_laboral_pareja = '';
            $db_idtipocontrato_laboral_pareja = '';
            $db_idforma_negocio_pareja = '';
            $db_idgiro_negocio_pareja = '';
            // $db_idempresa_negocio_pareja = '';
            $db_idubigeo_negocio_pareja = '';
            $db_idlocalnegocio_negocio_pareja = '';

            if( $request->idubigeo != null ){
                $db_ubigeo = DB::table('ubigeo')->whereId($request->idubigeo)->first();
                $db_idubigeo = $db_ubigeo ? $db_ubigeo->nombre : '';
            }
            if( $request->idgenero != null ){
                $db_genero = DB::table('f_genero')->whereId($request->idgenero)->first();
                $db_idgenero = $db_genero ? $db_genero->nombre : '';
            }
            if( $request->idestadocivil != null ){
                $db_estadocivil = DB::table('f_estadocivil')->whereId($request->idestadocivil)->first();
                $db_idestadocivil = $db_estadocivil ? $db_estadocivil->nombre : '';
            }
            if( $request->idnivelestudio != null ){
                $db_nivelestudio = DB::table('f_nivelestudio')->whereId($request->idnivelestudio)->first();
                $db_idnivelestudio = $db_nivelestudio ? $db_nivelestudio->nombre : '';
            }

            if( $request->idtipodocumento != null ){
                $db_tipodocumento = DB::table('s_tipodocumento')->whereId($request->idtipodocumento)->first();
                $db_idtipodocumento = $db_tipodocumento ? $db_tipodocumento->nombre : '';
            }
            if( $request->idtipoinformacion != null ){
                $db_tipoinformacion = DB::table('f_tipoinformacion')->whereId($request->idtipoinformacion)->first();
                $db_idtipoinformacion = $db_tipoinformacion ? $db_tipoinformacion->nombre : '';
            }
            if( $request->idocupacion_pareja != null ){
                $db_ocupacion = DB::table('f_ocupacion')->whereId($request->idocupacion_pareja)->first();
                $db_idocupacion_pareja = $db_ocupacion ? $db_ocupacion->nombre : '';
            }
            if( $request->idnivelestudio_pareja != null ){
                $db_nivelestudiopareja = DB::table('f_nivelestudio')->whereId($request->idnivelestudio_pareja)->first();
                $db_idnivelestudio_pareja = $db_nivelestudiopareja ? $db_nivelestudiopareja->nombre : '';
            }
            if( $request->idcondicionviviendalocal != null ){
                $db_condicionviviendalocal = DB::table('f_condicionviviendalocal')->whereId($request->idcondicionviviendalocal)->first();
                $db_idcondicionviviendalocal = $db_condicionviviendalocal ? $db_condicionviviendalocal->nombre : '';
            }
            if( $request->idfuenteingreso != null ){
                $db_fuenteingreso = DB::table('f_fuenteingreso')->whereId($request->idfuenteingreso)->first();
                $db_idfuenteingreso = $db_fuenteingreso ? $db_fuenteingreso->nombre : '';
            }

            if( $request->idforma_ac_economica != null ){
                $db_formactividadeconomica = DB::table('f_formaactividadeconomica')->whereId($request->idforma_ac_economica)->first();
                $db_idforma_ac_economica = $db_formactividadeconomica ? $db_formactividadeconomica->nombre : '';
            }
            if( $request->idgiro_ac_economica != null ){
                switch ($request->idgiro_ac_economica) {
                    case 1:
                        $db_idgiro_ac_economica = 'COMERCIO';
                        break;
                    case 2:
                        $db_idgiro_ac_economica = 'SERVICIO';
                        break;
                    case 3:
                        $db_idgiro_ac_economica = 'PRODUCCION';
                        break;
                    
                    default:
                        $db_idgiro_ac_economica = '';
                        break;
                }
                
            }
            if( $request->idubigeo_ac_economica != null ){
                $db_ubigeo_ac_economica = DB::table('ubigeo')->whereId($request->idubigeo_ac_economica)->first();
                $db_idubigeo_ac_economica = $db_ubigeo_ac_economica ? $db_ubigeo_ac_economica->nombre : '';
            }
            if( $request->idlocalnegocio_ac_economica != null ){
                $db_condicionviviendalocal = DB::table('f_condicionviviendalocal')->whereId($request->idlocalnegocio_ac_economica)->first();
                $db_idlocalnegocio_ac_economica = $db_condicionviviendalocal ? $db_condicionviviendalocal->nombre : '';
            }
            if( $request->idtipocontrato_laboral_cliente != null ){
                $db_contratolaboral = DB::table('f_contratolaboral')->whereId($request->idtipocontrato_laboral_cliente)->first();
                $db_idtipocontrato_laboral_cliente = $db_contratolaboral ? $db_contratolaboral->nombre : '';
            }
            if( $request->idtipocontrato_laboral_pareja != null ){
                $db_contratolaboral_pareja = DB::table('f_contratolaboral')->whereId($request->idtipocontrato_laboral_pareja)->first();
                $db_idtipocontrato_laboral_pareja = $db_contratolaboral_pareja ? $db_contratolaboral_pareja->nombre : '';
            }
            if( $request->idforma_negocio_pareja != null ){
                $db_formactividadeconomica = DB::table('f_formaactividadeconomica')->whereId($request->idforma_negocio_pareja)->first();
                $db_idforma_negocio_pareja = $db_formactividadeconomica ? $db_formactividadeconomica->nombre : '';
            }
            if( $request->idgiro_negocio_pareja != null ){
                switch ($request->idgiro_negocio_pareja) {
                    case 1:
                        $db_idgiro_negocio_pareja = 'COMERCIO';
                        break;
                    case 2:
                        $db_idgiro_negocio_pareja = 'SERVICIO';
                        break;
                    case 3:
                        $db_idgiro_negocio_pareja = 'PRODUCCION';
                        break;
                    
                    default:
                        $db_idgiro_negocio_pareja = '';
                        break;
                }
            }
            if( $request->idubigeo_negocio_pareja != null ){
                $db_ubigeo_ac_economica_pareja = DB::table('ubigeo')->whereId($request->idubigeo_negocio_pareja)->first();
                $db_idubigeo_negocio_pareja = $db_ubigeo_ac_economica_pareja ? $db_ubigeo_ac_economica_pareja->nombre : '';
            }
            if( $request->idlocalnegocio_negocio_pareja != null ){
                $db_condicionviviendalocal_pareja = DB::table('f_condicionviviendalocal')->whereId($request->idlocalnegocio_negocio_pareja)->first();
                $db_idlocalnegocio_negocio_pareja = $db_condicionviviendalocal_pareja ? $db_condicionviviendalocal_pareja->nombre : '';
            }

            /* ================================================= REGISTRAR */
            $user_id = DB::table('users')->insertGetId([
                'idtipopersona'       => $request->idtipopersona,
                'codigo'              => '',
                'nombre'              => $nombre,
                'apellidopaterno'     => $apellidopaterno,
                'apellidomaterno'     => $apellidomaterno,
                'razonsocial'         => $razonsocial,
                'nombrecompleto'      => $nombrecompleto,
                'identificacion'      => $identificacion,
                'email'               => '',
                'imagen'              => '',
                'numerotelefono'      => $request->numerotelefono!='' ? $request->numerotelefono : '',
                'direccion'           => $request->direccion!='' ? $request->direccion : '',
                'mapa_latitud'        => '',
                'mapa_longitud'       => '',
                'email_verified_at'   => Carbon::now(),
                'usuario'             => Carbon::now()->format("Ymdhisu"),
                'clave'               => '123',
                'password'            => Hash::make('123'),
                'idubigeo'            => $request->idubigeo!=null ? $request->idubigeo : 0,
                'iduserspadre'        => 0,
                'idtipousuario'       => 2, // 3=usuario sistema
                'idtienda'            => $idtienda,
                'idestadousuario'     => 2,
                'idestado'            => 1,
                'idgenero'            => $request->idgenero!=null?$request->idgenero:0,
                'fechanacimiento'     => $request->fechanacimientocreacion,
                'idestadocivil'       => $request->idestadocivil != null ? $request->idestadocivil : 0 ,
                'idnivelestudio'      => $request->idnivelestudio != null ? $request->idnivelestudio : 0 ,
                'referencia'          => $request->referencia,

                'db_idgenero'         => $db_idgenero,
                'db_idestadocivil'    => $db_idestadocivil,
                'db_idnivelestudio'   => $db_idnivelestudio,
                'db_idubigeo'         => $db_idubigeo,
            ]);
          
            //$usersnew = DB::table('users')->whereId($idtienda)->first();
          
            DB::table('users')->whereId($user_id)->update([
                'codigo'       => 'U'.str_pad($user_id, 8, "0", STR_PAD_LEFT),
            ]);

            $estado_casa_negocio = $request->input('casanegocio') ? 'SI' : 'NO'; 
            $informacin_cliente = [
                
                'idtipodocumento'       => $request->idtipodocumento != null ? $request->idtipodocumento : 0 ,
                'idtipoinformacion'     => $request->idtipoinformacion != null ? $request->idtipoinformacion : 0 ,
                'documento_representantelegal'          => $request->documento_representantelegal,
                'nombrecompelto_representantelegal'     => $request->nombrecompelto_representantelegal,

                'profesion'             => $request->profesion != null ? $request->profesion : '',
                'correo_electronico'    => $request->correo_electronico,
                'telefono_cliente'      => $request->telefono_cliente,

                'idpareja'                  => $request->idpareja != null ? $request->idpareja : 0 ,
                'dni_pareja'                => $request->dni_pareja,
                'nombres_pareja'            => $request->nombres_pareja,
                'ap_paterno_pareja'         => $request->ap_paterno_pareja,
                'ap_materno_pareja'         => $request->ap_materno_pareja,
                'nombrecompleto_pareja'     => $request->nombres_pareja!=''?$request->nombres_pareja.' '.$request->ap_paterno_pareja.' '.$request->ap_materno_pareja:'',
                
                'idocupacion_pareja'    => $request->idocupacion_pareja != null ? $request->idocupacion_pareja : 0,
                'profesion_pareja'      => $request->profesion_pareja != null ? $request->profesion_pareja : '',
                'idnivelestudio_pareja' => $request->idnivelestudio_pareja != null ? $request->idnivelestudio_pareja : 0,
                'telefono_pareja'       => $request->telefono_pareja,
                'referencia_direccion'  => $request->referencia_direccion,
                'suministro_electrocentro' => $request->suministro_electrocentro,
                'idcondicionviviendalocal' => $request->idcondicionviviendalocal != null ? $request->idcondicionviviendalocal : 0,
                'referencia_cliente'    => $request->referencia_cliente,

                'idfuenteingreso'           => $request->idfuenteingreso != null ? $request->idfuenteingreso : 0,
                'idforma_ac_economica'      => $request->idforma_ac_economica != null ? $request->idforma_ac_economica : 0,
                'idgiro_ac_economica'       => $request->idgiro_ac_economica != null ? $request->idgiro_ac_economica : 0,
                'descripcion_ac_economica'  => $request->descripcion_ac_economica,
                // 'idempresa_ac_economica'    => $request->idempresa_ac_economica != null ? $request->idempresa_ac_economica : 0,
                'ruc_ac_economica'          => $request->ruc_ac_economica != null ? $request->ruc_ac_economica : '',
                'razonsocial_ac_economica'  => $request->razonsocial_ac_economica != null ? $request->razonsocial_ac_economica : '',
                'casanegocio'               => $estado_casa_negocio,
                'direccion_ac_economica'    => $request->direccion_ac_economica,
                'idubigeo_ac_economica'     => $request->idubigeo_ac_economica != null ? $request->idubigeo_ac_economica : 0,
                'referencia_ac_economica'   => $request->referencia_ac_economica,
                'idlocalnegocio_ac_economica' => $request->idlocalnegocio_ac_economica != null ? $request->idlocalnegocio_ac_economica : 0,


                // 'idempresa_laboral_cliente'     => $request->idempresa_laboral_cliente != null ? $request->idempresa_laboral_cliente : 0,
                'ruc_laboral_cliente'           => $request->ruc_laboral_cliente != null ? $request->ruc_laboral_cliente : '',
                'razonsocial_laboral_cliente'   => $request->razonsocial_laboral_cliente != null ? $request->razonsocial_laboral_cliente : '',
                'fechainicio_laboral_cliente'   => $request->fechainicio_laboral_cliente != null ? $request->fechainicio_laboral_cliente : null,
                'antiguedad_laboral_cliente'    => $request->antiguedad_laboral_cliente != null ? $request->antiguedad_laboral_cliente : '',
                'cargo_laboral_cliente'         => $request->cargo_laboral_cliente != null ? $request->cargo_laboral_cliente : '',
                'area_laboral_cliente'          => $request->area_laboral_cliente != null ? $request->area_laboral_cliente : '',
                'idtipocontrato_laboral_cliente' => $request->idtipocontrato_laboral_cliente != null ? $request->idtipocontrato_laboral_cliente : 0,

                // 'idempresa_laboral_pareja'     => $request->idempresa_laboral_pareja != null ? $request->idempresa_laboral_pareja : 0,
                'ruc_laboral_pareja'           => $request->ruc_laboral_pareja!= null ? $request->ruc_laboral_pareja : '',
                'razonsocial_laboral_pareja'   => $request->razonsocial_laboral_pareja,

                'fechainicio_laboral_pareja'   => $request->fechainicio_laboral_pareja != null ? $request->fechainicio_laboral_pareja : null,
                'antiguedad_laboral_pareja'    => $request->antiguedad_laboral_pareja,
                'cargo_laboral_pareja'         => $request->cargo_laboral_pareja,
                'area_laboral_pareja'          => $request->area_laboral_pareja != null ? $request->area_laboral_pareja : '',
                'idtipocontrato_laboral_pareja' => $request->idtipocontrato_laboral_pareja != null ? $request->idtipocontrato_laboral_pareja : 0,

                'idforma_negocio_pareja'      => $request->idforma_negocio_pareja != null ? $request->idforma_negocio_pareja : 0,
                'idgiro_negocio_pareja'       => $request->idgiro_negocio_pareja != null ? $request->idgiro_negocio_pareja : 0,
                'descripcion_negocio_pareja'  => $request->descripcion_negocio_pareja,
                'idempresa_negocio_pareja'    => $request->idempresa_negocio_pareja != null ? $request->idempresa_negocio_pareja : 0,
                'ruc_negocio_pareja'          => $request->ruc_negocio_pareja != null ? $request->ruc_negocio_pareja : '',
                'razonsocial_negocio_pareja'  => $request->razonsocial_negocio_pareja != null ? $request->razonsocial_negocio_pareja : '',
                'direccion_negocio_pareja'    => $request->direccion_negocio_pareja,
                'idubigeo_negocio_pareja'     => $request->idubigeo_negocio_pareja != null ? $request->idubigeo_negocio_pareja : 0,
                'referencia_negocio_pareja'   => $request->referencia_negocio_pareja,
                'idlocalnegocio_negocio_pareja' => $request->idlocalnegocio_negocio_pareja != null ? $request->idlocalnegocio_negocio_pareja : 0,

                'db_idtipodocumento'                => $db_idtipodocumento,
                'db_idtipoinformacion'              => $db_idtipoinformacion,
                // 'db_idrepresentantelegal'           => $db_idrepresentantelegal,
                // 'db_idpareja'                       => $db_idpareja,
                'db_idocupacion_pareja'             => $db_idocupacion_pareja,
                'db_idnivelestudio_pareja'          => $db_idnivelestudio_pareja,
                'db_idcondicionviviendalocal'       => $db_idcondicionviviendalocal,
                'db_idfuenteingreso'                => $db_idfuenteingreso,
                'db_idforma_ac_economica'           => $db_idforma_ac_economica,
                'db_idgiro_ac_economica'            => $db_idgiro_ac_economica,
                // 'db_idempresa_ac_economica'         => $db_idempresa_ac_economica,
                'db_idubigeo_ac_economica'          => $db_idubigeo_ac_economica,
                'db_idlocalnegocio_ac_economica'    => $db_idlocalnegocio_ac_economica,
                // 'db_idempresa_laboral_cliente'      => $db_idempresa_laboral_cliente,
                'db_idtipocontrato_laboral_cliente' => $db_idtipocontrato_laboral_cliente,
                // 'db_idempresa_laboral_pareja'       => $db_idempresa_laboral_pareja,
                'db_idtipocontrato_laboral_pareja'  => $db_idtipocontrato_laboral_pareja,
                'db_idforma_negocio_pareja'         => $db_idforma_negocio_pareja,
                'db_idgiro_negocio_pareja'          => $db_idgiro_negocio_pareja,
                // 'db_idempresa_negocio_pareja'       => $db_idempresa_negocio_pareja,
                'db_idubigeo_negocio_pareja'        => $db_idubigeo_negocio_pareja,
                'db_idlocalnegocio_negocio_pareja'  => $db_idlocalnegocio_negocio_pareja,

            ];
            $informacin_cliente['id_s_users'] = $user_id;
            DB::table('s_users_prestamo')->insert($informacin_cliente);

            json_usuario($idtienda);
          
            return response()->json([
                'resultado'           => 'CORRECTO',
                'mensaje'             => 'Se ha registrado correctamente.',
                'idusuario'           => $user_id,
                'idtipopersona'       => $request->idtipopersona,
                'nombre'              => $nombre,
                'apellidopaterno'     => $apellidopaterno,
                'apellidomaterno'     => $apellidomaterno,
                'razonsocial'         => $razonsocial,
                'nombrecompleto'      => $nombrecompleto,
                'numerotelefono'      => $request->numerotelefono,
                'direccion'           => $request->direccion,
                'idubigeo'            => $request->idubigeo,
            ]);

        } 
        else if($request->input('view') == 'autorizacion'){

            $rules['password_autorizacion']   = 'required';
            $messages['password_autorizacion.required'] = 'El campo "Contraseña" es obligatorio.';
            $this->validate($request,$rules,$messages);

            $agencia = DB::table('tienda')
                        ->where('tienda.password_agencia',$request->input('password_autorizacion'))
                        ->first();

            if( $agencia ){
                return response()->json([
                    'resultado'           => 'CORRECTO',
                    'mensaje'             => 'Contraseña Correcta.',
                ]);
            }else{
                return response()->json([
                    'resultado'           => 'ERROR',
                    'mensaje'             => 'Contraseña Incorrecta.',
                ]);
            }
            
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        //
    }

    public function edit(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $usuario = DB::table('users')->where('users.id', $id)
            ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
            ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
            ->leftJoin('role_user','role_user.user_id','users.id')
            ->leftJoin('roles','roles.id','role_user.role_id')
            ->select(
                'users.*',
                'roles.id as idroles',
                'roles.description as descriptionrole',
                'ubigeo.nombre as ubigeonombre',
                'ubigeonacimiento.nombre as ubigeonacimientonombre'
            )
            ->first();

        if($request->input('view') == 'editar') {
            $tipopersonas = DB::table('tipopersona')->get();
            
            $tipoinformacion = DB::table('f_tipoinformacion')->get();
            $fuenteingreso = DB::table('f_fuenteingreso')->get();
            $genero = DB::table('f_genero')->get();
            $estadocivil = DB::table('f_estadocivil')->get();
            $nivelestudio = DB::table('f_nivelestudio')->get();
            $ocupacion = DB::table('f_ocupacion')->get();
            $condicionviviendalocal = DB::table('f_condicionviviendalocal')->get();
            // $tiporeferencia = DB::table('f_tiporeferencia')->get();
            $formactividadeconomica = DB::table('f_formaactividadeconomica')->get();
            $contratolaboral = DB::table('f_contratolaboral')->get();

            $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$id)->first();
            return view(sistema_view().'/usuario/edit',[
                'users_prestamo'    => $users_prestamo,
                'tipoinformacion'   => $tipoinformacion,
                //'tipopersona'       => $tipopersona,
                'fuenteingreso'     => $fuenteingreso,
                'genero'            => $genero,
                'estadocivil'       => $estadocivil,
                'nivelestudio'      => $nivelestudio,
                'ocupacion'         => $ocupacion,
                'condicionviviendalocal' => $condicionviviendalocal,
                'formactividadeconomica' => $formactividadeconomica,
                'contratolaboral'   => $contratolaboral,
                'tienda'            => $tienda,
                'usuario'           => $usuario,
                'tipopersonas'      => $tipopersonas,
            ]);
          
        } 
        else if( $request->input('view') == 'ficha' ){
            
            return view(sistema_view().'/usuario/ficha',[
                'tienda'            => $tienda,
                'usuario'           => $usuario,
            ]); 
        }
        else if( $request->input('view') == 'fichapdf' ){
            
            $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$id)->first();
            $pdf = PDF::loadView(sistema_view().'/usuario/fichapdf',[
                'users_prestamo'    => $users_prestamo,
                'tienda'            => $tienda,
                'usuario'           => $usuario,
            ]); 
            $pdf->setPaper('A4');
            // $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('FICHA_CLIENTE.pdf');
            
        }
        elseif($request->input('view')=='eliminar') {
            $tipopersonas = DB::table('tipopersona')->get();
            return view(sistema_view().'/usuario/delete',[
                'tienda' => $tienda,
                'usuario' => $usuario,
                'tipopersonas' => $tipopersonas,
            ]);
        }  
    }

    public function update(Request $request, $idtienda, $idusuario)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if ($request->input('view') == 'editar') {
            
            /* =================================================  VALIDAR CAMPOS */
            $identificacion   = 0;
            $nombre           = '';
            $apellidopaterno  = '';
            $apellidomaterno  = '';
            $razonsocial      = '';
            $nombrecompleto   = '';
            if ($request->idtipopersona == 1) {
                $rules = [
                    'dni' => 'required|numeric',
                ];
                
                if($request->dni!=0){
                    $rules = array_merge($rules,[
                        'dni' => 'digits:8'
                    ]);
                    if ($request->dni != ''){
                        $rules = array_merge($rules,[
                            'nombre' => 'required',
                            'apellidopaterno' => 'required',
                            'apellidomaterno' => 'required',
                        ]);
                    }
                }else{
                    if ($request->dni != '' && $request->dni == 0) {
                        $rules = array_merge($rules,[
                            'nombre' => 'required',
                        ]);
                    }elseif ($request->dni != ''){
                        $rules = array_merge($rules,[
                            'nombre' => 'required',
                            'apellidopaterno' => 'required',
                            'apellidomaterno' => 'required',
                        ]);
                    }
                }
                    
                $identificacion   = $request->dni;
                $nombre           = $request->input('nombre');
                $apellidopaterno  = $request->input('apellidopaterno')!=null ? $request->input('apellidopaterno') : '';
                $apellidomaterno  = $request->input('apellidomaterno')!=null ? $request->input('apellidomaterno') : '';
                $nombrecompleto   = ($apellidopaterno!=''?($apellidopaterno.' '.$apellidomaterno.', '):'').$nombre;
            }
            elseif ($request->idtipopersona == 2) {
                $rules = [
                    'ruc' => 'required|numeric',
                ];
                
                if($request->ruc!=0){
                    $rules = array_merge($rules,[
                        'ruc' => 'digits:11'
                    ]);
                    if ($request->ruc != ''){
                        $rules = array_merge($rules,[
                            'razonsocial'     => 'required',
                            'idubigeo'        => 'required',
                            'direccion'       => 'required',
                        ]);
                    }
                }else{
                    if ($request->ruc !='' && $request->ruc == 0) {
                        $rules = array_merge($rules,[
                            'razonsocial' => 'required',
                        ]);
                    }elseif ($request->ruc != ''){
                        $rules = array_merge($rules,[
                            'razonsocial'     => 'required',
                            'idubigeo'        => 'required',
                            'direccion'       => 'required',
                        ]);
                    }
                }

                $identificacion   = $request->input('ruc');
                $nombre           = $request->input('nombrecomercial')!=null ? $request->input('nombrecomercial') : '';
                $razonsocial      = $request->input('razonsocial')!=null ? $request->input('razonsocial') : '';
                $nombrecompleto   = $razonsocial;
            }
            elseif ($request->idtipopersona == 3) {
                $rules = [
                    'carnetextranjeria' => 'required',
                    'nombre_carnetextranjeria' => 'required',
                ];
                $identificacion   = $request->input('carnetextranjeria');
                $nombre           = $request->input('nombre_carnetextranjeria');
                $apellidopaterno  = $request->input('apellidopaterno_carnetextranjeria')!=null ? $request->input('apellidopaterno_carnetextranjeria') : '';
                $apellidomaterno  = $request->input('apellidomaterno_carnetextranjeria')!=null ? $request->input('apellidomaterno_carnetextranjeria') : '';
                $nombrecompleto   = $apellidopaterno.' '.$apellidomaterno.', '.$nombre;
            }


            //INICIO NUEVAS VALIDACIONDES
            $rules['direccion']   = 'required';
            $rules['idubigeo']   = 'required';
            $rules['idfuenteingreso']   = 'required';
            $rules['idtipoinformacion'] = 'required';
            //$rules['correo_electronico'] = 'required';

            $rules['referencia_direccion']  = 'required';
            //$rules['suministro_electrocentro']  = 'required';
            $rules['idcondicionviviendalocal']  = 'required';
            $rules['idtipopersona'] = 'required';
            $rules['idtipodocumento'] = 'required';
            $rules['fechanacimientocreacion'] = 'required';
            //$rules['correo_electronico'] = 'required';

                $rules['celular-clientetexto0'] = 'required';
                $messages['celular-clientetexto0.required'] = 'El campo "Telf./Celular" es obligatorio.';

                //$rules['celular-parejatexto0'] = 'required';
                //$messages['celular-parejatexto0.required'] = 'El campo "Telf./Celular de PAREJA" es obligatorio.';

              
                $rules['celular0'] = 'required';
                $messages['celular0.required'] = 'El campo "Telf./Celular" es obligatorio.';
              
                $rules['vinculo0'] = 'required';
                $messages['vinculo0.required'] = 'El campo "Nombres y Apellidos" es obligatorio.';
              
                $rules['referencia0'] = 'required';
                $messages['referencia0.required'] = 'El campo "	Vinculo Familiar/Personas/Otros" es obligatorio.';
          
            // INDEPENDIENTE
            if( $request->idtipoinformacion == 1 && $request->idfuenteingreso == 1){
                if( $request->idtipodocumento != 2 ){
                    $rules['idgenero'] = 'required';
                    $rules['idestadocivil'] = 'required';
                    $rules['idnivelestudio'] = 'required';
                    //$rules['profesion'] = 'required';
                }
                if( $request->idtipodocumento == 2 ){
                    $rules['documento_representantelegal'] = 'required';
                    $rules['nombrecompelto_representantelegal'] = 'required';
                }
                // $rules['direccion'] = 'required';
                // $rules['idubigeo'] = 'required';
                $rules['idforma_ac_economica'] = 'required';
                $rules['idgiro_ac_economica'] = 'required';
                $rules['descripcion_ac_economica']      = 'required';
                // OTROS
                if($request->casanegocio != 'on'){
                    $rules['direccion_ac_economica']        = 'required';
                    $rules['idubigeo_ac_economica']         = 'required';
                    $rules['referencia_ac_economica']       = 'required';
                    $rules['idlocalnegocio_ac_economica']   = 'required';
                }
                /*if( $request->idforma_ac_economica == 1 ){
                    $rules['ruc_ac_economica']              = 'required';
                    $rules['razonsocial_ac_economica']      = 'required';
                }*/
                
            }
            else if( $request->idtipoinformacion == 1 && $request->idfuenteingreso == 2 ){
                if( $request->idtipodocumento != 2 ){
                    $rules['idgenero'] = 'required';
                    $rules['idestadocivil'] = 'required';
                    $rules['idnivelestudio'] = 'required';
                    //$rules['profesion'] = 'required';
                }
                if( $request->idtipodocumento == 2 ){
                    $rules['documento_representantelegal'] = 'required';
                    $rules['nombrecompelto_representantelegal'] = 'required';
                }
                $rules['direccion'] = 'required';
                $rules['idubigeo'] = 'required';
                /*$rules['ruc_laboral_cliente']               = 'required';
                $rules['razonsocial_laboral_cliente']       = 'required';
                $rules['fechainicio_laboral_cliente']       = 'required';
                $rules['antiguedad_laboral_cliente']        = 'required';
                $rules['cargo_laboral_cliente']             = 'required';
                $rules['area_laboral_cliente']              = 'required';
                $rules['idtipocontrato_laboral_cliente']    = 'required';*/
            }
            else if( $request->idtipoinformacion == 2 && $request->idfuenteingreso == 1 ){ 
                if( $request->idtipodocumento != 2 ){
                    $rules['idgenero'] = 'required';
                    $rules['idestadocivil'] = 'required';
                    $rules['idnivelestudio'] = 'required';
                    //$rules['profesion'] = 'required';
                }
                if( $request->idtipodocumento == 2 ){
                    $rules['razonsocial'] = 'required';
                    $rules['documento_representantelegal'] = 'required';
                    $rules['nombrecompelto_representantelegal'] = 'required';
                }

                $rules['idforma_ac_economica'] = 'required';
                $rules['idgiro_ac_economica'] = 'required';
                $rules['descripcion_ac_economica']      = 'required';
                
                // OTROS
                if($request->casanegocio != 'on'){
                    $rules['direccion_ac_economica']        = 'required';
                    $rules['idubigeo_ac_economica']         = 'required';
                    $rules['referencia_ac_economica']       = 'required';
                    $rules['idlocalnegocio_ac_economica']   = 'required';
                }
                
                /*if( $request->idforma_ac_economica == 1 ){
                    $rules['ruc_ac_economica']              = 'required';
                    $rules['razonsocial_ac_economica']      = 'required';
                }*/

                /*$rules['ruc_laboral_cliente']               = 'required';
                $rules['razonsocial_laboral_cliente']       = 'required';
                $rules['fechainicio_laboral_cliente']       = 'required';
                $rules['antiguedad_laboral_cliente']        = 'required';
                $rules['cargo_laboral_cliente']             = 'required';
                $rules['area_laboral_cliente']              = 'required';
                $rules['idtipocontrato_laboral_cliente']    = 'required';*/
            }
            else if( $request->idtipoinformacion == 2 && $request->idfuenteingreso == 2 ){
                if( $request->idtipodocumento != 2 ){
                    $rules['idgenero'] = 'required';
                    $rules['idestadocivil'] = 'required';
                    $rules['idnivelestudio'] = 'required';
                    //$rules['profesion'] = 'required';
                }
                /*$rules['ruc_laboral_cliente']               = 'required';
                $rules['razonsocial_laboral_cliente']       = 'required';
                $rules['fechainicio_laboral_cliente']       = 'required';
                $rules['antiguedad_laboral_cliente']        = 'required';
                $rules['cargo_laboral_cliente']             = 'required';
                $rules['area_laboral_cliente']              = 'required';
                $rules['idtipocontrato_laboral_cliente']    = 'required';*/

            }

            if( $request->idestadocivil == 2 || $request->idestadocivil == 4){
                
                $telefono_pareja = json_decode($request->telefono_pareja);
                if(empty($telefono_pareja)){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Debe agregar al menos un nro de telefono de la pareja.'
                    ]);
                }

                $rules['dni_pareja']                        =  'required';
                $rules['nombres_pareja']                    =  'required';
                $rules['ap_paterno_pareja']                 =  'required';
                $rules['ap_materno_pareja']                 =  'required';
                $rules['idocupacion_pareja']                =  'required';

                if($request->idocupacion_pareja==1){
                    $rules['descripcion_negocio_pareja']        =  'required';
                    $rules['direccion_negocio_pareja']          =  'required';
                    $rules['idempresa_negocio_pareja']          =  'required';
                    $rules['idforma_negocio_pareja']            =  'required';
                    $rules['idgiro_negocio_pareja']             =  'required';
                    $rules['idlocalnegocio_negocio_pareja']     =  'required';
                    $rules['idubigeo_negocio_pareja']           =  'required';
                }elseif($request->idocupacion_pareja==2){
                    $rules['razonsocial_laboral_pareja']        =  'required';
                    $rules['antiguedad_laboral_pareja']         =  'required';
                    $rules['cargo_laboral_pareja']              =  'required';
                    $rules['idtipocontrato_laboral_pareja']     =  'required';
                }
                
            }


            $messages['idtipopersona.required'] = 'El campo "Tipo de Persona" es obligatorio.';
            $messages['idtipodocumento.required'] = 'El campo "Tipo de Documento" es obligatorio.';
            $messages['idgenero.required'] = 'El campo "Genero" es obligatorio.';
            $messages['fechanacimientocreacion.required'] = 'El campo "Fecha Nac./Creación" es obligatorio.';
            $messages['idestadocivil.required'] = 'El campo "Estado Civil" es obligatorio.';
            $messages['idnivelestudio.required'] = 'El campo "Nivel de Estudios" es obligatorio.';
            //$messages['profesion.required'] = 'El campo "Profesión" es obligatorio.';
            
            $messages['documento_representantelegal.required'] = 'El campo "Nro Documento Representante Legal" es obligatorio.';
            $messages['nombrecompelto_representantelegal.required'] = 'El campo "Nombre Completo Representante Legal" es obligatorio.';
            
            $messages['idubigeo.required'] = 'El campo "(Ubigeo) Distrito – Provincia – Departamento" es obligatorio.';
            $messages['idforma_ac_economica.required'] = 'El campo "Forma de Activ. Econom" es obligatorio.';
            $messages['idgiro_ac_economica.required'] = 'El campo "Giro Económico" es obligatorio.';
            $messages['descripcion_ac_economica.required'] = 'El campo "Descripción" es obligatorio.';
            $messages['direccion_ac_economica.required'] = 'El campo "Direccion" es obligatorio.';
            $messages['idubigeo_ac_economica.required'] = 'El campo "Ubicación (Ubigeo)" es obligatorio.';
            $messages['referencia_ac_economica.required'] = 'El campo "Referencia de Ubicación" es obligatorio.';
            $messages['idlocalnegocio_ac_economica.required'] = 'El campo "Local Negocio" es obligatorio.';
            //$messages['ruc_ac_economica.required'] = 'El campo "Empresa de Transporte" es obligatorio.';
            //$messages['razonsocial_ac_economica.required'] = 'El campo "Empresa de Transporte" es obligatorio.';
            /*$messages['ruc_laboral_cliente.required'] = 'El campo "RUC" es obligatorio.';
            $messages['razonsocial_laboral_cliente.required'] = 'El campo "Razón Social" es obligatorio.';
            $messages['fechainicio_laboral_cliente.required'] = 'El campo "Fecha Incio" es obligatorio.';
            $messages['antiguedad_laboral_cliente.required'] = 'El campo "Antiguedad (en años)" es obligatorio.';
            $messages['cargo_laboral_cliente.required'] = 'El campo "Cargo" es obligatorio.';
            $messages['area_laboral_cliente.required'] = 'El campo "Área" es obligatorio.';
            $messages['idtipocontrato_laboral_cliente.required'] = 'El campo "Contrato Laboral" es obligatorio.';*/
            $messages['direccion.required']  = 'El campo "Direccion" es obligatorio.';
            $messages['idfuenteingreso.required']  = 'El campo "Fuente de Ingreso" es obligatorio.';
            $messages['idtipoinformacion.required']     = 'El campo "Tipo de Información" es obligatorio.';
            // $messages['profesion.required']             = 'El campo "Profesión" es obligatorio.';
           // $messages['correo_electronico.required']    = 'El campo "Email" es obligatorio.';
            $messages['referencia_direccion.required']      = 'El campo "Referencia Ubicación" es obligatorio.';
            //$messages['suministro_electrocentro.required']  = 'El campo "Suministro Elect(Caso no exista N° Domicilio)" es obligatorio.';
            $messages['idcondicionviviendalocal.required']  = 'El campo "Condición de Vivienda" es obligatorio.';
            $messages['dni_pareja.required']                    = 'El campo "DNI" es obligatorio. ';
            $messages['nombres_pareja.required']                = 'El campo "Nombres" es obligatorio. ';
            $messages['ap_paterno_pareja.required']             = 'El campo "Apellido Paterno" es obligatorio. ';
            $messages['ap_materno_pareja.required']             = 'El campo "Apellido Materno" es obligatorio. ';
            $messages['idocupacion_pareja.required']            = 'El campo "Ocupación" es obligatorio. ';
            //$messages['profesion_pareja.required']              = 'El campo "Profesión" es obligatorio. ';
            //$messages['idnivelestudio_pareja.required']         = 'El campo "Nivel de Estudios" es obligatorio. ';
            //$messages['ruc_laboral_pareja.required']            = 'El campo "RUC" es obligatorio. ';
            $messages['razonsocial_laboral_pareja.required']    = 'El campo "Razón Social" es obligatorio. ';
            //$messages['fechainicio_laboral_pareja.required']    = 'El campo "Fecha Incio" es obligatorio. ';
            $messages['antiguedad_laboral_pareja.required']     = 'El campo "Antiguedad (en años)" es obligatorio. ';
            $messages['cargo_laboral_pareja.required']          = 'El campo "Cargo" es obligatorio. ';
            //$messages['area_laboral_pareja.required']           = 'El campo "Área" es obligatorio. ';
            $messages['idtipocontrato_laboral_pareja.required'] = 'El campo "Contrato Laboral" es obligatorio. ';
            
            

            $this->validate($request,$rules,$messages);
            

            $usuario = DB::table('users')
                        ->where('id','<>',$idusuario)
                        ->where('identificacion',$identificacion)
                        ->where('idtienda',$idtienda)
                        ->where('idestado',1)
                        ->first();

            if($usuario!='' and $identificacion!=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "RUC/DNI" ya existe, Ingrese Otro por favor.'
                ]);
            }

            /* ================================================= ACTUALIZAR */
            $usuario = DB::table('users')->whereId($idusuario)->first();
            $imagen = uploadfile($usuario->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');

            $db_idgenero        = '';
            $db_idestadocivil   = '';
            $db_idnivelestudio  = '';
            $db_idubigeo        = '';

            $db_idtipodocumento = '';
            $db_idtipoinformacion = '';
            // $db_idrepresentantelegal = '';
            $db_idpareja = '';
            $db_idocupacion_pareja = '';
            $db_idnivelestudio_pareja = '';
            $db_idcondicionviviendalocal = '';
            $db_idfuenteingreso = '';
            $db_idforma_ac_economica = '';
            $db_idgiro_ac_economica = '';
            // $db_idempresa_ac_economica = '';
            $db_idubigeo_ac_economica = '';
            $db_idlocalnegocio_ac_economica = '';
            // $db_idempresa_laboral_cliente = '';
            $db_idtipocontrato_laboral_cliente = '';
            // $db_idempresa_laboral_pareja = '';
            $db_idtipocontrato_laboral_pareja = '';
            $db_idforma_negocio_pareja = '';
            $db_idgiro_negocio_pareja = '';
            // $db_idempresa_negocio_pareja = '';
            $db_idubigeo_negocio_pareja = '';
            $db_idlocalnegocio_negocio_pareja = '';

            if( $request->idubigeo != null ){
                $db_ubigeo = DB::table('ubigeo')->whereId($request->idubigeo)->first();
                $db_idubigeo = $db_ubigeo ? $db_ubigeo->nombre : '';
            }
            if( $request->idgenero != null ){
                $db_genero = DB::table('f_genero')->whereId($request->idgenero)->first();
                $db_idgenero = $db_genero ? $db_genero->nombre : '';
            }
            if( $request->idestadocivil != null ){
                $db_estadocivil = DB::table('f_estadocivil')->whereId($request->idestadocivil)->first();
                $db_idestadocivil = $db_estadocivil ? $db_estadocivil->nombre : '';
            }
            if( $request->idnivelestudio != null ){
                $db_nivelestudio = DB::table('f_nivelestudio')->whereId($request->idnivelestudio)->first();
                $db_idnivelestudio = $db_nivelestudio ? $db_nivelestudio->nombre : '';
            }

            if( $request->idtipodocumento != null ){
                $db_tipodocumento = DB::table('s_tipodocumento')->whereId($request->idtipodocumento)->first();
                $db_idtipodocumento = $db_tipodocumento ? $db_tipodocumento->nombre : '';
            }
            if( $request->idtipoinformacion != null ){
                $db_tipoinformacion = DB::table('f_tipoinformacion')->whereId($request->idtipoinformacion)->first();
                $db_idtipoinformacion = $db_tipoinformacion ? $db_tipoinformacion->nombre : '';
            }
            if( $request->idocupacion_pareja != null ){
                $db_ocupacion = DB::table('f_ocupacion')->whereId($request->idocupacion_pareja)->first();
                $db_idocupacion_pareja = $db_ocupacion ? $db_ocupacion->nombre : '';
            }
            if( $request->idnivelestudio_pareja != null ){
                $db_nivelestudiopareja = DB::table('f_nivelestudio')->whereId($request->idnivelestudio_pareja)->first();
                $db_idnivelestudio_pareja = $db_nivelestudiopareja ? $db_nivelestudiopareja->nombre : '';
            }
            if( $request->idcondicionviviendalocal != null ){
                $db_condicionviviendalocal = DB::table('f_condicionviviendalocal')->whereId($request->idcondicionviviendalocal)->first();
                $db_idcondicionviviendalocal = $db_condicionviviendalocal ? $db_condicionviviendalocal->nombre : '';
            }
            if( $request->idfuenteingreso != null ){
                $db_fuenteingreso = DB::table('f_fuenteingreso')->whereId($request->idfuenteingreso)->first();
                $db_idfuenteingreso = $db_fuenteingreso ? $db_fuenteingreso->nombre : '';
            }

            if( $request->idforma_ac_economica != null ){
                $db_formactividadeconomica = DB::table('f_formaactividadeconomica')->whereId($request->idforma_ac_economica)->first();
                $db_idforma_ac_economica = $db_formactividadeconomica ? $db_formactividadeconomica->nombre : '';
            }
            if( $request->idgiro_ac_economica != null ){
                switch ($request->idgiro_ac_economica) {
                    case 1:
                        $db_idgiro_ac_economica = 'COMERCIO';
                        break;
                    case 2:
                        $db_idgiro_ac_economica = 'SERVICIO';
                        break;
                    case 3:
                        $db_idgiro_ac_economica = 'PRODUCCION';
                        break;
                    
                    default:
                        $db_idgiro_ac_economica = '';
                        break;
                }
                
            }
            if( $request->idubigeo_ac_economica != null ){
                $db_ubigeo_ac_economica = DB::table('ubigeo')->whereId($request->idubigeo_ac_economica)->first();
                $db_idubigeo_ac_economica = $db_ubigeo_ac_economica ? $db_ubigeo_ac_economica->nombre : '';
            }
            if( $request->idlocalnegocio_ac_economica != null ){
                $db_condicionviviendalocal = DB::table('f_condicionviviendalocal')->whereId($request->idlocalnegocio_ac_economica)->first();
                $db_idlocalnegocio_ac_economica = $db_condicionviviendalocal ? $db_condicionviviendalocal->nombre : '';
            }
            if( $request->idtipocontrato_laboral_cliente != null ){
                $db_contratolaboral = DB::table('f_contratolaboral')->whereId($request->idtipocontrato_laboral_cliente)->first();
                $db_idtipocontrato_laboral_cliente = $db_contratolaboral ? $db_contratolaboral->nombre : '';
            }
            if( $request->idtipocontrato_laboral_pareja != null ){
                $db_contratolaboral_pareja = DB::table('f_contratolaboral')->whereId($request->idtipocontrato_laboral_pareja)->first();
                $db_idtipocontrato_laboral_pareja = $db_contratolaboral_pareja ? $db_contratolaboral_pareja->nombre : '';
            }
            if( $request->idforma_negocio_pareja != null ){
                $db_formactividadeconomica = DB::table('f_formaactividadeconomica')->whereId($request->idforma_negocio_pareja)->first();
                $db_idforma_negocio_pareja = $db_formactividadeconomica ? $db_formactividadeconomica->nombre : '';
            }
            if( $request->idgiro_negocio_pareja != null ){
                switch ($request->idgiro_negocio_pareja) {
                    case 1:
                        $db_idgiro_negocio_pareja = 'COMERCIO';
                        break;
                    case 2:
                        $db_idgiro_negocio_pareja = 'SERVICIO';
                        break;
                    case 3:
                        $db_idgiro_negocio_pareja = 'PRODUCCION';
                        break;
                    
                    default:
                        $db_idgiro_negocio_pareja = '';
                        break;
                }
            }
            if( $request->idubigeo_negocio_pareja != null ){
                $db_ubigeo_ac_economica_pareja = DB::table('ubigeo')->whereId($request->idubigeo_negocio_pareja)->first();
                $db_idubigeo_negocio_pareja = $db_ubigeo_ac_economica_pareja ? $db_ubigeo_ac_economica_pareja->nombre : '';
            }
            if( $request->idlocalnegocio_negocio_pareja != null ){
                $db_condicionviviendalocal_pareja = DB::table('f_condicionviviendalocal')->whereId($request->idlocalnegocio_negocio_pareja)->first();
                $db_idlocalnegocio_negocio_pareja = $db_condicionviviendalocal_pareja ? $db_condicionviviendalocal_pareja->nombre : '';
            }

            DB::table('users')->whereId($idusuario)->update([
                'idtipopersona'       => $request->idtipopersona,
                'nombre'              => $nombre,
                'apellidopaterno'     => $apellidopaterno,
                'apellidomaterno'     => $apellidomaterno,
                'razonsocial'         => $razonsocial,
                'nombrecompleto'      => $nombrecompleto,
                'identificacion'      => $identificacion,
                'imagen'              => $imagen,
                'numerotelefono'      => $request->numerotelefono!='' ? $request->numerotelefono : '',
                'email'               => $request->email!='' ? $request->email : '',
                
                'idubigeo'            => $request->idubigeo!=null?$request->idubigeo:0,
                'direccion'           => $request->direccion!=null?$request->direccion:'',
                'mapa_latitud'        => $request->domicilio_mapa_latitud,
                'mapa_longitud'       => $request->domicilio_mapa_longitud,
                'idlogisticaruta'     => 0,
                
                'idgenero'            => $request->idgenero!=null?$request->idgenero:0,
                'fechanacimiento'     => $request->fechanacimientocreacion,
                'idestadocivil'       => $request->idestadocivil != null ? $request->idestadocivil : 0 ,
                'idnivelestudio'      => $request->idnivelestudio != null ? $request->idnivelestudio : 0 ,
                'referencia'          => $request->referencia,
                'db_idgenero'         => $db_idgenero,
                'db_idestadocivil'    => $db_idestadocivil,
                'db_idnivelestudio'   => $db_idnivelestudio,
                'db_idubigeo'         => $db_idubigeo,
            ]);
            
            
            
            $estado_casa_negocio = $request->input('casanegocio') ? 'SI' : 'NO'; 
            $informacin_cliente = [
                'idtipoinformacion'  => $request->idtipoinformacion != null ? $request->idtipoinformacion : 0 ,
                'documento_representantelegal'  => $request->documento_representantelegal,
                'nombrecompelto_representantelegal'  => $request->nombrecompelto_representantelegal,

                'profesion'             => $request->profesion != null ? $request->profesion : '',
                'correo_electronico'    => $request->correo_electronico,
                'telefono_cliente'      => $request->telefono_cliente,

                'idpareja'                  => $request->idpareja != null ? $request->idpareja : 0 ,
                'dni_pareja'                => $request->dni_pareja,
                'nombres_pareja'            => $request->nombres_pareja,
                'ap_paterno_pareja'         => $request->ap_paterno_pareja,
                'ap_materno_pareja'         => $request->ap_materno_pareja,
                'nombrecompleto_pareja'     => $request->nombres_pareja!=''?$request->nombres_pareja.' '.$request->ap_paterno_pareja.' '.$request->ap_materno_pareja:'',
                
                
                
                
                

                'idocupacion_pareja'    => $request->idocupacion_pareja != null ? $request->idocupacion_pareja : 0,
                'profesion_pareja'      => $request->profesion_pareja != null ? $request->profesion_pareja : '',
                'idnivelestudio_pareja' => $request->idnivelestudio_pareja != null ? $request->idnivelestudio_pareja : 0,
                'telefono_pareja'       => $request->telefono_pareja,
                'referencia_direccion'  => $request->referencia_direccion,
                'suministro_electrocentro' => $request->suministro_electrocentro,
                'idcondicionviviendalocal' => $request->idcondicionviviendalocal != null ? $request->idcondicionviviendalocal : 0,
                'referencia_cliente'    => $request->referencia_cliente,

                'idfuenteingreso'           => $request->idfuenteingreso != null ? $request->idfuenteingreso : 0,
                'idforma_ac_economica'      => $request->idforma_ac_economica != null ? $request->idforma_ac_economica : 0,
                'idgiro_ac_economica'       => $request->idgiro_ac_economica != null ? $request->idgiro_ac_economica : 0,
                'descripcion_ac_economica'  => $request->descripcion_ac_economica,
                // 'idempresa_ac_economica'    => $request->idempresa_ac_economica != null ? $request->idempresa_ac_economica : 0,
                'ruc_ac_economica'          => $request->ruc_ac_economica != null ? $request->ruc_ac_economica : '',
                'razonsocial_ac_economica'  => $request->razonsocial_ac_economica != null ? $request->razonsocial_ac_economica : '',
                'casanegocio'               => $estado_casa_negocio,
                'direccion_ac_economica'    => $request->direccion_ac_economica,
                'idubigeo_ac_economica'     => $request->idubigeo_ac_economica != null ? $request->idubigeo_ac_economica : 0,
                'referencia_ac_economica'   => $request->referencia_ac_economica,
                'idlocalnegocio_ac_economica' => $request->idlocalnegocio_ac_economica != null ? $request->idlocalnegocio_ac_economica : 0,


                // 'idempresa_laboral_cliente'     => $request->idempresa_laboral_cliente != null ? $request->idempresa_laboral_cliente : 0,
                'ruc_laboral_cliente'           => $request->ruc_laboral_cliente != null ? $request->ruc_laboral_cliente : '',
                'razonsocial_laboral_cliente'   => $request->razonsocial_laboral_cliente != null ? $request->razonsocial_laboral_cliente : '',
                'fechainicio_laboral_cliente'   => $request->fechainicio_laboral_cliente != null ? $request->fechainicio_laboral_cliente : null,
                'antiguedad_laboral_cliente'    => $request->antiguedad_laboral_cliente != null ? $request->antiguedad_laboral_cliente : '',
                'cargo_laboral_cliente'         => $request->cargo_laboral_cliente != null ? $request->cargo_laboral_cliente : '',
                'area_laboral_cliente'          => $request->area_laboral_cliente != null ? $request->area_laboral_cliente : '',
                'idtipocontrato_laboral_cliente' => $request->idtipocontrato_laboral_cliente != null ? $request->idtipocontrato_laboral_cliente : 0,
                

                // 'idempresa_laboral_pareja'     => $request->idempresa_laboral_pareja != null ? $request->idempresa_laboral_pareja : 0,
                'ruc_laboral_pareja'           => $request->ruc_laboral_pareja != null ? $request->ruc_laboral_pareja : '',
                'razonsocial_laboral_pareja'   => $request->razonsocial_laboral_pareja,

                'fechainicio_laboral_pareja'   => $request->fechainicio_laboral_pareja != null ? $request->fechainicio_laboral_pareja : null,
                'antiguedad_laboral_pareja'    => $request->antiguedad_laboral_pareja,
                'cargo_laboral_pareja'         => $request->cargo_laboral_pareja,
                'area_laboral_pareja'          => $request->area_laboral_pareja != null ? $request->area_laboral_pareja : '',
                'idtipocontrato_laboral_pareja' => $request->idtipocontrato_laboral_pareja != null ? $request->idtipocontrato_laboral_pareja : 0,

                'idforma_negocio_pareja'      => $request->idforma_negocio_pareja != null ? $request->idforma_negocio_pareja : 0,
                'idgiro_negocio_pareja'       => $request->idgiro_negocio_pareja != null ? $request->idgiro_negocio_pareja : 0,
                'descripcion_negocio_pareja'  => $request->descripcion_negocio_pareja,
                'idempresa_negocio_pareja'    => $request->idempresa_negocio_pareja != null ? $request->idempresa_negocio_pareja : 0,
                'ruc_negocio_pareja'          => $request->ruc_negocio_pareja != null ? $request->ruc_negocio_pareja : '',
                'razonsocial_negocio_pareja'  => $request->razonsocial_negocio_pareja != null ? $request->razonsocial_negocio_pareja : '',
                'direccion_negocio_pareja'    => $request->direccion_negocio_pareja,
                'idubigeo_negocio_pareja'     => $request->idubigeo_negocio_pareja != null ? $request->idubigeo_negocio_pareja : 0,
                'referencia_negocio_pareja'   => $request->referencia_negocio_pareja,
                'idlocalnegocio_negocio_pareja' => $request->idlocalnegocio_negocio_pareja != null ? $request->idlocalnegocio_negocio_pareja : 0,
                
                'db_idtipodocumento'                => $db_idtipodocumento,
                'db_idtipoinformacion'              => $db_idtipoinformacion,
                // 'db_idrepresentantelegal'           => $db_idrepresentantelegal,
                // 'db_idpareja'                       => $db_idpareja,
                'db_idocupacion_pareja'             => $db_idocupacion_pareja,
                'db_idnivelestudio_pareja'          => $db_idnivelestudio_pareja,
                'db_idcondicionviviendalocal'       => $db_idcondicionviviendalocal,
                'db_idfuenteingreso'                => $db_idfuenteingreso,
                'db_idforma_ac_economica'           => $db_idforma_ac_economica,
                'db_idgiro_ac_economica'            => $db_idgiro_ac_economica,
                // 'db_idempresa_ac_economica'         => $db_idempresa_ac_economica,
                'db_idubigeo_ac_economica'          => $db_idubigeo_ac_economica,
                'db_idlocalnegocio_ac_economica'    => $db_idlocalnegocio_ac_economica,
                // 'db_idempresa_laboral_cliente'      => $db_idempresa_laboral_cliente,
                'db_idtipocontrato_laboral_cliente' => $db_idtipocontrato_laboral_cliente,
                // 'db_idempresa_laboral_pareja'       => $db_idempresa_laboral_pareja,
                'db_idtipocontrato_laboral_pareja'  => $db_idtipocontrato_laboral_pareja,
                'db_idforma_negocio_pareja'         => $db_idforma_negocio_pareja,
                'db_idgiro_negocio_pareja'          => $db_idgiro_negocio_pareja,
                // 'db_idempresa_negocio_pareja'       => $db_idempresa_negocio_pareja,
                'db_idubigeo_negocio_pareja'        => $db_idubigeo_negocio_pareja,
                'db_idlocalnegocio_negocio_pareja'  => $db_idlocalnegocio_negocio_pareja,
            ];
            $buscar_user_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$idusuario)->first();
            if($buscar_user_prestamo){
                // UPDATE
                DB::table('s_users_prestamo')->whereId($buscar_user_prestamo->id)->update($informacin_cliente);
            }else{
                // INSERT
                $informacin_cliente['id_s_users'] = $idusuario;
                DB::table('s_users_prestamo')->insert($informacin_cliente);
            }
            
            json_usuario($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);

        }
        
    }

    public function destroy(Request $request, $idtienda, $idusuario)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if ($request->input('view') == 'eliminar') {
          
            DB::table('users')
                ->where('id',$idusuario)
                ->where('idtienda',$idtienda)
                ->delete();
          
            /*DB::table('users')
                ->where('id',$idusuario)
                ->where('idtienda',$idtienda)
                ->update([
                  'fechaeliminado'  => Carbon::now(),
                  'idestado'        =>2
                ]);*/

            json_usuario($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
       
    }
}
