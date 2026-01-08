@include('app.nuevosistema.tabla',[
    'tabla' => 'tabla-'.$_GET['name_modulo'],
    'tbody' => [
        ['data' => 'id'],
        ['data' => 'imagen','type'=>'img'],
        ['data' => 'titulo','type'=>'title'],
        ['data' => 'nombre'],
        ['data' => 'option','type'=>'btn'],
    ]
])