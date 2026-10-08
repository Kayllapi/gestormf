<div class="modal-header">
    <h5 class="modal-title">
        Clientes/Garantes - Asesor
        <a href="javascript:;" 
            class="btn btn-primary" 
            onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuario/create?view=registrar&modulo=usuario')}}'})">
        <i class="fa-solid fa-plus"></i> Registrar
        </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-6">
            <div class="row">
                <label for="fecha_fin" class="col-sm-3 col-form-label">ASESOR/EJECUTIVO</label>
                <div class="col-sm-9">
                    @php
                        $usuario = DB::table('users')
                            ->join('users_permiso','users_permiso.idusers','users.id')
                            ->join('permiso','permiso.id','users_permiso.idpermiso')
                            ->whereIn('users_permiso.idpermiso',[3,4,7,11])
                            ->where('users_permiso.idtienda',$tienda->id)
                            ->where('users.id', Auth::user()->id)
                            ->select('users.nombrecompleto','permiso.nombre as nombrepermiso')
                            ->first();
                        $usuarioText = "$usuario->nombrecompleto ($usuario->nombrepermiso)";
                    @endphp
                    <input type="text" class="form-control" value="{{$usuarioText}}" disabled>
                    <input type="hidden" id="idasesor" value="{{Auth::user()->id}}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-body">
    @include('app.nuevosistema.tabla',[
        'tabla' => '#tabla-usuario',
        'route' => url('backoffice/'.$tienda->id.'/usuarioasesor/show_table'),
        'type' => 'GET',
        'scrollY' => 'calc(-196px + 100vh)',
        'thead' => [
            ['data' => 'Código'],
            ['data' => 'RUC/DNI/CE'],
            ['data' => 'Cliente'],
            ['data' => 'Distrito - Provincia - Departamento	'],
            ['data' => 'Tipo de Persona'],
            ['data' => 'Tipo Documento'],
            ['data' => ''],
        ],
        'tbody' => [
            ['data' => 'codigo','type'=>'code'],
            ['data' => 'identificacion','type'=>'code'],
            ['data' => 'cliente','type'=>'text'],
            ['data' => 'ubigeo','type'=>'text'],
            ['data' => 'persona','type'=>'text'],
            ['data' => 'tipodocumento','type'=>'text'],
            ['data' => 'opcion','type'=>'btn'],
        ],
        'tfoot' => [
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            [
                'data' => [
                    [
                        'id' => 1,
                        'text' => 'NATURAL'
                    ],
                    [
                        'id' => 2,
                        'text' => 'JURÍDICA'
                    ]
                ],
                'type' => 'select',
            ],
            [
                'data' => [
                    [
                        'id' => 1,
                        'text' => 'DNI'
                    ],
                    [
                        'id' => 2,
                        'text' => 'RUC'
                    ],
                    [
                        'id' => 3,
                        'text' => 'CE'
                    ]
                ],
                'type' => 'select',
            ],
            ['type' => ''],
        ]
    ])
</div>