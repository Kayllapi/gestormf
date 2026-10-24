<?php
    $grupoActual = null;
?>
@foreach($items as $subvalue)
    @php
        $grupo = floor($subvalue->orden / 100);
    @endphp
    @if($grupoActual !== null && $grupo != $grupoActual)
        <li><hr class="dropdown-divider my-0" style="opacity:1;"></li>
    @endif
    @php
        $grupoActual = $grupo;
        $href = str_replace('{idtienda}', $tienda->id, $subvalue->vista);
        $hijos = DB::table('modulo')
            ->where('modulo.idestado',1)
            ->where('modulo.vista','<>','SOLO-ACCESO')
            ->where('modulo.idmodulo',$subvalue->id)
            ->whereIn('modulo.id',$modulos_acceso)
            ->select('modulo.*')
            ->orderBy('modulo.orden','asc')
            ->get();
    @endphp
    @if($hijos->isNotEmpty())
        <li class="dropdown-submenu">
            <a href="javascript:;" class="dropdown-item dropdown-toggle">
                <i class="{{ $subvalue->icono }}"></i> {{ $subvalue->nombre }}
            </a>
            <ul class="dropdown-menu">
                @include('layouts.backoffice.sistema.partials.menu_items', [
                    'items' => $hijos,
                    'tienda' => $tienda,
                    'modulos_acceso' => $modulos_acceso,
                    'idmenuraiz' => $idmenuraiz,
                ])
            </ul>
        </li>
    @else
        <li>
            <a href="javascript:;"
                class="dropdown-item"
                style="font-size: 13px;"
                onclick="pagina({route:'{{ url($href) }}?view=tabla',result:'#cuerposistema'}),
                menu_click({{ $idmenuraiz }})">
                <i class="{{ $subvalue->icono }}"></i> {{ $subvalue->nombre }}
            </a>
        </li>
    @endif
@endforeach
