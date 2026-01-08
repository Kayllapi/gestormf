@extends('layouts.master')
@section('cuerpo')
<div class="content">
                    <!--  section  --> 
                    <section class="parallax-section" data-scrollax-parent="true" id="sec1">
                        <div class="bg par-elem " data-bg="{{ url('public/backoffice/sistema/errordominio.png') }}" data-scrollax="properties: { translateY: '30%' }" style="background-image: url({{ url('public/backoffice/sistema/errordominio.png') }}); transform: translateZ(0px) translateY(-1.9234%);"></div>
                        <div class="overlay"></div>
                        <div class="bubble-bg"><div class="bubbles"><div class="individual-bubble" style="left: 220px; width: 10px; height: 10px; opacity: 0.00049928;"></div><div class="individual-bubble" style="left: 2072px; width: 5px; height: 5px; opacity: 0.00512072;"></div><div class="individual-bubble" style="left: 791px; width: 15px; height: 15px; opacity: 0.0141793;"></div><div class="individual-bubble" style="left: 1604px; width: 5px; height: 5px; opacity: 0.027848;"></div><div class="individual-bubble" style="left: 82px; width: 5px; height: 5px; opacity: 0.0473089;"></div><div class="individual-bubble" style="left: 1446px; width: 20px; height: 20px; opacity: 0.0709891;"></div><div class="individual-bubble" style="left: 201px; width: 5px; height: 5px; opacity: 0.0996365;"></div><div class="individual-bubble" style="left: 844px; width: 15px; height: 15px; opacity: 0.133231;"></div><div class="individual-bubble" style="left: 1539px; width: 20px; height: 20px; opacity: 0.171347;"></div><div class="individual-bubble" style="left: 69px; width: 5px; height: 5px; opacity: 0.217668;"></div><div class="individual-bubble" style="left: 422px; width: 20px; height: 20px; opacity: 0.265283;"></div><div class="individual-bubble" style="left: 391px; width: 20px; height: 20px; opacity: 0.318402;"></div><div class="individual-bubble" style="left: 643px; width: 20px; height: 20px; opacity: 0.377233;"></div><div class="individual-bubble" style="left: 800px; width: 20px; height: 20px; opacity: 0.44086;"></div></div></div>
                        <div class="container">
                            <div class="error-wrap">
                                <h2>404</h2>
                                <p>Lo sentimos, pero no se pudo encontrar la pagina con el dominio personalizado:</p>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </section>
                    <!--  section  end--> 
                    <!--section -->
                    <section class="gradient-bg">
                        <div class="cirle-bg">
                            <div class="bg" data-bg="{{ url('public/backoffice/sistema/circle.png') }}"></div>
                        </div>
                        <div class="container">
                            <div class="join-wrap fl-wrap">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h3>¿Deseas Contactarnos?</h3>
                                        <p>No permita que su presencia en línea le cueste dinero y tiempo. Si no pueden encontrarte, no pueden elegirte.</p>
                                    </div>
                                    <div class="col-md-4"><a href="{{ url('/pagina/contacto') }}" class="join-wrap-btn">Ponerse en contacto <i class="fa fa-envelope-o"></i></a></div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- section end -->
                    
                    
                    <div class="limit-box"></div>
                </div>
                    <!--section -->
                    <section id="sec1">
                        <div class="container">
                          <div class="section-title">
                            <h2>¿Cómo configurar tu Dominio Personalizado?</h2>
                            <div class="section-subtitle">¿Cómo configurar tu Dominio Personalizado?</div>
                            <p>Configura tu propio dominio.</p>
                            <span class="section-separator"></span>
                        </div>
                            <!--process-wrap  -->
                            <div class="process-wrap fl-wrap">
                                <ul>
                                    <li>
                                        <div class="process-item">
                                            <span class="process-count">01 . </span>
                                            <div class="time-line-icon"><i class="fa fa-map-o"></i></div>
                                            <h4>Registrate</h4>
                                            <p>Si eres usuario nuevo registrate, luego confirme su registro medianto su correo proporcionado, por ultimo ingrese a su Backoffice.</p>
                                        </div>
                                        <span class="pr-dec"></span>
                                    </li>
                                    <li>
                                        <div class="process-item">
                                            <span class="process-count">02 .</span>
                                            <div class="time-line-icon"><i class="fa fa-envelope-open-o"></i></div>
                                            <h4>Registra su Negocio</h4>
                                            <p>En su Backoffice registre su Negocio con la información necesaria, luego suba imagenes de portada, galeria de fotos y sus Productos/Servicios.</p>
                                        </div>
                                        <span class="pr-dec"></span>
                                    </li>
                                    <li>
                                        <div class="process-item">
                                            <span class="process-count">03 .</span>
                                            <div class="time-line-icon"><i class="fa fa-hand-peace-o"></i></div>
                                            <h4>Configura tu Dominio</h4>
                                            <p>En tu Negocio registrado ve a la opción "Configuración", luego configure su DNS de su proveedor, asi mismo registre su dominio configurado de su proveedor en nuestra plataforma.</p>
                                        </div>
                                        <div class="process-end"><i class="fa fa-check"></i></div>
                                    </li>
                                </ul>
                            </div>
                            <!--process-wrap   end-->
                        </div>
                    </section>
@endsection