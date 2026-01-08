
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: rgb(249, 249, 249);" id="bodyTable">
	<tbody><tr>
		<td align="center" valign="top" style="padding-right:10px;padding-left:10px;" id="bodyCell">
		<!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" style="width:600px;" width="600"><tr><td align="center" valign="top"><![endif]-->

		<!-- Email Wrapper Body Open // -->
		<table border="0" cellpadding="0" cellspacing="0" style="max-width:400px;" width="100%" class="wrapperBody">
			<tbody><tr>
				<td align="center" valign="top">
          <!-- Space -->
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="space">
						<tbody><tr>
							<td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
						</tr>
					</tbody></table>
          
					<!-- Table Card Open // -->
					<table border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;border-color:#E5E5E5; border-style:solid; border-width:0 1px 1px 1px;" width="100%" class="tableCard">

						<tbody>


						<tr>
							<td align="center" valign="top" style="padding-bottom: 5px; padding-left: 20px; padding-right: 20px;" class="mainTitle">
								
                
                
                <div style="font-family: Courier;font-weight: bold;font-size: 12px;width: 100%;text-align: center;color: #000;margin-top:20px;">
                      @if($facturacionnotadebito->agencialogo!='')
                          <img class="logo" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionnotadebito->agencialogo) }}" height=50px><br>
                      @endif
                      {{ strtoupper($facturacionnotadebito->emisor_nombrecomercial) }}
                      <br>
                      RUC:  {{ $facturacionnotadebito->emisor_ruc }}<br>
                            {{ strtoupper($facturacionnotadebito->emisor_direccion) }}<br>
                            {{ strtoupper($facturacionnotadebito->emisor_departamento.'/'.$facturacionnotadebito->emisor_provincia.'/'.$facturacionnotadebito->emisor_distrito) }}<br><br>

                      <div style="text-align: left;color: #000;">   
                        NOTA DE CRÉDITO: {{$facturacionnotadebito->notadebito_serie}}-{{ str_pad($facturacionnotadebito->notadebito_correlativo, 8, "0", STR_PAD_LEFT) }}<br>
                        EMISIÓN: {{ date_format(date_create($facturacionnotadebito->notadebito_fechaemision),"d/m/Y h:i:s A") }}<br>
                        @if($facturacionnotadebito->	notadebito_tipomoneda=='PEN')
                        MONEDA: SOLES<br>
                        @else($facturacionnotadebito->	notadebito_tipomoneda=='USD')
                        MONEDA: DOLARES<br>
                        @endif
                        CLIENTE: {{ strtoupper($facturacionnotadebito->cliente_razonsocial) }}<br>
                        DNI/RUC: {{ $facturacionnotadebito->cliente_numerodocumento }}<br>
                        DIRECCIÓN: {{ strtoupper($facturacionnotadebito->cliente_direccion) }}<br>
                        {{ strtoupper($facturacionnotadebito->cliente_departamento.'/'.$facturacionnotadebito->cliente_provincia.'/'.$facturacionnotadebito->cliente_distrito) }}<br>
                        RESPONSABLE: {{ strtoupper($facturacionnotadebito->responsablenombre) }}<br><br>
                    </div>
                    <?php 
                    $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                        ->where('s_facturacionboletafactura.id',$facturacionnotadebito->idfacturacionboletafactura)
                        ->first(); 
                    ?>
                    <div style="text-align: center;color: #000;">COMPROBANTE MODIFICADO</div><br>
                    <div style="text-align: left;color: #000;">
                        @if($facturacionboletafactura->venta_tipodocumento==3)
                           BOLETA: {{$facturacionboletafactura->venta_serie}}-{{ str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT) }}
                        @elseif($facturacionboletafactura->venta_tipodocumento==1)
                           FACTURA: {{$facturacionboletafactura->venta_serie}}-{{ str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT) }}
                        @endif<br>
                        EMISIÓN: {{ date_format(date_create($facturacionboletafactura->venta_fechaemision),"d/m/Y h:i:s A") }}<br>
                        MOTIVO: {{strtoupper($facturacionnotadebito->notadebito_descripcionmotivo) }}
                    </div>
                    <table style="width: 100%;margin:0px;padding:0px;font-size: 12px;margin-top:15px;">
                    <thead>
                      <tr>
                        <td colspan="3" style="text-align: center;height:5px;font-family: Courier;color: #000;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
                      </tr>
                      <tr>
                        <td style="white-space: nowrap;text-align: center;font-family: Courier;color: #000;">CANT</td>
                        <td style="white-space: nowrap;text-align: center;width:60px;font-family: Courier;color: #000;">P.UNIT.</td>
                        <td style="white-space: nowrap;text-align: right;width:30px;font-family: Courier;color: #000;">TOTAL</td>
                      </tr>
                      <tr>
                        <td colspan="3" style="text-align: center;height:5px;font-family: Courier;color: #000;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($notadebitodetalle as $value)
                      <tr>
                        <td colspan="3" style="text-align: left;font-family: Courier;color: #000;">{{ strtoupper($value->descripcion) }}</td>
                      </tr>
                      <tr>
                        <td style="white-space: nowrap;text-align: center;font-family: Courier;color: #000;">{{ $value->cantidad }}</td>
                        <td style="white-space: nowrap;text-align: center;font-family: Courier;color: #000;">{{ $value->montopreciounitario }}</td>
                        <td style="white-space: nowrap;text-align: right;font-family: Courier;color: #000;">{{number_format($value->cantidad*$value->montopreciounitario, 2, '.', '') }}</td>
                      </tr>
                      @endforeach
                      <tr>
                        <td colspan="3" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
                      </tr>
                     <tr>
                        <td colspan="2" style="text-align: right;font-family: Courier;color: #000;">SUB TOTAL:</td>
                        <td style="white-space: nowrap;text-align: right;font-family: Courier;color: #000;">
                          {{ $facturacionnotadebito->notadebito_valorventa }}
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" style="text-align: right;font-family: Courier;color: #000;">IGV:</td>
                        <td style="white-space: nowrap;text-align: right;font-family: Courier;color: #000;">
                          {{ $facturacionnotadebito->notadebito_totalimpuestos }}
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" style="text-align: right;font-family: Courier;color: #000;">TOTAL:</td>
                        <td style="white-space: nowrap;text-align: right;font-family: Courier;color: #000;">
                          {{ $facturacionnotadebito->notadebito_montoimpuestoventa }}
                        </td>
                      </tr>
                      <tr>
                        <td colspan="3" style="text-align: center;height:5px;font-family: Courier;color: #000;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
                      </tr>
                      <tr>
                        <td colspan="3" style="text-align: center;height:5px;font-family: Courier;color: #000;">{{ $facturacionnotadebito->leyenda_value }}</td>
                      </tr>
                      <tr>
                        <td colspan="3" style="text-align: center;height:5px;font-family: Courier;color: #000;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
                      </tr>
                    </tbody>
                  </table>

                  <div style="text-align: center;color: #000;">
                  ¡GRACIAS POR SU COMPRA!
                  </div>
                </div>
                
                
							</td>
						</tr>

						<tr>
							<td height="20" style="font-size:1px;line-height:1px;">&nbsp;</td>
						</tr>

						
					</tbody></table>
					<!-- Table Card Close// -->

					<!-- Space -->
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="space">
						<tbody><tr>
							<td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
						</tr>
					</tbody></table>

				</td>
			</tr>
		</tbody></table>
		<!-- Email Wrapper Body Close // -->

		<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
		</td>
	</tr>
</tbody></table>