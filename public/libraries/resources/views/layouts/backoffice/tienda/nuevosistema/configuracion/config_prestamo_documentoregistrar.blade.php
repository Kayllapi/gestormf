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
      <form action="javascript:;" onsubmit="registrarDocumento(this)">
        <div class="row">
          <div class="col-sm-12">
            <label>Nombre *</label>
            <input type="text" id="documento_registrar_nombre">
          </div>
          <div class="col-sm-12" style="text-align: left;float:left">
            <textarea id="documento_registrar_contenido"></textarea>
          </div>
        </div>
        <button type="submit" class="btn mx-btn-post">Guardar Documento</button>
      </form>
    </div>
    <div id="tab-menudocumentoregistrar-1" class="tab-content" style="display: none;">
      <table class='table'>
        <thead style='background: #31353d; color: #fff;'>
          <tr>
            <th style='padding: 8px;text-align: left;'>Nombre</th>
            <th style='padding: 8px;text-align: left;'>Descripción</th>
            <th width="10px"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style='padding: 8px;text-align: left;'>Nombre de empresa</td>
            <td style='padding: 8px;text-align: left;'>|agencia_nombrecomercial|</td>
            <td>
              <button class="btn mx-btn-post">
                <i class="fas fa-copy"></i> Copiar
              </button>
            </td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Representante de la empresa</td>
            <td style='padding: 8px;text-align: left;'>|agencia_representante_nombre|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Apellidos del representante</td>
            <td style='padding: 8px;text-align: left;'>|agencia_representante_apellidos|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Dirección del representante</td>
            <td style='padding: 8px;text-align: left;'>|agencia_representante_direccion|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Razón Social de la empresa</td>
            <td style='padding: 8px;text-align: left;'>|agencia_razonsocial|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Ruc de la empresa</td>
            <td style='padding: 8px;text-align: left;'>|agencia_ruc|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Nombre del cliente</td>
            <td style='padding: 8px;text-align: left;'>|nombrecliente|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Apellidos del cliente</td>
            <td style='padding: 8px;text-align: left;'>|apellidoscliente|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Identificación del cliente</td>
            <td style='padding: 8px;text-align: left;'>|dnicliente|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Dirección del cliente</td>
            <td style='padding: 8px;text-align: left;'>|direccioncliente|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Estado civil del cliente</td>
            <td style='padding: 8px;text-align: left;'>|estadocivilcliente|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Nombre del conyuge</td>
            <td style='padding: 8px;text-align: left;'>|nombreconyugue|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Apellidos del conyuge</td>
            <td style='padding: 8px;text-align: left;'>|apellidosconyugue|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Identificación del conyuge</td>
            <td style='padding: 8px;text-align: left;'>|dniconyugue|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Identificación del garante</td>
            <td style='padding: 8px;text-align: left;'>|dnigarante|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Fecha de desembolso</td>
            <td style='padding: 8px;text-align: left;'>|fechadesembolso|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Monto del crédito</td>
            <td style='padding: 8px;text-align: left;'>|montocredito|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Tasa del crédito</td>
            <td style='padding: 8px;text-align: left;'>|tasacredito|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Cantidad de numero de cuotas</td>
            <td style='padding: 8px;text-align: left;'>|numerocuota|</td>
          </tr>
          <tr>
            <td style='padding: 8px;text-align: left;'>Monto total del crédito</td>
            <td style='padding: 8px;text-align: left;'>|montocuota|</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
tab({click:'#tab-menudocumentoregistrar'});
</script>

<link href="https://kothing.github.io/editor/dist/css/kothing-editor.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.css"/>
<script src="https://kothing.github.io/editor/dist/kothing-editor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.js"></script>
<script>
  var editorRegistar = editor('documento_registrar_contenido');
  function editor(input) {
      var editorData = KothingEditor.create(input, {
          display: "block",
          width: "100%",
          height: "auto",
          popupDisplay: "full",
          katex: katex,
          toolbarItem: [
              ["undo", "redo"],
              ["font", "fontSize", "formatBlock"],
              [
                "bold",
                "underline",
                "italic",
                "strike",
                "subscript",
                "superscript",
                "fontColor",
                "hiliteColor",
              ],
              ["outdent", "indent", "align", "list", "horizontalRule"],
              ["link", "table", "image", "audio", "video"],
              ["lineHeight", "paragraphStyle", "textStyle"],
              ["showBlocks", "codeView"],
              ["math"],
              ["preview", "print", "fullScreen"],
              ["save", "template"],
              ["removeFormat"],
          ],
          templates: [
            {
              name: "Template-1",
              html: "<p>HTML source1</p>",
            },
            {
              name: "Template-2",
              html: "<p>HTML source2</p>",
            },
          ],
          charCounter: true,
      });
      return editorData;
  }
  
  function registrarDocumento(pthis){
      callback({
          route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion',
          method: 'POST',
          data:   {
              view: 'registrar-documento',
              documento_registrar_contenido: editorRegistar.getContents()
          }
      },
      function(resultado){
          index_documento();
      },pthis)
  }
</script>