<div class="modal-header">
    <h5 class="modal-title">ACTA DE APROBACIÓN</h5>
    <button type="button" class="btn-close" id="modal-close-usuario-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
</div>


<div class="modal-body">
    @if($credito->estado=='PROCESO')
          <div class="col-sm-12">
            <div class="btn-group mb-1" id="formato_evaluacion">
               <button type="button" class="btn btn-warning evaluacion" style="background-color: #f297ec;border-color: #212529;"
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/propuestacredito/'.$credito->id.'/edit?view=excepcion_autorizacion&detalle=false')}}', size: 'modal-fullscreen' })">
                  1.- EXCEPCIONES Y AUTORIZACIONES</button>
            </div>
              <div class="btn-group mb-1 evaluacion-resumida">
                 <button type="button" class="btn btn-warning evaluacion" style="background-color: #f297ec;border-color: #212529;"
                        onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/propuestacredito/'.$credito->id.'/edit?view=area_riesgos&detalle=false')}}', size: 'modal-fullscreen' })">
                  2.- OPINIÓN DE ÁREA DE RIESGOS</button>
              </div>
              <div class="btn-group mb-1 evaluacion-resumida">
                 <button type="button" class="btn btn-warning evaluacion" style="background-color: #f297ec;border-color: #212529;"
                        onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/propuestacredito/'.$credito->id.'/edit?view=comentario_visitas&detalle=false')}}', size: 'modal-fullscreen' })">
                  3.- COMENTARIO DE VISITAS Y/O VERIFICACIÓN</button>
              </div>
          </div>
    @endif
    <iframe id="iframe_acta_aprobacion" src="{{ url('/backoffice/'.$tienda->id.'/propuestacredito/'.$credito->id.'/edit?view=acta_aprobacionpdf') }}#zoom=100" frameborder="0" width="100%"
        style="height: calc(100vh - 68px);"></iframe>
</div>