<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use Image;

class TiendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        //$request->user()->authorizeRoles($request->path());

        $tiendas = DB::table('tienda')
          ->where('tienda.nombre','LIKE','%'.$request->input('searchtienda').'%')
          ->orderBy('tienda.id','desc')
          ->paginate(10);
      
        return view('layouts/backoffice/tienda/index',[
            'tiendas' => $tiendas,
            'idusers' => Auth::user()->id
        ]);
    }

    public function create(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
        
        $ubigeos = DB::table('ubigeo')->get();
        $categorias = DB::table('categoria')->whereIn('id',[8,12,13,21,24,30])->get();
        $codigotelefonicos = DB::table('codigotelefonico')->get();
        return view('layouts/backoffice/tienda/create',[
            'ubigeos' => $ubigeos,
            'categorias' => $categorias,
            'codigotelefonicos' => $codigotelefonicos
        ]);
    }

    public function store(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
       
        if($request->input('view')=='create') {
            $rules = [
              'nombre' => 'required',
              'link' => 'required|alpha_dash|unique:tienda',
              'idcategoria' => 'required',
              'idcodigotelefonico' => 'required',
              'numerotelefono' => 'required',
              'idubigeo' => 'required',
              'direccion' => 'required',
            ];
            $messages = [
              'idcategoria.required' => 'La "Categoria" es Obligatorio.',
              'link.required' => '',
              'link.alpha_dash' => '',
              'link.unique' => '',
              'nombre.required' => 'El "Nombre" es Obligatorio.',
              'idcodigotelefonico.required' => 'El "Código de Teléfono" es Obligatorio.',
              'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
              'idubigeo.required' => 'La "Ubicación (Ubigeo)" es Obligatorio.',
              'direccion.required' => 'La "Dirección" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            // portada
            /*if($request->input('imagenportadaant')!='') {
              $imagenportada = $request->input('imagenportadaant');
            }else{
              $imagenportada = '';
              if($request->file('imagenportada')!='') {
                if ($request->file('imagenportada')->isValid()) {
                    list($nombre,$ext) = explode(".", $request->file('imagenportada')->getClientOriginalName());
                    $imagenportada = Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$ext;
                }
              }
            }*/
          
            //logo
            /*if($request->input('imagenant')!='') {
              $imagen = $request->input('imagenant');
            }else{
              $imagen = '';
              if($request->file('imagen')!='') {
                if ($request->file('imagen')->isValid()) {
                    list($nombre,$ext) = explode(".", $request->file('imagen')->getClientOriginalName());
                    $imagen = Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$ext;
                }
              }
            }*/
          
            $idtienda = DB::table('tienda')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'link' => $request->input('link'),
				        'nombre' => $request->input('nombre'),
                'correo' => '',
                'numerotelefono' => $request->input('numerotelefono'),
                'direccion' => $request->input('direccion'),
                'referencia' => '',
                'paginaweb' => '',
                'imagen' => '',
                'imagenportada' => '',
                'contenido' => '',
                'mapa_ubicacion_lat' => '',
                'mapa_ubicacion_lng' => '',
                'calificacioncantidad' => 0,
                'calificacionpromedio' => '5',
                'dominio_personalizado' => '',
                'ecommerce_color' => '#008cea',
                'idcodigotelefonico' => $request->input('idcodigotelefonico'),
                'idcategoria' => $request->input('idcategoria'),
                'idubigeo' => $request->input('idubigeo'),
                'idusers' => Auth::user()->id,
                'idestadoprivacidad' => 1,
                'idestado' => 1
			    ]);
          
          
            /*if($request->input('imagenportadaant')!='') {
            }else{
              if($request->file('imagenportada')!='') {
                if ($request->file('imagenportada')->isValid()) {
                    $estructura = getcwd().'/public/backoffice/tienda/'.$idtienda.'/portada/';
                    if(mkdir($estructura, 0777, true)) {
                        //$request->file('imagenportada')->move($estructura, $imagenportada);
                        $resize_image = Image::make($request->file('imagenportada')->getRealPath());
                        $resize_image->resize(1900, 400, function($constraint){
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })->save($estructura.$imagenportada);
                    }
                }
              }
            }
          
            if($request->input('imagenant')!='') {
            }else{
              if($request->file('imagen')!='') {
                if ($request->file('imagen')->isValid()) {
                    $estructura = getcwd().'/public/backoffice/tienda/'.$idtienda.'/logo/';
                    if(mkdir($estructura, 0777, true)) {
                        //$request->file('imagen')->move(getcwd().'/public/backoffice/tienda/'.$idtienda.'/logo/', $imagen);
                        $resize_image = Image::make($request->file('imagen')->getRealPath());
                        $resize_image->resize(900, 900, function($constraint){
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })->save($estructura.$imagen);
                    }  
                }
              }
            }*/
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }elseif($request->input('view')=='recomendar') {
            $recomendacionvalid = DB::table('recomendacion')
                ->where('idtienda',$request->input('idtienda'))
                ->where('idusers',$request->input('idusers'))
                ->where('idtiporecomendacion',1)
                ->count();

            $estadoregistrar = 0;
            if($recomendacionvalid==0){
                DB::table('recomendacion')
                    ->where('idtienda',$request->input('idtienda'))
                    ->where('idusers',$request->input('idusers'))
                    ->delete();
                DB::table('recomendacion')->insert([
                    'fecharegistro' => Carbon::now(),
                    'idtienda' => $request->input('idtienda'),
                    'idusers' => $request->input('idusers'),
                    'idtiporecomendacion' => 1,
                ]);
                $estadoregistrar = 1;
            }else{
                DB::table('recomendacion')
                    ->where('idtienda',$request->input('idtienda'))
                    ->where('idusers',$request->input('idusers'))
                    ->delete();
                $estadoregistrar = 2;
            }
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.',
								'estadoregistrar' => $estadoregistrar
						]);
        }elseif($request->input('view')=='calificar') {
            $numerocalificacion = 0;
            for($i=1; $i<6; $i++){
                $calif = $request->input('rating-'.$i);
                if($calif!=''){
                    $numerocalificacion = $calif;
                    break;
                }
            }
            
            DB::table('calificacion')
                ->where('idtienda',$request->input('idtienda'))
                ->where('idusers',$request->input('idusers'))
                ->where('idestado',1)
                ->delete();
          
            DB::table('calificacion')->insert([
                'fecharegistro' => Carbon::now(),
                'numero' => $numerocalificacion,
                'idtienda' => $request->input('idtienda'),
                'idusers' => $request->input('idusers'),
                'idestado' => 1
            ]);
          
            //------> Actualizar calificacion de tienda <--------//
            $calificacion = DB::table('calificacion')
                ->where('idtienda',$request->input('idtienda'))
                ->where('idestado',1)
                ->select('idtienda',DB::raw('CONCAT(SUM(numero)/COUNT(*)) as total'),DB::raw('COUNT(*) as cantidad'))
                ->groupBy('idtienda')
                ->first();
            $cantidadcalificacion = 0;
            $totalsumacalificacion = 5;
            if($calificacion!=''){
                $cantidadcalificacion = $calificacion->cantidad;
                $totalsumacalificacion = $calificacion->total;
            }
          
            DB::table('tienda')
                ->whereId($request->input('idtienda'))
                ->update([
                    'calificacioncantidad' => $cantidadcalificacion,
                    'calificacionpromedio' => $totalsumacalificacion,
                ]);
            //------> Fin Actualizar calificacion de tienda <--------//
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.',
								'numero' => $numerocalificacion
						]);
        }elseif($request->input('view')=='comentar') {
            $rules = [
								'contenido' => 'required',
						];
						$messages = [
								'contenido.required' => 'El "Comentario" es Obligatorio.',
						];
            $this->validate($request,$rules,$messages);
            
            DB::table('tiendacomentario')->insert([
                'fecharegistro' => Carbon::now(),
                'fechaaprobacion' => Carbon::now(),
                'contenido' => $request->input('contenido'),
                'idtienda' => $request->input('idtienda'),
                'idusers' => $request->input('idusers'),
                'idestado' => 1
            ]);
                
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }elseif ($request->input('view') == 'reportartienda') {
            $rules = [
								'motivo' => 'required',
						];
						$messages = [
								'motivo.required' => 'El "Motivo" es Obligatorio.',
						];
            $this->validate($request,$rules,$messages);
            DB::table('reclamo')->insert([
                'motivo' => $request->input('motivo'),
                'idtienda' => $request->input('idtienda'),
                'idusers' => Auth::user()->id,
                'fecharegistro' => Carbon::now(),
                'fecha' => Carbon::now(),
                'idestado' => 1
            ]);
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }
        // categoria
        elseif($request->input('view')=='categoriacreate') {
            $rules = [
								'nombre' => 'required',
						];
						$messages = [
								'nombre.required' => 'El "Nombre" es Obligatorio.',
						];
            $this->validate($request,$rules,$messages);
            DB::table('tiendaproducto')->insert([
                'orden' => 0,
                'nombre' => $request->input('nombre'),
                'idtienda' => $request->input('idtienda'),
                'idestado' => 1
            ]);
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }
        // OFERTA
        elseif($request->input('view')=='ofertacreate') {

            $rules = [
								'nombre' => 'required',
								'stock' => 'required',
								'precio' => 'required',
								'preciooferta' => 'required',
								'fechainicio' => 'required',
								'fechafin' => 'required',
						];
						$messages = [
								'nombre.required' => 'El "Nombre" es Obligatorio.',
								'stock.required' => 'El "Stock de Oferta" es Obligatorio.',
								'precio.required' => 'El "Precio" es Obligatorio.',
								'preciooferta.required' => 'El "Precio de Ofertas" es Obligatorio.',
								'fechainicio.required' => 'El "Inicio" es Obligatorio.',
								'fechafin.required' => 'El "Fin" es Obligatorio.',
						];
            $this->validate($request,$rules,$messages);
          
            //imagen
            $imagen = '';
            if($request->file('imagen')!='') {
              if ($request->file('imagen')->isValid()) {
                  list($nombre,$ext) = explode(".", $request->file('imagen')->getClientOriginalName());
                  $imagen = Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$ext;
                  $request->file('imagen')->move(getcwd().'/public/backoffice/tienda/'.$request->input('idtienda').'/oferta/', $imagen);
              }
            }
            if($imagen==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'La Imagen es obligatorio.'
                ]);
            }
              
            DB::table('oferta')->insert([
                'nombre' => $request->input('nombre'),
                'precio' => $request->input('precio'),
                'preciooferta' => $request->input('preciooferta'),
                'stock' => $request->input('stock'),
                'imagen' => $imagen,
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
                'fecharegistro' => Carbon::now(),
                'idtienda' => $request->input('idtienda'),
                'idestado' => 1
            ]);
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }
    }

    public function show(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
        
        if($id=='showestadooferta'){
            DB::table('oferta')->whereId($request->input('idoferta'))->update([
								'idestado' => $request->input('idestado')
						]);
          
            return array(
              'resultado' => 'CORRECTO'
            );
        }
    }

    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view')=='informacion'){
            $ubigeos = DB::table('ubigeo')->get();
            $tienda = DB::table('tienda')->whereId($id)->first();
            $categorias = DB::table('categoria')->whereIn('id',[8,12,13,21,24,30])->get();
            $codigotelefonicos = DB::table('codigotelefonico')->get();
            return view('layouts/backoffice/tienda/editinformacion',[
                'ubigeos' => $ubigeos,
                'tienda' => $tienda,
                'categorias' => $categorias,
                'codigotelefonicos' => $codigotelefonicos
            ]);
        }elseif($request->input('view')=='dominiopersonalizado'){
            $ubigeos = DB::table('ubigeo')->get();
            $tienda = DB::table('tienda')->whereId($id)->first();
            $categorias = DB::table('categoria')->get();
            return view('layouts/backoffice/tienda/editdominiopersonalizado',[
                'ubigeos' => $ubigeos,
                'tienda' => $tienda,
                'categorias' => $categorias
            ]);
        }elseif($request->input('view')=='galeria'){
            $tienda = DB::table('tienda')->whereId($id)->first();
            $tiendagalerias = DB::table('tiendagaleria')
              ->where('idtienda',$id)
              ->orderBy('fecharegistro','desc')
              ->get();
          
            $tiendavideos = DB::table('tiendavideo')
              ->where('idtienda', $id)
              ->get();
            return view('layouts/backoffice/tienda/editgaleria',[
                'tienda' => $tienda,
                'tiendagalerias' => $tiendagalerias,
                'tiendavideos' => $tiendavideos
            ]);
        }elseif($request->input('view')=='eliminar'){
            $tienda = DB::table('tienda')->whereId($id)->first();
            return view('layouts/backoffice/tienda/delete',[
                'tienda' => $tienda
            ]);
        }elseif($request->input('view')=='resetear'){
            $tienda = DB::table('tienda')->whereId($id)->first();
            return view('layouts/backoffice/tienda/resetear',[
                'tienda' => $tienda
            ]);
        }
        //OFERTAS
        elseif($request->input('view')=='ofertaindex'){
            $tienda = DB::table('tienda')->whereId($id)->first();
          
            $ofertaproducto = DB::table('oferta')
              ->join('tienda','tienda.id','=','oferta.idtienda')
              ->where('idtienda',$id)
              ->select('oferta.*','tienda.nombre as tiendanombre')
              ->orderBy('id','desc')
              ->get();
          
            $empresa = DB::table('empresa')->where('idtienda',$id)->first();
          
            return view('layouts/backoffice/tienda/oferta/index',[
                'tienda' => $tienda,
                'ofertaproducto' => $ofertaproducto,
                'idusers' => Auth::user()->id,
                'empresa' => $empresa
            ]);
        }elseif($request->input('view')=='ofertacreate'){
            $tienda = DB::table('tienda')->whereId($id)->first();
            return view('layouts/backoffice/tienda/oferta/create',[
                'tienda' => $tienda
            ]);
        }elseif($request->input('view')=='ofertaedit'){
            $oferta = DB::table('oferta')
              ->whereId($request->input('idoferta'))
              ->first();
            return view('layouts/backoffice/tienda/oferta/edit',[
                'oferta' => $oferta
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view')=='editinformacion') {
            $rules = [
              'idcategoria' => 'required',
              'link' => 'required|alpha_dash',
              'nombre' => 'required',
              'idcodigotelefonico' => 'required',
              'numerotelefono' => 'required',
              'idubigeo' => 'required',
              'direccion' => 'required',
            ];
            $messages = [
              'idcategoria.required' => 'La "Categoria" es Obligatorio.',
              'link.required' => '',
               'link.alpha_dash' => '',
              'nombre.required' => 'El "Nombre" es Obligatorio.',
              'idcodigotelefonico.required' => 'El "Código de Teléfono" es Obligatorio.',
              'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
              'idubigeo.required' => 'La "Ubicación (Ubigeo)" es Obligatorio.',
              'direccion.required' => 'La "Dirección" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            
            // logo
            $tiendalink = DB::table('tienda')->where('id','<>',$id)->where('link',$request->input('link'))->first();
            if($tiendalink!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'Ya existe el Link, ingrese otro porfavor.'
                ]);
            }
          
            $tienda = DB::table('tienda')->whereId($id)->first();
            $imagenportada = uploadfile($tienda->imagenportada,$request->input('imagenportadaant'),$request->file('imagenportada'),'/public/backoffice/tienda/'.$id.'/portada/',1900,400);
            $imagen = uploadfile($tienda->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$id.'/logo/');
          
            DB::table('tienda')->whereId($id)->update([
                'link' => $request->input('link'),
                'nombre' => $request->input('nombre'),
                'correo' => $request->input('correo')!=null?$request->input('correo'):'',
                'numerotelefono' => $request->input('numerotelefono')!=null?$request->input('numerotelefono'):'',
                'direccion' => $request->input('direccion')!=null?$request->input('direccion'):'',
                'referencia' => $request->input('referencia')!=null?$request->input('referencia'):'',
                'paginaweb' => $request->input('paginaweb')!=null?$request->input('paginaweb'):'',
                'imagen' => $imagen,
                'imagenportada' => $imagenportada,
                'contenido' => $request->input('contenido')!=null?$request->input('contenido'):'',
                'mapa_ubicacion_lat' => $request->input('mapa_ubicacion_lat')!=null?$request->input('mapa_ubicacion_lat'):'',
                'mapa_ubicacion_lng' => $request->input('mapa_ubicacion_lng')!=null?$request->input('mapa_ubicacion_lng'):'',
                'idcodigotelefonico' => $request->input('idcodigotelefonico'),
                'idcategoria' => $request->input('idcategoria'),
                'idubigeo' => $request->input('idubigeo')
			      ]);

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view')=='editdominiopersonalizado') {
            /*$rules = [
                'dominio_personalizado' => 'required',
            ];
            $messages = [
                'dominio_personalizado.required' => 'El "Dominio Personalizado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);*/
          
            DB::table('tienda')->whereId($id)->update([
                'dominio_personalizado' => $request->input('dominio_personalizado')!=null?$request->input('dominio_personalizado'):'',
                'ecommerce_color' => $request->input('ecommerce_color')!=null?$request->input('ecommerce_color'):'',
                'idestadoprivacidad' => $request->input('idestadoprivacidad')!=null?$request->input('idestadoprivacidad'):0,
                'idestado' => $request->input('idestado'),
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view')=='editgaleria'){
            $imagen = '';
            if($request->file('imagen')!='') {
              if ($request->file('imagen')->isValid()) {
                  list($nombre,$ext) = explode(".", $request->file('imagen')->getClientOriginalName());
                  $imagen = Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$ext;
                  $request->file('imagen')->move(getcwd().'/public/backoffice/tienda/'.$id.'/galeria/', $imagen);
              }
            }
            $counttiendagaleria = DB::table('tiendagaleria')->where('idtienda',$id)->count();
            DB::table('tiendagaleria')->insert([
								'fecharegistro' => Carbon::now(),
                'orden' => $counttiendagaleria+1,
                'imagen' => $imagen,
                'idtipoarchivo' => 1,
                'idtienda' => $id,
                'idestado' => 1
						]);
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }elseif($request->input('view')=='editvideo'){
            DB::table('tiendavideo')->where('idtienda',$id)->delete();
            $listavideourl = explode('/-/',$request->input('detallevideo'));
            foreach($listavideourl as $value) {
              if ($value != '') {
                $listavideourl2 = explode('/,/',$value);
                DB::table('tiendavideo')->insert([
                    'item' => $listavideourl2[0],
                    'link' => $listavideourl2[1],
                    'fecharegistro' => Carbon::now(),
                    'idtienda' => $id,
                    'idestado' => 1
                ]);
              }
            }  
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'La lista de videos se ha registrado correctamente.'
            ]);
        }
        // producto/servicio
        elseif($request->input('view')=='categoriaedit') {
            $rules = [
								'nombre' => 'required',
						];
						$messages = [
								'nombre.required' => 'El "Nombre" es Obligatorio.',
						];
            $this->validate($request,$rules,$messages);
            DB::table('tiendaproducto')->whereId($id)->update([
                'nombre' => $request->input('nombre')
            ]);
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }
//         OFERTAS
        elseif($request->input('view')=='ofertaedit'){
         
           $rules = [
              'nombre' => 'required',
              'stock' => 'required',
              'precio' => 'required',
              'preciooferta' => 'required',
              'fechainicio' => 'required',
              'fechafin' => 'required',
          ];
          $messages = [
              'nombre.required' => 'El "Nombre" es Obligatorio.',
              'stock.required' => 'El "Stock" es Obligatorio.',
              'precio.required' => 'El "Precio" es Obligatorio.',
              'preciooferta.required' => 'El "Precio con Oferta" es Obligatorio.',
              'fechainicio.required' => 'El "Inicio" es Obligatorio.',
              'fechafin.required' => 'El "Fin" es Obligatorio.',
          ];
          $this->validate($request,$rules,$messages);
          
          //imagen
          $imagen = '';
          if($request->input('imagenant')!='') {
              $imagen = $request->input('imagenant');
          }else{
              if($request->file('imagen')!='') {
                if ($request->file('imagen')->isValid()) {
                    list($nombre,$ext) = explode(".", $request->file('imagen')->getClientOriginalName());
                    $imagen = Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$ext;
                    $request->file('imagen')->move(getcwd().'/public/backoffice/tienda/'.$request->input('idtienda').'/oferta/', $imagen);
                }
              }
          }
          if($imagen==''){
              return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje' => 'La Imagen es obligatorio.'
              ]);
          }

          DB::table('oferta')->whereId($id)->update([
              'nombre' => $request->input('nombre'),
              'stock' => $request->input('stock'),
              'precio' => $request->input('precio'),
              'preciooferta' => $request->input('preciooferta'),
              'imagen' => $imagen,
              'fechainicio' => $request->input('fechainicio'),
              'fechafin' => $request->input('fechafin'),
          ]);
          return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha actualizo correctamente.'
						]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view')=='deletetienda'){
          
            $rules = [
                'validardato' => 'required',
            ];
            $messages = [
                'validardato.required' => 'El "ELIMINAR" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if($request->validardato!='ELIMINAR'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'La información ingresada es incorrecta, ingrese otra vez por favor.'
                ]);
            }
          
            dd('---');
            /*$counttiendagalerias = DB::table('tiendagaleria')
                ->where('idtienda',$id)
                ->count();
            
            if($counttiendagalerias>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'No puede eliminar, ya que hay imagenes!.'
                ]);
            }
          
            $counttiendaproductos = DB::table('s_categoria')
                ->where('idtienda',$id)
                ->count();
          
            if($counttiendaproductos>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'No puede eliminar, ya que hay categorias!.'
                ]);
            }
                     
            $tienda = DB::table('tienda')->whereId($id)->first();
            $src = getcwd().'/public/backoffice/tienda/'.$id.'/';
            eliminardirectorio($src);
          
            DB::table('tiendagaleria')
                ->where('idtienda',$id)
                ->update([
                    'fechaeliminado' => Carbon::now(),
                    'idestado' => 2
                ]);*/
          
         
            //backoffice
            DB::table('tienda')->whereId($id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('oferta')->whereId($id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('pagotienda')->whereId($id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('reclamo')->whereId($id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('recomendacion')->whereId($id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('reservaoferta')->whereId($id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('calificacion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('tiendacomentario')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('tiendagaleria')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('tiendavideo')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('users')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            // Sistema
            DB::table('s_agencia')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_agenciarepresentante')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_aperturacierrebilletaje')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_banco')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_caja')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_carritocompra')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_categoria')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_comida_ambiente')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_comida_mesa')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_comida_ordenpedido')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_comida_ordenpedidodetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_comida_ordenpedidoventa')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_comida_piso')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
          
            //DB::table('s_compra')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_compradetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_compradevolucion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_compradevoluciondetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_conceptomovimiento')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_config')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_configuracion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_configuracioncomercio')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_configuracioncomida')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_configuracionfacturacion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_configuracionprestamo')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            DB::table('s_cuentabancaria')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_ecommerceportada')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacionboletafactura')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacionboletafacturadetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacioncomunicacionbaja')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacioncomunicacionbajadetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacionguiaremision')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacionguiaremisiondetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacionnotacredito')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacionnotacreditodetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacionrespuesta')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacionresumendiario')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_facturacionresumendiariodetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_formapago')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_marca')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_metodopago')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_moneda')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_movimiento')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_oferta')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
          
            //DB::table('s_prestamo_calificacion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_cartera')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_cobranza')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_cobranzacuentabancaria')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_cobranzadetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_credito')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditoaval')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditobien')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditobienimagen')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditocualitativo')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditocualitativodetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditocualitativopreguntas')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditodetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditodomicilio')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditodomicilioimagen')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditolaboral')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditolaboralcompra')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditolaboralegresogasto')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditolaboralegresopago')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditolaboralingreso')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditolaboralservicio')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditolaboralventa')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditomora')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditomoradetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_creditorelacion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_diaferiado')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_documento')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_estadocivil')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_frecuencia')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_giro')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_mora')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_nivelestudio')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_reprogramacion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_tipobien')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_tipocredito')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_tipopago')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_tiporelacion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_prestamo_transferenciacartera')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
          
            //DB::table('s_producto')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productodemo')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productodescuento')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productodescuentodetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productodetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productogaleria')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productomovimiento')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productooferta')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productosaldo')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productosaldodetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productotransferencia')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_productotransferenciadetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_sunat_modalidadtraslado')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_sunat_motivotraslado')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_tipocomprobante')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_tipoentrega')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_tipometodopago')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_tipomovimiento')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_tipopago')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_transferenciasaldo')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_usuariofacturacion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_usuarioingresosalida')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_usuariosaldo')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_venta')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_ventadelivery')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_ventadescuento')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_ventadescuentodetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_ventadetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_ventadevolucion')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
            //DB::table('s_ventadevoluciondetalle')->where('idtienda',$id)->update(['fechaeliminado' => Carbon::now(),'idestado' => 2]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
          
        }
        elseif($request->input('view')=='reseteartienda'){
          
            $rules = [
                'validardato' => 'required',
            ];
            $messages = [
                'validardato.required' => 'El "RESETEAR" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if($request->validardato!='RESETEAR'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'La información ingresada es incorrecta, ingrese otra vez por favor.'
                ]);
            }
         
            // Sistema
     
            /*DB::table('s_carritocompra')->where('idtienda',$id)->delete();
            DB::table('s_categoria')->where('idtienda',$id)->delete();
            DB::table('s_comida_ordenpedido')->where('idtienda',$id)->delete();
            DB::table('s_comida_ordenpedidodetalle')->where('idtienda',$id)->delete();
            DB::table('s_comida_ordenpedidoventa')->where('idtienda',$id)->delete();
            DB::table('s_compra')->where('idtienda',$id)->delete();
            DB::table('s_compradetalle')->where('idtienda',$id)->delete();
            DB::table('s_compradevolucion')->where('idtienda',$id)->delete();
            DB::table('s_compradevoluciondetalle')->where('idtienda',$id)->delete();
            DB::table('s_facturacionrespuesta')->where('idtienda',$id)->delete();
            DB::table('s_usuariofacturacion')->where('idtienda',$id)->delete();*/
          
            /*DB::table('s_producto')->where('idtienda',$id)->delete();
            DB::table('s_productodescuento')->where('idtienda',$id)->delete();
            DB::table('s_productodescuentodetalle')->where('idtienda',$id)->delete();
            DB::table('s_productodetalle')->where('idtienda',$id)->delete();
            DB::table('s_productogaleria')->where('idtienda',$id)->delete();
            DB::table('s_productopresentacion')->where('idtienda',$id)->delete();*/
          
            $s_productos = DB::table('s_producto')->where('s_producto.idtienda',$id)->get();
            foreach($s_productos as $value){
                sistema_productostock([
                    'idtienda'      => $id,
                    'idproducto'    => $value->id,
                ]);  
            }
          
            DB::table('s_productomovimiento')->where('idtienda',$id)->delete();
            DB::table('s_inventario')->where('idtienda',$id)->delete();
            //DB::table('s_productostock')->where('idtienda',$id)->delete();
            DB::table('s_productotransferencia')->where('idtienda',$id)->delete();
            DB::table('s_productotransferenciadetalle')->where('idtienda',$id)->delete();
            //CREDITOS
            DB::table('s_aperturacierre')->where('idtienda',$id)->delete();
            DB::table('s_aperturacierrebilletaje')->where('idtienda',$id)->delete();
            DB::table('s_movimiento')->where('idtienda',$id)->delete();
            DB::table('s_movimientosaldo')->where('idtienda',$id)->delete();
            DB::table('s_transferenciasaldo')->where('idtienda',$id)->delete();
          
            //DB::table('s_caja')->where('idtienda',$id)->delete();
            //DB::table('s_cuentabancaria')->where('idtienda',$id)->delete();
          
            DB::table('s_prestamo_cobranza')->where('idtienda',$id)->delete();
            //DB::table('s_prestamo_cobranzacuentabancaria')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_cobranzadetalle')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_credito')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_cobranzadetalle')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditobien')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditobienimagen')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditodetalle')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditodomicilio')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditodomicilioimagen')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboral')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralboletacompraimagen')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralcompra')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralegresogasto')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralegresogastofamiliar')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralegresopago')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralficharucimagen')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralingreso')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralnegocioimagen')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralotrogasto')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralotroingreso')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralreciboaguaimagen')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralreciboluzimagen')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralservicio')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditolaboralventa')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditorelacion')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_creditosustento')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_mora')->where('idtienda',$id)->delete();
            DB::table('s_prestamo_reprogramacion')->where('idtienda',$id)->delete();
          
            DB::table('s_compra')->where('idtienda',$id)->delete();
            DB::table('s_compradetalle')->where('idtienda',$id)->delete();
            DB::table('s_compradevolucion')->where('idtienda',$id)->delete();
            DB::table('s_compradevoluciondetalle')->where('idtienda',$id)->delete();
            DB::table('s_venta')->where('idtienda',$id)->delete();
            //DB::table('s_ventadetalle')->where('idtienda',$id)->delete();
            DB::table('s_ventadescuento')->where('idtienda',$id)->delete();
            DB::table('s_ventadescuentodetalle')->where('idtienda',$id)->delete();
            DB::table('s_ventadelivery')->where('idtienda',$id)->delete();
            DB::table('s_ventadevolucion')->where('idtienda',$id)->delete();
            DB::table('s_ventadevoluciondetalle')->where('idtienda',$id)->delete();
          
            DB::table('s_cotizacion')->where('idtienda',$id)->delete();
            DB::table('s_ordenservicio')->where('idtienda',$id)->delete();
          
            DB::table('s_facturacionboletafactura')->where('idtienda',$id)->delete();
            DB::table('s_facturacionboletafacturadetalle')->where('idtienda',$id)->delete();
            DB::table('s_facturacioncomunicacionbaja')->where('idtienda',$id)->delete();
            DB::table('s_facturacioncomunicacionbajadetalle')->where('idtienda',$id)->delete();
            DB::table('s_facturacionguiaremision')->where('idtienda',$id)->delete();
            DB::table('s_facturacionguiaremisiondetalle')->where('idtienda',$id)->delete();
            DB::table('s_facturacionnotacredito')->where('idtienda',$id)->delete();
            DB::table('s_facturacionnotacreditodetalle')->where('idtienda',$id)->delete();
            DB::table('s_facturacionnotadebito')->where('idtienda',$id)->delete();
            DB::table('s_facturacionnotadebitodetalle')->where('idtienda',$id)->delete();
            DB::table('s_facturacionresumendiario')->where('idtienda',$id)->delete();
            DB::table('s_facturacionresumendiariodetalle')->where('idtienda',$id)->delete();
          
            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/sistema_json/');

            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/sunat/produccion/boletafactura/');
            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/sunat/produccion/notadebito/');
            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/sunat/produccion/notacredito/');
            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/sunat/produccion/comunicacionbaja/');
            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/sunat/produccion/resumendiario/');
            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/sunat/produccion/guiaremision/');

            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/creditodomicilio/');
            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/creditolaboral/');
            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/creditobien/');
            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/prestamoreprogramacion/');
            eliminardirectorio(getcwd().'/public/backoffice/tienda/'.$id.'/cobranza/');
            
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha reseteado correctamente.'
            ]);
          
        }
        elseif($request->input('view')=='deletegaleria'){
            $tiendagaleria = DB::table('tiendagaleria')
              ->join('tienda','tienda.id','=','tiendagaleria.idtienda')
              ->where('tiendagaleria.id',$id)
              ->select(
                  'tiendagaleria.*',
                  'tienda.id as idtienda'
              )
              ->first();
            $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tiendagaleria->idtienda.'/galeria/'.$tiendagaleria->imagen;
            if(file_exists($rutaimagen) && $tiendagaleria->imagen!='') {
                unlink($rutaimagen);
            }
            DB::table('tiendagaleria')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
        // categoria
        elseif($request->input('view')=='categoriadelete'){
            $counttiendaproductodetalle = DB::table('tiendaproductodetalle')
                ->where('idtiendaproducto',$id)
                ->count();
            if($counttiendaproductodetalle>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'No puede eliminar, ya que hay imagenes!.'
                ]);
            }
            DB::table('tiendaproducto')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view')=='categoriadetalledelete'){
            $counttiendaproductodetallegaleria = DB::table('tiendaproductodetallegaleria')
                ->where('idtiendaproductodetalle',$id)
                ->count();
            if($counttiendaproductodetallegaleria>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'No puede eliminar, ya que hay imagenes!.'
                ]);
            }
            DB::table('tiendaproductodetalle')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
        elseif($request->input('view')=='deleteofertagaleria'){
//           dd($request->input());
            $ofertagaleria = DB::table('ofertagaleria')
              ->whereId($id)
              ->select(
                  'ofertagaleria.*'
              )
              ->first();
            $rutaimagen = getcwd().'/public/backoffice/tienda/'.$request->input('idtienda').'/oferta/'.$ofertagaleria->imagen;
            if(file_exists($rutaimagen) && $ofertagaleria->imagen!='') {
                unlink($rutaimagen);
            }
            DB::table('ofertagaleria')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
