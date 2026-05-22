<div class="modal-header">
  <h5 class="modal-title">Gestión de Cobranza Institucional</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
            <div class="modal-body">
                <div class="row">
                  <div class="col-sm-12 col-md-4">
                    <div class="row">
                      <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                      <div class="col-sm-9">
                          <select class="form-control" id="idagencia" disabled>
                            <option></option>
                            @foreach($agencias as $value)
                                <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                            @endforeach
                          </select>
                      </div>
                    </div>
                    <div class="row">
                      <label for="fecha_fin" class="col-sm-3 col-form-label">EJECUTIVO</label>
                      <div class="col-sm-9">
                        <select class="form-control" id="idasesor">
                          <option value="0">TODOS</option>
                          <?php
                            $usuarios = DB::table('users')
                              ->join('users_permiso','users_permiso.idusers','users.id')
                              ->join('permiso','permiso.id','users_permiso.idpermiso')
                              ->whereIn('users_permiso.idpermiso',[3,4,7])
                              ->where('users_permiso.idtienda',$tienda->id)
                              ->select('users.*','permiso.nombre as nombrepermiso')
                              ->get();
                          ?>
                          @foreach($usuarios as $value)
                            <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12 col-md-6">
                    <div class="row">
                      <div class="col-sm-12 col-md-5">
                        <div class="row">
                          <label for="fecha_fin" class="col-sm-7 col-form-label">DÍAS VENCIDOS <span style="float:right;">DE</span></label>
                          <div class="col-sm-5">
                              <input type="number" class="form-control" value="" id="dias_retencion_desde">
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12 col-md-5">
                        <div class="row">
                          <label for="fecha_fin" class="col-sm-3 col-form-label"><span style="float:right;">HASTA</span></label>
                          <div class="col-sm-5">
                              <input type="number" class="form-control" value="" id="dias_retencion_hasta">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 col-md-12" style="background-color: #bababa;border-radius: 5px;padding: 5px 10px;">
                        <div>
                          <div style="float: left;">LEYENDA: &nbsp;</div>
                          <div style="float: left;">0 DÍAS</div>
                          <div style="float: left;background-color: #fff;height: 10px;width: 20px;margin: 5px;margin-right: 20px;"></div> 
                          <div style="float: left;">1-{{configuracion($tienda->id,'dias_tolerancia_garantia')['valor']}} DÍAS</div>
                          <div style="float: left;background-color: #c0ee87;height: 10px;width: 20px;margin: 5px;margin-right: 20px;"></div> 
                          <div style="float: left;">> A {{configuracion($tienda->id,'dias_tolerancia_garantia')['valor']}} DÍAS</div>
                          <div style="float: left;background-color: #ffc9ca;height: 10px;width: 20px;margin: 5px;margin-right: 20px;"></div> 
                          <div style="float: left;">COMPRO.</div>
                          <div style="float: left;background-color: #fcbb59;height: 10px;width: 20px;margin: 5px;margin-right: 20px;"></div> 
                          <div style="float: left;">COMP. VENC.</div>
                          <div style="float: left;background-color: #fb9494;height: 10px;width: 20px;margin: 5px;"></div> 
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12 col-md-2">
                    <div class="row">
                      <div class="col-12">
                        <button type="button" class="btn btn-success" style="float: left;" onclick="lista_credito()">
                          <i class="fa-solid fa-search"></i> FILTRAR
                        </button>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <button type="button" class="btn btn-info mt-1" onclick="exportar_pdf()" style="font-weight: bold; margin-top: 0.3rem !important;">
                          <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
            </div> 
          </div>
        </div>
      </div>
  </div>
      <div class="col-sm-12">
        <div class="card">
          {{-- <div class="card-body">
            <style>
              .linea-1{text-decoration: underline; font-weight: bold;}
              .linea-2{background-color: #ffc9ca !important;text-decoration: underline;font-weight: bold;}
            </style>
            @include('app.nuevosistema.tabla',[
              'tabla' => '#table-lista-credito',
              'route' => url('backoffice/'.$tienda->id.'/gestioncobranza/showtable_data?idagencia='.$tienda->id.'&dias_retencion_desde=&dias_retencion_hasta=&idasesor='.Auth::user()->id),
              'check_id' => 'check_origen',
              'scrollY' => 'calc(-332px  + 100vh)',
              'dom' => 'rt',
              'thead' => [
                  ['data' => '' ],
                  ['data' => 'N°'],
                  ['data' => 'GP'],
                  ['data' => 'CUENTA'],
                  ['data' => 'DOI/RUC'],
                  ['data' => 'Apellidos y Nombres'],
                  ['data' => 'Fecha Desemb.'],
                  ['data' => 'Monto Crédito (S/.)'],
                  ['data' => 'F. Pago'],
                  ['data' => 'Saldo Cuotas Venc. (S/.)', 'class' => 'linea-1'],
                  ['data' => 'Días Vencido', 'class' => 'linea-2'],
                  ['data' => 'Form. C.'],
                  ['data' => 'Nro. de Cuotas Cumplido y Venc.'],
                  ['data' => 'Tele./Celu.'],
                  ['data' => 'F. Compromiso'],
                  ['data' => 'Anotación'],
                  ['data' => 'Direc/Domicilio'],
                  ['data' => 'Calificación'],
                  ['data' => 'Producto'],
                  ['data' => 'Modalidad'],
                  ['data' => 'DOI/RUC (Aval)'],
                  ['data' => 'Ape. Nom. Aval'],
                  ['data' => 'Ejecutivo'],
              ],
              'tbody' => [
                  ['data' => 'opcion','type'=>'btn'],
                  ['data' => 'numero','type'=>'text'],
                  ['data' => 'gp','type'=>'text'],
                  ['data' => 'cuenta','type'=>'text'],
                  ['data' => 'identificacioncliente','type'=>'text'],
                  ['data' => 'nombrecliente','type'=>'text'],
                  ['data' => 'fecha_desembolso','type'=>'text'],
                  ['data' => 'monto_solicitado','type'=>'text'],
                  ['data' => 'frecuencianombre','type'=>'text'],
                  ['data' => 'cuota_vencida','type'=>'text'],
                  ['data' => 'ultimo_atraso','type'=>'text'],
                  ['data' => 'cp','type'=>'text'],
                  ['data' => 'cuotas','type'=>'text'],
                  ['data' => 'telefonocliente','type'=>'text'],
                  ['data' => 'fechacompromiso','type'=>'text'],
                  ['data' => 'comentario','type'=>'text'],
                  ['data' => 'direccioncliente','type'=>'text'],
                  ['data' => 'clasificacion','type'=>'text'],
                  ['data' => 'nombreproductocredito','type'=>'text'],
                  ['data' => 'nombremodalidadcredito','type'=>'text'],
                  ['data' => 'identificacionaval','type'=>'text'],
                  ['data' => 'nombreaval','type'=>'text'],
                  ['data' => 'codigoasesor','type'=>'text'],
              ],
              'tfoot' => [
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => 'text'],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
                  ['type' => ''],
              ]
            ])
          </div> --}}
          <div class="card-body" style="overflow-y: scroll;height: calc(100vh - 230px);padding: 0;margin-top: 5px;overflow-x: scroll;">
            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <th style="width:10px;"></th>
                  <th>N°</th>
                  <th>GP</th>
                  <th>CUENTA</th>
                  <th>DOI/RUC</th>
                  <th>Apellidos y Nombres</th>
                  <th>Fecha Desemb.</th>
                  <th>Monto Crédito (S/.)</th>
                  <th>F. Pago</th>
                  <th><span style="text-decoration: underline; font-weight: bold;">Saldo Cuotas Venc. (S/.)</span></th>
                  <th><span style="background-color: #ffc9ca !important;text-decoration: underline;font-weight: bold;">Días Vencido</span></th>
                  <th>Form. C.</th>
                  <th>Nro. de Cuotas Cumplido y Venc.</th>
                  <th>Tele./Celu.</th>
                  <th>F. Compromiso</th>
                  <th>Anotación</th>
                  <th>Direc/Domicilio</th>
                  <th>Calificación</th>
                  <th>Producto</th>
                  <th>Modalidad</th>
                  <th>DOI/RUC (Aval)</th>
                  <th>Ape. Nom. Aval</th>
                  <th>Ejecutivo</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
</div>
<style>
table .dropdown {
    position: inherit;
}
</style>
<script>

  sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
  sistema_select2({ input:'#idasesor' });
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente' });
  
  lista_credito();
  function lista_credito(){
    $.ajax({
      url:"{{url('backoffice/0/gestioncobranza/showtable')}}",
      type:'GET',
      data: {
          idagencia : $('#idagencia').val(),
          idasesor : $('#idasesor').val(),
          dias_retencion_desde : $('#dias_retencion_desde').val(),
          dias_retencion_hasta : $('#dias_retencion_hasta').val(),
      },
      success: function (res){
        $('#table-lista-credito > tbody').html(res.html);
        $("tr#show_data_select").on("click", function() {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        });
      }
    })
  }
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/gestioncobranza/"+id+"/edit?view=compromisopago" });  
  }
  function show_estadocuenta(e) {
    let id = $(e).attr('estadocuenta-valor-columna');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/gestioncobranza/"+id+"/edit?view=estadocuenta", size: 'modal-fullscreen' });  
  }
  function show_notificacion(e) {
    let id = $(e).attr('notificacion-valor-columna');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/gestioncobranza/"+id+"/edit?view=notificacion", size: 'modal-fullscreen' });  
  }
 
   function vistapreliminar(){
      let idcredito = $('#table-lista-credito > tbody > tr.selected').attr('idcredito');
                        
      if(idcredito == "" || idcredito == undefined ){
        alert('Debe de seleccionar un crédito.');   
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/desembolsado/"+idcredito+"/edit?view=desembolsar";
      modal({ route: url, size: 'modal-fullscreen' })
   }
  
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/gestioncobranza/0/edit?view=exportar&dias_retencion_desde="+$('#dias_retencion_desde').val()+
          "&dias_retencion_hasta="+$('#dias_retencion_hasta').val()+
          "&idagencia="+$('#idagencia').val()+
          "&idasesor="+$('#idasesor').val();
      modal({ route: url,size:'modal-fullscreen' })
   }
  
   function exportar_excel(){
        window.location.href = '{{url('backoffice/'.$tienda->id.'/gestioncobranza/0/edit')}}?view=exportar_excel&dias_retencion_desde='+$('#dias_retencion_desde').val()+
              '&dias_retencion_hasta='+$('#dias_retencion_hasta').val()+
              '&idagencia='+$('#idagencia').val();
    }

    $(document).on(
        'keyup',
        '#buscar_cuenta, #buscar_identificacion, #buscar_nombre, #buscar_formac',
        function () {

            let cuenta = $('#buscar_cuenta').val().toLowerCase();
            let identificacion = $('#buscar_identificacion').val().toLowerCase();
            let nombre = $('#buscar_nombre').val().toLowerCase();
            let formac = $('#buscar_formac').val().toLowerCase();

            $('#table-lista-credito tbody tr').each(function () {

                if ($(this).hasClass('fila-total')) {
                    return;
                }

                let tdCuenta = $(this).find('.td-cuenta');
                let tdIdentificacion = $(this).find('.td-identificacion');
                let tdNombre = $(this).find('.td-nombre');
                let tdFormac = $(this).find('.td-formac');

                let textoCuenta = tdCuenta.text();
                let textoIdentificacion = tdIdentificacion.text();
                let textoNombre = tdNombre.text();
                let textoFormac = tdFormac.text();

                // limpiar highlights
                tdCuenta.html(textoCuenta);
                tdIdentificacion.html(textoIdentificacion);
                tdNombre.html(textoNombre);
                tdFormac.html(textoFormac);

                let matchCuenta = textoCuenta.toLowerCase().includes(cuenta);
                let matchIdentificacion = textoIdentificacion.toLowerCase().includes(identificacion);
                let matchNombre = textoNombre.toLowerCase().includes(nombre);
                let matchFormac = textoFormac.toLowerCase().includes(formac);

                let mostrar =
                    matchCuenta &&
                    matchIdentificacion &&
                    matchNombre &&
                    matchFormac;

                $(this).toggle(mostrar);

                // highlight
                if (mostrar) {

                    highlight(tdCuenta, cuenta);
                    highlight(tdIdentificacion, identificacion);
                    highlight(tdNombre, nombre);
                    highlight(tdFormac, formac);

                }

            });

        }
    );

    function highlight(td, textoBuscar) {

        if (textoBuscar == '') {
            return;
        }

        let original = td.text();

        let regex = new RegExp(`(${textoBuscar})`, 'gi');

        let nuevo = original.replace(
            regex,
            '<mark>$1</mark>'
        );

        td.html(nuevo);
    }
</script>  

