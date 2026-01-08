<?php
// ---------------------------- FACTURADOR ---------------------------- //
use Carbon\Carbon;

use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

// boleta y factura
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

// nota de credito
use Greenter\Model\Sale\Note;

// qr
use Greenter\Report\Render\QrRender;
use Greenter\Report\Filter\ImageFilter;

// resumen
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Summary\SummaryPerception;

// comunicación de baja
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;

// guia de remision
use Greenter\Model\Sale\Document;
// use Greenter\Model\Despatch\Transportist;
// use Greenter\Model\Despatch\Shipment;
// use Greenter\Model\Despatch\Direction;
// use Greenter\Model\Despatch\Despatch;
// use Greenter\Model\Despatch\DespatchDetail;

// consulta cdr
use Greenter\Ws\Services\ConsultCdrService;
use Greenter\Ws\Services\SoapClient;

// GUIA REMISION
use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Despatch\Direction;
use Greenter\Model\Despatch\Shipment;
use Greenter\Model\Despatch\Transportist;
use Greenter\Model\Despatch\Vehicle;
use Greenter\Model\Despatch\Driver;
use Greenter\Model\Response\BillResult;
use Greenter\Model\Despatch\AdditionalDoc;

// use Greenter\Report\Filter\ResolveFilter;
// use Greenter\Report\Filter\ImageFilter;
// use Greenter\Report\Render\QrRender;
// QR IMAGE
// use BaconQrCode\Common\ErrorCorrectionLevel;
// use BaconQrCode\Renderer\Image\SvgImageBackEnd;
// use BaconQrCode\Renderer\ImageRenderer;
// use BaconQrCode\Renderer\RendererStyle\RendererStyle;
// use BaconQrCode\Writer;

/*

//use Greenter\Model\Sale\BaseSale;

use Greenter\Model\Response\BillResult;*/


function facturador_conexion($data,$service='FE'){
        if($service=='FE'){
            $service_beta = SunatEndpoints::FE_BETA;
            $service_produccion = SunatEndpoints::FE_PRODUCCION;
        }elseif($service=='GUIA'){
            $service_beta = SunatEndpoints::GUIA_BETA;
            $service_produccion = SunatEndpoints::GUIA_PRODUCCION;
        }
        $see = new See();
        if($data['idestadofacturacion']==1){
            if($data['sunat_usuario']=='MODDATOS' && $data['sunat_clave']=='MODDATOS'){
                $raiz = 'public/backoffice/tienda/'.$data['idtienda'].'/sunat/produccion/';
                $see->setCertificate(file_get_contents(url($raiz.'certificado/'.$data['sunat_certificado'])));
                $see->setService($service_beta);
                $see->setClaveSOL('20000000001', 'MODDATOS', 'moddatos');
            }else{
                $raiz = 'public/backoffice/tienda/'.$data['idtienda'].'/sunat/produccion/';
                $see->setCertificate(file_get_contents(url($raiz.'certificado/'.$data['sunat_certificado'])));
                $see->setService($service_produccion);
                $see->setClaveSOL($data['emisor_ruc'], $data['sunat_usuario'], $data['sunat_clave']);
            }
        }elseif($data['idestadofacturacion']==2){
            $raiz = 'public/backoffice/sistema/sunat/beta/';
            $see->setCertificate(file_get_contents(url($raiz.'certificado/certificate.pem')));
            $see->setService(SunatEndpoints::FE_BETA);
            $see->setClaveSOL('20000000001', $data['sunat_usuario'], $data['sunat_clave']);
        }
      
        return [
            'see' => $see,
            'raiz' => $raiz
        ];
}
function facturador_facturaboleta($idfacturacionboletafactura,$nueva_fechaemision = ''){
  
        $data = DB::table('s_facturacionboletafactura')
            ->join('s_agencia','s_agencia.id','s_facturacionboletafactura.idagencia')
            ->where('s_facturacionboletafactura.id',$idfacturacionboletafactura)
            ->select(
                's_facturacionboletafactura.*',
                's_agencia.idestadofacturacion as idestadofacturacion',
                's_agencia.facturacion_usuario as sunat_usuario',
                's_agencia.facturacion_clave as sunat_clave',
                's_agencia.facturacion_certificado as sunat_certificado'
            )
            ->first();
        //dd($data);
        
        if($nueva_fechaemision!=''){
            $fechaemision = $nueva_fechaemision;
        }else{
            $fechaemision = $data->venta_fechaemision;
        }
  
        // Emisor
        $addressemisor = new Address();
        $addressemisor->setUbigueo($data->emisor_ubigeo)
            ->setDepartamento($data->emisor_departamento)
            ->setProvincia($data->emisor_provincia)
            ->setDistrito($data->emisor_distrito)
            ->setUrbanizacion($data->emisor_urbanizacion)
            ->setDireccion($data->emisor_direccion);

        $company = new Company();
        $company->setRuc($data->emisor_ruc)
            ->setRazonSocial($data->emisor_razonsocial)
            ->setNombreComercial($data->emisor_nombrecomercial)
            ->setAddress($addressemisor);
  
        // Cliente
        $addresscliente = new Address();
        $addresscliente->setUbigueo($data->cliente_ubigeo)
            ->setDepartamento($data->cliente_departamento)
            ->setProvincia($data->cliente_provincia)
            ->setDistrito($data->cliente_distrito)
            ->setUrbanizacion($data->cliente_urbanizacion)
            ->setDireccion($data->cliente_direccion);
      
        $client = new Client();
        $client->setTipoDoc($data->cliente_tipodocumento)
            ->setNumDoc($data->cliente_numerodocumento)
            ->setRznSocial($data->cliente_razonsocial)
            ->setAddress($addresscliente);
      
        // Venta
        $invoice = (new Invoice())
            ->setUblVersion($data->venta_ublversion)
            ->setTipoOperacion($data->venta_tipooperacion) // Catalog. 51
            ->setTipoDoc($data->venta_tipodocumento)
            ->setSerie($data->venta_serie)
            ->setCorrelativo($data->venta_correlativo)
            ->setFechaEmision(\DateTime::createFromFormat('Y-m-d H:i:s', $fechaemision))
            ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
            ->setTipoMoneda($data->venta_tipomoneda)
            ->setClient($client)
            ->setMtoOperGravadas($data->venta_montooperaciongravada)
            ->setMtoIGV($data->venta_montoigv)
            ->setTotalImpuestos($data->venta_totalimpuestos)
            ->setValorVenta($data->venta_valorventa)
            ->setSubTotal($data->venta_montoimpuestoventa)
            ->setMtoImpVenta($data->venta_montoimpuestoventa)
            ->setCompany($company);

        $datadetalles = DB::table('s_facturacionboletafacturadetalle')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$data->id)
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();
  
        $item = [];
        foreach($datadetalles as $value){
            $item[] = (new SaleDetail())
                ->setCodProducto($value->codigoproducto)
                ->setUnidad($value->unidad)
                ->setCantidad($value->cantidad)
                ->setDescripcion($value->descripcion)
                ->setMtoBaseIgv($value->montobaseigv)
                ->setPorcentajeIgv($value->porcentajeigv)
                ->setIgv($value->igv)
                ->setTipAfeIgv($value->tipoafectacionigv)
                ->setTotalImpuestos($value->totalimpuestos)
                ->setMtoValorVenta($value->montovalorventa)
                ->setMtoValorUnitario($value->montovalorunitario)
                ->setMtoPrecioUnitario($value->montopreciounitario);
        }
      
        // leyenda
        $legend = (new Legend())
            ->setCode($data->leyenda_codigo)
            ->setValue($data->leyenda_value);
      
        $invoice->setDetails($item)
                ->setLegends([$legend]);

        // Conexión SUNAT
        $facturador = facturador_conexion([
            'idtienda' => $data->idtienda,
            'idestadofacturacion' => $data->idestadofacturacion,
            'emisor_ruc' => $data->emisor_ruc,
            'sunat_usuario' => $data->sunat_usuario,
            'sunat_clave' => $data->sunat_clave,
            'sunat_certificado' => $data->sunat_certificado,
        ]);
  
    
        // QR
        $crearqr = new QrRender();
        $base64_qr = new ImageFilter();
        $minewqr = $base64_qr->toBase64($crearqr->getImage($invoice));
        // Fin QR
  
        // Envio SUNAT
        $result = $facturador['see']->send($invoice);
  
        // verificar si existe directorio
        $raiz = $facturador['raiz'].'boletafactura/';
        if (!file_exists($raiz)) {
            mkdir($raiz, 0777);
        }
  
        // Guardar XML firmado digitalmente.
        file_put_contents($raiz.$invoice->getName().'.xml',$facturador['see']->getFactory()->getLastXml());

        // Verificamos que la conexión con SUNAT fue exitosa.
        if ($result->isSuccess()) {
          
            // Guardamos el CDR
            file_put_contents($raiz.'R-'.$invoice->getName().'.zip', $result->getCdrZip());
          
            $cdr = $result->getCdrResponse();
            $result_cdr = facturador_consulta($cdr);
          
          
            // Mostrar error al conectarse a SUNAT.
            $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                'fecharegistro'               => Carbon::now(),
                'estado'                      => $result_cdr['estado'],
                'codigo'                      => $result_cdr['codigo'],
                'mensaje'                     => $cdr->getDescription(),
                'notas'                       => $result_cdr['notas'],
                'qr'                          => $minewqr,
                'nombre'                      => $invoice->getName(),
                's_idfacturacionboletafactura'=> $idfacturacionboletafactura,
                'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                'idtienda'                    => $data->idtienda,
            ]);
          
            DB::table('s_facturacionboletafactura')->whereId($idfacturacionboletafactura)->update([
                'idfacturacionrespuesta' => $idfacturacionrespuesta
            ]);
          
            return [
                'resultado' => 'CORRECTO',
                'mensaje' => $cdr->getDescription(),
            ];
        }else{
          
            $cdr = $result->getError();
            $result_cdr = facturador_consulta($cdr);
          
            // Mostrar error al conectarse a SUNAT.
            $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                'fecharegistro'               => Carbon::now(),
                'estado'                      => $result_cdr['estado'],
                'codigo'                      => $result_cdr['codigo'],
                'mensaje'                     => $result->getError()->getMessage(),
                'notas'                       => '',
                'qr'                          => $minewqr,
                'nombre'                      => $invoice->getName(),
                's_idfacturacionboletafactura'=> $idfacturacionboletafactura,
                'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                'idtienda'                    => $data->idtienda,
            ]);
          
            DB::table('s_facturacionboletafactura')->whereId($idfacturacionboletafactura)->update([
                'venta_fechaemision'      => $fechaemision,
                'idfacturacionrespuesta'  => $idfacturacionrespuesta
            ]);
          
            return [
                'resultado' => 'CORRECTO',
                'mensaje' => $result_cdr['codigo'].' - '.$result->getError()->getMessage(),
            ];
        }
}
function facturador_notadebito($idfacturacionnotadebito){
  
        $data = DB::table('s_facturacionnotadebito')
            ->join('s_agencia','s_agencia.id','s_facturacionnotadebito.idagencia')
            ->where('s_facturacionnotadebito.id',$idfacturacionnotadebito)
            ->select(
                's_facturacionnotadebito.*',
                's_agencia.idestadofacturacion as idestadofacturacion',
                's_agencia.facturacion_usuario as sunat_usuario',
                's_agencia.facturacion_clave as sunat_clave',
                's_agencia.facturacion_certificado as sunat_certificado'
            )
            ->first();
  
        // Emisor
        $addressemisor = new Address();
        $addressemisor->setUbigueo($data->emisor_ubigeo)
            ->setDepartamento($data->emisor_departamento)
            ->setProvincia($data->emisor_provincia)
            ->setDistrito($data->emisor_distrito)
            ->setUrbanizacion($data->emisor_urbanizacion)
            ->setDireccion($data->emisor_direccion);

        $company = new Company();
        $company->setRuc($data->emisor_ruc)
            ->setRazonSocial($data->emisor_razonsocial)
            ->setNombreComercial($data->emisor_nombrecomercial)
            ->setAddress($addressemisor);
  
        // Cliente
        $client = new Client();
        $client->setTipoDoc($data->cliente_tipodocumento)
            ->setNumDoc($data->cliente_numerodocumento)
            ->setRznSocial($data->cliente_razonsocial);
 
        // Nota de credito
        $invoice = (new Note())
            ->setUblVersion($data->notadebito_ublversion)
            ->setTipDocAfectado($data->notadebito_tipodocafectado)
            ->setNumDocfectado($data->notadebito_numerodocumentoafectado)
            ->setCodMotivo($data->notadebito_codigomotivo)
            ->setDesMotivo($data->notadebito_descripcionmotivo)
            ->setTipoDoc($data->notadebito_tipodocumento)
            ->setSerie($data->notadebito_serie)
            ->setFechaEmision(\DateTime::createFromFormat('Y-m-d H:i:s', $data->notadebito_fechaemision))
            ->setCorrelativo($data->notadebito_correlativo)
            ->setTipoMoneda($data->notadebito_tipomoneda)
//             ->setGuias($data->notacredito_guias)
            ->setClient($client)
            ->setMtoOperGravadas($data->notadebito_montooperaciongravada)
            //->setMtoOperExoneradas(0.00)
            //->setMtoOperInafectas(0.00)
            ->setMtoIGV($data->notadebito_montoigv)
            ->setTotalImpuestos($data->notadebito_totalimpuestos)
            ->setMtoImpVenta($data->notadebito_montoimpuestoventa)
            ->setCompany($company);

        $datadetalles = DB::table('s_facturacionnotadebitodetalle')
                ->where('s_facturacionnotadebitodetalle.idfacturacionnotadebito',$data->id)
                ->orderBy('s_facturacionnotadebitodetalle.id','asc')
                ->get();
  
        $item = [];
        foreach($datadetalles as $value){
            $item[] = (new SaleDetail())
                ->setCodProducto($value->codigoproducto)
                ->setUnidad($value->unidad)
                ->setCantidad($value->cantidad)
                ->setDescripcion($value->descripcion)
                ->setMtoBaseIgv($value->montobaseigv)
                ->setPorcentajeIgv($value->porcentajeigv)
                ->setIgv($value->igv)
                ->setTipAfeIgv($value->tipoafectacionigv)
                ->setTotalImpuestos($value->totalimpuestos)
                ->setMtoValorVenta($value->montovalorventa)
                ->setMtoValorUnitario($value->montovalorunitario)
                ->setMtoPrecioUnitario($value->montopreciounitario);
        }

        // leyenda
        $legend = (new Legend())
            ->setCode($data->leyenda_codigo)
            ->setValue($data->leyenda_value);
      
        $invoice->setDetails($item)
                ->setLegends([$legend]);

        // Conexión SUNAT
        $facturador = facturador_conexion([
            'idtienda' => $data->idtienda,
            'idestadofacturacion' => $data->idestadofacturacion,
            'emisor_ruc' => $data->emisor_ruc,
            'sunat_usuario' => $data->sunat_usuario,
            'sunat_clave' => $data->sunat_clave,
            'sunat_certificado' => $data->sunat_certificado,
        ]);
  
        // QR
        $crearqr = new QrRender();
        $base64_qr = new ImageFilter();
        $minewqr = $base64_qr->toBase64($crearqr->getImage($invoice));
        // Fin QR
  
        // Envio SUNAT
        $result = $facturador['see']->send($invoice);
  
       // verificar si existe directorio
        $raiz = $facturador['raiz'].'notadebito/';
        if (!file_exists($raiz)) {
            mkdir($raiz, 0777);
        }
  
        // Guardar XML firmado digitalmente.
        file_put_contents($raiz.$invoice->getName().'.xml',$facturador['see']->getFactory()->getLastXml());

        // Verificamos que la conexión con SUNAT fue exitosa.
        if ($result->isSuccess()) {

            // Guardamos el CDR
            file_put_contents($raiz.'R-'.$invoice->getName().'.zip', $result->getCdrZip());
          
            $cdr = $result->getCdrResponse();
            $result_cdr = facturador_consulta($cdr);

          
            // Mostrar error al conectarse a SUNAT.
            
            $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                'fecharegistro'               => Carbon::now(),
                'estado'                      => $result_cdr['estado'],
                'codigo'                      => $result_cdr['codigo'],
                'mensaje'                     => $cdr->getDescription(),
                'notas'                       => $result_cdr['notas'],
                'qr'                          => $minewqr,
                'nombre'                      => $invoice->getName(),
                's_idfacturacionnotadebito'   => $idfacturacionnotadebito,
                'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                'idtienda'                    => $data->idtienda,
            ]);
          
            DB::table('s_facturacionnotadebito')->whereId($idfacturacionnotadebito)->update([
                'idfacturacionrespuesta' => $idfacturacionrespuesta
            ]);
          
            return [
                'resultado' => 'CORRECTO',
                'mensaje' => $cdr->getDescription(),
            ];
        }else{
            // Mostrar error al conectarse a SUNAT.
            
            $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                'fecharegistro'               => Carbon::now(),
                'estado'                      => 'ERROR',
                'codigo'                      => $result->getError()->getCode(),
                'mensaje'                     => $result->getError()->getMessage(),
                'notas'                       => '',
                'qr'                          => $minewqr,
                'nombre'                      => $invoice->getName(),
                's_idfacturacionnotadebito'   => $idfacturacionnotadebito,
                'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                'idtienda'                    => $data->idtienda,
            ]);
          
            DB::table('s_facturacionnotadebito')->whereId($idfacturacionnotadebito)->update([
                'idfacturacionrespuesta' => $idfacturacionrespuesta
            ]);
          
            return [
                'resultado' => 'ERROR',
                'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
            ];
        }
}
function facturador_notacredito($idfacturacionnotacredito){
  
        $data = DB::table('s_facturacionnotacredito')
            ->join('s_agencia','s_agencia.id','s_facturacionnotacredito.idagencia')
            ->where('s_facturacionnotacredito.id',$idfacturacionnotacredito)
            ->select(
                's_facturacionnotacredito.*',
                's_agencia.idestadofacturacion as idestadofacturacion',
                's_agencia.facturacion_usuario as sunat_usuario',
                's_agencia.facturacion_clave as sunat_clave',
                's_agencia.facturacion_certificado as sunat_certificado'
            )
            ->first();
  
        // Emisor
        $addressemisor = new Address();
        $addressemisor->setUbigueo($data->emisor_ubigeo)
            ->setDepartamento($data->emisor_departamento)
            ->setProvincia($data->emisor_provincia)
            ->setDistrito($data->emisor_distrito)
            ->setUrbanizacion($data->emisor_urbanizacion)
            ->setDireccion($data->emisor_direccion);

        $company = new Company();
        $company->setRuc($data->emisor_ruc)
            ->setRazonSocial($data->emisor_razonsocial)
            ->setNombreComercial($data->emisor_nombrecomercial)
            ->setAddress($addressemisor);
  
        // Cliente
        $client = new Client();
        $client->setTipoDoc($data->cliente_tipodocumento)
            ->setNumDoc($data->cliente_numerodocumento)
            ->setRznSocial($data->cliente_razonsocial);
      
        // Nota de credito
        $invoice = (new Note())
            ->setUblVersion($data->notacredito_ublversion)
            ->setTipDocAfectado($data->notacredito_tipodocafectado)
            ->setNumDocfectado($data->notacredito_numerodocumentoafectado)
            ->setCodMotivo($data->notacredito_codigomotivo)
            ->setDesMotivo($data->notacredito_descripcionmotivo)
            ->setTipoDoc($data->notacredito_tipodocumento)
            ->setSerie($data->notacredito_serie)
            ->setFechaEmision(\DateTime::createFromFormat('Y-m-d H:i:s', $data->notacredito_fechaemision))
            ->setCorrelativo($data->notacredito_correlativo)
            ->setTipoMoneda($data->notacredito_tipomoneda)
//             ->setGuias($data->notacredito_guias)
            ->setClient($client)
            ->setMtoOperGravadas($data->notacredito_montooperaciongravada)
            //->setMtoOperExoneradas(0.00)
            //->setMtoOperInafectas(0.00)
            ->setMtoIGV($data->notacredito_montoigv)
            ->setTotalImpuestos($data->notacredito_totalimpuestos)
            ->setMtoImpVenta($data->notacredito_montoimpuestoventa)
            ->setCompany($company);

        $datadetalles = DB::table('s_facturacionnotacreditodetalle')
                ->where('s_facturacionnotacreditodetalle.idfacturacionnotacredito',$data->id)
                ->orderBy('s_facturacionnotacreditodetalle.id','asc')
                ->get();
        $item = [];
        foreach($datadetalles as $value){
            $item[] = (new SaleDetail())
                ->setCodProducto($value->codigoproducto)
                ->setUnidad($value->unidad)
                ->setCantidad($value->cantidad)
                ->setDescripcion($value->descripcion)
                ->setMtoBaseIgv($value->montobaseigv)
                ->setPorcentajeIgv($value->porcentajeigv)
                ->setIgv($value->igv)
                ->setTipAfeIgv($value->tipoafectacionigv)
                ->setTotalImpuestos($value->totalimpuestos)
                ->setMtoValorVenta($value->montovalorventa)
                ->setMtoValorUnitario($value->montovalorunitario)
                ->setMtoPrecioUnitario($value->montopreciounitario);
        }

        // leyenda
        $legend = (new Legend())
            ->setCode($data->leyenda_codigo)
            ->setValue($data->leyenda_value);
      
        $invoice->setDetails($item)
                ->setLegends([$legend]);

        // Conexión SUNAT
        $facturador = facturador_conexion([
            'idtienda' => $data->idtienda,
            'idestadofacturacion' => $data->idestadofacturacion,
            'emisor_ruc' => $data->emisor_ruc,
            'sunat_usuario' => $data->sunat_usuario,
            'sunat_clave' => $data->sunat_clave,
            'sunat_certificado' => $data->sunat_certificado,
        ]);
  
        // QR
        $crearqr = new QrRender();
        $base64_qr = new ImageFilter();
        $minewqr = $base64_qr->toBase64($crearqr->getImage($invoice));
        // Fin QR
  
        // Envio SUNAT
        $result = $facturador['see']->send($invoice);
  
        // verificar si existe directorio
        $raiz = $facturador['raiz'].'notacredito/';
        if (!file_exists($raiz)) {
            mkdir($raiz, 0777);
        }
  
        // Guardar XML firmado digitalmente.
        file_put_contents($raiz.$invoice->getName().'.xml',$facturador['see']->getFactory()->getLastXml());

        // Verificamos que la conexión con SUNAT fue exitosa.
        if ($result->isSuccess()) {

            // Guardamos el CDR
            file_put_contents($raiz.'R-'.$invoice->getName().'.zip', $result->getCdrZip());
          
            $cdr = $result->getCdrResponse();
            $result_cdr = facturador_consulta($cdr);
          
            // Mostrar error al conectarse a SUNAT.
            
            $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                'fecharegistro'               => Carbon::now(),
                'estado'                      => $result_cdr['estado'],
                'codigo'                      => $result_cdr['codigo'],
                'mensaje'                     => $cdr->getDescription(),
                'notas'                       => $result_cdr['notas'],
                'qr'                          => $minewqr,
                'nombre'                      => $invoice->getName(),
                's_idfacturacionnotacredito'  => $idfacturacionnotacredito,
                'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                'idtienda'                    => $data->idtienda,
            ]);
          
            DB::table('s_facturacionnotacredito')->whereId($idfacturacionnotacredito)->update([
                'idfacturacionrespuesta' => $idfacturacionrespuesta
            ]);
          
            return [
                'resultado' => 'CORRECTO',
                'mensaje' => $cdr->getDescription(),
            ];
        }else{
            // Mostrar error al conectarse a SUNAT.
            
            $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                'fecharegistro'               => Carbon::now(),
                'estado'                      => 'ERROR',
                'codigo'                      => $result->getError()->getCode(),
                'mensaje'                     => $result->getError()->getMessage(),
                'notas'                       => '',
                'qr'                          => $minewqr,
                'nombre'                      => $invoice->getName(),
                's_idfacturacionnotacredito'  => $idfacturacionnotacredito,
                'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                'idtienda'                    => $data->idtienda,
            ]);
          
            DB::table('s_facturacionnotacredito')->whereId($idfacturacionnotacredito)->update([
                'idfacturacionrespuesta' => $idfacturacionrespuesta
            ]);
          
            return [
                'resultado' => 'ERROR',
                'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
            ];
        }
}
function facturador_comunicacionbaja($idfacturacioncomunicacionbaja) {
    
        $data = DB::table('s_facturacioncomunicacionbaja')
            ->join('s_agencia','s_agencia.id','s_facturacioncomunicacionbaja.idagencia')
            ->where('s_facturacioncomunicacionbaja.id', $idfacturacioncomunicacionbaja)
            ->select(
                's_facturacioncomunicacionbaja.*',
                's_agencia.idestadofacturacion as idestadofacturacion',
                's_agencia.facturacion_usuario as sunat_usuario',
                's_agencia.facturacion_clave as sunat_clave',
                's_agencia.facturacion_certificado as sunat_certificado'
            )
            ->first();
        // Emisor
        $addressemisor = new Address();
        $addressemisor->setUbigueo($data->emisor_ubigeo)
            ->setDepartamento($data->emisor_departamento)
            ->setProvincia($data->emisor_provincia)
            ->setDistrito($data->emisor_distrito)
            ->setUrbanizacion($data->emisor_urbanizacion)
            ->setDireccion($data->emisor_direccion);

        $company = new Company();
        $company->setRuc($data->emisor_ruc)
            ->setRazonSocial($data->emisor_razonsocial)
            ->setNombreComercial($data->emisor_nombrecomercial)
            ->setAddress($addressemisor);
  
        $data_detalle = DB::table('s_facturacioncomunicacionbajadetalle')->where('idfacturacioncomunicacionbaja', $data->id)->get();
        $item = [];
        foreach ($data_detalle as $value) {
          $item[] = (new VoidedDetail())
              ->setTipoDoc($value->tipodocumento)
              ->setSerie($value->serie)
              ->setCorrelativo($value->correlativo)
              ->setDesMotivoBaja($value->descripcionmotivobaja);
        }

  
        $voided = new Voided();
        $voided->setCorrelativo($data->comunicacionbaja_correlativo)
            // Fecha Generacion menor que Fecha comunicacion
            ->setFecGeneracion(new DateTime($data->comunicacionbaja_fechageneracion))
            ->setFecComunicacion(new DateTime($data->comunicacionbaja_fechacomunicacion))
            ->setCompany($company)
            ->setDetails($item);
  
  
  
        // Conexión SUNAT
        $facturador = facturador_conexion([
            'idtienda' => $data->idtienda,
            'idestadofacturacion' => $data->idestadofacturacion,
            'emisor_ruc' => $data->emisor_ruc,
            'sunat_usuario' => $data->sunat_usuario,
            'sunat_clave' => $data->sunat_clave,
            'sunat_certificado' => $data->sunat_certificado,
        ]);
  
        // Envio SUNAT
        $result = $facturador['see']->send($voided);

        // verificar si existe directorio
        $raiz = $facturador['raiz'].'comunicacionbaja/';
        if (!file_exists($raiz)) {
            mkdir($raiz, 0777);
        }
  
        // Guardar XML firmado digitalmente.
        file_put_contents($raiz.$voided->getName().'.xml',$facturador['see']->getFactory()->getLastXml());

        // Verificamos que la conexión con SUNAT fue exitosa.
        if ($result->isSuccess()) {
          
            
            $ticket = $result->getTicket();
            $result = $facturador['see']->getStatus($ticket);

            // Guardamos el CDR
            file_put_contents($raiz.'R-'.$voided->getName().'.zip', $result->getCdrZip());
          
            if ($result->isSuccess()) {
              
                $cdr = $result->getCdrResponse();
                $result_cdr = facturador_consulta($cdr);

                // Mostrar error al conectarse a SUNAT.
                
                $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                    'fecharegistro'                   => Carbon::now(),
                    'estado'                          => $result_cdr['estado'],
                    'codigo'                          => $result_cdr['codigo'],
                    'mensaje'                         => $cdr->getDescription(),
                    'notas'                           => $result_cdr['notas'],
                    'qr'                              => '',
                    'nombre'                          => $voided->getName(),
                    's_idfacturacioncomunicacionbaja' => $idfacturacioncomunicacionbaja,
                    'idestadofacturacion'             => $data->idestadofacturacion, // 1=produccion, 2=beta
                    'idtienda'                        => $data->idtienda,
                ]);
          
                DB::table('s_facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
                    'idfacturacionrespuesta' => $idfacturacionrespuesta
                ]);
              
                return [
                    'resultado' => 'CORRECTO',
                    'mensaje' => $cdr->getDescription(),
                ];
            }else{
                // Mostrar error al conectarse a SUNAT.
                
                $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                    'fecharegistro'                   => Carbon::now(),
                    'estado'                          => 'ERROR',
                    'codigo'                          => $result->getError()->getCode(),
                    'mensaje'                         => $result->getError()->getMessage(),
                    'notas'                           => '',
                    'qr'                              => '',
                    'nombre'                          => $voided->getName(),
                    's_idfacturacioncomunicacionbaja' => $idfacturacioncomunicacionbaja,
                    'idestadofacturacion'             => $data->idestadofacturacion, // 1=produccion, 2=beta
                    'idtienda'                        => $data->idtienda,
                ]);
          
                DB::table('s_facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
                    'idfacturacionrespuesta' => $idfacturacionrespuesta
                ]);

                return [
                    'resultado' => 'ERROR',
                    'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                ];
            }
        }else{
                $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                    'fecharegistro'                   => Carbon::now(),
                    'estado'                          => 'ERROR',
                    'codigo'                          => $result->getError()->getCode(),
                    'mensaje'                         => $result->getError()->getMessage(),
                    'notas'                           => '',
                    'qr'                              => '',
                    'nombre'                          => $voided->getName(),
                    's_idfacturacioncomunicacionbaja' => $idfacturacioncomunicacionbaja,
                    'idestadofacturacion'             => $data->idestadofacturacion, // 1=produccion, 2=beta
                    'idtienda'                        => $data->idtienda,
                ]);
          
                DB::table('s_facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
                    'idfacturacionrespuesta' => $idfacturacionrespuesta
                ]);

                return [
                    'resultado' => 'ERROR',
                    'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                ];
        }
  
  
  
  
 
  /*if ($res->isSuccess()) { 
     $result = $facturador['see']->getStatus($res->getTicket());
     if ($result->isSuccess()) {
          file_put_contents($facturador['raiz'].'comunicacionbaja/'.$voided->getName().'.xml', $facturador['see']->getFactory()->getLastXml());
          file_put_contents($facturador['raiz'].'comunicacionbaja/'.'R-'.$voided->getName().'.zip', $result->getCdrZip());

          DB::table('facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
              'ticket' => $res->getTicket(),
              'estadofacturacion' => $result->getCdrResponse()->getDescription(),
              'idestadofacturacion' => 1, // correcto
              'idestadosunat' => $data->facturador_idestado
          ]);

          return [
              'resultado' => 'CORRECTO',
              'mensaje' => $result->getCdrResponse()->getDescription(),
              'data' => $data,
          ];
      } else {
              DB::table('facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
                  'ticket' => $res->getTicket(),
                  'estadofacturacion' => $result->getError()->getMessage(),
                  'idestadofacturacion' => 2, // error
                  'idestadosunat' => $data->facturador_idestado
             ]);

              return [
                  'resultado' => 'ERROR',
                  'mensaje' => $result->getError()->getMessage(),
                  'data' => $data,
              ];
      }
  }else{
        // 0098 = El procesamiento del comprobante aún no ha terminado
        // 0402 = La numeracion o nombre del documento ya ha sido enviado anteriormente'
        if($res->getError()->getCode()=='0402'){
            DB::table('facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
                'ticket' => $res->getTicket(),
                'estadofacturacion' => $res->getError()->getMessage(),
                'idestadofacturacion' => 1, // REENVIADO correcto
                'idestadosunat' => $data->facturador_idestado
            ]);

            return [
                'resultado' => 'CORRECTO',
                'mensaje' => $res->getError()->getMessage(),
                'data' => $data,
            ];
        }else{
            DB::table('facturacioncomunicacionbaja')->whereId($idfacturacioncomunicacionbaja)->update([
                'ticket' => $res->getTicket(),
                'estadofacturacion' => $res->getError()->getMessage(),
                'idestadofacturacion' => 2, // error
                'idestadosunat' => $data->facturador_idestado
            ]);

            return [
                'resultado' => 'ERROR',
                'mensaje' => $res->getError()->getMessage(),
                'data' => $data,
            ];
        }
            
  }*/
}
function facturador_resumendiario($idfacturacionresumendiario) {
        
        $data = DB::table('s_facturacionresumendiario')
            ->join('s_agencia','s_agencia.id','s_facturacionresumendiario.idagencia')
            ->where('s_facturacionresumendiario.id', $idfacturacionresumendiario)
            ->select(
                's_facturacionresumendiario.*',
                's_agencia.idestadofacturacion as idestadofacturacion',
                's_agencia.facturacion_usuario as sunat_usuario',
                's_agencia.facturacion_clave as sunat_clave',
                's_agencia.facturacion_certificado as sunat_certificado'
            )
            ->first();
  
        // Emisor
        $addressemisor = new Address();
        $addressemisor->setUbigueo($data->emisor_ubigeo)
            ->setDepartamento($data->emisor_departamento)
            ->setProvincia($data->emisor_provincia)
            ->setDistrito($data->emisor_distrito)
            ->setUrbanizacion($data->emisor_urbanizacion)
            ->setDireccion($data->emisor_direccion);

        $company = new Company();
        $company->setRuc($data->emisor_ruc)
            ->setRazonSocial($data->emisor_razonsocial)
            ->setNombreComercial($data->emisor_nombrecomercial)
            ->setAddress($addressemisor);

        $data_detalle = DB::table('s_facturacionresumendiariodetalle')->where('idfacturacionresumendiario', $data->id)->get();

        // Resumen
        $item = [];
        foreach ($data_detalle as $value) {
            $item[] = (new SummaryDetail())
                ->setTipoDoc($value->tipodocumento)
                ->setSerieNro($value->serienumero)
                ->setEstado($value->estado)
                ->setClienteTipo($value->clientetipo)
                ->setClienteNro($value->clientenumero)
                ->setTotal($value->total)
                ->setMtoOperGravadas($value->operacionesgravadas)
                ->setMtoOperInafectas($value->operacionesinafectas)
                ->setMtoOperExoneradas($value->operacionesexoneradas)
                ->setMtoOtrosCargos($value->otroscargos)
                ->setMtoIGV($value->montoigv);
        }

        $summary = (new Summary())
            ->setCorrelativo($data->resumen_correlativo)
            ->setFecGeneracion(date_create($data->resumen_fechageneracion))
            ->setFecResumen(date_create($data->resumen_fecharesumen))
            ->setCompany($company)
            ->setDetails($item);
  
  
        // Conexión SUNAT
        $facturador = facturador_conexion([
            'idtienda' => $data->idtienda,
            'idestadofacturacion' => $data->idestadofacturacion,
            'emisor_ruc' => $data->emisor_ruc,
            'sunat_usuario' => $data->sunat_usuario,
            'sunat_clave' => $data->sunat_clave,
            'sunat_certificado' => $data->sunat_certificado,
        ]);
  
        // Envio SUNAT
        $result = $facturador['see']->send($summary);

        // verificar si existe directorio
        $raiz = $facturador['raiz'].'resumendiario/';
        if (!file_exists($raiz)) {
            mkdir($raiz, 0777);
        }
  
        // Guardar XML firmado digitalmente.
        file_put_contents($raiz.$summary->getName().'.xml',$facturador['see']->getFactory()->getLastXml());
        DB::table('s_facturacionresumendiario')->whereId($idfacturacionresumendiario)->update([
          'nroticket' => $result->getTicket()
        ]);
        // Verificamos que la conexión con SUNAT fue exitosa.
        if ($result->isSuccess()) {
          
            
            $ticket = $result->getTicket();
            $result = $facturador['see']->getStatus($ticket);

            // Guardamos el CDR
            file_put_contents($raiz.'R-'.$summary->getName().'.zip', $result->getCdrZip());
          
            if ($result->isSuccess()) {
              
                $cdr = $result->getCdrResponse();
                $result_cdr = facturador_consulta($cdr);

                // Mostrar error al conectarse a SUNAT.
                
                $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                    'fecharegistro'               => Carbon::now(),
                    'estado'                      => $result_cdr['estado'],
                    'codigo'                      => $result_cdr['codigo'],
                    'mensaje'                     => $cdr->getDescription(),
                    'notas'                       => $result_cdr['notas'],
                    'qr'                          => '',
                    'nombre'                      => $summary->getName(),
                    's_idfacturacionresumendiario'=> $idfacturacionresumendiario,
                    'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                    'idtienda'                    => $data->idtienda,
                ]);
          
                DB::table('s_facturacionresumendiario')->whereId($idfacturacionresumendiario)->update([
                    'idfacturacionrespuesta' => $idfacturacionrespuesta
                ]);
              
                return [
                    'resultado' => 'CORRECTO',
                    'mensaje' => $cdr->getDescription(),
                ];
            }else{
                // Mostrar error al conectarse a SUNAT.
                
                $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                    'fecharegistro'               => Carbon::now(),
                    'estado'                      => 'ERROR',
                    'codigo'                      => $result->getError()->getCode(),
                    'mensaje'                     => $result->getError()->getMessage(),
                    'notas'                       => '',
                    'qr'                          => '',
                    'nombre'                      => $summary->getName(),
                    's_idfacturacionresumendiario'=> $idfacturacionresumendiario,
                    'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                    'idtienda'                    => $data->idtienda,
                ]);
          
                DB::table('s_facturacionresumendiario')->whereId($idfacturacionresumendiario)->update([
                    'idfacturacionrespuesta' => $idfacturacionrespuesta
                ]);

                return [
                    'resultado' => 'CORRECTO',
                    'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                ];
            }
        }
        else{
                $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                    'fecharegistro'               => Carbon::now(),
                    'estado'                      => 'ERROR',
                    'codigo'                      => $result->getError()->getCode(),
                    'mensaje'                     => $result->getError()->getMessage(),
                    'notas'                       => '',
                    'qr'                          => '',
                    'nombre'                      => $summary->getName(),
                    's_idfacturacionresumendiario'=> $idfacturacionresumendiario,
                    'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                    'idtienda'                    => $data->idtienda,
                ]);
          
                DB::table('s_facturacionresumendiario')->whereId($idfacturacionresumendiario)->update([
                    'idfacturacionrespuesta' => $idfacturacionrespuesta
                ]);

                return [
                    'resultado' => 'ERROR',
                    'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
                ];
        }
}
function facturador_guiaremision($idfacturacionguiaremision) {
        $data = DB::table('s_facturacionguiaremision')
            ->join('s_agencia','s_agencia.id','s_facturacionguiaremision.idagencia')
            ->where('s_facturacionguiaremision.id',$idfacturacionguiaremision)
            ->select(
                's_facturacionguiaremision.*',
                's_agencia.idestadofacturacion as idestadofacturacion',
                's_agencia.facturacion_usuario as sunat_usuario',
                's_agencia.facturacion_clave as sunat_clave',
                's_agencia.facturacion_certificado as sunat_certificado'
            )
            ->first();
  
        // Emisor
        $addressemisor = new Address();
        $addressemisor->setUbigueo($data->emisor_ubigeo)
            ->setDepartamento($data->emisor_departamento)
            ->setProvincia($data->emisor_provincia)
            ->setDistrito($data->emisor_distrito)
            ->setUrbanizacion($data->emisor_urbanizacion)
            ->setDireccion($data->emisor_direccion);

        $company = new Company();
        $company->setRuc($data->emisor_ruc)
            ->setRazonSocial($data->emisor_razonsocial)
            ->setNombreComercial($data->emisor_nombrecomercial)
            ->setAddress($addressemisor);

        $rel = new Document();
            //$rel->setTipoDoc('02') // Tipo: Numero de Orden de Entrega
            //->setNroDoc('213123');

        $transp = new Transportist();
        $transp->setTipoDoc($data->transporte_tipodocumento)
            ->setNumDoc($data->transporte_numerodocumento)
            ->setRznSocial($data->transporte_razonsocial)
            ->setPlaca($data->transporte_placa)
            ->setChoferTipoDoc($data->transporte_chofertipodocumento)
            ->setChoferDoc($data->transporte_choferdocumento);

        $envio = new Shipment();
        $envio->setCodTraslado($data->envio_codigotraslado) // Cat.20
            ->setDesTraslado($data->envio_descripciontraslado)
            ->setModTraslado($data->envio_modtraslado) // Cat.18
            ->setFecTraslado(date_create($data->envio_fechatraslado))
            //->setCodPuerto($data->envio_codigopuerto)
            //->setIndTransbordo($data->envio_indtransbordo)
            //->setPesoTotal($data->envio_pesototal)
            ->setUndPesoTotal($data->envio_unidadpesototal)
            //->setNumBultos(2) // Solo válido para importaciones
            //->setNumContenedor($data->envio_numerocontenedor)
            ->setLlegada(new Direction($data->envio_direccionllegadacodigoubigeo, $data->envio_direccionllegada))
            ->setPartida(new Direction($data->envio_direccionpartidacodigoubigeo, $data->envio_direccionpartida))
            ->setTransportista($transp);


        $despatch = new Despatch();
        $despatch->setTipoDoc($data->despacho_tipodocumento)
            ->setSerie($data->despacho_serie)
            ->setCorrelativo($data->despacho_correlativo)
            ->setFechaEmision(date_create($data->despacho_fechaemision))
            ->setCompany($company)
            ->setDestinatario((new Client())
                ->setTipoDoc($data->despacho_destinatario_tipodocumento)
                ->setNumDoc($data->despacho_destinatario_numerodocumento)
                ->setRznSocial($data->despacho_destinatario_razonsocial))
            //->setTercero((new Client())
                //->setTipoDoc('6')
                //->setNumDoc($data->despacho_tercero_numerodocumento)
                //->setRznSocial($data->despacho_tercero_razonsocial))
            ->setObservacion($data->despacho_observacion)
            //->setRelDoc($rel)
            ->setEnvio($envio);

          $data_detalle = DB::table('s_facturacionguiaremisiondetalle')->where('idfacturacionguiaremision', $idfacturacionguiaremision)->get();

          $item = [];
          foreach($data_detalle as $value){
              $item[] = (new DespatchDetail())
                  ->setCantidad($value->cantidad)
                  ->setUnidad($value->unidad)
                  ->setDescripcion($value->descripcion)
                  ->setCodigo($value->codigo)
                  ->setCodProdSunat($value->codprodsunat);
          }
  
  
  
        // Conexión SUNAT
        $facturador = facturador_conexion([
            'idtienda' => $data->idtienda,
            'idestadofacturacion' => $data->idestadofacturacion,
            'emisor_ruc' => $data->emisor_ruc,
            'sunat_usuario' => $data->sunat_usuario,
            'sunat_clave' => $data->sunat_clave,
            'sunat_certificado' => $data->sunat_certificado,
        ],'GUIA');
  
        // Envio SUNAT
        $despatch->setDetails($item);
        $result = $facturador['see']->send($despatch);
  
        // verificar si existe directorio
        $raiz = $facturador['raiz'].'guiaremision/';
        if (!file_exists($raiz)) {
            mkdir($raiz, 0777);
        }
  
        // Guardar XML firmado digitalmente.
        file_put_contents($raiz.$despatch->getName().'.xml',$facturador['see']->getFactory()->getLastXml());

        // Verificamos que la conexión con SUNAT fue exitosa.
        if ($result->isSuccess()) {
          
            // Guardamos el CDR
            file_put_contents($raiz.'GR-'.$despatch->getName().'.zip', $result->getCdrZip());
          
            $cdr = $result->getCdrResponse();
            $result_cdr = facturador_consulta($cdr);
          
            // Mostrar error al conectarse a SUNAT.
            
            $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                'fecharegistro'               => Carbon::now(),
                'estado'                      => $result_cdr['estado'],
                'codigo'                      => $result_cdr['codigo'],
                'mensaje'                     => $cdr->getDescription(),
                'notas'                       => $result_cdr['notas'],
                'qr'                          => '',
                'nombre'                      => $despatch->getName(),
                's_idfacturacionguiaremision'=> $idfacturacionguiaremision,
                'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                'idtienda'                    => $data->idtienda,
            ]);
          
                DB::table('s_facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
                    'idfacturacionrespuesta' => $idfacturacionrespuesta
                ]);
          
            return [
                'resultado' => 'CORRECTO',
                'mensaje' => $cdr->getDescription(),
            ];
        }else{
            // Mostrar error al conectarse a SUNAT.
            
            $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
                'fecharegistro'               => Carbon::now(),
                'estado'                      => 'ERROR',
                'codigo'                      => $result->getError()->getCode(),
                'mensaje'                     => $result->getError()->getMessage(),
                'notas'                       => '',
                'qr'                          => '',
                'nombre'                      => $despatch->getName(),
                's_idfacturacionguiaremision'=> $idfacturacionguiaremision,
                'idestadofacturacion'         => $data->idestadofacturacion, // 1=produccion, 2=beta
                'idtienda'                    => $data->idtienda,
            ]);
          
                DB::table('s_facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
                    'idfacturacionrespuesta' => $idfacturacionrespuesta
                ]);
          
            return [
                'resultado' => 'ERROR',
                'mensaje' => $result->getError()->getCode().' - '.$result->getError()->getMessage(),
            ];
        }
} 

function facturar_venta($idtienda,$idtipocomprobante,$idagencia,$idventa,$idcliente=0){
      $agencia = DB::table('s_agencia')
          ->join('ubigeo','ubigeo.id','s_agencia.idubigeo')
          ->where('s_agencia.id',$idagencia)
          ->where('idestadofacturacion',1)
          ->select(
              's_agencia.ruc as agenciaruc',
              's_agencia.razonsocial as agenciarazonsocial',
              's_agencia.nombrecomercial as agencianombrecomercial',
              's_agencia.facturacion_serie as facturacionserie',
              's_agencia.direccion as agenciadireccion',
              's_agencia.facturacion_correlativoinicial as facturacioncorrelativoinicial',
              'ubigeo.codigo as agenciaubigeocodigo',
              'ubigeo.departamento as agenciaubigeodepartamento',
              'ubigeo.provincia as agenciaubigeoprovincia',
              'ubigeo.distrito as agenciaubigeodistrito',
          )
          ->first();
    if($agencia==''){
          return [
            'resultado' => 'ERROR',
            'mensaje'   => 'La Empresa no existe, vuelva a intentar.'
          ];
    }else{
        $venta = DB::table('s_venta')
            ->join('users as cliente','cliente.id','s_venta.s_iduserscliente')
            // ->leftJoin('ubigeo as clienteubigeo','clienteubigeo.id','s_venta.cliente_idubigeo')
            ->join('tienda','tienda.id','s_venta.idtienda')
            ->leftJoin('s_ubigeo as tiendaubigeo','tiendaubigeo.id','tienda.idubigeo')
            ->join('s_moneda','s_moneda.id','s_venta.s_idmoneda')
            ->where('s_venta.idtienda',$idtienda)
            ->where('s_venta.id',$idventa)
            ->select(
                's_venta.*',
                // 'cliente.identificacion as clienteidentificacion',
            //   DB::raw('IF(cliente.idtipopersona=1,
            //   CONCAT(cliente.apellidos,", ",cliente.nombre),
            //   CONCAT(cliente.apellidos)) as clienterazoncial'),
                // 'cliente.nombrecompleto as clienterazoncial',
                DB::raw('IF(cliente.idtipopersona=1,1,6) as clientetipodocumento'),

                // 'clienteubigeo.codigo as clienteubigeocodigo',
                // 'clienteubigeo.departamento as clienteubigeodepartamento',
                // 'clienteubigeo.provincia as clienteubigeoprovincia',
                // 'clienteubigeo.distrito as clienteubigeodistrito',

                'tiendaubigeo.codigo as tiendaubigeocodigo',
                'tiendaubigeo.departamento as tiendaubigeodepartamento',
                'tiendaubigeo.provincia as tiendaubigeoprovincia',
                'tiendaubigeo.distrito as tiendaubigeodistrito',
                'tienda.direccion as tiendadireccion',

                's_moneda.codigo as monedacodigo',
                's_moneda.nombre as monedanombre',
          )
          ->first();

          if($venta->totalredondeado<=0){
              return [
                'resultado' => 'ERROR',
                'mensaje'   => 'El Total no puede ser menor o igual a 0.00.'
              ];
          }else{
              // Validando el tipo de comprobante que se va emitir
              if($idtipocomprobante==2) {
                  $venta_tipodocumento  = '03';
                  $venta_serie          = 'B'.str_pad($agencia->facturacionserie, 3, "0", STR_PAD_LEFT);
              }else if($idtipocomprobante==3) {
                  $venta_tipodocumento  = '01';
                  $venta_serie          = 'F'.str_pad($agencia->facturacionserie, 3, "0", STR_PAD_LEFT);
              }

              // Agregando el correlativo del comprobante
              $correlativo = DB::table('s_facturacionboletafactura')
                  ->where('venta_tipodocumento',$venta_tipodocumento)
                  ->where('emisor_ruc',$agencia->agenciaruc)
                  ->where('venta_serie',$venta_serie)
                  ->orderBy('venta_correlativo','desc')
                  ->limit(1)
                  ->first();
              
              if(!is_null($correlativo) ){
                  $venta_correlativo = $correlativo->venta_correlativo+1;
              }else{
                  $venta_correlativo = $agencia->facturacioncorrelativoinicial;
              }
              
                $facturacion_igv = configuracion($idtienda,'facturacion_igv')['resultado']=='CORRECTO'?configuracion($idtienda,'facturacion_igv')['valor']:18;
                $igv = ($facturacion_igv/100)+1;
                $total = number_format($venta->totalventa, 2, '.', '');
                $subtotal = number_format($total/$igv, 2, '.', '');
                $impuesto = number_format($total-$subtotal, 2, '.', '');   
            
                $clientetipodocumento     = $venta->clientetipodocumento;
                $clientenumerodocumento   = $venta->db_idusersclienteidentificacion;
                $clienterazonsocial       = $venta->db_iduserscliente;

                $clienteubigeo            = 'NONE';
                $clientedepartamento      = 'NONE';
                $clienteprovincia         = 'NONE';
                $clientedistrito          = 'NONE';
                
                // $clienteubigeo            = $venta->clienteubigeocodigo;
                // $clientedepartamento      = $venta->clienteubigeodepartamento;
                // $clienteprovincia         = $venta->clienteubigeoprovincia;
                // $clientedistrito          = $venta->clienteubigeodistrito;
                
                if($idcliente!=0){
                    $cliente = DB::table('users as cliente')
                        ->where('cliente.id',$idcliente)
                          ->select(
                            'cliente.nombrecompleto as nombrecompleto',
                            'cliente.identificacion as clienteidentificacion',
                            DB::raw('IF(cliente.idtipopersona=1,1,6) as clientetipodocumento')
                        )
                        ->first();
                    $clientetipodocumento     = $cliente->clientetipodocumento;
                    $clientenumerodocumento   = $cliente->clienteidentificacion;
                    $clienterazonsocial       = $cliente->nombrecompleto;


                    // $clienteubigeo            = $cliente->clienteubigeocodigo;
                    // $clientedepartamento      = $cliente->clienteubigeodepartamento;
                    // $clienteprovincia         = $cliente->clienteubigeoprovincia;
                    // $clientedistrito          = $cliente->clienteubigeodistrito;
                    $clienteubigeo            = 'NONE';
                    $clientedepartamento      = 'NONE';
                    $clienteprovincia         = 'NONE';
                    $clientedistrito          = 'NONE';
                }
                

                $idfacturacionboletafactura = DB::table('s_facturacionboletafactura')->insertGetId([
                      'emisor_ruc'                  => $agencia->agenciaruc,
                      'emisor_razonsocial'          => $agencia->agenciarazonsocial,
                      'emisor_nombrecomercial'      => $agencia->agencianombrecomercial,
                      'emisor_ubigeo'               => $agencia->agenciaubigeocodigo,
                      'emisor_departamento'         => $agencia->agenciaubigeodepartamento,
                      'emisor_provincia'            => $agencia->agenciaubigeoprovincia,
                      'emisor_distrito'             => $agencia->agenciaubigeodistrito,
                      'emisor_urbanizacion'         => '',
                      'emisor_direccion'            => $agencia->agenciadireccion,
                      'cliente_tipodocumento'       => $clientetipodocumento,
                      'cliente_numerodocumento'     => $clientenumerodocumento,
                      'cliente_razonsocial'         => $clienterazonsocial,
                      'cliente_ubigeo'              => $clienteubigeo!=''?$clienteubigeo:'',
                      'cliente_departamento'        => $clientedepartamento!=''?$clientedepartamento:'',
                      'cliente_provincia'           => $clienteprovincia!=''?$clienteprovincia:'',
                      'cliente_distrito'            => $clientedistrito!=''?$clientedistrito:'',
                      'cliente_urbanizacion'        => '',
                      'cliente_direccion'           => $venta->db_idusersclientedireccion,
                      'venta_ublversion'            => '2.1',
                      'venta_tipooperacion'         => '0101',
                      'venta_tipodocumento'         => $venta_tipodocumento,
                      'venta_serie'                 => $venta_serie,
                      'venta_correlativo'           => $venta_correlativo,
                      'venta_fechaemision'          => Carbon::now(),
                      'venta_tipomoneda'            => $venta->monedacodigo,
                      'venta_montooperaciongravada' => $subtotal,
                      'venta_montoigv'              => $impuesto,
                      'venta_totalimpuestos'        => $impuesto,
                      'venta_valorventa'            => $subtotal,
                      'venta_subtotal'              => $total,
                      'venta_montoimpuestoventa'    => $total,
                      'venta_igv'                   => $facturacion_igv,
                      'leyenda_codigo'              => '1000',
                      'leyenda_value'               => NumeroALetras::convertir(number_format($total,2, '.', '')).' CON  00/100 '.$venta->monedanombre,
                      'idventa'                     => $venta->id,
                      'idagencia'                   => $venta->s_idagencia,
                      'idtienda'                    => $venta->idtienda,
                      'idusuarioresponsable'        => Auth::user()->id,
                      'idusuariocliente'            => $venta->s_iduserscliente,
                    //'idestadofacturacion'       => 0,
                    //'idestadosunat'             => 1 // pendiente
                  ]);


              foreach(json_decode($venta->db_ventadetalle) as $value){

                    $unidad_medida_cpe = DB::table('s_unidadmedida')->whereId($value->idunidadmedida)->first();
                      $subtotal = number_format($value->total/$igv, 2, '.', '');
                      $impuesto = number_format($value->total-$subtotal, 2, '.', '');
                      $montovalorunitario = number_format($value->preciounitario/$igv, 2, '.', '');

                      DB::table('s_facturacionboletafacturadetalle')->insert([
                            'codigoproducto'             => str_pad($value->codigo, 6, "0", STR_PAD_LEFT),
                            'unidad'                     => $unidad_medida_cpe->codigo,
                            'cantidad'                   => $value->cantidad,
                            'descripcion'                => $value->concepto.($value->detalle!=''?' ('.strtoupper($value->detalle).')':''),
                            'montobaseigv'               => $subtotal,
                            'porcentajeigv'              => $facturacion_igv,
                            'igv'                        => $impuesto,
                            'tipoafectacionigv'          => '10',
                            'totalimpuestos'             => $impuesto,
                            'montovalorventa'            => $subtotal,
                            'montovalorunitario'         => $montovalorunitario,
                            'montopreciounitario'        => $value->preciounitario,
                            'idproducto'                 => $value->idproducto,
                            'idfacturacionboletafactura' => $idfacturacionboletafactura,
                            'idtienda'                   => $idtienda,
                       ]);
              }
            
              $result = facturador_facturaboleta($idfacturacionboletafactura);
            
              return [
                  'resultado' => $result['resultado'],
                  'mensaje'   => $result['mensaje'],
                  'idfacturacionboletafactura'   => $idfacturacionboletafactura
              ];
          }  
    }      
}

/*function facturar_prestamo($idtienda,$idtipocomprobante,$idagencia,$idcreditoprestamo){
      $agencia = DB::table('s_agencia')
          ->where('id',$idagencia)
          ->where('idestadofacturacion',1)
          ->select(
              's_agencia.ruc as agenciaruc',
              's_agencia.razonsocial as agenciarazonsocial',
              's_agencia.nombrecomercial as agencianombrecomercial',
              's_agencia.facturacion_serie as facturacionserie',
              's_agencia.facturacion_correlativoinicial as facturacioncorrelativoinicial',
          )
          ->first();
    if($agencia==''){
          return [
            'resultado' => 'ERROR',
            'mensaje'   => 'La Empresa no existe, vuelva a intentar.'
          ];
    }else{
        $prestamocobranza = DB::table('s_prestamo_cobranza')
            ->join('users as cliente','cliente.id','s_prestamo_cobranza.idcliente')
            ->leftJoin('ubigeo as clienteubigeo','clienteubigeo.id','cliente.idubigeo')
            ->join('tienda','tienda.id','s_prestamo_cobranza.idtienda')
            ->leftJoin('ubigeo as tiendaubigeo','tiendaubigeo.id','tienda.idubigeo')
            ->leftJoin('s_configuracionfacturacion','s_configuracionfacturacion.idtienda','tienda.id')
            ->join('s_moneda','s_moneda.id','s_prestamo_cobranza.idmoneda')
            ->where('s_prestamo_cobranza.idtienda',$idtienda)
            ->where('s_prestamo_cobranza.id',$idcreditoprestamo)
            ->select(
              's_prestamo_cobranza.*',
              's_configuracionfacturacion.igv as facturacionigv',
              'cliente.identificacion as clienteidentificacion',
              DB::raw('IF(cliente.idtipopersona=1,
              CONCAT(cliente.apellidos,", ",cliente.nombre),
              CONCAT(cliente.apellidos)) as clienterazoncial'),
              DB::raw('IF(cliente.idtipopersona=1,1,6) as clientetipodocumento'),
              'clienteubigeo.codigo as clienteubigeocodigo',
              'clienteubigeo.departamento as clienteubigeodepartamento',
              'clienteubigeo.provincia as clienteubigeoprovincia',
              'clienteubigeo.distrito as clienteubigeodistrito',
              'tiendaubigeo.codigo as tiendaubigeocodigo',
              'tiendaubigeo.departamento as tiendaubigeodepartamento',
              'tiendaubigeo.provincia as tiendaubigeoprovincia',
              'tiendaubigeo.distrito as tiendaubigeodistrito',
              'tienda.direccion as tiendadireccion',
              's_moneda.codigo as monedacodigo',
              's_moneda.nombre as monedanombre',
          )
          ->first();
          if($prestamocobranza->select_cuotaapagarredondeado<=0){
              return [
                'resultado' => 'ERROR',
                'mensaje'   => 'El Total no puede ser menor o igual a 0.00.'
              ];
          }else{
              // Validando el tipo de comprobante que se va emitir
              if($idtipocomprobante==2) {
                  $venta_tipodocumento  = '03';
                  $venta_serie          = 'B'.str_pad($agencia->facturacionserie, 3, "0", STR_PAD_LEFT);
              }else if($idtipocomprobante==3) {
                  $venta_tipodocumento  = '01';
                  $venta_serie          = 'F'.str_pad($agencia->facturacionserie, 3, "0", STR_PAD_LEFT);
              }

              // Agregando el correlativo del comprobante
              $correlativo = DB::table('s_facturacionboletafactura')
                  ->where('venta_tipodocumento',$venta_tipodocumento)
                  ->where('emisor_ruc',$agencia->agenciaruc)
                  ->where('venta_serie',$venta_serie)
                  ->orderBy('venta_correlativo','desc')
                  ->limit(1)
                  ->first();

              if(!is_null($correlativo) ){
                  $venta_correlativo = $correlativo->venta_correlativo+1;
              }else{
                  $venta_correlativo = $agencia->facturacioncorrelativoinicial;
              }

                $igv = ($prestamocobranza->facturacionigv/100)+1;
                $total = number_format($prestamocobranza->total, 2, '.', '');
                $subtotal = number_format($total/$igv, 2, '.', '');
                $impuesto = number_format($total-$subtotal, 2, '.', '');        

                $idfacturacionboletafactura = DB::table('s_facturacionboletafactura')->insertGetId([
                      'emisor_ruc'                  => $agencia->agenciaruc,
                      'emisor_razonsocial'          => $agencia->agenciarazonsocial,
                      'emisor_nombrecomercial'      => $agencia->agencianombrecomercial,
                      'emisor_ubigeo'               => $prestamocobranza->tiendaubigeocodigo,
                      'emisor_departamento'         => $prestamocobranza->tiendaubigeodepartamento,
                      'emisor_provincia'            => $prestamocobranza->tiendaubigeoprovincia,
                      'emisor_distrito'             => $prestamocobranza->tiendaubigeodistrito,
                      'emisor_urbanizacion'         => '',
                      'emisor_direccion'            => $prestamocobranza->tiendadireccion,
                      'cliente_tipodocumento'       => $prestamocobranza->clientetipodocumento,
                      'cliente_numerodocumento'     => $prestamocobranza->clienteidentificacion,
                      'cliente_razonsocial'         => $prestamocobranza->clienterazoncial,
                      'cliente_ubigeo'              => isset($prestamocobranza->clienteubigeocodigo)?$prestamocobranza->clienteubigeocodigo:'',
                      'cliente_departamento'        => $prestamocobranza->clienteubigeodepartamento,
                      'cliente_provincia'           => $prestamocobranza->clienteubigeoprovincia,
                      'cliente_distrito'            => $prestamocobranza->clienteubigeodistrito,
                      'cliente_urbanizacion'        => '',
                      'cliente_direccion'           => $prestamocobranza->cliente_direccion,
                      'venta_ublversion'            => '2.1',
                      'venta_tipooperacion'         => '0101',
                      'venta_tipodocumento'         => $venta_tipodocumento,
                      'venta_serie'                 => $venta_serie,
                      'venta_correlativo'           => $venta_correlativo,
                      'venta_fechaemision'          => Carbon::now(),
                      'venta_tipomoneda'            => $prestamocobranza->monedacodigo,
                      'venta_montooperaciongravada' => $subtotal,
                      'venta_montoigv'              => $impuesto,
                      'venta_totalimpuestos'        => $impuesto,
                      'venta_valorventa'            => $subtotal,
                      'venta_subtotal'              => $total,
                      'venta_montoimpuestoventa'    => $total,
                      'venta_igv'                   => $prestamocobranza->facturacionigv,
                      'leyenda_codigo'              => '1000',
                      'leyenda_value'               => NumeroALetras::convertir(number_format($total,2, '.', '')).' CON  00/100 '.$prestamocobranza->monedanombre,
                      'idventa'                     => $prestamocobranza->id,
                      'idagencia'                   => $prestamocobranza->s_idagencia,
                      'idtienda'                    => $prestamocobranza->idtienda,
                      'idusuarioresponsable'        => Auth::user()->id,
                      'idusuariocliente'            => $prestamocobranza->s_idusuariocliente,
                    //'idestadofacturacion'       => 0,
                    //'idestadosunat'             => 1 // pendiente
                  ]);

              $ventadetalle = DB::table('s_prestamo_creditodetalle')
                    ->join('s_producto','s_producto.id','s_prestamo_creditodetalle.s_idproducto')
                    ->join('unidadmedida','unidadmedida.id','s_prestamo_creditodetalle.idunidadmedida')
                    ->where('s_prestamo_creditodetalle.s_idventa',$prestamocobranza->id)
                    ->select(
                        's_prestamo_creditodetalle.*',
                        's_producto.id as idproducto',
                        's_producto.codigo as codigo',
                        's_producto.nombre as nombre',
                        'unidadmedida.codigo as unidadmedidacodigo'
                      )
                    ->get();

              foreach($ventadetalle as $value){

                      $subtotal = number_format($value->total/$igv, 2, '.', '');
                      $impuesto = number_format($value->total-$subtotal, 2, '.', '');
                      $montovalorunitario = number_format($value->preciounitario/$igv, 2, '.', '');

                      DB::table('s_facturacionboletafacturadetalle')->insert([
                            'codigoproducto'             => str_pad($value->codigo, 6, "0", STR_PAD_LEFT),
                            'unidad'                     => $value->unidadmedidacodigo,
                            'cantidad'                   => $value->cantidad,
                            'descripcion'                => $value->nombre,
                            'montobaseigv'               => $subtotal,
                            'porcentajeigv'              => $prestamocobranza->facturacionigv,
                            'igv'                        => $impuesto,
                            'tipoafectacionigv'          => '10',
                            'totalimpuestos'             => $impuesto,
                            'montovalorventa'            => $subtotal,
                            'montovalorunitario'         => $montovalorunitario,
                            'montopreciounitario'        => $value->preciounitario,
                            'idproducto'                 => $value->idproducto,
                            'idfacturacionboletafactura' => $idfacturacionboletafactura,
                            'idtienda'                   => $idtienda,
                       ]);
              }
            
              $result = facturador_facturaboleta($idfacturacionboletafactura);
            
              return [
                  'resultado' => $result['resultado'],
                  'mensaje'   => $result['mensaje']
              ];
          }  
    }      
}*/
function facturador_guia($data) {
 $see = new \Greenter\See();
  
 if($data->facturador_idestado == 2){
      $see->setService(SunatEndpoints::GUIA_PRODUCCION);
      $sunat_usuario = $data->sunat_usuario;
      $sunat_clave = $data->sunat_clave;
      $raiz = 'public/sunat/produccion/';
      $sunat_certificado = url($raiz.'certificado/'.$data->sunat_certificado);
  }else{
      $see->setService(SunatEndpoints::GUIA_BETA);
      $sunat_usuario = 'MODDATOS';
      $sunat_clave = 'moddatos';
      $raiz = 'public/sunat/beta/';
      $sunat_certificado = url($raiz.'certificado/certificate_demo.pem');
  }
  
  $see->setCertificate(file_get_contents($sunat_certificado));
  $see->setCredentials($data->emisor_ruc.$sunat_usuario, $sunat_clave);

  // Emisor
  $address = new Address();
  $address->setUbigueo($data->emisor_ubigeo)
      ->setDepartamento($data->emisor_departamento)
      ->setProvincia($data->emisor_provincia)
      ->setDistrito($data->emisor_distrito)
      ->setUrbanizacion($data->emisor_urbanizacion)
      ->setDireccion($data->emisor_direccion);

  $company = new Company();
  $company->setRuc($data->emisor_ruc)
      ->setRazonSocial($data->emisor_razonsocial)
      ->setNombreComercial($data->emisor_nombrecomercial)
      ->setAddress($address);

  return [
      'see' => $see,
      'company' => $company,
      'raiz' => $raiz
  ];
}
function facturador_consulta($cdr){

    $code = (int)$cdr->getCode();
    $notas = '';
    if($code === 0) {
        $estado = 'ACEPTADA';
        //$get_notes = isset($cdr->getNotes())?$cdr->getNotes():$cdr->getMessage();

        if($cdr->getCode()=='HTTP'){
            $estado = 'NO ENVIADO';
        }
        /*if(count($cdr->getNotes()) > 0) {
            $estado = 'OBSERVACIONES';
            // Corregir estas observaciones en siguientes emisiones.
            //$notas = json_encode($cdr->getNotes());
            foreach($cdr->getNotes() as $value){
                $notas = $notas.'/,/'.$value;
            }
        }*/
    }elseif($code >= 2000 && $code <= 3999) {
        $estado = 'RECHAZADA';
    }else{
        /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
        /*code: 0100 a 1999 */
        if ($code == 1033) {
          $estado = 'ACEPTADA/EXCEPCION';
        }else {
          $estado = 'EXCEPCION';  
        }
    }
    return [
        'estado' => $estado,
        'notas' => $notas,
        'codigo' => $code,
    ];
}

function consultaCdr($idfacturaboleta) {
    $data = DB::table('s_facturacionboletafactura')
      ->join('s_agencia','s_agencia.id','s_facturacionboletafactura.idagencia')
      ->where('s_facturacionboletafactura.id',$idfacturaboleta)
      ->select(
          's_facturacionboletafactura.*',
          's_agencia.idestadofacturacion as idestadofacturacion',
          's_agencia.facturacion_usuario as sunat_usuario',
          's_agencia.facturacion_clave as sunat_clave',
          's_agencia.facturacion_certificado as sunat_certificado'
      )
      ->first();
 
    $ws = new SoapClient(SunatEndpoints::FE_CONSULTA_CDR.'?wsdl');
    $ws->setCredentials($data->emisor_ruc.$data->sunat_usuario, $data->sunat_clave);
  
    $service = new ConsultCdrService();
    $service->setClient($ws);
    
    $arguments = [
        $data->emisor_ruc,
        $data->venta_tipodocumento,
        $data->venta_serie,
        $data->venta_correlativo
    ];
      
    $result = $service->getStatusCdr(...$arguments);
    
    if (!$result->isSuccess()) {
        $mensaje = $result->getMessage() ?? $result->getError()->getMessage();
       //$codigo = $result->getCode() ?? $result->getError()->getCode();
        $cdr = $result->getError();
        $result_cdr = facturador_consulta($cdr);
        return [
            'resultado' => 'ERROR',
            'mensaje'   => $result_cdr['codigo'].' - '.$mensaje,
            'codigo'    => $result_cdr['codigo'],
            'estado'    => $result_cdr['estado'],
        ];
    }  
 
    if ($result->getCdrZip()) {
        //$cdr_note = implode('/%/', $result->getCdrResponse()->getNotes());
      
        $cdr = $result->getCdrResponse();
        $result_cdr = facturador_consulta($cdr);
      
        return [
            'resultado' => 'CORRECTO',
            'mensaje'   => $result->getCdrResponse()->getDescription(),
            'codigo'    => $result_cdr['codigo'],
            'estado'    => $result_cdr['estado'],
        ];
    }
    
    
}
/*function facturador_respuesta($tipo,$id){
    $resultado = '';
    $mensaje = '';
    $facturacionrespuesta = '';
    if($tipo=='BOLETAFACTURA'){
        $facturacionrespuesta = DB::table('s_facturacionrespuesta')
            ->leftJoin('s_facturacionboletafactura','s_facturacionboletafactura.id','s_facturacionrespuesta.s_idfacturacionboletafactura')
            ->where('s_facturacionrespuesta.s_idfacturacionboletafactura',$id)
            ->orderBy('s_facturacionrespuesta.id','desc')
            ->select(
                's_facturacionrespuesta.*',
                's_facturacionboletafactura.venta_serie as serie',
                's_facturacionboletafactura.venta_correlativo as correlativo'
            )
            ->limit(1)
            ->first();
        if(isset($facturacionrespuesta)){
            if($facturacionrespuesta->estado=='ACEPTADA'){
                $resultado = 'ACEPTADA';
                $mensaje = $facturacionrespuesta->mensaje.'<br>';
            }elseif($facturacionrespuesta->estado=='OBSERVACIONES'){
                $resultado = 'OBSERVACIONES';
                $mensaje = 'El Comprobante tiene Observaciones:<br>'.$facturacionrespuesta->mensaje.'<br><b>¿Deseas reenviar el comprobante?</b><br>';
            }elseif($facturacionrespuesta->estado=='RECHAZADA'){
                $resultado = 'RECHAZADA';
                $mensaje = 'El Comprobante fue rechazado:<br>'.$facturacionrespuesta->mensaje.'<br><b>¿Deseas reenviar el comprobante?</b><br>';
            }elseif($facturacionrespuesta->estado=='EXCEPCION'){
                $resultado = 'EXCEPCION';
                $mensaje = 'El CDR es inválido debe tratarse de un error-excepción:<br>'.$facturacionrespuesta->mensaje.'<br><b>¿Deseas reenviar el comprobante?</b><br>';
            }else{
                // 1033 = El comprobante fue registrado previamente con otros datos.
                if($facturacionrespuesta->codigo==1033){
                    $resultado = 'ACEPTADA';
                    $mensaje = 'El comprobante '.$facturacionrespuesta->serie.'-'.$facturacionrespuesta->correlativo.' fue informado correctamente.<br>';
                }else{
                    $resultado = 'ERROR';
                    $mensaje = 'El envio del comprobante tiene un error!!<br><b>¿Deseas reenviar el comprobante?</b><br>';
                }  
            }  
        }else{
            $resultado = 'NOENVIADO';
            $mensaje = 'El Comprobante no fue enviado correctamente.<br><b>¿Deseas reenviar el comprobante?</b><br>';
        }
    }
    return [
        'resultado' => $resultado,
        'mensaje' => $mensaje,
        'facturacionrespuesta' => $facturacionrespuesta,
    ];
}*/
/*function facturador_impuesto($total,$igv){
    $igv = ($igv/100)+1;
    $total = number_format($total, 2, '.', '');
    $subtotal = number_format($total/$igv, 2, '.', '');
    $impuesto = number_format($total-$subtotal, 2, '.', '');
  
    return [
        'subtotal' => $subtotal,
        'impuesto' => $impuesto,
        'total' => $total,
    ];
}*/
function facturador_guia_api( $data ){
  $tipo_conexion_facturador = 'PRODUCCION';
  if( $data->facturacion_usuario == 'MODDATOS' || $data->facturacion_clave == 'MODDATOS' ){
    $tipo_conexion_facturador = 'DEMO';
  }
//   dump($tipo_conexion_facturador);

  $idtoken            = $tipo_conexion_facturador == "PRODUCCION" ? $data->id_token : 'test-85e5b0ae-255c-4891-a595-0b98c65c9854';
  $clavetoken         = $tipo_conexion_facturador == "PRODUCCION" ? $data->clave_token : 'test-Hty/M6QshYvPgItX2P0+Kw==';
  
  $sunat_usuario      = $tipo_conexion_facturador == "PRODUCCION" ? $data->facturacion_usuario : 'MODDATOS';
  $sunat_clave        = $tipo_conexion_facturador == "PRODUCCION" ? $data->facturacion_clave : 'MODDATOS';
  $raiz               = $tipo_conexion_facturador == "PRODUCCION" ? 'public/backoffice/tienda/'.$data->idtienda.'/sunat/produccion/' : 'public/backoffice/sistema/sunat/beta/';
  
  $sunat_certificado  = url($raiz.'certificado/'.($tipo_conexion_facturador == "PRODUCCION" ? $data->facturacion_certificado : 'certificate.pem'));
  
  $nube_fact_test = [
                      'auth' => 'https://gre-test.nubefact.com/v1',
                      'cpe' => 'https://gre-test.nubefact.com/v1'
                    ];
  $api = $tipo_conexion_facturador == "PRODUCCION" ? new \Greenter\Api() : new \Greenter\Api($nube_fact_test) ;
  
  
  $certificate = file_get_contents($sunat_certificado);
  if ($certificate === false) {
      throw new Exception('No se pudo cargar el certificado');
  }
  
  // Emisor
  $address = new Address();
  $address->setUbigueo($data->emisor_ubigeo)
      ->setDepartamento($data->emisor_departamento)
      ->setProvincia($data->emisor_provincia)
      ->setDistrito($data->emisor_distrito)
      ->setUrbanizacion($data->emisor_urbanizacion)
      ->setDireccion($data->emisor_direccion);
  $company = new Company();
  $company->setRuc($data->emisor_ruc)
      ->setRazonSocial($data->emisor_razonsocial)
      ->setNombreComercial($data->emisor_nombrecomercial)
      ->setAddress($address);
  
  $api_see = $api->setBuilderOptions([
          'strict_variables' => true,
          'optimizations' => 0,
          'debug' => true,
          'cache' => false,
      ])
      ->setApiCredentials($idtoken, $clavetoken)
      ->setClaveSOL($data->emisor_ruc, $sunat_usuario, $sunat_clave)
      ->setCertificate($certificate);
  
  return [
      'api' => $api_see,
      'company' => $company,
      'raiz' => $raiz
  ];
  
}

function facturador_guiaremision_api($idfacturacionguiaremision) {
  
  
  $data = DB::table('s_facturacionguiaremision')
    ->join('s_agencia','s_agencia.id','s_facturacionguiaremision.idagencia') 
    
    //->leftJoin('facturacionboletafactura','facturacionboletafactura.id','facturacionguiaremision.idfacturacionboletafactura')
    ->where('s_facturacionguiaremision.id', $idfacturacionguiaremision)
    ->select(
      's_facturacionguiaremision.*',
      's_agencia.idestadofacturacion as estadoagenciafacturacion',
      's_agencia.id_token as id_token',
      's_agencia.clave_token as clave_token',
      's_agencia.facturacion_usuario as facturacion_usuario',
      's_agencia.facturacion_clave as facturacion_clave',
      's_agencia.facturacion_certificado as facturacion_certificado'
      /*'facturacionboletafactura.venta_tipodocumento as venta_tipodocumento',
      'facturacionboletafactura.venta_serie as venta_serie',
      'facturacionboletafactura.venta_correlativo as venta_correlativo',*/
      //'facturacionboletafactura.emisor_ruc as emisor_ruc'
    )
    ->first();
//   dd($data);
 
  if($data->ticket != ''){
    
    $res_ticket = validaTicketGuia( $data->ticket, $idfacturacionguiaremision, $data);
    return $res_ticket;
  }
  $facturador_guia = facturador_guia_api( $data );
  

  
  $envio = new Shipment();
  $envio->setCodTraslado($data->envio_codigotraslado) // Cat.20
      ->setDesTraslado($data->envio_descripciontraslado)
      ->setModTraslado($data->envio_modtraslado) // Cat.18
      ->setFecTraslado(date_create($data->envio_fechatraslado))
      ->setPesoTotal(10.0)
      ->setUndPesoTotal($data->envio_unidadpesototal)
    
      ->setLlegada(new Direction( $data->envio_direccionllegadacodigoubigeo, $data->envio_direccionllegada ))
      ->setPartida(new Direction( $data->envio_direccionpartidacodigoubigeo, $data->envio_direccionpartida ));
  
  if($data->envio_modtraslado=="02"){
    // TRANSPORTE PRIVADO
    $vehiculoPrincipal = new Vehicle();
    $vehiculoPrincipal->setPlaca($data->transporte_placa); // VALIDAR A 6 DIGITOS

    $chofer = (new Driver())
        ->setTipo('Principal')
        ->setTipoDoc($data->transporte_chofertipodocumento)
        ->setNroDoc($data->transporte_choferdocumento)
        ->setLicencia($data->transporte_choferlicencia)  // VALIDAR A 10 DIGITOS
        ->setNombres($data->transporte_chofernombres)
        ->setApellidos($data->transporte_choferapellidos);

    $envio->setVehiculo($vehiculoPrincipal);
    $envio->setChoferes([$chofer]);

  }
  else if($data->envio_modtraslado=="01"){
    
    $transp = new Transportist();
    $transp->setTipoDoc($data->transporte_tipodocumento)
      ->setNumDoc($data->transporte_numerodocumento)
      ->setRznSocial($data->transporte_razonsocial);

      $envio->setTransportista($transp);
  }
    
  $despatch = new Despatch();
  $despatch->setVersion('2022')
      ->setTipoDoc($data->despacho_tipodocumento)
      ->setSerie($data->despacho_serie)
      ->setCorrelativo($data->despacho_correlativo)
      ->setFechaEmision(date_create($data->despacho_fechaemision))
      ->setCompany($facturador_guia['company'])
      ->setDestinatario((new Client())
          ->setTipoDoc($data->despacho_destinatario_tipodocumento)
          ->setNumDoc($data->despacho_destinatario_numerodocumento)
          ->setRznSocial($data->despacho_destinatario_razonsocial)) // misma empresa
      ->setObservacion($data->despacho_observacion)
      ->setEnvio($envio);

  
  $data_detalle = DB::table('s_facturacionguiaremisiondetalle')->where('idfacturacionguiaremision', $idfacturacionguiaremision)->get();
  
  $item = [];
  foreach($data_detalle as $value){
      $item[] = (new DespatchDetail())
          ->setCantidad($value->cantidad)
          ->setUnidad($value->unidad)
          ->setDescripcion($value->descripcion)
          ->setCodigo($value->codigo)
          ->setCodProdSunat($value->codprodsunat);
  }
  // Envio a SUNAT
  $despatch->setDetails($item);
  
  $envio_gre = $facturador_guia['api']->send($despatch);
  
  
  // verificar si existe directorio
  $raiz = $facturador_guia['raiz'].'guiaremision/';
  if (!file_exists($raiz)) {
      mkdir($raiz, 0777, true);
  }

  // Guardar XML firmado digitalmente.
  file_put_contents($raiz . $despatch->getName().'.xml', $facturador_guia['api']->getLastXml());
  
  $ticket = $envio_gre->getTicket();
 
  
  if (!$envio_gre->isSuccess()) {
      $error_consulta_ticket = $envio_gre->getError();
         
      $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
        'fecharegistro'               => Carbon::now(),
        'estado'                      => 'RECHAZADA',
        'codigo'                      => $error_consulta_ticket->getCode(),
        'mensaje'                     => $error_consulta_ticket->getMessage(),
        'notas'                       => '',
        'qr'                          => '',
        'nombre'                      => $despatch->getName(),
        's_idfacturacionguiaremision' => $idfacturacionguiaremision,
        'idestadofacturacion'         => $data->estadoagenciafacturacion, // 1=produccion, 2=beta
        'idtienda'                    => $data->idtienda,
      ]);

      DB::table('s_facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
          'idfacturacionrespuesta'  => $idfacturacionrespuesta,
          'ticket'                  => $ticket
      ]);
    
      return array(
        'tipo' => 'ERROR',
        'mensaje' => $error_consulta_ticket->getCode() . ' - ' . $error_consulta_ticket->getMessage() . ' | ERROR AL GENERAR GUIA'
      );
  }else{
    

    DB::table('s_facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
//         'idfacturacionrespuesta'  => $idfacturacionrespuesta,
        'ticket'                  => $ticket
    ]);
    
  }
  
  //   $ticket = $envio_gre->getTicket();
  $res_ticket = validaTicketGuia( $ticket, $idfacturacionguiaremision, $data);
   
  return $res_ticket;
} 
function validaTicketGuia( $ticket, $idfacturacionguiaremision, $data ){
    $facturador_guia = facturador_guia_api( $data );
    
  
    $api = $facturador_guia['api'];
    // CONSULTA DE ESTADO DE GUIA - MUCHAS VECES ESTE PROCESO PUEDE TOMAR ENTRE 1 A 2 MIN
    $consulta_ticket = $api->getStatus($ticket);
    $name_document = $data->emisor_ruc.'-09-'.$data->despacho_serie.'-'.$data->despacho_correlativo;
    if (!$consulta_ticket->isSuccess()) {
      $error_consulta_ticket = $consulta_ticket->getError();
      if( $error_consulta_ticket->getCode() == 1033 || $error_consulta_ticket->getCode() == 98 ){
        
        $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
          'fecharegistro'               => Carbon::now(),
          'estado'                      => 'ACEPTADA',
          'codigo'                      => '',
          'mensaje'                     => '',
          'notas'                       => '',
          'qr'                          => '',
          'nombre'                      => $name_document,
          's_idfacturacionguiaremision' => $idfacturacionguiaremision,
          'idestadofacturacion'         => $data->estadoagenciafacturacion, // 1=produccion, 2=beta
          'idtienda'                    => $data->idtienda,
        ]);

        DB::table('s_facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
            'idfacturacionrespuesta'  => $idfacturacionrespuesta
        ]);
        
        $res = array(
            'tipo'          => 'CORRECTO',
            'mensaje'       => 'GUIA ACEPTADA'
        );

        return $res;
      }
      else{
        $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
          'fecharegistro'               => Carbon::now(),
          'estado'                      => 'RECHAZADA',
          'codigo'                      => $error_consulta_ticket->getCode(),
          'mensaje'                     => $error_consulta_ticket->getMessage(),
          'notas'                       => '',
          'qr'                          => '',
          'nombre'                      => $name_document,
          's_idfacturacionguiaremision' => $idfacturacionguiaremision,
          'idestadofacturacion'         => $data->estadoagenciafacturacion, // 1=produccion, 2=beta
          'idtienda'                    => $data->idtienda,
        ]);

        DB::table('s_facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
            'idfacturacionrespuesta'  => $idfacturacionrespuesta
        ]);
//         DB::table('facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
//             'estadofacturacion'   => $error_consulta_ticket->getCode() . ' - ' . $error_consulta_ticket->getMessage(),
//             'idestadofacturacion' => 2
//         ]);
        
        return array(
          'tipo' => 'ERROR',
          'mensaje' => $error_consulta_ticket->getCode() . ' - ' . $error_consulta_ticket->getMessage() . ' | LA GUIA NO FUE ACEPTADA POR SUNAT, VUELVA A GENERAR'
        );
      }
      
    }
  

    $cdr = $consulta_ticket->getCdrResponse();
    ############## INICIO - ALMACENAMOS EL CDR DE LA GRE-REMITENTE ######################
    $raiz = $facturador_guia['raiz'].'guiaremision/';
    if (!file_exists($raiz)) {
        mkdir($raiz, 0777, true);
    }
    
    file_put_contents($raiz.'R' . $name_document .'.zip', $consulta_ticket->getCdrZip());
    ################# FIN - ALMACENAMOS EL CDR DE LA GRE-REMITENTE #######################
  
    $idfacturacionrespuesta = DB::table('s_facturacionrespuesta')->insertGetId([
      'fecharegistro'               => Carbon::now(),
      'estado'                      => 'ACEPTADA',
      'codigo'                      => '',
      'mensaje'                     => '',
      'notas'                       => '',
      'qr'                          => $cdr->getReference(),
      'nombre'                      => $name_document,
      's_idfacturacionguiaremision' => $idfacturacionguiaremision,
      'idestadofacturacion'         => $data->estadoagenciafacturacion, // 1=produccion, 2=beta
      'idtienda'                    => $data->idtienda,
    ]);

    DB::table('s_facturacionguiaremision')->whereId($idfacturacionguiaremision)->update([
      'idfacturacionrespuesta'  => $idfacturacionrespuesta,
      'ticket'                  => $ticket,
    ]);

    $res = array(
        'tipo'          => 'CORRECTO',
        'mensaje'       => $cdr->getDescription(),
        'ticket'        => $ticket,
        'comprobante'   => $cdr->getId(),
        'codretorno'    => $cdr->getCode(),
        'descripcion'   => $cdr->getDescription(),
        'qr'            => $cdr->getReference()
    );
    
    return $res;
    
}

