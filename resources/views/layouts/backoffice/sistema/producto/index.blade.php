@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Productos</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/producto/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="custom-form">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-6">
              <input type="text" class="column_filter" value="{{session('buscar_codigoProducto')}}" id="buscar_codigoProducto" placeholder="Buscar por Código" style="margin-bottom: 5px;">
              <input type="text" class="column_filter" value="{{session('buscar_nombreProducto')}}" id="buscar_nombreProducto" placeholder="Buscar por Nombre" style="margin-bottom: 5px;">
              <input type="text" class="column_filter" value="{{session('buscar_nombreCategoria')}}" id="buscar_nombreCategoria" placeholder="Buscar por Categoria" style="margin-bottom: 5px;">
          </div>
          <div class="col-md-6">
              <input type="text" class="column_filter" value="{{session('buscar_nombreMarca')}}" id="buscar_nombreMarca" placeholder="Buscar por Marca" style="margin-bottom: 5px;">
              <select id="buscar_estadoProducto">
                  <option></option>
                  <option value="1" {{session('buscar_estadoProducto')==1?'selected':''}}>Activado</option>
                  <option value="2" {{session('buscar_estadoProducto')==2?'selected':''}}>Desactivado</option>
              </select>
              <select id="buscar_estadotvProducto">
                  <option></option>
                  <option value="1" {{session('buscar_estadotvProducto')==1?'selected':''}}>Activado</option>
                  <option value="2" {{session('buscar_estadotvProducto')==2?'selected':''}}>Desactivado</option>
              </select>
          </div>
        </div> 
          <!--div class="col-md-4">
              <select class="form-control" id="camera-select" style="display:none;"></select>
              <a href="#" class="btn btn-primary" id="playscanear" data-toggle="tooltip"  style="padding: 11.5px 15px;margin-bottom: 5px;"><i class="fa fa-qrcode"></i> Escanear</a>
          </div-->
      </div>
      <div class="col-md-1">
        <div id="cont-producto-imagen" style="background-color: #dedede;height: 133px;width: 100%;border-radius: 5px;border: 1px solid #656565;float: left;margin-bottom: 5px;">
        </div>
        <div id="cont-camaraqr" style="background-color: #dedede;height: 133px;width: 100%;border-radius: 5px;border: 1px solid #656565;float: left;margin-bottom: 5px;display:none;">
          <canvas id="webcodecam-canvas" style="width: 100%;height: 131px;border-radius: 5px;"></canvas>
          <a href="javascript:;" id="stopscanear"></a>
        </div>
      </div>
      <div class="col-md-5">
        <div id="cont-producto-stock" style="background-color: #dedede;height: 133px;width: 100%;border-radius: 5px;overflow-y: auto;border: 1px solid #656565;margin-bottom: 5px;"></div>
      </div>
    </div>
</div>
<div class="table-responsive" style="float: left;">
        <table class="table" id="myTable" style="width:100%;">
            <thead class="thead-dark">
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>U. Medida</th>
                    <th>Precio</th>
                    <th width="10px">Imagen</th>
                    <th width="10px">Sistema</th>
                    <th width="10px">T. Virtual</th>
                    <th width="10px"></th>
                </tr>
            </thead>
        </table>
</div>
@endsection
@section('subscripts')
<style>
  .select2-container {
      margin-bottom: 5px;
  }
  .select_vencimiento {
      background-color:#ffcccb;
  }
</style>
<script>
$("#buscar_estadoProducto").select2({
    placeholder: "--- Estado de Sistema ---",
    minimumResultsForSearch: -1,
    allowClear: true
});
$("#buscar_estadotvProducto").select2({
    placeholder: "--- Estado de T. Virtual ---",
    minimumResultsForSearch: -1,
    allowClear: true
});
function mostrar_producto(idproducto){
    $('#stopscanear').click();
    $('#cont-camaraqr').css('display','none');
    $('#cont-producto-imagen').css('display','block');
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/producto/showstockimagenproducto')}}",
        type:'GET',
        data: {
            idproducto : idproducto
        },
        beforeSend: function (data) {
            load('#cont-producto-imagen');
            load('#cont-producto-stock');
        },
        success: function (respuesta){
            $('#cont-producto-imagen').html(respuesta['imagenes']);
            $('#cont-producto-stock').html(respuesta['stock']);
        }
    });
}
</script>



<script>
        $(document).ready(function() {
            $.fn.dataTableExt.ofnSearch['string'] = function ( data ) {
            return ! data ?
                '' :
                typeof data === 'string' ?
                    data
                        .replace( /\n/g, ' ' )
                        .replace( /á/g, 'a' )
                        .replace( /é/g, 'e' )
                        .replace( /í/g, 'i' )
                        .replace( /ó/g, 'o' )
                        .replace( /ú/g, 'u' )
                        .replace( /ê/g, 'e' )
                        .replace( /î/g, 'i' )
                        .replace( /ô/g, 'o' )
                        .replace( /è/g, 'e' )
                        .replace( /ï/g, 'i' )
                        .replace( /ü/g, 'u' )
                        .replace( /ç/g, 'c' ) :
                    data;
            };
          
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url : "{{url('backoffice/tienda/sistema/'.$tienda->id.'/producto/show_buscarproducto')}}",
                    /*data : {
                        'columns[4][search][value]' : 'Cerveza'
                    }*/
                },
                //ajax:       "{{url('public/backoffice/tienda/'.$tienda->id.'/productojson/productos.json')}}",
                dom:        'rtip',
                mark:       true,
                select: {
                    toggleable: false,
                    selector: 'td:not(:nth-child(10))'
                },
                //order: [[ 0, "desc" ]],
                language: {
                    info: "Mostrando _START_ de _TOTAL_ entradas",
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente"
                    },
                    select: {
                        rows: ""
                    },
                    processing:"Cargando..."
                },
                columns: [
                    { data: "id",visible: false,searchable: false },
                    { data: "idestado",visible: false,orderable: false},
                    { data: "idestadotv",visible: false,orderable: false},
                    { data: "codigo" },
                    { data: "nombre" },
                    {
                        render: function ( data, type, full, meta ) {
                            return full.categorianombre+(full.subcategorianombre!=undefined? ' / '+full.subcategorianombre:'');
                        }
                    },
                    { data: "marcanombre" },
                    {
                        render: function ( data, type, full, meta ) {
                            var umedida = full.unidadmedida;
                            if(full.idproductopresentacion>0){
                                umedida = '<i class="fa fa-random" style="color: #008cea;"></i> '+full.unidadmedida;
                            }
                            return umedida;
                        }
                    },
                    { data: "precioalpublico" },
                    {
                        render: function ( data, type, full, meta ) {
                            var imagen = '<img src="{{ url('public/backoffice/tienda') }}/'+full.idtienda+'/producto/40/'+full.imagen+'" height="40px">';
                            if(full.imagen==null){
                                imagen = '<img src="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}" height="40px">';
                            }
                            //var imagen = '';
                            return imagen;
                        },
                        orderable: false
                    },
                    {
                        render: function ( data, type, full, meta ) {
                            var estado = '<span class="badge badge-pill badge-dark">Desactivado</span>';
                            if(full.idestado==1){
                                estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span>';
                            }
                            return estado;
                        },
                        orderable: false
                    },
                    {
                        render: function ( data, type, full, meta ) {
                            var estado = '<span class="badge badge-pill badge-dark">Desactivado</span>';
                            if(full.idestadotv==1){
                                estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span>';
                            }
                            return estado;
                        },
                        orderable: false
                    },
                    { data: null, 
                        defaultContent: '<div class="header-user-menu menu-option" id="menu-opcion">'+
                                            '<a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>'+
                                            '<ul>'+
                                            '</ul>'+
                                        '</div>',
                        orderable: false, 
                    }
                ],   
                /*rowCallback: function (row, data) {
                    if(data.diasfaltantevencimiento<=data.alertavencimiento){
                        $(row).addClass('select_vencimiento');
                    }
                }*/
            });
          

            // Se muestra la imagen del producto y el almacén
            table
              .on('select', function ( e, dt, type, indexes ) {
                  var rowData = table.rows( indexes ).data().toArray();
                  mostrar_producto(rowData[0].id);
              });
          
            // Se muestra el botón opción
            $('#myTable tbody').on( 'click', 'div#menu-opcion', function () {
              
                // cargar menu tabla
                $("ul",this).toggleClass("hu-menu-vis");
                $("i",this).toggleClass("fa-angle-up");
                // fin cargar menu tabla
                var data    = table.row($(this).parents('tr')).data();
                var row     = $(this).find('ul');
                $(row).html('<li><a href="{{ url('backoffice/tienda/sistema') }}/'+data.idtienda+'/producto/'+data.id+'/edit?view=imagen"><i class="fa fa-images"></i> Imagenes</a></li>'+
                            '<li><a href="{{ url('backoffice/tienda/sistema') }}/'+data.idtienda+'/producto/'+data.id+'/edit?view=editar"><i class="fa fa-edit"></i> Editar</a></li>'+
                            '<li><a href="{{ url('backoffice/tienda/sistema') }}/'+data.idtienda+'/producto/'+data.id+'/edit?view=ticketprecio"><i class="fa fa-receipt"></i> Ticket de Precio</a></li>'+
                            '<li><a href="{{ url('backoffice/tienda/sistema') }}/'+data.idtienda+'/producto/'+data.id+'/edit?view=codigobarra"><i class="fa fa-barcode"></i> Código de Barra</a></li>'+
                            '<!--li><a href="{{ url('backoffice/tienda/sistema') }}/'+data.idtienda+'/producto/'+data.id+'/edit?view=eliminar"><i class="fa fa-trash"></i> Eliminar</a></li-->');
            });
        
            // Se establece el buscador y a la columna que le corresponde

            //
            $('#buscar_estadoProducto').on('change',function(){
                table.column(1).search(this.value).draw();
            });
            $('#buscar_estadotvProducto').on('change',function(){
                table.column(2).search(this.value).draw();
            });
            
            let $comment3 = document.getElementById("buscar_codigoProducto")
            let timeout3

            $comment3.addEventListener('keydown', () => {
              clearTimeout(timeout3)
              timeout3 = setTimeout(() => {
                table.column(3).search($('#buscar_codigoProducto').val()).draw();
                clearTimeout(timeout3)
              },700)
            });
              
            let $comment4 = document.getElementById("buscar_nombreProducto")
            let timeout4

            $comment4.addEventListener('keydown', () => {
              clearTimeout(timeout4)
              timeout4 = setTimeout(() => {
                table.column(4).search($('#buscar_nombreProducto').val()).draw();
                clearTimeout(timeout4)
              },700)
            });
          
            let $comment5 = document.getElementById("buscar_nombreCategoria")
            let timeout5

            $comment5.addEventListener('keydown', () => {
              clearTimeout(timeout5)
              timeout5 = setTimeout(() => {
                table.column(5).search($('#buscar_nombreCategoria').val()).draw();
                clearTimeout(timeout5)
              },700)
            });
            
            let $comment6 = document.getElementById("buscar_nombreMarca")
            let timeout6

            $comment6.addEventListener('keydown', () => {
              clearTimeout(timeout6)
              timeout6 = setTimeout(() => {
                table.column(6).search($('#buscar_nombreMarca').val()).draw();
                clearTimeout(timeout6)
              },700)
            });

            @if(session('buscar_nombreProducto')!='')
                setTimeout(function(){ 
                    table.column(1).search('{{session('buscar_estadoProducto')}}');
                    table.column(2).search('{{session('buscar_estadotvProducto')}}');
                    table.column(3).search('{{session('buscar_codigoProducto')}}');
                    table.column(4).search('{{session('buscar_nombreProducto')}}'); 
                    table.column(5).search('{{session('buscar_nombreCategoria')}}');
                    table.column(6).search('{{session('buscar_nombreMarca')}}');
                    table.draw();
                }, 100);
            @endif
        }); 
</script>
<script src="{{ url('public/libraries/webcodecamjs/js/filereader.js') }}"></script>
<script src="{{ url('public/libraries/webcodecamjs/js/qrcodelib.js') }}"></script>
<script src="{{ url('public/libraries/webcodecamjs/js/webcodecamjquery.js') }}"></script>
<script src="{{ url('public/libraries/webcodecamjs/js/mainjquerycallback.js') }}"></script>
<style>
  @media (max-width: 1064px){
    #cont-camaraqr {
        height: 303px !important;
    }
    #webcodecam-canvas {
        height: 300px !important;
    }
  }

</style>
<script>
webcodecamcallback({
      play : '#playscanear',
      stop : '#stopscanear',
      contenedor : '#cont-camaraqr',
      contenedorOcultar : '#cont-producto-imagen',
      canvas : '#webcodecam-canvas'
  },
  function(resultado){
      if(resultado!=''){
          $("#buscar_codigoProducto").val(resultado.code);
          $("#buscador_producto").submit();
      }  
});
</script>
@endsection

