@include('app.nuevosistema.tabla',[
    'tabla' => 'tabla-'.$_GET['name_modulo'],
    'tbody' => [
        ['data' => 'id'],
        ['data' => 'titulo','type'=>'title'],
        ['data' => 'nombre','type'=>'text'],
        ['data' => 'titulo2','type'=>'title'],
        ['data' => 'nombre2'],
        ['data' => 'option','type'=>'btn'],
    ]
])