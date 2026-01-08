<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: rgb(249, 249, 249);" id="bodyTable">
	<tbody><tr>
		<td align="center" valign="top" style="padding-right:10px;padding-left:10px;" id="bodyCell">
		<!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" style="width:600px;" width="600"><tr><td align="center" valign="top"><![endif]-->

		@include('app.email_cabecera')

		<!-- Email Wrapper Body Open // -->
		<table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperBody">
			<tbody><tr>
				<td align="center" valign="top">

					<!-- Table Card Open // -->
					<table border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;border-color:#E5E5E5; border-style:solid; border-width:0 1px 1px 1px;" width="100%" class="tableCard">

						<tbody>

						<tr>
							<td align="center" valign="top" style="padding-bottom: 20px;" class="imgHero">
								<!-- Hero Image // -->
								<a href="javascript:;" style="text-decoration:none;" class="">
									<img src="http://weekly.grapestheme.com/notify/img/hero-img/blue/heroFill/user-welcome.png" width="598" alt="" border="0" style="width: 100%; max-width: 598px; height: auto; display: block;" class="">
								</a>
							</td>
						</tr>

						<tr>
							<td align="center" valign="top" style="padding-bottom: 5px; padding-left: 20px; padding-right: 20px;" class="mainTitle">
								<!-- Main Title Text // -->
								<h2 class="text" style="color:#000000; font-family:'Poppins', Helvetica, Arial, sans-serif; font-size:28px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:36px; text-transform:none; text-align:center; padding:0; margin:0">
									¡Ha enviado correctamente su Pago!
								</h2>
							</td>
						</tr>

						<tr>
							<td align="center" valign="top" style="padding-left:20px;padding-right:20px;" class="containtTable ui-sortable">


								<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableTitleDescription" style="">
									<tbody><tr>
										<td align="center" valign="top" style="padding-bottom: 10px;" class="mediumTitle">
											<!-- Medium Title Text // -->
											<p class="text" style="color:#000000; font-family:'Poppins', Helvetica, Arial, sans-serif; font-size:18px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:26px; text-transform:none; text-align:center; padding:0; margin:0">La Activación se hara dentro de las 24 Horas</p>
										</td>
									</tr>
								</tbody></table>
								
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

		@include('app.email_pie')

		<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
		</td>
	</tr>
</tbody></table>