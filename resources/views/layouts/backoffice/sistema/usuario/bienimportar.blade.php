@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Garantias</span>
      <a class="btn btn-success" href="{{ redirect()->getUrlGenerator()->previous() }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Producto</th>
        <th>Descripción</th>
        <th>Valor Estimado</th>
        <th>Documento</th>
        <th>Imagenes</th>
      </tr>
    </thead>
    <tbody>
      @foreach($bienes as $value)
        <tr>
          <td>{{$value->producto}}</td>
          <td>{{$value->descripcion}}</td>
          <td>{{$value->valorestimado}}</td>
          <td>
            @if($value->idprestamo_documento==1)
                SIN DOCUMENTOS
            @elseif($value->idprestamo_documento==2)
                COPIA/LEGALIZADO
            @elseif($value->idprestamo_documento==3)
                ORIGINAL
            @endif
          </td>
          <td>
            <?php $prestamobienimagen = DB::table('s_prestamo_creditobienimagen')->where('idprestamo_creditobien', $value->id)->get(); ?>
            @foreach($prestamobienimagen as $valueimagen)
                <div style="background-image: url({{url('public/backoffice/tienda/'.$tienda->id.'/creditobien/'.$valueimagen->imagen)}});
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;
                              height: 42px;
                              width: 50px;
                              background-color: #31353c;
                              float: left;
                              margin-right: 1px;">
                </div>
            @endforeach 
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>                   
@endsection