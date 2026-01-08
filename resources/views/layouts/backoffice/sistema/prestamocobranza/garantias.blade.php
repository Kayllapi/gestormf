<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Producto</th>
        <th>Descripción</th>
        <th>Valor Estimado</th>
        <th>Documento</th>
        <th>Imagenes</th>
        <th>Fecha de Entrega</th>
        <th width="10px"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($bienes as $value)
        <tr>
          <td>{{$value->producto}}</td>
          <td>{{$value->descripcion}}</td>
          <td>{{$value->valorestimado}}</td>
          <td>
            @if($value->idprestamo_documento==1)
                SIN DOCUMENTOS
            @elseif($value->idprestamo_documento==2)
                COPIA/LEGALIZADO
            @elseif($value->idprestamo_documento==3)
                ORIGINAL
            @endif
          </td>
          <td>
            <?php $prestamobienimagen = DB::table('s_prestamo_creditobienimagen')->where('idprestamo_creditobien', $value->id)->get(); ?>
            @foreach($prestamobienimagen as $valueimagen)
                <div style="background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditobien/'.$valueimagen->imagen)}});
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;
                              height: 42px;
                              width: 50px;
                              background-color: #31353c;
                              float: left;
                              margin-right: 1px;">
                </div>
            @endforeach 
          </td>
          <td>{{($value->idestadoentrega==2 or $value->idestadoentrega==3)?date_format(date_create($value->fechaentrega),"d/m/Y h:i A"):'---'}}</td>
          <td>
            @if($value->idestadoentrega==2 or $value->idestadoentrega==3)
            @else
            <div class="header-user-menu menu-option" id="menu-opcion-garantia">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    <li><a href="javascript:;" onclick="devolver_garantias({{$value->id}})"><i class="fa fa-retweet"></i> Devolver</a></li>
                    <li><a href="javascript:;" onclick="rematar_garantias({{$value->id}})"><i class="fa fa-arrow-up"></i> Rematar</a></li>
                </ul>
            </div>
            @endif
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>
<script>  
    $("div#menu-opcion-garantia").on("click", function () {
        $("ul",this).toggleClass("hu-menu-vis");
        $("div",this).toggleClass("hu-menu-visdec");
    });
</script>

