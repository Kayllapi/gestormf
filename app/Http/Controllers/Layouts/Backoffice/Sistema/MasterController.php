<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Hash;
use Auth;
use Carbon\Carbon;

class MasterController extends Controller
{
    public function index(Request $request, $idtienda)
    {         
      
        $idtienda = user_permiso()->idtienda;

        $tienda = DB::table('tienda')->whereId($idtienda)->first();
  
        $usuario = DB::table('users')
            ->join('users_permiso','users_permiso.idusers','users.id')
            ->join('permiso','permiso.id','users_permiso.idpermiso')
            ->where('users.id',Auth::user()->id)
            ->where('users_permiso.idtienda',$idtienda)
            ->where('users_permiso.idsession',2)
            ->select('users.*','permiso.nombre as permiso')
            ->limit(1)
            ->first();
 
        $imagenusuario      = '';
        $imagenusuario_ruta = '/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$usuario->imagen;
        if(file_exists(getcwd().$imagenusuario_ruta) AND $usuario->imagen!=''){
            $imagenusuario = url($imagenusuario_ruta);
        }
      
        return view(sistema_view().'/master',[
            'tienda'        => $tienda,
            'usuario'       => $usuario,
            'imagenusuario' => $imagenusuario,
        ]);
    }

    public function create(Request $request, $idtienda)
    {
        $tienda = DB::table('tienda')->whereId(user_permiso()->idtienda)->first();
        
        // $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->input('view') == 'inicio') {
            
            return view(sistema_view().'/inicio/index',[
                'tienda' => $tienda,
            ]);
            
        }
        elseif($request->input('view') == 'modal_formapago') {
            
            return view('app/nuevosistema/modal_formapago',[
                'tienda' => $tienda,
            ]);
            
        }
        elseif($request->input('view') == 'forma_pago'){

            return view('app/nuevosistema/forma_pago',[
                'tienda' => $tienda,
            ]);

        }
    }
    
    public function store(Request $request)
    {
        $tienda = DB::table('tienda')->whereId(Auth::user()->idtienda)->first();
      
        if($request->input('view') == 'modal_formapago') {
            $rules = [
                'idformapago' => 'required',
            ];
          
            $messages = [];
            if($request->idformapago==1){
                
                for($i=0;$i<count($request->formapago_seleccionar);$i++){
                    $num = $request->formapago_seleccionar[$i];
                  
                    if($request->input('formapago_tipopago'.$num)==''){
                        $rules = array_merge($rules,[
                            'formapago_tipopago'.$num => 'required'
                        ]);
                        $messages = array_merge($messages,[
                            'formapago_tipopago'.$num.'.required' => 'El "Tipo de Pago" es Obligatorio.',
                        ]);
                    }
                  
                    if($request->input('formapago_tipopago'.$num)==1){
                        if($request->input('formapago_efectivo_montoefectivo'.$num)==''){
                            $rules = array_merge($rules,[
                                'formapago_efectivo_montoefectivo'.$num => 'required'
                            ]);
                            $messages = array_merge($messages,[
                                'formapago_efectivo_montoefectivo'.$num.'.required' => 'El "Monto en Efectivo" es Obligatorio.',
                            ]);
                        }
                    }
                    elseif($request->input('formapago_tipopago'.$num)==2){
                        if($request->input('formapago_deposito_idcuentabancaria'.$num)==''){
                            $rules = array_merge($rules,[
                                'formapago_deposito_idcuentabancaria'.$num => 'required'
                            ]);
                            $messages = array_merge($messages,[
                                'formapago_deposito_idcuentabancaria'.$num.'.required' => 'La "Cuenta Bancaria" es Obligatorio.',
                            ]);
                        }
                        if($request->input('formapago_deposito_numerooperacion'.$num)==''){
                            $rules = array_merge($rules,[
                                'formapago_deposito_numerooperacion'.$num => 'required'
                            ]);
                            $messages = array_merge($messages,[
                                'formapago_deposito_numerooperacion'.$num.'.required' => 'El "Número de Operación" es Obligatorio.',
                            ]);
                        }
                        if($request->input('formapago_deposito_montodeposito'.$num)==''){
                            $rules = array_merge($rules,[
                                'formapago_deposito_montodeposito'.$num => 'required'
                            ]);
                            $messages = array_merge($messages,[
                                'formapago_deposito_montodeposito'.$num.'.required' => 'El "Monto en Depósito" es Obligatorio.',
                            ]);
                        }
                        // if($request->input('formapago_deposito_voucher'.$num)==''){
                        //     $rules = array_merge($rules,[
                        //         'formapago_deposito_voucher'.$num => 'required'
                        //     ]);
                        //     $messages = array_merge($messages,[
                        //         'formapago_deposito_voucher'.$num.'.required' => 'El "Voucher" es Obligatorio.',
                        //     ]);
                        // }
                    }   
                }
              
              if($request->formapago_contado_pagado != $request->formapago_totalpago){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El "Total Pagado" debe ser igual al "Total a Pagar".',
                  ]);
              }
            }
            elseif($request->idformapago==2){
                $rules = array_merge($rules,[
                    'formapago_credito_fechainicio' => 'required',
                    'formapago_credito_ultimafecha' => 'required',
                ]);
            }
          
            $messages = array_merge($messages,[
                'idformapago.required'                    => 'La "Forma de Pago" es Obligatorio.',
                'formapago_credito_fechainicio.required'  => 'La "Fecha inicio" es Obligatorio.',
                'formapago_credito_ultimafecha.required'  => 'La "Última fecha" es Obligatorio.',
            ]);
          
            $this->validate($request,$rules,$messages);
          
          
            
          
            $monto_efectivo = 0;
            $monto_deposito = 0;
            $idformapagodetalle = 0;
            if( $request->idformapago == 1 ){
              for($i=0;$i<count($request->formapago_seleccionar);$i++){
                $num = $request->formapago_seleccionar[$i];
                $monto_pago_efectivo = $request->idformapago == 1 ? $request->input('formapago_efectivo_montoefectivo'.$num) : 0 ;
                if($request->input('formapago_tipopago'.$num)==1){
                    $idformapagodetalle = DB::table('s_formapagodetalle')->insertGetId([
                        'fecharegistro' => Carbon::now(),
                        'numerocuenta' => '',
                        'numerooperacion' => '',
                        'banco' => '',
                        'fecha' => '',
                        'hora' => '',
                        'monto' => $monto_pago_efectivo,
                        'voucher' => '',
                        's_idcuentabancaria' => 0,
                        's_idprestamo_cobranza' => 0,
                        'idventacobranza' => 0,
                        'idmoneda' => $request->formapago_idmoneda,
                        'idtienda' => $tienda->id,
                        'formapago_credito_fechainicio' => Carbon::now(),
                        'formapago_credito_ultimafecha' => Carbon::now(),
                        'idestado' => 1,
                    ]);
                    $monto_efectivo = $monto_efectivo+$monto_pago_efectivo;
                }
                elseif($request->input('formapago_tipopago'.$num)==2){
                    // $imagen = uploadfile('','',$request->file('formapago_deposito_voucher'.$num),'/public/backoffice/tienda/'.$tienda->id.'/formapago/');
                    $idformapagodetalle = DB::table('s_formapagodetalle')->insertGetId([
                        'fecharegistro' => Carbon::now(),
                        'numerocuenta' => $request->input('formapago_deposito_numerocuenta'.$num)!=''?$request->input('formapago_deposito_numerocuenta'.$num):'',
                        'numerooperacion' => $request->input('formapago_deposito_numerooperacion'.$num)!=''?$request->input('formapago_deposito_numerooperacion'.$num):'',
                        'banco' => $request->input('formapago_deposito_banco'.$num)!=''?$request->input('formapago_deposito_banco'.$num):'',
                        'fecha' => '',
                        'hora' => '',
                        'monto' => $request->input('formapago_deposito_montodeposito'.$num),
                        'voucher' => '',
                        // 'voucher' => $imagen,
                        's_idcuentabancaria' => $request->input('formapago_deposito_idcuentabancaria'.$num)!=''?$request->input('formapago_deposito_idcuentabancaria'.$num):0,
                        's_idprestamo_cobranza' => 0,
                        'idventacobranza' => 0,
                        'idmoneda' => $request->formapago_idmoneda,
                        'idtienda' => $tienda->id,
                        'formapago_credito_fechainicio' => Carbon::now(),
                        'formapago_credito_ultimafecha' => Carbon::now(),
                        'idestado' => 1,
                    ]);
                    $monto_deposito = $monto_deposito+$request->input('formapago_deposito_montodeposito'.$num);
                }

                    
              }
            }
            else if( $request->idformapago == 2 ){
              $idformapagodetalle = DB::table('s_formapagodetalle')->insertGetId([
                                      'fecharegistro' => Carbon::now(),
                                      'numerocuenta' => '',
                                      'numerooperacion' => '',
                                      'banco' => '',
                                      'fecha' => '',
                                      'hora' => '',
                                      'monto' => 0,
                                      'voucher' => '',
                                      's_idcuentabancaria' => 0,
                                      's_idprestamo_cobranza' => 0,
                                      'idventacobranza' => 0,
                                      'idmoneda' => $request->formapago_idmoneda,
                                      'idtienda' => $tienda->id,
                                      'formapago_credito_fechainicio' => $request->formapago_credito_fechainicio,
                                      'formapago_credito_ultimafecha' => $request->formapago_credito_ultimafecha,
                                      'idestado' => 1,
                                  ]);
            }
            
          
            return response()->json([
                'resultado'           => 'CORRECTO',
                'mensaje'             => 'Se ha registrado correctamente.',
                'idformapago'         => $request->idformapago,
                'idformapagodetalle'  => $idformapagodetalle,
                'monto_efectivo'      => $monto_efectivo,
                'monto_deposito'      => $monto_deposito,
            ]);
        }
        /*if($request->input('view') == 'registrar_accesorapido') {
          
            DB::table('s_accesorapido')->insert([
                'idusers' => Auth::user()->id,
                'idmodulo' => $request->idmodulo,
                'idtienda' => Auth::user()->idtienda,
                'idestado' => 1,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }*/
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showbuscaridentificacion'){
            return consultaDniRuc($request->input('buscar_identificacion'), $request->input('tipo_persona'));
        }
        elseif($id == 'show_agenciapermiso'){
            
              $tienda_permiso = DB::table('users_permiso')
                                  ->join('permiso','permiso.id','users_permiso.idpermiso')
                                  ->join('tienda','tienda.id','users_permiso.idtienda')
                                  ->where('users_permiso.idusers',Auth::user()->id)
                                  ->where('tienda.id',$request->idagenciapermiso)
                                  ->select(
                                    'users_permiso.*',
                                    'permiso.nombre as nombrepermiso',
                                    'tienda.nombreagencia as nombretienda',
                                  )
                                  ->get();
            $html = '';
            foreach($tienda_permiso as $val_permiso){
                $html = $html.'<li>
                  <a href="javascript:;" class="dropdown-item" 
                    onclick="cambiar_tienda('.$val_permiso->id.','.$val_permiso->idtienda.')"
                    '.($val_permiso->idsession == 2 ? 'style="background-color: #fdc36f;color: #000;font-weight: bold;"':'').' >
                    '.$val_permiso->nombrepermiso.'
                  </a>
                </li>';
            }
          
            return $html;
        }
        elseif($id == 'show_cambiarsucursal'){
            
            DB::table('users_permiso')
                ->where('idusers',Auth::user()->id)
                ->update([
                            'idsession' => 1
                        ]);
            DB::table('users_permiso')
                ->whereId($request->input('idpermiso'))
                ->update([
                            'idsession' => 2
                        ]);

            // return redirect('backoffice/inicio');

            // DB::table('users')->whereId(Auth::user()->id)->update([
            //     'idtienda' => $request->input('idtienda'),
            // ]);
        }
        elseif($id == 'show_actualizartabla'){
            if($request->tabla=='agencia'){ json_agencia($idtienda); }
            elseif($request->tabla=='sucursal'){ json_sucursal($idtienda); }
            elseif($request->tabla=='usuario'){ json_usuario($idtienda); }
            elseif($request->tabla=='usuarioacceso'){ json_usuarioacceso($idtienda); }
            elseif($request->tabla=='categoria'){ json_categoria($idtienda); }
            elseif($request->tabla=='marca'){ json_marca($idtienda); }
            elseif($request->tabla=='producto'){ json_producto($idtienda); }
            //elseif($request->tabla=='productomovimiento'){ json_productomovimiento($idtienda,Auth::user()->idsucursal); }
            //elseif($request->tabla=='productotransferencia'){ json_productotransferencia($idtienda,Auth::user()->idsucursal); }
            elseif($request->tabla=='caja'){ json_caja($idtienda); }
            elseif($request->tabla=='cuentabancaria'){ json_cuentabancaria($idtienda); }
            //elseif($request->tabla=='cajaapertura'){ json_cajaapertura($idtienda,Auth::user()->idsucursal); }
            elseif($request->tabla=='movimiento'){ json_movimiento($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            //elseif($request->tabla=='ordenservicio'){ json_ordenservicio($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            //elseif($request->tabla=='preprensa'){ json_preprensa($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            //elseif($request->tabla=='prensa'){ json_prensa($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            //elseif($request->tabla=='postprensa'){ json_postprensa($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            //elseif($request->tabla=='cotizacion'){ json_cotizacion($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            elseif($request->tabla=='compra'){ json_compra($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            elseif($request->tabla=='compradevolucion'){ json_compradevolucion($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            elseif($request->tabla=='venta'){ json_venta($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            elseif($request->tabla=='ventacredito'){ json_ventacredito($idtienda,Auth::user()->idsucursal); }
            // elseif($request->tabla=='ventacredito'){ json_cobranzacredito($idtienda,Auth::user()->idsucursal); }
            
            elseif($request->tabla=='ventadevolucion'){ json_ventadevolucion($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
          
            //elseif($request->tabla=='facturacionboletafactura'){ json_facturacionboletafactura($idtienda,Auth::user()->idsucursal); }
            // elseif($request->tabla=='facturacionnotacredito'){ json_facturacionnotacredito($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            // elseif($request->tabla=='facturacionnotadebito'){ json_facturacionnotadebito($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            // elseif($request->tabla=='facturacionguiaremision'){ json_facturacionguiaremision($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            // elseif($request->tabla=='facturacionresumendiario'){ json_facturacionresumendiario($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
            // elseif($request->tabla=='facturacioncomunicacionbaja'){ json_facturacioncomunicacionbaja($idtienda,Auth::user()->idsucursal,Auth::user()->id); }
        }
        elseif($id == 'show_aperturacaja'){
            $moneda_soles = DB::table('s_moneda')->whereId(1)->first();
            $moneda_dolares = DB::table('s_moneda')->whereId(2)->first();
            $apertura = sistema_apertura([
                'idtienda'          => Auth::user()->idtienda,
                'idsucursal'        => Auth::user()->idsucursal,
                'idusersrecepcion'  => Auth::user()->id,
            ]);
            $html_apertura = '';
            if($apertura['resultado']=='PROCESO'){
                $html_apertura = '<span class="badge rounded-pill bg-Secondary">Apertura en Proceso</span>';
            }elseif($apertura['resultado']=='PENDIENTE'){
                $html_apertura = '<span class="badge rounded-pill bg-primary">Apertura Pendiente</span>';
            }elseif($apertura['resultado']=='ABIERTO'){
                $total_soles = sistema_apertura_efectivo([
                    'idtienda'    => Auth::user()->idtienda,
                    'idsucursal'  => Auth::user()->idsucursal,
                    'idapertura'  => $apertura['apertura']->id,
                    'idmoneda'    => 1,
                ]);
                $total_dolares = sistema_apertura_efectivo([
                    'idtienda'    => Auth::user()->idtienda,
                    'idsucursal'  => Auth::user()->idsucursal,
                    'idapertura'  => $apertura['apertura']->id,
                    'idmoneda'    => 2,
                ]);
                $html_apertura = '<span class="badge rounded-pill bg-success">'.
                $apertura['apertura']->cajanombre.' ('.$moneda_soles->simbolo.' '.$total_soles['total'].' - '.$moneda_dolares->simbolo.' '.$total_dolares['total'].')</span>';
            }else{
                $html_apertura = '<span class="badge rounded-pill bg-dark">Caja Inactiva</span>';
            }
          
            return $html_apertura;
        }
        /*elseif($id == 'show_mostrarsucursales'){
     
            $sucursales = DB::table('s_sucursal')
              ->where('s_sucursal.idtienda',Auth::user()->idtienda)
              ->where('s_sucursal.idestado',1)
              ->orderBy('s_sucursal.nombre','asc')
              ->get();

            $html_sucursales = '';
            if(count($sucursales)>0){
                $class = 'sistema-nav-item';
                if(Auth::user()->idsucursal==0 or Auth::user()->idsucursal==null){
                    $class = 'sistema-nav-item-active';     
                }
                $html_sucursales = '<li><hr class="dropdown-divider"></li>
                    <li class="nav-item '.$class.'">
                    <a class="nav-link" href="javascript:;" onclick="cambiar_sucursal(0)">PRINCIPAL</a></li>';
                foreach($sucursales as $value){
                    $cantidad_modulos = DB::table('modulo')
                        ->join('usersrolesmodulo','usersrolesmodulo.idmodulo','modulo.id')
                        ->where('usersrolesmodulo.idusers',Auth::user()->id)
                        ->where('usersrolesmodulo.idsucursal',$value->id)
                        ->where('modulo.idestado',1)
                        ->count();
                    if($cantidad_modulos>0){
                        $class = 'sistema-nav-item';
                        if($value->id == Auth::user()->idsucursal){
                            $class = 'sistema-nav-item-active';     
                        }
                        $html_sucursales = $html_sucursales.'<li class="nav-item '.$class.'">
                            <a class="nav-link" href="javascript:;" onclick="cambiar_sucursal('.$value->id.')">'.$value->nombre.'</a></li>';
                    }
                }
                $html_sucursales = $html_sucursales.'<li><hr class="dropdown-divider"></li>';
            }
          
            return [
                'sucursales' => $html_sucursales
            ];
        }*/
    }
  
    public function edit(Request $request, $id)
    {
        $tienda = DB::table('tienda')->whereId(Auth::user()->idtienda)->first();
        if($request->input('view') == 'editarperfil') {
            
            $ubigeos = DB::table('ubigeo')->get();
            $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
            return view(sistema_view().'/inicio/editperfil',[
              'tienda' => $tienda,
              'ubigeos' => $ubigeos,
              'usuario' => $usuario
            ]);
        }
        else if( $request->view == 'editarpassword' ){
            $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
            return view(sistema_view().'/inicio/editpassword',[
              'tienda' => $tienda,
              'usuario' => $usuario
            ]);
        }
    }
  
    public function update(Request $request, $idtienda, $idusuario)
    {
        if($request->input('view') == 'editarperfil'){
            $usuario = DB::table('users')
                        ->where('id','<>',$idusuario)
                        ->where('idtienda',$idtienda)
                        ->where('idestado',1)
                        ->first();

            // $imagen = uploadfile($usuario->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('users')->whereId($idusuario)->update([
                'identificacion'  => $request->identificacion,
                'nombre'          => $request->nombre,
                'apellidopaterno' => $request->apellidopaterno,
                'apellidomaterno' => $request->apellidomaterno,
                'nombrecompleto'  => $request->apellidopaterno.' '.$request->apellidomaterno.', '.$request->nombre,
                'numerotelefono'  => $request->numerotelefono!='' ? $request->numerotelefono : '',
                'email'           => $request->email!='' ? $request->email : '',
                'idubigeo'        => $request->idubigeo!=null?$request->idubigeo:0,
                'direccion'       => $request->direccion!=null?$request->direccion:''
            ]);
            
            json_usuario($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        else if($request->input('view') == 'editarpassword'){

            $rules = [
                'antpassword' => 'required',
                'password' => 'required|string|min:3|confirmed',
                'password_confirmation' => 'required|required_with:passwordcsame:password|string|min:3',
            ];
            $messages = [
                    'antpassword.required' => 'La "Contraseña Actual" es Obligatorio.',
                    'password.required' => 'La "Nueva Contraseña" es Obligatorio.',
                    'password_confirmation.required' => 'El "Confirmar Nueva Contraseña" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            $usuario = DB::table('users')
                        ->where('id','<>',$idusuario)
                        ->where('idtienda',$idtienda)
                        ->where('clave',$request->input('antpassword'))
                        ->where('idestado',1)
                        ->first();

            if(!$usuario){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'la "Contraseña Actual" no es correcta.'
                ]);
            }
            DB::table('users')
                    ->whereId($idusuario)
                    ->where('idtienda',$idtienda)
                    ->update([
								'clave' => $request->input('password'),
                                'password' => Hash::make($request->input('password')),
						    ]);
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }
    }

    public function destroy($id)
    {
        //
    }
}
