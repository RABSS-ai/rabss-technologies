<?php
// admin/ directory and API endpoints are completely bypassed by this check.
// Launch date: Wednesday, September 2, 2026 
// Launch timezone: Asia/Kathmandu (Nepal Time)
define('LAUNCH_TIME', '2026-09-02 00:00:00');
define('LAUNCH_TIMEZONE', 'Asia/Kathmandu');

date_default_timezone_set(LAUNCH_TIMEZONE);

$launch_date = new DateTime(LAUNCH_TIME, new DateTimeZone(LAUNCH_TIMEZONE));
$current_date = new DateTime('now', new DateTimeZone(LAUNCH_TIMEZONE));

$is_launched = $current_date >= $launch_date;

// Handle AJAX request for the live website content safely at launch
if (isset($_GET['get_site'])) {
    header('Content-Type: text/html; charset=UTF-8');
    if ($is_launched) {
        define('LAUNCH_ALLOWED', true);
        if (file_exists(__DIR__ . '/public_index.php')) {
            require_once __DIR__ . '/public_index.php';
        } else {
            http_response_code(500);
            echo "Error: Main site template file not found.";
        }
    } else {
        http_response_code(403);
        echo "Error: Access denied. The system is currently in pre-launch stage.";
    }
    exit;
}

// If already launched, serve the real website directly
if ($is_launched) {
    define('LAUNCH_ALLOWED', true);
    if (file_exists(__DIR__ . '/public_index.php')) {
        require_once __DIR__ . '/public_index.php';
        exit;
    }
}

// Otherwise, render the premium Coming Soon countdown page
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RABSS Technologies — Launching Soon</title>
  <meta name="description" content="RABSS Technologies is launching soon. We build next-generation AI solutions, SaaS platforms, and custom software for startups and global businesses.">
  <meta name="robots" content="noindex, nofollow">
  
  <link rel="icon" type="image/jpeg" href="logo.jpg">
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
              800: '#181F30'
            }
          },
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'sans-serif'],
            heading: ['Space Grotesk', 'sans-serif'],
            mono: ['JetBrains Mono', 'monospace']
          }
        }
      }
    }
  </script>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  
  <style>
    body {
      background-color: #07090E;
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-image: 
        radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
        radial-gradient(at 100% 0%, rgba(6, 182, 212, 0.08) 0px, transparent 50%),
        linear-gradient(to right, rgba(255, 255, 255, 0.01) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255, 255, 255, 0.01) 1px, transparent 1px);
      background-size: 100% 100%, 100% 100%, 48px 48px, 48px 48px;
    }
    
    .glow-overlay {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, rgba(6,182,212,0.05) 50%, rgba(0,0,0,0) 100%);
      filter: blur(80px);
      pointer-events: none;
      z-index: 1;
    }

    @keyframes pulseGlow {
      0%, 100% { opacity: 0.6; transform: translate(-50%, -50%) scale(1); }
      50% { opacity: 0.9; transform: translate(-50%, -50%) scale(1.1); }
    }
    
    .pulse-glow {
      animation: pulseGlow 6s infinite ease-in-out;
    }

    @keyframes energyFlow {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    
    .energy-bar {
      background: linear-gradient(90deg, #4F46E5, #06B6D4, #4F46E5);
      background-size: 200% 200%;
      animation: energyFlow 3s infinite linear;
    }
  </style>
</head>

<body class="text-slate-100 min-h-screen flex flex-col justify-between p-6 relative overflow-hidden selection:bg-brand-500 selection:text-white">

  <!-- Glowing Backdrop Effects -->
  <div class="glow-overlay pulse-glow"></div>
  <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-600/10 blur-[130px] rounded-full pointer-events-none"></div>
  <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-cyan-500/10 blur-[130px] rounded-full pointer-events-none"></div>

  <!-- Header Section -->
  <header class="w-full max-w-7xl mx-auto flex justify-between items-center relative z-10">
    <div class="flex items-center gap-3">
      <div class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-brand-accent p-[1.5px] shadow-lg shadow-brand-500/20">
        <div class="w-full h-full bg-dark-950 rounded-[10px] flex items-center justify-center overflow-hidden">
          <img src="logo.jpg" alt="RABSS Technologies Logo" class="w-full h-full object-cover rounded-[10px]">
        </div>
      </div>
      <div class="flex flex-col">
        <span class="font-heading font-bold text-sm tracking-wider text-white">RABSS TECHNOLOGIES</span>
        <span class="text-[10px] text-slate-400 font-mono tracking-wider uppercase">Studio</span>
      </div>
    </div>
    
    <div class="flex items-center gap-2 bg-slate-900/60 border border-white/10 rounded-full px-3 py-1.5 text-[11px] text-slate-300 font-mono">
      <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
      <span>Nepal Time (NPT)</span>
    </div>
  </header>

  <!-- Main Countdown Container -->
  <main class="w-full max-w-4xl mx-auto text-center my-auto py-12 relative z-10 flex flex-col items-center justify-center">
    
    <!-- Outer Wrapper for Easy Transitions -->
    <div id="countdown-wrapper" class="space-y-8 w-full transition-all duration-700">
      
      <!-- Subheading Badge -->
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-500/10 border border-brand-500/30 backdrop-blur-md shadow-lg shadow-brand-500/5">
        <span class="text-xs font-mono tracking-widest text-brand-accent uppercase font-semibold">
          System Initializing • Launch Sequence Active
        </span>
      </div>

      <!-- Main Headline -->
      <div class="space-y-4">
        <h1 class="text-4xl sm:text-6xl font-heading font-extrabold tracking-tight text-white leading-none">
          WE ARE <span class="bg-gradient-to-r from-brand-600 via-indigo-400 to-brand-accent bg-clip-text text-transparent">LAUNCHING</span>
        </h1>
        <p class="text-slate-400 text-sm sm:text-base font-medium max-w-md mx-auto">
          The future of technology is almost here. We are prepping our high-performance AI solutions and SaaS engineering platform.
        </p>
      </div>

      <!-- Countdown Grid -->
      <div class="grid grid-cols-4 gap-3 sm:gap-6 max-w-2xl mx-auto pt-4 font-mono">
        <!-- Days -->
        <div class="p-4 sm:p-6 rounded-2xl bg-dark-900/80 border border-white/10 backdrop-blur-xl shadow-2xl relative group">
          <div class="absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-brand-500/50 to-transparent"></div>
          <span id="days" class="text-3xl sm:text-5xl font-extrabold text-white">00</span>
          <span class="block text-[10px] sm:text-xs text-slate-500 uppercase tracking-widest mt-2">Days</span>
        </div>
        <!-- Hours -->
        <div class="p-4 sm:p-6 rounded-2xl bg-dark-900/80 border border-white/10 backdrop-blur-xl shadow-2xl relative group">
          <div class="absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-brand-500/50 to-transparent"></div>
          <span id="hours" class="text-3xl sm:text-5xl font-extrabold text-white">00</span>
          <span class="block text-[10px] sm:text-xs text-slate-500 uppercase tracking-widest mt-2">Hours</span>
        </div>
        <!-- Minutes -->
        <div class="p-4 sm:p-6 rounded-2xl bg-dark-900/80 border border-white/10 backdrop-blur-xl shadow-2xl relative group">
          <div class="absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-brand-500/50 to-transparent"></div>
          <span id="minutes" class="text-3xl sm:text-5xl font-extrabold text-white">00</span>
          <span class="block text-[10px] sm:text-xs text-slate-500 uppercase tracking-widest mt-2">Minutes</span>
        </div>
        <!-- Seconds -->
        <div class="p-4 sm:p-6 rounded-2xl bg-dark-900/80 border border-white/10 backdrop-blur-xl shadow-2xl relative group">
          <div class="absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-brand-500/50 to-transparent"></div>
          <span id="seconds" class="text-3xl sm:text-5xl font-extrabold text-brand-accent">00</span>
          <span class="block text-[10px] sm:text-xs text-slate-500 uppercase tracking-widest mt-2">Seconds</span>
        </div>
      </div>

      <!-- Exact Date and Timezone -->
      <div class="text-slate-400 font-mono text-xs space-y-1">
        <p>Target Date: <span class="text-white font-semibold font-sans">Wednesday, September 2, 2026</span></p>
        <p>Timezone: <span class="text-brand-accent font-semibold">Nepal Time (NPT / UTC+5:45)</span></p>
      </div>

      <!-- Subtle Progress/Energy Effect -->
      <div class="max-w-md mx-auto space-y-2">
        <div class="w-full bg-dark-950 rounded-full h-1 border border-white/5 overflow-hidden">
          <div id="progress-bar" class="energy-bar h-full rounded-full transition-all duration-1000" style="width: 0%"></div>
        </div>
        <p class="text-[10px] text-slate-500 font-mono">ENERGIZING CORES // SEAMLESS SWITCHOVER DISPATCH ACTIVE</p>
      </div>

    </div>

    <!-- Live Switchover Transition Overlay (Hidden initially) -->
    <div id="launch-overlay" class="hidden absolute inset-0 flex flex-col items-center justify-center space-y-4 transition-all duration-700 opacity-0 scale-90">
      <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-brand-600 to-brand-accent p-1 animate-spin duration-[3000ms]">
        <div class="w-full h-full bg-dark-950 rounded-full flex items-center justify-center font-bold text-white text-xl">
          🚀
        </div>
      </div>
      <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight animate-pulse">
        WE ARE LIVE 🚀
      </h2>
      <p class="text-brand-accent font-mono text-xs tracking-widest uppercase">
        Establishing stable connection... welcome to the main site.
      </p>
    </div>

  </main>

  <!-- Footer Section -->
  <footer class="w-full max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500 relative z-10 font-mono">
    <p>© 2026 RABSS Technologies. All Rights Reserved.</p>
    <p>UTC+5:45 Nepal Node</p>
  </footer>

  <!-- Countdown Script -->
  <script>
    const serverTimeAtLoad = <?= time() * 1000 ?>; // PHP server time in ms
    const launchTime = <?= $launch_date->getTimestamp() * 1000 ?>; // Launch time in ms
    const startTime = launchTime - (7 * 24 * 60 * 60 * 1000); // 7 days total tracking period for progress bar
    const clientTimeAtLoad = Date.now();

    function getAdjustedServerTime() {
      const elapsed = Date.now() - clientTimeAtLoad;
      return serverTimeAtLoad + elapsed;
    }

    function updateCountdown() {
      const now = getAdjustedServerTime();
      const diff = launchTime - now;

      // Handle Launch switchover immediately
      if (diff <= 0) {
        clearInterval(countdownInterval);
        triggerLaunchTransition();
        return;
      }

      // Calculate progress percentage
      const totalDuration = launchTime - startTime;
      const elapsed = now - startTime;
      const progressPercent = Math.max(0, Math.min(100, (elapsed / totalDuration) * 100));
      document.getElementById('progress-bar').style.width = progressPercent + '%';

      // Parse countdown values
      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((diff % (1000 * 60)) / 1000);

      // Display values
      document.getElementById('days').textContent = String(days).padStart(2, '0');
      document.getElementById('hours').textContent = String(hours).padStart(2, '0');
      document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
      document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
    }

    async function triggerLaunchTransition() {
      const wrapper = document.getElementById('countdown-wrapper');
      wrapper.classList.add('opacity-0', 'scale-95');
      
      setTimeout(async () => {
        wrapper.classList.add('hidden');
        
        const overlay = document.getElementById('launch-overlay');
        overlay.classList.remove('hidden');
        void overlay.offsetWidth;
        overlay.classList.remove('opacity-0', 'scale-90');
        overlay.classList.add('opacity-100', 'scale-100');

        try {
          const response = await fetch('index.php?get_site=true');
          if (!response.ok) throw new Error('Launch server-side handshake failed.');
          const html = await response.text();

          setTimeout(() => {
            document.open();
            document.write(html);
            document.close();
          }, 1500);

        } catch (err) {
          console.error('Launch fetch failed:', err);
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        }
      }, 500);
    }

    // Initialize & Start Countdown
    updateCountdown();
    const countdownInterval = setInterval(updateCountdown, 1000);
  </script>
</body>
</html>