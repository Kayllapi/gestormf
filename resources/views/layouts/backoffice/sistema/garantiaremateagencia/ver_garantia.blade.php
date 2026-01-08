    <div class="modal-header">
        <h5 class="modal-title">Garantias</h5>
        <button type="button" class="btn-close" id="modal-close-garantias-modificar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">

            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark">
                <tr>
                  <td>CLIENTE</td>
                  <td>DNI</td>
                  <td>TIPO DE GARANTIA</td>
                  <td>DESCRIPCIÓN</td>
                  <td>MODELO</td>
                  <td>VALOR COMERCIAL</td>
                  <td>ACCESORIOS</td>
                  <td>COBERTURA</td>
                  <td>COLOR</td>
                  <td>CODIGO DE GARANTIA</td>
                </tr>
              </thead>
              <tbody>
                @foreach($credito_garantias as $value)
                <tr>
                  <td>{{$value->clientenombrecompleto}}</td>
                  <td>{{$value->dni}}</td>
                  <td>{{$value->garantias_tipogarantia}}</td>
                  <td>{{$value->descripcion}}</td>
                  <td>{{$value->garantias_modelo_tipo}}</td>
                  <td>{{$value->valor_comercial}}</td>
                  <td>{{$value->garantias_accesorio_doc}}</td>
                  <td>{{$value->valor_realizacion}}</td>
                  <td>{{$value->garantias_color}}</td>
                  <td>{{$value->garantias_codigo}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>