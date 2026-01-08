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
             $responsable = DB::table('users')->whereId($request->idusuario)->first();
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/usuario/autorizacion',[
                'tienda' => $tienda,
                'responsable' => $responsable,
                'usuarios' => $usuarios,
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
            $rules['idtipoinformacion'] = 'required';
            $rules['idfuenteingreso'] = 'required';
            $rules['idtipopersona'] = 'required';
            $rules['idtipodocumento'] = 'required';
          
            $razonsocial = '';
            $apellidopaterno  = '';
            $apellidomaterno  = '';
            //$fechanacimientocreacion = '';
          
            if($request->idtipodocumento == 1) {
                $rules['dni'] = 'required|integer|numeric|digits:8';
                $rules['nombre'] = 'required';
                $rules['apellidopaterno'] = 'required';
                $rules['apellidomaterno'] = 'required';

                $identificacion   = $request->dni;
                $nombre           = $request->input('nombre');
                $apellidopaterno  = $request->input('apellidopaterno')!=null ? $request->input('apellidopaterno') : '';
                $apellidomaterno  = $request->input('apellidomaterno')!=null ? $request->input('apellidomaterno') : '';
                $nombrecompleto   = ($apellidopaterno!=''?($apellidopaterno.' '.$apellidomaterno.', '):'').$nombre;
            }
            elseif($request->idtipodocumento == 2) {
                $rules['ruc'] = 'required|integer|numeric|digits:11';
                $rules['razonsocial'] = 'required';
                //$rules['fechanacimientocreacion'] = 'required';
                $rules['documento_representantelegal'] = 'required';
                $rules['nombrecompelto_representantelegal'] = 'required';
                $rules['celular-clientetexto0'] = 'required';

                $identificacion   = $request->input('ruc');
                $nombre           = $request->input('nombrecomercial')!=null ? $request->input('nombrecomercial') : '';
                $razonsocial      = $request->input('razonsocial');
                $nombrecompleto   = $razonsocial;
                //$fechanacimientocreacion = $request->input('fechanacimientocreacion');
                $documento_representantelegal = $request->input('documento_representantelegal');
                $nombrecompelto_representantelegal = $request->input('nombrecompelto_representantelegal');
                $celular_clientetexto0 = $request->input('celular-clientetexto0');
            }
            elseif($request->idtipodocumento == 3) {
                $rules['carnetextranjeria'] = 'required';
                $rules['nombre_carnetextranjeria'] = 'required';
                $rules['apellidopaterno_carnetextranjeria'] = 'required';
                $rules['apellidomaterno_carnetextranjeria'] = 'required';

                $identificacion   = $request->input('carnetextranjeria');
                $nombre           = $request->input('nombre_carnetextranjeria');
                $apellidopaterno  = $request->input('apellidopaterno_carnetextranjeria')!=null ? $request->input('apellidopaterno_carnetextranjeria') : '';
                $apellidomaterno  = $request->input('apellidomaterno_carnetextranjeria')!=null ? $request->input('apellidomaterno_carnetextranjeria') : '';
                $nombrecompleto   = $apellidopaterno.' '.$apellidomaterno.', '.$nombre;
            }
            
            $rules['fechanacimientocreacion'] = 'required';
            $fechanacimientocreacion = $request->input('fechanacimientocreacion');
          
            $idgenero = 0;
            $idestadocivil = 0;
            $idnivelestudio = 0;
            $celular_clientetexto0 = '';

            $direccion = '';
            $idubigeo = 0;
            $referencia_direccion = '';
            $idcondicionviviendalocal = 0;
            $celular0 = '';
            $vinculo0 = '';
            $referencia0 = '';
         
            $db_idgenero        = '';
            $db_idestadocivil   = '';
            $db_idnivelestudio  = '';
            $db_idubigeo        = '';
          
            if($request->idtipodocumento == 1 or $request->idtipodocumento == 3) {
                // DATOS DEL CLIENTE
                $rules['idgenero'] = 'required';
                //$rules['fechanacimientocreacion'] = 'required';
                $rules['idestadocivil'] = 'required';
                $rules['idnivelestudio'] = 'required';
                $rules['celular-clientetexto0'] = 'required';
              
                // DOMICILIO DE CLIENTE
                $rules['direccion'] = 'required';
                $rules['idubigeo'] = 'required';
                $rules['referencia_direccion'] = 'required';
                $rules['idcondicionviviendalocal'] = 'required';
                $rules['celular0'] = 'required';
                $rules['vinculo0'] = 'required';
                $rules['referencia0'] = 'required';
              
                $idgenero = $request->input('idgenero');
                //$fechanacimientocreacion = $request->input('fechanacimientocreacion');
                $idestadocivil = $request->input('idestadocivil');
                $idnivelestudio = $request->input('idnivelestudio');
                $celular_clientetexto0 = $request->input('celular-clientetexto0');

                $direccion = $request->input('direccion');
                $idubigeo = $request->input('idubigeo');
                $referencia_direccion = $request->input('referencia_direccion');
                $idcondicionviviendalocal = $request->input('idcondicionviviendalocal');
                $celular0 = $request->input('celular0');
                $vinculo0 = $request->input('vinculo0');
                $referencia0 = $request->input('referencia0');
              
                
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
            }
            elseif($request->idtipodocumento == 2){
                // DOMICILIO DE CLIENTE
                $rules['direccion'] = 'required';
                $rules['idubigeo'] = 'required';
                $rules['referencia_direccion'] = 'required';
                $rules['idcondicionviviendalocal'] = 'required';
                $rules['celular0'] = 'required';
                $rules['vinculo0'] = 'required';
                $rules['referencia0'] = 'required';
              
                $direccion = $request->input('direccion');
                $idubigeo = $request->input('idubigeo');
                $referencia_direccion = $request->input('referencia_direccion');
                $idcondicionviviendalocal = $request->input('idcondicionviviendalocal');
                $celular0 = $request->input('celular0');
                $vinculo0 = $request->input('vinculo0');
                $referencia0 = $request->input('referencia0');
                
                if( $request->idubigeo != null ){
                    $db_ubigeo = DB::table('ubigeo')->whereId($request->idubigeo)->first();
                    $db_idubigeo = $db_ubigeo ? $db_ubigeo->nombre : '';
                }
            }
            $idforma_ac_economica = 0;
            $idgiro_ac_economica = 0;
            $descripcion_ac_economica = '';

            $direccion_ac_economica = '';
            $idubigeo_ac_economica = 0;
            $referencia_ac_economica = '';
            $idlocalnegocio_ac_economica = 0;
          
            $db_idubigeo_ac_economica = '';
            $db_idlocalnegocio_ac_economica = '';
         
            //if($request->idtipoinformacion==2  && $request->idfuenteingreso==1){
            if((($request->idtipodocumento==1 || $request->idtipodocumento==3) && $request->idfuenteingreso==1) || 
              ($request->idtipodocumento==2 && $request->idfuenteingreso==1)){
                // ACTIVIDAD ECONÓMICA DEL CLIENTE
                $rules['idforma_ac_economica'] = 'required';
                $rules['idgiro_ac_economica'] = 'required';
                $rules['descripcion_ac_economica'] = 'required';
              
                $idforma_ac_economica = $request->input('idforma_ac_economica');
                $idgiro_ac_economica = $request->input('idgiro_ac_economica');
                $descripcion_ac_economica = $request->input('descripcion_ac_economica');
              
              
                if($request->casanegocio!='on'){
                    $rules['direccion_ac_economica'] = 'required';
                    $rules['idubigeo_ac_economica'] = 'required';
                    $rules['referencia_ac_economica'] = 'required';
                    $rules['idlocalnegocio_ac_economica'] = 'required';
                  
                    $direccion_ac_economica = $request->input('direccion_ac_economica');
                    $idubigeo_ac_economica = $request->input('idubigeo_ac_economica');
                    $referencia_ac_economica = $request->input('referencia_ac_economica');
                    $idlocalnegocio_ac_economica = $request->input('idlocalnegocio_ac_economica');
                  
                    if( $request->idlocalnegocio_ac_economica != null ){
                        $db_condicionviviendalocal = DB::table('f_condicionviviendalocal')->whereId($request->idlocalnegocio_ac_economica)->first();
                        $db_idlocalnegocio_ac_economica = $db_condicionviviendalocal ? $db_condicionviviendalocal->nombre : '';
                    }
                    if( $request->idubigeo_ac_economica != null ){
                        $db_ubigeo_ac_economica = DB::table('ubigeo')->whereId($request->idubigeo_ac_economica)->first();
                        $db_idubigeo_ac_economica = $db_ubigeo_ac_economica ? $db_ubigeo_ac_economica->nombre : '';
                    }
                }
              
            }
          
            /*$idforma_negocio_pareja = 0;
            $idgiro_negocio_pareja = 0;
            $descripcion_negocio_pareja = '';
            $direccion_negocio_pareja = '';
            $idubigeo_negocio_pareja = 0;
            $idlocalnegocio_negocio_pareja = 0;
          
            $db_idforma_negocio_pareja = '';
            $db_idubigeo_negocio_pareja = '';
            $db_idlocalnegocio_negocio_pareja = '';
            if(($request->idtipoinformacion==2 && $request->idfuenteingreso==1 && $request->idocupacion_pareja==1) or ($request->idtipoinformacion==2 && $request->idocupacion_pareja==1)){
                // NEGOCIO DE: PAREJA/REPRESENTANTE LEG.
                $rules['idforma_negocio_pareja'] = 'required';
                $rules['idgiro_negocio_pareja'] = 'required';
                $rules['descripcion_negocio_pareja'] = 'required';
                $rules['direccion_negocio_pareja'] = 'required';
                $rules['idubigeo_negocio_pareja'] = 'required';
                $rules['idlocalnegocio_negocio_pareja'] = 'required';
              
                $idforma_negocio_pareja = $request->input('idforma_negocio_pareja');
                $idgiro_negocio_pareja = $request->input('idgiro_negocio_pareja');
                $descripcion_negocio_pareja = $request->input('descripcion_negocio_pareja');
                $direccion_negocio_pareja = $request->input('direccion_negocio_pareja');
                $idubigeo_negocio_pareja = $request->input('idubigeo_negocio_pareja');
                $idlocalnegocio_negocio_pareja = $request->input('idlocalnegocio_negocio_pareja');
              
                if( $request->idforma_negocio_pareja != null ){
                    $db_formactividadeconomica = DB::table('f_formaactividadeconomica')->whereId($request->idforma_negocio_pareja)->first();
                    $db_idforma_negocio_pareja = $db_formactividadeconomica ? $db_formactividadeconomica->nombre : '';
                }
              
                if( $request->idubigeo_negocio_pareja != null ){
                    $db_ubigeo_ac_economica_pareja = DB::table('ubigeo')->whereId($request->idubigeo_negocio_pareja)->first();
                    $db_idubigeo_negocio_pareja = $db_ubigeo_ac_economica_pareja ? $db_ubigeo_ac_economica_pareja->nombre : '';
                }
                if( $request->idlocalnegocio_negocio_pareja != null ){
                    $db_condicionviviendalocal_pareja = DB::table('f_condicionviviendalocal')->whereId($request->idlocalnegocio_negocio_pareja)->first();
                    $db_idlocalnegocio_negocio_pareja = $db_condicionviviendalocal_pareja ? $db_condicionviviendalocal_pareja->nombre : '';
                }
            }
            */
          
            $idempresa_laboral_pareja = 0;
            $ruc_laboral_pareja = '';
            $razonsocial_laboral_pareja = '';
            $fechainicio_laboral_pareja = null;
            $antiguedad_laboral_pareja = '';
            $cargo_laboral_pareja = '';
            $area_laboral_pareja = '';
            $idtipocontrato_laboral_pareja = 0;
          
            $idforma_negocio_pareja = 0;
            $idgiro_negocio_pareja = 0;
            $descripcion_negocio_pareja = '';
            $idempresa_negocio_pareja = 0;
            $ruc_negocio_pareja = '';
            $razonsocial_negocio_pareja = '';
            $direccion_negocio_pareja = '';
            $idubigeo_negocio_pareja = 0;
            $referencia_negocio_pareja = '';
            $idlocalnegocio_negocio_pareja = 0;
          
            $db_idtipocontrato_laboral_pareja = '';
            $db_idforma_negocio_pareja = '';
            $db_idubigeo_negocio_pareja = '';
            $db_idlocalnegocio_negocio_pareja = '';
            $db_idgiro_negocio_pareja = '';
          
            /*$razonsocial_laboral_pareja = '';
            $antiguedad_laboral_pareja = '';
            $cargo_laboral_pareja = '';
            $idtipocontrato_laboral_pareja = 0;
          
            $db_idtipocontrato_laboral_pareja = '';*/
            //if(($request->idtipoinformacion==2 && $request->idocupacion_pareja==2) || ($request->idtipodocumento==2 && $request->idfuenteingreso==1 && $request->idtipoinformacion==2)){
 
                // CENTRO LABORAL DE: PAREJA/REPRESENTANTE LEG.
                /*$rules['razonsocial_laboral_pareja'] = 'required';
                $rules['antiguedad_laboral_pareja'] = 'required';
                $rules['cargo_laboral_pareja'] = 'required';
                $rules['idtipocontrato_laboral_pareja'] = 'required';*/
          
                /*$razonsocial_laboral_pareja = $request->input('razonsocial_laboral_pareja') != null ? $request->input('razonsocial_laboral_pareja') : '';
                $antiguedad_laboral_pareja = $request->input('antiguedad_laboral_pareja') != null ? $request->input('antiguedad_laboral_pareja') : '';
                $cargo_laboral_pareja = $request->input('cargo_laboral_pareja') != null ? $request->input('cargo_laboral_pareja') : '';
                $idtipocontrato_laboral_pareja = $request->input('idtipocontrato_laboral_pareja') != null ? $request->input('idtipocontrato_laboral_pareja') : 0;
              
                if( $request->idtipocontrato_laboral_pareja != null ){
                    $db_contratolaboral_pareja = DB::table('f_contratolaboral')->whereId($request->idtipocontrato_laboral_pareja)->first();
                    $db_idtipocontrato_laboral_pareja = $db_contratolaboral_pareja ? $db_contratolaboral_pareja->nombre : '';
                }*/
          
                /*$razonsocial_laboral_pareja = $request->input('razonsocial_laboral_pareja');
                $antiguedad_laboral_pareja = $request->input('antiguedad_laboral_pareja');
                $cargo_laboral_pareja = $request->input('cargo_laboral_pareja');
                $idtipocontrato_laboral_pareja = $request->input('idtipocontrato_laboral_pareja');
              
                if( $request->idtipocontrato_laboral_pareja != null ){
                    $db_contratolaboral_pareja = DB::table('f_contratolaboral')->whereId($request->idtipocontrato_laboral_pareja)->first();
                    $db_idtipocontrato_laboral_pareja = $db_contratolaboral_pareja ? $db_contratolaboral_pareja->nombre : '';
                }*/
            //}
            if($request->idestadocivil==2 || $request->idestadocivil==4  or ($request->idtipoinformacion==2 && $request->idfuenteingreso==1)){
                if($request->idocupacion_pareja==2 or ($request->idtipoinformacion==2 && $request->idfuenteingreso==1) ){
                  
                    if($request->ruc_laboral_pareja!=''){
                        $rules['ruc_laboral_pareja'] = 'required|integer|numeric|digits:11';
                    }
                  
                    $ruc_laboral_pareja = $request->ruc_laboral_pareja!= null ? $request->ruc_laboral_pareja : '';
                    $razonsocial_laboral_pareja = $request->input('razonsocial_laboral_pareja') != null ? $request->input('razonsocial_laboral_pareja') : '';
                    $fechainicio_laboral_pareja = $request->fechainicio_laboral_pareja != null ? $request->fechainicio_laboral_pareja : null;
                    $antiguedad_laboral_pareja = $request->input('antiguedad_laboral_pareja') != null ? $request->input('antiguedad_laboral_pareja') : '';
                    $cargo_laboral_pareja = $request->input('cargo_laboral_pareja') != null ? $request->input('cargo_laboral_pareja') : '';
                    $area_laboral_pareja = $request->area_laboral_pareja != null ? $request->area_laboral_pareja : '';
                    $idtipocontrato_laboral_pareja = $request->input('idtipocontrato_laboral_pareja') != null ? $request->input('idtipocontrato_laboral_pareja') : 0;

                    if( $request->idtipocontrato_laboral_pareja != null ){
                        $db_contratolaboral_pareja = DB::table('f_contratolaboral')->whereId($request->idtipocontrato_laboral_pareja)->first();
                        $db_idtipocontrato_laboral_pareja = $db_contratolaboral_pareja ? $db_contratolaboral_pareja->nombre : '';
                    }
                }
                elseif($request->idocupacion_pareja==1 && $request->idtipoinformacion==2){
                  
                    // NEGOCIO DE: PAREJA/REPRESENTANTE LEG.
                    $rules['idforma_negocio_pareja'] = 'required';
                    $rules['idgiro_negocio_pareja'] = 'required';
                    $rules['descripcion_negocio_pareja'] = 'required';
                    $rules['direccion_negocio_pareja'] = 'required';
                    $rules['idubigeo_negocio_pareja'] = 'required';
                    $rules['idlocalnegocio_negocio_pareja'] = 'required';

                    if($request->ruc_negocio_pareja!=''){
                        $rules['ruc_negocio_pareja'] = 'required|integer|numeric|digits:11';
                    }
                  
                    $idforma_negocio_pareja = $request->input('idforma_negocio_pareja');
                    $idgiro_negocio_pareja = $request->input('idgiro_negocio_pareja');
                    $descripcion_negocio_pareja = $request->input('descripcion_negocio_pareja');
                    $ruc_negocio_pareja = $request->ruc_negocio_pareja != null ? $request->ruc_negocio_pareja : '';
                    $razonsocial_negocio_pareja = $request->razonsocial_negocio_pareja != null ? $request->razonsocial_negocio_pareja : '';
                    $direccion_negocio_pareja = $request->input('direccion_negocio_pareja');
                    $referencia_negocio_pareja = $request->referencia_negocio_pareja;
                    $idubigeo_negocio_pareja = $request->input('idubigeo_negocio_pareja');
                    $idlocalnegocio_negocio_pareja = $request->input('idlocalnegocio_negocio_pareja');

                    if( $request->idforma_negocio_pareja != null ){
                        $db_formactividadeconomica = DB::table('f_formaactividadeconomica')->whereId($request->idforma_negocio_pareja)->first();
                        $db_idforma_negocio_pareja = $db_formactividadeconomica ? $db_formactividadeconomica->nombre : '';
                    }

                    if( $request->idubigeo_negocio_pareja != null ){
                        $db_ubigeo_ac_economica_pareja = DB::table('ubigeo')->whereId($request->idubigeo_negocio_pareja)->first();
                        $db_idubigeo_negocio_pareja = $db_ubigeo_ac_economica_pareja ? $db_ubigeo_ac_economica_pareja->nombre : '';
                    }
                    if( $request->idlocalnegocio_negocio_pareja != null ){
                        $db_condicionviviendalocal_pareja = DB::table('f_condicionviviendalocal')->whereId($request->idlocalnegocio_negocio_pareja)->first();
                        $db_idlocalnegocio_negocio_pareja = $db_condicionviviendalocal_pareja ? $db_condicionviviendalocal_pareja->nombre : '';
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
                }
            }
          
            $dni_pareja = '';
            $nombres_pareja = '';
            $ap_paterno_pareja = '';
            $ap_materno_pareja = '';
            $idocupacion_pareja = 0;
          
            $profesion_pareja = '';
            $idnivelestudio_pareja = 0;
            $telefono_pareja = '[]';
          
            $db_idocupacion_pareja = '';
            $db_idnivelestudio_pareja = '';
          
            if($request->idestadocivil==2 || $request->idestadocivil==4){
                // DATOS DE PAREJA
                $rules['dni_pareja'] = 'required';
                $rules['nombres_pareja'] = 'required';
                $rules['ap_paterno_pareja'] = 'required';
                $rules['ap_materno_pareja'] = 'required';
                $rules['idocupacion_pareja'] = 'required';
              
                $dni_pareja = $request->input('dni_pareja');
                $nombres_pareja = $request->input('nombres_pareja');
                $ap_paterno_pareja = $request->input('ap_paterno_pareja');
                $ap_materno_pareja = $request->input('ap_materno_pareja');
                $idocupacion_pareja = $request->input('idocupacion_pareja');
          
                $profesion_pareja = $request->input('profesion_pareja');
                $idnivelestudio_pareja = $request->input('idnivelestudio_pareja');
                $telefono_pareja = $request->input('telefono_pareja');

                if( $request->idocupacion_pareja != null ){
                    $db_ocupacion = DB::table('f_ocupacion')->whereId($request->idocupacion_pareja)->first();
                    $db_idocupacion_pareja = $db_ocupacion ? $db_ocupacion->nombre : '';
                }
              
                if( $request->idnivelestudio_pareja != null ){
                    $db_nivelestudiopareja = DB::table('f_nivelestudio')->whereId($request->idnivelestudio_pareja)->first();
                    $db_idnivelestudio_pareja = $db_nivelestudiopareja ? $db_nivelestudiopareja->nombre : '';
                }
          
                if($request->dni_pareja!=''){
                    $rules['dni_pareja'] = 'required|integer|numeric|digits:8';
                }
            }
            if($request->ruc_laboral_cliente!=''){
                $rules['ruc_laboral_cliente'] = 'required|integer|numeric|digits:11';
            }
          
            $messages = [
                'idtipoinformacion.required' => 'El "Tipo de Información" es Obligatorio.',
                'idfuenteingreso.required' => 'El "Fuente de Ingreso" es Obligatorio.',
                'idtipopersona.required' => 'El "Tipo de Persona" es Obligatorio.',
                'idtipodocumento.required' => 'El "Tipo de Documento" es Obligatorio.',
              
                'dni.required' => 'El "DNI" es obligatorio.',
                'dni.integer' => 'El "DNI" debe ser número entero.',
                'dni.numeric' => 'El "DNI" debe ser Númerico.',
                'dni.digits' => 'El "DNI" debe ser de 8 Digitos.',
              
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'apellidopaterno.required' => 'El "Apellido Paterno" es Obligatorio.',
                'apellidomaterno.required' => 'El "Apellido Materno" es Obligatorio.',
         
                'ruc.required' => 'El "RUC" es obligatorio.',
                'ruc.integer' => 'El "RUC" debe ser número entero.',
                'ruc.numeric' => 'El "RUC" debe ser Númerico.',
                'ruc.digits' => 'El "RUC" debe ser de 11 Digitos.',
              
                'razonsocial.required' => 'La "Razón Social" es Obligatorio.',
                'fechanacimientocreacion.required' => 'La "Fecha Nac./Creación" es Obligatorio.',
                'documento_representantelegal.required' => 'El "Nro Documento Representante Legal" es Obligatorio.',
                'nombrecompelto_representantelegal.required' => 'El "Nombre Completo Representante Legal" es Obligatorio.',
                'celular-clientetexto0.required' => 'El "Telf./Celular" es Obligatorio.',
              
                'carnetextranjeria.required' => 'El "Carnet Extranjería" es Obligatorio.',
                'nombre_carnetextranjeria.required' => 'El "Nombre" es Obligatorio.',
                'apellidopaterno_carnetextranjeria.required' => 'El "Apellido Paterno" es Obligatorio.',
                'apellidomaterno_carnetextranjeria.required' => 'El "Apellido Materno" es Obligatorio.',
              
                'idgenero.required' => 'El "Genero" es Obligatorio.',
                'idestadocivil.required' => 'El "Estado Civil" es Obligatorio.',
                'idnivelestudio.required' => 'El "Nivel de Estudios" es Obligatorio.',
                'celular-clientetexto0.required' => 'El "Telf./Celular" es Obligatorio.',
              
                'direccion.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo.required' => 'El "Distrito – Provincia – Departamento" es Obligatorio.',
                'referencia_direccion.required' => 'La "Referencia Ubicación" es Obligatorio.',
                'idcondicionviviendalocal.required' => 'La "Condición de Vivienda/Local" es Obligatorio.',
                'celular0.required' => 'El "Telf./Celular" es Obligatorio.',
                'vinculo0.required' => 'Los "Nombres y Apellidos" es Obligatorio.',
                'referencia0.required' => 'El "Vinculo Familiar/Personas/Otros" es Obligatorio.',
              
                'idforma_ac_economica.required' => 'La "Forma de Activ. Econom" es Obligatorio.',
                'idgiro_ac_economica.required' => 'El "Giro Económico" es Obligatorio.',
                'descripcion_ac_economica.required' => 'La "Descripción" es Obligatorio.',
                'direccion_ac_economica.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo_ac_economica.required' => 'El "Distrito – Provincia – Departamento" es Obligatorio.',
                'referencia_ac_economica.required' => 'La "Referencia de Ubicación" es Obligatorio.',
                'idlocalnegocio_ac_economica.required' => 'El "Local Negocio" es Obligatorio.',
                    
                'dni_pareja.required' => 'El "PAREJA - DNI/CE" es Obligatorio.',
                'dni_pareja.integer' => 'El "PAREJA - DNI/CE" debe ser número entero.',
                'dni_pareja.numeric' => 'El "PAREJA - DNI/CE" debe ser Númerico.',
                'dni_pareja.digits' => 'El "PAREJA - DNI/CE" debe ser de 8 Digitos.',
              
                'nombres_pareja.required' => 'El "PAREJA - Nombre" es Obligatorio.',
                'ap_paterno_pareja.required' => 'El "PAREJA - Apellido Paterno" es Obligatorio.',
                'ap_materno_pareja.required' => 'El "PAREJA - Apellido Materno" es Obligatorio.',
                'idocupacion_pareja.required' => 'La "PAREJA - Ocupación" es Obligatorio.',
                  
                'idforma_negocio_pareja.required' => 'La "PAREJA - Forma de Activ. Econom" es Obligatorio.',
                'idgiro_negocio_pareja.required' => 'El "PAREJA - Giro Económico" es Obligatorio.',
                'descripcion_negocio_pareja.required' => 'PAREJA - La "Descripción" es Obligatorio.',
                'direccion_negocio_pareja.required' => 'La "PAREJA - Dirección" es Obligatorio.',
                'idubigeo_negocio_pareja.required' => 'El "PAREJA - Distrito – Provincia – Departamento" es Obligatorio.',
                'idlocalnegocio_negocio_pareja.required' => 'El "PAREJA - Negocio" es Obligatorio.',
               
                'razonsocial_laboral_pareja.required' => 'El "PAREJA - Nombre: Persona Natural/Persona Jurídica" es Obligatorio.',
                'antiguedad_laboral_pareja.required' => 'La "PAREJA - Antiguedad (en años)" es Obligatorio.',
                'cargo_laboral_pareja.required' => 'El "PAREJA - Cargo" es Obligatorio.',
                'idtipocontrato_laboral_pareja.required' => 'El "PAREJA - Contrato Laboral" es Obligatorio.',
              
                'ruc_laboral_cliente.required' => 'El "RUC" es obligatorio.',
                'ruc_laboral_cliente.integer' => 'El "RUC" debe ser número entero.',
                'ruc_laboral_cliente.numeric' => 'El "RUC" debe ser Númerico.',
                'ruc_laboral_cliente.digits' => 'El "RUC" debe ser de 11 Digitos.',
              
                'ruc_laboral_pareja.required' => 'El "RUC DE PAREJA/REPRESENTANTE LEG." es obligatorio.',
                'ruc_laboral_pareja.integer' => 'El "RUC DE PAREJA/REPRESENTANTE LEG." debe ser número entero.',
                'ruc_laboral_pareja.numeric' => 'El "RUC DE PAREJA/REPRESENTANTE LEG." debe ser Númerico.',
                'ruc_laboral_pareja.digits' => 'El "RUC DE PAREJA/REPRESENTANTE LEG." debe ser de 11 Digitos.',
              
                'ruc_negocio_pareja.required' => 'El "RUC DE NEGOCIO DE: PAREJA" es obligatorio.',
                'ruc_negocio_pareja.integer' => 'El "RUC DE NEGOCIO DE: PAREJA" debe ser número entero.',
                'ruc_negocio_pareja.numeric' => 'El "RUC DE NEGOCIO DE: PAREJA" debe ser Númerico.',
                'ruc_negocio_pareja.digits' => 'El "RUC DE NEGOCIO DE: PAREJA" debe ser de 11 Digitos.',
            ];
            $this->validate($request,$rules,$messages);
            // FIN NUEVAS VALIDACIONES
          
            $usuario = DB::table('users')
                ->where('identificacion',$identificacion)
                ->where('idtienda',$idtienda)
                ->where('idestado',1)
                ->first();

            if($usuario!='' and $identificacion!=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "RUC/DNI" ya esta registrado, Ingrese Otro por favor.'
                ]);
            }

            $db_idtipoinformacion = '';
            $db_idtipodocumento = '';
            $db_idcondicionviviendalocal = '';
            $db_idfuenteingreso = '';
            $db_idforma_ac_economica = '';
            $db_idgiro_ac_economica = '';
            $db_idtipocontrato_laboral_cliente = '';
            $db_idgiro_negocio_pareja = '';
          
            if( $request->idtipoinformacion != null ){
                $db_tipoinformacion = DB::table('f_tipoinformacion')->whereId($request->idtipoinformacion)->first();
                $db_idtipoinformacion = $db_tipoinformacion ? $db_tipoinformacion->nombre : '';
            }
            if( $request->idtipodocumento != null ){
                $db_tipodocumento = DB::table('s_tipodocumento')->whereId($request->idtipodocumento)->first();
                $db_idtipodocumento = $db_tipodocumento ? $db_tipodocumento->nombre : '';
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
            if( $request->idtipocontrato_laboral_cliente != null ){
                $db_contratolaboral = DB::table('f_contratolaboral')->whereId($request->idtipocontrato_laboral_cliente)->first();
                $db_idtipocontrato_laboral_cliente = $db_contratolaboral ? $db_contratolaboral->nombre : '';
            }
            /*if( $request->idgiro_negocio_pareja != null ){
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
            }*/

            /* ================================================= REGISTRAR */
          
            // obtener ultimo código
            $users = DB::table('users')
                ->where('users.idtienda',$idtienda)
                ->where('users.idtipousuario',2)
                ->orderBy('users.codigo','desc')
                ->limit(1)
                ->first();

            $codigo = 1;
            if($users!=''){
                $cod = explode('CL',$users->codigo);
                $codigo = intval($cod[1])+1;
            }
            
            $user_id = DB::table('users')->insertGetId([
                'fechamodificacion' => Carbon::now(),
                'iduser_modificacion' => Auth::user()->id,
              
                'idtipopersona'       => $request->idtipopersona,
                'codigo'              => 'CL'.str_pad($codigo, 8, "0", STR_PAD_LEFT),
                'nombre'              => $nombre,
                'apellidopaterno'     => $apellidopaterno,
                'apellidomaterno'     => $apellidomaterno,
                'razonsocial'         => $razonsocial,
                'nombrecompleto'      => $nombrecompleto,
                'identificacion'      => $identificacion,
                'email'               => '',
                'imagen'              => '',
                'numerotelefono'      => $celular_clientetexto0,
                'direccion'           => $direccion,
                'mapa_latitud'        => '',
                'mapa_longitud'       => '',
                'email_verified_at'   => Carbon::now(),
                'usuario'             => Carbon::now()->format("Ymdhisu"),
                'clave'               => '123',
                'password'            => Hash::make('123'),
                'idubigeo'            => $idubigeo,
                'iduserspadre'        => 0,
                'idtipousuario'       => 2, // 3=usuario sistema
                'idtienda'            => $idtienda,
                'idasesor'            => Auth::user()->id,
                'idestadousuario'     => 2,
                'idestado'            => 1,
                'idgenero'            => $idgenero,
                'fechanacimiento'     => $fechanacimientocreacion,
                'idestadocivil'       => $idestadocivil,
                'idnivelestudio'      => $idnivelestudio,
                'referencia'          => $request->referencia,

                'db_idgenero'         => $db_idgenero,
                'db_idestadocivil'    => $db_idestadocivil,
                'db_idnivelestudio'   => $db_idnivelestudio,
                'db_idubigeo'         => $db_idubigeo,
            ]);
          
            //$usersnew = DB::table('users')->whereId($idtienda)->first();
          
            /*DB::table('users')->whereId($user_id)->update([
                'codigo'       => 'CL'.str_pad($user_id, 8, "0", STR_PAD_LEFT),
            ]);*/

            $estado_casa_negocio = $request->input('casanegocio') ? 'SI' : 'NO'; 
            $informacin_cliente = [
                'idtipodocumento'       => $request->idtipodocumento != null ? $request->idtipodocumento : 0 ,
                'idtipoinformacion'     => $request->idtipoinformacion != null ? $request->idtipoinformacion : 0 ,
                'documento_representantelegal'          => $request->documento_representantelegal,
                'nombrecompelto_representantelegal'     => $request->nombrecompelto_representantelegal,

                'profesion'             => $request->profesion != null ? $request->profesion : '',
                'correo_electronico'    => $request->correo_electronico,
                'telefono_cliente'      => $request->telefono_cliente,

                'idpareja'                  => 0,
                'dni_pareja'                => $dni_pareja,
                'nombres_pareja'            => $nombres_pareja,
                'ap_paterno_pareja'         => $ap_paterno_pareja,
                'ap_materno_pareja'         => $ap_materno_pareja,
                'nombrecompleto_pareja'     => $nombres_pareja!=''?$ap_paterno_pareja.' '.$ap_materno_pareja.', '.$nombres_pareja:$nombres_pareja,
                
                'idocupacion_pareja'    => $idocupacion_pareja,
                'profesion_pareja'      => $profesion_pareja,
                'idnivelestudio_pareja' => $idnivelestudio_pareja,
                'telefono_pareja'       => $telefono_pareja,
              
                'referencia_direccion'  => $referencia_direccion,
                'suministro_electrocentro' => $request->suministro_electrocentro,
                'idcondicionviviendalocal' => $idcondicionviviendalocal,
                'referencia_cliente'    => $request->referencia_cliente,

                'idfuenteingreso'           => $request->idfuenteingreso != null ? $request->idfuenteingreso : 0,
                'idforma_ac_economica'      => $idforma_ac_economica,
                'idgiro_ac_economica'       => $idgiro_ac_economica,
                'descripcion_ac_economica'  => $descripcion_ac_economica,
                'ruc_ac_economica'          => $request->ruc_ac_economica != null ? $request->ruc_ac_economica : '',
                'razonsocial_ac_economica'  => $request->razonsocial_ac_economica != null ? $request->razonsocial_ac_economica : '',
                'casanegocio'               => $estado_casa_negocio,
                'direccion_ac_economica'    => $direccion_ac_economica,
                'idubigeo_ac_economica'     => $idubigeo_ac_economica,
                'referencia_ac_economica'   => $referencia_ac_economica,
                'idlocalnegocio_ac_economica' => $idlocalnegocio_ac_economica,

                'ruc_laboral_cliente'           => $request->ruc_laboral_cliente != null ? $request->ruc_laboral_cliente : '',
                'razonsocial_laboral_cliente'   => $request->razonsocial_laboral_cliente != null ? $request->razonsocial_laboral_cliente : '',
                'fechainicio_laboral_cliente'   => $request->fechainicio_laboral_cliente != null ? $request->fechainicio_laboral_cliente : null,
                'antiguedad_laboral_cliente'    => $request->antiguedad_laboral_cliente != null ? $request->antiguedad_laboral_cliente : '',
                'cargo_laboral_cliente'         => $request->cargo_laboral_cliente != null ? $request->cargo_laboral_cliente : '',
                'area_laboral_cliente'          => $request->area_laboral_cliente != null ? $request->area_laboral_cliente : '',
                'idtipocontrato_laboral_cliente' => $request->idtipocontrato_laboral_cliente != null ? $request->idtipocontrato_laboral_cliente : 0,

                'ruc_laboral_pareja'           => $ruc_laboral_pareja,
                'razonsocial_laboral_pareja'   => $razonsocial_laboral_pareja,

                'fechainicio_laboral_pareja'   => $fechainicio_laboral_pareja,
                'antiguedad_laboral_pareja'    => $antiguedad_laboral_pareja,
                'cargo_laboral_pareja'         => $cargo_laboral_pareja,
                'area_laboral_pareja'          => $area_laboral_pareja,
                'idtipocontrato_laboral_pareja' => $idtipocontrato_laboral_pareja,

                'idforma_negocio_pareja'      => $idforma_negocio_pareja,
                'idgiro_negocio_pareja'       => $idgiro_negocio_pareja,
                'descripcion_negocio_pareja'  => $descripcion_negocio_pareja,
                'idempresa_negocio_pareja'    => $request->idempresa_negocio_pareja != null ? $request->idempresa_negocio_pareja : 0,
                'ruc_negocio_pareja'          => $ruc_negocio_pareja,
                'razonsocial_negocio_pareja'  => $razonsocial_negocio_pareja,
                'direccion_negocio_pareja'    => $direccion_negocio_pareja,
                'idubigeo_negocio_pareja'     => $idubigeo_negocio_pareja,
                'referencia_negocio_pareja'   => $referencia_negocio_pareja,
                'idlocalnegocio_negocio_pareja' => $idlocalnegocio_negocio_pareja,

                'db_idtipodocumento'                => $db_idtipodocumento,
                'db_idtipoinformacion'              => $db_idtipoinformacion,
                'db_idocupacion_pareja'             => $db_idocupacion_pareja,
                'db_idnivelestudio_pareja'          => $db_idnivelestudio_pareja,
                'db_idcondicionviviendalocal'       => $db_idcondicionviviendalocal,
                'db_idfuenteingreso'                => $db_idfuenteingreso,
                'db_idforma_ac_economica'           => $db_idforma_ac_economica,
                'db_idgiro_ac_economica'            => $db_idgiro_ac_economica,
                'db_idubigeo_ac_economica'          => $db_idubigeo_ac_economica,
                'db_idlocalnegocio_ac_economica'    => $db_idlocalnegocio_ac_economica,
                'db_idtipocontrato_laboral_cliente' => $db_idtipocontrato_laboral_cliente,
                'db_idtipocontrato_laboral_pareja'  => $db_idtipocontrato_laboral_pareja,
                'db_idforma_negocio_pareja'         => $db_idforma_negocio_pareja,
                'db_idgiro_negocio_pareja'          => $db_idgiro_negocio_pareja,
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

            $rules = [
                'idresponsable' => 'required',          
                'responsableclave' => 'required',              
            ];
          
            $messages = [
                'idresponsable.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);
          
            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->where('users.clave',$request->responsableclave)
                ->first();
            $idresponsable = 0;
            if($usuario!=''){
                $idresponsable = $usuario->id;
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.',
                'iduser_modificacion'   => $idresponsable
            ]);
            
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
          
            $prendaria = DB::table('credito_garantia')
                                ->join('credito','credito.id','credito_garantia.idcredito')
                                ->where('credito_garantia.idcliente',$id)
                                ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
                                ->count();
          
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
                'prendaria'         => $prendaria,
            ]);
          
        } 
        else if($request->input('view') == 'ubicacion') {
            return view(sistema_view().'/usuario/ubicacion',[
                'tienda'            => $tienda,
                'usuario'           => $usuario,
            ]);
        } 
        else if( $request->input('view') == 'imprimir_ubicacion' ){
            
            return view(sistema_view().'/usuario/imprimir_ubicacion',[
                'tienda'            => $tienda,
                'usuario'           => $usuario,
            ]); 
        }
        else if($request->input('view') == 'imprimir_ubicacionpdf') {
            $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$id)->first();
            $pdf = PDF::loadView(sistema_view().'/usuario/imprimir_ubicacionpdf',[
                'users_prestamo'    => $users_prestamo,
                'tienda'            => $tienda,
                'usuario'           => $usuario,
            ]); 
            $pdf->setPaper('A4');
            // $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('FICHA_CLIENTE.pdf');
          
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
            $rules = [];
            $rules['idtipoinformacion'] = 'required';
            $rules['idfuenteingreso'] = 'required';
            $rules['idtipopersona'] = 'required';
            $rules['idtipodocumento'] = 'required';
          
            $razonsocial = '';
            $apellidopaterno  = '';
            $apellidomaterno  = '';
          
            if($request->idtipodocumento == 1) {
                $rules['dni'] = 'required|integer|numeric|digits:8';
                $rules['nombre'] = 'required';
                $rules['apellidopaterno'] = 'required';
                $rules['apellidomaterno'] = 'required';

                $identificacion   = $request->dni;
                $nombre           = $request->input('nombre');
                $apellidopaterno  = $request->input('apellidopaterno')!=null ? $request->input('apellidopaterno') : '';
                $apellidomaterno  = $request->input('apellidomaterno')!=null ? $request->input('apellidomaterno') : '';
                $nombrecompleto   = ($apellidopaterno!=''?($apellidopaterno.' '.$apellidomaterno.', '):'').$nombre;
            }
            elseif($request->idtipodocumento == 2) {
                $rules['ruc'] = 'required|integer|numeric|digits:11';
                $rules['razonsocial'] = 'required';
                //$rules['fechanacimientocreacion'] = 'required';
                $rules['documento_representantelegal'] = 'required';
                $rules['nombrecompelto_representantelegal'] = 'required';
                $rules['celular-clientetexto0'] = 'required';

                $identificacion   = $request->input('ruc');
                $nombre           = $request->input('nombrecomercial')!=null ? $request->input('nombrecomercial') : '';
                $razonsocial      = $request->input('razonsocial');
                $nombrecompleto   = $razonsocial;
                //$fechanacimientocreacion = $request->input('fechanacimientocreacion');
                $documento_representantelegal = $request->input('documento_representantelegal');
                $nombrecompelto_representantelegal = $request->input('nombrecompelto_representantelegal');
                $celular_clientetexto0 = $request->input('celular-clientetexto0');
            }
            elseif($request->idtipodocumento == 3) {
                $rules['carnetextranjeria'] = 'required';
                $rules['nombre_carnetextranjeria'] = 'required';
                $rules['apellidopaterno_carnetextranjeria'] = 'required';
                $rules['apellidomaterno_carnetextranjeria'] = 'required';

                $identificacion   = $request->input('carnetextranjeria');
                $nombre           = $request->input('nombre_carnetextranjeria');
                $apellidopaterno  = $request->input('apellidopaterno_carnetextranjeria')!=null ? $request->input('apellidopaterno_carnetextranjeria') : '';
                $apellidomaterno  = $request->input('apellidomaterno_carnetextranjeria')!=null ? $request->input('apellidomaterno_carnetextranjeria') : '';
                $nombrecompleto   = $apellidopaterno.' '.$apellidomaterno.', '.$nombre;
            }
            
            $rules['fechanacimientocreacion'] = 'required';
            $fechanacimientocreacion = $request->input('fechanacimientocreacion');
          
            $idgenero = 0;
            //$fechanacimientocreacion = NULL;
            $idestadocivil = 0;
            $idnivelestudio = 0;
            $celular_clientetexto0 = '';

            $direccion = '';
            $idubigeo = 0;
            $referencia_direccion = '';
            $idcondicionviviendalocal = 0;
            $celular0 = '';
            $vinculo0 = '';
            $referencia0 = '';
         
            $db_idgenero        = '';
            $db_idestadocivil   = '';
            $db_idnivelestudio  = '';
            $db_idubigeo        = '';
          
            if($request->idtipodocumento == 1 or $request->idtipodocumento == 3) {
                // DATOS DEL CLIENTE
                $rules['idgenero'] = 'required';
                //$rules['fechanacimientocreacion'] = 'required';
                $rules['idestadocivil'] = 'required';
                $rules['idnivelestudio'] = 'required';
                $rules['celular0'] = 'required';
              
                // DOMICILIO DE CLIENTE
                $rules['direccion'] = 'required';
                $rules['idubigeo'] = 'required';
                $rules['referencia_direccion'] = 'required';
                $rules['idcondicionviviendalocal'] = 'required';
                $rules['celular0'] = 'required';
                $rules['vinculo0'] = 'required';
                $rules['referencia0'] = 'required';
              
                $idgenero = $request->input('idgenero');
                //$fechanacimientocreacion = $request->input('fechanacimientocreacion');
                $idestadocivil = $request->input('idestadocivil');
                $idnivelestudio = $request->input('idnivelestudio');
                $celular_clientetexto0 = $request->input('celular0');

                $direccion = $request->input('direccion');
                $idubigeo = $request->input('idubigeo');
                $referencia_direccion = $request->input('referencia_direccion');
                $idcondicionviviendalocal = $request->input('idcondicionviviendalocal');
                $celular0 = $request->input('celular0');
                $vinculo0 = $request->input('vinculo0');
                $referencia0 = $request->input('referencia0');
              
                
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
            }
            elseif($request->idtipodocumento == 2){
                // DOMICILIO DE CLIENTE
                $rules['direccion'] = 'required';
                $rules['idubigeo'] = 'required';
                $rules['referencia_direccion'] = 'required';
                $rules['idcondicionviviendalocal'] = 'required';
                $rules['celular0'] = 'required';
                $rules['vinculo0'] = 'required';
                $rules['referencia0'] = 'required';
              
                $direccion = $request->input('direccion');
                $idubigeo = $request->input('idubigeo');
                $referencia_direccion = $request->input('referencia_direccion');
                $idcondicionviviendalocal = $request->input('idcondicionviviendalocal');
                $celular0 = $request->input('celular0');
                $vinculo0 = $request->input('vinculo0');
                $referencia0 = $request->input('referencia0');
                
                if( $request->idubigeo != null ){
                    $db_ubigeo = DB::table('ubigeo')->whereId($request->idubigeo)->first();
                    $db_idubigeo = $db_ubigeo ? $db_ubigeo->nombre : '';
                }
            }
            $idforma_ac_economica = 0;
            $idgiro_ac_economica = 0;
            $descripcion_ac_economica = '';

            $direccion_ac_economica = '';
            $idubigeo_ac_economica = 0;
            $referencia_ac_economica = '';
            $idlocalnegocio_ac_economica = 0;
          
            $db_idubigeo_ac_economica = '';
            $db_idlocalnegocio_ac_economica = '';
           
            if((($request->idtipodocumento==1 || $request->idtipodocumento==3) && $request->idfuenteingreso==1) || 
              ($request->idtipodocumento==2 && $request->idfuenteingreso==1)){
              
                // ACTIVIDAD ECONÓMICA DEL CLIENTE
                $rules['idforma_ac_economica'] = 'required';
                $rules['idgiro_ac_economica'] = 'required';
                $rules['descripcion_ac_economica'] = 'required';
              
                $idforma_ac_economica = $request->input('idforma_ac_economica');
                $idgiro_ac_economica = $request->input('idgiro_ac_economica');
                $descripcion_ac_economica = $request->input('descripcion_ac_economica');
              
                if($request->casanegocio!='on'){
                    $rules['direccion_ac_economica'] = 'required';
                    $rules['idubigeo_ac_economica'] = 'required';
                    $rules['referencia_ac_economica'] = 'required';
                    $rules['idlocalnegocio_ac_economica'] = 'required';
                  
                    $direccion_ac_economica = $request->input('direccion_ac_economica');
                    $idubigeo_ac_economica = $request->input('idubigeo_ac_economica');
                    $referencia_ac_economica = $request->input('referencia_ac_economica');
                    $idlocalnegocio_ac_economica = $request->input('idlocalnegocio_ac_economica');
                  
                    if( $request->idlocalnegocio_ac_economica != null ){
                        $db_condicionviviendalocal = DB::table('f_condicionviviendalocal')->whereId($request->idlocalnegocio_ac_economica)->first();
                        $db_idlocalnegocio_ac_economica = $db_condicionviviendalocal ? $db_condicionviviendalocal->nombre : '';
                    }
                    if( $request->idubigeo_ac_economica != null ){
                        $db_ubigeo_ac_economica = DB::table('ubigeo')->whereId($request->idubigeo_ac_economica)->first();
                        $db_idubigeo_ac_economica = $db_ubigeo_ac_economica ? $db_ubigeo_ac_economica->nombre : '';
                    }
                }
              
            }
          
            /*$idforma_negocio_pareja = 0;
            $idgiro_negocio_pareja = 0;
            $descripcion_negocio_pareja = '';
            $direccion_negocio_pareja = '';
            $idubigeo_negocio_pareja = 0;
            $idlocalnegocio_negocio_pareja = 0;
          
            if(($request->idtipoinformacion==2 && $request->idfuenteingreso==1 && $request->idocupacion_pareja==1) or ($request->idtipoinformacion==2 && $request->idocupacion_pareja==1)){
                
            }*/
          
          
            $idempresa_laboral_pareja = 0;
            $ruc_laboral_pareja = '';
            $razonsocial_laboral_pareja = '';
            $fechainicio_laboral_pareja = null;
            $antiguedad_laboral_pareja = '';
            $cargo_laboral_pareja = '';
            $area_laboral_pareja = '';
            $idtipocontrato_laboral_pareja = 0;
          
            $idforma_negocio_pareja = 0;
            $idgiro_negocio_pareja = 0;
            $descripcion_negocio_pareja = '';
            $idempresa_negocio_pareja = 0;
            $ruc_negocio_pareja = '';
            $razonsocial_negocio_pareja = '';
            $direccion_negocio_pareja = '';
            $idubigeo_negocio_pareja = 0;
            $referencia_negocio_pareja = '';
            $idlocalnegocio_negocio_pareja = 0;
          
            $db_idtipocontrato_laboral_pareja = '';
            $db_idforma_negocio_pareja = '';
            $db_idubigeo_negocio_pareja = '';
            $db_idlocalnegocio_negocio_pareja = '';
            $db_idgiro_negocio_pareja = '';
          
            /*$razonsocial_laboral_pareja = '';
            $antiguedad_laboral_pareja = '';
            $cargo_laboral_pareja = '';
            $idtipocontrato_laboral_pareja = 0;
          
            */
          
            //if(($request->idtipoinformacion==2 && $request->idocupacion_pareja==2) || ($request->idtipodocumento==2 && $request->idfuenteingreso==1 && $request->idtipoinformacion==2)){
                // CENTRO LABORAL DE: PAREJA/REPRESENTANTE LEG.
                /*$rules['razonsocial_laboral_pareja'] = 'required';
                $rules['antiguedad_laboral_pareja'] = 'required';
                $rules['cargo_laboral_pareja'] = 'required';
                $rules['idtipocontrato_laboral_pareja'] = 'required';*/
          //dd($request->idocupacion_pareja);
            if($request->idestadocivil==2 || $request->idestadocivil==4  or ($request->idtipoinformacion==2 && $request->idfuenteingreso==1)){
                if($request->idocupacion_pareja==2 or ($request->idtipoinformacion==2 && $request->idfuenteingreso==1) ){
                  
                    if($request->ruc_laboral_pareja!=''){
                        $rules['ruc_laboral_pareja'] = 'required|integer|numeric|digits:11';
                    }
                  
                    $ruc_laboral_pareja = $request->ruc_laboral_pareja!= null ? $request->ruc_laboral_pareja : '';
                    $razonsocial_laboral_pareja = $request->input('razonsocial_laboral_pareja') != null ? $request->input('razonsocial_laboral_pareja') : '';
                    $fechainicio_laboral_pareja = $request->fechainicio_laboral_pareja != null ? $request->fechainicio_laboral_pareja : null;
                    $antiguedad_laboral_pareja = $request->input('antiguedad_laboral_pareja') != null ? $request->input('antiguedad_laboral_pareja') : '';
                    $cargo_laboral_pareja = $request->input('cargo_laboral_pareja') != null ? $request->input('cargo_laboral_pareja') : '';
                    $area_laboral_pareja = $request->area_laboral_pareja != null ? $request->area_laboral_pareja : '';
                    $idtipocontrato_laboral_pareja = $request->input('idtipocontrato_laboral_pareja') != null ? $request->input('idtipocontrato_laboral_pareja') : 0;

                    if( $request->idtipocontrato_laboral_pareja != null ){
                        $db_contratolaboral_pareja = DB::table('f_contratolaboral')->whereId($request->idtipocontrato_laboral_pareja)->first();
                        $db_idtipocontrato_laboral_pareja = $db_contratolaboral_pareja ? $db_contratolaboral_pareja->nombre : '';
                    }
                }
                elseif($request->idocupacion_pareja==1 && $request->idtipoinformacion==2){
                  
                    // NEGOCIO DE: PAREJA/REPRESENTANTE LEG.
                    $rules['idforma_negocio_pareja'] = 'required';
                    $rules['idgiro_negocio_pareja'] = 'required';
                    $rules['descripcion_negocio_pareja'] = 'required';
                    $rules['direccion_negocio_pareja'] = 'required';
                    $rules['idubigeo_negocio_pareja'] = 'required';
                    $rules['idlocalnegocio_negocio_pareja'] = 'required';
                  
                    if($request->ruc_negocio_pareja!=''){
                        $rules['ruc_negocio_pareja'] = 'required|integer|numeric|digits:11';
                    }

                    $idforma_negocio_pareja = $request->input('idforma_negocio_pareja');
                    $idgiro_negocio_pareja = $request->input('idgiro_negocio_pareja');
                    $descripcion_negocio_pareja = $request->input('descripcion_negocio_pareja');
                    $ruc_negocio_pareja = $request->ruc_negocio_pareja != null ? $request->ruc_negocio_pareja : '';
                    $razonsocial_negocio_pareja = $request->razonsocial_negocio_pareja != null ? $request->razonsocial_negocio_pareja : '';
                    $direccion_negocio_pareja = $request->input('direccion_negocio_pareja');
                    $referencia_negocio_pareja = $request->referencia_negocio_pareja;
                    $idubigeo_negocio_pareja = $request->input('idubigeo_negocio_pareja');
                    $idlocalnegocio_negocio_pareja = $request->input('idlocalnegocio_negocio_pareja');

                    if( $request->idforma_negocio_pareja != null ){
                        $db_formactividadeconomica = DB::table('f_formaactividadeconomica')->whereId($request->idforma_negocio_pareja)->first();
                        $db_idforma_negocio_pareja = $db_formactividadeconomica ? $db_formactividadeconomica->nombre : '';
                    }

                    if( $request->idubigeo_negocio_pareja != null ){
                        $db_ubigeo_ac_economica_pareja = DB::table('ubigeo')->whereId($request->idubigeo_negocio_pareja)->first();
                        $db_idubigeo_negocio_pareja = $db_ubigeo_ac_economica_pareja ? $db_ubigeo_ac_economica_pareja->nombre : '';
                    }
                    if( $request->idlocalnegocio_negocio_pareja != null ){
                        $db_condicionviviendalocal_pareja = DB::table('f_condicionviviendalocal')->whereId($request->idlocalnegocio_negocio_pareja)->first();
                        $db_idlocalnegocio_negocio_pareja = $db_condicionviviendalocal_pareja ? $db_condicionviviendalocal_pareja->nombre : '';
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
                }
            }
            //}
          
            $dni_pareja = '';
            $nombres_pareja = '';
            $ap_paterno_pareja = '';
            $ap_materno_pareja = '';
            $idocupacion_pareja = 0;
          
            $profesion_pareja = '';
            $idnivelestudio_pareja = 0;
            $telefono_pareja = '[]';
          
            $db_idocupacion_pareja = '';
            $db_idnivelestudio_pareja = '';
          
            if($request->idestadocivil==2 || $request->idestadocivil==4){
                // DATOS DE PAREJA
                $rules['dni_pareja'] = 'required';
                $rules['nombres_pareja'] = 'required';
                $rules['ap_paterno_pareja'] = 'required';
                $rules['ap_materno_pareja'] = 'required';
                $rules['idocupacion_pareja'] = 'required';
              
                $dni_pareja = $request->input('dni_pareja');
                $nombres_pareja = $request->input('nombres_pareja');
                $ap_paterno_pareja = $request->input('ap_paterno_pareja');
                $ap_materno_pareja = $request->input('ap_materno_pareja');
                $idocupacion_pareja = $request->input('idocupacion_pareja');
          
                $profesion_pareja = $request->input('profesion_pareja');
                $idnivelestudio_pareja = $request->input('idnivelestudio_pareja');
                $telefono_pareja = $request->input('telefono_pareja');

                if( $request->idocupacion_pareja != null ){
                    $db_ocupacion = DB::table('f_ocupacion')->whereId($request->idocupacion_pareja)->first();
                    $db_idocupacion_pareja = $db_ocupacion ? $db_ocupacion->nombre : '';
                }
              
                if( $request->idnivelestudio_pareja != null ){
                    $db_nivelestudiopareja = DB::table('f_nivelestudio')->whereId($request->idnivelestudio_pareja)->first();
                    $db_idnivelestudio_pareja = $db_nivelestudiopareja ? $db_nivelestudiopareja->nombre : '';
                }
          
                if($request->dni_pareja!=''){
                    $rules['dni_pareja'] = 'required|integer|numeric|digits:8';
                }
            }
          
            if($request->ruc_laboral_cliente!=''){
                $rules['ruc_laboral_cliente'] = 'required|integer|numeric|digits:11';
            }
            $messages = [
                'idtipoinformacion.required' => 'El "Tipo de Información" es Obligatorio.',
                'idfuenteingreso.required' => 'El "Fuente de Ingreso" es Obligatorio.',
                'idtipopersona.required' => 'El "Tipo de Persona" es Obligatorio.',
                'idtipodocumento.required' => 'El "Tipo de Documento" es Obligatorio.',
              
                'dni.required' => 'El "DNI" es obligatorio.',
                'dni.integer' => 'El "DNI" debe ser número entero.',
                'dni.numeric' => 'El "DNI" debe ser Númerico.',
                'dni.digits' => 'El "DNI" debe ser de 8 Digitos.',
              
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'apellidopaterno.required' => 'El "Apellido Paterno" es Obligatorio.',
                'apellidomaterno.required' => 'El "Apellido Materno" es Obligatorio.',
              
                'ruc.required' => 'El "RUC" es obligatorio.',
                'ruc.integer' => 'El "RUC" debe ser número entero.',
                'ruc.numeric' => 'El "RUC" debe ser Númerico.',
                'ruc.digits' => 'El "RUC" debe ser de 11 Digitos.',
              
                'razonsocial.required' => 'La "Razón Social" es Obligatorio.',
                'fechanacimientocreacion.required' => 'La "Fecha Nac./Creación" es Obligatorio.',
                'documento_representantelegal.required' => 'El "Nro Documento Representante Legal" es Obligatorio.',
                'nombrecompelto_representantelegal.required' => 'El "Nombre Completo Representante Legal" es Obligatorio.',
                //'celular-clientetexto0.required' => 'El "Telf./Celular" es Obligatorio.',
              
                'carnetextranjeria.required' => 'El "Carnet Extranjería" es Obligatorio.',
                'nombre_carnetextranjeria.required' => 'El "Nombre" es Obligatorio.',
                'apellidopaterno_carnetextranjeria.required' => 'El "Apellido Paterno" es Obligatorio.',
                'apellidomaterno_carnetextranjeria.required' => 'El "Apellido Materno" es Obligatorio.',
              
                'idgenero.required' => 'El "Genero" es Obligatorio.',
                'idestadocivil.required' => 'El "Estado Civil" es Obligatorio.',
                'idnivelestudio.required' => 'El "Nivel de Estudios" es Obligatorio.',
                //'celular-clientetexto0.required' => 'El "Telf./Celular" es Obligatorio.',
              
                'direccion.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo.required' => 'El "Distrito – Provincia – Departamento" es Obligatorio.',
                'referencia_direccion.required' => 'La "Referencia Ubicación" es Obligatorio.',
                'idcondicionviviendalocal.required' => 'La "Condición de Vivienda/Local" es Obligatorio.',
                'celular0.required' => 'El "Telf./Celular" es Obligatorio.',
                'vinculo0.required' => 'Los "Nombres y Apellidos" es Obligatorio.',
                'referencia0.required' => 'El "Vinculo Familiar/Personas/Otros" es Obligatorio.',
              
                'idforma_ac_economica.required' => 'La "Forma de Activ. Econom" es Obligatorio.',
                'idgiro_ac_economica.required' => 'El "Giro Económico" es Obligatorio.',
                'descripcion_ac_economica.required' => 'La "Descripción" es Obligatorio.',
                'direccion_ac_economica.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo_ac_economica.required' => 'El "Distrito – Provincia – Departamento" es Obligatorio.',
                'referencia_ac_economica.required' => 'La "Referencia de Ubicación" es Obligatorio.',
                'idlocalnegocio_ac_economica.required' => 'El "Local Negocio" es Obligatorio.',
                    
                'dni_pareja.required' => 'El "PAREJA - DNI/CE" es Obligatorio.',
                'dni_pareja.integer' => 'El "PAREJA - DNI/CE" debe ser número entero.',
                'dni_pareja.numeric' => 'El "PAREJA - DNI/CE" debe ser Númerico.',
                'dni_pareja.digits' => 'El "PAREJA - DNI/CE" debe ser de 8 Digitos.',
              
                'nombres_pareja.required' => 'El "PAREJA - Nombre" es Obligatorio.',
                'ap_paterno_pareja.required' => 'El "PAREJA - Apellido Paterno" es Obligatorio.',
                'ap_materno_pareja.required' => 'El "PAREJA - Apellido Materno" es Obligatorio.',
                'idocupacion_pareja.required' => 'La "PAREJA - Ocupación" es Obligatorio.',
                  
                'idforma_negocio_pareja.required' => 'La "PAREJA - Forma de Activ. Econom" es Obligatorio.',
                'idgiro_negocio_pareja.required' => 'El "PAREJA - Giro Económico" es Obligatorio.',
                'descripcion_negocio_pareja.required' => 'PAREJA - La "Descripción" es Obligatorio.',
                'direccion_negocio_pareja.required' => 'La "PAREJA - Dirección" es Obligatorio.',
                'idubigeo_negocio_pareja.required' => 'El "PAREJA - Distrito – Provincia – Departamento" es Obligatorio.',
                'idlocalnegocio_negocio_pareja.required' => 'El "PAREJA - Negocio" es Obligatorio.',
               
                'razonsocial_laboral_pareja.required' => 'El "PAREJA - Nombre: Persona Natural/Persona Jurídica" es Obligatorio.',
                'antiguedad_laboral_pareja.required' => 'La "PAREJA - Antiguedad (en años)" es Obligatorio.',
                'cargo_laboral_pareja.required' => 'El "PAREJA - Cargo" es Obligatorio.',
                'idtipocontrato_laboral_pareja.required' => 'El "PAREJA - Contrato Laboral" es Obligatorio.',
              
                'ruc_laboral_cliente.required' => 'El "RUC" es obligatorio.',
                'ruc_laboral_cliente.integer' => 'El "RUC" debe ser número entero.',
                'ruc_laboral_cliente.numeric' => 'El "RUC" debe ser Númerico.',
                'ruc_laboral_cliente.digits' => 'El "RUC" debe ser de 11 Digitos.',
              
                'ruc_laboral_pareja.required' => 'El "RUC DE PAREJA/REPRESENTANTE LEG." es obligatorio.',
                'ruc_laboral_pareja.integer' => 'El "RUC DE PAREJA/REPRESENTANTE LEG." debe ser número entero.',
                'ruc_laboral_pareja.numeric' => 'El "RUC DE PAREJA/REPRESENTANTE LEG." debe ser Númerico.',
                'ruc_laboral_pareja.digits' => 'El "RUC DE PAREJA/REPRESENTANTE LEG." debe ser de 11 Digitos.',
              
                'ruc_negocio_pareja.required' => 'El "RUC DE NEGOCIO DE: PAREJA" es obligatorio.',
                'ruc_negocio_pareja.integer' => 'El "RUC DE NEGOCIO DE: PAREJA" debe ser número entero.',
                'ruc_negocio_pareja.numeric' => 'El "RUC DE NEGOCIO DE: PAREJA" debe ser Númerico.',
                'ruc_negocio_pareja.digits' => 'El "RUC DE NEGOCIO DE: PAREJA" debe ser de 11 Digitos.',
            ];
            $this->validate($request,$rules,$messages);
            // FIN NUEVAS VALIDACIONES
          
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

            $db_idtipoinformacion = '';
            $db_idtipodocumento = '';
            $db_idcondicionviviendalocal = '';
            $db_idfuenteingreso = '';
            $db_idforma_ac_economica = '';
            $db_idgiro_ac_economica = '';
            $db_idtipocontrato_laboral_cliente = '';
          
            if( $request->idtipoinformacion != null ){
                $db_tipoinformacion = DB::table('f_tipoinformacion')->whereId($request->idtipoinformacion)->first();
                $db_idtipoinformacion = $db_tipoinformacion ? $db_tipoinformacion->nombre : '';
            }
            if( $request->idtipodocumento != null ){
                $db_tipodocumento = DB::table('s_tipodocumento')->whereId($request->idtipodocumento)->first();
                $db_idtipodocumento = $db_tipodocumento ? $db_tipodocumento->nombre : '';
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
            if( $request->idtipocontrato_laboral_cliente != null ){
                $db_contratolaboral = DB::table('f_contratolaboral')->whereId($request->idtipocontrato_laboral_cliente)->first();
                $db_idtipocontrato_laboral_cliente = $db_contratolaboral ? $db_contratolaboral->nombre : '';
            }
          
            DB::table('users')->whereId($idusuario)->update([
                'fechamodificacioncliente'=> Carbon::now(),
                'idtipopersona'       => $request->idtipopersona,
                //'codigo'              => 'CL'.str_pad($idusuario, 8, "0", STR_PAD_LEFT),
                'nombre'              => $nombre,
                'apellidopaterno'     => $apellidopaterno,
                'apellidomaterno'     => $apellidomaterno,
                'razonsocial'         => $razonsocial,
                'nombrecompleto'      => $nombrecompleto,
                'identificacion'      => $identificacion,
                'email'               => '',
                'imagen'              => '',
                'numerotelefono'      => $celular_clientetexto0,
                'direccion'           => $direccion,
                'idubigeo'            => $idubigeo,
                'idtienda'            => $idtienda,
                'idgenero'            => $idgenero,
                'fechanacimiento'     => $fechanacimientocreacion,
                'idestadocivil'       => $idestadocivil,
                'idnivelestudio'      => $idnivelestudio,
                'referencia'          => $request->referencia,

                'db_idgenero'         => $db_idgenero,
                'db_idestadocivil'    => $db_idestadocivil,
                'db_idnivelestudio'   => $db_idnivelestudio,
                'db_idubigeo'         => $db_idubigeo,
                'idcliente_modificacion'         => $request->iduser_modificacion,
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

                'idpareja'                  => 0,
                'dni_pareja'                => $dni_pareja,
                'nombres_pareja'            => $nombres_pareja,
                'ap_paterno_pareja'         => $ap_paterno_pareja,
                'ap_materno_pareja'         => $ap_materno_pareja,
                'nombrecompleto_pareja'     => $nombres_pareja!=''?$ap_paterno_pareja.' '.$ap_materno_pareja.', '.$nombres_pareja:$nombres_pareja,
                
                'idocupacion_pareja'    => $idocupacion_pareja,
                'profesion_pareja'      => $profesion_pareja,
                'idnivelestudio_pareja' => $idnivelestudio_pareja,
                'telefono_pareja'       => $telefono_pareja,
              
                'referencia_direccion'  => $referencia_direccion,
                'suministro_electrocentro' => $request->suministro_electrocentro,
                'idcondicionviviendalocal' => $idcondicionviviendalocal,
                'referencia_cliente'    => $request->referencia_cliente,

                'idfuenteingreso'           => $request->idfuenteingreso != null ? $request->idfuenteingreso : 0,
                'idforma_ac_economica'      => $idforma_ac_economica,
                'idgiro_ac_economica'       => $idgiro_ac_economica,
                'descripcion_ac_economica'  => $descripcion_ac_economica,
                'ruc_ac_economica'          => $request->ruc_ac_economica != null ? $request->ruc_ac_economica : '',
                'razonsocial_ac_economica'  => $request->razonsocial_ac_economica != null ? $request->razonsocial_ac_economica : '',
                'casanegocio'               => $estado_casa_negocio,
                'direccion_ac_economica'    => $direccion_ac_economica,
                'idubigeo_ac_economica'     => $idubigeo_ac_economica,
                'referencia_ac_economica'   => $referencia_ac_economica,
                'idlocalnegocio_ac_economica' => $idlocalnegocio_ac_economica,

                'ruc_laboral_cliente'           => $request->ruc_laboral_cliente != null ? $request->ruc_laboral_cliente : '',
                'razonsocial_laboral_cliente'   => $request->razonsocial_laboral_cliente != null ? $request->razonsocial_laboral_cliente : '',
                'fechainicio_laboral_cliente'   => $request->fechainicio_laboral_cliente != null ? $request->fechainicio_laboral_cliente : null,
                'antiguedad_laboral_cliente'    => $request->antiguedad_laboral_cliente != null ? $request->antiguedad_laboral_cliente : '',
                'cargo_laboral_cliente'         => $request->cargo_laboral_cliente != null ? $request->cargo_laboral_cliente : '',
                'area_laboral_cliente'          => $request->area_laboral_cliente != null ? $request->area_laboral_cliente : '',
                'idtipocontrato_laboral_cliente' => $request->idtipocontrato_laboral_cliente != null ? $request->idtipocontrato_laboral_cliente : 0,

                'ruc_laboral_pareja'           => $ruc_laboral_pareja,
                'razonsocial_laboral_pareja'   => $razonsocial_laboral_pareja,

                'fechainicio_laboral_pareja'   => $fechainicio_laboral_pareja,
                'antiguedad_laboral_pareja'    => $antiguedad_laboral_pareja,
                'cargo_laboral_pareja'         => $cargo_laboral_pareja,
                'area_laboral_pareja'          => $area_laboral_pareja,
                'idtipocontrato_laboral_pareja' => $idtipocontrato_laboral_pareja,

                'idforma_negocio_pareja'      => $idforma_negocio_pareja,
                'idgiro_negocio_pareja'       => $idgiro_negocio_pareja,
                'descripcion_negocio_pareja'  => $descripcion_negocio_pareja,
                'idempresa_negocio_pareja'    => $request->idempresa_negocio_pareja != null ? $request->idempresa_negocio_pareja : 0,
                'ruc_negocio_pareja'          => $ruc_negocio_pareja,
                'razonsocial_negocio_pareja'  => $razonsocial_negocio_pareja,
                'direccion_negocio_pareja'    => $direccion_negocio_pareja,
                'idubigeo_negocio_pareja'     => $idubigeo_negocio_pareja,
                'referencia_negocio_pareja'   => $referencia_negocio_pareja,
                'idlocalnegocio_negocio_pareja' => $idlocalnegocio_negocio_pareja,

                'db_idtipodocumento'                => $db_idtipodocumento,
                'db_idtipoinformacion'              => $db_idtipoinformacion,
                'db_idocupacion_pareja'             => $db_idocupacion_pareja,
                'db_idnivelestudio_pareja'          => $db_idnivelestudio_pareja,
                'db_idcondicionviviendalocal'       => $db_idcondicionviviendalocal,
                'db_idfuenteingreso'                => $db_idfuenteingreso,
                'db_idforma_ac_economica'           => $db_idforma_ac_economica,
                'db_idgiro_ac_economica'            => $db_idgiro_ac_economica,
                'db_idubigeo_ac_economica'          => $db_idubigeo_ac_economica,
                'db_idlocalnegocio_ac_economica'    => $db_idlocalnegocio_ac_economica,
                'db_idtipocontrato_laboral_cliente' => $db_idtipocontrato_laboral_cliente,
                'db_idtipocontrato_laboral_pareja'  => $db_idtipocontrato_laboral_pareja,
                'db_idforma_negocio_pareja'         => $db_idforma_negocio_pareja,
                'db_idgiro_negocio_pareja'          => $db_idgiro_negocio_pareja,
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
        
        elseif ($request->input('view') == 'editar_ubicacion') {

        
                $rules = [
                    'domicilio_mapa_latitud' => 'required',
                    'domicilio_mapa_longitud' => 'required',
                ];
      
            $messages = [
                    'domicilio_mapa_latitud.required' => 'La "Ubicación" es Obligatorio.',
                    'domicilio_mapa_longitud.required' => 'La "ApUbicaciónllidos" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);


            DB::table('users')->whereId($idusuario)->update([
                'mapa_latitud' => $request->domicilio_mapa_latitud,
                'mapa_longitud' => $request->domicilio_mapa_longitud,
            ]);
          
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
