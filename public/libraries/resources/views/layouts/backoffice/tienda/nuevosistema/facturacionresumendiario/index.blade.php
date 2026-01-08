@include('app.nuevosistema.tabla',[
    'tabla' => 'tabla-'.$_GET['name_modulo'],
'tbody' => [
        ['data' => 'id'],
        ['data' => 'titulo','type'=>'title'],
        ['data' => 'nombre','search'=>'text'],
        ['data' => 'titulo2','type'=>'title'],
        ['data' => 'nombre2','search'=>'text'],
        ['data' => 'option','type'=>'btn'],
    ]
])
