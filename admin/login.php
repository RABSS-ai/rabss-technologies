<?php
// admin/login.php
session_start();
require_once __DIR__ . '/api/db.php';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    if (isset($_SESSION['user_name'])) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_name, action, entity, ip_address) VALUES (?, 'Logged out of Super Admin', 'Auth', ?)");
        $stmt->execute([$_SESSION['user_name'], $ip]);
    }
    session_unset();
    session_destroy();
    header("Location: login.php?msg=logged_out");
    exit;
}

// Redirect if already authenticated
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $token = $_POST['csrf_token'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    // Verify CSRF
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Invalid security token. Please refresh and try again.';
    } elseif (empty($email) || empty($password)) {
        $error = 'Please enter both your work email and master password.';
    } else {
        // Rate limiting check
        $_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;
        if ($_SESSION['login_attempts'] >= 5) {
            $error = 'Too many failed login attempts. Please wait 60 seconds.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'Active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Reset attempts
                $_SESSION['login_attempts'] = 0;

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];

                // Audit Log
                $stmtAudit = $pdo->prepare("INSERT INTO audit_logs (user_name, action, entity, ip_address) VALUES (?, 'Successful Super Admin Login', 'Auth', ?)");
                $stmtAudit->execute([$user['name'], $ip]);

                header("Location: index.php");
                exit;
            } else {
                $_SESSION['login_attempts']++;
                $error = 'Invalid credentials. Access denied.';

                // Log failed attempt
                $stmtAudit = $pdo->prepare("INSERT INTO audit_logs (user_name, action, entity, ip_address) VALUES (?, 'Failed Login Attempt', 'Auth', ?)");
                $stmtAudit->execute([$email ?: 'Unknown', $ip]);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Authentication — RABSS OS</title>
  
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
            mono: ['JetBrains Mono', 'monospace']
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
  </style>
</head>

<body class="bg-dark-950 text-slate-100 min-h-screen flex items-center justify-center p-4 selection:bg-brand-500 selection:text-white relative overflow-hidden">

  <!-- Glow Backdrop -->
  <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[350px] bg-gradient-to-tr from-brand-600/20 via-purple-600/20 to-brand-accent/20 blur-[130px] rounded-full pointer-events-none"></div>
  <div class="absolute bottom-10 -right-20 w-80 h-80 bg-cyan-500/10 blur-[120px] rounded-full pointer-events-none"></div>

  <div class="max-w-md w-full relative z-10">
    
    <!-- Header Brand -->
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-600 to-brand-accent p-0.5 shadow-xl shadow-brand-500/30 mb-4">
        <div class="w-full h-full bg-dark-950 rounded-[14px] flex items-center justify-center font-mono font-bold text-2xl text-white">
          R
        </div>
      </div>
      <h1 class="text-2xl font-extrabold text-white tracking-tight">RABSS TECHNOLOGIES</h1>
      <p class="text-xs font-mono text-slate-400 mt-1 uppercase tracking-widest flex items-center justify-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        Business Operating System // Super Admin
      </p>
    </div>

    <!-- Login Card -->
    <div class="p-8 rounded-3xl bg-dark-900/90 border border-white/10 backdrop-blur-2xl shadow-2xl">
      
      <!-- Error / Success Alert -->
      <?php if (!empty($error)): ?>
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-mono flex items-center gap-2">
          <span>⚠️</span>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out'): ?>
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-mono flex items-center gap-2">
          <span>✓</span>
          <span>Session securely terminated.</span>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php" class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <!-- Email Field -->
        <div>
          <label class="block text-xs font-mono text-slate-300 mb-1.5 font-semibold">ADMIN WORK EMAIL</label>
          <div class="relative">
            <input 
              type="email" 
              name="email" 
              id="email" 
              required 
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              class="w-full bg-dark-950 border border-white/10 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-brand-500 transition-colors font-mono"
            >
          </div>
        </div>

        <!-- Password Field -->
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-mono text-slate-300 font-semibold">MASTER PASSWORD</label>
            <button type="button" id="toggle-pwd-btn" class="text-[11px] font-mono text-brand-accent hover:underline">Show</button>
          </div>
          <div class="relative">
            <input 
              type="password" 
              name="password" 
              id="password" 
              required 
              value=""
              class="w-full bg-dark-950 border border-white/10 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-brand-500 transition-colors font-mono"
            >
          </div>
        </div>

        <!-- Remember Me & Quick Fill -->
        <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" name="remember" class="rounded bg-dark-950 border-white/10 text-brand-600 focus:ring-0">
            <span>Remember device</span>
          </label>
          <button type="button" id="btn-quick-fill" class="text-slate-400 hover:text-white font-mono text-[11px]">
            ⚡ Default CEO Fill
          </button>
        </div>

        <!-- Submit Button -->
        <button 
          type="submit" 
          class="w-full py-3.5 bg-gradient-to-r from-brand-600 via-indigo-500 to-brand-accent hover:from-brand-500 hover:to-cyan-400 text-white font-bold rounded-xl shadow-xl shadow-brand-600/30 transition-all duration-300 flex items-center justify-center gap-2 text-sm mt-6"
        >
          <span>Authenticate & Access Command Center</span>
          <span>→</span>
        </button>

      </form>

      <!-- Security Meta -->
      <div class="mt-8 pt-6 border-t border-white/5 flex items-center justify-between text-[11px] font-mono text-slate-500">
        <span class="flex items-center gap-1">🔒 256-bit TLS</span>
        <span>RBAC Protected</span>
        <span>UTC+5:45 Node</span>
      </div>

    </div>

    <!-- Back to Public Site -->
    <div class="text-center mt-6">
      <a href="../index.html" class="text-xs text-slate-400 hover:text-white transition-colors font-mono">
        ← Return to Public Website
      </a>
    </div>

  </div>

  <script>
    // Show/Hide Password
    const pwdInput = document.getElementById('password');
    const toggleBtn = document.getElementById('toggle-pwd-btn');
    toggleBtn.addEventListener('click', () => {
      if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        toggleBtn.textContent = 'Hide';
      } else {
        pwdInput.type = 'password';
        toggleBtn.textContent = 'Show';
      }
    });

    // Quick Fill Helper for Demo / Testing
    document.getElementById('btn-quick-fill').addEventListener('click', () => {
      document.getElementById('email').value = 'ceo@rabss.tech';
      document.getElementById('password').value = 'Admin@RABSS2026';
    });
  </script>
</body>
</html>