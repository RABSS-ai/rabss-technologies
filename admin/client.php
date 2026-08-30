<?php
// admin/client.php
session_start();
require_once __DIR__ . '/api/db.php';

// Auth Protection
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['user_name'] ?? 'Subash Sitaula';
$user_role = $_SESSION['user_role'] ?? 'Super Admin';
$msg = '';
$error = '';

// Handle Actions (Add, Edit, Delete Client, Add Note)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    // 1. ADD NEW CLIENT
    if ($action === 'add_client') {
        $name = trim($_POST['name'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $country = trim($_POST['country'] ?? 'USA');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $currency = trim($_POST['currency'] ?? 'USD');
        $notes = trim($_POST['notes'] ?? '');

        if (!$name || !$email) {
            $error = 'Client name and email are required.';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO clients (name, company, country, email, phone, currency, notes, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Active')
            ");
            $stmt->execute([$name, $company, $country, $email, $phone, $currency, $notes]);
            $clientId = $pdo->lastInsertId();

            // Audit log
            $stmtAudit = $pdo->prepare("INSERT INTO audit_logs (user_name, action, entity, entity_id, ip_address) VALUES (?, ?, 'Client', ?, ?)");
            $stmtAudit->execute([$user_name, "Created new client: {$name} ({$company})", (string)$clientId, $ip]);

            $msg = "Client '{$name}' created successfully.";
        }
    }

    // 2. EDIT CLIENT
    if ($action === 'edit_client') {
        $id = (int)($_POST['client_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $country = trim($_POST['country'] ?? 'USA');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $currency = trim($_POST['currency'] ?? 'USD');
        $status = trim($_POST['status'] ?? 'Active');
        $notes = trim($_POST['notes'] ?? '');

        if ($id > 0 && $name && $email) {
            $stmt = $pdo->prepare("
                UPDATE clients 
                SET name = ?, company = ?, country = ?, email = ?, phone = ?, currency = ?, status = ?, notes = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $company, $country, $email, $phone, $currency, $status, $notes, $id]);

            // Audit log
            $stmtAudit = $pdo->prepare("INSERT INTO audit_logs (user_name, action, entity, entity_id, ip_address) VALUES (?, ?, 'Client', ?, ?)");
            $stmtAudit->execute([$user_name, "Updated client #{$id}: {$name}", (string)$id, $ip]);

            $msg = "Client details updated successfully.";
        }
    }

    // 3. DELETE CLIENT
    if ($action === 'delete_client') {
        $id = (int)($_POST['client_id'] ?? 0);
        if ($id > 0) {
            $stmtName = $pdo->prepare("SELECT name FROM clients WHERE id = ?");
            $stmtName->execute([$id]);
            $clientName = $stmtName->fetchColumn() ?: "Client #{$id}";

            $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
            $stmt->execute([$id]);

            // Audit log
            $stmtAudit = $pdo->prepare("INSERT INTO audit_logs (user_name, action, entity, entity_id, ip_address) VALUES (?, ?, 'Client', ?, ?)");
            $stmtAudit->execute([$user_name, "Deleted client: {$clientName}", (string)$id, $ip]);

            $msg = "Client '{$clientName}' has been removed.";
        }
    }
}

// Fetch new inquiries count
$stmt_new = $pdo->query("SELECT COUNT(*) as new_cnt FROM inquiries WHERE status = 'New'");
$new_count = $stmt_new->fetch()['new_cnt'] ?? 0;

// Fetch All Clients with Aggregated Project & Invoice Counts
$clients = $pdo->query("
    SELECT 
        c.*,
        (SELECT COUNT(*) FROM projects p WHERE p.client_id = c.id) as project_count,
        (SELECT COUNT(*) FROM invoices i WHERE i.client_id = c.id AND i.status IN ('Sent', 'Overdue')) as pending_invoices,
        (SELECT COALESCE(SUM(total), 0) FROM invoices i WHERE i.client_id = c.id AND i.status = 'Paid') as lifetime_paid
    FROM clients c 
    ORDER BY c.id DESC
")->fetchAll();

// Total Summary Metrics
$total_clients = count($clients);
$active_clients = count(array_filter($clients, fn($c) => $c['status'] === 'Active'));
$total_ltv = array_sum(array_column($clients, 'lifetime_paid'));
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client CRM & Accounts — RABSS OS</title>
  
  <!-- Tailwind CSS & Google Fonts -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              500: '#6366F1',
              600: '#4F46E5',
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
            <a href="inquiry_box.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>📥</span> Inquiries Inbox <span class="ml-auto px-1.5 py-0.5 rounded bg-brand-500/20 text-brand-accent text-[10px] font-mono font-bold" id="badge-inquiry-count"><?= $new_count ?></span>
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
            <a href="client.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-white bg-dark-800 transition-colors">
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
            <a href="index.php?view=projects" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
              <span>🚀</span> Active Projects
            </a>
            <a href="task.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
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
            <span class="text-xs font-bold text-white leading-tight"><?= htmlspecialchars($user_name) ?></span>
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

  <!-- ==================== MAIN CONTENT AREA ==================== -->
  <div class="flex-1 flex flex-col h-full overflow-hidden">
    
    <!-- Top Header Bar -->
    <header class="h-16 bg-dark-900/80 border-b border-white/5 backdrop-blur-xl px-6 flex items-center justify-between shrink-0">
      <div class="flex items-center gap-4">
        <h1 class="text-lg font-bold text-white flex items-center gap-2">
          <span>🏢</span> Client Accounts & CRM Directory
        </h1>
      </div>

      <div class="flex items-center gap-3">
        <button onclick="openAddClientModal()" class="px-4 py-2 bg-gradient-to-r from-brand-600 to-brand-accent hover:from-brand-500 hover:to-cyan-400 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-lg shadow-brand-600/20 transition-all">
          <span>+</span> Add New Client
        </button>
      </div>
    </header>

    <!-- Workspace View -->
    <main class="flex-1 overflow-y-auto p-6 lg:p-8 space-y-6">
      
      <!-- Notifications -->
      <?php if (!empty($msg)): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-mono flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span>✓</span>
            <span><?= htmlspecialchars($msg) ?></span>
          </div>
          <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">✕</button>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-mono flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span>⚠️</span>
            <span><?= htmlspecialchars($error) ?></span>
          </div>
          <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white">✕</button>
        </div>
      <?php endif; ?>

      <!-- 4 Quick Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
          <p class="text-xs text-slate-400 font-mono">TOTAL ACCOUNTS</p>
          <p class="text-2xl font-extrabold font-mono text-white mt-1"><?= $total_clients ?></p>
          <p class="text-[11px] text-slate-500 mt-1 font-mono">USA, Canada, UAE, Qatar</p>
        </div>

        <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
          <p class="text-xs text-slate-400 font-mono">ACTIVE CLIENTS</p>
          <p class="text-2xl font-extrabold font-mono text-emerald-400 mt-1"><?= $active_clients ?></p>
          <p class="text-[11px] text-slate-500 mt-1 font-mono">In active delivery / retainer</p>
        </div>

        <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
          <p class="text-xs text-slate-400 font-mono">VERIFIED PAID LTV</p>
          <p class="text-2xl font-extrabold font-mono text-brand-accent mt-1">$<?= number_format($total_ltv, 2) ?></p>
          <p class="text-[11px] text-slate-500 mt-1 font-mono">Settled invoice revenue</p>
        </div>

        <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
          <p class="text-xs text-slate-400 font-mono">AVG REVENUE / CLIENT</p>
          <p class="text-2xl font-extrabold font-mono text-purple-300 mt-1">
            $<?= $total_clients > 0 ? number_format($total_ltv / $total_clients, 2) : '0.00' ?>
          </p>
          <p class="text-[11px] text-slate-500 mt-1 font-mono">Founding tier average</p>
        </div>

      </div>

      <!-- Filter & Search Controls -->
      <div class="p-4 rounded-2xl bg-dark-900 border border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative flex-1 w-full sm:w-auto">
          <input 
            type="text" 
            id="client-search" 
            onkeyup="filterClients()"
            class="w-full bg-dark-850 border border-white/10 rounded-xl px-4 py-2 text-xs text-slate-200 focus:outline-none focus:border-brand-500 font-mono"
            placeholder="Search clients by name, company, email..."
          >
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
          <select id="country-filter" onchange="filterClients()" class="bg-dark-850 border border-white/10 rounded-xl px-3 py-2 text-xs font-mono text-slate-300 focus:outline-none focus:border-brand-500">
            <option value="">All Markets</option>
            <option value="USA">🇺🇸 USA</option>
            <option value="Canada">🇨🇦 Canada</option>
            <option value="UAE">🇦🇪 UAE</option>
            <option value="Qatar">🇶🇦 Qatar</option>
          </select>

          <select id="status-filter" onchange="filterClients()" class="bg-dark-850 border border-white/10 rounded-xl px-3 py-2 text-xs font-mono text-slate-300 focus:outline-none focus:border-brand-500">
            <option value="">All Statuses</option>
            <option value="Active">Active</option>
            <option value="Onboarding">Onboarding</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>

      <!-- Clients Table -->
      <div class="bg-dark-900 border border-white/10 rounded-3xl overflow-hidden shadow-2xl">
        <table class="w-full text-left text-xs" id="clients-table">
          <thead class="bg-dark-850 text-slate-400 font-mono border-b border-white/5">
            <tr>
              <th class="p-4">Client & Contact</th>
              <th class="p-4">Company</th>
              <th class="p-4">Country</th>
              <th class="p-4">Projects</th>
              <th class="p-4">Settled LTV</th>
              <th class="p-4">Status</th>
              <th class="p-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5" id="clients-tbody">
            <?php if (empty($clients)): ?>
              <tr>
                <td colspan="7" class="p-8 text-center text-slate-500 font-mono">
                  No client accounts found. Click "+ Add New Client" to register one.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($clients as $c): 
                $flag = match($c['country']) {
                  'USA' => '🇺🇸',
                  'Canada' => '🇨🇦',
                  'UAE' => '🇦🇪',
                  'Qatar' => '🇶🇦',
                  default => '🌍'
                };
              ?>
                <tr class="client-row hover:bg-dark-850/50 transition-colors" data-country="<?= htmlspecialchars($c['country']) ?>" data-status="<?= htmlspecialchars($c['status']) ?>">
                  
                  <!-- Client & Email -->
                  <td class="p-4">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-xl bg-brand-500/20 text-brand-accent border border-brand-500/30 flex items-center justify-center font-bold text-xs">
                        <?= strtoupper(substr($c['name'], 0, 2)) ?>
                      </div>
                      <div>
                        <p class="font-bold text-white text-sm"><?= htmlspecialchars($c['name']) ?></p>
                        <p class="text-slate-400 text-[11px] font-mono"><?= htmlspecialchars($c['email']) ?></p>
                      </div>
                    </div>
                  </td>

                  <!-- Company & Phone -->
                  <td class="p-4">
                    <p class="text-slate-200 font-semibold"><?= htmlspecialchars($c['company'] ?: 'Independent Founder') ?></p>
                    <p class="text-slate-500 text-[11px] font-mono"><?= htmlspecialchars($c['phone'] ?: 'No direct phone') ?></p>
                  </td>

                  <!-- Country -->
                  <td class="p-4 font-mono text-slate-300">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-dark-800 border border-white/5 text-xs">
                      <span><?= $flag ?></span>
                      <span><?= htmlspecialchars($c['country']) ?></span>
                    </span>
                  </td>

                  <!-- Projects -->
                  <td class="p-4 font-mono">
                    <span class="px-2 py-0.5 rounded bg-brand-500/20 text-brand-accent text-[11px]">
                      <?= $c['project_count'] ?> Active
                    </span>
                  </td>

                  <!-- Settled LTV -->
                  <td class="p-4 font-mono font-bold text-emerald-400">
                    <?= htmlspecialchars($c['currency']) ?> <?= number_format($c['lifetime_paid'], 2) ?>
                  </td>

                  <!-- Status -->
                  <td class="p-4 font-mono">
                    <?php if ($c['status'] === 'Active'): ?>
                      <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-bold">● Active</span>
                    <?php else: ?>
                      <span class="px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-400 text-[10px]">● <?= htmlspecialchars($c['status']) ?></span>
                    <?php endif; ?>
                  </td>

                  <!-- Actions -->
                  <td class="p-4 text-right">
                    <div class="inline-flex items-center gap-2">
                      <button 
                        onclick='openProfileModal(<?= json_encode($c) ?>)' 
                        class="px-2.5 py-1 rounded-lg bg-dark-800 hover:bg-dark-700 text-slate-200 hover:text-white text-xs font-mono transition-colors"
                        title="360° Client Profile"
                      >
                        Profile
                      </button>
                      
                      <button 
                        onclick='openEditModal(<?= json_encode($c) ?>)' 
                        class="px-2.5 py-1 rounded-lg bg-brand-500/20 hover:bg-brand-500/30 text-brand-accent text-xs font-mono transition-colors"
                        title="Edit Details"
                      >
                        Edit
                      </button>

                      <form method="POST" action="client.php" onsubmit="return confirm('Delete client <?= htmlspecialchars($c['name']) ?>?');" class="inline">
                        <input type="hidden" name="action" value="delete_client">
                        <input type="hidden" name="client_id" value="<?= $c['id'] ?>">
                        <button type="submit" class="p-1 rounded-lg text-slate-500 hover:text-rose-400 text-xs font-mono" title="Delete">
                          🗑️
                        </button>
                      </form>
                    </div>
                  </td>

                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>

  <!-- ==================== 1. ADD CLIENT MODAL ==================== -->
  <div id="add-client-modal" class="hidden fixed inset-0 z-50 bg-dark-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-dark-900 border border-white/20 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative">
      <button onclick="closeModal('add-client-modal')" class="absolute top-6 right-6 text-slate-400 hover:text-white text-xl">✕</button>
      
      <h3 class="text-xl font-bold text-white mb-1">Add New Client Account</h3>
      <p class="text-xs text-slate-400 font-mono mb-6">Register a new founder or enterprise account</p>

      <form method="POST" action="client.php" class="space-y-4 text-xs">
        <input type="hidden" name="action" value="add_client">

        <div>
          <label class="block text-slate-300 font-mono mb-1">Client Full Name *</label>
          <input type="text" name="name" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500" placeholder="e.g. John Doe">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-slate-300 font-mono mb-1">Company Name</label>
            <input type="text" name="company" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500" placeholder="e.g. Acme Corp">
          </div>
          <div>
            <label class="block text-slate-300 font-mono mb-1">Country / Market</label>
            <select name="country" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500">
              <option value="USA">🇺🇸 United States</option>
              <option value="Canada">🇨🇦 Canada</option>
              <option value="UAE">🇦🇪 United Arab Emirates</option>
              <option value="Qatar">🇶🇦 Qatar</option>
              <option value="Other">🌍 International / Other</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-slate-300 font-mono mb-1">Work Email *</label>
            <input type="email" name="email" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500" placeholder="john@company.com">
          </div>
          <div>
            <label class="block text-slate-300 font-mono mb-1">Billing Currency</label>
            <select name="currency" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500">
              <option value="USD">USD ($)</option>
              <option value="CAD">CAD (C$)</option>
              <option value="AED">AED (AED)</option>
              <option value="QAR">QAR (QAR)</option>
              <option value="NPR">NPR (Rs.)</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-slate-300 font-mono mb-1">Direct Phone / WhatsApp</label>
          <input type="text" name="phone" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500" placeholder="+1-555-019-2834">
        </div>

        <div>
          <label class="block text-slate-300 font-mono mb-1">Internal Notes & Context</label>
          <textarea name="notes" rows="2" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500" placeholder="Founder background, project scope notes..."></textarea>
        </div>

        <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs transition-colors mt-4">
          Save & Create Client Account →
        </button>
      </form>
    </div>
  </div>

  <!-- ==================== 2. EDIT CLIENT MODAL ==================== -->
  <div id="edit-client-modal" class="hidden fixed inset-0 z-50 bg-dark-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-dark-900 border border-white/20 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative">
      <button onclick="closeModal('edit-client-modal')" class="absolute top-6 right-6 text-slate-400 hover:text-white text-xl">✕</button>
      
      <h3 class="text-xl font-bold text-white mb-1">Edit Client Profile</h3>
      <p class="text-xs text-slate-400 font-mono mb-6">Update corporate credentials and billing settings</p>

      <form method="POST" action="client.php" class="space-y-4 text-xs">
        <input type="hidden" name="action" value="edit_client">
        <input type="hidden" name="client_id" id="edit-id">

        <div>
          <label class="block text-slate-300 font-mono mb-1">Client Full Name *</label>
          <input type="text" name="name" id="edit-name" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-slate-300 font-mono mb-1">Company Name</label>
            <input type="text" name="company" id="edit-company" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500">
          </div>
          <div>
            <label class="block text-slate-300 font-mono mb-1">Country / Market</label>
            <select name="country" id="edit-country" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500">
              <option value="USA">🇺🇸 United States</option>
              <option value="Canada">🇨🇦 Canada</option>
              <option value="UAE">🇦🇪 United Arab Emirates</option>
              <option value="Qatar">🇶🇦 Qatar</option>
              <option value="Other">🌍 International / Other</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-slate-300 font-mono mb-1">Work Email *</label>
            <input type="email" name="email" id="edit-email" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500">
          </div>
          <div>
            <label class="block text-slate-300 font-mono mb-1">Account Status</label>
            <select name="status" id="edit-status" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500">
              <option value="Active">Active</option>
              <option value="Onboarding">Onboarding</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-slate-300 font-mono mb-1">Direct Phone</label>
            <input type="text" name="phone" id="edit-phone" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500">
          </div>
          <div>
            <label class="block text-slate-300 font-mono mb-1">Currency</label>
            <select name="currency" id="edit-currency" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500">
              <option value="USD">USD ($)</option>
              <option value="CAD">CAD (C$)</option>
              <option value="AED">AED (AED)</option>
              <option value="QAR">QAR (QAR)</option>
              <option value="NPR">NPR (Rs.)</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-slate-300 font-mono mb-1">Internal Notes</label>
          <textarea name="notes" id="edit-notes" rows="2" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500"></textarea>
        </div>

        <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs transition-colors mt-4">
          Update Client Account →
        </button>
      </form>
    </div>
  </div>

  <!-- ==================== 3. 360° CLIENT PROFILE MODAL ==================== -->
  <div id="profile-modal" class="hidden fixed inset-0 z-50 bg-dark-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-dark-900 border border-white/20 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl relative">
      <button onclick="closeModal('profile-modal')" class="absolute top-6 right-6 text-slate-400 hover:text-white text-xl">✕</button>
      
      <div class="flex items-center gap-4 mb-6">
        <div id="prof-avatar" class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-600 to-brand-accent p-0.5 flex items-center justify-center font-bold text-lg text-white">
          --
        </div>
        <div>
          <h3 id="prof-name" class="text-xl font-bold text-white">--</h3>
          <p id="prof-company" class="text-xs text-brand-accent font-mono">--</p>
        </div>
      </div>

      <!-- Quick Metrics Grid -->
      <div class="grid grid-cols-3 gap-3 mb-6 font-mono text-xs">
        <div class="p-3 rounded-xl bg-dark-950 border border-white/5">
          <span class="text-slate-400 text-[10px]">TOTAL LTV</span>
          <p id="prof-ltv" class="text-emerald-400 font-bold mt-0.5">$0.00</p>
        </div>
        <div class="p-3 rounded-xl bg-dark-950 border border-white/5">
          <span class="text-slate-400 text-[10px]">PROJECTS</span>
          <p id="prof-projects" class="text-white font-bold mt-0.5">0 Active</p>
        </div>
        <div class="p-3 rounded-xl bg-dark-950 border border-white/5">
          <span class="text-slate-400 text-[10px]">MARKET</span>
          <p id="prof-country" class="text-purple-300 font-bold mt-0.5">--</p>
        </div>
      </div>

      <!-- Contact Details -->
      <div class="space-y-2 text-xs font-mono text-slate-300 p-4 rounded-xl bg-dark-950 border border-white/5 mb-6">
        <div class="flex justify-between">
          <span class="text-slate-500">Email:</span>
          <span id="prof-email" class="text-white">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Phone:</span>
          <span id="prof-phone" class="text-white">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Currency:</span>
          <span id="prof-currency" class="text-brand-accent">--</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Client Since:</span>
          <span id="prof-date" class="text-slate-400">--</span>
        </div>
      </div>

      <!-- Notes -->
      <div class="mb-6">
        <p class="text-[11px] font-mono text-slate-400 mb-1">INTERNAL STRATEGY NOTES:</p>
        <div id="prof-notes" class="p-3 rounded-xl bg-dark-850 border border-white/5 text-xs text-slate-300 italic">
          No internal notes recorded.
        </div>
      </div>

      <div class="flex gap-3">
        <a href="index.php#invoices" class="flex-1 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs text-center transition-colors">
          + Invoice This Client
        </a>
        <a href="index.php#projects" class="flex-1 py-2.5 bg-dark-800 hover:bg-dark-700 text-slate-200 font-bold rounded-xl text-xs text-center transition-colors">
          + View Projects
        </a>
      </div>

    </div>
  </div>

  <!-- JavaScript Helpers -->
  <script>
    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
    }

    function openAddClientModal() {
      document.getElementById('add-client-modal').classList.remove('hidden');
    }

    function openEditModal(c) {
      document.getElementById('edit-id').value = c.id;
      document.getElementById('edit-name').value = c.name || '';
      document.getElementById('edit-company').value = c.company || '';
      document.getElementById('edit-country').value = c.country || 'USA';
      document.getElementById('edit-email').value = c.email || '';
      document.getElementById('edit-phone').value = c.phone || '';
      document.getElementById('edit-currency').value = c.currency || 'USD';
      document.getElementById('edit-status').value = c.status || 'Active';
      document.getElementById('edit-notes').value = c.notes || '';
      document.getElementById('edit-client-modal').classList.remove('hidden');
    }

    function openProfileModal(c) {
      document.getElementById('prof-avatar').textContent = (c.name || 'CL').substring(0, 2).toUpperCase();
      document.getElementById('prof-name').textContent = c.name;
      document.getElementById('prof-company').textContent = c.company ? c.company + ' • ' + c.country : 'Independent Account';
      document.getElementById('prof-ltv').textContent = (c.currency || 'USD') + ' ' + (parseFloat(c.lifetime_paid) || 0).toLocaleString();
      document.getElementById('prof-projects').textContent = (c.project_count || 0) + ' Projects';
      document.getElementById('prof-country').textContent = c.country || 'Global';
      document.getElementById('prof-email').textContent = c.email || 'N/A';
      document.getElementById('prof-phone').textContent = c.phone || 'N/A';
      document.getElementById('prof-currency').textContent = c.currency || 'USD';
      document.getElementById('prof-date').textContent = c.created_at || 'Recently added';
      document.getElementById('prof-notes').textContent = c.notes ? c.notes : 'No internal notes recorded.';
      document.getElementById('profile-modal').classList.remove('hidden');
    }

    function filterClients() {
      const search = document.getElementById('client-search').value.toLowerCase();
      const country = document.getElementById('country-filter').value;
      const status = document.getElementById('status-filter').value;
      const rows = document.querySelectorAll('.client-row');

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const rowCountry = row.getAttribute('data-country');
        const rowStatus = row.getAttribute('data-status');

        const matchesSearch = text.includes(search);
        const matchesCountry = !country || rowCountry === country;
        const matchesStatus = !status || rowStatus === status;

        if (matchesSearch && matchesCountry && matchesStatus) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

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

    document.getElementById('btn-lock-session').addEventListener('click', () => {
      window.location.href = 'login.php?action=logout';
    });
  </script>
</body>
</html>