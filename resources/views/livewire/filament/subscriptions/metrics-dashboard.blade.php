<div x-data="metricsDashboard(@js($subscriptionId))" x-init="init()" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div><h2 class="text-lg font-semibold text-gray-950 dark:text-white">Resource Metrics</h2><p class="text-sm text-gray-500">Azure Monitor data is requested only while Metrics is active.</p></div>
        <div class="flex flex-wrap gap-2">
            <select x-model="preset" @change="applyPreset()" class="fi-input rounded-lg">
                <option value="15m">Last 15 minutes</option><option value="30m">Last 30 minutes</option><option value="1h">Last 1 hour</option><option value="6h">Last 6 hours</option><option value="12h">Last 12 hours</option><option value="24h">Last 24 hours</option><option value="7d">Last 7 days</option><option value="30d">Last 30 days</option><option value="custom">Custom range</option>
            </select>
            <button type="button" @click="refreshAll()" class="fi-btn fi-btn-size-md fi-btn-color-gray">Refresh</button>
            <button type="button" x-show="canManage" @click="openBuilder()" class="fi-btn fi-btn-size-md fi-btn-color-primary">Add graph</button>
        </div>
    </div>
    <div x-show="preset === 'custom'" class="flex flex-wrap items-end gap-3 rounded-xl border p-4">
        <label class="text-sm">Start (IST)<input type="datetime-local" x-model="customStart" class="fi-input mt-1 block"></label>
        <label class="text-sm">End (IST)<input type="datetime-local" x-model="customEnd" class="fi-input mt-1 block"></label>
        <button type="button" @click="applyCustomRange()" class="fi-btn fi-btn-size-md fi-btn-color-primary">Apply</button>
    </div>
    <p x-show="loading" class="text-sm text-gray-500">Loading Azure resource metric definitions…</p>
    <p x-show="error" x-text="error" class="rounded-lg bg-danger-50 p-3 text-sm text-danger-700"></p>
    <div x-show="graphs.length === 0 && !loading" class="rounded-xl border border-dashed p-8 text-center">
        <p class="font-medium">No metric graphs configured</p><p class="mt-1 text-sm text-gray-500">Create a graph for a resource and add one or more metric series.</p>
        <button type="button" x-show="canManage" @click="openBuilder()" class="fi-btn fi-btn-size-md fi-btn-color-primary mt-4">Create first graph</button>
    </div>
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <template x-for="graph in graphs" :key="graph.id">
            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-start justify-between gap-3"><div><h3 class="font-semibold" x-text="graph.title"></h3><p class="text-xs text-gray-500" x-text="graph.resource?.name || graph.resource_id"></p></div>
                    <button type="button" x-show="canManage" @click="removeGraph(graph)" class="text-xs text-danger-600">Remove</button>
                </div>
                <svg viewBox="0 0 720 230" class="mt-3 h-56 w-full" role="img">
                    <line x1="42" y1="10" x2="42" y2="196" stroke="currentColor" opacity=".2"/><line x1="42" y1="196" x2="704" y2="196" stroke="currentColor" opacity=".2"/>
                    <template x-for="(series, index) in (graph.series || [])" :key="index"><polyline :points="chartPoints(graph.id, index)" fill="none" :stroke="lineColor(index)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></template>
                    <text x="42" y="218" fill="currentColor" font-size="10" x-text="rangeLabelStart()"></text><text x="704" y="218" text-anchor="end" fill="currentColor" font-size="10">Now</text>
                </svg>
                <div class="mt-2 flex flex-wrap gap-3"><template x-for="(series, index) in (graph.series || [])" :key="index"><div class="flex items-center gap-2 text-xs"><span class="h-2 w-5 rounded" :style="'background:' + lineColor(index)"></span><span x-text="series.metric + ' · ' + series.aggregation"></span></div></template></div>
                <p x-show="graphLoading[graph.id]" class="mt-2 text-xs text-gray-400">Updating…</p><p x-show="graphErrors[graph.id]" class="mt-2 text-xs text-danger-600" x-text="graphErrors[graph.id]"></p>
            </section>
        </template>
    </div>
    <div x-show="builderOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @keydown.escape.window="builderOpen = false">
        <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-xl bg-white p-5 shadow-xl dark:bg-gray-900" @click.outside="builderOpen = false">
            <div class="flex justify-between"><h3 class="text-lg font-semibold">Add metric graph</h3><button type="button" @click="builderOpen = false">✕</button></div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <label class="text-sm">Resource<select x-model="draft.resource_id" @change="resetDraftSeries()" class="fi-input mt-1 block w-full"><option value="">Choose resource</option><template x-for="resource in resources" :key="resource.resource_id"><option :value="resource.resource_id" x-text="resource.name + ' (' + resource.resource_type + ')'"></option></template></select></label>
                <label class="text-sm">Graph title<input x-model="draft.title" maxlength="160" class="fi-input mt-1 block w-full" placeholder="e.g. VM performance"></label>
            </div>
            <div class="mt-5 space-y-3"><div class="flex justify-between"><h4 class="text-sm font-semibold">Metric series</h4><button type="button" @click="addDraftSeries()" class="text-sm text-primary-600">+ Add series</button></div>
                <template x-for="(series, index) in draft.series" :key="index"><div class="grid gap-3 rounded-lg border p-3 sm:grid-cols-[1fr_1fr_auto]">
                    <label class="text-sm">Metric<select x-model="series.metric" @change="series.aggregation = ''" class="fi-input mt-1 block w-full"><option value="">Choose metric</option><template x-for="metric in (selectedResource?.metrics || [])" :key="metric.name"><option :value="metric.name" x-text="metric.display_name + ' (' + metric.unit + ')'"></option></template></select></label>
                    <label class="text-sm">Aggregation<select x-model="series.aggregation" class="fi-input mt-1 block w-full"><option value="">Choose aggregation</option><template x-for="aggregation in aggregationsFor(series.metric)" :key="aggregation"><option :value="aggregation" x-text="aggregation"></option></template></select></label>
                    <button type="button" @click="draft.series.splice(index, 1)" class="text-xs text-danger-600">Remove</button>
                </div></template>
            </div>
            <div class="mt-6 flex justify-end gap-2"><button type="button" @click="builderOpen = false" class="fi-btn fi-btn-size-md fi-btn-color-gray">Cancel</button><button type="button" @click="saveGraph()" :disabled="saving || !draft.resource_id || !draft.title.trim() || draft.series.length === 0 || draft.series.some(s => !s.metric || !s.aggregation)" class="fi-btn fi-btn-size-md fi-btn-color-primary" x-text="saving ? 'Saving…' : 'Save graph'"></button></div>
        </div>
    </div>
</div>
@script
<script>
    Alpine.data('metricsDashboard', (subscriptionId) => ({
        subscriptionId, resources: [], graphs: [], graphData: {}, graphFetchedAt: {}, graphLoading: {}, graphErrors: {},
        loading: false, saving: false, error: '', builderOpen: false,
        canManage: @js(auth()->guard('developers')->user()?->is_active === true || (auth()->guard('web')->user()?->is_active === true && app(\App\Services\AccessAuthorizationService::class)->hasPermission(auth()->guard('web')->user(), 'resources.metrics.manage', $this->record))),
        preset: '1h', customStart: '', customEnd: '', timer: null, refreshTimer: null, active: false, loadingInitial: false,
        draft: { resource_id: '', title: '', series: [{ metric: '', aggregation: '' }] },
        init() {
            this.applyPreset();
            this.timer = window.setInterval(() => this.checkActive(), 1200);
            this.refreshTimer = window.setInterval(() => { if (this.active && !this.loadingInitial) this.refreshAll(); }, 30000);
            document.addEventListener('visibilitychange', () => this.checkActive());
            document.addEventListener('click', (event) => { if (event.target.closest('[role="tab"],button[aria-selected]')) requestAnimationFrame(() => this.checkActive()); });
            window.addEventListener('popstate', () => this.checkActive());
            this.checkActive();
        },
        destroy() { if (this.timer) window.clearInterval(this.timer); if (this.refreshTimer) window.clearInterval(this.refreshTimer); },
        isMetricsTabActive() {
            if (['Metrics', 'metrics'].includes(new URLSearchParams(window.location.search).get('tab'))) return true;
            return [...document.querySelectorAll('[role="tab"][aria-selected="true"],button[aria-selected="true"]')].some(el => el.textContent.trim() === 'Metrics');
        },
        checkActive() {
            const next = this.isMetricsTabActive() && document.visibilityState === 'visible';
            if (next && !this.active) { this.active = true; this.loadInitial(); }
            else if (!next && this.active) this.active = false;
        },
        async request(path, options = {}) {
            const response = await fetch('/subscriptions/' + encodeURIComponent(this.subscriptionId) + '/metrics/' + path, {
                ...options, credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', ...(options.headers || {}) }
            });
            const body = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(body.message || 'Metrics request failed.');
            return body.data;
        },
        async loadInitial() {
            if (!this.active || this.loadingInitial) return;
            this.loadingInitial = true; this.loading = true; this.error = '';
            try { [this.resources, this.graphs] = await Promise.all([this.request('resources'), this.request('graphs')]); await this.refreshAll(true); }
            catch (e) { this.error = e.message; }
            finally { this.loading = false; this.loadingInitial = false; }
        },
        async refreshAll(forceFull = false) {
            if (!this.active || document.visibilityState !== 'visible') return;
            const range = this.getRange(); if (!range) return;
            await Promise.all(this.graphs.map(graph => this.refreshGraph(graph, range, forceFull)));
        },
        async refreshGraph(graph, range = this.getRange(), forceFull = false) {
            if (!this.active || !range || this.graphLoading[graph.id]) return;
            this.graphLoading[graph.id] = true; this.graphErrors[graph.id] = '';
            try {
                const previous = this.graphData[graph.id];
                const queryRange = { ...range };
                if (!forceFull && previous && this.graphFetchedAt[graph.id]) {
                    const overlap = this.requestedIntervalMinutes() * 2;
                    const incrementalStart = new Date(Date.parse(this.graphFetchedAt[graph.id]) - overlap * 60000).toISOString();
                    if (Date.parse(incrementalStart) > Date.parse(range.start)) queryRange.start = incrementalStart;
                }
                const fresh = await this.request('graphs/' + graph.id + '/data?' + new URLSearchParams(queryRange));
                this.graphData[graph.id] = forceFull || !previous ? fresh : this.mergeSeries(previous, fresh, range.start);
                this.graphFetchedAt[graph.id] = new Date().toISOString();
            } catch (e) { this.graphErrors[graph.id] = e.message; }
            finally { this.graphLoading[graph.id] = false; }
        },
        requestedIntervalMinutes() { return this.interval() === 'PT1H' ? 60 : Number(this.interval().match(/\d+/)?.[0] || 1); },
        mergeSeries(previous, fresh, minimumTime) {
            return previous.map((oldSeries, index) => {
                const newSeries = fresh[index] || oldSeries;
                const oldMetrics = oldSeries.value || [];
                const newMetrics = newSeries.value || [];
                const metricKeys = new Set([...oldMetrics, ...newMetrics].map(metric => metric.name?.value || metric.name));
                const value = [...metricKeys].map(key => {
                    const oldMetric = oldMetrics.find(metric => (metric.name?.value || metric.name) === key);
                    const newMetric = newMetrics.find(metric => (metric.name?.value || metric.name) === key);
                    const source = newMetric || oldMetric;
                    const oldLines = oldMetric?.timeseries || [];
                    const newLines = newMetric?.timeseries || [];
                    const lineKeys = new Set([...oldLines, ...newLines].map(line => JSON.stringify(line.metadatavalues || [])));
                    const timeseries = [...lineKeys].map(lineKey => {
                        const oldLine = oldLines.find(line => JSON.stringify(line.metadatavalues || []) === lineKey);
                        const newLine = newLines.find(line => JSON.stringify(line.metadatavalues || []) === lineKey);
                        const points = new Map();
                        [...(oldLine?.data || []), ...(newLine?.data || [])].forEach(point => {
                            if (Date.parse(point.timeStamp) >= Date.parse(minimumTime)) points.set(point.timeStamp, point);
                        });
                        return { ...(newLine || oldLine), data: [...points.values()].sort((a, b) => Date.parse(a.timeStamp) - Date.parse(b.timeStamp)) };
                    });
                    return { ...source, timeseries };
                });
                return { ...newSeries, value };
            });
        },
        getRange() {
            const end = this.preset === 'custom' ? this.customEnd : this.localDateTime(0);
            const start = this.preset === 'custom' ? this.customStart : this.localDateTime(this.durationMinutes());
            if (!start || !end) return null;
            const iso = value => new Date(value + ':00+05:30').toISOString();
            return { start: iso(start), end: iso(end), interval: this.interval() };
        },
        localDateTime(minutesAgo) { const d = new Date(Date.now() + 330 * 60000 - minutesAgo * 60000); return d.toISOString().slice(0,16); },
        durationMinutes() { if (this.preset === 'custom' && this.customStart && this.customEnd) return Math.max(1, Math.round((Date.parse(this.customEnd + ':00+05:30') - Date.parse(this.customStart + ':00+05:30')) / 60000)); return ({'15m':15,'30m':30,'1h':60,'6h':360,'12h':720,'24h':1440,'7d':10080,'30d':43200})[this.preset] || 60; },
        interval() { const m = this.durationMinutes(); return m <= 60 ? 'PT1M' : m <= 1440 ? 'PT5M' : 'PT1H'; },
        applyPreset() { if (this.preset === 'custom') { if (!this.customEnd) this.customEnd = this.localDateTime(0); if (!this.customStart) this.customStart = this.localDateTime(60); } this.graphData = {}; this.graphFetchedAt = {}; if (this.active) this.refreshAll(true); },
        applyCustomRange() { this.graphData = {}; this.graphFetchedAt = {}; this.refreshAll(true); },
        rangeLabelStart() { return this.preset === 'custom' ? this.customStart : this.preset + ' ago'; },
        lineColor(i) { return ['#2563eb','#16a34a','#d97706','#9333ea','#dc2626','#0891b2'][i % 6]; },
        chartPoints(graphId, index) {
            const item = (this.graphData[graphId] || [])[index];
            const vals = item?.value?.flatMap(m => (m.timeseries || []).flatMap(line => line.data || [])) || [];
            vals.sort((a, b) => Date.parse(a.timeStamp) - Date.parse(b.timeStamp));
            const key = String(item?.aggregation || '').toLowerCase();
            const nums = vals.map(p => p[key]).filter(v => v !== null && v !== undefined && Number.isFinite(Number(v))).map(Number);
            if (!nums.length) return '';
            const min = Math.min(...nums), max = Math.max(...nums), span = max - min || 1;
            return nums.map((v,i) => (44 + i / Math.max(1, nums.length - 1) * 658).toFixed(1) + ',' + (190 - (v-min)/span*170).toFixed(1)).join(' ');
        },
        get selectedResource() { return this.resources.find(r => r.resource_id === this.draft.resource_id); },
        aggregationsFor(name) { return this.selectedResource?.metrics?.find(m => m.name === name)?.aggregations || []; },
        resetDraftSeries() { this.draft.series = [{ metric: '', aggregation: '' }]; },
        addDraftSeries() { if (this.draft.series.length < 20) this.draft.series.push({ metric: '', aggregation: '' }); },
        openBuilder() { this.builderOpen = true; if (!this.resources.length) this.loadInitial(); },
        async saveGraph() {
            this.saving = true;
            try { const created = await this.request('graphs', {method:'POST', body:JSON.stringify(this.draft)}); this.builderOpen = false; this.draft = {resource_id:'',title:'',series:[{metric:'',aggregation:''}]}; this.graphs.push(created); await this.refreshGraph(created, this.getRange(), true); }
            catch (e) { this.error = e.message; } finally { this.saving = false; }
        },
        async removeGraph(graph) {
            if (!window.confirm('Remove this graph for everyone who can view this subscription?')) return;
            try { await this.request('graphs/' + graph.id, {method:'DELETE'}); this.graphs = this.graphs.filter(g => g.id !== graph.id); delete this.graphData[graph.id]; delete this.graphFetchedAt[graph.id]; }
            catch (e) { this.error = e.message; }
        }
    }));
</script>
@endscript
