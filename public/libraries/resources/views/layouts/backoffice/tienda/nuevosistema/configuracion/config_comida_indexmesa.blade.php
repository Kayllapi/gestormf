
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span><b>Piso {{ str_pad($piso->nombre, 2, "0", STR_PAD_LEFT) }} / Ambiente {{ str_pad($ambiente->nombre, 2, "0", STR_PAD_LEFT) }}</b> / Mesas</span>
          <a class="btn btn-success" href="javascript:;" onclick="index_ambiente({{ $tienda->id }}, {{ $piso->id }})"><i class="fa fa-angle-left"></i> Atras</a>
          <a class="btn btn-warning" href="javascript:;" onclick="registrar_mesa({{ $tienda->id }}, {{ $piso->id }}, {{ $ambiente->id }})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div>
    </div>
      @include('app.sistema.tabla',[
          'tabla' => 'tabla-mesas',
          'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/show-indexmesa'),
          'data' => [
              'idpiso' => $piso->id,
              'idambiente' => $ambiente->id
          ],
          'thead' => [
              ['data' => 'Mesa'],
              ['data' => 'Estado', 'width' => '10px'],
              ['data' => '', 'width' => '10px']
          ],
          'tbody' => [
              ['data' => 'numero_mesa'],
              ['data' => 'estado'],
              ['render' => 'opcion'],
          ],
          'tfoot' => [
              ['input' => ''],
              ['input' => ''],
              ['input' => ''],
          ]
      ])