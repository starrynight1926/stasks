// Mock state cho demo. Lưu trong localStorage để các trang share nhau.
const STATUS = {
  todo:    { key:'todo',    label:'Chưa làm',       cls:'gray' },
  doing:   { key:'doing',   label:'Đang thực hiện', cls:'blue' },
  done:    { key:'done',    label:'Đã xong',        cls:'green' },
  partial: { key:'partial', label:'Xong một phần',  cls:'amber' },
  blocked: { key:'blocked', label:'Không thể xong', cls:'red' },
};

function todayISO(d=new Date()){ return d.toISOString().slice(0,10); }
function addDays(d, n){ const x=new Date(d); x.setDate(x.getDate()+n); return x; }
function fmtDate(d){
  return d.toLocaleDateString('vi-VN', { weekday:'long', day:'2-digit', month:'long', year:'numeric' });
}

function seed(){
  const t = new Date();
  const iso = (n)=>todayISO(addDays(t,n));
  return [
    { id:1, title:'Review PR #241 — module Billing', iso:iso(0),  status:'doing',   project:'twenty', priority:'high' },
    { id:2, title:'Viết tài liệu onboarding cho dev mới', iso:iso(0), status:'todo', project:'docs', priority:'med' },
    { id:3, title:'Fix bug: upload avatar >5MB bị crash', iso:iso(0), status:'done', project:'api', priority:'high' },
    { id:4, title:'Họp planning sprint 42', iso:iso(0), status:'done', project:'ops', priority:'low' },
    { id:5, title:'Chuẩn bị slide demo cho khách', iso:iso(0), status:'partial', project:'sales', priority:'high', note:'Đã làm phần context, còn phần pricing chưa xong vì chờ số từ finance' },
    { id:6, title:'Deploy staging bản mới', iso:iso(-1), status:'done', project:'ops' },
    { id:7, title:'Reply email đối tác A', iso:iso(-1), status:'blocked', project:'sales', note:'Đối tác không phản hồi, dời sang tuần sau' },
    { id:8, title:'Review spec feature export CSV', iso:iso(-1), status:'done', project:'docs' },
    { id:9, title:'Setup Postgres replica', iso:iso(-2), status:'done', project:'infra' },
    { id:10, title:'Dọn backlog cũ', iso:iso(-2), status:'partial', project:'ops', note:'Xong 10/15 ticket' },
    { id:11, title:'Nghiên cứu MCP tool cho internal bot', iso:iso(1), status:'todo', project:'r&d' },
    { id:12, title:'1-1 với manager', iso:iso(1), status:'todo', project:'ops' },
  ];
}

const KEY = 'sui_tasks_v1';
function loadTasks(){
  const raw = localStorage.getItem(KEY);
  if (!raw){ const s = seed(); localStorage.setItem(KEY, JSON.stringify(s)); return s; }
  try{ return JSON.parse(raw); } catch{ return seed(); }
}
function saveTasks(ts){ localStorage.setItem(KEY, JSON.stringify(ts)); }
function updateTask(id, patch){
  const ts = loadTasks();
  const i = ts.findIndex(t=>t.id===id);
  if (i<0) return;
  ts[i] = {...ts[i], ...patch};
  saveTasks(ts);
  return ts[i];
}
function resetTasks(){ localStorage.removeItem(KEY); }

// Render helpers
function statusTag(status){
  const s = STATUS[status] || STATUS.todo;
  return `<span class="tag pill ${s.cls}">● ${s.label}</span>`;
}

// Date strip (7 days around a center date)
function renderDateStrip(container, centerISO, onPick){
  const center = new Date(centerISO);
  const start = addDays(center, -3);
  const todayStr = todayISO();
  const dowShort = ['CN','T2','T3','T4','T5','T6','T7'];
  const tasks = loadTasks();
  const html = [];
  for (let i=0;i<7;i++){
    const d = addDays(start, i);
    const iso = todayISO(d);
    const has = tasks.some(t=>t.iso===iso);
    const cls = ['day'];
    if (iso===centerISO) cls.push('active');
    else if (iso===todayStr) cls.push('today');
    html.push(`<div class="${cls.join(' ')}" data-iso="${iso}">
      <div class="dow">${dowShort[d.getDay()]}</div>
      <div class="num">${d.getDate()}</div>
      ${has?'<div class="dot"></div>':''}
    </div>`);
  }
  container.innerHTML = html.join('');
  container.querySelectorAll('.day').forEach(el=>{
    el.addEventListener('click', ()=> onPick(el.dataset.iso));
  });
}
