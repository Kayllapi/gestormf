 @include('app.sistema.tabla',[
     'tabla' => 'tabla-pagorealizado',
     'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/show-indexpagorealizado'),
      'data' => [
          'idcredito' => $s_prestamo_credito->id
      ],
     'thead' => [
         ['data' => 'Código'],
         ['data' => 'Fecha de Pago'],
         ['data' => 'Monto'],
         ['data' => 'Responsable'],
         ['data' => 'Estado'],
         ['data' => '', 'width' => '10px']
     ],
     'tbody' => [
         ['data' => 'codigo'],
         ['data' => 'fechapago'],
         ['data' => 'monto'],
         ['data' => 'responsable'],
         ['data' => 'estado'],
         ['render' => 'opcion']
     ],
     'tfoot' => [
         ['input' => 'text'],
         ['input' => 'date'],
         ['input' => ''],
         ['input' => 'text'],
         ['input' => ''],
         ['input' => ''],
     ]
 ])