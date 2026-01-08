
@include('app.nuevosistema.tabla',[
    'tabla' => 'tabla-'.$_GET['name_modulo'],
    'tbody' => [
        ['data' => 'id'],
        ['data' => 'codigo'],
        ['data' => 'tipo'],
        ['data' => 'moneda'],
        ['data' => 'monto'],
        ['data' => 'responsable'],
        ['data' => 'estado'],
        ['data' => 'option', 'type' => 'btn'],
    ]
])