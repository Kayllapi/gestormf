
<div class="table-responsive" style="float: left;">
<table class="table" id="{{$tabla}}">
    <thead class="thead-dark">
      <tr>
        @foreach($thead as $value)
        <th <?php echo isset($value['width']) ? 'width="'.$value['width'].'"':'' ?>>{{$value['data']}}</th>
        @endforeach
      </tr>
    </thead>
    <tfoot>
      <tr>
        @foreach($tfoot as $value)
            @if($value['input']=='text')
            <th><input type="text" placeholder="Buscar..."></th>
            @elseif($value['input']=='date')
            <th><input type="date" placeholder="Buscar..." style="padding: 7.5px;"></th>
            @elseif($value['input']=='select')
            <th><select style="padding: 9px;border-radius: 5px;width: 100%;border: 1px solid #d3d8de;">
                <option value="">Buscar...</option>
                @foreach($value['option'] as $listval)
                  <option value="{{$listval['id']}}">{{$listval['nombre']}}</option>
                @endforeach
              </select></th>
            @else
            <th></th>
            @endif
        @endforeach
      </tr>
    </tfoot>
</table>
</div>

@section('scriptsapp')
<script>

            $.fn.dataTableExt.ofnSearch['string'] = function ( data ) {
            return ! data ? '' :
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
       
            var table = $('#{{$tabla}}').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url : '{{$route}}',
                },
                dom:        'rtip',
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
                initComplete: function () {
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
                },
                columns: [
                    @foreach($tbody as $value)
                      @if(isset($value['render']))
                          @if($value['render']=='estado')
                          {
                              render: function ( data, type, full, meta ) {
                                  var estado = '';
                                  @if(isset($value['renderdata']))
                                      @foreach($value['renderdata'] as $valuerenderdata)
                                          if(full.{{$value['data']}}=={{$valuerenderdata['id']}}){
                                              estado = '<span class="badge badge-pill badge-{{$valuerenderdata['class']}}"><i class="{{$valuerenderdata['icon']}}"></i> {{$valuerenderdata['nombre']}}</span>';
                                          }
                                      @endforeach
                                  @else 
                                      if(full.{{$value['data']}}==1){
                                          estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span>';
                                      }else if(full.{{$value['data']}}==2){
                                          estado = '<span class="badge badge-pill badge-dark">Desactivado</span>';
                                      }
                                  @endif
                                  return estado;
                              },
                              orderable: false
                          },  
                          @elseif($value['render']=='lista')
                          {
                              render: function ( data, type, full, meta ) {
                                  var estado = '';
                                  @if(isset($value['renderdata']))
                                      @foreach($value['renderdata'] as $valuerenderdatalista)
                                          if(full.{{$value['data']}}=={{$valuerenderdatalista['id']}}){
                                              estado = '{{$valuerenderdatalista['nombre']}}';
                                          }
                                      @endforeach
                                  @endif
                                  return estado;
                              },
                              orderable: false
                          },                             
                          @elseif($value['render']=='imagen')
                          {
                              render: function ( data, type, full, meta ) {
                                  var imagen = '<img src="{{ url('public/backoffice/tienda') }}/'+full.idtienda+'/sistema/'+full.{{$value['data']}}+'" height="40px">';
                                  if(full.{{$value['data']}}==null){
                                      imagen = '<img src="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}" height="40px">';
                                  }
                                  return imagen;
                              },
                              orderable: false
                          },    
                          @endif
                      @elseif(isset($value['content']))
                          {   data: null, 
                              defaultContent: '<div class="header-user-menu menu-option" id="menu-opcion">'+
                                                '<a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>'+
                                                '<ul>'+
                                                '</ul>'+
                                            '</div>',
                              orderable: false, 
                          },
                      @else
                          { data: '{{$value['data']}}' },
                      @endif
                    @endforeach
                ]
            });
          
            // Se muestra el botón opción
            $('#{{$tabla}} tbody').on( 'click', 'div#menu-opcion', function () {
                // cargar menu tabla
                $("ul",this).toggleClass("hu-menu-vis");
                $("i",this).toggleClass("fa-angle-up");
                // fin cargar menu tabla
                var data    = table.row($(this).parents('tr')).data();
                var row     = $(this).find('ul');
              
                var html_li = '';
              
                @foreach($tbody as $value)
                    @if(isset($value['content']))
                        @foreach($value['content'] as $valuecontent)
                            <?php 
                            $btnroute = $valuecontent['route'];
                            $cant = explode('{',$valuecontent['route']);
                            for($i = 1; $i < count($cant); $i++) {
                                $firstIndex = stripos($btnroute, "{");
                                $lastIndex = stripos($btnroute, "}");
                                $texto = substr($btnroute,$firstIndex+1,$lastIndex-$firstIndex-1);
                                $btnroute = str_replace('{'.$texto.'}','\'+data.'.$texto.'+\'',$btnroute);
                                echo 'console.log(data.'.$texto.');';
                            }
                            ?>
                        html_li = html_li+'<li><a href="javascript:;" onclick="pagina({route:\'<?php echo $btnroute ?>\',result:\'#mx-subcuerpo\'})"><i class="{{$valuecontent['icon']}}"></i> {{$valuecontent['nombre']}}</a></li>';
                        @endforeach
                    @endif
                @endforeach
                $(row).html(html_li);
            });
          

            $("div#menu-opcion").on("click", function () {
                $("ul",this).toggleClass("hu-menu-vis");
                $("i",this).toggleClass("fa-angle-up");
            });

            
</script>
@endsection

