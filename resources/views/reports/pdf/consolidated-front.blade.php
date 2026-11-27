<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
@page { margin:25mm 20mm; }
body { font-family:DejaVu Sans,sans-serif; color:#20252b; font-size:10pt; line-height:1.55; }
.cover { height:245mm; text-align:center; page-break-after:always; }
.cover-inner { padding-top:78mm; }
.logo { max-width:150px; max-height:48px; margin-bottom:24px; }
.brand { font-size:10pt; font-weight:bold; letter-spacing:2px; color:#526171; text-transform:uppercase; }
h1 { font-size:27pt; font-weight:normal; margin:15px 0; }
.rule { width:70px; border-top:2px solid #b18a43; margin:25px auto; }
.meta { color:#687582; line-height:2; }
h2 { font-size:17pt; font-weight:normal; margin:0 0 20px; }
table { width:100%; border-collapse:collapse; }
th { text-align:left; background:#f1f3f5; padding:10px 8px; color:#52606d; font-size:8pt; }
td { padding:11px 8px; border-bottom:1px solid #e0e4e8; font-size:9pt; }
.footer { position:fixed; bottom:-14mm; left:0; right:0; border-top:1px solid #d8dde2; padding-top:5px; color:#77818a; font-size:7pt; }
</style>
</head>
<body>
<div class="cover"><div class="cover-inner">
    @if($logoPath)<img class="logo" src="{{ $logoPath }}" alt="{{ $brandName }}">@endif
    <div class="brand">{{ $brandName }}</div><div class="rule"></div>
    <h1>Consolidated report</h1>
    <div class="meta">{{ $report->scope_name }}<br>{{ $report->period_start->format('d M Y') }} – {{ $report->period_end->format('d M Y') }}<br>Generated {{ $generatedAt->format('d M Y H:i') }}</div>
</div></div>
<div class="footer">System generated report · {{ $brandName }}</div>
<h2>Report index</h2>
<table><thead><tr><th>Report section</th><th>Starting page</th><th>Pages</th></tr></thead><tbody>
@foreach($index as $item)
<tr><td>{{ $item['title'] }}</td><td>{{ $item['page'] }}</td><td>{{ $item['pages'] }}</td></tr>
@endforeach
</tbody></table>
</body>
</html>
