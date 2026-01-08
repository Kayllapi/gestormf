@extends('layouts.backoffice.cine.master')
@section('cuerpo')
	<section class="section section--first section--bg" data-bg="https://kayllapi.com/public/layouts/cinema/img/section/section.jpg">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="section__wrap">
						<h2 class="section__title">Plan de Precios</h2>
						<ul class="breadcrumb">
							<li class="breadcrumb__item"><a href="{{url('backoffice/cineplus')}}">Inicio</a></li>
							<li class="breadcrumb__item breadcrumb__item--active">Plan de Precios</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>
	<div class="section section--top">
		<div class="container">
			<div class="row row--grid">
				<div class="col-12 col-md-6 col-lg-4 order-md-2 order-lg-1">
				</div>
				<div class="col-12 col-md-12 col-lg-4 order-md-1 order-lg-2">
					<div class="price price--premium">
						<div class="price__item price__item--first"><span>Creditos</span> <span>S/. 5.00 <sub>/ mes</sub></span></div>
						<div class="price__item"><span><i class="icon ion-ios-checkmark"></i> Días Ilimitados</span></div>
						<div class="price__item"><span><i class="icon ion-ios-checkmark"></i> Resolución en Full HD y Ultra HD</span></div>
						<div class="price__item"><span><i class="icon ion-ios-checkmark"></i> Disponibilidad de por vida</span></div>
						<div class="price__item"><span><i class="icon ion-ios-checkmark"></i> Cualquier Dispositivo</span></div>
						<div class="price__item"><span><i class="icon ion-ios-checkmark"></i> Soporte 24/7</span></div>
						<a href="#" class="price__btn">Elija Plan</a>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 order-md-3">
				</div>
			</div>
		</div>
	</div>
	<!-- features 
	 <section class="section section--border">
		<div class="container">
			<div class="row">
				<!-- section title 
				<div class="col-12">
					<h2 class="section__title section__title--mb0">Nuestras caracteristicas</h2>
				</div>-->
				<!-- end section title -->

				<!-- feature 
				<div class="col-12 col-md-6 col-lg-4">
					<div class="feature">
						<i class="icon ion-ios-tv feature__icon"></i>
						<h3 class="feature__title">Ultra HD</h3>
						<p class="feature__text">Si va a utilizar un pasaje de Lorem Ipsum, debe asegurarse de que no haya nada vergonzoso escondido en medio del texto.</p>
					</div>
				</div>-->
				<!-- end feature -->

				<!-- feature
				<div class="col-12 col-md-6 col-lg-4">
					<div class="feature">
						<i class="icon ion-ios-film feature__icon"></i>
						<h3 class="feature__title">Película</h3>
						<p class="feature__text">Todos los generadores Lorem Ipsum en Internet tienden a repetir fragmentos predefinidos según sea necesario, por lo que este es el primero.</p>
					</div>
				</div>
				<!-- end feature -->

				<!-- feature 
				<div class="col-12 col-md-6 col-lg-4">
					<div class="feature">
						<i class="icon ion-ios-trophy feature__icon"></i>
						<h3 class="feature__title">Premios</h3>
						<p class="feature__text">Es para hacer un libro de muestras tipo. Ha sobrevivido no solo a cinco siglos, sino también al salto a la composición tipográfica electrónica, permaneciendo.</p>
					</div>
				</div>
				<!-- end feature -->

				<!-- feature 
				<div class="col-12 col-md-6 col-lg-4">
					<div class="feature">
						<i class="icon ion-ios-notifications feature__icon"></i>
						<h3 class="feature__title">Notificaciones</h3>
						<p class="feature__text">Varias versiones han evolucionado a lo largo de los años, a veces por accidente, a veces a propósito.</p>
					</div>
				</div>
				<!-- end feature -

				<!-- feature 
				<div class="col-12 col-md-6 col-lg-4">
					<div class="feature">
						<i class="icon ion-ios-rocket feature__icon"></i>
						<h3 class="feature__title">Cohete</h3>
						<p class="feature__text">Es para hacer un libro de muestras tipo. Ha sobrevivido no solo a cinco siglos, sino también al salto a la composición tipográfica electrónica.</p>
					</div>
				</div>
				<!-- end feature -->

				<!-- feature 
				<div class="col-12 col-md-6 col-lg-4">
					<div class="feature">
						<i class="icon ion-ios-globe feature__icon"></i>
						<h3 class="feature__title">Subtítulos en varios idiomas</h3>
						<p class="feature__text">Varias versiones han evolucionado a lo largo de los años, a veces por accidente, a veces a propósito.</p>
					</div>
				</div>
				<!-- end feature 
			</div>
		</div> 
</sectio> -->
@endsection
@section('scriptsbackoffice')
@endsection



