<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Importar Garantia</span>
        <a class="btn btn-success" href="javascript:;" onclick="bien_index()"><i class="fa fa-angle-left"></i> Atras</a>
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
            <a class="btn btn-warning" href="javascript:;" onclick="bien_importaragregar({{$value->id}})" id="btn_seleccionar{{$value->id}}">Seleccionar <i class="fa fa-angle-right"></i></a>
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
    function bien_importaragregar(idbien){
        $('#btn_seleccionar'+idbien).removeAttr('onclick');
        $('#btn_seleccionar'+idbien).html('Cargando...');
        callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}',
            method: 'PUT',
            data:   {
                view: 'importar-bien',
                idprestamo_creditobien: idbien
            }
        },
        function(resultado){
            $('#btn_seleccionar'+idbien).css('background-color','#0ec529');
            $('#btn_seleccionar'+idbien).html('<i class="fa fa-check"></i> Agregado');
        })
    }
</script>