<?php
// admin/index.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RABSS OS — Super Admin Business Operating System</title>
  
  <link rel="icon" type="image/jpeg" href="../logo.jpg">
  <!-- Suppress Tailwind Play CDN warning in console -->
  <script>
    const originalWarn = console.warn;
    console.warn = function(...args) {
      if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com')) {
        return;
      }
      originalWarn.apply(console, args);
    };
  </script>
  <!-- Tailwind CSS & Google Fonts -->
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
            <button class="nav-tab-btn active w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors" data-view="dashboard">
              <span>📊</span> Dashboard
            </button>
            <a href="inquiry_box.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>📥</span> Inquiries Inbox <span class="ml-auto px-1.5 py-0.5 rounded bg-brand-500/20 text-brand-accent text-[10px] font-mono font-bold" id="badge-inquiry-count">0</span>
            </a>
          </div>
        </div>

        <!-- CRM & Sales -->
        <div>
          <p class="px-3 text-[10px] font-mono text-slate-500 uppercase tracking-widest mb-2 font-semibold">CRM & Sales</p>
          <div class="space-y-1">
            <button class="nav-tab-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors" data-view="leads">
              <span>🎯</span> Leads Pipeline
            </button>
            <a href="client.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>👥</span> Clients & Contacts
            </a>
            <a href="proposal.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>📄</span> Proposals & Scope
            </a>
          </div>
        </div>

        <!-- Projects & Tasks -->
        <div>
          <p class="px-3 text-[10px] font-mono text-slate-500 uppercase tracking-widest mb-2 font-semibold">Execution</p>
          <div class="space-y-1">
            <button class="nav-tab-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors" data-view="projects">
              <span>🚀</span> Active Projects
            </button>
            <a href="task.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>📋</span> Tasks & Sprints
            </a>
          </div>
        </div>

        <!-- Finance -->
        <div>
          <p class="px-3 text-[10px] font-mono text-slate-500 uppercase tracking-widest mb-2 font-semibold">Finance & Accounting</p>
          <div class="space-y-1">
            <button class="nav-tab-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors" data-view="invoices">
              <span>💳</span> Invoices & Billing
            </button>
            <button class="nav-tab-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors" data-view="finance">
              <span>📈</span> P&L / Multi-Currency
            </button>
          </div>
        </div>

        <!-- System & Automation -->
        <div>
          <p class="px-3 text-[10px] font-mono text-slate-500 uppercase tracking-widest mb-2 font-semibold">System</p>
          <div class="space-y-1">
           <a href="automations.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>🤖</span> Automation Engine
            </a>
            <button class="nav-tab-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors" data-view="audit">
              <span>🛡️</span> Security & Audit Log
            </button>
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
          <button id="btn-lock-session" class="text-slate-400 hover:text-rose-400 p-1.5 rounded-lg hover:bg-dark-800 text-xs font-mono" title="Lock session">
            🔒
          </button>
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
      
      <!-- Left: Global Search & Command Palette Trigger -->
      <div class="flex items-center gap-4">
        <button id="cmd-palette-trigger" class="flex items-center gap-3 px-3.5 py-1.5 rounded-xl bg-dark-850 border border-white/10 text-slate-400 text-xs font-mono hover:border-brand-500/50 transition-all">
          <span>🔍</span> Quick Search... <kbd class="px-1.5 py-0.5 rounded bg-dark-800 text-[10px] border border-white/10">Ctrl + K</kbd>
        </button>
        
        <!-- Market Timezone Tickers -->
        <div class="hidden xl:flex items-center gap-3 text-[11px] font-mono text-slate-400">
          <span>🇺🇸 NY: <strong class="text-slate-200" id="clock-ny">--:--</strong></span>
          <span>🇨🇦 TOR: <strong class="text-slate-200" id="clock-tor">--:--</strong></span>
          <span>🇦🇪 DXB: <strong class="text-slate-200" id="clock-dxb">--:--</strong></span>
          <span>🇶🇦 DOH: <strong class="text-slate-200" id="clock-doh">--:--</strong></span>
        </div>
      </div>

      <!-- Right: Quick Create, AI Assistant & Currency Selector -->
      <div class="flex items-center gap-3">
        
        <!-- Multi-Currency Selector -->
        <select id="currency-switcher" class="bg-dark-850 border border-white/10 rounded-xl px-3 py-1.5 text-xs font-mono text-slate-300 focus:outline-none focus:border-brand-500">
          <option value="USD">💵 USD ($)</option>
          <option value="CAD">🍁 CAD (C$)</option>
          <option value="AED">🇦🇪 AED (AED)</option>
          <option value="QAR">🇶🇦 QAR (QAR)</option>
          <option value="NPR">🇳🇵 NPR (Rs.)</option>
        </select>

        <!-- AI Assistant Drawer Trigger -->
        <button id="btn-toggle-ai" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-500/20 border border-brand-500/30 text-brand-accent hover:bg-brand-500/30 text-xs font-mono font-bold transition-all shadow-sm">
          <span>✨</span> Ask AI OS
        </button>

        <!-- Quick Create Dropdown -->
        <div class="relative">
          <button id="quick-create-btn" class="px-3.5 py-1.5 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-all shadow-lg shadow-brand-600/30">
            <span>+</span> New
          </button>
          <div id="quick-create-menu" class="hidden absolute right-0 mt-2 w-48 bg-dark-900 border border-white/10 rounded-2xl shadow-2xl p-2 space-y-1 text-xs z-50">
            <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-dark-800 text-slate-200" onclick="openCreateModal('lead')">🎯 New Lead</button>
            <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-dark-800 text-slate-200" onclick="openCreateModal('project')">🚀 New Project</button>
            <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-dark-800 text-slate-200" onclick="openCreateModal('invoice')">💳 New Invoice</button>
            <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-dark-800 text-slate-200" onclick="openCreateModal('task')">📋 New Task</button>
          </div>
        </div>

      </div>

    </header>

    <!-- Scrollable Workspace Panel -->
    <main class="flex-1 overflow-y-auto p-6 lg:p-8 space-y-8">
      
      <!-- ==================== VIEW: DASHBOARD ==================== -->
      <div id="view-dashboard" class="view-panel space-y-8">
        
        <!-- Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
              Good morning, Subash
            </h1>
            <p class="text-sm text-slate-400 mt-1">
              Here is what's happening across RABSS Technologies operations today.
            </p>
          </div>
          <div class="flex items-center gap-2">
            <button class="px-3 py-1.5 rounded-xl bg-dark-850 border border-white/10 text-xs font-mono text-slate-300 hover:text-white" onclick="loadDashboard()">
              🔄 Refresh Telemetry
            </button>
          </div>
        </div>

        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          
          <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
            <div class="flex items-center justify-between text-xs text-slate-400 font-mono mb-2">
              <span>VERIFIED REVENUE</span>
              <span class="text-emerald-400">↑ Paid</span>
            </div>
            <p class="text-2xl font-extrabold font-mono text-white" id="stat-revenue">$0.00</p>
            <p class="text-[11px] text-slate-500 mt-2 font-mono">100% verified settlement</p>
          </div>

          <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
            <div class="flex items-center justify-between text-xs text-slate-400 font-mono mb-2">
              <span>OUTSTANDING INVOICES</span>
              <span class="text-amber-400">Receivables</span>
            </div>
            <p class="text-2xl font-extrabold font-mono text-amber-300" id="stat-outstanding">$0.00</p>
            <p class="text-[11px] text-slate-500 mt-2 font-mono">Across US/UAE clients</p>
          </div>

          <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
            <div class="flex items-center justify-between text-xs text-slate-400 font-mono mb-2">
              <span>TOTAL EXPENSES</span>
              <span class="text-rose-400">P&L Outflow</span>
            </div>
            <p class="text-2xl font-extrabold font-mono text-rose-300" id="stat-expenses">$0.00</p>
            <p class="text-[11px] text-slate-500 mt-2 font-mono">Hosting & Tooling overhead</p>
          </div>

          <div class="p-5 rounded-2xl bg-dark-900 border border-white/10 bg-gradient-to-br from-brand-600/10 to-transparent">
            <div class="flex items-center justify-between text-xs text-slate-400 font-mono mb-2">
              <span>NET OPERATING PROFIT</span>
              <span class="text-brand-accent">Margin</span>
            </div>
            <p class="text-2xl font-extrabold font-mono text-brand-accent" id="stat-net-profit">$0.00</p>
            <p class="text-[11px] text-emerald-400 mt-2 font-mono">Healthy cash-flow</p>
          </div>

        </div>

        <!-- Brand New Operational Control Center Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
          
          <!-- Recent Inquiries Inbox & Dynamic Leads -->
          <div class="lg:col-span-7 bg-dark-900 border border-white/10 rounded-3xl p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
              <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                  <span>📥</span> Active Website Inquiries
                </h3>
                <p class="text-xs text-slate-400">Live submissions routed from public site form</p>
              </div>
              <span class="px-2.5 py-0.5 rounded-full bg-brand-500/10 border border-brand-500/20 text-[10px] font-mono text-brand-accent animate-pulse">Live Sync</span>
            </div>
            <div id="dashboard-inquiries-list" class="space-y-3 max-h-[380px] overflow-y-auto pr-1">
              <p class="text-xs text-slate-500 font-mono italic py-4 text-center">Checking database feed...</p>
            </div>
          </div>

          <!-- Operational Health & Active Projects -->
          <div class="lg:col-span-5 bg-dark-900 border border-white/10 rounded-3xl p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
              <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                  <span>🚀</span> Project Health & Latency
                </h3>
                <p class="text-xs text-slate-400">Operational delivery performance telemetry</p>
              </div>
            </div>
            <div id="dashboard-projects-summary" class="space-y-4">
              <p class="text-xs text-slate-500 font-mono italic py-4 text-center">Checking active sprints...</p>
            </div>
          </div>

        </div>

        <!-- P&L Performance & Quick Command Ticker -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="p-6 bg-dark-900 border border-white/10 rounded-3xl space-y-3">
            <p class="text-xs font-mono text-slate-400 uppercase tracking-wider">⚡ Quick Action Hub</p>
            <div class="grid grid-cols-2 gap-2">
              <button class="p-3 rounded-xl bg-dark-850 hover:bg-brand-500/15 border border-white/5 text-left text-xs font-semibold hover:border-brand-500/30 transition-colors" onclick="openCreateModal('lead')">🎯 Create Lead</button>
              <button class="p-3 rounded-xl bg-dark-850 hover:bg-brand-500/15 border border-white/5 text-left text-xs font-semibold hover:border-brand-500/30 transition-colors" onclick="openCreateModal('project')">🚀 Create Project</button>
              <button class="p-3 rounded-xl bg-dark-850 hover:bg-brand-500/15 border border-white/5 text-left text-xs font-semibold hover:border-brand-500/30 transition-colors" onclick="openCreateModal('invoice')">💳 Issue Invoice</button>
              <button class="p-3 rounded-xl bg-dark-850 hover:bg-brand-500/15 border border-white/5 text-left text-xs font-semibold hover:border-brand-500/30 transition-colors" onclick="openCreateModal('task')">📋 Create Task</button>
            </div>
          </div>
          
          <div class="md:col-span-2 p-6 bg-dark-900 border border-white/10 rounded-3xl space-y-3">
            <p class="text-xs font-mono text-slate-400 uppercase tracking-wider">🌐 Global Currency Valuation Metrics</p>
            <div class="grid grid-cols-4 gap-4 text-center font-mono text-xs pt-1">
              <div class="p-3 rounded-xl bg-dark-850 border border-white/5">
                <span class="text-[10px] text-slate-500 block mb-1">USD Node</span>
                <span class="text-emerald-400 font-bold font-mono">$1.00</span>
                <span class="text-[9px] text-slate-400 block mt-0.5">Base Market</span>
              </div>
              <div class="p-3 rounded-xl bg-dark-850 border border-white/5">
                <span class="text-[10px] text-slate-500 block mb-1">CAD Node</span>
                <span class="text-indigo-400 font-bold font-mono">C$ 1.36</span>
                <span class="text-[9px] text-slate-400 block mt-0.5">SME Target</span>
              </div>
              <div class="p-3 rounded-xl bg-dark-850 border border-white/5">
                <span class="text-[10px] text-slate-500 block mb-1">AED Node</span>
                <span class="text-cyan-400 font-bold font-mono">3.67 AED</span>
                <span class="text-[9px] text-slate-400 block mt-0.5">Gulf Hub</span>
              </div>
              <div class="p-3 rounded-xl bg-dark-850 border border-white/5">
                <span class="text-[10px] text-slate-500 block mb-1">QAR Node</span>
                <span class="text-purple-400 font-bold font-mono">3.64 QAR</span>
                <span class="text-[9px] text-slate-400 block mt-0.5">Qatar Hub</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================== VIEW: INBOX ==================== -->
      <div id="view-inbox" class="view-panel hidden space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-white">Inquiries Inbox</h2>
            <p class="text-xs text-slate-400 font-mono">Form submissions received from the public website</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-dark-850 hover:bg-dark-800 border border-white/10 text-white font-bold rounded-xl text-xs" onclick="loadInquiries()">
              🔄 Refresh
            </button>
          </div>
        </div>

        <div id="inquiries-table-container" class="bg-dark-900 border border-white/10 rounded-3xl overflow-hidden">
          <!-- Injected via JS -->
        </div>
      </div>

      <!-- ==================== VIEW: LEADS & CRM ==================== -->
      <div id="view-leads" class="view-panel hidden space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-white">CRM & Opportunity Pipeline</h2>
            <p class="text-xs text-slate-400 font-mono">Drag and drop leads between lifecycle stages</p>
          </div>
          <button class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs" onclick="openCreateModal('lead')">
            + Add Lead
          </button>
        </div>

        <!-- Kanban Board (Stages) -->
        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-4 overflow-x-auto pb-4">
          
          <!-- Column: New Inquiry -->
          <div class="kanban-col bg-dark-900 border border-white/10 rounded-2xl p-3 flex flex-col min-w-[220px]" data-stage="New Inquiry">
            <div class="flex items-center justify-between mb-3 px-1 text-xs font-bold text-slate-300 font-mono">
              <span>NEW INQUIRY</span>
              <span class="count-badge px-2 py-0.5 rounded bg-dark-800 text-[10px] text-slate-400">0</span>
            </div>
            <div class="kanban-list space-y-3 flex-1 min-h-[300px]" id="leads-col-new"></div>
          </div>

          <!-- Column: Contacted -->
          <div class="kanban-col bg-dark-900 border border-white/10 rounded-2xl p-3 flex flex-col min-w-[220px]" data-stage="Contacted">
            <div class="flex items-center justify-between mb-3 px-1 text-xs font-bold text-slate-300 font-mono">
              <span>CONTACTED</span>
              <span class="count-badge px-2 py-0.5 rounded bg-dark-800 text-[10px] text-slate-400">0</span>
            </div>
            <div class="kanban-list space-y-3 flex-1 min-h-[300px]" id="leads-col-contacted"></div>
          </div>

          <!-- Column: Discovery -->
          <div class="kanban-col bg-dark-900 border border-white/10 rounded-2xl p-3 flex flex-col min-w-[220px]" data-stage="Discovery">
            <div class="flex items-center justify-between mb-3 px-1 text-xs font-bold text-slate-300 font-mono">
              <span>DISCOVERY</span>
              <span class="count-badge px-2 py-0.5 rounded bg-dark-800 text-[10px] text-slate-400">0</span>
            </div>
            <div class="kanban-list space-y-3 flex-1 min-h-[300px]" id="leads-col-discovery"></div>
          </div>

          <!-- Column: Proposal Sent -->
          <div class="kanban-col bg-dark-900 border border-white/10 rounded-2xl p-3 flex flex-col min-w-[220px]" data-stage="Proposal Sent">
            <div class="flex items-center justify-between mb-3 px-1 text-xs font-bold text-slate-300 font-mono">
              <span>PROPOSAL SENT</span>
              <span class="count-badge px-2 py-0.5 rounded bg-dark-800 text-[10px] text-slate-400">0</span>
            </div>
            <div class="kanban-list space-y-3 flex-1 min-h-[300px]" id="leads-col-proposal"></div>
          </div>

          <!-- Column: Negotiation -->
          <div class="kanban-col bg-dark-900 border border-white/10 rounded-2xl p-3 flex flex-col min-w-[220px]" data-stage="Negotiation">
            <div class="flex items-center justify-between mb-3 px-1 text-xs font-bold text-slate-300 font-mono">
              <span>NEGOTIATION</span>
              <span class="count-badge px-2 py-0.5 rounded bg-dark-800 text-[10px] text-slate-400">0</span>
            </div>
            <div class="kanban-list space-y-3 flex-1 min-h-[300px]" id="leads-col-negotiation"></div>
          </div>

          <!-- Column: Won -->
          <div class="kanban-col bg-dark-900 border border-emerald-500/30 bg-emerald-950/10 rounded-2xl p-3 flex flex-col min-w-[220px]" data-stage="Won">
            <div class="flex items-center justify-between mb-3 px-1 text-xs font-bold text-emerald-400 font-mono">
              <span>WON ✓</span>
              <span class="count-badge px-2 py-0.5 rounded bg-emerald-500/20 text-[10px] text-emerald-300">0</span>
            </div>
            <div class="kanban-list space-y-3 flex-1 min-h-[300px]" id="leads-col-won"></div>
          </div>

          <!-- Column: Lost -->
          <div class="kanban-col bg-dark-900 border border-rose-500/20 rounded-2xl p-3 flex flex-col min-w-[220px]" data-stage="Lost">
            <div class="flex items-center justify-between mb-3 px-1 text-xs font-bold text-rose-400 font-mono">
              <span>LOST</span>
              <span class="count-badge px-2 py-0.5 rounded bg-rose-500/20 text-[10px] text-rose-300">0</span>
            </div>
            <div class="kanban-list space-y-3 flex-1 min-h-[300px]" id="leads-col-lost"></div>
          </div>

        </div>
      </div>

      <!-- ==================== VIEW: ACTIVE PROJECTS ==================== -->
      <div id="view-projects" class="view-panel hidden space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-white">Engineering & Project Operations</h2>
            <p class="text-xs text-slate-400 font-mono">Live sprint tracking, deadlines, and budgets</p>
          </div>
          <button class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs" onclick="openCreateModal('project')">
            + New Project
          </button>
        </div>

        <div id="projects-table-container" class="bg-dark-900 border border-white/10 rounded-3xl overflow-hidden">
          <!-- Injected via JS -->
        </div>
      </div>

      <!-- ==================== VIEW: INVOICES & BILLING ==================== -->
      <div id="view-invoices" class="view-panel hidden space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-white">Invoicing & International Receivables</h2>
            <p class="text-xs text-slate-400 font-mono">Multi-currency billing across USA, Canada, UAE, Qatar</p>
          </div>
          <button class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs" onclick="openCreateModal('invoice')">
            + Create Invoice
          </button>
        </div>

        <div id="invoices-table-container" class="bg-dark-900 border border-white/10 rounded-3xl overflow-hidden">
          <!-- Injected via JS -->
        </div>
      </div>

      <!-- ==================== VIEW: AUTOMATION ENGINE ==================== -->
      <div id="view-automations" class="view-panel hidden space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-white">Business Automation Rules</h2>
            <p class="text-xs text-slate-400 font-mono">Trigger → Condition → Action background workflows</p>
          </div>
        </div>

        <div class="space-y-4">
          <div class="p-5 rounded-2xl bg-dark-900 border border-white/10 flex items-center justify-between">
            <div>
              <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold uppercase">ACTIVE</span>
              <h4 class="text-base font-bold text-white mt-1">Website Inquiry → Auto CRM Lead Sync</h4>
              <p class="text-xs text-slate-400">Trigger: Website form submitted → Action: Create Lead & Assign to Subash Sitaula</p>
            </div>
            <span class="text-xs font-mono text-slate-500">Auto-dispatched 4 times</span>
          </div>

          <div class="p-5 rounded-2xl bg-dark-900 border border-white/10 flex items-center justify-between">
            <div>
              <span class="px-2 py-0.5 rounded bg-brand-500/20 text-brand-accent text-[10px] font-mono font-bold uppercase">ACTIVE</span>
              <h4 class="text-base font-bold text-white mt-1">Proposal Won → Client & Project Scaffold</h4>
              <p class="text-xs text-slate-400">Trigger: Lead moved to Won → Action: Generate Client record + Project Checklist</p>
            </div>
            <span class="text-xs font-mono text-slate-500">Auto-dispatched 2 times</span>
          </div>
        </div>
      </div>

      <!-- ==================== VIEW: AUDIT LOGS ==================== -->
      <div id="view-audit" class="view-panel hidden space-y-6">
        <div>
          <h2 class="text-2xl font-bold text-white">Security & System Audit Logs</h2>
          <p class="text-xs text-slate-400 font-mono">Tamper-evident record of all user and financial actions</p>
        </div>

        <div id="audit-table-container" class="bg-dark-900 border border-white/10 rounded-3xl overflow-hidden">
          <!-- Injected via JS -->
        </div>
      </div>

    </main>
  </div>

  <!-- ==================== 3. AI ASSISTANT SLIDE-OVER DRAWER ==================== -->
  <div id="ai-drawer" class="hidden fixed inset-y-0 right-0 w-96 bg-dark-900 border-l border-white/10 shadow-2xl z-50 flex flex-col justify-between">
    <div class="p-6 border-b border-white/10 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-brand-accent">✨</span>
        <h3 class="font-bold text-white text-sm">RABSS AI Business Assistant</h3>
      </div>
      <button id="close-ai-drawer" class="text-slate-400 hover:text-white text-lg">✕</button>
    </div>

    <!-- Chat Logs -->
    <div id="ai-chat-logs" class="flex-1 p-6 overflow-y-auto space-y-4 text-xs font-mono">
      <div class="p-3.5 rounded-2xl bg-dark-850 border border-white/5 text-slate-300">
        👋 Hello Subash. I can analyze revenue metrics, list overdue invoices, or summarize stale CRM leads. What would you like to know?
      </div>
    </div>

    <!-- Prompt Input -->
    <div class="p-4 border-t border-white/10 bg-dark-950">
      <form id="ai-query-form" class="flex gap-2">
        <input type="text" id="ai-input" class="flex-1 bg-dark-850 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500" placeholder="e.g. Total revenue this month...">
        <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold">Ask</button>
      </form>
    </div>
  </div>

  <!-- ==================== 4. COMMAND PALETTE (CTRL+K) ==================== -->
  <div id="cmd-palette-modal" class="hidden fixed inset-0 z-50 bg-dark-950/80 backdrop-blur-md flex items-start justify-center pt-24 p-4">
    <div class="bg-dark-900 border border-white/20 rounded-3xl max-w-xl w-full p-4 shadow-2xl">
      <input type="text" id="cmd-search-input" class="w-full bg-dark-950 border border-white/10 rounded-2xl px-4 py-3 text-sm text-white focus:outline-none focus:border-brand-500 mb-3" placeholder="Type a command or search entities (e.g. Leads, Projects, Invoices)...">
      <div class="space-y-1 text-xs font-mono text-slate-300" id="cmd-results">
        <button class="w-full text-left px-3 py-2 rounded-xl hover:bg-dark-800 flex items-center justify-between" onclick="switchView('dashboard')"><span>📊 Go to Dashboard</span><kbd class="text-[10px] text-slate-500">Jump</kbd></button>
        <button class="w-full text-left px-3 py-2 rounded-xl hover:bg-dark-800 flex items-center justify-between" onclick="switchView('inbox')"><span>📥 Go to Inquiries Inbox</span><kbd class="text-[10px] text-slate-500">Jump</kbd></button>
        <button class="w-full text-left px-3 py-2 rounded-xl hover:bg-dark-800 flex items-center justify-between" onclick="switchView('leads')"><span>🎯 Go to Leads Pipeline</span><kbd class="text-[10px] text-slate-500">Jump</kbd></button>
        <button class="w-full text-left px-3 py-2 rounded-xl hover:bg-dark-800 flex items-center justify-between" onclick="switchView('invoices')"><span>💳 Go to Invoices & Billing</span><kbd class="text-[10px] text-slate-500">Jump</kbd></button>
      </div>
    </div>
  </div>

  <!-- ==================== UNIVERSAL CREATION MODAL ==================== -->
  <div id="creation-modal" class="hidden fixed inset-0 z-50 bg-dark-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-dark-900 border border-white/10 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative">
      <button id="close-creation-modal" class="absolute top-6 right-6 text-slate-400 hover:text-white text-xl">✕</button>
      <h3 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
        <span id="modal-icon">➕</span> <span id="modal-title">Create New Entity</span>
      </h3>
      <p class="text-xs text-slate-400 font-mono mb-6" id="modal-subtitle">Add a new record to the system database</p>
      
      <form id="creation-form" class="space-y-4 text-xs">
        <!-- Dynamic fields will be injected here by JS -->
      </form>
    </div>
  </div>

  <!-- JavaScript -->
  <script src="assets/js/admin.js"></script>
</body>
</html>