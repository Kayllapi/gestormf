
               <table class="table">
                   <tr style="color: white;">
                       <th style="border: 1px solid #31353d;background-color: #4171ed;text-align:center;">Nº</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">FECHA DEL PERIODO</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">CÒDIGO ENTIDAD</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">CÒDIGO TARJETA CRÉDITO</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">CÒDIGO PRESTAMO</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">CÒDIGO AGENCIA</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">TIPO DOCUMENTO Ó ENTIDAD</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">Nº DOCUMENTO ENTIDAD DNI O RUC</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">RAZÓN SOCIAL</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">APELLIDO PATERNO</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">APELLIDO MATERNO</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">NOMBRES</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">TIPO PERSONA</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">MODALIDAD DE CRÉDITO</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">MN DEUDA DIRECTA VIGENTE</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">MN DEUDA DIRECTA REFINANCIADA</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">MN DEUDA DIRECTA VENCIDA &lt;= 30</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">MN DEUDA DIRECTA VENCIDA &gt; 30</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">MN DEUDA DIRECTA COBRANZA JUDICIAL</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">MN DEUDA DIRECTA (AVALES, CARTAS, FINANZAS, CRÉDITO)</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">MN DEUDA AVALADA</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">MN LINEA DE CRÉDITO</th>
                       <th style="border: 1px solid #31353d;background-color: #e33a3a;text-align:center;">MN CRÉDITOS CASTIGADOS</th>
                       <th style="border: 1px solid #31353d;background-color: #df910e;text-align:center;">ME DEUDA DIRECTA VIGENTE</th>
                       <th style="border: 1px solid #31353d;background-color: #df910e;text-align:center;">ME DEUDA DIRECTA REFINANCIADA</th>
                       <th style="border: 1px solid #31353d;background-color: #df910e;text-align:center;">ME DEUDA DIRECTA VENCIDA &lt;= 30</th>
                       <th style="border: 1px solid #31353d;background-color: #df910e;text-align:center;">ME DEUDA DIRECTA VENCIDA &gt; 30</th>
                       <th style="border: 1px solid #31353d;background-color: #df910e;text-align:center;">ME DEUDA DIRECTA COBRANZA JUDICIAL</th>
                       <th style="border: 1px solid #31353d;background-color: #df910e;text-align:center;">ME DEUDA DIRECTA (AVALES, CARTAS, FINANZAS, CRÉDITO)</th>
                       <th style="border: 1px solid #31353d;background-color: #df910e;text-align:center;">ME DEUDA AVALADA</th>
                       <th style="border: 1px solid #31353d;background-color: #df910e;text-align:center;">ME LINEA DE CRÉDITO</th>
                       <th style="border: 1px solid #31353d;background-color: #df910e;text-align:center;">ME CRÉDITOS CASTIGADOS</th>
                       <th style="border: 1px solid #31353d;background-color: #a9bf1b;text-align:center;">CALIFICACIÓN</th>
                       <th style="border: 1px solid #31353d;background-color: #a9bf1b;text-align:center;">NÚMERO DE DÍAS VENCIDOS Y MOROSOS</th>
                       <th style="border: 1px solid #31353d;background-color: #1b9cbf;text-align:center;">DIRECCIÓN</th>
                       <th style="border: 1px solid #31353d;background-color: #1b9cbf;text-align:center;">DISTRITO</th>
                       <th style="border: 1px solid #31353d;background-color: #1b9cbf;text-align:center;">PROVINCIA</th>
                       <th style="border: 1px solid #31353d;background-color: #1b9cbf;text-align:center;">DEPARTAMENTO</th>
                       <th style="border: 1px solid #31353d;background-color: #1b9cbf;text-align:center;">TELÉFONO</th>
                       <th style="border: 1px solid #31353d;background-color: #1b9cbf;text-align:center;">FECHA DE VENCIMIENTO</th>
                   </tr>
                    <?php $i=1; ?>
                    @foreach($prestamomoras as $valuedetalle)
                    <tr>
                        <td style="border: 1px solid #31353d;text-align:center;"><?php echo $i; ?></td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['fechaperiodo']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['codigoentidad']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['codigotarjeta']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['codigoprestamo']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['codigoagencia']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['tipodocumento']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['documentoentidad']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['razonsocial']}}</td>
                        <td style="border: 1px solid #31353d;text-align:left;">{{$valuedetalle['apellidopaterno']}}</td>
                        <td style="border: 1px solid #31353d;text-align:left;">{{$valuedetalle['apellidomaterno']}}</td>
                        <td style="border: 1px solid #31353d;text-align:left;">{{$valuedetalle['nombres']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['tipopersona']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['modalidadcredito']}}</td>
                        <td style="border: 1px solid #31353d;text-align:right;">{{$valuedetalle['deudavigente']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:right;">{{$valuedetalle['deudadirectavencida1']}}</td>
                        <td style="border: 1px solid #31353d;text-align:right;">{{$valuedetalle['deudadirectavencida2']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;"></td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['calificacion']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['numerodiasvencidos']}}</td>
                        <td style="border: 1px solid #31353d;text-align:left;">{{$valuedetalle['direccion']}}</td>
                        <td style="border: 1px solid #31353d;text-align:left;">{{$valuedetalle['distrito']}}</td>
                        <td style="border: 1px solid #31353d;text-align:left;">{{$valuedetalle['provincia']}}</td>
                        <td style="border: 1px solid #31353d;text-align:left;">{{$valuedetalle['departamento']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['telefono']}}</td>
                        <td style="border: 1px solid #31353d;text-align:center;">{{$valuedetalle['fechavencimiento']}}</td>
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                </table>
