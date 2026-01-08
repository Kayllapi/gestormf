<div class="custom-form">
    <label>Forma de Pago *</label>
    <select id="idformapago" disabled>
        <option></option>
        <option value="1">CONTADO</option>
        <option value="2">CRÉDITO</option>
    </select>
    <div id="cont-formapago-contado" style="display:none;">
      <table class="table" id="tabla-tipopago" style="margin-bottom: 3px;">
        <thead>
          <tr style="background: #31353d; color: #fff;">
            <th style="padding: 8px;">Depósito</th>
            <th width="10px"><a href="javascript:;" onclick="formapago_contado_agregar()" class="btn btn-warning"><i class="fa fa-plus"></i></a></th>
          </tr>
        </thead>
        <tbody num="0" id="tbody-formapago">
        </tbody>
      </table>
      <div class="row">
        <div class="col-sm-6">
            <div style="background-color: #11c529;padding: 10px;border-radius: 5px;font-size: 20px;font-weight: bold;text-align: center;color: #fff;margin-bottom: 5px;">
              Efectivo: <span id="totalmonto_efectivo">0.00</span>
            </div>
        </div>
        <div class="col-sm-6">
            <div style="background-color: #e0b609;padding: 10px;border-radius: 5px;font-size: 20px;font-weight: bold;text-align: center;color: #fff;margin-bottom: 5px;">
              Depósito: <span id="totalmonto_deposito">0.00</span> 
            </div>
        </div>
      </div>                   
    </div>
    <div id="cont-formapago-credito" style="display:none;">
    <table class="table" id="tabla-formapago">
       <thead class="thead-dark">
         <tr>
           <th>Nro</th>
           <th width="40%">Ultima Fecha</th>
           <th width="40%">Importe</th>
           <th width="10px" style="padding: 0px;padding-right: 1px;">
           <a href="javascript:;" class="btn  color-bg flat-btn" onclick="formapago_credito_agregar()"><i class="fa fa-plus"></i></a>
           </th>
         </tr>
       </thead>
       <tbody num="0"></tbody>
    </table>
    </div>
</div>
<?php
$cuentabancarias = DB::table('s_cuentabancaria')
    ->where('s_cuentabancaria.idtienda', $tienda->id)
    ->where('s_cuentabancaria.idestado', 1)
    ->get();
?>
<script>

    $("#idformapago").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).on("change", function (e) {
        $('#cont-formapago-contado').css('display','none');
        $('#cont-formapago-credito').css('display','none');
        $('#cont-formapago-venta').css('display','block');
        if (e.currentTarget.value == 1) {
            $('#cont-formapago-contado').css('display','block');
        }
        else if (e.currentTarget.value == 2) {
            $('#cont-formapago-credito').css('display','block');
        }
    }).val(1).trigger("change"); 
  
  
    function formapago_contado_agregar(){
      var num = $("#tabla-tipopago > tbody#tbody-formapago").attr('num');
      var nuevaFila='<tr id="'+num+'">'+
                         '<td>'+
                           '<table class="table">'+
                             '<tbody>'+
                               '<tr>'+
                                 '<td width="130px">Cuenta *</td>'+
                                 '<td colspan="2">'+
                                   '<select id="formapago_idcuentabancaria'+num+'">'+
                                     '<option></option>'+
                                     '@foreach ($cuentabancarias as $value)'+
                                     '<option value="{{ $value->id }}" formapago_banco="{{ $value->banco }}" formapago_numerocuenta="{{ $value->numerocuenta }}">{{ $value->banco }}: {{ $value->numerocuenta }}</option>'+
                                     '@endforeach'+
                                   '</select>'+
                                 '</td>'+
                               '</tr>'+
                               '<!--tr>'+
                                 '<td>Nro de Operación *</td>'+
                                 '<td colspan="2"><input type="number" id="formapago_numerooperacion'+num+'"/></td>'+
                               '</tr>'+
                               '<tr>'+
                                 '<td>Fecha / Hora *</td>'+
                                 '<td><input type="date" id="formapago_fecha'+num+'"/></td>'+
                                 '<td><input type="time" id="formapago_hora'+num+'"/></td>'+
                               '</tr-->'+
                               '<tr>'+
                                 '<td>Monto *</td>'+
                                 '<td colspan="2"><input type="number" id="formapago_montodeposito'+num+'" onkeyup="formapago_contado_calcular();" step="0.01"/></td>'+
                               '</tr>'+
                               '<tr>'+
                                 '<td>Voucher *</td>'+
                                 '<td colspan="2">'+
                                   '<div class="file-input">'+
                                     '<label for="formapago_voucher'+num+'">'+
                                       '<i class="fa fa-upload"></i> Subir Voucher'+
                                       '<p id="file-result-formapago_voucher'+num+'"></p>'+
                                     '</label>'+
                                     '<input type="file" id="formapago_voucher'+num+'" class="file">'+
                                   '</div>'+
                                 '</td>'+
                               '</tr>'+
                             '</tbody>'+
                           '</table>'+
                         '</td><td><a href="javascript:;" onclick="formapago_contado_eliminar('+num+')" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'+
                       '</tr>';

        $("#tabla-tipopago > tbody#tbody-formapago").append(nuevaFila);
        $("#tabla-tipopago > tbody#tbody-formapago").attr('num',parseInt(num)+1);  
      
        $('#formapago_idcuentabancaria'+num).select2({
            placeholder: '-- Seleccionar Cuenta Bancaria --',
            minimumResultsForSearch: -1
        });
      
        // subir voucher
        file({click:'#formapago_voucher'+num});
    }
    function formapago_contado_eliminar(num){
        $("#tabla-tipopago > tbody#tbody-formapago > tr#"+num).remove();
        formapago_contado_calcular();
    }
    function formapago_contado_calcular(){
        var totalefectivo = parseFloat($('#{{$request->efectivo}}').val());
        var totaldeposito = 0;
        $("#tabla-tipopago > tbody#tbody-formapago > tr").each(function() {
            var num = $(this).attr('id');      
            var formapago_montodeposito =  $('#formapago_montodeposito'+num).val()!=''?$('#formapago_montodeposito'+num).val():0;
            totaldeposito = totaldeposito+parseFloat(formapago_montodeposito);
        });
        $('#totalmonto_deposito').html(totaldeposito.toFixed(2));
        $('#totalmonto_efectivo').html(parseFloat(totalefectivo-totaldeposito).toFixed(2));
    }
    function formapago_contado_seleccionar(){
        var data = [];
        $("#tabla-tipopago > tbody#tbody-formapago > tr").each(function() {
            var num = $(this).attr('id');      
            data.push({'num' : num});
        });
        return JSON.stringify(data);;
    } 
  
    formapago_credito_agregar();  
    function formapago_credito_agregar(){
        var num = $("#tabla-formapago > tbody").attr('num');
        $('#tabla-formapago > tbody').append('<tr id="'+num+'">'+
            '<td style="text-align: center;" id="formapago_numero'+num+'">1</td>'+
            '<td class="mx-td-input"><input id="formapago_ultimafecha'+num+'" type="date"></td>'+
            '<td class="mx-td-input"><input id="formapago_importe'+num+'" type="number"></td>'+
            '<td class="mx-td-input"><a id="del'+num+'" href="javascript:;" onclick="formapago_credito_eliminar('+num+')" class="btn btn-danger big-btn" style="padding: 12px 15px;"><i class="fa fa-close"></i></a></td>'+
            '</tr>');
        $("#tabla-formapago > tbody").attr('num',parseInt(num)+1);
        formapago_credito_actualizar();      
    }
    function formapago_credito_eliminar(num){
        $("#tabla-formapago > tbody > tr#"+num).remove();
        formapago_credito_actualizar();
    } 
    function formapago_credito_actualizar(){
        var total = parseFloat($('#{{$request->efectivo}}').val());
        var cantidad = parseFloat($("#tabla-formapago > tbody > tr").length);
        var importe = total/cantidad;
        var i = 1;
        $("#tabla-formapago > tbody > tr").each(function() {
            var num = $(this).attr('id');        
            $("#formapago_numero"+num).html(i);    
            $("#formapago_importe"+num).val(importe.toFixed(2));
            i++;
        });
    }
    function formapago_credito_seleccionar(){
        var data = '';
        $("#tabla-formapago > tbody > tr").each(function() {
            var num = $(this).attr('id');        
            var formapago_numero = $("#formapago_numero"+num).val();
            var formapago_ultimafecha = $("#formapago_ultimafecha"+num).val();
            var formapago_importe = $("#formapago_importe"+num).val();
            data = data+'/&/'+formapago_numero+'/,/'+formapago_ultimafecha+'/,/'+formapago_importe;
        });
        return data;
    } 
  
</script>