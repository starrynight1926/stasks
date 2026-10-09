<x-layouts.app title="Note nhanh">
<div x-data="quickNotes()" x-init="init()" class="flex gap-6">
    {{-- LEFT: content --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-primary">Note nhanh việc</h1>
                <p class="text-sm text-neutral mt-1">Ghi chú công việc theo ngày, lưu trực tiếp cho tài khoản của bạn</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="pickDay(todayISO())" class="px-3 py-1.5 text-sm text-neutral hover:text-primary hover:bg-surface-alt rounded-lg transition">Hôm nay</button>
            </div>
        </div>

        {{-- date header --}}
        <div class="flex items-baseline gap-3 mb-4">
            <div class="text-xl font-semibold text-primary" x-text="bigDate"></div>
            <div class="text-xs text-neutral" x-text="monthLabel"></div>
        </div>

        {{-- stats --}}
        <div class="grid grid-cols-5 gap-3 mb-4">
            <template x-for="s in statsRow" :key="s.k">
                <div class="bg-white border border-border rounded-xl p-3">
                    <div class="text-xs text-neutral" x-text="s.k"></div>
                    <div class="text-xl font-semibold mt-0.5" :class="s.cls" x-text="s.v"></div>
                </div>
            </template>
        </div>

        {{-- list --}}
        <div class="space-y-2 mb-3">
            <template x-if="dayTasks.length === 0">
                <div class="bg-white border border-dashed border-border rounded-xl p-10 text-center text-sm text-neutral">
                    Không có công việc nào vào ngày này.
                </div>
            </template>
            <template x-for="t in dayTasks" :key="t.id">
                <div class="bg-white border border-border rounded-xl p-3 flex items-start gap-3 hover:shadow-sm transition"
                     :class="t.status==='done' ? 'opacity-80' : ''">
                    {{-- checkbox --}}
                    <button @click="toggleDone(t)"
                            class="w-5 h-5 mt-0.5 rounded border flex-shrink-0 flex items-center justify-center transition"
                            :class="t.status==='done' ? 'bg-secondary border-secondary' : 'border-neutral-light hover:bg-surface-alt'">
                        <svg x-show="t.status==='done'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </button>

                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium" :class="t.status==='done' ? 'line-through text-neutral' : 'text-primary'" x-text="t.title"></h3>
                        <div class="flex items-center gap-2 mt-1 text-xs text-neutral flex-wrap">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium"
                                  :class="statusPill(t.status).cls">
                                <span class="w-1.5 h-1.5 rounded-full" :class="statusPill(t.status).dot"></span>
                                <span x-text="statusPill(t.status).label"></span>
                            </span>
                            <span x-text="'· ' + (t.project || '—')"></span>
                            <template x-if="t.priority"><span x-text="'· ưu tiên ' + t.priority"></span></template>
                        </div>
                        <template x-if="t.note">
                            <div class="mt-2 text-xs bg-amber-50 text-amber-700 border border-amber-100 rounded px-2 py-1" x-text="'📝 ' + t.note"></div>
                        </template>
                    </div>

                    <div class="flex items-center gap-1 flex-shrink-0 relative" x-data="{ open:false }" @click.outside="open=false">
                        <button @click="open = !open" class="px-2 py-1 text-xs border border-border rounded-md hover:bg-surface-alt transition">Trạng thái ▾</button>
                        <div x-show="open" x-cloak class="absolute right-0 top-9 z-30 w-52 bg-white border border-border rounded-xl shadow-lg py-1">
                            <template x-for="opt in statusOpts" :key="opt.key">
                                <button @click="open=false; changeStatus(t, opt.key)"
                                        class="w-full text-left flex items-center gap-2 px-3 py-2 text-sm text-primary hover:bg-surface-alt transition">
                                    <span class="w-2 h-2 rounded-full" :class="opt.dot"></span>
                                    <span x-text="opt.label"></span>
                                </button>
                            </template>
                        </div>
                        <button @click="removeTask(t)" class="p-1.5 rounded hover:bg-red-50 transition" title="Xóa">
                            <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- quick add --}}
        <div class="bg-white border border-dashed border-border rounded-xl p-3 flex items-center gap-3 focus-within:border-secondary focus-within:ring-2 focus-within:ring-secondary/20 transition">
            <div class="w-5 h-5 rounded border border-dashed border-neutral-light flex items-center justify-center text-neutral text-sm">+</div>
            <input x-model="newTitle" @keydown.enter="addNew()" type="text" placeholder="Thêm công việc cho ngày này — Enter để lưu"
                   class="flex-1 bg-transparent outline-none text-sm text-primary placeholder:text-neutral-light">
            <input x-model="newProj" type="text" placeholder="dự án" list="projList"
                   class="w-24 px-2 py-1 text-xs bg-surface-alt border border-border rounded text-neutral outline-none focus:border-secondary">
            <datalist id="projList">
                <template x-for="p in projects" :key="p"><option :value="p"></option></template>
            </datalist>
            <span class="text-[10px] text-neutral">⏎ Enter</span>
        </div>
    </div>

    {{-- RIGHT: calendar --}}
    <aside class="w-72 flex-shrink-0 bg-white border border-border rounded-xl p-4 h-fit sticky top-20">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-semibold text-primary capitalize" x-text="calMonthLabel"></div>
            <div class="flex gap-1">
                <button @click="shiftMonth(-1)" class="w-7 h-7 flex items-center justify-center rounded hover:bg-surface-alt transition text-neutral">‹</button>
                <button @click="shiftMonth(1)" class="w-7 h-7 flex items-center justify-center rounded hover:bg-surface-alt transition text-neutral">›</button>
            </div>
        </div>
        <div class="grid grid-cols-7 gap-0.5 text-center mb-1">
            <template x-for="d in ['CN','T2','T3','T4','T5','T6','T7']" :key="d">
                <div class="text-[10px] font-medium text-neutral uppercase py-1" x-text="d"></div>
            </template>
        </div>
        <div class="grid grid-cols-7 gap-0.5">
            <template x-for="c in calCells" :key="c.iso + '-' + c.i">
                <button @click="pickDay(c.iso)"
                        class="aspect-square rounded flex flex-col items-center justify-between py-1 text-xs transition"
                        :class="[
                            c.iso===currentISO ? 'bg-secondary text-white' :
                              (c.iso===todayStr ? 'text-secondary font-semibold hover:bg-surface-alt' :
                                (c.outside ? 'text-neutral-light hover:bg-surface-alt' : 'text-primary hover:bg-surface-alt'))
                        ]">
                    <span x-text="c.day"></span>
                    <span class="flex gap-0.5 h-1.5">
                        <template x-for="s in c.dots" :key="s">
                            <span class="w-1 h-1 rounded-full"
                                  :class="c.iso===currentISO ? 'bg-white/90' : dotColor(s)"></span>
                        </template>
                    </span>
                </button>
            </template>
        </div>
        <div class="mt-3 pt-3 border-t border-border space-y-1 text-[11px] text-neutral">
            <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Đã xong</div>
            <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>Đang thực hiện</div>
            <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Xong một phần</div>
            <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Không thể xong</div>
            <div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-neutral-light"></span>Chưa làm</div>
        </div>
    </aside>
</div>

<script>
function quickNotes(){
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const URLS = {
        list:   @json(route('quick-notes.list')),
        store:  @json(route('quick-notes.store')),
        base:   '/api/quick-notes',
    };
    const STATUS_OPTS = [
        { key:'todo',    label:'Chưa làm',       dot:'bg-neutral-light' },
        { key:'doing',   label:'Đang thực hiện', dot:'bg-secondary' },
        { key:'done',    label:'Đã xong',        dot:'bg-green-500' },
        { key:'partial', label:'Xong một phần…', dot:'bg-amber-500' },
        { key:'blocked', label:'Không thể xong…',dot:'bg-red-500' },
    ];
    const PILLS = {
        todo:    { label:'Chưa làm',       cls:'bg-slate-100 text-slate-600',  dot:'bg-neutral-light' },
        doing:   { label:'Đang thực hiện', cls:'bg-blue-50 text-blue-700',     dot:'bg-secondary' },
        done:    { label:'Đã xong',        cls:'bg-green-50 text-green-700',   dot:'bg-green-500' },
        partial: { label:'Xong một phần',  cls:'bg-amber-50 text-amber-700',   dot:'bg-amber-500' },
        blocked: { label:'Không thể xong', cls:'bg-red-50 text-red-700',       dot:'bg-red-500' },
    };

    function toISO(d){ const z=new Date(d.getTime()-d.getTimezoneOffset()*60000); return z.toISOString().slice(0,10); }
    function addDays(d, n){ const x=new Date(d); x.setDate(x.getDate()+n); return x; }

    return {
        tasks: [],
        currentISO: toISO(new Date()),
        todayStr: toISO(new Date()),
        calMonth: (() => { const d=new Date(); d.setDate(1); return d; })(),
        newTitle: '', newProj: '',
        statusOpts: STATUS_OPTS,

        async init(){ await this.reload(); },
        todayISO(){ return toISO(new Date()); },

        async api(url, opts={}){
            const r = await fetch(url, {
                headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'},
                ...opts,
            });
            if(!r.ok) throw new Error('HTTP '+r.status);
            return r.status===204 ? null : r.json();
        },
        async reload(){ this.tasks = await this.api(URLS.list); },

        pickDay(iso){
            this.currentISO = iso;
            const d = new Date(iso); this.calMonth = new Date(d.getFullYear(), d.getMonth(), 1);
        },
        shiftMonth(dir){ this.calMonth = new Date(this.calMonth.getFullYear(), this.calMonth.getMonth()+dir, 1); },

        get bigDate(){
            const d=new Date(this.currentISO);
            const dow=d.toLocaleDateString('vi-VN',{weekday:'long'});
            return dow.charAt(0).toUpperCase()+dow.slice(1)+', '+String(d.getDate()).padStart(2,'0')+'/'+String(d.getMonth()+1).padStart(2,'0');
        },
        get monthLabel(){ return new Date(this.currentISO).toLocaleDateString('vi-VN',{month:'long',year:'numeric'}); },
        get calMonthLabel(){ return this.calMonth.toLocaleDateString('vi-VN',{month:'long',year:'numeric'}); },

        get dayTasks(){ return this.tasks.filter(t => t.iso===this.currentISO); },
        get projects(){ return [...new Set(this.tasks.map(t=>t.project).filter(Boolean))]; },

        get statsRow(){
            const t = this.dayTasks;
            const c = s => t.filter(x=>x.status===s).length;
            return [
                { k:'Tổng',       v:t.length, cls:'text-primary' },
                { k:'Đã xong',    v:c('done'),    cls:'text-green-600' },
                { k:'Đang làm',   v:c('doing'),   cls:'text-secondary' },
                { k:'Một phần',   v:c('partial'), cls:'text-amber-600' },
                { k:'Không xong', v:c('blocked'), cls:'text-red-600' },
            ];
        },

        get calCells(){
            const first = new Date(this.calMonth);
            const start = new Date(first); start.setDate(1 - first.getDay());
            const byDay = {};
            this.tasks.forEach(t => (byDay[t.iso] = byDay[t.iso] || []).push(t.status));
            const cells = [];
            for (let i=0;i<42;i++){
                const d = addDays(start, i);
                const iso = toISO(d);
                const list = byDay[iso] || [];
                const uniq = [];
                ['done','doing','partial','blocked','todo'].forEach(s => { if (list.includes(s)) uniq.push(s); });
                cells.push({
                    i, iso, day: d.getDate(),
                    outside: d.getMonth() !== first.getMonth(),
                    dots: uniq.slice(0,3),
                });
            }
            return cells;
        },

        statusPill(k){ return PILLS[k] || PILLS.todo; },
        dotColor(s){ return PILLS[s]?.dot || 'bg-neutral-light'; },

        async addNew(){
            const title = this.newTitle.trim(); if (!title) return;
            const t = await this.api(URLS.store, { method:'POST', body:JSON.stringify({
                title, iso: this.currentISO, status:'todo', project: this.newProj.trim() || null
            })});
            this.tasks.push(t);
            this.newTitle = '';
        },
        async toggleDone(t){
            const next = t.status==='done' ? 'todo' : 'done';
            const updated = await this.api(URLS.base+'/'+t.id, { method:'PATCH', body:JSON.stringify({ status:next, note: next==='done' ? null : t.note }) });
            const i = this.tasks.findIndex(x=>x.id===t.id); if (i>=0) this.tasks[i] = updated;
        },
        async changeStatus(t, s){
            let note = t.note;
            if (s==='partial' || s==='blocked'){
                const q = s==='partial' ? 'Phần nào đã xong, phần nào còn lại?' : 'Lý do không thể xong là gì?';
                const v = prompt(q, note || ''); if (v===null) return;
                note = v.trim() || null;
            } else { note = null; }
            const updated = await this.api(URLS.base+'/'+t.id, { method:'PATCH', body:JSON.stringify({ status:s, note }) });
            const i = this.tasks.findIndex(x=>x.id===t.id); if (i>=0) this.tasks[i] = updated;
        },
        async removeTask(t){
            if (!confirm('Xóa công việc này?')) return;
            await this.api(URLS.base+'/'+t.id, { method:'DELETE' });
            this.tasks = this.tasks.filter(x=>x.id!==t.id);
        },
    };
}
</script>
</x-layouts.app>
