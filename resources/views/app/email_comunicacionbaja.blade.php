
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
                      @if($facturacioncomunicacionbaja->agencialogo!='')
                          <img class="logo" src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacioncomunicacionbaja->agencialogo) }}" height=50px><br>
                      @endif
                      {{ strtoupper($facturacioncomunicacionbaja->emisor_nombrecomercial) }}
                      <br>
                      RUC:  {{ $facturacioncomunicacionbaja->emisor_ruc }}<br>
                            {{ strtoupper($facturacioncomunicacionbaja->emisor_direccion) }}<br>
                            {{ strtoupper($facturacioncomunicacionbaja->emisor_departamento.'/'.$facturacioncomunicacionbaja->emisor_provincia.'/'.$facturacioncomunicacionbaja->emisor_distrito) }}<br><br>

                      <div style="text-align: left;color: #000;">   
                            COMUNICACIÓN DE BAJA: {{ str_pad($facturacioncomunicacionbaja->comunicacionbaja_correlativo, 8, "0", STR_PAD_LEFT) }}<br>
                            GENERACIÓN: {{ date_format(date_create($facturacioncomunicacionbaja->comunicacionbaja_fechageneracion),"d/m/Y h:i:s A") }}<br>
                            COMUNICACIÓN: {{ date_format(date_create($facturacioncomunicacionbaja->comunicacionbaja_fechacomunicacion),"d/m/Y h:i:s A") }}<br>
                            RESPONSABLE: {{ strtoupper($facturacioncomunicacionbaja->responsablenombre) }}<br>
                    </div>
                    <table style="width: 100%;margin:0px;padding:0px;font-size: 12px;margin-top:15px;">
                    <tbody>
                      <tr>
                        <td style="text-align: center;height:5px;font-family: Courier;color: #000;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
                      </tr>
                      @foreach($facturacioncomunicacionbajadetalle as $value)
                      <tr>
                        <td style="text-align: left;font-family: Courier;color: #000;">
                          RUC/DNI: {{ $value->clienteidentificacion }} <br>
                          CLIENTE: {{ strtoupper($value->cliente) }}<br>
                          SERIE-CORRELATIVO: {{ $value->serie }}-{{ $value->correlativo }}<br>
                          MOTIVO: {{ $value->descripcionmotivobaja }}<br>
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align: center;height:5px;font-family: Courier;color: #000;"><div style="border-top: 1px dashed #31353d;width:100%;"></div></td>
                      </tr>
                      @endforeach
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