<x-filament-panels::page>
<style>
.reports-workspace{width:100%;max-width:1500px;margin:0 auto;color:var(--fi-color-gray-950, #111827)}
.reports-intro{display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;padding:0 0 1.25rem;margin-bottom:1.25rem;border-bottom:1px solid var(--fi-color-gray-200, #e5e7eb)}
.dark .reports-intro{border-color:var(--fi-color-gray-800, #1f2937)}
.dark .reports-workspace{color:#f9fafb}
.reports-intro p{margin:.35rem 0 0;color:var(--fi-color-gray-500, #6b7280);font-size:.875rem;line-height:1.55}
.reports-retention{white-space:nowrap;color:var(--fi-color-gray-500, #6b7280);font-size:.75rem}
.reports-layout{display:grid;grid-template-columns:minmax(0,1.65fr) minmax(260px,.8fr);gap:1.1rem;align-items:start}
.reports-card{min-width:0;border:1px solid var(--fi-color-gray-200, #e5e7eb);border-radius:.75rem;background:#fff;box-shadow:0 1px 2px rgb(0 0 0 / .04);overflow:hidden}
.dark .reports-card{border-color:var(--fi-color-gray-800, #1f2937);background:#111827}
.reports-card-head{padding:1.1rem 1.25rem;border-bottom:1px solid var(--fi-color-gray-200, #e5e7eb)}
.dark .reports-card-head{border-color:var(--fi-color-gray-800, #1f2937)}
.reports-card-head h2{margin:0;font-size:.95rem;font-weight:650}
.reports-card-head p{margin:.3rem 0 0;color:var(--fi-color-gray-500, #6b7280);font-size:.8rem;line-height:1.5}
.reports-card-body{padding:1.25rem}
.reports-help-list{display:grid;gap:1.15rem;padding:1.2rem 1.25rem}
.reports-help-item+.reports-help-item{padding-top:1rem;border-top:1px solid var(--fi-color-gray-200, #e5e7eb)}
.dark .reports-help-item+.reports-help-item{border-color:var(--fi-color-gray-800, #1f2937)}
.reports-help-item h3{margin:0;font-size:.82rem;font-weight:650}
.reports-help-item p{margin:.35rem 0 0;color:var(--fi-color-gray-500, #6b7280);font-size:.8rem;line-height:1.6}
.reports-form-actions{display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-top:1.2rem;padding-top:1.1rem;border-top:1px solid var(--fi-color-gray-200, #e5e7eb)}
.dark .reports-form-actions{border-color:var(--fi-color-gray-800, #1f2937)}
.reports-form-actions p{margin:0;color:var(--fi-color-gray-500, #6b7280);font-size:.75rem;line-height:1.5}
.reports-history{margin-top:1.35rem}
.reports-history-row{display:flex;justify-content:space-between;align-items:center;gap:1rem;padding:1rem 1.25rem}
.reports-history-row+.reports-history-row{border-top:1px solid var(--fi-color-gray-100)}
.dark .reports-history-row+.reports-history-row{border-color:var(--fi-color-gray-800, #1f2937)}
.reports-history-title{display:flex;align-items:center;flex-wrap:wrap;gap:.55rem;font-size:.875rem;font-weight:600}
.reports-history-meta{margin-top:.3rem;color:var(--fi-color-gray-500, #6b7280);font-size:.75rem;line-height:1.55}
.reports-history-error{margin-top:.45rem;color:var(--fi-color-danger-600);font-size:.8rem}
.reports-empty{padding:2.5rem 1rem;text-align:center;color:var(--fi-color-gray-500, #6b7280);font-size:.85rem}
.reports-downloads{display:flex;flex-wrap:wrap;gap:.5rem}
@media(max-width:900px){.reports-layout{grid-template-columns:1fr}}
@media(max-width:640px){.reports-intro{align-items:flex-start;flex-direction:column}.reports-retention{white-space:normal}.reports-card-body{padding:1rem}.reports-form-actions,.reports-history-row{align-items:flex-start;flex-direction:column}}
</style>
<div class="reports-workspace">
<header class="reports-intro"><div><p>Build downloadable PDF reports for a subscription, project, or business unit.</p></div><div class="reports-retention">Private downloads · Available for 7 days</div></header>
<div class="reports-layout">
<section class="reports-card">
<div class="reports-card-head"><h2>Configure a report</h2><p>Choose the scope, reporting period, and the information to include.</p></div>
<div class="reports-card-body"><form wire:submit="generate">{{ $this->form }}<div class="reports-form-actions"><p>Generation runs in the background. You can safely leave this page.</p><x-filament::button type="submit" icon="heroicon-o-document-arrow-down">Generate PDF</x-filament::button></div></form></div>
</section>
<aside class="reports-card"><div class="reports-card-head"><h2>Report output</h2><p>Understand how your selections are delivered.</p></div><div class="reports-help-list">
<div class="reports-help-item"><h3>Individual reports</h3><p>Select one or more report sections. Each selected section is delivered as a separate PDF.</p></div>
<div class="reports-help-item"><h3>Consolidated PDF</h3><p>Select Consolidated on its own to include all available sections, or combine it with selected sections only.</p></div>
<div class="reports-help-item"><h3>Access and retention</h3><p>Only your developer account can download these files. PDFs are automatically removed after seven days.</p></div>
</div></aside></div>
<div class="reports-history" wire:poll.5s><section class="reports-card">
<div class="reports-card-head"><h2>Your generated reports</h2><p>Processing status and completed downloads appear here.</p></div>
@if($reports->isEmpty())<div class="reports-empty">No reports generated yet. Your exports will appear here.</div>
@else
@foreach($reports as $report)
<div class="reports-history-row"><div style="min-width:0">
<div class="reports-history-title"><span>{{ $report->scope_name }}</span><x-filament::badge :color="match($report->status) { 'completed' => 'success', 'failed' => 'danger', 'processing' => 'info', default => 'gray' }">{{ ucfirst($report->status) }}</x-filament::badge></div>
<div class="reports-history-meta">{{ ucfirst(str_replace('_',' ',$report->scope_type)) }} · {{ $report->period_start->format('d M Y') }} – {{ $report->period_end->format('d M Y') }} · Requested {{ $report->created_at->format('d M Y, H:i') }}</div>
@if($report->failure_reason)<div class="reports-history-error">{{ $report->failure_reason }}</div>@endif
</div>
@if(!empty($report->files))<div class="reports-downloads">@foreach($report->files as $file)<x-filament::button size="sm" color="gray" outlined tag="a" :href="route('developer.reports.download',['report'=>$report,'file'=>$file['key']])">{{ $file['label'] }}</x-filament::button>@endforeach</div>
@elseif(in_array($report->status,['queued','processing'],true))<span class="reports-history-meta">Preparing PDF files…</span>@endif
</div>
@endforeach
@endif
</section></div>
</div>
</x-filament-panels::page>