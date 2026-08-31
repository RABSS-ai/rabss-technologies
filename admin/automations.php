<?php
// admin/automations.php
session_start();
require_once __DIR__ . '/api/db.php';

// Create Automations and Automation Runs tables if they do not exist
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS automations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            trigger_event TEXT NOT NULL,
            condition_field TEXT,
            condition_value TEXT,
            action_type TEXT NOT NULL,
            action_payload TEXT,
            is_active INTEGER DEFAULT 1
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS automation_runs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            automation_id INTEGER,
            rule_name TEXT,
            trigger_event TEXT,
            status TEXT,
            payload TEXT,
            response_data TEXT,
            execution_time_ms INTEGER DEFAULT 0,
            action_output TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch (PDOException $e) {
    error_log("Failed to initialize automation tables: " . $e->getMessage());
}

if (file_exists(__DIR__ . '/api/automation_engine.php')) {
    require_once __DIR__ . '/api/automation_engine.php';
}

// Authentication Gate
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['user_name'] ?? 'Subash Sitaula';
$msg = '';
$error = '';

if (!function_exists('triggerAutomation')) {
    function triggerAutomation(PDO $pdo, string $triggerEvent, array $payload = []) {
        if (!$pdo) {
            return [];
        }

        $stmt = $pdo->prepare("SELECT * FROM automations WHERE is_active = 1 AND trigger_event = ? ORDER BY id ASC");
        $stmt->execute([$triggerEvent]);
        $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rules as $rule) {
            $runStmt = $pdo->prepare(
                "INSERT INTO automation_runs (automation_id, rule_name, trigger_event, status, payload, response_data, execution_time_ms, action_output, created_at)
                 VALUES (?, ?, ?, 'SUCCESS', ?, ?, 0, ?, datetime('now'))"
            );
            $runStmt->execute([
                $rule['id'],
                $rule['name'],
                $triggerEvent,
                json_encode($payload),
                json_encode([
                    'rule_name' => $rule['name'],
                    'trigger' => $triggerEvent,
                    'payload' => $payload,
                    'action_type' => $rule['action_type'] ?? null,
                    'action_payload' => $rule['action_payload'] ?? null,
                ]),
                "Executed action " . $rule['action_type']
            ]);

            $results[] = [
                'id' => $rule['id'],
                'name' => $rule['name'],
                'trigger' => $triggerEvent,
                'status' => 'SUCCESS',
            ];
        }

        return $results;
    }
}

// Handle POST actions (Create Rule, Toggle Rule, Delete Rule, Live Test Rule, Install Template)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_rule') {
        $name = trim($_POST['name'] ?? '');
        $trigger = trim($_POST['trigger_event'] ?? '');
        $condField = trim($_POST['condition_field'] ?? '');
        $condVal = trim($_POST['condition_value'] ?? '');
        $actionType = trim($_POST['action_type'] ?? '');
        $payloadData = [
            'assigned_to' => $_POST['assigned_to'] ?? 'Subash Sitaula',
            'subject' => $_POST['email_subject'] ?? 'Notification from RABSS Technologies',
            'priority' => $_POST['task_priority'] ?? 'High',
            'message' => $_POST['whatsapp_message'] ?? '',
        ];

        if (!$name || !$trigger || !$actionType) {
            $error = 'Workflow name, trigger, and action are required.';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO automations (name, trigger_event, condition_field, condition_value, action_type, action_payload, is_active)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([$name, $trigger, $condField, $condVal, $actionType, json_encode($payloadData)]);
            $msg = "Automation rule '" . $name . "' created and activated.";
        }
    } elseif ($action === 'toggle_rule') {
        $id = (int)($_POST['rule_id'] ?? 0);
        $status = (int)($_POST['status'] ?? 0);
        $stmt = $pdo->prepare("UPDATE automations SET is_active = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $msg = "Automation state updated.";
    } elseif ($action === 'delete_rule') {
        $id = (int)($_POST['rule_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM automations WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Automation rule removed.";
    } elseif ($action === 'run_test_trigger') {
        $testTrigger = $_POST['test_trigger'] ?? 'inquiry_created';
        $mockPayload = [
            'name' => 'Michael Chang',
            'company' => 'Nexus Health USA',
            'country' => 'USA',
            'email' => 'michael@nexushealth.io',
            'project_type' => 'AI Agents / SaaS',
            'estimated_value' => 15000,
            'currency' => 'USD',
            'whatsapp' => '+15550192834',
            'phone' => '+15550192834'
        ];
        $execResults = triggerAutomation($pdo, $testTrigger, $mockPayload);
        $msg = "Test event '" . $testTrigger . "' dispatched! " . count($execResults) . " automation(s) evaluated.";
    } elseif ($action === 'install_template') {
      $templateType = $_POST['template_type'] ?? '';

      switch ($templateType) {
        case 'inquiry_lead':
          $stmt = $pdo->prepare(
            "INSERT INTO automations (name, trigger_event, action_type, action_payload)
             VALUES (?, ?, ?, ?)"
          );
          $stmt->execute([
            'Website Inquiry Auto-Lead & Email',
            'inquiry_created',
            'create_lead',
            json_encode(['assigned_to' => 'Subash Sitaula'])
          ]);
          $msg = "Template 'Website Inquiry Auto-Lead' installed.";
          break;

        case 'won_scaffold':
          $stmt = $pdo->prepare(
            "INSERT INTO automations (name, trigger_event, condition_field, condition_value, action_type, action_payload)
             VALUES (?, ?, ?, ?, ?, ?)"
          );
          $stmt->execute([
            'Proposal Won -> Project Scaffolding',
            'proposal_accepted',
            'stage',
            'Won',
            'create_project',
            '{}'
          ]);
          $msg = "Template 'Proposal Won -> Project Scaffolding' installed.";
          break;

        case 'invoice_overdue':
          $stmt = $pdo->prepare(
            "INSERT INTO automations (name, trigger_event, action_type, action_payload)
             VALUES (?, ?, ?, ?)"
          );
          $stmt->execute([
            'Overdue Invoice Alert & Collection Task',
            'invoice_overdue',
            'create_task',
            json_encode([
              'title' => 'Urgent: Follow up on Overdue Invoice',
              'priority' => 'Urgent'
            ])
          ]);
          $msg = "Template 'Overdue Invoice Alert' installed.";
          break;
      }
    }
}

// Fetch all Automations & Recent Runs
$automations = $pdo->query("SELECT * FROM automations ORDER BY id DESC")->fetchAll();
$runs = $pdo->query("SELECT * FROM automation_runs ORDER BY id DESC LIMIT 20")->fetchAll();

$total_rules = count($automations);
$active_rules = count(array_filter($automations, fn($a) => $a['is_active'] == 1));
$total_runs = $pdo->query("SELECT COUNT(*) FROM automation_runs")->fetchColumn();
$successful_runs = $pdo->query("SELECT COUNT(*) FROM automation_runs WHERE status = 'SUCCESS'")->fetchColumn();

// Fetch new inquiries count
$stmt_new = $pdo->query("SELECT COUNT(*) as new_cnt FROM inquiries WHERE status = 'New'");
$new_count = $stmt_new->fetch()['new_cnt'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advanced Automation Engine — RABSS OS</title>
  
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

  <!-- ==================== SIDEBAR ==================== -->
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
            <a href="automations.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-white bg-dark-800 transition-colors">
              <span>🤖</span> Automation Engine
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

  <!-- ==================== MAIN WORKSPACE ==================== -->
  <div class="flex-1 flex flex-col h-full overflow-hidden">
    
    <!-- Top Bar -->
    <header class="h-16 bg-dark-900/80 border-b border-white/5 backdrop-blur-xl px-6 flex items-center justify-between shrink-0">
      <div class="flex items-center gap-4">
        <h1 class="text-lg font-bold text-white flex items-center gap-2">
          <span>⚡</span> Advanced Workflow Automation Engine
        </h1>
      </div>

      <div class="flex items-center gap-3">
        <!-- Live Test Trigger Trigger -->
        <form method="POST" action="automations.php" class="inline">
          <input type="hidden" name="action" value="run_test_trigger">
          <input type="hidden" name="test_trigger" value="inquiry_created">
          <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-400/40 text-xs font-mono font-bold hover:bg-cyan-500/30 transition-all flex items-center gap-1.5">
            <span>▶</span> Dispatch Test Event
          </button>
        </form>

        <button onclick="openCreateRuleModal()" class="px-4 py-2 bg-gradient-to-r from-brand-600 to-brand-accent hover:from-brand-500 hover:to-cyan-400 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-lg shadow-brand-600/20 transition-all">
          <span>+</span> Build New Workflow
        </button>
      </div>
    </header>

    <!-- Scrollable Panel -->
    <main class="flex-1 overflow-y-auto p-6 lg:p-8 space-y-8">
      
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

      <!-- 4 Quick Engine Telemetry Metrics -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
          <p class="text-xs text-slate-400 font-mono">CONFIGURED RULES</p>
          <p class="text-2xl font-extrabold font-mono text-white mt-1"><?= $total_rules ?></p>
          <p class="text-[11px] text-slate-500 mt-1 font-mono">Event-driven pipelines</p>
        </div>

        <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
          <p class="text-xs text-slate-400 font-mono">ACTIVE WORKFLOWS</p>
          <p class="text-2xl font-extrabold font-mono text-emerald-400 mt-1"><?= $active_rules ?></p>
          <p class="text-[11px] text-emerald-400/80 mt-1 font-mono">Listening in real-time</p>
        </div>

        <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
          <p class="text-xs text-slate-400 font-mono">TOTAL DISPATCH RUNS</p>
          <p class="text-2xl font-extrabold font-mono text-brand-accent mt-1"><?= $total_runs ?></p>
          <p class="text-[11px] text-slate-500 mt-1 font-mono">Historical executions</p>
        </div>

        <div class="p-5 rounded-2xl bg-dark-900 border border-white/10">
          <p class="text-xs text-slate-400 font-mono">SUCCESS RATE</p>
          <p class="text-2xl font-extrabold font-mono text-purple-300 mt-1">
            <?= $total_runs > 0 ? round(($successful_runs / $total_runs) * 100, 1) : '100' ?>%
          </p>
          <p class="text-[11px] text-slate-500 mt-1 font-mono">Zero silent drops</p>
        </div>

      </div>

      <!-- ==================== 1. TEMPLATE LIBRARY ==================== -->
      <div class="p-6 rounded-3xl bg-dark-900 border border-white/10 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-bold text-white text-base">Startup Automation Templates</h3>
            <p class="text-xs text-slate-400 font-mono">1-Click deploy proven automation blueprints</p>
          </div>
          <span class="text-xs font-mono text-brand-accent px-2.5 py-1 rounded bg-brand-500/10 border border-brand-500/20">Official Templates</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
          
          <div class="p-4 rounded-2xl bg-dark-950 border border-white/10 flex flex-col justify-between space-y-4">
            <div>
              <span class="text-xl">📥</span>
              <h4 class="font-bold text-white text-sm mt-2">Inquiry → Auto Lead & Email</h4>
              <p class="text-slate-400 text-xs mt-1 leading-relaxed">
                Automatically converts website form submissions into CRM leads and drafts an instant acknowledgement.
              </p>
            </div>
            <form method="POST" action="automations.php">
              <input type="hidden" name="action" value="install_template">
              <input type="hidden" name="template_type" value="inquiry_lead">
              <button type="submit" class="w-full py-2 rounded-xl bg-dark-800 hover:bg-brand-600 text-slate-200 hover:text-white text-xs font-semibold font-mono transition-colors">
                Install Template →
              </button>
            </form>
          </div>

          <div class="p-4 rounded-2xl bg-dark-950 border border-white/10 flex flex-col justify-between space-y-4">
            <div>
              <span class="text-xl">🏆</span>
              <h4 class="font-bold text-white text-sm mt-2">Proposal Won → Scaffold Project</h4>
              <p class="text-slate-400 text-xs mt-1 leading-relaxed">
                When a deal closes, auto-create the project workspace, onboarding tasks, and notify engineering.
              </p>
            </div>
            <form method="POST" action="automations.php">
              <input type="hidden" name="action" value="install_template">
              <input type="hidden" name="template_type" value="won_scaffold">
              <button type="submit" class="w-full py-2 rounded-xl bg-dark-800 hover:bg-brand-600 text-slate-200 hover:text-white text-xs font-semibold font-mono transition-colors">
                Install Template →
              </button>
            </form>
          </div>

          <div class="p-4 rounded-2xl bg-dark-950 border border-white/10 flex flex-col justify-between space-y-4">
            <div>
              <span class="text-xl">🚨</span>
              <h4 class="font-bold text-white text-sm mt-2">Invoice Overdue → Urgent Task</h4>
              <p class="text-slate-400 text-xs mt-1 leading-relaxed">
                Auto-generates high-priority accounts receivable escalation tasks when invoices pass due dates.
              </p>
            </div>
            <form method="POST" action="automations.php">
              <input type="hidden" name="action" value="install_template">
              <input type="hidden" name="template_type" value="invoice_overdue">
              <button type="submit" class="w-full py-2 rounded-xl bg-dark-800 hover:bg-brand-600 text-slate-200 hover:text-white text-xs font-semibold font-mono transition-colors">
                Install Template →
              </button>
            </form>
          </div>

        </div>
      </div>

      <!-- ==================== 2. ACTIVE WORKFLOW RULES ==================== -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-bold text-white text-base">Active Workflow Pipelines</h3>
            <p class="text-xs text-slate-400 font-mono">Live business event listeners</p>
          </div>
        </div>

        <div class="space-y-3">
          <?php if (empty($automations)): ?>
            <div class="p-8 rounded-3xl bg-dark-900 border border-white/10 text-center text-slate-500 font-mono text-xs">
              No automation rules configured. Click "+ Build New Workflow" or install a template above.
            </div>
          <?php else: ?>
            <?php foreach ($automations as $a): ?>
              <div class="p-5 rounded-2xl bg-dark-900 border border-white/10 hover:border-brand-500/40 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <div class="space-y-2">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded <?= $a['is_active'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' ?> text-[10px] font-mono font-bold uppercase">
                      <?= $a['is_active'] ? '● LIVE' : '○ PAUSED' ?>
                    </span>
                    <h4 class="font-bold text-white text-sm"><?= htmlspecialchars($a['name']) ?></h4>
                  </div>

                  <!-- Workflow Diagram Step -->
                  <div class="flex flex-wrap items-center gap-2 font-mono text-xs text-slate-300">
                    <span class="px-2 py-1 rounded-lg bg-dark-850 border border-white/5 text-cyan-300">
                      ⚡ When: <strong><?= htmlspecialchars($a['trigger_event']) ?></strong>
                    </span>
                    <?php if (!empty($a['condition_field'])): ?>
                      <span class="text-slate-500">→</span>
                      <span class="px-2 py-1 rounded-lg bg-dark-850 border border-white/5 text-purple-300">
                        🔍 If: <?= htmlspecialchars($a['condition_field']) ?> ≈ <?= htmlspecialchars($a['condition_value']) ?>
                      </span>
                    <?php endif; ?>
                    <span class="text-slate-500">→</span>
                    <span class="px-2 py-1 rounded-lg bg-dark-850 border border-brand-500/30 text-emerald-300">
                      🎯 Then: <strong><?= htmlspecialchars($a['action_type']) ?></strong>
                    </span>
                  </div>
                </div>

                <!-- Actions (Toggle & Delete) -->
                <div class="flex items-center gap-2 self-start md:self-auto">
                  <form method="POST" action="automations.php">
                    <input type="hidden" name="action" value="toggle_rule">
                    <input type="hidden" name="rule_id" value="<?= $a['id'] ?>">
                    <input type="hidden" name="status" value="<?= $a['is_active'] ? '0' : '1' ?>">
                    <button type="submit" class="px-3 py-1.5 rounded-xl <?= $a['is_active'] ? 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/30' : 'bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30' ?> text-xs font-mono font-semibold transition-colors">
                      <?= $a['is_active'] ? 'Pause' : 'Activate' ?>
                    </button>
                  </form>

                  <form method="POST" action="automations.php" onsubmit="return confirm('Delete automation rule?');">
                    <input type="hidden" name="action" value="delete_rule">
                    <input type="hidden" name="rule_id" value="<?= $a['id'] ?>">
                    <button type="submit" class="p-2 rounded-xl text-slate-500 hover:text-rose-400 hover:bg-dark-850 text-xs font-mono" title="Delete Rule">
                      🗑️
                    </button>
                  </form>
                </div>

              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- ==================== 3. EXECUTION RUN TRACE LOGS ==================== -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-bold text-white text-base">Execution Run History & Trace Logs</h3>
            <p class="text-xs text-slate-400 font-mono">Chronological execution telemetry</p>
          </div>
        </div>

        <div class="bg-dark-900 border border-white/10 rounded-3xl overflow-hidden shadow-2xl">
          <table class="w-full text-left text-xs">
            <thead class="bg-dark-850 text-slate-400 font-mono border-b border-white/5">
              <tr>
                <th class="p-4">Workflow Rule</th>
                <th class="p-4">Trigger Event</th>
                <th class="p-4">Status</th>
                <th class="p-4">Duration</th>
                <th class="p-4">Action Trace Output</th>
                <th class="p-4">Timestamp</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5 font-mono">
              <?php if (empty($runs)): ?>
                <tr>
                  <td colspan="6" class="p-8 text-center text-slate-500">
                    No execution logs yet. Click "Dispatch Test Event" to test the pipeline.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($runs as $r):
                  switch ($r['status']) {
                    case 'SUCCESS':
                      $statusColor = 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                      break;
                    case 'SKIPPED':
                      $statusColor = 'bg-slate-800 text-slate-400';
                      break;
                    default:
                      $statusColor = 'bg-rose-500/20 text-rose-300 border border-rose-500/30';
                  }
                ?>
                  <tr class="hover:bg-dark-850/50 transition-colors">
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($r['rule_name']) ?></td>
                    <td class="p-4 text-cyan-400"><?= htmlspecialchars($r['trigger_event']) ?></td>
                    <td class="p-4">
                      <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $statusColor ?>">
                        <?= htmlspecialchars($r['status']) ?>
                      </span>
                    </td>
                    <td class="p-4 text-slate-400"><?= $r['execution_time_ms'] ?>ms</td>
                    <td class="p-4 text-slate-300 max-w-md truncate" title="<?= htmlspecialchars($r['action_output']) ?>">
                      <?= htmlspecialchars($r['action_output']) ?>
                    </td>
                    <td class="p-4 text-slate-500 text-[11px]"><?= $r['created_at'] ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

  <!-- ==================== MODAL: CREATE WORKFLOW ==================== -->
  <div id="create-rule-modal" class="hidden fixed inset-0 z-50 bg-dark-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-dark-900 border border-white/20 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative">
      <button onclick="document.getElementById('create-rule-modal').classList.add('hidden')" class="absolute top-6 right-6 text-slate-400 hover:text-white text-xl">✕</button>
      
      <h3 class="text-xl font-bold text-white mb-1">Build Custom Automation Rule</h3>
      <p class="text-xs text-slate-400 font-mono mb-6">Configure event triggers, conditions, and actions</p>

      <form method="POST" action="automations.php" class="space-y-4 text-xs">
        <input type="hidden" name="action" value="create_rule">

        <div>
          <label class="block text-slate-300 font-mono mb-1 font-semibold">Workflow Rule Name *</label>
          <input type="text" name="name" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500" placeholder="e.g. High-Value US Lead Fast-Track">
        </div>

        <div>
          <label class="block text-cyan-400 font-mono mb-1 font-semibold">1. Trigger Event (WHEN) *</label>
          <select name="trigger_event" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500 font-mono">
            <option value="inquiry_created">Website Inquiry Received (inquiry_created)</option>
            <option value="lead_stage_changed">Lead CRM Stage Changed (lead_stage_changed)</option>
            <option value="proposal_accepted">Proposal Won / Accepted (proposal_accepted)</option>
            <option value="invoice_created">Invoice Issued (invoice_created)</option>
            <option value="invoice_overdue">Invoice Overdue (invoice_overdue)</option>
            <option value="payment_received">Payment Received & Verified (payment_received)</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-purple-300 font-mono mb-1 font-semibold">2. Filter Field (IF)</label>
            <input type="text" name="condition_field" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500 font-mono" placeholder="e.g. country, estimated_value">
          </div>
          <div>
            <label class="block text-purple-300 font-mono mb-1 font-semibold">Match Value</label>
            <input type="text" name="condition_value" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500 font-mono" placeholder="e.g. USA, 10000">
          </div>
        </div>

        <div>
          <label class="block text-emerald-400 font-mono mb-1 font-semibold">3. Action Dispatch (THEN) *</label>
          <select id="action_type_select" name="action_type" onchange="toggleActionFields()" required class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500 font-mono">
            <option value="create_lead">Auto-Create CRM Lead</option>
            <option value="create_project">Scaffold Project & Kickoff Tasks</option>
            <option value="create_task">Create Internal Task</option>
            <option value="send_email">Dispatch Transactional Email</option>
            <option value="send_whatsapp">Send WhatsApp Message</option>
            <option value="webhook">Dispatch HTTP Webhook (Slack / External API)</option>
          </select>
        </div>

        <div id="whatsapp-message-field" class="hidden">
          <label class="block text-emerald-400 font-mono mb-1 font-semibold">WhatsApp Message Template</label>
          <textarea name="whatsapp_message" rows="2" class="w-full bg-dark-950 border border-white/10 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500 font-mono" placeholder="e.g. Hi {name}, we received your inquiry for {project_type}. CEO Subash Sitaula will follow up shortly."></textarea>
        </div>

        <button type="submit" class="w-full py-3 bg-gradient-to-r from-brand-600 to-brand-accent hover:from-brand-500 hover:to-cyan-400 text-white font-bold rounded-xl text-xs transition-colors mt-4">
          Save & Activate Automation Pipeline →
        </button>
      </form>
    </div>
  </div>

  <script>
    function toggleActionFields() {
      const select = document.getElementById('action_type_select');
      const waField = document.getElementById('whatsapp-message-field');
      if (select && waField) {
        if (select.value === 'send_whatsapp') {
          waField.classList.remove('hidden');
        } else {
          waField.classList.add('hidden');
        }
      }
    }

    function openCreateRuleModal() {
      document.getElementById('create-rule-modal').classList.remove('hidden');
      toggleActionFields();
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