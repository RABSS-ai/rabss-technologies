<?php
// admin/inquiry_box.php
session_start();
require_once __DIR__ . '/api/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch inquiries
try {
    $stmt = $pdo->query("SELECT * FROM inquiries ORDER BY id DESC");
    $inquiries = $stmt->fetchAll();
    
    $stmt_new = $pdo->query("SELECT COUNT(*) as new_cnt FROM inquiries WHERE status = 'New'");
    $new_count = $stmt_new->fetch()['new_cnt'] ?? 0;
} catch (PDOException $e) {
    $inquiries = [];
    $new_count = 0;
    error_log("Error fetching inquiries: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inquiries Inbox — RABSS OS</title>
  <link rel="icon" type="image/jpeg" href="../logo.jpg">
  <!-- Tailwind CSS & Google Fonts -->
  <script>
    const originalWarn = console.warn;
    console.warn = function(...args) {
      if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com')) {
        return;
      }
      originalWarn.apply(console, args);
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#EEF2FF',
              500: '#6366F1',
              600: '#4F46E5',
              700: '#4338CA',
              accent: '#06B6D4'
            },
            dark: {
              950: '#07090E',
              900: '#0C0F17',
              850: '#111622',
              800: '#181F30',
              700: '#263147'
            }
          },
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'sans-serif'],
            mono: ['JetBrains Mono', 'monospace']
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="bg-dark-950 text-slate-100 font-sans antialiased selection:bg-brand-500 selection:text-white flex h-screen overflow-hidden">

  <!-- ==================== 1. SIDEBAR ==================== -->
  <aside id="sidebar" class="w-64 bg-dark-900 border-r border-white/5 flex flex-col justify-between transition-all duration-300 z-30 shrink-0">
    <div class="p-4 flex flex-col h-full overflow-y-auto">
      
      <!-- Brand & CEO Status -->
      <div class="flex items-center gap-3 px-2 py-3 mb-4 border-b border-white/5">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-brand-accent p-[1.5px] shadow-lg shadow-brand-500/20 flex items-center justify-center overflow-hidden">
          <img src="../logo.jpg" alt="RABSS Technologies Logo" class="w-full h-full object-cover rounded-[10px]">
        </div>
        <div class="flex flex-col">
          <span class="font-bold text-sm tracking-wider text-white">RABSS OS</span>
          <span class="text-[10px] font-mono text-emerald-400 flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> SUPER ADMIN
          </span>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="space-y-6 flex-1 text-xs">
        
        <!-- Main -->
        <div>
          <p class="px-3 text-[10px] font-mono text-slate-500 uppercase tracking-widest mb-2 font-semibold">Main</p>
          <div class="space-y-1">
            <a href="index.php?view=dashboard" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>📊</span> Dashboard
            </a>
            <a href="inquiry_box.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-white bg-dark-800 transition-colors">
              <span>📥</span> Inquiries Inbox <span class="ml-auto px-1.5 py-0.5 rounded bg-brand-500/20 text-brand-accent text-[10px] font-mono font-bold" id="badge-inquiry-count"><?= $new_count ?></span>
            </a>
            <a href="meeting.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>🤝</span> Video Meeting
            </a>
          </div>
        </div>

        <!-- CRM & Sales -->
        <div>
          <p class="px-3 text-[10px] font-mono text-slate-500 uppercase tracking-widest mb-2 font-semibold">CRM & Sales</p>
          <div class="space-y-1">
            <a href="index.php?view=leads" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>🎯</span> Leads Pipeline
            </a>
            <a href="index.php?view=clients" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>🏢</span> Clients
            </a>
            <a href="index.php?view=proposals" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>📄</span> Proposals & Scope
            </a>
          </div>
        </div>

        <!-- Projects & Tasks -->
        <div>
          <p class="px-3 text-[10px] font-mono text-slate-500 uppercase tracking-widest mb-2 font-semibold">Execution</p>
          <div class="space-y-1">
            <a href="index.php?view=projects" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>🚀</span> Active Projects
            </a>
            <a href="index.php?view=tasks" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>📋</span> Tasks & Sprints
            </a>
          </div>
        </div>

        <!-- Finance -->
        <div>
          <p class="px-3 text-[10px] font-mono text-slate-500 uppercase tracking-widest mb-2 font-semibold">Finance & Accounting</p>
          <div class="space-y-1">
            <a href="index.php?view=invoices" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>💳</span> Invoices & Billing
            </a>
            <a href="index.php?view=finance" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>📈</span> P&L / Multi-Currency
            </a>
          </div>
        </div>

        <!-- System & Automation -->
        <div>
          <p class="px-3 text-[10px] font-mono text-slate-500 uppercase tracking-widest mb-2 font-semibold">System</p>
          <div class="space-y-1">
            <a href="index.php?view=automations" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>⚡</span> Automation Rules
            </a>
            <a href="index.php?view=audit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>🛡️</span> Security & Audit Log
            </a>
          </div>
        </div>

      </nav>

      <!-- User Profile Box -->
      <div class="pt-4 border-t border-white/5 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-dark-800 border border-white/10 flex items-center justify-center font-bold text-xs text-white">
            SS
          </div>
          <div class="flex flex-col">
            <span class="text-xs font-bold text-white leading-tight">Subash Sitaula</span>
            <span class="text-[10px] font-mono text-slate-400">CEO / Founder</span>
          </div>
        </div>
        <div class="flex items-center gap-1">
          <button id="btn-logout" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg hover:bg-dark-800 text-xs font-mono" title="Logout">
            🚪
          </button>
        </div>
      </div>

    </div>
  </aside>

  <!-- ==================== 2. MAIN VIEW AREA ==================== -->
  <div class="flex-1 flex flex-col h-full overflow-hidden">
    
    <!-- Top Navigation Bar -->
    <header class="h-16 bg-dark-900/80 border-b border-white/5 backdrop-blur-xl px-6 flex items-center justify-between shrink-0">
      
      <!-- Left Timezone Tickers -->
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-3 text-[11px] font-mono text-slate-400">
          <span>🇺🇸 NY: <strong class="text-slate-200" id="clock-ny">--:--</strong></span>
          <span>🇨🇦 TOR: <strong class="text-slate-200" id="clock-tor">--:--</strong></span>
          <span>🇦🇪 DXB: <strong class="text-slate-200" id="clock-dxb">--:--</strong></span>
          <span>🇶🇦 DOH: <strong class="text-slate-200" id="clock-doh">--:--</strong></span>
        </div>
      </div>

      <!-- Right Action Indicators -->
      <div class="flex items-center gap-3 text-xs font-mono">
        <span class="px-2.5 py-1 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-accent font-bold">DATABASE FEED ACTIVE</span>
      </div>
    </header>

    <!-- Scrollable Workspace Panel -->
    <main class="flex-1 overflow-y-auto p-6 lg:p-8 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-tight">Inquiries Inbox</h1>
          <p class="text-xs text-slate-400 font-mono mt-1">Form submissions received from the public website</p>
        </div>
        <div class="flex items-center gap-2">
          <button class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs transition-colors" onclick="window.location.reload()">
            🔄 Refresh Feed
          </button>
        </div>
      </div>

      <div class="bg-dark-900 border border-white/10 rounded-3xl overflow-hidden shadow-2xl">
        <?php if (empty($inquiries)): ?>
          <p class="text-xs text-slate-500 font-mono italic py-12 text-center">No inquiries received yet.</p>
        <?php else: ?>
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
              <?php foreach ($inquiries as $inq): ?>
                <tr class="hover:bg-dark-850/50 transition-colors">
                  <td class="p-4 font-bold text-white"><?= htmlspecialchars($inq['name']) ?></td>
                  <td class="p-4 text-slate-300 font-mono"><?= htmlspecialchars($inq['email']) ?></td>
                  <td class="p-4 text-slate-300 font-mono"><?= htmlspecialchars($inq['whatsapp'] ?? '—') ?></td>
                  <td class="p-4 text-slate-300"><?= htmlspecialchars($inq['company'] ?? '—') ?></td>
                  <td class="p-4 font-mono text-brand-accent"><?= htmlspecialchars($inq['country'] ?? '—') ?></td>
                  <td class="p-4 text-slate-300"><?= htmlspecialchars($inq['project_type'] ?? '—') ?></td>
                  <td class="p-4 font-mono text-emerald-400"><?= htmlspecialchars($inq['budget'] ?? '—') ?></td>
                  <td class="p-4 font-mono text-cyan-400"><?= htmlspecialchars($inq['source'] ?? 'Website Form') ?></td>
                  <td class="p-4 font-mono">
                    <?php if (($inq['status'] ?? 'New') === 'New'): ?>
                      <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold text-[10px]">New</span>
                    <?php else: ?>
                      <span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold text-[10px]"><?= htmlspecialchars($inq['status']) ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="p-4 text-slate-300 max-w-xs truncate" title="<?= htmlspecialchars($inq['description'] ?? '') ?>"><?= htmlspecialchars($inq['description'] ?? '—') ?></td>
                  <td class="p-4 font-mono text-slate-500"><?= htmlspecialchars($inq['created_at'] ?? 'Recently') ?></td>
                  <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                    <button class="px-2 py-1 bg-dark-800 hover:bg-dark-700 text-slate-200 hover:text-white rounded font-mono text-[10px] font-bold" onclick="viewInquiryById(<?= $inq['id'] ?>)">View</button>
                    <?php if (($inq['status'] ?? 'New') === 'New'): ?>
                      <button class="px-2 py-1 bg-brand-600 hover:bg-brand-500 text-white rounded font-mono text-[10px] font-bold" onclick="promoteToLead(<?= $inq['id'] ?>)">Promote</button>
                    <?php else: ?>
                      <span class="text-[10px] font-mono text-emerald-400 bg-emerald-950/40 border border-emerald-500/30 px-1.5 py-0.5 rounded font-bold">Promoted</span>
                    <?php endif; ?>
                    <button class="px-2 py-1 bg-rose-600 hover:bg-rose-500 text-white rounded font-mono text-[10px] font-bold" onclick="deleteInquiry(<?= $inq['id'] ?>)">Delete</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <!-- ==================== VIEW INQUIRY MODAL ==================== -->
  <div id="view-inquiry-modal" class="hidden fixed inset-0 z-50 bg-dark-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-dark-900 border border-white/20 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl relative">
      <button onclick="closeModal('view-inquiry-modal')" class="absolute top-6 right-6 text-slate-400 hover:text-white text-xl">✕</button>
      
      <div class="flex items-center gap-4 mb-6">
        <div id="inq-avatar" class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-600 to-brand-accent p-0.5 flex items-center justify-center font-bold text-lg text-white">
          --
        </div>
        <div>
          <h3 id="inq-name" class="text-xl font-bold text-white">--</h3>
          <p id="inq-company" class="text-xs text-brand-accent font-mono">--</p>
        </div>
      </div>

      <!-- Quick Metrics Grid -->
      <div class="grid grid-cols-3 gap-3 mb-6 font-mono text-xs">
        <div class="p-3 rounded-xl bg-dark-950 border border-white/5">
          <span class="text-slate-400 text-[10px]">BUDGET</span>
          <p id="inq-budget" class="text-emerald-400 font-bold mt-0.5">--</p>
        </div>
        <div class="p-3 rounded-xl bg-dark-950 border border-white/5">
          <span class="text-slate-400 text-[10px]">PROJECT TYPE</span>
          <p id="inq-type" class="text-white font-bold mt-0.5">--</p>
        </div>
        <div class="p-3 rounded-xl bg-dark-950 border border-white/5">
          <span class="text-slate-400 text-[10px]">MARKET</span>
          <p id="inq-country" class="text-purple-300 font-bold mt-0.5">--</p>
        </div>
      </div>

      <!-- Contact Details -->
      <div class="space-y-2 text-xs font-mono text-slate-300 p-4 rounded-xl bg-dark-950 border border-white/5 mb-6">
        <div class="flex justify-between">
          <span class="text-slate-500">Email:</span>
          <span id="inq-email" class="text-white">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">WhatsApp:</span>
          <span id="inq-whatsapp" class="text-white">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Source:</span>
          <span id="inq-source" class="text-cyan-400">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Received At:</span>
          <span id="inq-date" class="text-slate-400">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Status:</span>
          <span id="inq-status" class="text-white">--</span>
        </div>
      </div>

      <!-- Description -->
      <div class="mb-6">
        <p class="text-[11px] font-mono text-slate-400 mb-1">PROJECT DESCRIPTION / GOALS:</p>
        <div id="inq-description" class="p-3 rounded-xl bg-dark-850 border border-white/5 text-xs text-slate-300 whitespace-pre-wrap max-h-48 overflow-y-auto">
          --
        </div>
      </div>

      <div class="flex gap-3" id="modal-action-buttons">
        <!-- Injected via JS -->
      </div>
    </div>
  </div>

  <!-- Clocks and Logout script for Inquiry Box Page -->
  <script>
    window.allInquiries = <?= json_encode($inquiries) ?>;

    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
    }

    function viewInquiryById(id) {
      if (!window.allInquiries) return;
      const inq = window.allInquiries.find(item => item.id == id);
      if (!inq) return;

      document.getElementById('inq-avatar').textContent = (inq.name || 'IN').substring(0, 2).toUpperCase();
      document.getElementById('inq-name').textContent = inq.name;
      document.getElementById('inq-company').textContent = inq.company ? inq.company + ' • ' + (inq.country || '') : 'Independent Account';
      document.getElementById('inq-budget').textContent = inq.budget || '—';
      document.getElementById('inq-type').textContent = inq.project_type || '—';
      document.getElementById('inq-country').textContent = inq.country || 'Other';
      document.getElementById('inq-email').textContent = inq.email || '—';
      document.getElementById('inq-whatsapp').textContent = inq.whatsapp || '—';
      document.getElementById('inq-source').textContent = inq.source || 'Website Form';
      document.getElementById('inq-date').textContent = inq.created_at || '—';
      document.getElementById('inq-status').textContent = inq.status || 'New';
      document.getElementById('inq-description').textContent = inq.description || 'No description provided.';

      const isNew = (inq.status || 'New') === 'New';
      const actionButtons = document.getElementById('modal-action-buttons');
      if (actionButtons) {
        if (isNew) {
          actionButtons.innerHTML = `
            <button class="flex-1 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs text-center transition-colors" onclick="closeModal('view-inquiry-modal'); promoteToLead(${inq.id});">
              Promote to CRM Lead
            </button>
            <button class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs text-center transition-colors" onclick="closeModal('view-inquiry-modal'); deleteInquiry(${inq.id});">
              Delete Inquiry
            </button>
          `;
        } else {
          actionButtons.innerHTML = `
            <span class="flex-1 py-2.5 text-emerald-400 bg-emerald-950/40 border border-emerald-500/30 font-bold rounded-xl text-xs text-center">
              Promoted to CRM
            </span>
            <button class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs text-center transition-colors" onclick="closeModal('view-inquiry-modal'); deleteInquiry(${inq.id});">
              Delete Inquiry
            </button>
          `;
        }
      }

      document.getElementById('view-inquiry-modal').classList.remove('hidden');
    }

    document.getElementById('view-inquiry-modal').addEventListener('click', (e) => {
      if (e.target === document.getElementById('view-inquiry-modal')) {
        closeModal('view-inquiry-modal');
      }
    });

    function updateClocks() {
      const opts = { hour: '2-digit', minute: '2-digit', hour12: false };
      document.getElementById('clock-ny').textContent = new Intl.DateTimeFormat('en-US', { ...opts, timeZone: 'America/New_York' }).format(new Date());
      document.getElementById('clock-tor').textContent = new Intl.DateTimeFormat('en-US', { ...opts, timeZone: 'America/Toronto' }).format(new Date());
      document.getElementById('clock-dxb').textContent = new Intl.DateTimeFormat('en-US', { ...opts, timeZone: 'Asia/Dubai' }).format(new Date());
      document.getElementById('clock-doh').textContent = new Intl.DateTimeFormat('en-US', { ...opts, timeZone: 'Asia/Qatar' }).format(new Date());
    }
    updateClocks();
    setInterval(updateClocks, 10000);

    document.getElementById('btn-logout').addEventListener('click', async () => {
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
            window.location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        } catch (e) {
          console.error('Promotion failed:', e);
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
            window.location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        } catch (e) {
          console.error('Delete inquiry failed:', e);
          alert('Server connection failed.');
        }
      }
    }
  </script>
</body>
</html>