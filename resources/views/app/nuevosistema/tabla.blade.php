<?php
$estado_activado = DB::table('s_estado')->whereId(1)->first();
$estado_desactivado = DB::table('s_estado')->whereId(2)->first();
$idtabla = explode('#',$tabla);
if($idtabla){
    $idtabla = $idtabla[1];
}
if(!isset($check_id)){
    $check_id = '';
}

?>
<div id="load-tabla"></div>
<div class="table-responsive-sm">
<table class="table table-striped table-hover " id="{{$idtabla}}" style="width:100%;">
    @if(isset($thead))
    <thead class="table-dark">
      <tr>
        @foreach($thead as $value)
        @if(isset($value['data']))
        <th <?php echo isset($value['width']) ? 'width="'.$value['width'].'"':'' ?>>{{$value['data']}}</th>
        @endif
        @endforeach
      </tr>
    </thead>
    @endif
    @if(isset($tfoot))
    <tfoot>
      <tr>
        @foreach($tfoot as $value)
          @if(isset($value['type']))
            @if($value['type']=='text')
            <th style="background-color: #144081 !important;"><input type="text" class="form-control" placeholder="Buscar..."></th>
            @elseif($value['type']=='date')
            <th style="background-color: #144081 !important;"><input type="date" class="form-control" style="width: 145px;"></th>
            @elseif($value['type']=='check')
            <th style="background-color: #144081 !important;"><div class="form-check"><input class="form-check-input" type="checkbox" onclick="check_seleccionar_todo{{$check_id}}(this)">
              </div>
            </th>
            @elseif($value['type']=='select')
            <th style="background-color: #144081 !important;">
              <select class="form-select" style="width: 100%;">
                <option value="">Buscar...</option>
                <?php
                $data = $value['data'];
                if($value['data']=='json:estado'){
                    $data = file_get_contents(getcwd().'/public/nuevosistema/librerias/json/estado.json');
                    $data = json_decode($data);
                    $data = $data->data;
                } 
                elseif($value['data']=='json:tipopersona'){
                    $data = file_get_contents(getcwd().'/public/nuevosistema/librerias/json/tipopersona.json');
                    $data = json_decode($data);
                    $data = $data->data;
                }   
                elseif($value['data']=='json:tipomovimiento'){
                    $data = file_get_contents(getcwd().'/public/nuevosistema/librerias/json/tipomovimiento.json');
                    $data = json_decode($data);
                    $data = $data->data;
                } 
                elseif($value['data']=='json:tipodocumento'){
                    $data = file_get_contents(getcwd().'/public/nuevosistema/librerias/json/tipodocumento.json');
                    $data = json_decode($data);
                    $data = $data->data;
                } 
                ?>
                @foreach($data as $listval)
                  <?php 
                  if(is_array($listval)) {
                      $listval = (object) $listval;
                  }
                  ?>
                  <option value="{{$listval->id}}">{{$listval->text}}</option>
                @endforeach
              </select>
            </th>
            @else
            <th style="background-color: #144081 !important;"></th>
            @endif
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
  table.dataTable td.dt-control {
    text-align: center;
  }
  table.dataTable td.dt-control:before {
    height: 15px;
    width: 15px;
    margin-top: -1px;
    display: inline-block;
    color: white;
    border: 0.15em solid white;
    border-radius: 1em;
    box-shadow: 0 0 0.2em #444;
    box-sizing: content-box;
    text-align: center;
    text-indent: 0px !important;
    font-family: "Courier New",Courier,monospace;
    line-height: 16px;
    content: "+";
    background-color: #31b131;
    cursor: pointer;
}
  table.dataTable tr.shown td.dt-control:before {
    content: "-";
    background-color: #d33333;
}
</style>
<?php
$name_table = generateRandomString();
/*function generar_token_seguro($longitud)
{
    if ($longitud < 4) {
        $longitud = 4;
    }
 
    return bin2hex(random_bytes(($longitud - ($longitud % 2)) / 2));
}*/
?>
<script>
    function format_stock(d) {
        let th_sucursal = ``;
        let tr_unidadmedida   = ``;
        $.each(d.db_stock, function( key, value ) {
            let td_stock = ``;
            $.each(d.db_presentacion, function( key_p, values_p ) {
                td_stock   += `<td style="text-align: center;">${stock_presentacion(value.stock,values_p.por)}</td>`;
            });
            th_sucursal += `<tr>
                <th>${value.sucursal}</th>
                ${td_stock}
            </tr>`;
        });
        $.each(d.db_presentacion, function( key, value ) {
            tr_unidadmedida   += `<th style="text-align: center;">${value.unidadmedidanombre}</th>`;
        });
        let html = `
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 170px;text-align: center;">SUCURSAL</th>
                                ${tr_unidadmedida}
                            </tr>
                        </thead>
                        <tbody>
                            ${th_sucursal}
                        </tbody>
                    </table>`;
        return (html);
    }
  
    var {{$name_table}} = $('{{$tabla}}').DataTable({
        @if(isset($type))
        @if($type=='GET')
        processing: true,
        serverSide: true,
        @endif
        @endif
        ajax: {
            url: '{{$route}}',
            @if(isset($type))
            type: '{{$type}}',
            @endif
            /*error: function(xhr, error, code) {
                alert('hay errro!!')
            }*/
        },
        dom: '{{isset($dom)?$dom:'rti'}}',
        scrollX: true,
        mark: true,
        select: {
            toggleable: false,
            selector: 'td:not(:nth-child(10))'
        },
        keys: true,
        scrollY: {{isset($scrollY)?$scrollY:350}},
        scroller: {
            loadingIndicator: true
        },
        ordering: false,
        colReorder: false,
        //order: [[ 1, "asc" ]],
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
        initComplete: function () {
          this.api().columns().every( function () {
              var that = this;
              $( 'input[type="text"]', this.footer() ).on( 'keyup change clear', function () {
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
        },
        createdRow: function( row, data, dataIndex ) {
            $(row).addClass(data.style);
            $(row).attr('data-valor-columna', data.id);
            if(data.click){
              $(row).attr('onclick', 'show_data(this)'); 
             }
            // Agregar la función onclick a cada fila
            $(row).attr('style', data.style);
            //console.log(data.style)
        },
        columns: [
            @foreach($tbody as $value)
                  @if(isset($value['type']))
                      @if($value['type']=='img')
                      {
                          render: function ( data, type, full, meta ) {
                              var img = '<div class="img" style="background-image: url({{url('/public/backoffice/sistema/sin_imagen_cuadrado.png')}});"></div>';
                              if(full.{{$value['data']}}!=''){
                                  img = '<div class="img" style="background-image: url({{url('/public/backoffice/tienda/')}}'+full.{{$value['data']}}+');"></div>';
                              }
                              return img;
                          },
                          className: 'table-type-img',
                          orderable: false,
                      },
                      @elseif($value['type']=='badge')
                      {
                          render: function ( data, type, full, meta ) {
                              var estado = '<span class="badge rounded-pill text-bg-dark">{{$estado_desactivado->nombre}}</span>';
                              if(full.{{$value['data']}}==1){
                                  estado = '<span class="badge rounded-pill text-bg-success">{{$estado_activado->nombre}}</span>';
                              }
                              return estado;
                          },
                          className: 'table-type-badge'
                      },
                      @elseif($value['type']=='check')
                      {
                          render: function ( data, type, full, meta ) {
                              return '<div class="form-check"><input class="form-check-input" type="checkbox" id="check_seleccionar{{$check_id}}" data="'+full.{{$value['data']}}+'"></div>';
                          },
                          className: 'table-type-check',
                          orderable: false,
                      },
                      @elseif($value['type']=='code')
                      { 
                          data: "{{$value['data']}}",
                          className: 'table-type-code',
                      },
                      @elseif($value['type']=='date')
                      { 
                          data: "{{$value['data']}}",
                          className: 'table-type-date',
                      },
                      @elseif($value['type']=='text')
                      { 
                          data: "{{$value['data']}}",
                          className: 'table-type-text',
                      },
                      @elseif($value['type']=='num')
                      { 
                          data: "{{$value['data']}}",
                          className: 'table-type-num',
                      },
                      @elseif($value['type']=='ajust')
                      { 
                          data: "{{$value['data']}}",
                          className: 'table-type-ajust',
                      },
                      @elseif($value['type']=='money')
                      { 
                          data: "{{$value['data']}}",
                          className: 'table-type-money',
                      },
                      @elseif($value['type']=='select')
                      { 
                          data: "{{$value['data']}}",
                          className: 'table-type-select',
                      },
                      @elseif($value['type']=='estado')
                      { 
                          data: "{{$value['data']}}",
                          className: 'table-type-estado',
                      },
                      @elseif($value['type']=='detail_stock')
                      {  
                          className: 'dt-control',
                          orderable: false,
                          data: null,
                          defaultContent: '',
                      },
                      @elseif($value['type']=='btn')
                      {  
                          render: function ( data, type, full, meta ) {
                              var btn = '';
                              if(full.{{$value['data']}}[0]!=undefined){
                                 btn = '<div class="dropdown" id="menu-opcion">'+
                                            '<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">'+
                                              'Opción'+
                                            '</button>'+
                                            '<ul class="dropdown-menu dropdown-menu-end">'+
                                            '</ul>'+
                                          '</div>';
                              }
                            
                              return btn;
                          },
                          className: 'table-type-btn',
                          orderable: false, 
                      },
                      @endif
                  @else
                  @endif
            @endforeach
        ],
    });
  
    // MOSTRAR ERROR DE TABLA
    $.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) { 
        console.log(message);
    };

    {{$name_table}}.on('click', 'tr', function () {
        var data = {{$name_table}}.row(this).data();
        $('{{$tabla}} tbody .selected').removeClass('selected');
        $(this).addClass('selected');
    });
  
    
    //  MOSTRAR DETALLE
    {{$name_table}}.on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = {{$name_table}}.row(tr);
 
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(format_stock(row.data())).show();
            tr.addClass('shown');
        }
    });
                        
    // Se muestra el botón opción
    {{$name_table}}.on( 'click', 'div#menu-opcion', function () {

        var data    = {{$name_table}}.row($(this).parents('tr')).data();
        var row     = $(this).find('ul');
        var opcion_html = '';
        $.each(data.opcion, function( key, value ) {
            if(value.nombre!=undefined){
                var route = 'modal({route:\'{{url('/backoffice')}}'+value.onclick+'\'})';
                opcion_html = opcion_html+'<li><a class="dropdown-item" href="javascript:;" onclick="'+route+'"><i class="fa-solid fa-'+value.icono+'"></i> '+value.nombre+'</a></li>';
            }
        });
        $(row).html(opcion_html);
    });
  
    // subir y bajar por medio de teclado
    {{$name_table}}.on('key-focus', function (e, datatable, cell) {
        datatable.rows().deselect();
        datatable.row( cell.index().row ).select();
   
    });
    // enter para abrir detalle
    {{$name_table}}.on( 'key', function ( e, datatable, key, cell, originalEvent ) {
        if(key === 13){
            $('.selected').dblclick();
        }
    });
                        
    function check_seleccionar_todo{{$check_id}}(pthis){
        var select_check = $(pthis).is(':checked');
        {{$name_table}}.rows((idx,data,node)=>{
          
            var input = $(node).find(`input#check_seleccionar{{$check_id}}`);
            if(select_check==true){
                input.prop("checked",true);
            }else{
                input.prop("checked",false);
            }
            //console.log(select_check)
        });
      
        // asignar
        var input_asignado = [];
        {{$name_table}}.rows((idx,data,node)=>{
            var input = $(node).find("input");
            if(input.prop("checked")==true){
                input_asignado.push(input.attr("data"));
            }
        });
      
        if(JSON.stringify(input_asignado)=='[]'){
            $('#{{$check_id}}').val('');
        }else{
            $('#{{$check_id}}').val(JSON.stringify(input_asignado));
        }
    };
    
    {{$name_table}}.on('click', `input#check_seleccionar{{$check_id}}`, function () {
        // asignar
        var input_asignado = [];
        {{$name_table}}.rows((idx,data,node)=>{
            var input = $(node).find("input#check_seleccionar{{$check_id}}");
            if(input.prop("checked")==true){
                input_asignado.push(input.attr("data"));
            }
        });
      
        if(JSON.stringify(input_asignado)=='[]'){
            $('#{{$check_id}}').val('');
        }else{
            $('#{{$check_id}}').val(JSON.stringify(input_asignado));
        }
    });
                        
</script>