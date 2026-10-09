<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Note nhanh việc — ProjectFlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('quick-tasks/styles.css') }}">
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="sb-head">
      <div class="sb-logo">S</div>
      <div>
        <div class="sb-ws">Note nhanh việc</div>
        <div style="font-size:.7rem; color:var(--gray9)">{{ session('user_name', 'User') }}</div>
      </div>
    </div>
    <div class="sb-section">
      <a class="sb-item active" href="{{ route('quick-tasks') }}">📋 Hôm nay</a>
      <a class="sb-item" href="{{ route('tasks.board') }}">🗂️ Tasks (hệ thống)</a>
      <a class="sb-item" href="{{ route('dashboard') }}">📊 Dashboard</a>
    </div>
    <div style="margin-top:auto; padding:12px">
      <form action="{{ route('logout') }}" method="POST" style="margin:0">
        @csrf
        <button type="submit" class="sb-item" style="width:100%; text-align:left; border:none; background:transparent; cursor:pointer; font:inherit">🚪 Đăng xuất</button>
      </form>
    </div>
  </aside>

  <main class="main">
    <header class="topbar">
      <h1 id="pageTitle">Hôm nay</h1>
    </header>

    <div class="workspace">
      <div class="content">
        <div class="date-head">
          <div>
            <span class="big" id="bigDate">—</span>
            <span class="sub" id="monthLabel">—</span>
          </div>
          <div class="right">
            <button class="btn btn-ghost btn-sm" onclick="pickDay(todayISO())">Hôm nay</button>
          </div>
        </div>

        <div class="stats" id="stats"></div>
        <div id="list"></div>

        <div class="quick-add">
          <div class="plus">+</div>
          <input id="quickAdd" type="text" placeholder="Thêm công việc cho ngày này — Enter để lưu">
          <input class="proj" id="quickProj" type="text" placeholder="dự án" list="projList" style="width:100px">
          <datalist id="projList"></datalist>
          <span class="hint">⏎ Enter</span>
        </div>
      </div>

      <aside class="cal-panel">
        <div class="cal-head">
          <div class="m" id="calMonth">—</div>
          <div class="nav">
            <button class="btn btn-ghost btn-sm btn-icon" onclick="shiftMonth(-1)" title="Tháng trước">‹</button>
            <button class="btn btn-ghost btn-sm btn-icon" onclick="shiftMonth(1)"  title="Tháng sau">›</button>
          </div>
        </div>
        <div class="cal-dow">
          <div>CN</div><div>T2</div><div>T3</div><div>T4</div><div>T5</div><div>T6</div><div>T7</div>
        </div>
        <div class="cal-grid" id="calGrid"></div>

        <div class="cal-legend">
          <div class="row"><span class="sw" style="background:#4ca374"></span> Đã xong</div>
          <div class="row"><span class="sw" style="background:var(--accent9)"></span> Đang thực hiện</div>
          <div class="row"><span class="sw" style="background:#c25700"></span> Xong một phần</div>
          <div class="row"><span class="sw" style="background:#be3b38"></span> Không thể xong</div>
          <div class="row"><span class="sw" style="background:var(--gray7)"></span> Chưa làm</div>
        </div>
      </aside>
    </div>
  </main>
</div>

<script>
  const STATUS = {
    todo:    { label:'Chưa làm',       cls:'gray' },
    doing:   { label:'Đang thực hiện', cls:'blue' },
    done:    { label:'Đã xong',        cls:'green' },
    partial: { label:'Xong một phần',  cls:'amber' },
    blocked: { label:'Không thể xong', cls:'red' },
  };
  const CSRF = document.querySelector('meta[name="csrf-token"]').content;
  const URLS = {
    list:   '{{ route('quick-notes.list') }}',
    store:  '{{ route('quick-notes.store') }}',
    update: id => `/api/quick-notes/${id}`,
    destroy:id => `/api/quick-notes/${id}`,
  };

  function todayISO(d=new Date()){
    const z = new Date(d.getTime() - d.getTimezoneOffset()*60000);
    return z.toISOString().slice(0,10);
  }
  function addDays(d, n){ const x=new Date(d); x.setDate(x.getDate()+n); return x; }
  function statusTag(status){
    const s = STATUS[status] || STATUS.todo;
    return `<span class="tag pill ${s.cls}">● ${s.label}</span>`;
  }

  let TASKS = [];

  async function api(url, opts={}){
    const r = await fetch(url, {
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json', 'Content-Type':'application/json' },
      ...opts,
    });
    if (!r.ok) throw new Error('API '+r.status);
    return r.status===204 ? null : r.json();
  }
  async function loadAll(){ TASKS = await api(URLS.list); }
  async function createTask(payload){ const t = await api(URLS.store, {method:'POST', body:JSON.stringify(payload)}); TASKS.push(t); return t; }
  async function patchTask(id, payload){ const t = await api(URLS.update(id), {method:'PATCH', body:JSON.stringify(payload)}); const i=TASKS.findIndex(x=>x.id===id); if(i>=0) TASKS[i]=t; return t; }
  async function deleteTask(id){ await api(URLS.destroy(id), {method:'DELETE'}); TASKS = TASKS.filter(x=>x.id!==id); }

  let currentISO = todayISO();
  let calMonth = new Date(currentISO); calMonth.setDate(1);

  function render(){
    const d = new Date(currentISO);
    const dow = d.toLocaleDateString('vi-VN',{weekday:'long'});
    const dd = String(d.getDate()).padStart(2,'0');
    const mm = String(d.getMonth()+1).padStart(2,'0');
    document.getElementById('bigDate').textContent =
      `${dow.charAt(0).toUpperCase()+dow.slice(1)}, ${dd}/${mm}`;
    document.getElementById('monthLabel').textContent =
      d.toLocaleDateString('vi-VN',{month:'long', year:'numeric'});

    renderCalendar();
    const tasks = TASKS.filter(t=>t.iso===currentISO);
    renderStats(tasks);
    renderList(tasks);
    refreshProjects();
  }

  function pickDay(iso){
    currentISO = iso;
    const d = new Date(iso);
    calMonth = new Date(d.getFullYear(), d.getMonth(), 1);
    render();
  }
  function shiftMonth(dir){
    calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth()+dir, 1);
    renderCalendar();
    document.getElementById('calMonth').textContent =
      calMonth.toLocaleDateString('vi-VN',{month:'long', year:'numeric'});
  }

  function renderCalendar(){
    const first = new Date(calMonth);
    const start = new Date(first);
    start.setDate(1 - first.getDay());
    const todayStr = todayISO();
    const byDay = {};
    TASKS.forEach(t=>{ (byDay[t.iso]=byDay[t.iso]||[]).push(t.status); });

    const cells = [];
    for (let i=0;i<42;i++){
      const d = addDays(start, i);
      const iso = todayISO(d);
      const outside = d.getMonth() !== first.getMonth();
      const classes = ['cal-cell'];
      if (outside) classes.push('outside');
      if (iso===todayStr) classes.push('today');
      if (iso===currentISO) classes.push('active');

      const list = byDay[iso] || [];
      const uniq = [];
      ['done','doing','partial','blocked','todo'].forEach(s=>{ if (list.includes(s)) uniq.push(s); });
      const dots = uniq.slice(0,3).map(s=>`<span class="d-${s}"></span>`).join('');

      cells.push(`<div class="${classes.join(' ')}" data-iso="${iso}"><div class="n">${d.getDate()}</div><div class="cal-dots">${dots}</div></div>`);
    }
    const grid = document.getElementById('calGrid');
    grid.innerHTML = cells.join('');
    document.getElementById('calMonth').textContent =
      first.toLocaleDateString('vi-VN',{month:'long', year:'numeric'});
    grid.querySelectorAll('.cal-cell').forEach(el=>{ el.onclick = ()=> pickDay(el.dataset.iso); });
  }

  function refreshProjects(){
    const projs = [...new Set(TASKS.map(t=>t.project).filter(Boolean))];
    document.getElementById('projList').innerHTML = projs.map(p=>`<option value="${p}">`).join('');
  }

  document.getElementById('quickAdd').addEventListener('keydown', async (e)=>{
    if (e.key!=='Enter') return;
    const title = e.target.value.trim();
    if (!title) return;
    const proj = document.getElementById('quickProj').value.trim() || null;
    try {
      await createTask({ title, iso: currentISO, status:'todo', project: proj });
      e.target.value='';
      render();
    } catch(err){ alert('Lỗi lưu: '+err.message); }
  });

  function renderStats(tasks){
    const count = s => tasks.filter(t=>t.status===s).length;
    document.getElementById('stats').innerHTML = `
      <div class="stat"><div class="k">Tổng</div><div class="v">${tasks.length}</div></div>
      <div class="stat ok"><div class="k">Đã xong</div><div class="v">${count('done')}</div></div>
      <div class="stat doing"><div class="k">Đang làm</div><div class="v">${count('doing')}</div></div>
      <div class="stat warn"><div class="k">Một phần</div><div class="v">${count('partial')}</div></div>
      <div class="stat bad"><div class="k">Không xong</div><div class="v">${count('blocked')}</div></div>
    `;
  }

  function renderList(tasks){
    const list = document.getElementById('list');
    if (!tasks.length){ list.innerHTML = '<div class="empty">Không có công việc nào vào ngày này.</div>'; return; }
    list.innerHTML = '<div class="task-list">' + tasks.map(renderCard).join('') + '</div>';
    bindCards();
  }

  function esc(s){ return String(s??'').replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
  function renderCard(t){
    const isDone = t.status==='done';
    return `
      <div class="task-card ${isDone?'done':''}" data-id="${t.id}">
        <div class="task-check ${isDone?'checked':''}" data-action="toggle"></div>
        <div class="task-body">
          <h3 class="task-title">${esc(t.title)}</h3>
          <div class="task-meta">
            ${statusTag(t.status)}
            <span>· ${esc(t.project||'—')}</span>
            ${t.priority?`<span>· ưu tiên ${esc(t.priority)}</span>`:''}
          </div>
          ${t.note?`<div class="task-note">📝 ${esc(t.note)}</div>`:''}
        </div>
        <div class="task-actions">
          <div class="status-wrap">
            <button class="btn btn-secondary btn-sm" data-action="status">Trạng thái ▾</button>
            <div class="status-menu">
              <div class="status-item todo"    data-s="todo">   <span class="sw"></span> Chưa làm</div>
              <div class="status-item doing"   data-s="doing">  <span class="sw"></span> Đang thực hiện</div>
              <div class="status-item done"    data-s="done">   <span class="sw"></span> Đã xong</div>
              <div class="status-item partial" data-s="partial"><span class="sw"></span> Xong một phần…</div>
              <div class="status-item blocked" data-s="blocked"><span class="sw"></span> Không thể xong…</div>
            </div>
          </div>
          <button class="btn btn-ghost btn-sm" data-action="delete" title="Xóa">✕</button>
        </div>
      </div>
    `;
  }

  function bindCards(){
    document.querySelectorAll('.task-card').forEach(card=>{
      const id = +card.dataset.id;
      card.querySelector('[data-action="toggle"]').onclick = async ()=>{
        const t = TASKS.find(x=>x.id===id);
        const next = t.status==='done' ? 'todo' : 'done';
        await patchTask(id,{status:next, note: next==='done'?null:t.note});
        render();
      };
      card.querySelector('[data-action="delete"]').onclick = async ()=>{
        if(!confirm('Xóa công việc này?')) return;
        await deleteTask(id);
        render();
      };
      const menu = card.querySelector('.status-menu');
      card.querySelector('[data-action="status"]').onclick = (e)=>{
        e.stopPropagation();
        document.querySelectorAll('.status-menu.open').forEach(m=>m!==menu && m.classList.remove('open'));
        menu.classList.toggle('open');
      };
      menu.querySelectorAll('.status-item').forEach(it=>{
        it.onclick = async ()=>{
          const s = it.dataset.s;
          let note = TASKS.find(x=>x.id===id).note;
          if (s==='partial' || s==='blocked'){
            const q = s==='partial' ? 'Phần nào đã xong, phần nào còn lại?' : 'Lý do không thể xong là gì?';
            const v = prompt(q, note||'');
            if (v===null) return;
            note = v.trim() || null;
          } else { note = null; }
          await patchTask(id,{status:s, note});
          render();
        };
      });
    });
    document.addEventListener('click', ()=>{
      document.querySelectorAll('.status-menu.open').forEach(m=>m.classList.remove('open'));
    }, {once:true});
  }

  (async ()=>{
    try { await loadAll(); } catch(e){ alert('Không tải được dữ liệu: '+e.message); }
    render();
  })();
</script>
</body>
</html>
