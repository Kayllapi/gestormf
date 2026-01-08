
<div style="width: 100%;height: 55px;">
    <div class="header-search mx-header-search">
        <div class="header-search-input-item mx-header-search-input-item">
            <input type="text" id="tabla_bsucador_{{$tabla}}" placeholder="Buscar..." style="margin-bottom: 0px;border-radius: 0px;">
        </div>
        <button class="header-search-button" type="submit"><i class="fa fa-search"></i></button>
    </div>
</div>
<div class="table-responsive" style="float: left;">
<table class="table" id="{{$tabla}}" style="width: 100%;">
    <thead class="thead-dark">
      <tr>
        @foreach($thead as $value)
        <th <?php echo isset($value['width']) ? 'width="'.$value['width'].'"':'' ?>>{{$value['data']}}</th>
        @endforeach
      </tr>
    </thead>
    @if(isset($tfoot))
    <tfoot>
      <tr>
        @foreach($tfoot as $value)
            @if($value['input']=='text')
            <th style="padding:2px"><input type="text" placeholder="Buscar..."></th>
            @elseif($value['input']=='date')
            <th style="padding:2px"><input type="date" placeholder="Buscar..." style="padding: 7.5px;"></th>
            @elseif($value['input']=='select')
            <th style="padding:2px"><select style="padding: 9px;border-radius: 5px;width: 100%;border: 1px solid #d3d8de;">
                <option value="">Buscar...</option>
                @foreach($value['option'] as $listval)
                  <option value="{{$listval['id']}}">{{$listval['nombre']}}</option>
                @endforeach
              </select></th>
            @else
            <th style="padding:2px"></th>
            @endif
        @endforeach
      </tr>
    </tfoot>
    @endif
</table>
</div>

<style>
  .columdata{
      width: unset !important;
  }
</style>
@if(isset($script))
@section($script)
@endif
<script>
  
    <?php $var = rand(100000000, 999999999) ?>
    
    <?php 
    $rutaajax = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/sistema_json/'.$_GET['name_modulo'].'.json'; 
    if(file_exists($rutaajax) AND $_GET['name_modulo']!=''){
        $urlajax = url('/public/backoffice/tienda/'.$tienda->id.'/sistema_json/'.$_GET['name_modulo'].'.json');
    }else{
        $urlajax = '';
    }
    ?>
       
    var table{{$var}} = $('#{{$tabla}}').DataTable({
        ajax: '{{$urlajax}}',
        dom: 'rti',
        scrollX: true,
        mark: true,
        keys: true,
        scrollY: 400,
        scroller: {
            loadingIndicator: true
        },
        colReorder: false,
        order: [[ 0, "desc" ]],
        language: {
            info: "Mostrando _START_ de _TOTAL_ entradas",
            paginate: {
                previous: "Anterior",
                next: "Siguiente"
            },
            select: {
                rows: ""
            },
            processing:"Cargando...",
            emptyTable: "No hay datos disponibles en la tabla"
        },
        /*initComplete: function () {
          this.api().columns().every( function () {
              var that = this;
              $( 'input', this.footer() ).on( 'keyup change clear', function () {
                  if ( that.search() !== this.value ) {
                      that
                          .search( this.value )
                          .draw();
                  }
              } );
              $( 'select', this.footer() ).on( 'keyup change clear', function () {
                  if ( that.search() !== this.value ) {
                      that
                          .search( this.value )
                          .draw();
                  }
              } );
          } );
        },*/
        createdRow: function( row, data, dataIndex ) {
            $(row).addClass(data.classname);
        },
        columns: [
            <?php $i = 0 ?>
            @foreach($tbody as $value)
              @if($i==0)
                  { 
                      data: '{{$value['data']}}',
                      visible: false,
                  },
              @else
                  { 
                      data: '{{$value['data']}}'
                  },
              @endif
            <?php $i++ ?>
            @endforeach
        ]
    });

    $('#{{$tabla}} tbody').on('click', 'tr', function () {
        var data = table{{$var}}.row(this).data();
        $('#{{$tabla}} tbody .selected').removeClass('selected');
        $(this).addClass('selected');
        //$('#idmodulotabla').val(data.id);
        //mostrar_producto(data.id);
    });
  
    var detailRows = [];
    $('#{{$tabla}} tbody').on('dblclick', 'tr', function () {
        // detalle
        var tr = $(this).closest('tr');
        var row = table{{$var}}.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
            detailRows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );
            row.child('<div id="cont-cuerpotabladetalle'+row.data().id+'" style="text-align: center;background-color: #dbdbdb;padding: 5px;"></div>').show();
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
            
            pagina({route:'{{url($_GET['url_sistema'].'/'.$tienda->id) }}/{{$_GET['name_modulo']}}/'+row.data().id+'/edit'+
                        '?view=detalle'+
                        '&nombre_modulo={{$_GET['nombre_modulo']}}'+
                        '&imagen_modulo={{$_GET['imagen_modulo']}}'+
                        '&idmodulo={{$_GET['idmodulo']}}'+
                        '&nombre_submodulo={{$_GET['nombre_submodulo']}}'+
                        '&imagen_submodulo={{$_GET['imagen_submodulo']}}'+
                        '&idsubmodulo={{$_GET['idsubmodulo']}}'+
                        '&name_modulo={{$_GET['name_modulo']}}'+
                        '&url_sistema={{$_GET['url_sistema']}}',
                        result:'#cont-cuerpotabladetalle'+row.data().id});
        }
    });
  
    // subir y bajar por medio de teclado
    table{{$var}}.on('key-focus', function (e, datatable, cell) {
        datatable.rows().deselect();
        datatable.row( cell.index().row ).select();
   
    });
    // enter para abrir detalle
    table{{$var}}.on( 'key', function ( e, datatable, key, cell, originalEvent ) {
        if(key === 13){
            $('.selected').dblclick();
        }
    });
    
    // Buscador
    $('#buscar_codigoProducto').on('keyup',function(){
        table{{$var}}.column(1).search(this.value).draw();
    });
    $('#buscar_nombreProducto').on('keyup',function(){
        table{{$var}}.column(2).search(this.value).draw();
    });
    $('#buscar_nombreCategoria').on('keyup',function(){
        table{{$var}}.column(3).search(this.value).draw();
    });
    $('#buscar_nombreMarca').on('keyup',function(){
        table{{$var}}.column(4).search(this.value).draw();
    });
    $('#buscar_estadotvProducto').on('change',function(){
        table{{$var}}.column(7).search(this.value).draw();
    });
    $('#buscar_estadoProducto').on('change',function(){
        table{{$var}}.column(8).search(this.value).draw();
    });

    $('#tabla_bsucador_{{$tabla}}').on('keyup', function (e) {
        table{{$var}}.search(this.value).draw();
    });
  
    //$('#{{$tabla}} > tbody > tr:first-child > td:first-child').click();
  
</script>
@if(isset($script))
@endsection
@endif