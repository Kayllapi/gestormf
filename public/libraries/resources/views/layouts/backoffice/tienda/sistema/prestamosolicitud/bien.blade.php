<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Garantias</span>
      <a class="btn btn-warning" href="javascript:;" onclick="bien_create()"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Producto</th>
        <th>Descripción</th>
        <th>Valor Estimado</th>
        <th>Documento</th>
        <th>Imagenes</th>
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
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    <li><a href="javascript:;" onclick="bien_edit({{$value->id}})"><i class="fa fa-edit"></i> Editar</a></li>
                    <li><a href="javascript:;" onclick="bien_imagen({{$value->id}})"><i class="fa fa-images"></i> Imágenes</a></li>
                    <li><a href="javascript:;" onclick="bien_eliminar({{$value->id}})"><i class="fa fa-trash"></i> Eliminar</a></li>
                </ul>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>
<script>  
    $("div#menu-opcion").on("click", function () {
        $("ul",this).toggleClass("hu-menu-vis");
        $("div",this).toggleClass("hu-menu-visdec");
    });
</script>
