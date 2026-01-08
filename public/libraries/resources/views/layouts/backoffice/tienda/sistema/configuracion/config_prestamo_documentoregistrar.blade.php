<div class="tabs-container" id="tab-menudocumentoregistrar">
  <ul class="tabs-menu">
    <li class="current"><a href="#tab-menudocumentoregistrar-0" id="tab-cobranza-general">Registrar Documento</a></li>
    <li><a href="#tab-menudocumentoregistrar-1" id="tab-cobranza-leyenda">Leyenda</a></li>
  </ul>
  <div class="tab">
    <div id="tab-menudocumentoregistrar-0" class="tab-content" style="display: block;">
      <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Registrar Documentos</span>
          <a class="btn btn-success" href="javascript:;" onclick="index_documento()"><i class="fa fa-angle-left"></i> Atras</a></a>
        </div>
      </div>
        <form action="javascript:;" 
              onsubmit="callback({
                  route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion',
                  method: 'POST',
                  data:   {
                      view: 'registrar-documento',
                      documento_registrar_contenido: CKEDITOR.instances.documento_registrar_contenido.getData()
                  }
              },
              function(resultado){
                  index_documento();
              },this)">
        <div class="row">
          <div class="col-sm-12">
            <label>Nombre *</label>
            <input type="text" id="documento_registrar_nombre" onkeyup="texto_mayucula(this)">
          </div>
          <div class="col-sm-12" style="text-align: left;float:left">
            <textarea id="documento_registrar_contenido"></textarea>
          </div>
        </div>
        <button type="submit" class="btn mx-btn-post">Guardar Documento</button>
      </form>
    </div>
    <div id="tab-menudocumentoregistrar-1" class="tab-content" style="display: none;">
      
        <div class="row">
            <div class="col-md-4">
              <table class='table'>
                  <tr>
                    <th colspan="2" style='padding: 8px;text-align: center;background-color: #31353c;color: #fff;'>Empresa</th>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Nombre Comercial</td>
                    <td style='padding: 8px;text-align: left;'>[agencia_nombrecomercial]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Razón Social</td>
                    <td style='padding: 8px;text-align: left;'>[agencia_razonsocial]|</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>RUC</td>
                    <td style='padding: 8px;text-align: left;'>[agencia_ruc]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Domicilio Fiscal</td>
                    <td style='padding: 8px;text-align: left;'>[agencia_direccion]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Distrito - Provincia - Departamento</td>
                    <td style='padding: 8px;text-align: left;'>[agencia_ubigeo]</td>
                  </tr>
                  <tr>
                    <th colspan="2" style='padding: 8px;text-align: center;background-color: #31353c;color: #fff;'>Representante Legal</th>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>DNI</td>
                    <td style='padding: 8px;text-align: left;'>[agencia_representante_dni]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Nombre</td>
                    <td style='padding: 8px;text-align: left;'>[agencia_representante_nombre]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Apellidos</td>
                    <td style='padding: 8px;text-align: left;'>[agencia_representante_apellidos]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Cargo</td>
                    <td style='padding: 8px;text-align: left;'>[agencia_representante_cargo]</td>
                  </tr>
                  <tr>
                    <th colspan="2" style='padding: 8px;text-align: center;background-color: #31353c;color: #fff;'>Documento</th>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Correlativo de Pagaré</td>
                    <td style='padding: 8px;text-align: left;'>[documento_pagare_correlativo]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Fecha Actual</td>
                    <td style='padding: 8px;text-align: left;'>[documento_fechaactual]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Hora Actual</td>
                    <td style='padding: 8px;text-align: left;'>[documento_horaactual]</td>
                  </tr>
              </table>
            </div>
            <div class="col-md-4">
              <table class='table'>
                  <tr>
                    <th colspan="2" style='padding: 8px;text-align: center;background-color: #31353c;color: #fff;'>Cliente</th>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>DNI</td>
                    <td style='padding: 8px;text-align: left;'>[cliente_dni]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Nombre</td>
                    <td style='padding: 8px;text-align: left;'>[cliente_nombre]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Apellidos</td>
                    <td style='padding: 8px;text-align: left;'>[cliente_apellidos]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Dirección</td>
                    <td style='padding: 8px;text-align: left;'>[cliente_direccion]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Distrito - Provincia - Departamento</td>
                    <td style='padding: 8px;text-align: left;'>[cliente_ubigeo]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Estado Civil</td>
                    <td style='padding: 8px;text-align: left;'>[cliente_estadocivil]</td>
                  </tr>
                  <tr>
                    <th colspan="2" style='padding: 8px;text-align: center;background-color: #31353c;color: #fff;'>Conyugue</th>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>DNI</td>
                    <td style='padding: 8px;text-align: left;'>[conyugue_dni]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Nombre</td>
                    <td style='padding: 8px;text-align: left;'>[conyugue_nombre]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Apellidos</td>
                    <td style='padding: 8px;text-align: left;'>[conyugue_apellidos]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Dirección</td>
                    <td style='padding: 8px;text-align: left;'>[conyugue_direccion]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Distrito - Provincia - Departamento</td>
                    <td style='padding: 8px;text-align: left;'>[conyugue_ubigeo]</td>
                  </tr>
                  <tr>
                    <th colspan="2" style='padding: 8px;text-align: center;background-color: #31353c;color: #fff;'>Garante</th>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>DNI</td>
                    <td style='padding: 8px;text-align: left;'>[garante_dni]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Nombre</td>
                    <td style='padding: 8px;text-align: left;'>[garante_nombre]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Apellidos</td>
                    <td style='padding: 8px;text-align: left;'>[garante_apellidos]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Dirección</td>
                    <td style='padding: 8px;text-align: left;'>[garante_direccion]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Distrito - Provincia - Departamento</td>
                    <td style='padding: 8px;text-align: left;'>[garante_ubigeo]</td>
                  </tr>
              </table>
            </div>
            <div class="col-md-4">
              <table class='table'>
                  <tr>
                    <th colspan="2" style='padding: 8px;text-align: center;background-color: #31353c;color: #fff;'>Crédito</th>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Fecha de Desembolso</td>
                    <td style='padding: 8px;text-align: left;'>[credito_fechadesembolso]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Ultima Fecha de Pago</td>
                    <td style='padding: 8px;text-align: left;'>[credito_ultimafechapago]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Monto Desembolsado</td>
                    <td style='padding: 8px;text-align: left;'>[credito_monto]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Tasa de Crédito</td>
                    <td style='padding: 8px;text-align: left;'>[credito_tasacredito]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Número de Cuotas</td>
                    <td style='padding: 8px;text-align: left;'>[credito_numerocuota]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Cuota</td>
                    <td style='padding: 8px;text-align: left;'>[credito_cuota]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Total a Pagar</td>
                    <td style='padding: 8px;text-align: left;'>[credito_totalapagar]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Frecuencia de Pagos</td>
                    <td style='padding: 8px;text-align: left;'>[credito_frecuencia]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Garantias</td>
                    <td style='padding: 8px;text-align: left;'>[credito_garantias]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Total de Garantias</td>
                    <td style='padding: 8px;text-align: left;'>[credito_garantias_total]</td>
                  </tr>
                  <tr>
                    <th colspan="2" style='padding: 8px;text-align: center;background-color: #31353c;color: #fff;'>Negocio</th>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Nombre</td>
                    <td style='padding: 8px;text-align: left;'>[credito_negocio_nombre]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Giro</td>
                    <td style='padding: 8px;text-align: left;'>[credito_negocio_giro]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Actividad</td>
                    <td style='padding: 8px;text-align: left;'>[credito_negocio_actividad]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Dirección</td>
                    <td style='padding: 8px;text-align: left;'>[credito_negocio_direccion]</td>
                  </tr>
                  <tr>
                    <td style='padding: 8px;text-align: left;'>Distrito - Provincia - Departamento</td>
                    <td style='padding: 8px;text-align: left;'>[credito_negocio_ubigeo]</td>
                  </tr>
              </table>
            </div>
        </div>
    </div>
  </div>
</div>

<script>
    tab({click:'#tab-menudocumentoregistrar'});

    CKEDITOR.replace('documento_registrar_contenido', {
      height: 600,
      /*toolbarGroups: [
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
        { name: 'forms', groups: [ 'forms' ] },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
        { name: 'links', groups: [ 'links' ] },
        { name: 'insert', groups: [ 'insert' ] },
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'colors', groups: [ 'colors' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'others', groups: [ 'others' ] },
        { name: 'about', groups: [ 'about' ] }
      ],
      removeButtons: 'Source,Save,ExportPdf,Print,Cut,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Iframe,About',*/
      contentsCss: [
        'https://cdn.ckeditor.com/4.19.0/full-all/contents.css',
        'https://ckeditor.com/docs/ckeditor4/4.19.0/examples/assets/css/pastefromword.css'
      ],
      bodyClass: 'document-editor'
    });
</script>