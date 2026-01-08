
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span><b>Piso {{ str_pad($piso->nombre, 2, "0", STR_PAD_LEFT) }}</b> / Ambientes</span>
          <a class="btn btn-success" href="javascript:;" onclick="index_piso()"><i class="fa fa-angle-left"></i> Atras</a>
          <a class="btn btn-warning" href="javascript:;" onclick="registrar_ambiente({{ $tienda->id }}, {{ $piso->id }})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div>
    </div>
      @include('app.sistema.tabla',[
          'tabla' => 'tabla-ambientes',
          'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/show-indexambiente'),
          'data' => [
              'idpiso' => $piso->id
          ],
          'thead' => [
              ['data' => 'Ambientes'],
              ['data' => 'Mesas'],
              ['data' => 'Estado', 'width' => '10px'],
              ['data' => '', 'width' => '10px']
          ],
          'tbody' => [
              ['data' => 'ambiente'],
              ['data' => 'mesas'],
              ['data' => 'estado'],
              ['render' => 'opcion'],
          ],
          'tfoot' => [
              ['input' => ''],
              ['input' => ''],
              ['input' => ''],
              ['input' => ''],
          ]
      ])