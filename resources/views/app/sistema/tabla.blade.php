<div class="table-responsive" style="float: left;">
<table class="table" id="{{$tabla}}" style="width: 100%;">
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

<style>
  .columdata{
      width: unset !important;
  }
  .table td {
    height: 45px;
}
</style>
@if(isset($script))
@section($script)
@endif
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
            <?php $var = rand(100000000, 999999999) ?>
       
            var table{{$var}} = $('#{{$tabla}}').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url : '{{$route}}',
                    @if(isset($data))
                    data: {
                        @foreach($data as $key => $value)
                        '{{$key}}': '{{$value}}',
                        @endforeach
                    }
                    @endif
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
                createdRow: function( row, data, dataIndex ) {
                    $(row).addClass(data.classname);
                },
                columns: [
                    @foreach($tbody as $value)
                      @if(isset($value['render']))
                          @if($value['render']=='opcion')
                          {   data: null, 
                              defaultContent: '<div class="header-user-menu menu-option" id="menu-opcion{{$var}}">'+
                                                '<a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>'+
                                                '<ul>'+
                                                '</ul>'+
                                            '</div>',
                              orderable: false
                          },
                          @endif
                      @else
                          { 
                              data: '{{$value['data']}}'
                          },
                      @endif
                    @endforeach
                ]
            });
          
            // Se muestra el botón opción
            $('#{{$tabla}} tbody').on( 'click', 'div#menu-opcion{{$var}}', function () {
                // cargar menu tabla
                $("ul",this).toggleClass("hu-menu-vis");
                $("i",this).toggleClass("fa-angle-up");
                // fin cargar menu tabla
                var data    = table{{$var}}.row($(this).parents('tr')).data();
                var row     = $(this).find('ul');
                $(row).html(data.opcion);
            });
          

            $("div#menu-opcion{{$var}}").on("click", function () {
                $("ul",this).toggleClass("hu-menu-vis");
                $("i",this).toggleClass("fa-angle-up");
            });

            
</script>
@if(isset($script))
@endsection
@endif