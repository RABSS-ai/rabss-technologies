/**
 * RABSS OS — Super Admin BOS Client Script
 * CEO / Founder: Subash Sitaula
 */

document.addEventListener('DOMContentLoaded', () => {
  initNavigation();
  initTimezoneClocks();
  initCommandPalette();
  initAiAssistant();
  initLogout();

  // Parse view from URL queries to handle multi-page navigation
  const urlParams = new URLSearchParams(window.location.search);
  const targetView = urlParams.get('view');
  if (targetView) {
    switchView(targetView);
  } else {
    loadDashboard();
  }

  loadInquiries();
  loadLeads();
  loadProjects();
  loadInvoices();
  loadAuditLogs();
  initCreationModal();
});

/* 1. View Navigation Switcher */
function switchView(viewName) {
  if (viewName === 'inbox') {
    window.location.href = 'inquiry_box.php';
    return;
  }

  document.querySelectorAll('.view-panel').forEach(el => el.classList.add('hidden'));
  document.querySelectorAll('.nav-tab-btn').forEach(btn => btn.classList.remove('active'));

  const targetPanel = document.getElementById(`view-${viewName}`);
  if (targetPanel) targetPanel.classList.remove('hidden');

  const activeBtn = document.querySelector(`.nav-tab-btn[data-view="${viewName}"]`);
  if (activeBtn) activeBtn.classList.add('active');

  const cmdModal = document.getElementById('cmd-palette-modal');
  if (cmdModal) cmdModal.classList.add('hidden');

  // Auto-refresh views when switched to ensure real-time accuracy
  if (viewName === 'inbox') {
    loadInquiries();
  } else if (viewName === 'dashboard') {
    loadDashboard();
  } else if (viewName === 'leads') {
    loadLeads();
  } else if (viewName === 'projects') {
    loadProjects();
  } else if (viewName === 'invoices') {
    loadInvoices();
  } else if (viewName === 'audit') {
    loadAuditLogs();
  }
}

function initNavigation() {
  document.querySelectorAll('.nav-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const view = btn.getAttribute('data-view');
      switchView(view);
    });
  });

  const quickBtn = document.getElementById('quick-create-btn');
  const quickMenu = document.getElementById('quick-create-menu');
  if (quickBtn && quickMenu) {
    quickBtn.addEventListener('click', () => quickMenu.classList.toggle('hidden'));
  }
}

/* 2. Global Market Timezone Clocks */
function initTimezoneClocks() {
  function update() {
    const opts = { hour: '2-digit', minute: '2-digit', hour12: false };
    document.getElementById('clock-ny').textContent = new Intl.DateTimeFormat('en-US', { ...opts, timeZone: 'America/New_York' }).format(new Date());
    document.getElementById('clock-tor').textContent = new Intl.DateTimeFormat('en-US', { ...opts, timeZone: 'America/Toronto' }).format(new Date());
    document.getElementById('clock-dxb').textContent = new Intl.DateTimeFormat('en-US', { ...opts, timeZone: 'Asia/Dubai' }).format(new Date());
    document.getElementById('clock-doh').textContent = new Intl.DateTimeFormat('en-US', { ...opts, timeZone: 'Asia/Qatar' }).format(new Date());
  }
  update();
  setInterval(update, 10000);
}

/* 3. Dashboard Telemetry Loader */
async function loadDashboard() {
  try {
    const res = await fetch('api/index.php?action=get_dashboard');
    const data = await res.json();
    if (data.status === 'success') {
      const m = data.metrics;
      document.getElementById('stat-revenue').textContent = `$${m.revenue.toLocaleString()}`;
      document.getElementById('stat-outstanding').textContent = `$${m.outstanding.toLocaleString()}`;
      document.getElementById('stat-expenses').textContent = `$${m.expenses.toLocaleString()}`;
      document.getElementById('stat-net-profit').textContent = `$${m.net_profit.toLocaleString()}`;
      
      const badge = document.getElementById('badge-inquiry-count');
      if (badge) {
        badge.textContent = m.new_leads;
      }
    }
    loadRecentInquiries();
    loadProjectsSummary();
  } catch (e) {
    console.error('Failed to load dashboard metrics:', e);
  }
}

async function loadRecentInquiries() {
  const container = document.getElementById('dashboard-inquiries-list');
  if (!container) return;
  try {
    const res = await fetch('api/index.php?action=get_inquiries');
    const data = await res.json();
    if (data.status === 'success' && data.inquiries) {
      const inquiries = data.inquiries.slice(0, 5);
      if (inquiries.length === 0) {
        container.innerHTML = `<p class="text-xs text-slate-500 font-mono italic py-4 text-center">No active inquiries in queue.</p>`;
        return;
      }
      let html = '';
      inquiries.forEach(inq => {
        const displayBudget = inq.budget || 'Flexible / TBD';
        html += `
          <div class="p-3.5 rounded-xl bg-dark-850 border border-white/5 hover:border-brand-500/30 transition-all flex items-center justify-between text-xs">
            <div class="space-y-1">
              <div class="flex items-center gap-2">
                <span class="font-bold text-white">${inq.name}</span>
                <span class="px-1.5 py-0.2 rounded bg-dark-800 text-[10px] text-brand-accent font-mono">${inq.country}</span>
              </div>
              <p class="text-slate-400">${inq.company || 'Direct Founder'} — <span class="font-mono text-slate-500 text-[11px]">${inq.project_type || 'Custom Software'}</span>${inq.whatsapp ? ` — <span class="font-mono text-emerald-400 text-[11px]">💬 ${inq.whatsapp}</span>` : ''}</p>
              ${inq.description ? `<p class="text-slate-300 italic text-[11px] mt-1.5 border-l-2 border-brand-500/40 pl-2.5 py-1 bg-white/5 rounded-r-lg max-w-lg whitespace-pre-wrap">${inq.description}</p>` : ''}
            </div>
            <div class="text-right space-y-1">
              <span class="text-emerald-400 font-bold font-mono block">${displayBudget}</span>
              <a href="inquiry_box.php" class="px-2 py-1 rounded bg-brand-600 hover:bg-brand-500 text-white text-[10px] font-bold inline-block">Manage</a>
            </div>
          </div>
        `;
      });
      container.innerHTML = html;
    }
  } catch (e) {
    console.error('Failed to load recent inquiries:', e);
  }
}

async function loadProjectsSummary() {
  const container = document.getElementById('dashboard-projects-summary');
  if (!container) return;
  try {
    const res = await fetch('api/index.php?action=get_projects');
    const data = await res.json();
    if (data.status === 'success' && data.projects) {
      const projects = data.projects.slice(0, 4);
      if (projects.length === 0) {
        container.innerHTML = `<p class="text-xs text-slate-500 font-mono italic py-4 text-center">No active projects logged.</p>`;
        return;
      }
      let html = '';
      projects.forEach(p => {
        html += `
          <div class="space-y-1.5">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-white">${p.name}</span>
              <span class="font-mono text-slate-400">${p.progress}%</span>
            </div>
            <div class="w-full bg-dark-800 rounded-full h-1.5">
              <div class="bg-gradient-to-r from-brand-600 to-brand-accent h-1.5 rounded-full" style="width: ${p.progress}%"></div>
            </div>
            <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono">
              <span>Type: ${p.project_type}</span>
              <span>Deadline: ${p.deadline}</span>
            </div>
          </div>
        `;
      });
      container.innerHTML = html;
    }
  } catch (e) {
    console.error('Failed to load dashboard metrics:', e);
  }
}

/* 3.1 Session Logout Handler */
function initLogout() {
  const logoutBtn = document.getElementById('btn-logout');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      if (confirm('Are you sure you want to log out of RABSS OS?')) {
        try {
          const res = await fetch('api/index.php?action=logout');
          const data = await res.json();
          if (data.status === 'success') {
            window.location.href = 'login.php';
          }
        } catch (e) {
          console.error('Logout failed:', e);
        }
      }
    });
  }
}

function initCreationModal() {
  const modal = document.getElementById('creation-modal');
  const closeBtn = document.getElementById('close-creation-modal');
  if (closeBtn && modal) {
    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.classList.add('hidden');
    });
  }

  const form = document.getElementById('creation-form');
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const type = form.getAttribute('data-type');
      const formData = new FormData(form);
      const payload = {};
      formData.forEach((value, key) => { payload[key] = value; });

      try {
        const res = await fetch(`api/index.php?action=create_${type}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.status === 'success') {
          modal.classList.add('hidden');
          loadDashboard();
          loadInquiries();
          loadLeads();
          loadProjects();
          loadInvoices();
          loadAuditLogs();
          alert(`${type.toUpperCase()} created successfully!`);
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        console.error(err);
        alert('Server connection failed.');
      }
    });
  }
}

function openCreateModal(type) {
  const modal = document.getElementById('creation-modal');
  const form = document.getElementById('creation-form');
  const title = document.getElementById('modal-title');
  const subtitle = document.getElementById('modal-subtitle');
  const icon = document.getElementById('modal-icon');

  if (!modal || !form) return;

  form.setAttribute('data-type', type);
  modal.classList.remove('hidden');

  let formFields = '';
  if (type === 'inquiry') {
    icon.textContent = '📩';
    title.textContent = 'Create New Inquiry';
    subtitle.textContent = 'Manually add a website or client inquiry record';
    formFields = `
      <div>
        <label class="block text-slate-300 font-mono mb-1">Inquirer Name *</label>
        <input type="text" name="name" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Email *</label>
          <input type="email" name="email" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Company</label>
          <input type="text" name="company" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Country</label>
          <select name="country" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="USA">🇺🇸 USA</option>
            <option value="Canada">🇨🇦 Canada</option>
            <option value="UAE">🇦🇪 UAE</option>
            <option value="Qatar">🇶🇦 Qatar</option>
            <option value="Other">🌍 Other</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Project Type</label>
          <select name="project_type" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="AI Agent / LLM Automation">AI Agent / LLM Automation</option>
            <option value="MVP Fast-Track (New Idea)">MVP Fast-Track (New Idea)</option>
            <option value="SaaS Platform Development">SaaS Platform Development</option>
            <option value="Custom Business Software">Custom Business Software</option>
            <option value="Web Development">Web Development</option>
            <option value="Mobile App">Mobile App</option>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-slate-300 font-mono mb-1">Target Budget</label>
        <select name="budget" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500 font-mono">
          <option value="$1,500 – $3,000 USD">$1,500 – $3,000 USD</option>
          <option value="$3,000 – $5,000 USD">$3,000 – $5,000 USD</option>
          <option value="$5,000 – $10,000 USD">$5,000 – $10,000 USD</option>
          <option value="$10,000+ USD">$10,000+ USD</option>
          <option value="Flexible">Flexible / Discuss</option>
        </select>
      </div>
      <div>
        <label class="block text-slate-300 font-mono mb-1">Description / Goals *</label>
        <textarea name="description" required rows="3" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500"></textarea>
      </div>
    `;
  } else if (type === 'lead') {
    icon.textContent = '🎯';
    title.textContent = 'Create New Lead';
    subtitle.textContent = 'Capture sales discovery and partner pipeline prospects';
    formFields = `
      <div>
        <label class="block text-slate-300 font-mono mb-1">Lead Name *</label>
        <input type="text" name="name" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Company</label>
          <input type="text" name="company" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Country</label>
          <select name="country" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="USA">🇺🇸 USA</option>
            <option value="Canada">🇨🇦 Canada</option>
            <option value="UAE">🇦🇪 UAE</option>
            <option value="Qatar">🇶🇦 Qatar</option>
            <option value="Other">🌍 Other</option>
          </select>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Email</label>
          <input type="email" name="email" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Phone / WhatsApp</label>
          <input type="text" name="phone" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500" placeholder="+1...">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Service Type</label>
          <select name="service" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="AI Development">AI Development</option>
            <option value="Custom Software">Custom Software</option>
            <option value="SaaS Platform">SaaS Platform</option>
            <option value="MVP Fast-Track">MVP Fast-Track</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Lead Source</label>
          <select name="source" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="Website Form">Website Form</option>
            <option value="LinkedIn">LinkedIn</option>
            <option value="Referral">Referral</option>
            <option value="Cold Outreach">Cold Outreach</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Estimated Value</label>
          <input type="number" name="estimated_value" value="2500" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500 font-mono">
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Currency</label>
          <select name="currency" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="USD">USD ($)</option>
            <option value="CAD">CAD (C$)</option>
            <option value="AED">AED (AED)</option>
            <option value="QAR">QAR (QAR)</option>
          </select>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Next Follow-up</label>
          <input type="date" name="next_followup" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500 font-mono">
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Assigned To</label>
          <input type="text" name="assigned_to" value="Subash Sitaula" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">WhatsApp Number</label>
          <input type="text" name="whatsapp" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500" placeholder="+1...">
        </div>
      </div>
      <div>
        <label class="block text-slate-300 font-mono mb-1">Notes</label>
        <textarea name="notes" rows="2" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500"></textarea>
      </div>
    `;
  } else if (type === 'project') {
    icon.textContent = '🚀';
    title.textContent = 'Create New Project';
    subtitle.textContent = 'Spawn an active workspace and sprint workflow client contract';
    formFields = `
      <div>
        <label class="block text-slate-300 font-mono mb-1">Project Name *</label>
        <input type="text" name="name" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Client ID Reference</label>
          <select name="client_id" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="1">Marcus Vance (Vance Capital)</option>
            <option value="2">Fatima Al-Zahra (Dubai AI Labs)</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Project Type</label>
          <select name="project_type" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="AI / SaaS">AI / SaaS Platform</option>
            <option value="Custom Software">Custom Software</option>
            <option value="E-commerce">E-commerce Portal</option>
            <option value="Mobile App">Mobile App</option>
          </select>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Budget</label>
          <input type="number" name="budget" value="5000" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500 font-mono">
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Currency</label>
          <select name="currency" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="USD">USD ($)</option>
            <option value="CAD">CAD (C$)</option>
            <option value="AED">AED (AED)</option>
            <option value="QAR">QAR (QAR)</option>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-slate-300 font-mono mb-1">Deadline Date</label>
        <input type="date" name="deadline" value="${new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]}" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500 font-mono">
      </div>
      <div>
        <label class="block text-slate-300 font-mono mb-1">Description</label>
        <textarea name="description" rows="2" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500"></textarea>
      </div>
    `;
  } else if (type === 'invoice') {
    icon.textContent = '💳';
    title.textContent = 'Issue Client Invoice';
    subtitle.textContent = 'Generate formal payment receivables with tracking reference';
    formFields = `
      <div>
        <label class="block text-slate-300 font-mono mb-1">Recipient Client</label>
        <select name="client_id" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
          <option value="1">Marcus Vance (Vance Capital)</option>
          <option value="2">Fatima Al-Zahra (Dubai AI Labs)</option>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Invoice Total Amount *</label>
          <input type="number" name="amount" value="2500" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500 font-mono">
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Currency</label>
          <select name="currency" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="USD">USD ($)</option>
            <option value="CAD">CAD (C$)</option>
            <option value="AED">AED (AED)</option>
            <option value="QAR">QAR (QAR)</option>
          </select>
        </div>
      </div>
    `;
  } else if (type === 'task') {
    icon.textContent = '📋';
    title.textContent = 'Create Task Milestone';
    subtitle.textContent = 'Add operational delivery micro-tasks inside active sprints';
    formFields = `
      <div>
        <label class="block text-slate-300 font-mono mb-1">Task Title *</label>
        <input type="text" name="title" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Associated Project</label>
          <select name="project_id" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="1">AI Business OS Prototype</option>
            <option value="2">Multi-Storefront Headless Portal</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Assignee</label>
          <input type="text" name="assignee" value="Subash Sitaula" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-slate-300 font-mono mb-1">Priority</label>
          <select name="priority" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="Low">Low</option>
            <option value="Medium" selected>Medium</option>
            <option value="High">High</option>
            <option value="Critical">Critical</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-300 font-mono mb-1">Sprint Status</label>
          <select name="status" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-brand-500">
            <option value="To Do">To Do</option>
            <option value="In Progress">In Progress</option>
            <option value="Review">Review</option>
            <option value="Completed">Completed</option>
          </select>
        </div>
      </div>
    `;
  }

  formFields += `
    <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-brand-600/20 text-center block mt-6">
      Confirm & Deploy Record
    </button>
  `;

  form.innerHTML = formFields;
}

window.openCreateModal = openCreateModal;
window.switchView = switchView;

/* 4. Leads & CRM Kanban */
async function loadLeads() {
  try {
    const res = await fetch('api/index.php?action=get_leads');
    const data = await res.json();
    if (data.status === 'success') {
      const stageMap = {
        'New Inquiry': document.getElementById('leads-col-new'),
        'Contacted': document.getElementById('leads-col-contacted'),
        'Discovery': document.getElementById('leads-col-discovery'),
        'Proposal Sent': document.getElementById('leads-col-proposal'),
        'Negotiation': document.getElementById('leads-col-negotiation'),
        'Won': document.getElementById('leads-col-won'),
        'Lost': document.getElementById('leads-col-lost')
      };

      // Clear existing leads from all columns
      Object.values(stageMap).forEach(el => { if (el) el.innerHTML = ''; });

      // Add drag and drop functionality to columns
      Object.keys(stageMap).forEach(stageName => {
        const column = stageMap[stageName];
        if (column) {
          column.dataset.stage = stageName; // Set data-stage attribute for easy identification
          column.addEventListener('dragover', handleDragOver);
          column.addEventListener('dragleave', handleDragLeave);
          column.addEventListener('drop', handleDrop);
        }
      });

      data.leads.forEach(lead => {
        const targetCol = stageMap[lead.stage] || stageMap['New Inquiry'];
        if (targetCol) {
          const estVal = lead.estimated_value ? `${lead.currency || 'USD'} ${lead.estimated_value.toLocaleString()}` : 'Flexible / TBD';
          const card = document.createElement('div');
          card.className = 'kanban-card p-3.5 rounded-xl bg-dark-850 border border-white/10 hover:border-brand-500/40 text-xs space-y-2';
          card.draggable = true;
          card.dataset.leadId = lead.id; // Store lead ID
          card.dataset.leadStage = lead.stage; // Store current stage
          card.addEventListener('dragstart', handleDragStart);
          card.addEventListener('dragend', handleDragEnd);
          card.innerHTML = `
            <div class="flex items-center justify-between">
              <span class="font-bold text-white">${lead.name}</span>
              <div class="flex items-center gap-1.5">
                <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-dark-800 text-brand-accent">${lead.country}</span>
                <button class="text-slate-500 hover:text-rose-500 transition-colors text-[10px] font-bold p-0.5" onclick="deleteLead(event, ${lead.id})" title="Delete Lead">✕</button>
              </div>
            </div>
            <p class="text-[11px] text-slate-400">${lead.company || 'Direct Founder'}</p>
            ${lead.phone ? `<p class="text-[10px] text-slate-400">📞 ${lead.phone}</p>` : ''}
            ${lead.next_followup ? `<p class="text-[10px] text-amber-400 font-mono">📅 Follow-up: ${lead.next_followup}</p>` : ''}
            <div class="flex items-center justify-between text-[11px] font-mono pt-1 border-t border-white/5">
              <span class="text-emerald-400 font-bold">${estVal}</span>
              <span class="text-slate-500">${lead.service}</span>
            </div>
          `;
          targetCol.appendChild(card);
        }
      });
    }
  } catch (e) {
    console.error('Failed to load leads:', e);
  }
}

function handleDragStart(e) {
  draggedLeadId = e.target.dataset.leadId;
  e.dataTransfer.setData('text/plain', draggedLeadId);
  e.target.classList.add('opacity-50'); // Visual feedback for dragging
}

function handleDragOver(e) {
  e.preventDefault(); // Allow dropping
  e.currentTarget.classList.add('bg-dark-800'); // Visual feedback for drop target
}

function handleDragLeave(e) {
  e.currentTarget.classList.remove('bg-dark-800');
}

async function handleDrop(e) {
  e.preventDefault();
  e.currentTarget.classList.remove('bg-dark-800'); // Remove visual feedback

  const leadId = e.dataTransfer.getData('text/plain');
  const newStage = e.currentTarget.dataset.stage;

  if (leadId && newStage) {
    const draggedCard = document.querySelector(`[data-lead-id="${leadId}"]`);
    if (draggedCard && draggedCard.dataset.leadStage !== newStage) { // Only update if stage changed
      // Optimistic UI update
      e.currentTarget.appendChild(draggedCard);
      draggedCard.dataset.leadStage = newStage; // Update the data attribute immediately

      await updateLeadStageInBackend(leadId, newStage);
    }
  }
}

function handleDragEnd(e) {
  e.target.classList.remove('opacity-50'); // Remove visual feedback
  draggedLeadId = null;
}

async function updateLeadStageInBackend(leadId, newStage) {
  try {
    const res = await fetch('api/index.php?action=update_lead_stage', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ lead_id: leadId, stage: newStage })
    });
    const data = await res.json();
    if (data.status === 'success') {
      console.log(`Lead ${leadId} moved to ${newStage}`);
      loadLeads(); // Reload leads to ensure consistency and update any other related UI elements
      loadDashboard(); // Dashboard might have lead counts
    } else {
      alert('Error updating lead stage: ' + data.message);
      loadLeads(); // Reload to revert to actual state if backend update failed
    }
  } catch (e) {
    console.error('Failed to update lead stage:', e);
    alert('Server connection failed while updating lead stage.');
    loadLeads(); // Reload to revert to actual state
  }
}

async function loadInquiries() {
  try {
    const res = await fetch('api/index.php?action=get_inquiries');
    const data = await res.json();
    const container = document.getElementById('inquiries-table-container');
    if (data.status === 'success' && container) {
      if (data.inquiries.length === 0) {
        container.innerHTML = `<p class="text-xs text-slate-500 font-mono italic py-8 text-center">No inquiries received yet.</p>`;
        return;
      }
      let html = `
        <table class="w-full text-left text-xs">
          <thead class="bg-dark-850 text-slate-400 font-mono border-b border-white/5">
            <tr>
              <th class="p-4">Name</th>
              <th class="p-4">Email</th>
              <th class="p-4">WhatsApp</th>
              <th class="p-4">Company</th>
              <th class="p-4">Country</th>
              <th class="p-4">Project Type</th>
              <th class="p-4">Budget</th>
              <th class="p-4">Source</th>
              <th class="p-4">Status</th>
              <th class="p-4">Overview</th>
              <th class="p-4">Date</th>
              <th class="p-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
      `;
      data.inquiries.forEach(inq => {
        const isNew = (inq.status || 'New') === 'New';
        const statusBadge = isNew 
          ? `<span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold text-[10px]">New</span>`
          : `<span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold text-[10px]">${inq.status}</span>`;
        const promoteBtn = isNew
          ? `<button class="px-2 py-1 bg-brand-600 hover:bg-brand-500 text-white rounded font-mono text-[10px] font-bold mr-1.5" onclick="promoteToLead(${inq.id})">Promote</button>`
          : `<span class="text-[10px] font-mono text-emerald-400 bg-emerald-950/40 border border-emerald-500/30 px-1.5 py-0.5 rounded font-bold mr-1.5">Promoted</span>`;

        html += `
          <tr class="hover:bg-dark-850/50 transition-colors">
            <td class="p-4 font-bold text-white">${inq.name}</td>
            <td class="p-4 text-slate-300 font-mono">${inq.email}</td>
            <td class="p-4 text-slate-300 font-mono">${inq.whatsapp || '—'}</td>
            <td class="p-4 text-slate-300">${inq.company || '—'}</td>
            <td class="p-4 font-mono text-brand-accent">${inq.country || '—'}</td>
            <td class="p-4 text-slate-300">${inq.project_type || '—'}</td>
            <td class="p-4 font-mono text-emerald-400">${inq.budget || '—'}</td>
            <td class="p-4 font-mono text-cyan-400">${inq.source || 'Website Form'}</td>
            <td class="p-4 font-mono">${statusBadge}</td>
            <td class="p-4 text-slate-400 max-w-xs truncate" title="${inq.description || ''}">${inq.description || '—'}</td>
            <td class="p-4 font-mono text-slate-500">${inq.created_at || 'Recently'}</td>
            <td class="p-4 text-right">
              ${promoteBtn}
              <button class="px-2 py-1 bg-rose-600 hover:bg-rose-500 text-white rounded font-mono text-[10px] font-bold" onclick="deleteInquiry(${inq.id})">Delete</button>
            </td>
          </tr>
        `;
      });
      html += `</tbody></table>`;
      container.innerHTML = html;
    } else if (container) {
      container.innerHTML = `
        <div class="p-6 bg-rose-950/20 border border-rose-500/30 rounded-2xl text-xs font-mono space-y-2 text-rose-300">
          <p class="font-bold">🚨 API Response Failure during Inquiry Retrieval</p>
          <p>Payload Status: ${data.status}</p>
          <p>Message: ${data.message || 'No descriptive error returned by endpoint.'}</p>
          <p class="text-slate-400">Review "admin/api/debug_log.txt" or "admin/api/php_error_log.txt" on the server for dynamic diagnostics.</p>
        </div>
      `;
    }
  } catch (e) {
    console.error('Failed to load inquiries:', e);
    const container = document.getElementById('inquiries-table-container');
    if (container) {
      container.innerHTML = `
        <div class="p-6 bg-rose-950/20 border border-rose-500/30 rounded-2xl text-xs font-mono space-y-2 text-rose-300">
          <p class="font-bold">🚨 Critical Client-Side Fetch Exception</p>
          <p>Error Message: ${e.message}</p>
          <p class="text-slate-400">Verify your local PHP development server is running and database file paths remain uncorrupted.</p>
        </div>
      `;
    }
  }
}

async function promoteToLead(id) {
  if (confirm('Are you sure you want to convert this inquiry into a pipeline lead?')) {
    try {
      const res = await fetch('api/index.php?action=promote_to_lead', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ inquiry_id: id })
      });
      const data = await res.json();
      if (data.status === 'success') {
        loadInquiries();
        loadDashboard();
        loadLeads();
      } else {
        alert('Error: ' + data.message);
      }
    } catch (e) {
      console.error('Failed to promote inquiry:', e);
      alert('Server connection failed.');
    }
  }
}
async function deleteInquiry(id) {
  if (confirm('Are you sure you want to delete this inquiry?')) {
    try {
      const res = await fetch('api/index.php?action=delete_inquiry', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const data = await res.json();
      if (data.status === 'success') {
        loadInquiries();
        loadDashboard();
      } else {
        alert('Error: ' + data.message);
      }
    } catch (e) {
      console.error('Failed to delete inquiry:', e);
      alert('Server connection failed.');
    }
  }
}

async function deleteLead(event, id) {
  if (event) event.stopPropagation();
  if (confirm('Are you sure you want to permanently delete this lead?')) {
    try {
      const res = await fetch('api/index.php?action=delete_lead', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const data = await res.json();
      if (data.status === 'success') {
        loadLeads();
        loadDashboard();
      } else {
        alert('Error: ' + data.message);
      }
    } catch (e) {
      console.error('Failed to delete lead:', e);
      alert('Server connection failed.');
    }
  }
}
window.deleteLead = deleteLead;

/* 5. Projects & Invoices Table Renderers */
async function loadProjects() {
  try {
    const res = await fetch('api/index.php?action=get_projects');
    const data = await res.json();
    const container = document.getElementById('projects-table-container');
    if (data.status === 'success' && container) {
      let html = `
        <table class="w-full text-left text-xs">
          <thead class="bg-dark-850 text-slate-400 font-mono border-b border-white/5">
            <tr>
              <th class="p-4">Project Name</th>
              <th class="p-4">Client</th>
              <th class="p-4">Budget</th>
              <th class="p-4">Progress</th>
              <th class="p-4">Status</th>
              <th class="p-4">Deadline</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
      `;
      data.projects.forEach(p => {
        html += `
          <tr class="hover:bg-dark-850/50 transition-colors">
            <td class="p-4 font-bold text-white">${p.name}</td>
            <td class="p-4 text-slate-300">${p.client_company || p.client_name}</td>
            <td class="p-4 font-mono text-emerald-400">${p.currency} ${p.budget?.toLocaleString()}</td>
            <td class="p-4">
              <div class="w-24 bg-dark-800 rounded-full h-1.5">
                <div class="bg-brand-500 h-1.5 rounded-full" style="width: ${p.progress}%"></div>
              </div>
            </td>
            <td class="p-4"><span class="px-2 py-0.5 rounded bg-brand-500/20 text-brand-accent font-mono text-[10px]">${p.status}</span></td>
            <td class="p-4 font-mono text-slate-400">${p.deadline}</td>
          </tr>
        `;
      });
      html += `</tbody></table>`;
      container.innerHTML = html;
    }
  } catch (e) {
    console.error('Failed to load projects:', e);
  }
}

async function loadInvoices() {
  try {
    const res = await fetch('api/index.php?action=get_invoices');
    const data = await res.json();
    const container = document.getElementById('invoices-table-container');
    if (data.status === 'success' && container) {
      let html = `
        <table class="w-full text-left text-xs">
          <thead class="bg-dark-850 text-slate-400 font-mono border-b border-white/5">
            <tr>
              <th class="p-4">Invoice #</th>
              <th class="p-4">Client</th>
              <th class="p-4">Amount</th>
              <th class="p-4">Issue Date</th>
              <th class="p-4">Due Date</th>
              <th class="p-4">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
      `;
      data.invoices.forEach(i => {
        const statusColor = i.status === 'Paid' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-300';
        html += `
          <tr class="hover:bg-dark-850/50 transition-colors">
            <td class="p-4 font-mono font-bold text-brand-accent">${i.invoice_number}</td>
            <td class="p-4 text-slate-300">${i.client_company || i.client_name}</td>
            <td class="p-4 font-mono text-white font-bold">${i.currency} ${i.total?.toLocaleString()}</td>
            <td class="p-4 font-mono text-slate-400">${i.issue_date}</td>
            <td class="p-4 font-mono text-slate-400">${i.due_date}</td>
            <td class="p-4"><span class="px-2 py-0.5 rounded ${statusColor} font-mono text-[10px] font-bold">${i.status}</span></td>
          </tr>
        `;
      });
      html += `</tbody></table>`;
      container.innerHTML = html;
    }
  } catch (e) {
    console.error('Failed to load invoices:', e);
  }
}

async function loadAuditLogs() {
  try {
    const res = await fetch('api/index.php?action=get_audit_logs');
    const data = await res.json();
    const container = document.getElementById('audit-table-container');
    if (data.status === 'success' && container) {
      let html = `
        <table class="w-full text-left text-xs">
          <thead class="bg-dark-850 text-slate-400 font-mono border-b border-white/5">
            <tr>
              <th class="p-4">Actor</th>
              <th class="p-4">Action Event</th>
              <th class="p-4">Entity</th>
              <th class="p-4">IP Address</th>
              <th class="p-4">Timestamp</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
      `;
      data.logs.forEach(l => {
        html += `
          <tr class="hover:bg-dark-850/50 transition-colors">
            <td class="p-4 font-bold text-white">${l.user_name}</td>
            <td class="p-4 text-slate-300">${l.action}</td>
            <td class="p-4 font-mono text-brand-accent text-[11px]">${l.entity}</td>
            <td class="p-4 font-mono text-slate-500 text-[11px]">${l.ip_address}</td>
            <td class="p-4 font-mono text-slate-400 text-[11px]">${l.created_at}</td>
          </tr>
        `;
      });
      html += `</tbody></table>`;
      container.innerHTML = html;
    }
  } catch (e) {
    console.error('Failed to load audit logs:', e);
  }
}

/* 6. Command Palette (Ctrl+K) */
function initCommandPalette() {
  const modal = document.getElementById('cmd-palette-modal');
  const trigger = document.getElementById('cmd-palette-trigger');

  window.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      modal.classList.toggle('hidden');
      if (!modal.classList.contains('hidden')) {
        document.getElementById('cmd-search-input').focus();
      }
    }
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
      modal.classList.add('hidden');
    }
  });

  if (trigger) {
    trigger.addEventListener('click', () => {
      modal.classList.remove('hidden');
      document.getElementById('cmd-search-input').focus();
    });
  }

  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.add('hidden');
  });
}

/* 7. AI Assistant Drawer & Form */
function initAiAssistant() {
  const btnToggle = document.getElementById('btn-toggle-ai');
  const drawer = document.getElementById('ai-drawer');
  const closeBtn = document.getElementById('close-ai-drawer');
  const form = document.getElementById('ai-query-form');
  const input = document.getElementById('ai-input');
  const logs = document.getElementById('ai-chat-logs');

  if (btnToggle && drawer) {
    btnToggle.addEventListener('click', () => drawer.classList.toggle('hidden'));
    closeBtn.addEventListener('click', () => drawer.classList.add('hidden'));
  }

  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const q = input.value.trim();
      if (!q) return;

      // User Message
      logs.innerHTML += `<div class="p-3 rounded-2xl bg-brand-600/20 text-brand-accent text-right">"${q}"</div>`;
      input.value = '';

      try {
        const res = await fetch('api/index.php?action=ask_ai', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ query: q })
        });
        const data = await res.json();
        logs.innerHTML += `<div class="p-3.5 rounded-2xl bg-dark-850 border border-white/5 text-slate-300">🤖 ${data.answer}</div>`;
        logs.scrollTop = logs.scrollHeight;
      } catch (err) {
        logs.innerHTML += `<div class="p-3 rounded-2xl bg-rose-500/20 text-rose-300">Error connecting to AI OS endpoint.</div>`;
      }
    });
  }
}