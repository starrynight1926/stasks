<x-layouts.app title="Note nhanh">
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<div x-data="quickNotes()" x-init="init()" class="flex gap-6" id="qtRoot">
    {{-- LEFT: content --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-primary">Note nhanh việc</h1>
                <p class="text-sm text-neutral mt-1">Ghi chú công việc theo ngày, lưu trực tiếp cho tài khoản của bạn</p>
            </div>
            <div class="flex items-center gap-2">
                @if(count($visibleUsers) > 1)
                    <select x-model.number="userId" @change="reload()"
                            class="px-3 py-1.5 text-sm border border-border rounded-lg outline-none focus:border-secondary transition bg-white">
                        @foreach($visibleUsers as $u)
                            <option value="{{ $u->id }}" @selected($u->id == session('member_id'))>{{ $u->name }} · {{ ucfirst($u->level ?? 'member') }}</option>
                        @endforeach
                    </select>
                @endif
                <button @click="pickDay(todayISO())" class="px-3 py-1.5 text-sm text-neutral hover:text-primary hover:bg-surface-alt rounded-lg transition">Hôm nay</button>
                <button @click="exportPdf()" :disabled="exporting"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm border border-border rounded-lg hover:bg-surface-alt transition disabled:opacity-50">
                    <svg class="w-4 h-4 text-neutral" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span x-text="exporting ? 'Đang xuất…' : 'Xuất PDF'"></span>
                </button>
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
                     :class="effectiveStatus(t)==='done' ? 'opacity-80' : ''">
                    {{-- checkbox --}}
                    <button @click="toggleDone(t)"
                            class="w-5 h-5 mt-0.5 rounded border flex-shrink-0 flex items-center justify-center transition"
                            :class="effectiveStatus(t)==='done' ? 'bg-secondary border-secondary' : 'border-neutral-light hover:bg-surface-alt'">
                        <svg x-show="effectiveStatus(t)==='done'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </button>

                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium" :class="effectiveStatus(t)==='done' ? 'line-through text-neutral' : 'text-primary'">
                            <span x-text="t.title"></span>
                            <template x-if="carrySuffix(t)">
                                <span class="text-neutral font-normal" x-text="' — ' + carrySuffix(t)"></span>
                            </template>
                        </h3>
                        <div class="flex items-center gap-2 mt-1 text-xs text-neutral flex-wrap">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium"
                                  :class="statusPill(effectiveStatus(t)).cls">
                                <span class="w-1.5 h-1.5 rounded-full" :class="statusPill(effectiveStatus(t)).dot"></span>
                                <span x-text="statusPill(effectiveStatus(t)).label"></span>
                            </span>
                            <span x-text="'· ' + (t.project || '—')"></span>
                            <template x-if="t.priority"><span x-text="'· ưu tiên ' + t.priority"></span></template>
                        </div>
                        <template x-if="t.note">
                            <div class="mt-2 text-xs bg-amber-50 text-amber-700 border border-amber-100 rounded px-2 py-1 flex items-start gap-2 group/note">
                                <span class="flex-1 whitespace-pre-wrap" x-text="'📝 ' + t.note"></span>
                                <button @click="editNote(t)" class="opacity-0 group-hover/note:opacity-100 text-amber-700 hover:underline text-[10px] flex-shrink-0" title="Sửa">Sửa</button>
                                <button @click="clearNote(t)" class="opacity-0 group-hover/note:opacity-100 text-amber-700 hover:underline text-[10px] flex-shrink-0" title="Xóa ghi chú">Xóa</button>
                            </div>
                        </template>

                        {{-- subtasks --}}
                        <template x-if="(t.subtasks || []).length">
                            <div class="mt-2 space-y-1">
                                <template x-for="(st, idx) in t.subtasks" :key="st.id">
                                    <div class="flex items-center gap-2 text-xs group/st">
                                        <button @click="toggleSubtask(t, idx)"
                                                class="w-4 h-4 rounded border flex-shrink-0 flex items-center justify-center transition"
                                                :class="st.done ? 'bg-secondary border-secondary' : 'border-neutral-light hover:bg-surface-alt'">
                                            <svg x-show="st.done" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <span class="flex-1" :class="st.done ? 'line-through text-neutral' : 'text-primary'" x-text="st.title"></span>
                                        <button @click="removeSubtask(t, idx)" class="opacity-0 group-hover/st:opacity-100 text-danger hover:underline text-[10px]" title="Xóa">✕</button>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- inline add buttons --}}
                        <div class="mt-2 flex items-center gap-3 text-[11px] text-neutral flex-wrap">
                            <button @click="addSubtask(t)" class="hover:text-secondary transition inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Thêm task con
                            </button>
                            <template x-if="!t.note">
                                <button @click="editNote(t)" class="hover:text-secondary transition inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Thêm ghi chú
                                </button>
                            </template>
                            <template x-if="canCarry(t)">
                                <button @click="carryOver(t)" class="hover:text-secondary transition inline-flex items-center gap-1 font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    Tiếp tục công việc
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 flex-shrink-0 relative">
                        <button @click.stop="openStatusId = (openStatusId===t.id ? null : t.id)" class="px-2 py-1 text-xs border border-border rounded-md hover:bg-surface-alt transition">Trạng thái ▾</button>
                        <div x-show="openStatusId===t.id" x-cloak @click.outside="openStatusId=null"
                             class="absolute right-0 top-9 z-50 w-52 bg-white border border-border rounded-xl shadow-lg py-1">
                            <template x-for="opt in statusOpts" :key="opt.key">
                                <button @click="openStatusId=null; changeStatus(t, opt.key)"
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
            <input x-model="newTitle" @keydown.enter.prevent="if (!$event.isComposing && $event.keyCode !== 229) addNew()" type="text" placeholder="Thêm công việc cho ngày này — Enter để lưu"
                   class="flex-1 bg-transparent outline-none text-sm text-primary placeholder:text-neutral-light">
            <input x-show="projects.length" x-cloak x-model="newProj" type="text" placeholder="dự án" list="projList"
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
        listBase: @json(route('quick-notes.list')),
        storeBase: @json(route('quick-notes.store')),
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
        openStatusId: null,
        userId: @json(session('member_id')),
        exporting: false,

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
        async reload(){
            const u = this.userId ? ('?user_id='+this.userId) : '';
            this.tasks = await this.api(URLS.listBase + u);
        },

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

        _adding: false,
        async addNew(){
            if (this._adding) return;
            const title = this.newTitle.trim(); if (!title) return;
            this._adding = true;
            try {
                const url = URLS.storeBase + (this.userId ? ('?user_id='+this.userId) : '');
                const t = await this.api(url, { method:'POST', body:JSON.stringify({
                    title, iso: this.currentISO, status:'todo', project: this.newProj.trim() || null
                })});
                this.tasks.push(t);
                this.newTitle = '';
            } finally {
                this._adding = false;
            }
        },
        async _patch(id, payload){
            const updated = await this.api(URLS.base+'/'+id, { method:'PATCH', body:JSON.stringify(payload) });
            const i = this.tasks.findIndex(x=>x.id===id); if (i>=0) this.tasks[i] = updated;
            return updated;
        },
        async toggleDone(t){
            // if the task is only "effectively done" via a done clone, toggle that clone instead
            if (t.status !== 'done' && this.effectiveStatus(t) === 'done'){
                const clone = this.tasks.find(x => x.id === t.carried_to_id);
                if (clone) return this.toggleDone(clone);
            }
            const next = t.status==='done' ? 'todo' : 'done';
            await this._patch(t.id, { status:next });
        },
        async changeStatus(t, s){
            let payload = { status: s };
            if (s==='partial' || s==='blocked'){
                const q = s==='partial' ? 'Phần nào đã xong, phần nào còn lại?' : 'Lý do không thể xong là gì?';
                const v = prompt(q, t.note || ''); if (v===null) return;
                const n = v.trim();
                if (n) payload.note = n;
            }
            await this._patch(t.id, payload);
        },
        async editNote(t){
            const v = prompt('Ghi chú cho công việc:', t.note || '');
            if (v===null) return;
            await this._patch(t.id, { note: v.trim() || null });
        },
        async clearNote(t){
            if (!confirm('Xóa ghi chú?')) return;
            await this._patch(t.id, { note: null });
        },
        async addSubtask(t){
            const v = prompt('Tên task con:');
            if (v===null) return;
            const title = v.trim(); if (!title) return;
            const list = [...(t.subtasks || []), { id: Date.now(), title, done: false }];
            await this._patch(t.id, { subtasks: list });
        },
        async toggleSubtask(t, idx){
            const list = (t.subtasks || []).map((s, i) => i===idx ? { ...s, done: !s.done } : s);
            await this._patch(t.id, { subtasks: list });
        },
        async removeSubtask(t, idx){
            const list = (t.subtasks || []).filter((_, i) => i!==idx);
            await this._patch(t.id, { subtasks: list });
        },
        canCarry(t){
            return t.iso < this.todayStr && this.effectiveStatus(t) !== 'done' && !t.carried_to_id;
        },
        effectiveStatus(t){
            if (t.carried_to_id){
                const clone = this.tasks.find(x => x.id === t.carried_to_id);
                if (clone && clone.status === 'done') return 'done';
            }
            return t.status;
        },
        carrySuffix(t){
            if (!t.carried_to_id) return null;
            const clone = this.tasks.find(x => x.id === t.carried_to_id);
            if (!clone) return null;
            const d = new Date(clone.iso);
            const label = `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
            return (clone.status === 'done' ? 'Hoàn thành ' : 'Tiếp tục ') + label;
        },
        async carryOver(t){
            const def = this.todayStr;
            const iso = prompt('Chuyển tiếp sang ngày nào? (YYYY-MM-DD)', def);
            if (iso===null) return;
            const target = iso.trim(); if (!target) return;
            if (!/^\d{4}-\d{2}-\d{2}$/.test(target)){ alert('Ngày không hợp lệ, định dạng YYYY-MM-DD'); return; }
            if (target < this.todayStr && !confirm('Ngày bạn chọn ở quá khứ. Vẫn tiếp tục?')) return;
            const r = await this.api(URLS.base+'/'+t.id+'/carry', { method:'POST', body:JSON.stringify({ iso: target }) });
            const i = this.tasks.findIndex(x=>x.id===t.id); if (i>=0) this.tasks[i] = r.original;
            if (!this.tasks.find(x=>x.id===r.clone.id)) this.tasks.push(r.clone);
            this.pickDay(target);
        },
        async exportPdf(){
            if (this.exporting) return;
            this.exporting = true;
            try {
                const node = document.getElementById('qtRoot');
                const d = new Date(this.currentISO);
                const name = 'note-nhanh-' + this.currentISO + '.pdf';
                await html2pdf().set({
                    margin: 10,
                    filename: name,
                    image: { type: 'jpeg', quality: 0.95 },
                    html2canvas: { scale: 2, backgroundColor: '#ffffff', useCORS: true },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' },
                    pagebreak: { mode: ['css', 'legacy'] },
                }).from(node).save();
            } catch (e) {
                alert('Không xuất được PDF: ' + e.message);
            } finally {
                this.exporting = false;
            }
        },
        async removeTask(t){
            if (!confirm('Xóa công việc này?')) return;
            await this.api(URLS.base+'/'+t.id, { method:'DELETE' });
            this.tasks = this.tasks.filter(x=>x.id!==t.id)
                                   .map(x => x.carried_to_id === t.id ? { ...x, carried_to_id: null } : x);
        },
    };
}
</script>
</x-layouts.app>
