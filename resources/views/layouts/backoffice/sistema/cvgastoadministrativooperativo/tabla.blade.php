<div class="modal-header">
    <h5 class="modal-title">
        Gasto Administrativo y Operativo
        <a href="javascript:;" 
            class="btn btn-primary" 
            onclick="load_nuevo_gastoadministrativooperativo()">
            <i class="fa-solid fa-plus"></i> Nuevo
        </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>

<div class="modal-body">
    <div class="row">
        @if (!$apertura_caja)
        <div class="modal-body" style="position: absolute; z-index: 100;">
            <div class="alert bg-danger" style="height: 150px;">
            <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
            Falta aperturar caja.
            </div>
        </div>
        @elseif($arqueocaja)
            <div class="modal-body" style="position: absolute; z-index: 100;">
                <div class="alert bg-danger" style="height: 150px;">
                <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
                Ya esta arqueado la caja!!
                </div>
            </div>
        @endif
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-2" id="form-result-giro">
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-2">
                <div class="modal-body">
                    <div class="row">
                    <label class="col-sm-3 col-form-label" style="text-align: right;">Fecha inicio</label>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" id="fechainicio" value="{{now()->format('Y-m-d')}}">
                    </div>
                    <label class="col-sm-1 col-form-label" style="text-align: right;">Fecha fin:</label>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" id="fechafin" value="{{now()->format('Y-m-d')}}">
                    </div>
                    <div class="col-sm-3">
                        <button type="button" class="btn btn-primary" onclick="lista_gastoadministrativooperativo()" style="font-weight: bold;">
                            <i class="fa-solid fa-search"></i> Filtrar
                        </button>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body" style="overflow-y: scroll;height: 300px;padding: 0;margin-top: 5px;overflow-x: scroll;">
                
                <table class="table table-striped table-hover" id="table-lista-gastoadministrativooperativo">
                    <thead class="table-dark" style="position: sticky;top: 0; font-weight: bold;">
                        <tr>
                            <td rowspan="2" width="10px" >N°</td>
                            <td rowspan="2" >Operación</td>
                            <td rowspan="2" >Monto (S/.)</td>
                            <td rowspan="2" >Fecha de gasto</td>
                            <td rowspan="2" >Descripción</td>
                            <td colspan="2" style="text-align: center;">Sustento</td>
                            <td rowspan="2" >F. Pago</td>
                            <td rowspan="2" >Banco</td>
                            <td rowspan="2" >Validación</td>
                            <td rowspan="2" >Usuario</td>
                        </tr>
                        <tr>
                            <td >Comprobante</td>
                            <td >N° y Detalle de Comp.</td>
                        </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
    <div style="text-align: right;">
        <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
            <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF
        </button>
    </div>
</div>
<script>
    lista_gastoadministrativooperativo();
    function lista_gastoadministrativooperativo(id){
        var fechainicio = $('#fechainicio').val();
        var fechafin = $('#fechafin').val();
        $.ajax({
            url:"{{url('backoffice/0/cvgastoadministrativooperativo/show_table')}}",
            type:'GET',
            data:{
                fechainicio: $('#fechainicio').val(),
                fechafin: $('#fechafin').val(),
            },
            success: function (res){
                $('#table-lista-gastoadministrativooperativo > tbody').html(res.html);
            }
        })
    }
    load_nuevo_gastoadministrativooperativo();
    function load_nuevo_gastoadministrativooperativo(){
        pagina({ route:"{{url('backoffice/'.$tienda->id.'/cvgastoadministrativooperativo/create?view=registrar')}}", result:'#form-result-giro'});
    }

    function show_data(e) {
        let id = $(e).attr('data-valor-columna');

        $('tr.selected').removeClass('selected');
        $(e).addClass('selected');
        pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/cvgastoadministrativooperativo/"+id+"/edit?view=editar", result:'#form-result-giro'});
    }

    function validar(idgastoadministrativooperativo){
        modal({ route:"{{ url('backoffice/'.$tienda->id) }}/cvgastoadministrativooperativo/"+idgastoadministrativooperativo+"/edit?view=validar",  size: 'modal-sm' });
    }

    function exportar_pdf(){
        let url = "{{ url('backoffice/'.$tienda->id) }}/cvgastoadministrativooperativo/0/edit?view=exportar&fechainicio="+$('#fechainicio').val()+
            "&fechafin="+$('#fechafin').val();
        modal({ route: url,size:'modal-fullscreen' })
    }
</script>

