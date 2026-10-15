<div class="modal-header">
    <h5 class="modal-title">REPORTE LISTA NEGRA</h5>
    <button type="button" class="btn-close" id="modal-close-usuario-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="row">
                    <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="idagencia">
                            <option></option>
                            @foreach($agencias as $value)
                                <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                </div> 
                <div class="col-sm-12 col-md-1">
                    <button type="button" class="btn btn-success" onclick="verpdf()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div id="cont_loading"></div>
                    <iframe id="iframe_listanegra" frameborder="0" width="100%" style="height: calc(-215px  + 100vh)"></iframe>
                    <!-- <iframe src="{{ url('/backoffice/'.$tienda->id.'/listanegra/0/edit?view=reportepdf') }}#zoom=100" frameborder="0" width="100%" height="600px"></iframe> -->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
    
    verpdf();
    function verpdf(){
        let idagencia = $('#idagencia').val();

        load('#cont_loading');
        $('#iframe_listanegra').addClass('d-none');

        // 1. Registrar el evento ANTES de cambiar el src
        $('#iframe_listanegra').off('load').on('load', function(){
            if($(this).attr('src') !== ''){
                $('#cont_loading').html('');
                $(this).removeClass('d-none');
            }
        });

        // 2. Reset y luego asignar nuevo src
        $('#iframe_listanegra').attr('src', '');

        setTimeout(function(){
            $('#iframe_listanegra').attr('src',
                '{{ url('/backoffice/'.$tienda->id.'/listanegra/0/edit?view=reportepdf') }}'
                + '&idagencia=' + idagencia
                + '#zoom=100'
            );
        }, 100);
    }
</script>