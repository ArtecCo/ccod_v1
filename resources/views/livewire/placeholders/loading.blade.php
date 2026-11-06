<div class="ccod-skeleton ccod-skeleton--{{ $variant ?? 'cards' }}" role="status" aria-label="Loading content">
    @if (($variant ?? 'cards') === 'stats')
        <div class="ccod-skeleton__stats">
            @for ($i = 0; $i < 4; $i++)
                <div class="ccod-skeleton__card"><div class="ccod-skeleton__block ccod-skeleton__label"></div><div class="ccod-skeleton__block ccod-skeleton__value"></div><div class="ccod-skeleton__block ccod-skeleton__line"></div></div>
            @endfor
        </div>
    @elseif (($variant ?? 'cards') === 'chart')
        <div class="ccod-skeleton__card ccod-skeleton__chart-card"><div class="ccod-skeleton__block ccod-skeleton__label"></div><div class="ccod-skeleton__chart">
            @for ($i = 0; $i < 12; $i++)<div class="ccod-skeleton__bar" style="height: {{ [28,42,34,58,47,70,52,78,61,86,68,94][$i] }}%"></div>@endfor
        </div><div class="ccod-skeleton__block ccod-skeleton__line"></div></div>
    @elseif (($variant ?? 'cards') === 'table')
        <div class="ccod-skeleton__card ccod-skeleton__table-card"><div class="ccod-skeleton__block ccod-skeleton__label"></div>
            <div class="ccod-skeleton__table-head">@for ($i=0;$i<4;$i++)<div class="ccod-skeleton__block ccod-skeleton__line"></div>@endfor</div>
            @for ($row=0;$row<5;$row++)<div class="ccod-skeleton__table-row">@for ($col=0;$col<4;$col++)<div class="ccod-skeleton__block ccod-skeleton__line"></div>@endfor</div>@endfor
        </div>
    @elseif (($variant ?? 'cards') === 'metrics')
        <div class="ccod-skeleton__metrics-toolbar"><div class="ccod-skeleton__card"><div class="ccod-skeleton__block ccod-skeleton__label"></div><div class="ccod-skeleton__block ccod-skeleton__line"></div></div><div class="ccod-skeleton__block ccod-skeleton__button"></div></div>
        <div class="ccod-skeleton__metrics-grid">@for ($i=0;$i<2;$i++)<div class="ccod-skeleton__card ccod-skeleton__chart-card"><div class="ccod-skeleton__block ccod-skeleton__label"></div><div class="ccod-skeleton__chart">@for ($bar=0;$bar<12;$bar++)<div class="ccod-skeleton__bar" style="height: {{ [28,42,34,58,47,70,52,78,61,86,68,94][$bar] }}%"></div>@endfor</div></div>@endfor</div>
    @else
        <div class="ccod-skeleton__stats">@for ($i=0;$i<3;$i++)<div class="ccod-skeleton__card"><div class="ccod-skeleton__block ccod-skeleton__label"></div><div class="ccod-skeleton__block ccod-skeleton__value"></div><div class="ccod-skeleton__block ccod-skeleton__line"></div></div>@endfor</div>
    @endif
</div>
