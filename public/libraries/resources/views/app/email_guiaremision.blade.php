
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
                      @if($facturacionguiaremision->agencialogo!='')
                          <img class="logo" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionguiaremision->agencialogo) }}" height=50px><br>
                      @endif
                      {{ strtoupper($facturacionguiaremision->emisor_nombrecomercial) }}
                      <br>
                      RUC:  {{ $facturacionguiaremision->emisor_ruc }}<br>
                            {{ strtoupper($facturacionguiaremision->emisor_direccion) }}<br>
                            {{ strtoupper($facturacionguiaremision->emisor_departamento.'/'.$facturacionguiaremision->emisor_provincia.'/'.$facturacionguiaremision->emisor_distrito) }}<br><br>

    <div style="text-align: left;color: #000;">
      GUIA DE REMISIÓN: {{$facturacionguiaremision->despacho_serie}}-{{ str_pad($facturacionguiaremision->despacho_correlativo, 8, "0", STR_PAD_LEFT) }}<br>
      EMISIÓN: {{date_format(date_create($facturacionguiaremision->despacho_fechaemision),"d/m/Y h:i:s A") }}<br><br>
    </div>  
        <div style="text-align: center;color: #000;">REMITENTE</div><br>
    <div style="text-align: left;color: #000;">
      RUC: {{ strtoupper($facturacionguiaremision->emisor_ruc)}}<br>
      RAZÓN SOCIAL: {{ strtoupper($facturacionguiaremision->emisor_razonsocial)}}<br>
      PARTIDA: {{ $facturacionguiaremision->envio_direccionpartida }}<br>
      UBIGEO: {{ $facturacionguiaremision->partidaubigeonombre }}<br><br>
    </div>  
        <div style="text-align: center;color: #000;">DESTINATARIO</div><br>
    <div style="text-align: left;color: #000;">
      RUC/DNI: {{ strtoupper($facturacionguiaremision->despacho_destinatario_numerodocumento)}}<br>
      RAZÓN SOCIAL: {{strtoupper($facturacionguiaremision->despacho_destinatario_razonsocial)}}<br>
      LLEDADA: {{ $facturacionguiaremision->envio_direccionllegada }}<br>
      UBIGEO: {{ $facturacionguiaremision->llegadaubigeonombre }}<br><br>
    </div>  
        <div style="text-align: center;color: #000;">TRANSPORTISTA</div><br>
    <div style="text-align: left;color: #000;">
      RUC/DNI: {{ strtoupper($facturacionguiaremision->transporte_choferdocumento )}}<br>
      RAZÓN SOCIAL: {{ strtoupper($facturacionguiaremision->transportista )}}<br><br>
    </div>  
        <div style="text-align: center;color: #000;">DETALLE DE ENVIO</div><br>
    <div style="text-align: left;color: #000;">
      MOTIVO: {{ $facturacionguiaremision->envio_descripciontraslado }}<br>
      F. TRASLADO: {{date_format(date_create($facturacionguiaremision->envio_fechatraslado),"d/m/Y") }}<br>
      OBSERVACIÓN: {{ strtoupper($facturacionguiaremision->despacho_observacion )}}<br>
      RESPONSABLE: {{ strtoupper($facturacionguiaremision->responsablenombre) }}<br>
    </div>
                  
                    <table style="width: 100%;margin:0px;padding:0px;font-size: 12px;margin-top:15px;">
                    <thead>
                      <tr>
                        <td colspan="2" style="text-align: center;height:5px;font-family: Courier;color: #000;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
                      </tr>
                      <tr>
                        <td style="white-space: nowrap;text-align: center;font-family: Courier;color: #000;">CANT</td>
                        <td style="white-space: nowrap;text-align: center;width:60px;font-family: Courier;color: #000;">UNIDAD</td>
                      </tr>
                      <tr>
                        <td colspan="2" style="text-align: center;height:5px;font-family: Courier;color: #000;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($facturacionguiaremisiondetalles as $value)
                      <tr>
                        <td colspan="2" style="text-align: left;font-family: Courier;color: #000;">{{ strtoupper($value->descripcion) }}</td>
                      </tr>
                      <tr>
                        <td style="white-space: nowrap;text-align: center;font-family: Courier;color: #000;">{{ $value->cantidad }}</td>
                        <td style="white-space: nowrap;text-align: center;font-family: Courier;color: #000;">{{ $value->unidad }}</td>
                      </tr>
                      @endforeach
                      <tr>
                        <td colspan="2" style="text-align: center;height:5px;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
                      </tr>
                      <tr>
                        <td colspan="2" style="text-align: center;"</td>
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