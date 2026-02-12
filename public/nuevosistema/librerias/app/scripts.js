function ir_sistema(){
    $('#cont-menu-modulo').css('display','none');
    $('#mx-cont-modulo-titulo').html('');
    $('.btn_sistema').css('display','block');
    $('.btn_modulo').css('display','none');
    $('.btn_submodulo').css('display','none');
    $('.btn_opcion').css('display','none');
}
function ir_modulo(){
    $('#cont-menu-modulo').css('display','block');
    $('#mx-cont-modulo-titulo').html('<a href="javascript:;" class="btn btn-light">Sistema</a>');

    $('.btn_sistema').css('display','none');
    $('.btn_modulo').css('display','block');
    $('.btn_submodulo').css('display','none');
    $('.btn_opcion').css('display','none');
}
function ir_submodulo(idmodulo,nombremodulo){
    $('#mx-cont-modulo-titulo').html('<a href="javascript:;" class="btn btn-light" onclick="ir_modulo()">Sistema</a>'+
          '<a href="javascript:;" class="btn btn-primary">'+nombremodulo+'</a>');
    $('#mx-cont-modulo-titulo').attr('idmodulo',idmodulo);
    $('#mx-cont-modulo-titulo').attr('nombremodulo',nombremodulo);

    $('.btn_modulo').css('display','none');
    $('a#btn_submodulo_'+idmodulo).css('display','block');
    $('a#btn_submodulo_atras_'+idmodulo).css('display','block');
    $('.btn_opcion').css('display','none');
}
function ir_opcion(idmodulo,nombremodulo,idsubmodulo,nombresubmodulo){
    $('#mx-cont-modulo-titulo').html('<a href="javascript:;" class="btn btn-light" onclick="ir_modulo()">Sistema</a>'+
          '<a href="javascript:;" class="btn btn-light" onclick="ir_submodulo('+idmodulo+',\''+nombremodulo+'\')">'+nombremodulo+'</a>'+
          '<a href="javascript:;" class="btn btn-primary">'+nombresubmodulo+'</a>');

    $('.btn_submodulo').css('display','none');
    $('a#btn_opcion_'+idsubmodulo).css('display','block');
    $('a#btn_opcion_atras_'+idsubmodulo).css('display','block');
}
          
          
/* =================================================  STOCK */
function sistema_stock(param){
    json({
        route: raiz()+'public/backoffice/tienda/'+param['idtienda']+'/sistema_json/producto.json',
        search: {
            id : param['idproducto']
        }
    },
    function(resultado){
        let th_sucursal = ``;
        let tr_unidadmedida   = ``;
        $.each(resultado.db_stock, function( key, value ) {
            let td_stock = ``;
            $.each(resultado.db_presentacion, function( key_p, values_p ) {
                td_stock   += `<td style="text-align: center;">${stock_presentacion(value.stock,values_p.por)}</td>`;
            });
            th_sucursal += `<tr>
                <th>${value.sucursal}</th>
                ${td_stock}
            </tr>`;
        });
        $.each(resultado.db_presentacion, function( key, value ) {
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
        if(param['input']!=undefined){
            $(param['input']).html(html);
        }else{        
            console.log(html)
            return html;
        }
    });
}
/* =================================================  SELECT 2 */
function sistema_select2(param){
    if(param['json']!=undefined){
        var jsondata = param['json'].split(':');
        var urltienda = '';
        var modulo = param['json'];
        if(jsondata.length>1){
            urltienda = jsondata[0];
            modulo = jsondata[1];
        }

        var ruta = '';
        if(urltienda=='tienda'){
            /*if(modulo=='facturacionboletafactura' || modulo=='ventacredito'){
                ruta = raiz()+'public/backoffice/tienda/'+param['idtienda']+'/sistema_json/'+modulo+'_'+param['idtienda']+'.json?token='+Math.floor((Math.random() * 100) + 1);
            }else{*/
                //ruta = raiz()+'public/backoffice/tienda/'+param['idtienda']+'/sistema_json/'+modulo+'.json?token='+Math.floor((Math.random() * 100) + 1);
                ruta = raiz()+'backoffice/'+param['idtienda']+'/usuario/show_usuario';
            //}
        }else{
            ruta = raiz()+'public/nuevosistema/librerias/json/'+modulo+'.json?token='+Math.floor((Math.random() * 100) + 1);
        }
      
 
        $.getJSON(ruta).done(function(data) {
          
            var textominimo = 0;
            if(modulo=='usuario' || 
                modulo=='usuarioacceso' || 
                modulo=='ubigeo' || 
                modulo=='facturacionboletafactura'){
                
                if(param['search']=='false'){
                    $(param['input']).select2({
                        data: data.data,
                        matcher: function (params, data) {
                            if ($.trim(params.term) === '') {
                                return data;
                            }

                            terms=(params.term).split(' ');

                            for (var i = 0; i < terms.length; i++) {
                                if (((data.text).toUpperCase()).indexOf((terms[i]).toUpperCase()) == -1) 
                                return null;
                            }
                            return data;
                        },
                        placeholder: '-- Seleccionar --',
                        theme: 'bootstrap-5',
                        dropdownParent: $(param['input']).parent(),
                    });
                 }else{
                    $(param['input']).select2({
                        data: data.data,
                        matcher: function (params, data) {
                            if ($.trim(params.term) === '') {
                                return data;
                            }

                            terms=(params.term).split(' ');

                            for (var i = 0; i < terms.length; i++) {
                                if (((data.text).toUpperCase()).indexOf((terms[i]).toUpperCase()) == -1) 
                                return null;
                            }
                            return data;
                        },
                        placeholder: '-- Seleccionar --',
                        theme: 'bootstrap-5',
                        dropdownParent: $(param['input']).parent(),
                        minimumInputLength: 2,
                    });
                 }
                       
                
            }
          else if(modulo=='unidadmedida'){
                $(param['input']).select2({
                    data: data.data,
                    matcher: function (params, data) {
                        if ($.trim(params.term) === '') {
                            return data;
                        }

                        terms=(params.term).split(' ');

                        for (var i = 0; i < terms.length; i++) {
                            if (((data.text).toUpperCase()).indexOf((terms[i]).toUpperCase()) == -1) 
                            return null;
                        }
                        return data;
                    },
                    placeholder: '-- Seleccionar --',
                    theme: 'bootstrap-5',
                    dropdownParent: $(param['input']).parent(),
                });   
                
            }else if(modulo=='producto'){
                $(param['input']).select2({
                    data: data.data,
                    matcher: function (params, data) {
                        if ($.trim(params.term) === '') {
                            return data;
                        }
                        terms=(params.term).split(' ');
                        for (var i = 0; i < terms.length; i++) {
                            if (((data.text).toUpperCase()).indexOf((terms[i]).toUpperCase()) == -1) 
                            return null;
                        }
                        return data;
                    },
                    placeholder: '-- Seleccionar --',
                    theme: 'bootstrap-5',
                    dropdownParent: $(param['input']).parent(),
                    minimumInputLength: 2,
                    templateResult: function (state) {
                        if (!state.id) {
                            return state.text;
                        }
                        return $('<div>'+
                                 '<div style=\'background-image: url('+raiz()+'public/backoffice/sistema/'+state.imagen+');'+
                                            'background-repeat: no-repeat;'+
                                            'background-size: contain;'+
                                            'background-position: center;'+
                                            'width: 44px;'+
                                            'height: 44px;'+
                                            'float: left;'+
                                            'margin-right: 5px;'+
                                            'margin-top: -10px;\'>'+
                                          '</div><div>'+(state.codigo!=''?state.codigo+' - ':'')+state.nombre+'</div><div>'+state.preciopublico+' '+state.unidadmedidanombre+'</div>');
                    },
                    templateSelection: function (repo) {
                        if (!repo.id) {
                            return repo.text;
                        }
                        if(repo.codigo!=''){
                            if(repo.codigo==undefined){
                                return $('<span>'+repo.text+'</span>');
                            }
                            return $('<span>'+(repo.codigo!=''?repo.codigo+' - ':'')+repo.nombre+'</span>');
                        }else{
                            return $('<span>'+repo.nombre+'</span>');
                        }
                    },
                }); 
            }else{
                $(param['input']).select2({
                    data: data.data,
                    matcher: function (params, data) {
                        if ($.trim(params.term) === '') {
                            return data;
                        }
                        terms=(params.term).split(' ');
                        for (var i = 0; i < terms.length; i++) {
                            if (((data.text).toUpperCase()).indexOf((terms[i]).toUpperCase()) == -1) 
                            return null;
                        }
                        return data;
                    },
                    placeholder: '-- Seleccionar --',
                    theme: 'bootstrap-5',
                    dropdownParent: $(param['input']).parent(),
                    minimumResultsForSearch: -1,
                }); 
            }   
            if(param['val']!=undefined){
                if(param['val']!=''){
                    $(param['input']).val(param['val']).trigger('change');
                }
            }
        });
    }else{
      
        $(param['input']).select2({
            placeholder: '-- Seleccionar --',
            minimumResultsForSearch: -1,
            theme: 'bootstrap-5',
            dropdownParent: $(param['input']).parent()
        });
        if(param['val']!=undefined){
            if(param['val']!=''){
                $(param['input']).val(param['val']).trigger('change');
            }
        }
    }

}

/* =================================================  FORMA DE PAGO */
function sistema_formapago(param){
    const fecha = new Date();
    $(param['input']).html('<div class="mb-1">'+
               '<select class="form-select" id="formapago_idformapago">'+
                   '<option></option>'+
               '</select>'+
           '</div>'+
           '<div class="mb-1">'+
               '<div id="cont-formapago1">'+
                   '<table class="table" id="tabla-formapago" style="margin-bottom: 3px;">'+
                     '<thead>'+
                       '<tr style="background: #31353d; color: #fff;">'+
                         '<th style="padding: 8px;">Tipo de Pago</th>'+
                         '<th width="10px"><a href="javascript:;" onclick="formapago_agregar('+param['idtienda']+')" class="btn btn-warning"><i class="fa fa-plus"></i></a></th>'+
                       '</tr>'+
                     '</thead>'+
                     '<tbody num="0" id="tbody-formapago">'+
                     '</tbody>'+
                   '</table>'+
                   '<label>Total Efectivo</label>'+
                   '<input type="text" class="form-control" value="0.00" id="formapago_totalefectivo" disabled>'+
                   '<label>Total Depósito</label>'+
                   '<input type="text" class="form-control" value="0.00" id="formapago_totaldeposito" disabled>'+
                   '<label>Total Pagado</label>'+
                   '<input type="text" class="form-control" value="0.00" id="formapago_totalpagado" disabled>'+
               '</div>'+
               '<div id="cont-formapago2" style="display:none;">'+
                   '<div class="mb-1">'+
                       '<label>Fecha de Inicio *</label>'+
                       '<input type="date" class="form-control" id="formapago_credito_fechainicio" value="'+fecha+'">'+
                   '</div>'+
                   '<div class="mb-1">'+
                       '<label>Última Fecha *</label>'+
                       '<input type="date" class="form-control" id="formapago_credito_ultimafecha" value="'+fecha+'">'+
                   '</div>'+
               '</div>'+
           '</div>');
    let selected = param['selected'] ? param['selected'] : 1;
    sistema_select2({ idtienda:param['idtienda'], json:'formapago', input:'#formapago_idformapago', val: selected });

    if(selected == 1){
        $('#cont-formapago1').css('display','none');
        $('#cont-formapago2').css('display','none');
        $('#cont-formapago1').css('display','block');
    }else if(selected == 2){
        $('#cont-formapago1').css('display','none');
        $('#cont-formapago2').css('display','none');
        $('#cont-formapago2').css('display','block');
    }
    $("#formapago_idformapago").on("select2:select", function(e) {
        $('#cont-formapago1').css('display','none');
        $('#cont-formapago2').css('display','none');
        if(e.params.data.id==1){
            $('#cont-formapago1').css('display','block');
        }
        else if(e.params.data.id==2){
            $('#cont-formapago2').css('display','block');
        }
    });

    formapago_agregar(param['idtienda']);
}
function formapago_agregar(idtienda){
    var num = $("#tabla-formapago > tbody#tbody-formapago").attr('num');
    var btn_eliminar = '';
    if(num>0){
        btn_eliminar = '<a href="javascript:;" onclick="formapago_eliminar('+num+')" class="btn btn-danger"><i class="fa fa-trash"></i></a>';    
    }
    var nuevaFila='<tr id="'+num+'">'+
                     '<td>'+
                       '<table class="table">'+
                         '<tbody>'+
                           '<tr>'+
                             '<td width="90px">Pago *</td>'+
                             '<td>'+
                               '<select class="form-select" id="formapago_idtipopago'+num+'">'+
                                 '<option></option>'+
                               '</select>'+
                             '</td>'+
                           '</tr>'+
                         '</tbody>'+
                       '</table>'+
                       '<div id="cont-tipopago1'+num+'">'+
                         '<table class="table">'+
                           '<tbody>'+
                             '<tr>'+
                               '<td width="90px">Monto *</td>'+
                               '<td><input type="number" class="form-control" id="formapago_efectivo_montoefectivo'+num+'" value="0.00" onclick="formapago_calcularmonto();" onkeyup="formapago_calcularmonto();"/></td>'+
                             '</tr>'+
                           '</tbody>'+
                         '</table>'+
                       '</div>'+
                       '<div id="cont-tipopago2'+num+'" style="display:none;">'+
                         '<table class="table">'+
                           '<tbody>'+
                             '<tr>'+
                               '<td width="90px">Cuenta Bancaria *</td>'+
                               '<td colspan="2">'+
                                 '<select class="form-select" id="formapago_deposito_idcuentabancaria'+num+'">'+
                                   '<option></option>'+
                                 '</select>'+
                               '</td>'+
                             '</tr>'+
                             '<tr>'+
                               '<td>Nro de Operación *</td>'+
                               '<td colspan="2"><input type="text" class="form-control" id="formapago_deposito_numerooperacion'+num+'"/></td>'+
                             '</tr>'+
                             '<tr>'+
                               '<td>Monto *</td>'+
                               '<td colspan="2"><input type="number" class="form-control" id="formapago_deposito_montodeposito'+num+'" value="0.00" onclick="formapago_calcularmonto();" onkeyup="formapago_calcularmonto();" step="0.01"/></td>'+
                             '</tr>'+
                           '</tbody>'+
                         '</table>'+
                       '</div>'+
                     '</td><td>'+btn_eliminar+'</td>'+
                   '</tr>';

    $("#tabla-formapago > tbody#tbody-formapago").append(nuevaFila);
    $("#tabla-formapago > tbody#tbody-formapago").attr('num',parseInt(num)+1);  

    sistema_select2({ idtienda:idtienda, json:'tipopago', input:'#formapago_idtipopago'+num, val:1 });
    sistema_select2({ idtienda:idtienda, json:'tienda:cuentabancaria', input:'#formapago_deposito_idcuentabancaria'+num });

    $("#formapago_idtipopago"+num).on("select2:select", function(e) {
        $('#cont-tipopago1'+num).css('display','none');
        $('#cont-tipopago2'+num).css('display','none');
        if(e.params.data.id==1){
            $('#cont-tipopago1'+num).css('display','block');
        }
        else if(e.params.data.id==2){
            $('#cont-tipopago2'+num).css('display','block');
        }
        formapago_calcularmonto();
    });
}
function formapago_eliminar(num){
    $("#tabla-formapago > tbody#tbody-formapago > tr#"+num).remove();
    formapago_calcularmonto();
}
function formapago_data(){
    var data = [];
    $("#tabla-formapago > tbody#tbody-formapago > tr").each(function() {
        var num = $(this).attr('id');      
        data.push({
            'num' : num,
            'formapago_idtipopago'                : $('#formapago_idtipopago'+num+' :selected').val(),
            'formapago_efectivo_montoefectivo'    : $('#formapago_efectivo_montoefectivo'+num).val(),
            'formapago_deposito_idcuentabancaria' : $('#formapago_deposito_idcuentabancaria'+num+' :selected').val(),
            'formapago_deposito_banco'            : $('#formapago_deposito_idcuentabancaria'+num+' :selected').attr('formapago_banco'),
            'formapago_deposito_numerocuenta'     : $('#formapago_deposito_idcuentabancaria'+num+' :selected').attr('formapago_numerocuenta'),
            'formapago_deposito_numerooperacion'  : $('#formapago_deposito_numerooperacion'+num).val(),
            'formapago_deposito_montodeposito'    : $('#formapago_deposito_montodeposito'+num).val(),
            //'formapago_deposito_voucher'          : $('#formapago_deposito_voucher'+num).prop("files")[0],
        });
    });
    return JSON.stringify(data);
} 
function formapago_calcularmonto(){
    var totalefectivo = 0;
    var totaldeposito = 0;
    $("#tabla-formapago > tbody#tbody-formapago > tr").each(function() {
        var num = $(this).attr('id');      
        var formapago_idtipopago = $('#formapago_idtipopago'+num+' :selected').val();
        if(formapago_idtipopago==1){
            var formapago_efectivo_montoefectivo =  $('#formapago_efectivo_montoefectivo'+num).val()!=''?$('#formapago_efectivo_montoefectivo'+num).val():0;
            totalefectivo = totalefectivo+parseFloat(formapago_efectivo_montoefectivo);
        }
        else if(formapago_idtipopago==2){
            var formapago_deposito_montodeposito =  $('#formapago_deposito_montodeposito'+num).val()!=''?$('#formapago_deposito_montodeposito'+num).val():0;
            totaldeposito = totaldeposito+parseFloat(formapago_deposito_montodeposito);
        }
    });

    var formapago_totalpago = parseFloat($('#formapago_totalpago').val());
    $('#formapago_totalefectivo').val(totalefectivo.toFixed(2));
    $('#formapago_totaldeposito').val(totaldeposito.toFixed(2));
    var totalpagado = totalefectivo+totaldeposito;
    $('#formapago_totalpagado').val(parseFloat(totalpagado).toFixed(2));
}