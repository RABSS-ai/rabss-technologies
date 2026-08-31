<?php
// admin/meeting.php
session_start();
require_once __DIR__ . '/api/db.php';

$is_admin = isset($_SESSION['user_id']);
$room_param = $_GET['room'] ?? '';
$is_valid_room = false;
$meeting_details = null;

if (!empty($room_param)) {
    try {
        $stmt_room = $pdo->prepare("SELECT * FROM meetings WHERE room_id = ?");
        $stmt_room->execute([$room_param]);
        $meeting_details = $stmt_room->fetch();
        if ($meeting_details) {
            $is_valid_room = true;
        }
    } catch (PDOException $e) {
        error_log("Error validating room: " . $e->getMessage());
    }
}

// Auth Protection: If not admin and not joining a valid room, redirect to login
if (!$is_admin && !$is_valid_room) {
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['user_name'] ?? 'Guest User';
$msg = '';
$error = '';

// Fetch new inquiries count
try {
    $stmt_new = $pdo->query("SELECT COUNT(*) as new_cnt FROM inquiries WHERE status = 'New'");
    $new_count = $stmt_new->fetch()['new_cnt'] ?? 0;
} catch (PDOException $e) {
    $new_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Video Meetings Room — RABSS OS</title>
  <link rel="icon" type="image/jpeg" href="../logo.jpg">
  
  <!-- Tailwind CSS & Google Fonts -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              400: '#818CF8',
              500: '#6366F1',
              600: '#4F46E5',
              accent: '#06B6D4',
              glow: '#4F46E525'
            },
            dark: {
              950: '#07090E',
              900: '#0C0F17',
              850: '#111622',
              800: '#181F30',
              700: '#222B3F'
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
  
  <!-- Load Socket.io Client -->
  <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/admin.css">
  
  <style>
    body {
      background-color: #05070a;
      background-image: 
        radial-gradient(at 0% 100%, rgba(99, 102, 241, 0.06) 0px, transparent 50%),
        radial-gradient(at 100% 0%, rgba(6, 182, 212, 0.06) 0px, transparent 50%);
    }
    
    .premium-glass {
      background: rgba(12, 15, 23, 0.7);
      backdrop-filter: blur(24px) saturate(180%);
      -webkit-backdrop-filter: blur(24px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.07);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .glow-border {
      position: relative;
    }
    .glow-border::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: inherit;
      padding: 1.5px;
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.35), rgba(6, 182, 212, 0.15), transparent 60%);
      mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      pointer-events: none;
    }

    .premium-input {
      background: rgba(7, 9, 14, 0.8) !important;
      border: 1px solid rgba(255, 255, 255, 0.08) !important;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .premium-input:focus {
      border-color: rgba(99, 102, 241, 0.6) !important;
      box-shadow: 0 0 15px rgba(99, 102, 241, 0.15) !important;
      background: rgba(12, 15, 23, 0.95) !important;
    }

    .cyber-frame {
      position: relative;
    }
    .cyber-frame::after {
      content: '';
      position: absolute;
      inset: 0;
      border: 1px solid rgba(6, 182, 212, 0.15);
      border-radius: inherit;
      pointer-events: none;
    }
    /* Cyber Tech Brackets */
    .cyber-corners::before, .cyber-corners::after {
      content: '';
      position: absolute;
      width: 12px;
      height: 12px;
      border-color: rgba(6, 182, 212, 0.6);
      border-style: solid;
      pointer-events: none;
      z-index: 10;
    }
    .cyber-corners::before {
      top: -1px;
      left: -1px;
      border-width: 2px 0 0 2px;
    }
    .cyber-corners::after {
      bottom: -1px;
      right: -1px;
      border-width: 0 2px 2px 0;
    }

    .glass-dock {
      background: rgba(15, 22, 36, 0.85);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.06);
      box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
    }

    .chat-bubble-card {
      padding: 0.75rem 1rem;
      border-radius: 1.25rem;
      background: rgba(24, 31, 48, 0.35);
      border: 1px solid rgba(255, 255, 255, 0.04);
      margin-bottom: 0.75rem;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .chat-bubble-card.self-sent {
      background: rgba(99, 102, 241, 0.1);
      border-color: rgba(99, 102, 241, 0.2);
    }

    .b-meta {
      display: flex;
      justify-content: space-between;
      font-size: 10px;
      color: rgba(255, 255, 255, 0.4);
      margin-bottom: 4px;
    }

    .b-sender {
      font-weight: 700;
      color: rgba(255, 255, 255, 0.8);
    }

    .b-text {
      color: #f1f5f9;
      word-break: break-word;
    }

    @keyframes scanline {
      0% { transform: translateY(-100%); }
      100% { transform: translateY(100%); }
    }

    .scan-line::after {
      content: "";
      position: absolute;
      width: 100%;
      height: 2px;
      background: linear-gradient(to right, transparent, rgba(6, 182, 212, 0.3), transparent);
      animation: scanline 4s linear infinite;
    }

    /* Micro Scrollbar */
    .micro-scrollbar::-webkit-scrollbar {
      width: 4px;
      height: 4px;
    }
    .micro-scrollbar::-webkit-scrollbar-track {
      background: transparent;
    }
    .micro-scrollbar::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 99px;
    }
    .micro-scrollbar::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    /* GPU-Optimized Fullscreen Performance tweaks to prevent lags and hangs */
    :fullscreen, :-webkit-full-screen {
      background-color: #000000 !important;
    }
    .cyber-frame:fullscreen, 
    .cyber-frame:-webkit-full-screen {
      border: none !important;
      border-radius: 0px !important;
      box-shadow: none !important;
      padding: 0 !important;
      background: #000000 !important;
    }
    .cyber-frame:fullscreen::before,
    .cyber-frame:fullscreen::after,
    .cyber-frame:-webkit-full-screen::before,
    .cyber-frame:-webkit-full-screen::after,
    .cyber-corners:fullscreen::before,
    .cyber-corners:fullscreen::after,
    .cyber-corners:-webkit-full-screen::before,
    .cyber-corners:-webkit-full-screen::after {
      display: none !important;
      content: none !important;
      animation: none !important;
    }
  </style>
</head>
<body class="text-slate-100 font-sans antialiased selection:bg-brand-500 selection:text-white flex h-screen overflow-hidden">

  <?php if ($is_admin): ?>
  <!-- ==================== SIDEBAR ==================== -->
  <aside id="sidebar" class="w-64 bg-dark-900 border-r border-white/5 flex flex-col justify-between transition-all duration-300 z-30 shrink-0">
    <div class="p-4 flex flex-col h-full overflow-y-auto">
      <div class="flex items-center gap-3 px-2 py-3 mb-4 border-b border-white/5">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-brand-accent p-[1.5px] shadow-lg shadow-brand-500/20 flex items-center justify-center overflow-hidden">
          <img src="../logo.jpg" alt="RABSS Technologies" class="w-full h-full object-cover rounded-[10px]">
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
            <a href="meeting.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-white bg-dark-800 transition-colors">
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
            <a href="automations.php" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-slate-300 hover:text-white hover:bg-dark-800 transition-colors">
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
  <?php endif; ?>

  <!-- ==================== MAIN PANEL ==================== -->
  <div class="flex-1 flex flex-col h-full overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="absolute top-1/4 left-1/3 w-[500px] h-[500px] bg-brand-600/5 blur-[130px] rounded-full pointer-events-none z-0"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[400px] h-[400px] bg-cyan-500/3 blur-[120px] rounded-full pointer-events-none z-0"></div>

    <header class="h-16 bg-dark-900/40 border-b border-white/5 backdrop-blur-md px-6 flex items-center justify-between shrink-0 z-10">
      <h1 class="text-lg font-bold text-white flex items-center gap-2">
        <span class="text-brand-400">🤝</span> <?= $is_admin ? "Interactive Video Meetings Suite" : "RABSS Technologies Video Meeting" ?>
      </h1>
      <div class="flex items-center gap-3">
        <?php if ($is_admin): ?>
         
        <?php endif; ?>
        <span class="px-2.5 py-1 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-mono font-bold flex items-center gap-1.5 shadow-sm">
          <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span> WebRTC P2P
        </span>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto p-6 lg:p-8 space-y-6 flex flex-col relative z-10">
      <div id="meeting-grid" class="grid grid-cols-1 gap-6 flex-1 min-h-0 max-w-md mx-auto w-full transition-all duration-500">
        
        <!-- Left Side: Custom Video Call Window Layout -->
        <div id="video-container" class="hidden lg:col-span-8 flex flex-col premium-glass glow-border rounded-[24px] overflow-hidden p-4 space-y-4 shadow-2xl">
          <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 min-h-0 relative">
            <!-- Local Stream Window -->
            <div class="group bg-black/60 border border-white/10 rounded-2xl overflow-hidden relative flex items-center justify-center aspect-video md:aspect-auto shadow-inner hover:border-brand-500/40 transition-all duration-300 scan-line cyber-frame cyber-corners">
              <div class="absolute inset-0 bg-gradient-to-t from-dark-950/90 via-transparent to-transparent z-10 pointer-events-none"></div>
              <video id="local-video" autoplay playsinline muted class="w-full h-full object-cover scale-x-[-1]"></video>
              <!-- Avatar Placeholder for Camera Off -->
              <div id="local-avatar-placeholder" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-dark-950/90 z-10">
                <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-brand-600 to-brand-accent p-[1.5px] animate-pulse">
                  <div class="w-full h-full bg-dark-900 rounded-full flex items-center justify-center font-bold text-lg text-white">
                    <?= htmlspecialchars(strtoupper(substr($user_name, 0, 2))) ?>
                  </div>
                </div>
                <span class="text-[11px] font-mono text-slate-400 mt-3 flex items-center gap-1.5">
                  📹 Camera Disabled
                </span>
              </div>
              <!-- Screen Share Active Overlay -->
              <div id="local-screenshare-placeholder" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-dark-950/95 z-10">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-cyan-500 to-brand-accent p-[1.5px] animate-pulse">
                  <div class="w-full h-full bg-dark-900 rounded-2xl flex items-center justify-center text-xl text-white">
                    🖥️
                  </div>
                </div>
                <span class="text-[12px] font-mono text-cyan-400 mt-3 font-bold">Screen Sharing Active</span>
                <span class="text-[10px] text-slate-500 font-mono mt-1">To prevent feedback loops, local rendering is paused</span>
              </div>
              <span class="absolute bottom-4 left-4 z-20 bg-dark-900/90 border border-white/10 px-3 py-1 rounded-lg text-xs font-mono text-white shadow-lg flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Local (You) <span id="local-mute-badge" class="hidden text-rose-400 font-bold ml-1">🔇 Muted</span>
              </span>
            </div>
            <!-- Remote Stream Window -->
            <div class="group bg-black/60 border border-white/10 rounded-2xl overflow-hidden relative flex items-center justify-center aspect-video md:aspect-auto shadow-inner hover:border-brand-500/40 transition-all duration-300 scan-line cyber-frame cyber-corners">
              <div class="absolute inset-0 bg-gradient-to-t from-dark-950/90 via-transparent to-transparent z-10 pointer-events-none"></div>
              <video id="remote-video" autoplay playsinline class="w-full h-full object-cover"></video>
              <!-- Avatar Placeholder for Remote Camera Off -->
              <div id="remote-avatar-placeholder" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-dark-950/90 z-10">
                <div class="w-16 h-16 rounded-full bg-slate-800 border border-white/10 flex items-center justify-center">
                  <span class="text-slate-400 text-lg">👤</span>
                </div>
                <span class="text-[11px] font-mono text-slate-500 mt-3">Waiting for Remote Video...</span>
              </div>
              <span class="absolute bottom-4 left-4 z-20 bg-dark-900/90 border border-white/10 px-3 py-1 rounded-lg text-xs font-mono text-brand-accent shadow-lg flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span> Remote Peer
              </span>
            </div>
          </div>

          <!-- Active Control Bar -->
          <div class="flex items-center justify-center gap-3 glass-dock p-3 rounded-2xl max-w-xl mx-auto w-full">
            <button id="btn-toggle-mic" class="px-4 py-2.5 bg-dark-800 hover:bg-brand-500/10 hover:text-brand-400 border border-white/10 hover:border-brand-500/30 rounded-xl text-xs font-mono text-slate-300 transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
              <span>🎙️ Mute mic</span>
            </button>
            <button id="btn-toggle-cam" class="px-4 py-2.5 bg-dark-800 hover:bg-brand-500/10 hover:text-brand-400 border border-white/10 hover:border-brand-500/30 rounded-xl text-xs font-mono text-slate-300 transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
              <span>📹 Disable Cam</span>
            </button>
            <button id="btn-share-screen" class="px-4 py-2.5 bg-dark-800 hover:bg-brand-500/10 hover:text-brand-400 border border-white/10 hover:border-brand-500/30 rounded-xl text-xs font-mono text-slate-300 transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <span>🖥️ Share Screen</span>
            </button>
            <button id="btn-voice-rec" class="px-4 py-2.5 bg-dark-800 hover:bg-brand-500/10 hover:text-brand-400 border border-white/10 hover:border-brand-500/30 rounded-xl text-xs font-mono text-slate-300 transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
              <span>🎤 Start Voice AI</span>
            </button>
            <button id="btn-toggle-chat" class="hidden px-4 py-2.5 bg-dark-800 hover:bg-brand-500/10 hover:text-brand-400 border border-white/10 hover:border-brand-500/30 rounded-xl text-xs font-mono text-slate-300 transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
              <span>💬 Hide Chat</span>
            </button>
            <button id="btn-leave" class="px-5 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/25 rounded-xl text-xs font-mono transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-rose-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
              <span>Leave Call</span>
            </button>
          </div>
        </div>

        <!-- Right Side: Room Connection Details & Chat -->
        <div id="right-sidebar" class="lg:col-span-12 premium-glass glow-border rounded-[24px] p-6 flex flex-col justify-between max-h-[85vh] shadow-2xl w-full z-10">
          <div class="space-y-6 overflow-y-auto flex-1 pr-1 micro-scrollbar" id="connection-controls">
            <?php if ($is_admin): ?>
              <!-- Tab selector -->
              <div class="flex gap-1.5 p-1 bg-dark-950 border border-white/5 rounded-xl text-xs">
                <button id="tab-btn-schedule" class="flex-1 px-3 py-2 rounded-lg bg-gradient-to-r from-brand-600 to-brand-accent text-white font-bold font-mono transition-all duration-300 shadow-md shadow-brand-500/10">Schedule</button>
                <button id="tab-btn-list" class="flex-1 px-3 py-2 rounded-lg hover:bg-white/5 border border-transparent text-slate-400 font-bold font-mono transition-all duration-300">Invites</button>
                <button id="tab-btn-manual" class="flex-1 px-3 py-2 rounded-lg hover:bg-white/5 border border-transparent text-slate-400 font-bold font-mono transition-all duration-300">Manual</button>
              </div>

              <!-- Tab 1: Schedule -->
              <div id="tab-schedule" class="space-y-4">
                <div>
                  <h3 class="text-base font-bold text-white mb-1">Schedule Video Session</h3>
                  <p class="text-[11px] text-slate-400 font-mono">Create an invitation link and notify recipient</p>
                </div>

                <div class="space-y-3 text-xs">
                  <div>
                    <label class="block text-slate-400 font-mono mb-1.5 text-[10px] tracking-wider uppercase font-bold">MEETING TITLE</label>
                    <input type="text" id="sched-title" class="w-full premium-input rounded-xl px-3.5 py-2.5 text-slate-200 font-mono" placeholder="e.g. Code Review with Client">
                  </div>
                  <div>
                    <label class="block text-slate-400 font-mono mb-1.5 text-[10px] tracking-wider uppercase font-bold">INVITEE NAME</label>
                    <input type="text" id="sched-name" class="w-full premium-input rounded-xl px-3.5 py-2.5 text-slate-200 font-mono" placeholder="e.g. John Doe">
                  </div>
                  <div>
                    <label class="block text-slate-400 font-mono mb-1.5 text-[10px] tracking-wider uppercase font-bold">INVITEE EMAIL</label>
                    <input type="email" id="sched-email" class="w-full premium-input rounded-xl px-3.5 py-2.5 text-slate-200 font-mono" placeholder="e.g. john@example.com">
                  </div>
                  <div>
                    <label class="block text-slate-400 font-mono mb-1.5 text-[10px] tracking-wider uppercase font-bold">SCHEDULED AT</label>
                    <input type="datetime-local" id="sched-time" class="w-full premium-input rounded-xl px-3.5 py-2.5 text-slate-200 font-mono">
                  </div>
                  <button id="btn-schedule-meeting" class="w-full py-3 bg-gradient-to-r from-brand-600 via-indigo-600 to-brand-accent hover:from-brand-500 hover:to-cyan-400 text-white font-bold rounded-xl text-xs transition-colors flex items-center justify-center gap-2 shadow-lg shadow-brand-600/20">
                    📅 Schedule & Send Invitation
                  </button>
                </div>
              </div>

              <!-- Tab 2: Invites List -->
              <div id="tab-list" class="hidden space-y-4">
                <div>
                  <h3 class="text-base font-bold text-white mb-1">Scheduled Invites</h3>
                  <p class="text-[11px] text-slate-400 font-mono">Manage and join active scheduled sessions</p>
                </div>
                <div id="meetings-list-container" class="space-y-2.5 max-h-[40vh] overflow-y-auto pr-1 micro-scrollbar">
                  <!-- Dynamically populated -->
                </div>
              </div>

              <!-- Tab 3: Manual Connect -->
              <div id="tab-manual" class="hidden space-y-4">
                <div>
                  <h3 class="text-base font-bold text-white mb-1">Direct Connection</h3>
                  <p class="text-[11px] text-slate-400 font-mono">Input manual room configuration</p>
                </div>

                <div class="space-y-3 text-xs">
                  <div>
                    <label class="block text-slate-400 font-mono mb-1.5 font-bold">SIGNALING SERVER URL</label>
                    <input type="text" id="socket-url" class="w-full premium-input rounded-xl px-3.5 py-2.5 text-slate-200 font-mono" value="http://localhost:3000">
                  </div>
                  <div>
                    <label class="block text-slate-400 font-mono mb-1.5 font-bold">MEETING ROOM ID</label>
                    <input type="text" id="meeting-room" class="w-full premium-input rounded-xl px-3.5 py-2.5 text-slate-200 font-mono" placeholder="e.g. client-sprint-call">
                  </div>
                  <button id="btn-connect" class="w-full py-3 bg-gradient-to-r from-brand-600 via-indigo-600 to-brand-accent hover:from-brand-500 hover:to-cyan-400 text-white font-bold rounded-xl text-xs transition-colors flex items-center justify-center gap-2">
                    ⚡ Connect Manually
                  </button>
                </div>
              </div>
            <?php else: ?>
              <!-- Guest Welcome Details -->
              <div class="space-y-5">
                <div>
                  <span class="text-2xl">👋</span>
                  <h3 class="text-base font-bold text-white mt-2">Welcome, <?= htmlspecialchars($meeting_details['invitee_name'] ?? 'Guest') ?>!</h3>
                  <p class="text-[11px] text-slate-400 font-mono mt-1">You are invited to join this secure video session.</p>
                </div>
                
                <div class="p-5 bg-dark-950/80 border border-white/10 rounded-2xl space-y-4 text-xs font-mono shadow-xl relative overflow-hidden">
                  <div class="absolute top-0 right-0 w-24 h-24 bg-brand-500/10 blur-xl rounded-full pointer-events-none"></div>
                  <div>
                    <span class="text-slate-500 block text-[10px] tracking-wider uppercase font-bold">MEETING SUBJECT</span>
                    <span class="text-white font-bold text-sm"><?= htmlspecialchars($meeting_details['title'] ?? 'Secure Discussion') ?></span>
                  </div>
                  <div>
                    <span class="text-slate-500 block text-[10px] tracking-wider uppercase font-bold">HOST</span>
                    <span class="text-brand-accent font-bold flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span> Subash Sitaula (CEO)</span>
                  </div>
                  <div>
                    <span class="text-slate-500 block text-[10px] tracking-wider uppercase font-bold">SCHEDULED TIME</span>
                    <span class="text-amber-400 font-semibold"><?= htmlspecialchars($meeting_details['scheduled_at'] ?? 'Now') ?></span>
                  </div>
                </div>

                <!-- Invisible helper inputs keeping javascript fully happy -->
                <input type="hidden" id="socket-url" value="http://localhost:3000">
                <input type="hidden" id="meeting-room" value="<?= htmlspecialchars($room_param) ?>">
                <button id="btn-connect" class="w-full py-4 bg-gradient-to-r from-brand-600 via-indigo-500 to-brand-accent hover:from-brand-500 hover:to-cyan-400 text-white font-bold rounded-xl text-xs transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-brand-600/25">
                  ⚡ Join Secure Room Now
                </button>
              </div>
            <?php endif; ?>
          </div>

          <!-- Real-time Room Chat Container (Strictly keeps original layout IDs) -->
          <div id="chat-container" class="hidden flex flex-col h-full justify-between space-y-4 min-h-0">
            <div class="border-b border-white/5 pb-3 shrink-0">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-brand-400">💬</span> Meeting Chat
                  </h3>
                  <p class="text-[10px] text-slate-400 font-mono mt-0.5">Secure real-time end-to-end corridor</p>
                </div>
                <span class="px-1.5 py-0.5 rounded bg-brand-500/15 border border-brand-500/20 text-[9px] font-mono text-brand-400">P2P Encrypted</span>
              </div>
            </div>
            <div id="chat-messages" class="flex-1 overflow-y-auto space-y-3 pr-1 text-xs font-mono min-h-0 micro-scrollbar">
              <!-- Chat messages will be added dynamically by script -->
            </div>

          <!-- AI Copilot Suggestion Box (Host Only) -->
          <div id="ai-copilot-box" class="hidden p-3 rounded-xl bg-brand-500/10 border border-brand-500/30 text-xs text-slate-300 font-mono space-y-1.5 shrink-0">
            <div class="flex items-center justify-between text-brand-400 font-bold text-[10px]">
              <span class="flex items-center gap-1">✨ AI COPILOT SUGGESTED ANSWER (HOST ONLY)</span>
              <div class="flex gap-2">
                <button type="button" id="btn-use-suggestion" class="text-cyan-400 hover:text-cyan-300 hover:underline">Insert Answer</button>
                <button type="button" id="btn-close-suggestion" class="text-slate-500 hover:text-slate-300">✕</button>
              </div>
            </div>
            <p id="ai-suggestion-text" class="text-[11px] leading-relaxed italic text-slate-200"></p>
          </div>

            <form id="chat-form" class="flex gap-2 shrink-0 pt-2.5 border-t border-white/5">
              <input type="text" id="chat-input" class="flex-1 premium-input rounded-xl px-4 py-3 text-xs text-slate-200 font-mono" placeholder="Type secure message..." required>
              <button type="submit" class="px-5 py-3 bg-gradient-to-r from-brand-600 to-brand-accent hover:from-brand-500 hover:to-cyan-400 text-white font-bold rounded-xl text-xs font-mono transition-all duration-300 shadow-md shadow-brand-500/10 shrink-0">Send</button>
            </form>
          </div>

          <!-- Status Indicator Panel -->
          <div id="status-panel" class="mt-6 p-3.5 rounded-xl bg-dark-950/80 border border-white/5 font-mono text-[11px] space-y-2 shrink-0 shadow-inner">
            <div class="flex justify-between">
              <span class="text-slate-500">Signaling Status:</span>
              <span id="sig-status" class="text-rose-400 font-bold flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Offline
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">Media Input:</span>
              <span id="media-status" class="text-slate-400 font-medium">Not Requested</span>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- WebRTC Custom Meeting Script -->
  <script>
    let localStream;
    let remoteStream;
    let peerConnection;
    let socket;
    let dataChannel; // Added for true peer-to-peer chat peering
    let candidateQueue = [];
    let isNegotiating = false;
    let screenStream = null;
    let isScreenSharing = false;
    let currentPeerId = null;
    let isListeningRemoteByHost = false;
    const myName = "<?= htmlspecialchars($user_name, ENT_QUOTES) ?>";
    
    const isAdmin = <?= $is_admin ? 'true' : 'false' ?>;
    const iceConfig = {
      iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
      ]
    };

    const localVideo = document.getElementById('local-video');
    const remoteVideo = document.getElementById('remote-video');

    // Toggle Fullscreen on Double Click
    function toggleFullscreen(videoElement) {
      if (isScreenSharing && videoElement === localVideo) {
        alert("Full-screen is disabled on the local screen-share preview to prevent infinite recursive feedback loops and system hangs.");
        return;
      }

      const container = videoElement.parentElement; // Fullscreen the container to avoid native raw video element rendering thread deadlocks
      if (!document.fullscreenElement) {
        // Temporarily disable performance-heavy scan-line animation on full screen
        const hasScanLine = container.classList.contains('scan-line');
        if (hasScanLine) {
          container.classList.remove('scan-line');
          container.dataset.hadScanLine = "true";
        }

        container.requestFullscreen().catch(err => {
          console.error(`Error attempting to enable fullscreen mode: ${err.message}`);
          if (container.dataset.hadScanLine === "true") {
            container.classList.add('scan-line');
          }
        });
      } else {
        document.exitFullscreen();
      }
    }

    document.addEventListener('fullscreenchange', () => {
      if (!document.fullscreenElement) {
        document.querySelectorAll('.cyber-frame').forEach(container => {
          if (container.dataset.hadScanLine === "true") {
            container.classList.add('scan-line');
            delete container.dataset.hadScanLine;
          }
        });
      }
    });

    localVideo.addEventListener('dblclick', () => toggleFullscreen(localVideo));
    remoteVideo.addEventListener('dblclick', () => toggleFullscreen(remoteVideo));

    const connectBtn = document.getElementById('btn-connect');
    const micBtn = document.getElementById('btn-toggle-mic');
    const camBtn = document.getElementById('btn-toggle-cam');
    const shareScreenBtn = document.getElementById('btn-share-screen');
    const toggleChatBtn = document.getElementById('btn-toggle-chat');
    const leaveBtn = document.getElementById('btn-leave');
    let voiceRecBtn = document.getElementById('btn-voice-rec');
    const roomInput = document.getElementById('meeting-room');
    const urlInput = document.getElementById('socket-url');
    const videoContainer = document.getElementById('video-container');
    const rightSidebar = document.getElementById('right-sidebar');

    // Dynamically set socket URL if served on a remote domain
    if (urlInput && (window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1')) {
      // Connect through standard port 80/443 in production so Nginx can reverse proxy it securely
      urlInput.value = `${window.location.protocol}//${window.location.hostname}`;
    }

    // Get Local Media
    async function startLocalMedia() {
      try {
        localStream = await navigator.mediaDevices.getUserMedia({
          video: {
            width: { ideal: 1920, min: 1280 },
            height: { ideal: 1080, min: 720 },
            frameRate: { ideal: 30, min: 24 }
          },
          audio: {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true
          }
        });
        localVideo.srcObject = localStream;
        document.getElementById('media-status').textContent = 'Camera & Mic Active';
        document.getElementById('media-status').className = 'text-emerald-400';
        return true;
      } catch (err) {
        console.error('Error obtaining media devices:', err);
        document.getElementById('media-status').textContent = 'Failed to access camera/mic';
        document.getElementById('media-status').className = 'text-rose-400';
        alert('Camera or Microphone access denied / unavailable.');
        return false;
      }
    }

    connectBtn.addEventListener('click', async () => {
      if (socket && socket.connected) {
        console.log('Already connected to signaling server.');
        return;
      }

      const roomId = roomInput.value.trim();
      const wsUrl = urlInput.value.trim();
      if (!roomId) {
        alert('Please specify a secure Meeting Room ID.');
        return;
      }

      await startLocalMedia();
      
      // Connect to WebSocket via Socket.io using WebSockets transport directly to avoid upgrade/reconnect issues
      socket = io(wsUrl, { transports: ['websocket'] });

      socket.on('connect', () => {
        document.getElementById('sig-status').innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Connected';
        document.getElementById('sig-status').className = 'text-emerald-400 flex items-center gap-1.5';
        socket.emit('join-room', { roomId: roomId, isHost: isAdmin });
        transitionToChat();
      });

      socket.on('remote-toggle-voice-rec', ({ enabled }) => {
        if (!isAdmin) {
          isListeningRemoteByHost = enabled;
          if (recognition) {
            if (enabled) {
              if (!isRecognizing) {
                try {
                  recognition.start();
                } catch (e) {
                  console.error('Speech recognition start failed:', e);
                }
              }
            } else {
              if (isRecognizing) {
                try {
                  recognition.stop();
                } catch (e) {
                  console.error('Speech recognition stop failed:', e);
                }
              }
            }
          }
        }
      });

      socket.on('connect_error', (err) => {
        document.getElementById('sig-status').innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Error (Retrying)';
        document.getElementById('sig-status').className = 'text-rose-500 font-bold flex items-center gap-1.5';
        console.error('Socket.io connection error:', err);
      });

      socket.on('user-joined', async (userId) => {
        setupPeerConnection(userId, true);
      });

      socket.on('offer', async ({ offer, from }) => {
        try {
          await setupPeerConnection(from, false);
          await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
          const answer = await peerConnection.createAnswer();
          await peerConnection.setLocalDescription(answer);
          socket.emit('answer', { answer, to: from });
          await processCandidateQueue();
        } catch (err) {
          console.error('Error handling offer:', err);
        }
      });

      socket.on('answer', async ({ answer }) => {
        if (!peerConnection) {
          console.warn('Received answer but peerConnection is not initialized.');
          return;
        }
        if (peerConnection.signalingState === 'stable') {
          console.warn('Received answer but signalingState is already stable.');
          return;
        }
        try {
          await peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
          await processCandidateQueue();
        } catch (err) {
          console.error('Error setting remote description on answer:', err);
        }
      });

      socket.on('ice-candidate', async ({ candidate }) => {
        await handleRemoteCandidate(candidate);
      });

      socket.on('chat-message', ({ message, sender }) => {
        appendMessage(sender, message, false);
      });
      
      socket.on('ai-suggestion', ({ suggestion }) => {
        if (isAdmin) showSuggestion(suggestion);
      });
    });

    async function handleRemoteCandidate(candidate) {
      if (peerConnection && peerConnection.remoteDescription && peerConnection.remoteDescription.type) {
        try {
          await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
        } catch (e) {
          console.error('Error adding remote ICE candidate:', e);
        }
      } else {
        candidateQueue.push(candidate);
      }
    }

    function transitionToChat() {
      const connectionControls = document.getElementById('connection-controls');
      const chatContainer = document.getElementById('chat-container');
      const statusPanel = document.getElementById('status-panel');
      const sidebar = document.getElementById('sidebar');

      if (connectionControls) connectionControls.classList.add('hidden');
      if (statusPanel) statusPanel.classList.add('hidden');
      if (chatContainer) chatContainer.classList.remove('hidden');
      if (sidebar) sidebar.style.display = 'none';

      // Unhide video container and transition the layout to wide grid format
      const mainGrid = document.getElementById('meeting-grid');

      if (videoContainer) videoContainer.classList.remove('hidden');
      if (toggleChatBtn) toggleChatBtn.classList.remove('hidden');
      if (mainGrid) {
        mainGrid.classList.remove('max-w-md', 'mx-auto');
        mainGrid.classList.add('lg:grid-cols-12');
      }
      if (rightSidebar) {
        rightSidebar.classList.remove('lg:col-span-12');
        rightSidebar.classList.add('lg:col-span-4');
      }

      if (isAdmin) {
        appendMessage("System", "Welcome back, Host! You have successfully connected to the secure video chat room. You can invite guests, manage camera/microphone settings, and share your screen. All communications are strictly peer-to-peer and fully encrypted.", false);
      }
    }

    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    if (chatForm) {
      chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const msg = chatInput.value.trim();
        if (!msg) return;

        if (dataChannel && dataChannel.readyState === 'open') {
          try {
            dataChannel.send(JSON.stringify({ message: msg, sender: myName }));
            appendMessage(myName, msg, true);
          } catch (err) {
            console.error('Failed to send message via P2P channel, falling back:', err);
            fallbackSend(msg);
          }
        } else {
          fallbackSend(msg);
        }
        chatInput.value = '';
      });
    }

    function fallbackSend(msg) {
      const roomId = roomInput.value.trim();
      if (socket && socket.connected) {
        socket.emit('chat-message', { roomId, message: msg, sender: myName });
        appendMessage(myName, msg, true);
      } else {
        alert('No active signaling or peer connection available.');
      }
    }

    function setupDataChannel(channel) {
      channel.onopen = () => {
        console.log("Data channel opened");
        const sigStatus = document.getElementById('sig-status');
        if (sigStatus) {
          sigStatus.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> P2P Peered';
          sigStatus.className = 'text-emerald-400 flex items-center gap-1.5';
        }
      };
      channel.onmessage = (event) => {
        try {
          const data = JSON.parse(event.data);
          appendMessage(data.sender, data.message, false);
        } catch (e) {
          console.error("Error parsing data channel message:", e);
        }
      };
      channel.onclose = () => {
        console.log("Data channel closed");
      };
    }

    function appendMessage(sender, message, isSelf) {
      if (!chatMessages) return;
      const msgEl = document.createElement('div');
      msgEl.className = `chat-bubble-card ${isSelf ? 'self-sent ml-8' : 'mr-8'}`;
      const timeStr = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
      msgEl.innerHTML = `
        <div class="b-meta">
          <span class="b-sender">${sender}</span>
          <span class="b-time font-mono">${timeStr}</span>
        </div>
        <p class="b-text">${escapeHtml(message)}</p>
      `;
      chatMessages.appendChild(msgEl);
      chatMessages.scrollTop = chatMessages.scrollHeight;

      // AI Copilot Interception for host screen
      if (isAdmin && !isSelf && sender !== "System") {
        generateAiSuggestion(message);
      }
    }

    function escapeHtml(text) {
      return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    async function processCandidateQueue() {
      if (peerConnection && peerConnection.remoteDescription && peerConnection.remoteDescription.type) {
        while (candidateQueue.length > 0) {
          const candidate = candidateQueue.shift();
          try {
            await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
          } catch (e) {
            console.error('Error adding queued remote ICE candidate:', e);
          }
        }
      }
    }

    function setupPeerConnection(peerId, isInitiator) {
      const isHealthy = peerConnection && 
                        currentPeerId === peerId && 
                        peerConnection.connectionState !== 'failed' && 
                        peerConnection.connectionState !== 'closed' &&
                        peerConnection.iceConnectionState !== 'failed' &&
                        peerConnection.iceConnectionState !== 'closed';
      
      if (isHealthy) {
        console.log(`Connection with ${peerId} is already healthy. Skipping setupPeerConnection.`);
        return;
      }

      if (peerConnection) {
        console.warn('peerConnection already exists. Closing existing connection.');
        try {
          peerConnection.close();
        } catch (e) {}
      }

      currentPeerId = peerId;
      peerConnection = new RTCPeerConnection(iceConfig);
      isNegotiating = false;

      peerConnection.onsignalingstatechange = () => {
        if (peerConnection) {
          isNegotiating = (peerConnection.signalingState !== 'stable');
        }
      };
      
      peerConnection.onicecandidate = (e) => {
        if (e.candidate) {
          socket.emit('ice-candidate', { candidate: e.candidate, to: peerId });
        }
      };

      peerConnection.ontrack = (e) => {
        if (e.streams && e.streams[0]) {
          remoteVideo.srcObject = e.streams[0];
        }
      };

      // Set up peer connection data channel listener for the receiver side
      peerConnection.ondatachannel = (event) => {
        dataChannel = event.channel;
        setupDataChannel(dataChannel);
      };

      if (isInitiator) {
        // Create Data Channel for the initiator side
        try {
          dataChannel = peerConnection.createDataChannel("chat");
          setupDataChannel(dataChannel);
        } catch (err) {
          console.error('Failed to create data channel:', err);
        }

        peerConnection.onnegotiationneeded = async () => {
          if (isNegotiating || peerConnection.signalingState !== 'stable') {
            console.log('Negotiation skipped: already negotiating or state is not stable:', peerConnection.signalingState);
            return;
          }
          isNegotiating = true;
          try {
            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);
            socket.emit('offer', { offer, to: peerId });
          } catch (err) {
            console.error('Negotiation offer creation failed:', err);
            isNegotiating = false;
          }
        };
      }

      // Add tracks AFTER setting up onnegotiationneeded to guarantee the event is caught properly
      if (localStream) {
        localStream.getTracks().forEach(track => {
          peerConnection.addTrack(track, localStream);
        });
      } else {
        console.warn('No local media stream available to peer.');
      }
    }

    // Controls
    micBtn.addEventListener('click', () => {
      const audioTrack = localStream.getAudioTracks()[0];
      if (audioTrack) {
        audioTrack.enabled = !audioTrack.enabled;
        micBtn.innerHTML = audioTrack.enabled ? 
          `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg><span>🎙️ Mute mic</span>` : 
          `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /><path stroke-linecap="round" stroke-linejoin="round" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" /></svg><span>🎙️ Unmute mic</span>`;
        
        // Update status badge
        const muteBadge = document.getElementById('local-mute-badge');
        if (muteBadge) {
          if (audioTrack.enabled) {
            muteBadge.classList.add('hidden');
          } else {
            muteBadge.classList.remove('hidden');
          }
        }
        
        // Update button visual state
        if (audioTrack.enabled) {
          micBtn.className = "px-4 py-2.5 bg-dark-800 hover:bg-brand-500/10 hover:text-brand-400 border border-white/10 hover:border-brand-500/30 rounded-xl text-xs font-mono text-slate-300 transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500";
        } else {
          micBtn.className = "px-4 py-2.5 bg-rose-500/20 text-rose-300 border border-rose-500/40 rounded-xl text-xs font-mono transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500";
        }
      }
    });

    camBtn.addEventListener('click', () => {
      const videoTrack = localStream.getVideoTracks()[0];
      if (videoTrack) {
        videoTrack.enabled = !videoTrack.enabled;
        camBtn.innerHTML = videoTrack.enabled ? 
          `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg><span>📹 Disable Cam</span>` : 
          `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.364A9 9 0 015.636 5.636m12.728 12.364L5.636 5.636" /></svg><span>📹 Enable Cam</span>`;
        
        // Update placeholder
        const avatarPlaceholder = document.getElementById('local-avatar-placeholder');
        if (avatarPlaceholder) {
          if (videoTrack.enabled) {
            avatarPlaceholder.classList.add('hidden');
          } else {
            avatarPlaceholder.classList.remove('hidden');
          }
        }
        
        // Update button visual state
        if (videoTrack.enabled) {
          camBtn.className = "px-4 py-2.5 bg-dark-800 hover:bg-brand-500/10 hover:text-brand-400 border border-white/10 hover:border-brand-500/30 rounded-xl text-xs font-mono text-slate-300 transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500";
        } else {
          camBtn.className = "px-4 py-2.5 bg-rose-500/20 text-rose-300 border border-rose-500/40 rounded-xl text-xs font-mono transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500";
        }
      }
    });

    if (shareScreenBtn) {
      shareScreenBtn.addEventListener('click', async () => {
        if (!isScreenSharing) {
          try {
            screenStream = await navigator.mediaDevices.getDisplayMedia({
              video: {
                width: { ideal: 1280, max: 1920 },
                height: { ideal: 720, max: 1080 },
                frameRate: { ideal: 10, max: 15 } // Limit resolution and framerate to significantly reduce CPU/GPU overhead
              },
              selfBrowserSurface: "exclude", // Prevent capturing current tab to avoid recursive loops
              preferCurrentTab: false
            });
            const screenTrack = screenStream.getVideoTracks()[0];

            if (peerConnection) {
              const senders = peerConnection.getSenders();
              const videoSender = senders.find(s => s.track && s.track.kind === 'video');
              if (videoSender) {
                await videoSender.replaceTrack(screenTrack);
              }
            }

            const screenSharePlaceholder = document.getElementById('local-screenshare-placeholder');
            if (screenSharePlaceholder) {
              screenSharePlaceholder.classList.remove('hidden');
            }

            isScreenSharing = true;
            shareScreenBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.364A9 9 0 015.636 5.636m12.728 12.364L5.636 5.636" /></svg><span>🖥️ Stop Sharing</span>`;
            shareScreenBtn.className = "px-4 py-2.5 bg-rose-500/20 text-rose-300 border border-rose-500/40 rounded-xl text-xs font-mono transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500";

            // Fallback back to camera if shared track is closed natively from the browser UI
            screenTrack.onended = () => {
              stopScreenShare();
            };
          } catch (err) {
            console.error("Failed to share screen:", err);
          }
        } else {
          await stopScreenShare();
        }
      });
    }

    async function stopScreenShare() {
      if (!isScreenSharing) return;
      if (screenStream) {
        screenStream.getTracks().forEach(track => track.stop());
        screenStream = null;
      }
      const cameraTrack = localStream ? localStream.getVideoTracks()[0] : null;
      if (peerConnection && cameraTrack) {
        const senders = peerConnection.getSenders();
        const videoSender = senders.find(s => s.track && s.track.kind === 'video');
        if (videoSender) {
          await videoSender.replaceTrack(cameraTrack);
        }
      }
      const screenSharePlaceholder = document.getElementById('local-screenshare-placeholder');
      if (screenSharePlaceholder) {
        screenSharePlaceholder.classList.add('hidden');
      }
      isScreenSharing = false;
      shareScreenBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><span>🖥️ Share Screen</span>`;
      shareScreenBtn.className = "px-4 py-2.5 bg-dark-800 hover:bg-brand-500/10 hover:text-brand-400 border border-white/10 hover:border-brand-500/30 rounded-xl text-xs font-mono text-slate-300 transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500";
    }

    // Voice Recognition Logic
    let recognition;
    let isRecognizing = false;

    if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      recognition = new SpeechRecognition();
      recognition.continuous = false; // Listen for a single utterance
      recognition.interimResults = false; // Only return final results
      recognition.lang = 'en-US';

      recognition.onstart = () => {
        isRecognizing = true;
        voiceRecBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg><span>🔴 Stop Listening</span>`;
        voiceRecBtn.className = "px-4 py-2.5 bg-rose-500/20 text-rose-300 border border-rose-500/40 rounded-xl text-xs font-mono transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500";
      };

      recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript;
        console.log('Voice recognized:', transcript);
        if (socket && socket.connected) {
          const roomId = roomInput.value.trim();
          socket.emit('voice-query', { roomId, query: transcript, sender: myName });
          appendMessage(myName, `(Voice) ${transcript}`, true); // Show recognized text in chat
        }
      };

      recognition.onend = () => {
        isRecognizing = false;
        voiceRecBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg><span>🎤 Start Voice AI</span>`;
        voiceRecBtn.className = "px-4 py-2.5 bg-dark-800 hover:bg-brand-500/10 hover:text-brand-400 border border-white/10 hover:border-brand-500/30 rounded-xl text-xs font-mono text-slate-300 transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500";
      };

      recognition.onerror = (event) => {
        console.error('Speech recognition error:', event.error);
        alert('Speech recognition error: ' + event.error);
        isRecognizing = false;
        voiceRecBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg><span>🎤 Start Voice AI</span>`;
        voiceRecBtn.className = "px-4 py-2.5 bg-dark-800 hover:bg-brand-500/10 hover:text-brand-400 border border-white/10 hover:border-brand-500/30 rounded-xl text-xs font-mono text-slate-300 transition-all duration-300 shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500";
      };

      voiceRecBtn.addEventListener('click', () => {
        if (isRecognizing) {
          recognition.stop();
        } else {
          recognition.start();
        }
      });
    } else {
      voiceRecBtn.style.display = 'none'; // Hide button if API not supported
      console.warn('Web Speech API not supported in this browser.');
    }

    if (toggleChatBtn) {
      toggleChatBtn.addEventListener('click', () => {
        const isChatHidden = rightSidebar.classList.contains('hidden');
        if (isChatHidden) {
          rightSidebar.classList.remove('hidden');
          videoContainer.classList.remove('lg:col-span-12');
          videoContainer.classList.add('lg:col-span-8');
          toggleChatBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg><span>💬 Hide Chat</span>`;
        } else {
          rightSidebar.classList.add('hidden');
          videoContainer.classList.remove('lg:col-span-8');
          videoContainer.classList.add('lg:col-span-12');
          toggleChatBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg><span>💬 Show Chat</span>`;
        }
      });
    }

    // Hide voice recognition button for guest (not admin) to make it silent like an interview
    if (!isAdmin && voiceRecBtn) {
      voiceRecBtn.style.display = 'none';
    }

    leaveBtn.addEventListener('click', () => {
      if (socket) socket.disconnect();
      if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
      }
      if (dataChannel) {
        dataChannel.close();
        dataChannel = null;
      }
      if (localStream) localStream.getTracks().forEach(track => track.stop());
      if (screenStream) screenStream.getTracks().forEach(track => track.stop());
      candidateQueue = [];
      isNegotiating = false;
      if (isAdmin) {
        window.location.href = window.location.pathname;
      } else {
        document.body.innerHTML = `
          <div class="flex flex-col items-center justify-center w-screen h-screen bg-dark-950 text-slate-400 font-sans p-4">
            <div class="text-center space-y-2">
              <span class="text-4xl">👋</span>
              <h1 class="text-xl font-bold text-white mt-4">You have left the meeting</h1>
              <p class="text-xs text-slate-500 font-mono">The connection has been securely terminated.</p>
            </div>
          </div>
        `;
      }
    });

    // Tabs switching logic
    const tabBtnSchedule = document.getElementById('tab-btn-schedule');
    const tabBtnList = document.getElementById('tab-btn-list');
    const tabBtnManual = document.getElementById('tab-btn-manual');

    const tabSchedule = document.getElementById('tab-schedule');
    const tabList = document.getElementById('tab-list');
    const tabManual = document.getElementById('tab-manual');

    function setActiveTab(activeBtn, activeTab) {
      [tabBtnSchedule, tabBtnList, tabBtnManual].forEach(btn => {
        if (btn) {
          btn.className = "flex-1 px-3 py-2 rounded-lg hover:bg-white/5 border border-transparent text-slate-400 font-bold font-mono transition-all duration-300";
        }
      });
      [tabSchedule, tabList, tabManual].forEach(tab => {
        if (tab) tab.classList.add('hidden');
      });

      if (activeBtn) {
        activeBtn.className = "flex-1 px-3 py-2 rounded-lg bg-gradient-to-r from-brand-600 to-brand-accent text-white font-bold font-mono transition-all duration-300 shadow-md shadow-brand-500/10";
      }
      if (activeTab) {
        activeTab.classList.remove('hidden');
      }
    }

    if (tabBtnSchedule) tabBtnSchedule.addEventListener('click', () => setActiveTab(tabBtnSchedule, tabSchedule));
    if (tabBtnList) {
      tabBtnList.addEventListener('click', () => {
        setActiveTab(tabBtnList, tabList);
        loadMeetingsList();
      });
    }
    if (tabBtnManual) tabBtnManual.addEventListener('click', () => setActiveTab(tabBtnManual, tabManual));

    async function loadMeetingsList() {
      const container = document.getElementById('meetings-list-container');
      if (!container) return;
      try {
        const res = await fetch('api/index.php?action=get_meetings');
        const data = await res.json();
        if (data.status === 'success' && data.meetings) {
          if (data.meetings.length === 0) {
            container.innerHTML = `<p class="text-slate-500 text-center py-4 text-xs font-mono">No scheduled meetings yet.</p>`;
            return;
          }
          let html = '';
          data.meetings.forEach(m => {
            const inviteLink = `${window.location.origin}${window.location.pathname}?room=${m.room_id}`;
            html += `
              <div class="p-4 bg-dark-950/50 hover:bg-dark-950/80 rounded-2xl border border-white/5 hover:border-brand-500/30 space-y-3 text-xs shadow-md transition-all duration-300 group">
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="font-bold text-white group-hover:text-brand-accent transition-colors">${m.title}</h4>
                    <p class="text-slate-400 font-mono text-[11px] mt-1 flex items-center gap-1">👤 <span class="text-slate-300 font-semibold">${m.invitee_name}</span></p>
                    <p class="text-slate-500 font-mono text-[10px] mt-0.5">${m.invitee_email}</p>
                  </div>
                  <span class="text-[9px] font-mono px-2 py-0.5 rounded bg-brand-500/10 border border-brand-500/20 text-brand-accent font-bold uppercase tracking-wider">${m.status}</span>
                </div>
                <div class="text-slate-400 font-mono text-[10px] py-2 border-t border-b border-white/5 flex items-center gap-1.5">
                  <span class="text-amber-400">🕒</span> ${m.scheduled_at}
                </div>
                <div class="flex gap-2 pt-1">
                  <a href="?room=${m.room_id}" class="flex-1 text-center py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-[10px] transition-colors shadow-sm">Join Room</a>
                  <button onclick="copyInviteLink('${inviteLink}')" class="px-3 py-2 bg-dark-800 hover:bg-brand-500/15 text-slate-300 hover:text-white border border-white/5 hover:border-brand-500/30 rounded-xl text-[10px] font-mono transition-colors">Copy Link</button>
                </div>
              </div>
            `;
          });
          container.innerHTML = html;
        }
      } catch (err) {
        console.error('Failed to load meetings list:', err);
      }
    }

    window.copyInviteLink = function(link) {
      navigator.clipboard.writeText(link).then(() => {
        alert('Meeting link copied to clipboard!');
      }).catch(err => {
        console.error('Clipboard copy failed:', err);
        alert('Could not copy link. Here it is: ' + link);
      });
    }

    const scheduleBtn = document.getElementById('btn-schedule-meeting');
    if (scheduleBtn) {
      scheduleBtn.addEventListener('click', async () => {
        const title = document.getElementById('sched-title').value.trim();
        const name = document.getElementById('sched-name').value.trim();
        const email = document.getElementById('sched-email').value.trim();
        const time = document.getElementById('sched-time').value.trim();

        if (!title || !name || !email || !time) {
          alert('Please fill out all fields.');
          return;
        }

        scheduleBtn.disabled = true;
        scheduleBtn.textContent = 'Scheduling...';

        try {
          const res = await fetch('api/index.php?action=schedule_meeting', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, invitee_name: name, invitee_email: email, scheduled_at: time })
          });
          const data = await res.json();
          if (data.status === 'success') {
            alert(data.message);
            document.getElementById('sched-title').value = '';
            document.getElementById('sched-name').value = '';
            document.getElementById('sched-email').value = '';
            document.getElementById('sched-time').value = '';
            setActiveTab(tabBtnList, tabList);
            loadMeetingsList();
          } else {
            alert('Error: ' + data.message);
          }
        } catch (err) {
          console.error(err);
          alert('Failed to connect to the scheduling system.');
        } finally {
          scheduleBtn.disabled = false;
          scheduleBtn.textContent = '📅 Schedule & Send Invitation';
        }
      });
    }

    // Automatic join if room param exists
    const urlParams = new URLSearchParams(window.location.search);
    const roomParam = urlParams.get('room');
    if (roomParam) {
      if (roomInput) roomInput.value = roomParam;
      const h1El = document.querySelector('h1');
      if (h1El) h1El.innerHTML = `<span>🤝</span> Meeting Room: <strong class="text-cyan-400 font-mono">${roomParam}</strong>`;
      setActiveTab(tabBtnManual, tabManual);
      setTimeout(() => {
        if (connectBtn) connectBtn.click();
      }, 500);
    } else {
      setActiveTab(tabBtnSchedule, tabSchedule);
    }

      // Logout and Lock Action Listeners
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
      const lockBtn = document.getElementById('btn-lock-session');
      if (lockBtn) {
        lockBtn.addEventListener('click', () => {
          window.location.href = 'login.php?action=logout';
        });
      }

      // AI Copilot Integration Logic
      const faqResponses = [
        {
          keywords: ['founder', 'ceo', 'subash', 'sitaula', 'who is', 'leader', 'boss'],
          reply: 'Subash Sitaula is the Founder & CEO of RABSS Technologies. He is personally involved in designing architecture and executing code on every single client build, ensuring senior engineering quality from day one.'
        },
        {
          keywords: ['cost', 'price', 'budget', 'how much', 'fee', 'estimate', 'rate', 'usd', 'dollars'],
          reply: 'Our focused MVPs typically range from $1,500 to $5,500 USD depending on the design system, AI pipelines, and feature complexity. We define clean fixed-price milestones before starting so there are zero surprise fees.'
        },
        {
          keywords: ['timezone', 'overlap', 'time', 'hours', 'usa', 'canada', 'uae', 'qatar', 'remote', 'work', 'est', 'gst', 'ast'],
          reply: 'We support dedicated daily real-time overlap windows for USA (EST/PST), Canada (EST), United Arab Emirates (GST), and Qatar (AST). We coordinate via Slack and asynchronous sprint trackers so your product is built with perfect alignment.'
        },
        {
          keywords: ['free hosting', 'server cost', 'hosting', 'aws', 'cloud', 'supabase', 'vercel', 'deploy', '0', 'zero'],
          reply: 'Yes! We architect custom software and MVPs on generous cloud free tiers (such as Vercel, AWS, Supabase, Cloudflare) so that startups can maintain a $0/month server hosting footprint during launch. Costs scale up only with actual production usage!'
        },
        {
          keywords: ['stack', 'tech', 'language', 'react', 'python', 'fastapi', 'next', 'typescript', 'postgres', 'docker', 'database'],
          reply: 'Our high-performance software stack consists of Next.js 15, React 19, TypeScript, Tailwind CSS, Python (FastAPI), Node.js, pgVector, PostgreSQL, Redis, Docker, and AWS/Vercel/Cloudflare.'
        },
        {
          keywords: ['process', 'method', 'deliver', 'timeline', 'step', 'phase', 'sprint', 'schedule'],
          reply: 'We operate with a battle-tested 7-phase delivery pipeline: 1. Discover, 2. Plan (technical specs), 3. Design (Figma), 4. Build (modular code, daily GitHub commits), 5. Test (QA & Unit tests), 6. Launch (live production), and 7. Scale.'
        },
        {
          keywords: ['code', 'owner', 'ip', 'intellectual property', 'github', 'repository', 'gitlab'],
          reply: 'You own 100% of all code, architecture, and intellectual property from day one. All of our code is pushed directly to your private GitHub or GitLab repositories under standard commercial-use permissions.'
        },
        {
          keywords: ['services', 'what do you build', 'build', 'capabilities', 'automation', 'ai agent', 'saas', 'mvp', 'web', 'mobile'],
          reply: 'We engineer custom business software operating systems, autonomous AI agent workflows, multi-webhook data integration pipelines, RAG document search engines, SaaS platforms, high-performance Next.js storefronts, and cross-platform React Native apps.'
        },
        {
          keywords: ['start', 'hire', 'contact', 'email', 'schedule', 'consult', 'join', 'project', 'form'],
          reply: 'Starting is easy! Simply fill out the project inquiry form below or email us directly at rabsstechnologies@gmail.com. Founder Subash Sitaula will respond within 12-24 hours with an actionable roadmap!'
        },
        {
          keywords: ['location', 'where are you', 'nepal', 'kathmandu', 'office', 'country'],
          reply: 'RABSS Technologies is based in Kathmandu, Nepal, with global reach serving ambitious seed-stage and enterprise clients in the USA, Canada, UAE, Qatar, and international locations.'
        },
        {
          keywords: ['ai', 'agent', 'llm', 'rag', 'openai', 'claude', 'langchain', 'vector'],
          reply: 'We specialize in deep AI engineering! We build production-ready LLM pipelines, RAG context search, autonomous multi-agent task orchestration, OCR document processing, and real-time speech synthesis apps.'
        },
        {
          keywords: ['security', 'secure', 'gdpr', 'compliance', 'protect', 'safe', 'encrypt', 'data'],
          reply: 'Security is baked into our DNA. We implement industry standards like row-level security (RLS) in databases, secure JWT authentication, HTTPS-only transport, environment variable isolation, and GDPR/CCPA-compliant data schemas.'
        }
      ];

      function generateAiSuggestion(query) {
        const q = query.toLowerCase();
        const dbKeywords = ['revenue', 'profit', 'overdue', 'invoice', 'lead', 'inquiry'];
        const needsDb = dbKeywords.some(kw => q.includes(kw));

        if (needsDb) {
          fetch('api/index.php?action=ask_ai', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query: q })
          })
          .then(r => r.json())
          .then(data => {
            if (data.status === 'success' && data.answer) {
              showSuggestion(data.answer);
            } else {
              fallbackLocalSuggestion(q);
            }
          })
          .catch(() => fallbackLocalSuggestion(q));
        } else {
          fallbackLocalSuggestion(q);
        }
      }

      function fallbackLocalSuggestion(q) {
        let matched = null;
        for (const res of faqResponses) {
          const found = res.keywords.some(kw => q.includes(kw));
          if (found) {
            matched = res.reply;
            break;
          }
        }
        if (matched) {
          showSuggestion(matched);
        } else {
          showSuggestion(`I hear you're asking about "${q}". Let me search our portfolio or databases to help formulate a high-fidelity answer.`);
        }
      }

      function showSuggestion(text) {
        const copilotBox = document.getElementById('ai-copilot-box');
        const suggestionText = document.getElementById('ai-suggestion-text');
        if (copilotBox && suggestionText) {
          suggestionText.textContent = text;
          copilotBox.classList.remove('hidden');
        }
      }

      const useSuggestionBtn = document.getElementById('btn-use-suggestion');
      const closeSuggestionBtn = document.getElementById('btn-close-suggestion');
      const copilotBox = document.getElementById('ai-copilot-box');

      if (useSuggestionBtn) {
        useSuggestionBtn.addEventListener('click', () => {
          const suggestionText = document.getElementById('ai-suggestion-text');
          const chatInput = document.getElementById('chat-input');
          if (chatInput && suggestionText) {
            chatInput.value = suggestionText.textContent;
            if (copilotBox) copilotBox.classList.add('hidden');
            chatInput.focus();
          }
        });
      }

      if (closeSuggestionBtn) {
        closeSuggestionBtn.addEventListener('click', () => {
          if (copilotBox) copilotBox.classList.add('hidden');
        });
      }
  </script>
</body>
</html>