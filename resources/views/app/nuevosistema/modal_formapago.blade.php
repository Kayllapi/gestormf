<div id="carga-pago">
<div class="modal-header">
    <h5 class="modal-title">Forma de Pago</h5>
    <button type="button" class="btn-close" id="modal-close-formapago-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="mb-1">
        <label>Total a Pagar</label>
        <input type="text" class="form-control" value="{{$_GET['totalpago']}}" id="formapago_totalpago" disabled>
    </div>
    <div class="mb-1">
        <label>Forma de pago *</label>
        <select class="form-select" id="idformapago">
            <option></option>
        </select>
    </div>
    <div class="mb-1">
        <div id="cont-formapago1"></div>
        <div id="cont-formapago2" style="display:none;">
            <div class="mb-1">
                <label>Fecha de Inicio *</label>
                <input type="date" class="form-control" id="formapago_credito_fechainicio" value="{{ date('Y-m-d') }}">
            </div>
            <div class="mb-1">
                <label>Última Fecha *</label>
                <input type="date" class="form-control" id="formapago_credito_ultimafecha" value="{{ date('Y-m-d') }}">
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
        <button type="button" 
                onclick="formapago_pago()" 
                class="btn btn-primary"><i class="fa-solid fa-check"></i> Realizar Pago</button>
</div>
</div>
<script>
    formapago();
    formapago_agregar();
    @include('app.nuevosistema.select2',['json'=>'formapago','input'=>'#idformapago','val'=>1])
  
    $("#idformapago").on("select2:select", function(e) {
        $('#cont-formapago1').css('display','none');
        $('#cont-formapago2').css('display','none');
        if(e.params.data.id==1){
            $('#cont-formapago1').css('display','block');
        }
        else if(e.params.data.id==2){
            $('#cont-formapago2').css('display','block');
        }
    });
  
    function formapago_pago(){
      
        callback({
            route: '{{ url('backoffice/tienda/nuevosistema') }}',
            method: 'POST',
            carga: '#carga-pago',
            data:{
                view                          : 'modal_formapago',  
                idformapago                   : $('#idformapago option:selected').val(),
                formapago_idmoneda            : {{$_GET['idmoneda']}},
                formapago_seleccionar         : formapago_seleccionar(),
                formapago_totalpago           : $('#formapago_totalpago').val(),
                formapago_contado_efectivo    : $('#formapago_totalefectivo').val(),
                formapago_contado_deposito    : $('#formapago_totaldeposito').val(),
                formapago_contado_pagado      : $('#formapago_totalpagado').val(),
                formapago_credito_fechainicio : $('#formapago_credito_fechainicio').val(),
                formapago_credito_ultimafecha : $('#formapago_credito_ultimafecha').val(),
            }
        },
        function(resultado){
            formapago_registrar({
                idformapago         : resultado['idformapago'],
                idformapagodetalle  : resultado['idformapagodetalle'],
                monto_efectivo      : resultado['monto_efectivo'],
                monto_deposito      : resultado['monto_deposito'],
                monto_total         : resultado['monto_total'],
            })
            $('#modal-close-formapago-registrar').click(); 
        });
        
    }
  
    function formapago(){
        $('#cont-formapago1').html('<table class="table" id="tabla-formapago" style="margin-bottom: 3px;">'+
               '<thead>'+
                 '<tr style="background: #31353d; color: #fff;">'+
                   '<th style="padding: 8px;">Tipo de Pago</th>'+
                   '<th width="10px"><a href="javascript:;" onclick="formapago_agregar()" class="btn btn-warning"><i class="fa fa-plus"></i></a></th>'+
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
             '<input type="text" class="form-control" value="0.00" id="formapago_totalpagado" disabled>');
    }
    function formapago_agregar(){
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
                                   '<td><input type="number" class="form-control" id="formapago_efectivo_montoefectivo'+num+'" onclick="formapago_calcularmonto();" onkeyup="formapago_calcularmonto();"/></td>'+
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
                                   '<td colspan="2"><input type="number" class="form-control" id="formapago_deposito_montodeposito'+num+'" onclick="formapago_calcularmonto();" onkeyup="formapago_calcularmonto();" step="0.01"/></td>'+
                                 '</tr>'+
                                 '<tr>'+
                                   '<td>Voucher *</td>'+
                                   '<td colspan="2">'+
                                       '<input type="file" class="form-control" id="formapago_deposito_voucher'+num+'" class="file">'+
                                   '</td>'+
                                 '</tr>'+
                               '</tbody>'+
                             '</table>'+
                           '</div>'+
                         '</td><td>'+btn_eliminar+'</td>'+
                       '</tr>';

        $("#tabla-formapago > tbody#tbody-formapago").append(nuevaFila);
        $("#tabla-formapago > tbody#tbody-formapago").attr('num',parseInt(num)+1);  

        @include('app.nuevosistema.select2',['json'=>'tipopago','input'=>"#formapago_idtipopago/+num+/",'val'=>1])
        @include('app.nuevosistema.select2',['json'=>'tienda:cuentabancaria','input'=>"#formapago_deposito_idcuentabancaria/+num+/"])
      
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
      
        // subir voucher
        file({click:'#formapago_deposito_voucher'+num});
        
    }
    function formapago_eliminar(num){
        $("#tabla-formapago > tbody#tbody-formapago > tr#"+num).remove();
        formapago_calcularmonto();
    }
    function formapago_seleccionar(){
        var data = [];
        $("#tabla-formapago > tbody#tbody-formapago > tr").each(function() {
            var num = $(this).attr('id');      
            data.push({
                'num' : num,
                'formapago_tipopago'                  : $('#formapago_idtipopago'+num+' :selected').val(),
                'formapago_efectivo_montoefectivo'    : $('#formapago_efectivo_montoefectivo'+num).val(),
                'formapago_deposito_idcuentabancaria' : $('#formapago_deposito_idcuentabancaria'+num+' :selected').val(),
                'formapago_deposito_banco'            : $('#formapago_deposito_idcuentabancaria'+num+' :selected').attr('formapago_banco'),
                'formapago_deposito_numerocuenta'     : $('#formapago_deposito_idcuentabancaria'+num+' :selected').attr('formapago_numerocuenta'),
                'formapago_deposito_numerooperacion'  : $('#formapago_deposito_numerooperacion'+num).val(),
                //'formapago_deposito_fecha'            : $('#formapago_fecha'+num).val(),
                //'formapago_deposito_hora'             : $('#formapago_hora'+num).val(),
                'formapago_deposito_montodeposito'    : $('#formapago_deposito_montodeposito'+num).val(),
                //'formapago_deposito_montocontado'     : $('#formapago_montocontado'+num).val(),
                'formapago_deposito_voucher'          : $('#formapago_deposito_voucher'+num).prop("files")[0],
            });
        });
      
        //return JSON.stringify(data);
        return data;
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
</script>