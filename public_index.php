<?php
// Prevent direct access to public index template before the official launch
if (!defined('LAUNCH_ALLOWED')) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Primary SEO Meta Tags -->
  <title>RABSS Technologies | AI & Custom Software Studio for Startups & Global Businesses</title>
  <meta name="title" content="RABSS Technologies | AI & Custom Software Studio for Startups & Global Businesses">
  <meta name="description" content="We turn ambitious ideas into digital products. AI-powered applications, SaaS platforms, custom software, and MVP development for businesses in the USA, Canada, UAE, and Qatar.">
  <meta name="keywords" content="RABSS Technologies, Subash Sitaula, software development, AI development, SaaS development, MVP development, AI agents, custom software Nepal, US startup software, Canada digital products, UAE AI automation, Qatar software">
  <meta name="author" content="Subash Sitaula, RABSS Technologies">
  <link rel="canonical" href="https://rabss.tech/">

  <!-- Open Graph / Facebook / LinkedIn -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://rabss.tech/">
  <meta property="og:title" content="RABSS Technologies | We Build Software That Moves Businesses Forward">
  <meta property="og:description" content="A modern software & AI development studio helping startups and businesses turn ideas into scalable digital products.">
  <meta property="og:image" content="https://rabss.tech/assets/og-image.png">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="https://rabss.tech/">
  <meta property="twitter:title" content="RABSS Technologies | AI & Custom Software Studio">
  <meta property="twitter:description" content="Building next-generation digital products, SaaS, and AI automation for global founders.">
  <meta property="twitter:image" content="https://rabss.tech/assets/og-image.png">

  <!-- Favicon / Fonts -->
  <link rel="icon" type="image/jpeg" href="logo.jpg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS Play CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            dark: '#080B11',
            surface: '#0E131F',
          },
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
            heading: ['"Space Grotesk"', 'sans-serif'],
            mono: ['"JetBrains Mono"', 'monospace'],
          }
        }
      }
    }
  </script>

  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="styles.css">

  <!-- Structured Data / JSON-LD -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "@id": "https://rabss.tech/#organization",
        "name": "RABSS Technologies",
        "url": "https://rabss.tech",
        "logo": "https://rabss.tech/logo.jpg",
        "founder": {
          "@type": "Person",
          "name": "Subash Sitaula",
          "jobTitle": "Founder & CEO"
        },
        "description": "A modern software and AI development studio helping startups and businesses turn ideas into scalable digital products.",
        "areaServed": [
          {"@type": "Country", "name": "United States"},
          {"@type": "Country", "name": "Canada"},
          {"@type": "Country", "name": "United Arab Emirates"},
          {"@type": "Country", "name": "Qatar"}
        ],
        "knowsAbout": [
          "Artificial Intelligence",
          "Custom Software Engineering",
          "SaaS Architecture",
          "MVP Development",
          "AI Agent Workflows",
          "Cloud Engineering"
        ]
      },
      {
        "@type": "WebSite",
        "@id": "https://rabss.tech/#website",
        "url": "https://rabss.tech",
        "name": "RABSS Technologies",
        "publisher": { "@id": "https://rabss.tech/#organization" }
      },
      {
        "@type": "FAQPage",
        "mainEntity": [
          {
            "@type": "Question",
            "name": "How does RABSS Technologies work with international clients across time zones?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "We offer dedicated daily overlap hours, clear asynchronous updates via Slack/Notion, sprint reviews, and direct Slack/Discord access to CEO Subash Sitaula and the core engineering team."
            }
          },
          {
            "@type": "Question",
            "name": "How is RABSS Technologies different from traditional software agencies?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "We operate with zero middle management. You work directly with senior engineers and the founder. Every project gets our full attention because each product builds our founding reputation."
            }
          }
        ]
      }
    ]
  }
  </script>
</head>

<body class="bg-dark text-slate-100 font-sans antialiased selection:bg-indigo-500 selection:text-white">

  <!-- ==================== 1. NAVBAR ==================== -->
  <header id="navbar" class="fixed top-0 left-0 w-full z-50 transition-all duration-300 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <nav class="flex items-center justify-between bg-slate-900/60 backdrop-blur-xl border border-white/10 rounded-2xl px-5 py-3 shadow-2xl">
        
        <!-- Logo -->
        <a href="#hero" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg">
          <div class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 via-purple-600 to-cyan-400 p-[1.5px] shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-300">
            <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center overflow-hidden">
              <img src="logo.jpg" alt="RABSS Technologies Logo" class="w-full h-full object-cover rounded-[10px]">
            </div>
          </div>
          <div class="flex flex-col">
            <span class="font-heading font-bold text-lg tracking-wider text-white flex items-center gap-1.5">
              RABSS <span class="text-xs px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 font-mono font-normal">TECH</span>
            </span>
            <span class="text-[10px] text-slate-400 font-mono tracking-wider">STUDIO</span>
          </div>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden lg:flex items-center gap-7 text-sm font-medium text-slate-300">
          <a href="#services" class="hover:text-white transition-colors">Services</a>
          <a href="#ai-automation" class="hover:text-cyan-400 transition-colors flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span> AI Solutions
          </a>
          <a href="#concepts" class="hover:text-white transition-colors">What We Build</a>
          <a href="#markets" class="hover:text-white transition-colors">Markets</a>
          <a href="#process" class="hover:text-white transition-colors">Process</a>
          <a href="#tools" class="hover:text-white transition-colors">Free Tools</a>
          <a href="#about" class="hover:text-white transition-colors">About</a>
        </div>

        <!-- Right Side: Market Pill & CTA -->
        <div class="hidden md:flex items-center gap-4">
          <div class="flex items-center gap-2 bg-slate-800/80 border border-white/10 rounded-full px-3 py-1 text-xs text-slate-300 font-mono">
            <span class="text-xs">🇺🇸 🇨🇦 🇦🇪 🇶🇦</span>
            <span class="text-slate-400">|</span>
            <span class="text-emerald-400 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Open</span>
          </div>

          <a href="#contact" class="relative inline-flex items-center justify-center p-0.5 overflow-hidden rounded-xl font-medium group focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <span class="w-full h-full bg-gradient-to-br from-cyan-500 via-indigo-600 to-purple-600 group-hover:from-cyan-400 group-hover:to-indigo-500 absolute"></span>
            <span class="relative px-4 py-2 transition-all ease-out bg-slate-950 rounded-[10px] group-hover:bg-opacity-0 text-white text-sm font-semibold flex items-center gap-1.5">
              Start Your Project <span class="transition-transform duration-300 group-hover:translate-x-1">→</span>
            </span>
          </a>
        </div>

        <!-- Mobile Menu Toggle -->
        <button id="mobile-menu-btn" class="lg:hidden text-slate-300 hover:text-white p-2 rounded-lg bg-slate-800/60 border border-white/10 focus:outline-none" aria-label="Toggle navigation menu">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
          </svg>
        </button>
      </nav>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-menu" class="hidden fixed inset-0 top-[76px] bg-slate-950/95 backdrop-blur-2xl border-t border-white/10 p-6 flex-col justify-between z-40 lg:hidden">
      <div class="flex flex-col gap-5 text-lg font-medium text-slate-200">
        <a href="#services" class="mobile-nav-link py-2 border-b border-white/5">Services</a>
        <a href="#ai-automation" class="mobile-nav-link py-2 border-b border-white/5 text-cyan-400">AI & Automation</a>
        <a href="#concepts" class="mobile-nav-link py-2 border-b border-white/5">What We Build (Concepts)</a>
        <a href="#markets" class="mobile-nav-link py-2 border-b border-white/5">Global Markets</a>
        <a href="#process" class="mobile-nav-link py-2 border-b border-white/5">Development Process</a>
        <a href="#tools" class="mobile-nav-link py-2 border-b border-white/5">Cost Calculator & AI Tools</a>
        <a href="#about" class="mobile-nav-link py-2 border-b border-white/5">About & Founder</a>
      </div>
      <div class="pt-6">
        <a href="#contact" class="mobile-nav-link w-full py-3.5 bg-gradient-to-r from-indigo-600 to-cyan-500 text-white text-center font-semibold rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/30">
          Start Your Project →
        </a>
      </div>
    </div>
  </header>

  <main>
    <!-- ==================== 2. HERO SECTION ==================== -->
    <section id="hero" class="relative pt-32 pb-20 md:pt-44 md:pb-32 overflow-hidden">
      <!-- Glow Gradients -->
      <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-gradient-to-tr from-indigo-500/20 via-purple-600/20 to-cyan-500/20 blur-[130px] rounded-full pointer-events-none"></div>
      <div class="absolute -top-10 -right-20 w-96 h-96 bg-cyan-500/10 blur-[120px] rounded-full pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Top Badge -->
        <div class="flex justify-center mb-6">
          <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-slate-900/80 border border-indigo-500/30 backdrop-blur-md shadow-lg shadow-indigo-500/10">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
            <span class="text-xs font-mono tracking-widest text-slate-200 uppercase font-semibold">
              BUILDING THE FUTURE • NOW ACCEPTING FOUNDING PROJECTS
            </span>
          </div>
        </div>

        <!-- Main Headline -->
        <div class="text-center max-w-4xl mx-auto">
          <h1 class="text-4xl sm:text-6xl lg:text-7xl font-heading font-extrabold tracking-tight text-white leading-[1.1]">
            WE BUILD SOFTWARE THAT <br class="hidden sm:block">
            <span class="bg-gradient-to-r from-cyan-400 via-indigo-300 to-purple-400 bg-clip-text text-transparent">
              MOVES BUSINESSES FORWARD.
            </span>
          </h1>

          <p class="mt-6 text-lg sm:text-xl text-slate-300 leading-relaxed max-w-3xl mx-auto">
            From AI-powered applications and SaaS platforms to custom business software, we design and build technology that helps ambitious businesses in the 
            <span class="text-white font-medium">USA 🇺🇸</span>, 
            <span class="text-white font-medium">Canada 🇨🇦</span>, 
            <span class="text-white font-medium">UAE 🇦🇪</span>, and 
            <span class="text-white font-medium">Qatar 🇶🇦</span> move faster.
          </p>

          <!-- CTAs -->
          <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="#contact" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-indigo-600 via-indigo-500 to-cyan-500 hover:from-indigo-500 hover:to-cyan-400 text-white font-semibold rounded-xl shadow-xl shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
              Start Your Project <span>→</span>
            </a>
            <a href="#concepts" class="w-full sm:w-auto px-8 py-4 bg-slate-900/80 hover:bg-slate-850 border border-white/15 hover:border-white/30 text-slate-200 font-semibold rounded-xl backdrop-blur-md hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
              Explore What We Build
            </a>
          </div>

          <!-- Micro Capabilities List -->
          <div class="mt-8 flex flex-wrap items-center justify-center gap-3 text-xs sm:text-sm font-mono text-slate-400">
            <span class="px-3 py-1 rounded-md bg-slate-900/70 border border-white/5">AI Agents</span>
            <span>•</span>
            <span class="px-3 py-1 rounded-md bg-slate-900/70 border border-white/5">SaaS Platforms</span>
            <span>•</span>
            <span class="px-3 py-1 rounded-md bg-slate-900/70 border border-white/5">MVP Fast-Track</span>
            <span>•</span>
            <span class="px-3 py-1 rounded-md bg-slate-900/70 border border-white/5">Web & Mobile</span>
            <span>•</span>
            <span class="px-3 py-1 rounded-md bg-slate-900/70 border border-white/5">Business Automation</span>
          </div>
        </div>

        <!-- Interactive Hero Ecosystem Visualizer -->
        <div class="mt-16 relative">
          <div class="relative rounded-2xl bg-slate-950/80 border border-white/15 backdrop-blur-2xl shadow-2xl p-4 sm:p-6 overflow-hidden">
            
            <!-- Window Header -->
            <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-6">
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
                <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                <span class="ml-3 text-xs font-mono text-slate-400">RABSS_CORE_ENGINE // LIVE_PREVIEW</span>
              </div>
              <div class="flex items-center gap-2">
                <button class="hero-tab-btn active px-3 py-1 text-xs font-mono rounded-lg bg-indigo-600/30 text-indigo-300 border border-indigo-500/40" data-view="dashboard">SaaS Dashboard</button>
                <button class="hero-tab-btn px-3 py-1 text-xs font-mono rounded-lg bg-slate-900 text-slate-400 border border-white/5 hover:text-white" data-view="ai-agent">AI Agent</button>
                <button class="hero-tab-btn px-3 py-1 text-xs font-mono rounded-lg bg-slate-900 text-slate-400 border border-white/5 hover:text-white" data-view="code-infra">Cloud & API</button>
              </div>
            </div>

            <!-- View: SaaS Dashboard -->
            <div id="view-dashboard" class="hero-view-panel grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-8 space-y-6">
                <!-- Stat Cards -->
                <div class="grid grid-cols-3 gap-4">
                  <div class="p-4 rounded-xl bg-slate-900/80 border border-white/10">
                    <p class="text-xs text-slate-400">Active AI Workflows</p>
                    <p class="text-2xl font-bold font-mono text-cyan-400 mt-1">128</p>
                    <p class="text-[11px] text-emerald-400 mt-1 font-mono">↑ 99.98% uptime</p>
                  </div>
                  <div class="p-4 rounded-xl bg-slate-900/80 border border-white/10">
                    <p class="text-xs text-slate-400">Latency / Response</p>
                    <p class="text-2xl font-bold font-mono text-purple-400 mt-1">42ms</p>
                    <p class="text-[11px] text-indigo-300 mt-1 font-mono">Edge accelerated</p>
                  </div>
                  <div class="p-4 rounded-xl bg-slate-900/80 border border-white/10">
                    <p class="text-xs text-slate-400">Tasks Automated</p>
                    <p class="text-2xl font-bold font-mono text-emerald-400 mt-1">24,500+</p>
                    <p class="text-[11px] text-slate-400 mt-1 font-mono">Zero manual steps</p>
                  </div>
                </div>

                <!-- Live Interactive Graph Simulation -->
                <div class="p-5 rounded-xl bg-slate-900/80 border border-white/10">
                  <div class="flex items-center justify-between mb-4">
                    <div>
                      <h4 class="text-sm font-semibold text-white">System Throughput & Data Processing</h4>
                      <p class="text-xs text-slate-400 font-mono">Global edge network telemetry</p>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 text-xs font-mono">Live Stream</span>
                  </div>
                  <div class="h-36 flex items-end gap-2 pt-4 px-2 border-b border-white/10">
                    <div class="flex-1 bg-indigo-600/40 hover:bg-indigo-500 rounded-t h-[45%] transition-all"></div>
                    <div class="flex-1 bg-indigo-600/40 hover:bg-indigo-500 rounded-t h-[60%] transition-all"></div>
                    <div class="flex-1 bg-indigo-600/40 hover:bg-indigo-500 rounded-t h-[80%] transition-all"></div>
                    <div class="flex-1 bg-indigo-600/40 hover:bg-indigo-500 rounded-t h-[65%] transition-all"></div>
                    <div class="flex-1 bg-cyan-500/50 hover:bg-cyan-400 rounded-t h-[95%] transition-all"></div>
                    <div class="flex-1 bg-cyan-500/50 hover:bg-cyan-400 rounded-t h-[85%] transition-all"></div>
                    <div class="flex-1 bg-indigo-600/40 hover:bg-indigo-500 rounded-t h-[70%] transition-all"></div>
                    <div class="flex-1 bg-purple-600/50 hover:bg-purple-400 rounded-t h-[90%] transition-all"></div>
                    <div class="flex-1 bg-cyan-500/60 hover:bg-cyan-400 rounded-t h-[100%] transition-all"></div>
                  </div>
                </div>
              </div>

              <!-- Real-time Agent Log -->
              <div class="lg:col-span-4 p-4 rounded-xl bg-slate-900/90 border border-white/10 font-mono text-xs flex flex-col justify-between">
                <div>
                  <div class="flex items-center justify-between pb-3 border-b border-white/10 mb-3 text-slate-400">
                    <span>AGENT DISPATCHER</span>
                    <span class="text-emerald-400">● RUNNING</span>
                  </div>
                  <div class="space-y-2 text-[11px] text-slate-300">
                    <p class="text-indigo-300">[16:47:01] ⚡ Webhook trigger: USA Client Lead</p>
                    <p class="text-cyan-300">[16:47:02] 🤖 Document AI parsing PDF contract...</p>
                    <p class="text-slate-400">[16:47:03] 📊 Synced to CRM & PostgreSQL Cluster</p>
                    <p class="text-emerald-300">[16:47:04] ✓ Auto-notified engineering team</p>
                  </div>
                </div>
                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-slate-400">
                  <span>Stack: FastAPI + React + pgVector</span>
                </div>
              </div>
            </div>

            <!-- View: AI Agent (Hidden by default) -->
            <div id="view-ai-agent" class="hero-view-panel hidden p-6 rounded-xl bg-slate-900/80 border border-white/10 font-mono text-sm">
              <div class="max-w-2xl mx-auto space-y-4">
                <div class="flex gap-3">
                  <div class="w-8 h-8 rounded-lg bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-xs text-indigo-300 font-bold">U</div>
                  <div class="p-3.5 rounded-xl bg-slate-800/80 text-slate-200 text-xs leading-relaxed">
                    "Extract structured invoice data from 500 PDF vendors and route anomaly payouts to Slack with risk scores."
                  </div>
                </div>
                <div class="flex gap-3">
                  <div class="w-8 h-8 rounded-lg bg-cyan-500/30 border border-cyan-400/40 flex items-center justify-center text-xs text-cyan-300 font-bold">🤖</div>
                  <div class="p-3.5 rounded-xl bg-indigo-950/40 border border-indigo-500/30 text-slate-200 text-xs leading-relaxed">
                    <p class="text-cyan-300 font-semibold mb-1">RABSS AI Agent Workflow Dispatched:</p>
                    <p>1. OCR & Vision Tokenization completed (498 clean, 2 flagged).</p>
                    <p>2. Risk Analysis: Invoice #9021 flagged (Mismatch in VAT calculation).</p>
                    <p>3. Dispatching automated Slack notification to Finance Lead...</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- View: Cloud & Infra (Hidden by default) -->
            <div id="view-code-infra" class="hero-view-panel hidden p-6 rounded-xl bg-slate-900/80 border border-white/10 font-mono text-xs">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-lg bg-slate-950 border border-white/10 text-slate-300">
                  <p class="text-cyan-400 font-bold mb-2">// Infrastructure as Code (Terraform)</p>
                  <p>module "rabss_cluster" {</p>
                  <p class="pl-4">source  = "aws/ecs/service"</p>
                  <p class="pl-4">regions = ["us-east-1", "ca-central-1", "me-central-1"]</p>
                  <p class="pl-4">scaling = { min: 2, max: 50, metric: "ai_queue_depth" }</p>
                  <p>}</p>
                </div>
                <div class="p-4 rounded-lg bg-slate-950 border border-white/10 text-slate-300">
                  <p class="text-purple-400 font-bold mb-2">// Edge Routing & Latency Matrix</p>
                  <p>🇺🇸 New York: <span class="text-emerald-400">18ms</span></p>
                  <p>🇨🇦 Toronto: <span class="text-emerald-400">22ms</span></p>
                  <p>🇦🇪 Dubai: <span class="text-emerald-400">29ms</span></p>
                  <p>🇶🇦 Doha: <span class="text-emerald-400">31ms</span></p>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </section>

    <!-- ==================== 3. SERVICES SECTION ==================== -->
    <section id="services" class="py-24 relative bg-slate-950/60 border-t border-white/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-mono mb-4">
            CORE CAPABILITIES
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
            Everything You Need to Build Your Product.
          </h2>
          <p class="mt-4 text-slate-400 text-lg">
            From zero to launch, we architect, design, and engineer custom digital solutions with modern technical standards.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          
          <!-- Service 1: AI Development -->
          <div class="service-card group p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/50 hover:bg-slate-900/90 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="w-12 h-12 rounded-xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 group-hover:scale-110 group-hover:text-cyan-300 transition-all mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">AI Development</h3>
              <p class="text-slate-400 text-sm leading-relaxed mb-4">
                Build custom AI applications, AI agents, document intelligence systems, and LLM-powered workflows designed around your business processes.
              </p>
            </div>
            <div>
              <div class="flex flex-wrap gap-1.5 mb-4 text-[11px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">LLMs</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">RAG</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">AI Agents</span>
              </div>
              <a href="#contact" class="text-xs font-semibold text-indigo-400 group-hover:text-cyan-300 flex items-center gap-1">
                Explore AI Solutions →
              </a>
            </div>
          </div>

          <!-- Service 2: Custom Software -->
          <div class="service-card group p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/50 hover:bg-slate-900/90 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="w-12 h-12 rounded-xl bg-purple-600/20 border border-purple-500/30 flex items-center justify-center text-purple-400 group-hover:scale-110 group-hover:text-purple-300 transition-all mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">Custom Software</h3>
              <p class="text-slate-400 text-sm leading-relaxed mb-4">
                Build custom software and business applications tailored to your workflows, operations, integrations, and long-term growth.
              </p>
            </div>
            <div>
              <div class="flex flex-wrap gap-1.5 mb-4 text-[11px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">FastAPI</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Node.js</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">PostgreSQL</span>
              </div>
              <a href="#contact" class="text-xs font-semibold text-indigo-400 group-hover:text-purple-300 flex items-center gap-1">
                Discuss Software →
              </a>
            </div>
          </div>

          <!-- Service 3: SaaS Development -->
          <div class="service-card group p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/50 hover:bg-slate-900/90 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="w-12 h-12 rounded-xl bg-cyan-600/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 group-hover:scale-110 group-hover:text-cyan-300 transition-all mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">SaaS Development</h3>
              <p class="text-slate-400 text-sm leading-relaxed mb-4">
                Turn your software idea into a scalable SaaS platform with multi-tenant architecture, subscriptions, secure integrations, and production-ready infrastructure.
              </p>
            </div>
            <div>
              <div class="flex flex-wrap gap-1.5 mb-4 text-[11px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Multi-tenant</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Stripe</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Next.js</span>
              </div>
              <a href="#contact" class="text-xs font-semibold text-indigo-400 group-hover:text-cyan-300 flex items-center gap-1">
                Build SaaS →
              </a>
            </div>
          </div>

          <!-- Service 4: MVP Development -->
          <div class="service-card group p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/50 hover:bg-slate-900/90 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="w-12 h-12 rounded-xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 group-hover:scale-110 group-hover:text-emerald-300 transition-all mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">MVP Fast-Track</h3>
              <p class="text-slate-400 text-sm leading-relaxed mb-4">
                Launch a focused MVP in weeks with the essential features needed to validate your idea, attract early users, and prepare for growth or investment.
              </p>
            </div>
            <div>
              <div class="flex flex-wrap gap-1.5 mb-4 text-[11px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Rapid Dev</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Lean Scope</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Analytics</span>
              </div>
              <a href="#contact" class="text-xs font-semibold text-indigo-400 group-hover:text-emerald-300 flex items-center gap-1">
                Launch MVP →
              </a>
            </div>
          </div>

          <!-- Service 5: Web Applications -->
          <div class="service-card group p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/50 hover:bg-slate-900/90 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="w-12 h-12 rounded-xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center text-blue-400 group-hover:scale-110 group-hover:text-blue-300 transition-all mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">Web Development</h3>
              <p class="text-slate-400 text-sm leading-relaxed mb-4">
                Build fast, responsive web applications, client portals, dashboards, and scalable digital platforms using modern frontend and backend technologies.
              </p>
            </div>
            <div>
              <div class="flex flex-wrap gap-1.5 mb-4 text-[11px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">React</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">TypeScript</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Tailwind</span>
              </div>
              <a href="#contact" class="text-xs font-semibold text-indigo-400 group-hover:text-blue-300 flex items-center gap-1">
                Explore Web →
              </a>
            </div>
          </div>

          <!-- Service 6: Mobile Development -->
          <div class="service-card group p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/50 hover:bg-slate-900/90 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="w-12 h-12 rounded-xl bg-pink-600/20 border border-pink-500/30 flex items-center justify-center text-pink-400 group-hover:scale-110 group-hover:text-pink-300 transition-all mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">Mobile Apps</h3>
              <p class="text-slate-400 text-sm leading-relaxed mb-4">
                Develop high-performance iOS and Android mobile applications with modern cross-platform technologies and polished user experiences.
              </p>
            </div>
            <div>
              <div class="flex flex-wrap gap-1.5 mb-4 text-[10px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">React Native</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">iOS / Android</span>
              </div>
              <a href="#contact" class="text-xs font-semibold text-indigo-400 group-hover:text-pink-300 flex items-center gap-1">
                Explore Mobile →
              </a>
            </div>
          </div>

          <!-- Service 7: Business Automation -->
          <div class="service-card group p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/50 hover:bg-slate-900/90 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="w-12 h-12 rounded-xl bg-amber-600/20 border border-amber-500/30 flex items-center justify-center text-amber-400 group-hover:scale-110 group-hover:text-amber-300 transition-all mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">Business Automation</h3>
              <p class="text-slate-400 text-sm leading-relaxed mb-4">
                Automate repetitive business processes by connecting CRM, ERP, payments, APIs, databases, webhooks, and AI-powered workflows.
              </p>
            </div>
            <div>
              <div class="flex flex-wrap gap-1.5 mb-4 text-[10px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Webhooks</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">API Pipeline</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Workflows</span>
              </div>
              <a href="#contact" class="text-xs font-semibold text-indigo-400 group-hover:text-amber-300 flex items-center gap-1">
                Automate Ops →
              </a>
            </div>
          </div>

          <!-- Service 8: E-commerce Development -->
          <div class="service-card group p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/50 hover:bg-slate-900/90 transition-all duration-300 flex flex-col justify-between">
            <div>
              <div class="w-12 h-12 rounded-xl bg-teal-600/20 border border-teal-500/30 flex items-center justify-center text-teal-400 group-hover:scale-110 group-hover:text-teal-300 transition-all mb-5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">Modern E-commerce</h3>
              <p class="text-slate-400 text-sm leading-relaxed mb-4">
                Build scalable e-commerce platforms with custom storefronts, checkout systems, inventory integrations, and international payment solutions.
              </p>
            </div>
            <div>
              <div class="flex flex-wrap gap-1.5 mb-4 text-[10px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Headless</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Stripe</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Custom Cart</span>
              </div>
              <a href="#contact" class="text-xs font-semibold text-indigo-400 group-hover:text-teal-300 flex items-center gap-1">
                Build Store →
              </a>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ==================== 4. AI & AUTOMATION FEATURE ==================== -->
    <section id="ai-automation" class="py-24 relative overflow-hidden">
      <!-- Glow -->
      <div class="absolute top-1/2 right-0 w-96 h-96 bg-cyan-600/15 blur-[140px] rounded-full pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          
          <div class="lg:col-span-5 space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-mono">
              AI FOR BUSINESS
            </div>
            <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight leading-tight">
              MAKE AI WORK FOR YOUR BUSINESS.
            </h2>
            <p class="text-slate-300 text-lg leading-relaxed">
              AI isn't just a chatbot. We help businesses identify where intelligent software can reduce repetitive work, improve customer experiences, and unlock entirely new revenue opportunities.
            </p>

            <div class="grid grid-cols-2 gap-4 pt-2">
              <div class="p-3.5 rounded-xl bg-slate-900/80 border border-white/10">
                <p class="text-white font-semibold text-sm">AI Agents</p>
                <p class="text-xs text-slate-400 mt-1">Autonomous workflows</p>
              </div>
              <div class="p-3.5 rounded-xl bg-slate-900/80 border border-white/10">
                <p class="text-white font-semibold text-sm">Document AI</p>
                <p class="text-xs text-slate-400 mt-1">Instant OCR & extraction</p>
              </div>
              <div class="p-3.5 rounded-xl bg-slate-900/80 border border-white/10">
                <p class="text-white font-semibold text-sm">Voice AI</p>
                <p class="text-xs text-slate-400 mt-1">Real-time speech agents</p>
              </div>
              <div class="p-3.5 rounded-xl bg-slate-900/80 border border-white/10">
                <p class="text-white font-semibold text-sm">AI-Powered SaaS</p>
                <p class="text-xs text-slate-400 mt-1">Intelligence inside apps</p>
              </div>
            </div>

            <div class="pt-4">
              <a href="#contact" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold transition-all shadow-lg shadow-cyan-500/25">
                Build an AI Solution →
              </a>
            </div>
          </div>

          <!-- Interactive AI Pipeline Simulator -->
          <div class="lg:col-span-7">
            <div class="p-6 sm:p-8 rounded-2xl bg-slate-950 border border-cyan-500/30 shadow-2xl relative">
              <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-6">
                <div>
                  <h4 class="text-sm font-semibold text-white font-mono">LIVE AI WORKFLOW SIMULATION</h4>
                  <p class="text-xs text-slate-400">Click step to simulate pipeline dispatch</p>
                </div>
                <button id="run-ai-pipeline" class="px-3 py-1.5 rounded-lg bg-cyan-500/20 text-cyan-300 border border-cyan-400/40 text-xs font-mono hover:bg-cyan-500/30 transition-all">
                  ▶ Run Test Pipeline
                </button>
              </div>

              <!-- Pipeline Steps Diagram -->
              <div class="space-y-4 font-mono text-xs">
                
                <div id="step-1" class="ai-step p-4 rounded-xl bg-slate-900 border border-white/10 flex items-center justify-between transition-all">
                  <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-indigo-600/30 text-indigo-400 flex items-center justify-center font-bold">1</span>
                    <div>
                      <p class="text-white font-semibold">Incoming Business Event / Data</p>
                      <p class="text-slate-400 text-[11px]">Unstructured email, customer PDF, or webhook event</p>
                    </div>
                  </div>
                  <span class="status-badge px-2 py-0.5 rounded bg-slate-800 text-slate-400">Ready</span>
                </div>

                <div id="step-2" class="ai-step p-4 rounded-xl bg-slate-900 border border-white/10 flex items-center justify-between transition-all">
                  <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-purple-600/30 text-purple-400 flex items-center justify-center font-bold">2</span>
                    <div>
                      <p class="text-white font-semibold">LLM + Vector Context Extraction</p>
                      <p class="text-slate-400 text-[11px]">Entity matching, confidence scoring, security guardrails</p>
                    </div>
                  </div>
                  <span class="status-badge px-2 py-0.5 rounded bg-slate-800 text-slate-400">Idle</span>
                </div>

                <div id="step-3" class="ai-step p-4 rounded-xl bg-slate-900 border border-white/10 flex items-center justify-between transition-all">
                  <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-cyan-600/30 text-cyan-400 flex items-center justify-center font-bold">3</span>
                    <div>
                      <p class="text-white font-semibold">Autonomous Decision Engine</p>
                      <p class="text-slate-400 text-[11px]">Validates business logic and verifies required credentials</p>
                    </div>
                  </div>
                  <span class="status-badge px-2 py-0.5 rounded bg-slate-800 text-slate-400">Idle</span>
                </div>

                <div id="step-4" class="ai-step p-4 rounded-xl bg-slate-900 border border-white/10 flex items-center justify-between transition-all">
                  <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-emerald-600/30 text-emerald-400 flex items-center justify-center font-bold">4</span>
                    <div>
                      <p class="text-white font-semibold">Action Dispatch & CRM Sync</p>
                      <p class="text-slate-400 text-[11px]">Database write, client email sent, executive report filed</p>
                    </div>
                  </div>
                  <span class="status-badge px-2 py-0.5 rounded bg-slate-800 text-slate-400">Idle</span>
                </div>

              </div>

              <div id="pipeline-output" class="mt-6 p-4 rounded-xl bg-slate-900/90 border border-white/10 font-mono text-[11px] text-slate-300">
                <span class="text-cyan-400">// Output Console:</span> Click "Run Test Pipeline" to simulate an end-to-end AI automation flow.
              </div>

            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- ==================== 5. MVP FAST-TRACK SECTION ==================== -->
    <section id="mvp" class="py-24 bg-slate-950/80 border-t border-white/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-mono mb-4">
            FOR FOUNDERS & STARTUPS
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
            HAVE AN IDEA? START WITH AN MVP.
          </h2>
          <p class="mt-4 text-slate-400 text-lg leading-relaxed">
            You don't need to build everything on day one. We help founders turn ideas into focused, high-performance MVPs that can be launched, tested, and validated with real users in weeks.
          </p>
        </div>

        <!-- Roadmap Timeline -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 text-center">
          
          <div class="p-4 rounded-xl bg-slate-900 border border-white/10">
            <span class="text-xs font-mono text-indigo-400 font-bold">01</span>
            <h4 class="text-sm font-bold text-white mt-1">IDEA</h4>
            <p class="text-[11px] text-slate-400 mt-1">Core value prop</p>
          </div>

          <div class="p-4 rounded-xl bg-slate-900 border border-white/10">
            <span class="text-xs font-mono text-indigo-400 font-bold">02</span>
            <h4 class="text-sm font-bold text-white mt-1">PLAN</h4>
            <p class="text-[11px] text-slate-400 mt-1">Lean scope & specs</p>
          </div>

          <div class="p-4 rounded-xl bg-slate-900 border border-white/10">
            <span class="text-xs font-mono text-indigo-400 font-bold">03</span>
            <h4 class="text-sm font-bold text-white mt-1">DESIGN</h4>
            <p class="text-[11px] text-slate-400 mt-1">Polished UI/UX</p>
          </div>

          <div class="p-4 rounded-xl bg-slate-900 border border-indigo-500/40 bg-indigo-950/20">
            <span class="text-xs font-mono text-cyan-400 font-bold">04</span>
            <h4 class="text-sm font-bold text-cyan-300 mt-1">MVP</h4>
            <p class="text-[11px] text-slate-300 mt-1">Production build</p>
          </div>

          <div class="p-4 rounded-xl bg-slate-900 border border-white/10">
            <span class="text-xs font-mono text-indigo-400 font-bold">05</span>
            <h4 class="text-sm font-bold text-white mt-1">USERS</h4>
            <p class="text-[11px] text-slate-400 mt-1">Early onboarding</p>
          </div>

          <div class="p-4 rounded-xl bg-slate-900 border border-white/10">
            <span class="text-xs font-mono text-indigo-400 font-bold">06</span>
            <h4 class="text-sm font-bold text-white mt-1">FEEDBACK</h4>
            <p class="text-[11px] text-slate-400 mt-1">Metric analysis</p>
          </div>

          <div class="p-4 rounded-xl bg-slate-900 border border-white/10">
            <span class="text-xs font-mono text-indigo-400 font-bold">07</span>
            <h4 class="text-sm font-bold text-white mt-1">SCALE</h4>
            <p class="text-[11px] text-slate-400 mt-1">Fund & expand</p>
          </div>

        </div>

        <div class="mt-12 text-center">
          <a href="#contact" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 font-bold transition-all shadow-xl shadow-emerald-500/20">
            Discuss My MVP Idea →
          </a>
        </div>

      </div>
    </section>

    <!-- ==================== 6. CONCEPT PROJECTS ==================== -->
    <section id="concepts" class="py-24 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
          <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-mono mb-4">
              CAPABILITY SHOWCASE
            </div>
            <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
              WHAT WE CAN BUILD
            </h2>
            <p class="mt-3 text-slate-400 text-lg max-w-2xl">
              A curated selection of internal concepts and architectural demonstration prototypes created by our engineering team to showcase our technical breadth.
            </p>
          </div>

          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-mono">
            <span>ℹ️</span> Every demonstration here is an internal concept project.
          </div>
        </div>

        <!-- 6 Concept Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          
          <!-- Concept 1 -->
          <div class="concept-card rounded-2xl bg-slate-900/70 border border-white/10 hover:border-cyan-500/40 overflow-hidden flex flex-col justify-between transition-all duration-300 group">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <span class="px-2.5 py-1 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] font-mono font-bold tracking-wider uppercase">
                  CONCEPT PROJECT
                </span>
                <span class="text-xs font-mono text-slate-400">01 / 06</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">AI Business OS</h3>
              <p class="text-xs text-slate-400 mb-4 font-mono">Autonomous Enterprise Operations Platform</p>
              
              <!-- Mockup Frame -->
              <div class="p-4 rounded-xl bg-slate-950 border border-white/10 mb-4 font-mono text-xs text-slate-300">
                <div class="flex items-center justify-between text-[10px] text-slate-500 mb-2 border-b border-white/5 pb-1">
                  <span>MODULE // WORKFLOW_AI</span>
                  <span class="text-emerald-400">STATUS: OPTIMAL</span>
                </div>
                <p class="text-cyan-400">▶ Live Agent routing 42 enterprise leads</p>
                <p class="text-slate-400 mt-1">▶ Continuous RAG sync across 14 data sources</p>
              </div>

              <div class="space-y-2 text-xs text-slate-300">
                <p><strong class="text-white">Problem:</strong> Disconnected legacy apps causing siloed data and manual handoffs.</p>
                <p><strong class="text-white">Solution:</strong> Unified multi-agent orchestrator with vector search and live webhooks.</p>
              </div>
            </div>

            <div class="p-6 pt-0">
              <div class="flex flex-wrap gap-1.5 mb-4 text-[10px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">FastAPI</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">LangChain</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">pgVector</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">React</span>
              </div>
              <button class="view-concept-btn w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors" data-concept="01">
                View Concept Specs →
              </button>
            </div>
          </div>

          <!-- Concept 2 -->
          <div class="concept-card rounded-2xl bg-slate-900/70 border border-white/10 hover:border-cyan-500/40 overflow-hidden flex flex-col justify-between transition-all duration-300 group">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <span class="px-2.5 py-1 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] font-mono font-bold tracking-wider uppercase">
                  CONCEPT PROJECT
                </span>
                <span class="text-xs font-mono text-slate-400">02 / 06</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">CommerceFlow</h3>
              <p class="text-xs text-slate-400 mb-4 font-mono">Modern Multi-Storefront Commerce Engine</p>
              
              <div class="p-4 rounded-xl bg-slate-950 border border-white/10 mb-4 font-mono text-xs text-slate-300">
                <div class="flex items-center justify-between text-[10px] text-slate-500 mb-2 border-b border-white/5 pb-1">
                  <span>GATEWAY // GLOBAL_CHECKOUT</span>
                  <span class="text-emerald-400">99.99% CONV</span>
                </div>
                <p class="text-purple-400">▶ Multi-currency: USD / CAD / AED / QAR</p>
                <p class="text-slate-400 mt-1">▶ Sub-millisecond headless edge routing</p>
              </div>

              <div class="space-y-2 text-xs text-slate-300">
                <p><strong class="text-white">Problem:</strong> Sluggish e-commerce templates causing checkout abandonments.</p>
                <p><strong class="text-white">Solution:</strong> Headless Next.js edge storefront with dynamic inventory syncing.</p>
              </div>
            </div>

            <div class="p-6 pt-0">
              <div class="flex flex-wrap gap-1.5 mb-4 text-[10px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Next.js 15</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Stripe</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">GraphQL</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Redis</span>
              </div>
              <button class="view-concept-btn w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors" data-concept="02">
                View Concept Specs →
              </button>
            </div>
          </div>

          <!-- Concept 3 -->
          <div class="concept-card rounded-2xl bg-slate-900/70 border border-white/10 hover:border-cyan-500/40 overflow-hidden flex flex-col justify-between transition-all duration-300 group">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <span class="px-2.5 py-1 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] font-mono font-bold tracking-wider uppercase">
                  CONCEPT PROJECT
                </span>
                <span class="text-xs font-mono text-slate-400">03 / 06</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">LifeOS</h3>
              <p class="text-xs text-slate-400 mb-4 font-mono">Personal AI Productivity & Knowledge Hub</p>
              
              <div class="p-4 rounded-xl bg-slate-950 border border-white/10 mb-4 font-mono text-xs text-slate-300">
                <div class="flex items-center justify-between text-[10px] text-slate-500 mb-2 border-b border-white/5 pb-1">
                  <span>INTELLIGENCE // VOICE_TO_ACTION</span>
                  <span class="text-cyan-400">ACTIVE</span>
                </div>
                <p class="text-emerald-400">▶ Voice memo parsed into 4 calendar actions</p>
                <p class="text-slate-400 mt-1">▶ Semantic search across 10,000 personal notes</p>
              </div>

              <div class="space-y-2 text-xs text-slate-300">
                <p><strong class="text-white">Problem:</strong> Information overload across fragmented notes, bookmarks, and audio.</p>
                <p><strong class="text-white">Solution:</strong> On-device vector retrieval paired with voice-driven AI synthesis.</p>
              </div>
            </div>

            <div class="p-6 pt-0">
              <div class="flex flex-wrap gap-1.5 mb-4 text-[10px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">React Native</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Whisper AI</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">SQLite</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Tailwind</span>
              </div>
              <button class="view-concept-btn w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors" data-concept="03">
                View Concept Specs →
              </button>
            </div>
          </div>

          <!-- Concept 4 -->
          <div class="concept-card rounded-2xl bg-slate-900/70 border border-white/10 hover:border-cyan-500/40 overflow-hidden flex flex-col justify-between transition-all duration-300 group">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <span class="px-2.5 py-1 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] font-mono font-bold tracking-wider uppercase">
                  CONCEPT PROJECT
                </span>
                <span class="text-xs font-mono text-slate-400">04 / 06</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">GlobalDesk</h3>
              <p class="text-xs text-slate-400 mb-4 font-mono">Distributed Operations & Workflow Suite</p>
              
              <div class="p-4 rounded-xl bg-slate-950 border border-white/10 mb-4 font-mono text-xs text-slate-300">
                <div class="flex items-center justify-between text-[10px] text-slate-500 mb-2 border-b border-white/5 pb-1">
                  <span>DISPATCH // TIMEZONE_ORCHESTRATOR</span>
                  <span class="text-purple-400">SYNCED</span>
                </div>
                <p class="text-indigo-300">▶ Asynchronous handover protocol between US & Asia</p>
                <p class="text-slate-400 mt-1">▶ Automated SLA escalation triggers</p>
              </div>

              <div class="space-y-2 text-xs text-slate-300">
                <p><strong class="text-white">Problem:</strong> Cross-border team communication friction and missed SLAs.</p>
                <p><strong class="text-white">Solution:</strong> Automated task handoff engine with real-time audit trails.</p>
              </div>
            </div>

            <div class="p-6 pt-0">
              <div class="flex flex-wrap gap-1.5 mb-4 text-[10px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">TypeScript</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Node.js</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">WebSockets</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Docker</span>
              </div>
              <button class="view-concept-btn w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors" data-concept="04">
                View Concept Specs →
              </button>
            </div>
          </div>

          <!-- Concept 5 -->
          <div class="concept-card rounded-2xl bg-slate-900/70 border border-white/10 hover:border-cyan-500/40 overflow-hidden flex flex-col justify-between transition-all duration-300 group">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <span class="px-2.5 py-1 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] font-mono font-bold tracking-wider uppercase">
                  CONCEPT PROJECT
                </span>
                <span class="text-xs font-mono text-slate-400">05 / 06</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">SmartCRM</h3>
              <p class="text-xs text-slate-400 mb-4 font-mono">AI-Assisted Customer Relationship Engine</p>
              
              <div class="p-4 rounded-xl bg-slate-950 border border-white/10 mb-4 font-mono text-xs text-slate-300">
                <div class="flex items-center justify-between text-[10px] text-slate-500 mb-2 border-b border-white/5 pb-1">
                  <span>INTELLIGENCE // LEAD_SCORING</span>
                  <span class="text-cyan-400">PREDICTIVE</span>
                </div>
                <p class="text-emerald-400">▶ Deal close probability: 87.4%</p>
                <p class="text-slate-400 mt-1">▶ Auto-generated tailored proposal draft</p>
              </div>

              <div class="space-y-2 text-xs text-slate-300">
                <p><strong class="text-white">Problem:</strong> Sales reps spending 40% of their workday doing manual data entry.</p>
                <p><strong class="text-white">Solution:</strong> Auto-enriching CRM that transcribes calls and drafts pipeline updates.</p>
              </div>
            </div>

            <div class="p-6 pt-0">
              <div class="flex flex-wrap gap-1.5 mb-4 text-[10px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Python</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">FastAPI</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">OpenAI</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">PostgreSQL</span>
              </div>
              <button class="view-concept-btn w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors" data-concept="05">
                View Concept Specs →
              </button>
            </div>
          </div>

          <!-- Concept 6 -->
          <div class="concept-card rounded-2xl bg-slate-900/70 border border-white/10 hover:border-cyan-500/40 overflow-hidden flex flex-col justify-between transition-all duration-300 group">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <span class="px-2.5 py-1 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] font-mono font-bold tracking-wider uppercase">
                  CONCEPT PROJECT
                </span>
                <span class="text-xs font-mono text-slate-400">06 / 06</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">FinancePilot</h3>
              <p class="text-xs text-slate-400 mb-4 font-mono">Real-Time Financial Intelligence Dashboard</p>
              
              <div class="p-4 rounded-xl bg-slate-950 border border-white/10 mb-4 font-mono text-xs text-slate-300">
                <div class="flex items-center justify-between text-[10px] text-slate-500 mb-2 border-b border-white/5 pb-1">
                  <span>METRICS // RUNWAY_FORECAST</span>
                  <span class="text-emerald-400">REALTIME</span>
                </div>
                <p class="text-indigo-400">▶ Burn Rate: $14.2k/mo • Runway: 18 months</p>
                <p class="text-slate-400 mt-1">▶ Multi-entity tax calculation simulator</p>
              </div>

              <div class="space-y-2 text-xs text-slate-300">
                <p><strong class="text-white">Problem:</strong> Delayed end-of-month spreadsheets leaving founders blind to unit economics.</p>
                <p><strong class="text-white">Solution:</strong> Live multi-bank sync with predictive cashflow machine learning models.</p>
              </div>
            </div>

            <div class="p-6 pt-0">
              <div class="flex flex-wrap gap-1.5 mb-4 text-[10px] font-mono text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">React</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Plaid API</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">Tailwind</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-white/5">ClickHouse</span>
              </div>
              <button class="view-concept-btn w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors" data-concept="06">
                View Concept Specs →
              </button>
            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- ==================== 7. HONEST STARTUP SECTION ==================== -->
    <section class="py-20 bg-slate-950 border-y border-white/10 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-3xl mx-auto text-center mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-mono mb-4">
            TRANSPARENCY & VALUES
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
            We're New. And We're Here to Prove Ourselves.
          </h2>
          <p class="mt-4 text-slate-300 text-lg leading-relaxed">
            We're at the beginning of our journey, which means every project matters. We're building our reputation one product at a time through direct communication, thoughtful engineering, and a relentless focus on quality.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          
          <div class="p-8 rounded-2xl bg-slate-900/80 border border-white/10 hover:border-indigo-500/40 transition-all">
            <div class="w-12 h-12 rounded-xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 mb-6 font-mono font-bold text-lg">
              01
            </div>
            <h3 class="text-xl font-bold text-white mb-3">Direct Access</h3>
            <p class="text-slate-400 leading-relaxed text-sm">
              Work directly with the team and founder building your product. No middle managers, no lost context, and zero communication barriers.
            </p>
          </div>

          <div class="p-8 rounded-2xl bg-slate-900/80 border border-white/10 hover:border-cyan-500/40 transition-all">
            <div class="w-12 h-12 rounded-xl bg-cyan-600/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 mb-6 font-mono font-bold text-lg">
              02
            </div>
            <h3 class="text-xl font-bold text-white mb-3">No Unnecessary Layers</h3>
            <p class="text-slate-400 leading-relaxed text-sm">
              Clear, transparent communication without complicated legacy agency structures or inflated enterprise markups.
            </p>
          </div>

          <div class="p-8 rounded-2xl bg-slate-900/80 border border-white/10 hover:border-purple-500/40 transition-all">
            <div class="w-12 h-12 rounded-xl bg-purple-600/20 border border-purple-500/30 flex items-center justify-center text-purple-400 mb-6 font-mono font-bold text-lg">
              03
            </div>
            <h3 class="text-xl font-bold text-white mb-3">Every Project Matters</h3>
            <p class="text-slate-400 leading-relaxed text-sm">
              Your project isn't ticket #400 in a queue. Your product directly shapes the founding reputation we are actively building.
            </p>
          </div>

        </div>

      </div>
    </section>

    <!-- ==================== 8. MARKETS SECTION ==================== -->
    <section id="markets" class="py-24 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-mono mb-4">
            GLOBAL CLIENT FOCUS
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
              AI & SOFTWARE DEVELOPMENT FOR GLOBAL BUSINESSES

        </h2>
          <p class="mt-4 text-slate-400 text-lg">
                RABSS Technologies provides custom AI development, software development, SaaS, MVP, and business automation solutions for startups and growing businesses in the USA, Canada, UAE, Qatar, and Nepal.

          </p>
        </div>

        <!-- 4 Country Cards with Live Timezones -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          
          <!-- USA -->
          <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 hover:border-indigo-500/50 transition-all flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-3xl">🇺🇸</span>
                <span class="market-clock text-xs font-mono text-cyan-400" data-tz="America/New_York">EST: --:--</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-1">United States</h3>
              <p class="text-xs text-indigo-300 font-mono mb-4">AI & Custom Software Development for US Startups</p>
              
              <ul class="space-y-2 text-xs text-slate-300 mb-6">
                <li class="flex items-center gap-2"><span>✓</span> AI Agents & LLM Workflows</li>
                <li class="flex items-center gap-2"><span>✓</span> Multi-tenant SaaS Platforms</li>
                <li class="flex items-center gap-2"><span>✓</span> Rapid MVP for Seed Founders</li>
                <li class="flex items-center gap-2"><span>✓</span> Custom Enterprise Software</li>
              </ul>
            </div>
            <a href="#contact" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white text-xs font-semibold text-center transition-colors">
              Explore USA Solutions →
            </a>
          </div>
        <!-----Nepal---> 
        <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 hover:border-indigo-500/50 transition-all flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-3xl">🇳🇵</span>
                <span class="market-clock text-xs font-mono text-cyan-400" data-tz="Asia/Kathmandu">NPT: --:--</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-1">Nepal</h3>
              <p class="text-xs text-indigo-300 font-mono mb-4">RABSS Technologies is a Nepal-based software development company building AI solutions, custom software, SaaS platforms, and MVPs for businesses and startups in Nepal and international markets.</p>
              
              <ul class="space-y-2 text-xs text-slate-300 mb-6">
                <li class="flex items-center gap-2"><span>✓</span> High-Performance Web Apps</li>
                <li class="flex items-center gap-2"><span>✓</span> Workflow Automation Pipelines</li>
                <li class="flex items-center gap-2"><span>✓</span> AI System Integration</li>
                <li class="flex items-center gap-2"><span>✓</span> Mobile Applications (iOS/Android)</li>
              </ul>
            </div>
            <a href="#contact" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white text-xs font-semibold text-center transition-colors">
              Explore Nepal Solutions →
            </a>
          </div>
          <!-- Canada -->
          <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 hover:border-indigo-500/50 transition-all flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-3xl">🇨🇦</span>
                <span class="market-clock text-xs font-mono text-cyan-400" data-tz="America/Toronto">EST: --:--</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-1">Canada</h3>
              <p class="text-xs text-indigo-300 font-mono mb-4">AI, SaaS & Digital Product Development for Canadian Businesses</p>
              
              <ul class="space-y-2 text-xs text-slate-300 mb-6">
                <li class="flex items-center gap-2"><span>✓</span> High-Performance Web Apps</li>
                <li class="flex items-center gap-2"><span>✓</span> Workflow Automation Pipelines</li>
                <li class="flex items-center gap-2"><span>✓</span> AI System Integration</li>
                <li class="flex items-center gap-2"><span>✓</span> Mobile Applications (iOS/Android)</li>
              </ul>
            </div>
            <a href="#contact" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white text-xs font-semibold text-center transition-colors">
              Explore Canada Solutions →
            </a>
          </div>

          <!-- UAE -->
          <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 hover:border-indigo-500/50 transition-all flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-3xl">🇦🇪</span>
                <span class="market-clock text-xs font-mono text-cyan-400" data-tz="Asia/Dubai">GST: --:--</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-1">United Arab Emirates</h3>
              <p class="text-xs text-indigo-300 font-mono mb-4">AI Automation & Custom Software Development in the UAE</p>
              
              <ul class="space-y-2 text-xs text-slate-300 mb-6">
                <li class="flex items-center gap-2"><span>✓</span> Intelligent Customer Automation</li>
                <li class="flex items-center gap-2"><span>✓</span> Custom ERP / CRM Integrations</li>
                <li class="flex items-center gap-2"><span>✓</span> Headless E-commerce Solutions</li>
                <li class="flex items-center gap-2"><span>✓</span> Document AI & Extraction</li>
              </ul>
            </div>
            <a href="#contact" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white text-xs font-semibold text-center transition-colors">
              Explore UAE Solutions →
            </a>
          </div>

          <!-- Qatar -->
          <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 hover:border-indigo-500/50 transition-all flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-3xl">🇶🇦</span>
                <span class="market-clock text-xs font-mono text-cyan-400" data-tz="Asia/Qatar">AST: --:--</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-1">Qatar</h3>
              <p class="text-xs text-indigo-300 font-mono mb-4">AI, Software & Digital Solutions for Qatar Businesses</p>
              
              <ul class="space-y-2 text-xs text-slate-300 mb-6">
                <li class="flex items-center gap-2"><span>✓</span> SME Digitalization Platforms</li>
                <li class="flex items-center gap-2"><span>✓</span> Custom Web Portals & Apps</li>
                <li class="flex items-center gap-2"><span>✓</span> Cloud Architecture & Security</li>
                <li class="flex items-center gap-2"><span>✓</span> AI Operational Optimization</li>
              </ul>
            </div>
            <a href="#contact" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white text-xs font-semibold text-center transition-colors">
              Explore Qatar Solutions →
            </a>
          </div>

        </div>

      </div>
    </section>

    <!-- ==================== 9. WHY CHOOSE US ==================== -->
    <section class="py-20 bg-slate-950/60 border-t border-white/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-mono mb-4">
            AGILITY & ADVANTAGE
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
            A SMALL TEAM CAN MOVE FAST.
          </h2>
          <p class="mt-4 text-slate-400 text-lg">
            Big agencies give you account executives and bureaucracy. We give you senior technical execution and speed.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          
          <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10">
            <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
              <span class="text-cyan-400">⚡</span> Direct Communication
            </h3>
            <p class="text-slate-400 text-sm leading-relaxed">
              Talk directly via Slack, Discord, or Google Meet with the engineers writing your code and designing your systems.
            </p>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10">
            <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
              <span class="text-indigo-400">🔄</span> Adaptive & Flexible
            </h3>
            <p class="text-slate-400 text-sm leading-relaxed">
              We adapt sprints, priorities, and feature requirements around your evolving business goals without red tape.
            </p>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10">
            <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
              <span class="text-purple-400">🚀</span> Modern Technology
            </h3>
            <p class="text-slate-400 text-sm leading-relaxed">
              We build on contemporary foundations: Next.js 15, FastAPI, TypeScript, pgVector, and Docker for long-term scalability.
            </p>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10">
            <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
              <span class="text-emerald-400">🛡️</span> Radical Transparency
            </h3>
            <p class="text-slate-400 text-sm leading-relaxed">
              Clear scope, structured sprint milestones, daily GitHub commits, and no hidden line items or surprise fees.
            </p>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10">
            <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
              <span class="text-pink-400">🤝</span> Founder-Friendly
            </h3>
            <p class="text-slate-400 text-sm leading-relaxed">
              We understand budget constraints, investor demo deadlines, and the crucial balance between speed and architectural integrity.
            </p>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10">
            <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
              <span class="text-amber-400">🌱</span> Long-Term Partnership
            </h3>
            <p class="text-slate-400 text-sm leading-relaxed">
              We don't abandon code at launch. We partner with you to monitor, optimize, add features, and scale as your userbase expands.
            </p>
          </div>

        </div>

      </div>
    </section>

    <!-- ==================== 10. DEVELOPMENT PROCESS ==================== -->
    <section id="process" class="py-24 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-mono mb-4">
            METHODOLOGY
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
            FROM IDEA TO LAUNCH.
          </h2>
          <p class="mt-4 text-slate-400 text-lg">
            A battle-tested 7-phase delivery pipeline engineered for speed, security, and predictability.
          </p>
        </div>

        <div class="space-y-6 max-w-4xl mx-auto">
          
          <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
              <span class="font-mono text-cyan-400 font-bold text-lg">01</span>
              <div>
                <h3 class="text-lg font-bold text-white">DISCOVER</h3>
                <p class="text-slate-400 text-sm">Deep dive into your business model, target users, problem statement, and success criteria.</p>
              </div>
            </div>
            <span class="text-xs font-mono px-3 py-1 rounded bg-slate-800 text-slate-300 self-start md:self-auto">Days 1–3</span>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
              <span class="font-mono text-cyan-400 font-bold text-lg">02</span>
              <div>
                <h3 class="text-lg font-bold text-white">PLAN</h3>
                <p class="text-slate-400 text-sm">Define technical architecture, database schemas, API specs, and sprint milestone deliverables.</p>
              </div>
            </div>
            <span class="text-xs font-mono px-3 py-1 rounded bg-slate-800 text-slate-300 self-start md:self-auto">Days 4–7</span>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
              <span class="font-mono text-cyan-400 font-bold text-lg">03</span>
              <div>
                <h3 class="text-lg font-bold text-white">DESIGN</h3>
                <p class="text-slate-400 text-sm">Craft high-fidelity Figma UI/UX mockups, interactive prototypes, and atomic component systems.</p>
              </div>
            </div>
            <span class="text-xs font-mono px-3 py-1 rounded bg-slate-800 text-slate-300 self-start md:self-auto">Week 2</span>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/70 border border-indigo-500/40 bg-indigo-950/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
              <span class="font-mono text-indigo-400 font-bold text-lg">04</span>
              <div>
                <h3 class="text-lg font-bold text-white">BUILD</h3>
                <p class="text-slate-300 text-sm">Clean, modular code execution with continuous CI/CD, daily commits, and staging environment access.</p>
              </div>
            </div>
            <span class="text-xs font-mono px-3 py-1 rounded bg-indigo-600/30 text-indigo-300 self-start md:self-auto">Weeks 3–6</span>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
              <span class="font-mono text-cyan-400 font-bold text-lg">05</span>
              <div>
                <h3 class="text-lg font-bold text-white">TEST</h3>
                <p class="text-slate-400 text-sm">Automated unit testing, end-to-end integration tests, load testing, and rigorous security audits.</p>
              </div>
            </div>
            <span class="text-xs font-mono px-3 py-1 rounded bg-slate-800 text-slate-300 self-start md:self-auto">Week 7</span>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
              <span class="font-mono text-cyan-400 font-bold text-lg">06</span>
              <div>
                <h3 class="text-lg font-bold text-white">LAUNCH</h3>
                <p class="text-slate-400 text-sm">Production deployment to AWS/Vercel/Docker clusters, domain routing, SSL, and telemetry setup.</p>
              </div>
            </div>
            <span class="text-xs font-mono px-3 py-1 rounded bg-slate-800 text-slate-300 self-start md:self-auto">Week 8</span>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/70 border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
              <span class="font-mono text-cyan-400 font-bold text-lg">07</span>
              <div>
                <h3 class="text-lg font-bold text-white">SCALE</h3>
                <p class="text-slate-400 text-sm">Ongoing monitoring, performance tuning, user feedback integration, and iterative sprint scaling.</p>
              </div>
            </div>
            <span class="text-xs font-mono px-3 py-1 rounded bg-slate-800 text-slate-300 self-start md:self-auto">Ongoing</span>
          </div>

        </div>

      </div>
    </section>

    <!-- ==================== 11. TECHNOLOGY STACK ==================== -->
    <section class="py-20 bg-slate-950 border-t border-white/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-mono mb-4">
            TECHNICAL ECOSYSTEM
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
            BUILT WITH MODERN TECHNOLOGY.
          </h2>
          <p class="mt-4 text-slate-400 text-lg">
            We write clean, typed, maintainable code using the industry's most reliable and performant tools.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          
          <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10">
            <h3 class="text-sm font-bold font-mono text-cyan-400 uppercase tracking-wider mb-4">// Frontend</h3>
            <ul class="space-y-3 font-mono text-sm text-slate-300">
              <li class="flex items-center justify-between"><span>React / Next.js 15</span><span class="text-slate-500 text-xs">SSR / Edge</span></li>
              <li class="flex items-center justify-between"><span>TypeScript</span><span class="text-slate-500 text-xs">Strict typed</span></li>
              <li class="flex items-center justify-between"><span>Tailwind CSS</span><span class="text-slate-500 text-xs">Design System</span></li>
              <li class="flex items-center justify-between"><span>Three.js</span><span class="text-slate-500 text-xs">3D / Canvas</span></li>
            </ul>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10">
            <h3 class="text-sm font-bold font-mono text-indigo-400 uppercase tracking-wider mb-4">// Backend</h3>
            <ul class="space-y-3 font-mono text-sm text-slate-300">
              <li class="flex items-center justify-between"><span>Python / FastAPI</span><span class="text-slate-500 text-xs">Async High-Perf</span></li>
              <li class="flex items-center justify-between"><span>Node.js / Express</span><span class="text-slate-500 text-xs">Microservices</span></li>
              <li class="flex items-center justify-between"><span>PostgreSQL</span><span class="text-slate-500 text-xs">ACID / pgVector</span></li>
              <li class="flex items-center justify-between"><span>Redis</span><span class="text-slate-500 text-xs">Cache & Queues</span></li>
            </ul>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10">
            <h3 class="text-sm font-bold font-mono text-purple-400 uppercase tracking-wider mb-4">// AI & Automation</h3>
            <ul class="space-y-3 font-mono text-sm text-slate-300">
              <li class="flex items-center justify-between"><span>LLMs & Function Calling</span><span class="text-slate-500 text-xs">OpenAI / Claude</span></li>
              <li class="flex items-center justify-between"><span>Autonomous AI Agents</span><span class="text-slate-500 text-xs">LangChain / Crew</span></li>
              <li class="flex items-center justify-between"><span>RAG Pipelines</span><span class="text-slate-500 text-xs">Hybrid Search</span></li>
              <li class="flex items-center justify-between"><span>Voice AI & Whisper</span><span class="text-slate-500 text-xs">Real-time Audio</span></li>
            </ul>
          </div>

          <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10">
            <h3 class="text-sm font-bold font-mono text-emerald-400 uppercase tracking-wider mb-4">// Cloud & DevOps</h3>
            <ul class="space-y-3 font-mono text-sm text-slate-300">
              <li class="flex items-center justify-between"><span>Amazon Web Services</span><span class="text-slate-500 text-xs">ECS / Lambda</span></li>
              <li class="flex items-center justify-between"><span>Docker Containers</span><span class="text-slate-500 text-xs">Reproducible</span></li>
              <li class="flex items-center justify-between"><span>CI/CD Pipelines</span><span class="text-slate-500 text-xs">GitHub Actions</span></li>
              <li class="flex items-center justify-between"><span>Vercel / Cloudflare</span><span class="text-slate-500 text-xs">Global Edge</span></li>
            </ul>
          </div>

        </div>

      </div>
    </section>

    <!-- ==================== 12. FOUNDING CUSTOMER PROGRAM ==================== -->
    <section class="py-24 relative overflow-hidden bg-gradient-to-b from-indigo-950/40 via-slate-950 to-slate-950 border-y border-indigo-500/20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="max-w-4xl mx-auto p-8 sm:p-12 rounded-3xl bg-slate-900/90 border border-indigo-500/30 backdrop-blur-2xl shadow-2xl">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-500/20 border border-indigo-500/40 text-indigo-300 text-xs font-mono mb-6">
            FOUNDING CUSTOMER PROGRAM
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight leading-tight">
            BE ONE OF OUR FIRST PRODUCTS.
          </h2>
          <p class="mt-4 text-slate-300 text-lg leading-relaxed">
            We're opening a limited number of project slots for ambitious businesses and founders who want to build with our team during the early stage of our journey.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-8">
            <div class="p-4 rounded-xl bg-slate-950/60 border border-white/10 flex items-start gap-3">
              <span class="text-cyan-400 font-bold">✓</span>
              <div>
                <h4 class="text-sm font-bold text-white">Direct Founder & CEO Collaboration</h4>
                <p class="text-xs text-slate-400 mt-0.5">Direct communication line with Subash Sitaula throughout delivery.</p>
              </div>
            </div>
            <div class="p-4 rounded-xl bg-slate-950/60 border border-white/10 flex items-start gap-3">
              <span class="text-cyan-400 font-bold">✓</span>
              <div>
                <h4 class="text-sm font-bold text-white">Priority Attention</h4>
                <p class="text-xs text-slate-400 mt-0.5">Your project receives 100% focused bandwidth as a marquee build.</p>
              </div>
            </div>
            <div class="p-4 rounded-xl bg-slate-950/60 border border-white/10 flex items-start gap-3">
              <span class="text-cyan-400 font-bold">✓</span>
              <div>
                <h4 class="text-sm font-bold text-white">Product Strategy Support</h4>
                <p class="text-xs text-slate-400 mt-0.5">Architecture review, MVP scoping, and tech roadmap planning.</p>
              </div>
            </div>
            <div class="p-4 rounded-xl bg-slate-950/60 border border-white/10 flex items-start gap-3">
              <span class="text-cyan-400 font-bold">✓</span>
              <div>
                <h4 class="text-sm font-bold text-white">Long-Term Partnership</h4>
                <p class="text-xs text-slate-400 mt-0.5">Preferential engineering bandwidth as your startup scales.</p>
              </div>
            </div>
          </div>

          <div class="pt-4 flex flex-col sm:flex-row items-center gap-4">
            <a href="#contact" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-cyan-400 to-indigo-500 hover:from-cyan-300 hover:to-indigo-400 text-slate-950 font-bold transition-all shadow-xl shadow-cyan-500/20 text-center">
              Apply for a Founding Project →
            </a>
            <span class="text-xs font-mono text-slate-400">Limited slots for Q3/Q4 builds</span>
          </div>

        </div>

      </div>
    </section>

    <!-- ==================== 13. ABOUT & FOUNDER ==================== -->
    <section id="about" class="py-24 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          
          <div class="lg:col-span-6 space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-mono">
              ABOUT RABSS TECHNOLOGIES
            </div>
            <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight leading-tight">
              WE'RE JUST GETTING STARTED.
            </h2>
            <p class="text-slate-300 text-lg leading-relaxed">
              We started RABSS Technologies with a simple, uncompromising belief: <strong class="text-white">ambitious ideas deserve great technology.</strong>
            </p>
            <p class="text-slate-400 leading-relaxed">
              We are building a modern software and AI studio focused on helping founders, startups, and international businesses turn bold concepts into useful, scalable, and resilient digital products.
            </p>
            <p class="text-slate-400 leading-relaxed">
              We're at the very beginning of our journey, and we're looking for ambitious partners in the USA, Canada, UAE, and Qatar who want to build something meaningful together.
            </p>

            <div class="pt-4 flex items-center gap-6">
              <div>
                <p class="font-bold text-white text-lg">Subash Sitaula</p>
                <p class="text-sm font-mono text-indigo-400">Founder & CEO, RABSS Technologies</p>
              </div>
            </div>
          </div>

          <!-- Founder Leadership Card -->
          <div class="lg:col-span-6">
            <div class="p-8 rounded-3xl bg-slate-900/80 border border-white/15 backdrop-blur-xl shadow-2xl relative overflow-hidden">
              <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-cyan-400 p-0.5">
                  <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center font-heading font-bold text-xl text-white">
                    SS
                  </div>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-white">Subash Sitaula</h3>
                  <p class="text-xs font-mono text-cyan-400">Founder & Chief Executive Officer</p>
                  <p class="text-xs text-slate-400">Kathmandu, Nepal • Global Clients</p>
                </div>
              </div>

              <blockquote class="text-slate-300 text-sm italic border-l-2 border-indigo-500 pl-4 py-1 mb-6">
                "When you partner with us, you are not handing your vision to an anonymous team. You get my personal commitment to architectural excellence, direct communication, and relentless execution."
              </blockquote>

              <div class="space-y-3 font-mono text-xs text-slate-300 pt-2 border-t border-white/10">
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Core Focus:</span>
                  <span class="text-white">AI Automation, SaaS, Full-Stack Architecture</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Timezone Support:</span>
                  <span class="text-emerald-400">US EST, Canada, UAE GST, Qatar AST</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-400">Direct Inquiries:</span>
                  <span class="text-indigo-300">rabsstechnologies@gmail.com</span>
                </div>
              </div>

            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- ==================== 14. FREE INTERACTIVE TOOLS ==================== -->
    <section id="tools" class="py-24 bg-slate-950 border-t border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-mono mb-4">
            FREE FOUNDER TOOLS
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
            ESTIMATE & PLAN YOUR PROJECT.
          </h2>
          <p class="mt-4 text-slate-400 text-lg">
            Use our interactive estimation and assessment engines to plan your software scope before you build.
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <!-- Tool 1: Interactive MVP Cost Calculator -->
          <div class="p-8 rounded-3xl bg-slate-900/80 border border-white/10 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                  <span>🧮</span> MVP Cost & Timeline Estimator
                </h3>
                <span class="text-xs font-mono text-cyan-400 px-2 py-0.5 rounded bg-cyan-500/10 border border-cyan-500/20">Interactive</span>
              </div>

              <form id="mvp-calc-form" class="space-y-4 text-sm">
                <div>
                  <label class="block text-xs font-mono text-slate-300 mb-1">Product Type</label>
                  <select id="calc-type" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500">
                    <option value="saas">SaaS Platform (Web)</option>
                    <option value="mobile">Mobile Application (iOS + Android)</option>
                    <option value="web-mobile">Web & Mobile App</option>
                    <option value="ai">AI Agent / Automation Engine</option>
                    <option value="ecommerce">Custom E-Commerce Platform</option>
                  </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-mono text-slate-300 mb-1">Design Fidelity</label>
                    <select id="calc-design" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500">
                      <option value="clean">Modern Clean UI</option>
                      <option value="custom">Custom Design System + 3D</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-mono text-slate-300 mb-1">AI Integration</label>
                    <select id="calc-ai" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500">
                      <option value="none">Standard Logic (No AI)</option>
                      <option value="rag">RAG / LLM Integration</option>
                      <option value="agent">Autonomous AI Agents</option>
                    </select>
                  </div>
                </div>

              <!-- Live Calculation Output -->
              <div class="mt-6 p-4 rounded-2xl bg-slate-950 border border-indigo-500/30">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-xs text-slate-400 font-mono">Estimated Development Range</p>
                    <p id="calc-price-output" class="text-2xl font-extrabold font-mono text-cyan-400 mt-1">$1,500 – $3,000 USD</p>
                  </div>
                  <div class="text-right">
                    <p class="text-xs text-slate-400 font-mono">Est. Delivery</p>
                    <p id="calc-time-output" class="text-lg font-bold font-mono text-indigo-300 mt-1">3 – 5 Weeks</p>
                  </div>
                </div>
                <div class="flex items-center justify-between border-t border-white/5 pt-3 mt-3">
                  <p class="text-xs text-slate-400 font-mono">Server & Hosting Cost</p>
                  <p class="text-xs font-bold font-mono text-emerald-400">FREE ($0/mo Free Tier Setup)</p>
                </div>
                <p class="text-[10px] text-slate-500 font-mono mt-3">
                  * Initial directional estimate. We design on high-allowance cloud free tiers so your server costs remain $0/month.
                </p>
              </div>
             </form>
            </div>

            <div class="mt-6">
              <a href="#contact" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl text-center flex items-center justify-center gap-1 transition-colors">
                Lock In This Estimate With Founder →
              </a>
            </div>
          </div>

          <!-- Tool 2: AI Automation Assessment -->
          <div class="p-8 rounded-3xl bg-slate-900/80 border border-white/10 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                  <span>⚡</span> AI Automation Readiness Quiz
                </h3>
                <span class="text-xs font-mono text-emerald-400 px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">Assessment</span>
              </div>

              <div class="space-y-4 text-sm">
                <div>
                  <label class="block text-xs font-mono text-slate-300 mb-1">Company Size / Industry</label>
                  <select id="quiz-industry" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    <option value="startup">Tech Startup (1–10 people)</option>
                    <option value="sme">Small/Medium Business (10–50 people)</option>
                    <option value="agency">Agency / Professional Services</option>
                    <option value="ecommerce">E-commerce / Retail</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-mono text-slate-300 mb-1">Biggest Operational Bottleneck</label>
                  <select id="quiz-bottleneck" class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    <option value="support">Manual customer support & queries</option>
                    <option value="data">Data entry between multiple tools</option>
                    <option value="docs">Document & invoice processing</option>
                    <option value="outreach">Sales lead research & personalization</option>
                  </select>
                </div>

                <div id="quiz-result-box" class="p-4 rounded-2xl bg-slate-950 border border-cyan-500/30 font-mono text-xs">
                  <p class="text-emerald-400 font-bold">Automation Potential: High (65% Hours Saved)</p>
                  <p id="quiz-recommendation" class="text-slate-300 mt-1">
                    Recommended: Implement an Autonomous Support Agent + Document Parsing webhook pipeline.
                  </p>
                </div>
              </div>
            </div>

            <div class="mt-6">
              <a href="#contact" class="w-full py-3 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold rounded-xl text-center flex items-center justify-center gap-1 transition-colors">
                Discuss Your Automation Opportunity →
              </a>
            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- ==================== 15. INSIGHTS & ARTICLES ==================== -->
    <section id="insights" class="py-24 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-mono mb-4">
            ENGINEERING & STRATEGY
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
            INSIGHTS FOR BUILDERS.
          </h2>
          <p class="mt-4 text-slate-400 text-lg">
            Practical guides on AI development, software architecture, and startup execution for founders.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          
          <article class="p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/40 transition-all flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between text-xs font-mono text-slate-400 mb-3">
                <span class="text-cyan-400">AI STRATEGY</span>
                <span>5 min read</span>
              </div>
              <h3 class="text-lg font-bold text-white mb-2 hover:text-cyan-300 cursor-pointer">
                How Small & Medium Businesses Can Practicalize AI in 2026
             </h3>
              <p class="text-slate-400 text-xs leading-relaxed mb-4">
                Moving past simple chatbots: how autonomous agents and document AI create measurable ROI for real-world operations.
              </p>
            </div>
            <a href="#contact" class="text-xs font-semibold text-indigo-400 hover:text-cyan-300">Read Insight →</a>
          </article>

          <article class="p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/40 transition-all flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between text-xs font-mono text-slate-400 mb-3">
                <span class="text-purple-400">STARTUP MVP</span>
                <span>7 min read</span>
              </div>
              <h3 class="text-lg font-bold text-white mb-2 hover:text-purple-300 cursor-pointer">
                How Much Should You Really Spend on Your First MVP?
              </h3>
              <p class="text-slate-400 text-xs leading-relaxed mb-4">
                A transparent breakdown of MVP scopes, architectural trade-offs, and how to avoid over-engineering before product-market fit.
              </p>
            </div>
            <a href="#contact" class="text-xs font-semibold text-indigo-400 hover:text-purple-300">Read Insight →</a>
          </article>

          <article class="p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/40 transition-all flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between text-xs font-mono text-slate-400 mb-3">
                <span class="text-emerald-400">GLOBAL MARKETS</span>
                <span>6 min read</span>
              </div>
              <h3 class="text-lg font-bold text-white mb-2 hover:text-emerald-300 cursor-pointer">
                Cross-Border Software Engineering: The Agile Remote Blueprint
              </h3>
              <p class="text-slate-400 text-xs leading-relaxed mb-4">
                How North American and Gulf businesses leverage high-velocity engineering studios with zero communication friction.
              </p>
            </div>
            <a href="#contact" class="text-xs font-semibold text-indigo-400 hover:text-emerald-300">Read Insight →</a>
          </article>

        </div>

      </div>
    </section>

    <!-- ==================== 16. FAQ SECTION ==================== -->
    <section class="py-24 bg-slate-950 border-t border-white/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-mono mb-4">
            COMMON QUESTIONS
          </div>
          <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight">
            Frequently Asked Questions.
          </h2>
          <p class="mt-4 text-slate-400">
            Clear, transparent answers about our engineering process, pricing, and communication.
          </p>
        </div>

      </div>
    </section>

    <!-- ==================== 17. CONTACT & PROJECT INQUIRY FORM ==================== -->
    <section id="contact" class="py-24 relative overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 border-t border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
          
          <!-- Left Pitch -->
          <div class="lg:col-span-5 space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-mono">
              DIRECT INQUIRY
            </div>
            <h2 class="text-3xl sm:text-5xl font-heading font-extrabold text-white tracking-tight leading-tight">
              TELL US WHAT YOU'RE BUILDING.
            </h2>
            <p class="text-slate-300 text-lg leading-relaxed">
              Whether you need an AI agent workflow, a high-converting MVP, or full-scale custom software, we are ready to discuss your architecture.
            </p>

            <div class="p-6 rounded-2xl bg-slate-950/60 border border-white/10 space-y-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-300">
                  ✉️
                </div>
                <div>
                  <p class="text-xs text-slate-400">Direct Email</p>
                  <p class="text-sm font-mono text-white font-semibold">rabsstechnologies@gmail.com</p>
                </div>
              </div>

              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-600/20 border border-cyan-500/30 flex items-center justify-center text-cyan-300">
                  📍
                </div>
                <div>
                  <p class="text-xs text-slate-400">Location & Markets</p>
                  <p class="text-sm font-mono text-white font-semibold">Kathmandu, Nepal • Serving USA, Canada, UAE, Qatar</p>
                </div>
              </div>
            </div>

            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-mono">
              ✓ Direct founder review by Subash Sitaula on every single submission.
            </div>
          </div>

          <!-- Right Form -->
          <div class="lg:col-span-7">
            <div class="p-8 sm:p-10 rounded-3xl bg-slate-950 border border-white/15 shadow-2xl relative">
              
              <form id="project-form" class="space-y-6">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-xs font-mono text-slate-300 mb-2">Your Name *</label>
                    <input type="text" name="name" required class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:border-indigo-500" placeholder="e.g. Alex Morgan">
                  </div>
                  <div>
                    <label class="block text-xs font-mono text-slate-300 mb-2">Work Email *</label>
                    <input type="email" name="email" required class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:border-indigo-500" placeholder="alex@company.com">
                  </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-xs font-mono text-slate-300 mb-2">Company / Project Name</label>
                    <input type="text" name="company" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:border-indigo-500" placeholder="Acme Labs">
                  </div>
                  <div>
                    <label class="block text-xs font-mono text-slate-300 mb-2">WhatsApp Number (with Country Code)</label>
                    <input type="text" name="whatsapp" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:border-indigo-500" placeholder="e.g. +1 555-0199">
                  </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-xs font-mono text-slate-300 mb-2">Your Country / Location *</label>
                    <select name="country" required class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:border-indigo-500">
                      <option value="USA">🇺🇸 United States</option>
                      <option value="Canada">🇨🇦 Canada</option>
                      <option value="UAE">🇦🇪 United Arab Emirates</option>
                      <option value="Qatar">🇶🇦 Qatar</option>
                      <option value="Other">🌍 International / Other</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-mono text-slate-300 mb-2">Target Budget Range</label>
                    <select name="budget" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:border-indigo-500">
                      <option value="1.5k-3k">$1,500 – $3,000 USD</option>
                      <option value="3k-5k">$3,000 – $5,000 USD</option>
                      <option value="5k-10k">$5,000 – $10,000 USD</option>
                      <option value="10k+">$10,000+ USD</option>
                      <option value="undecided">Flexible / Discuss with Founder</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-mono text-slate-300 mb-2">Project Category *</label>
                  <select name="project_type" required class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:border-indigo-500">
                    <option value="ai">AI Agent / LLM Automation</option>
                    <option value="mvp">MVP Fast-Track (New Idea)</option>
                    <option value="saas">SaaS Platform Development</option>
                    <option value="custom">Custom Business Software</option>
                    <option value="web">Web Development</option>
                    <option value="mobile">Mobile App</option>
                    <option value="web-mobile">Web & Mobile App</option>
                    <option value="ecommerce">Modern E-commerce Platform</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-mono text-slate-300 mb-2">Project Overview & Goals *</label>
                  <textarea name="description" rows="4" required class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:border-indigo-500 text-sm" placeholder="Tell us what you want to build, any key features, or problems you're trying to solve..."></textarea>
                </div>

                <button type="submit" id="form-submit-btn" class="w-full py-4 bg-gradient-to-r from-indigo-600 via-indigo-500 to-cyan-500 hover:from-indigo-500 hover:to-cyan-400 text-white font-bold rounded-xl shadow-xl shadow-indigo-600/30 transition-all duration-300 flex items-center justify-center gap-2">
                  Send Project Inquiry →
                </button>

              </form>

              <!-- Success State -->
              <div id="form-success-state" class="hidden text-center py-12 space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center mx-auto text-2xl">
                  ✓
                </div>
                <h3 class="text-2xl font-bold text-white">Inquiry Received!</h3>
                <p class="text-slate-300 text-sm max-w-md mx-auto">
                  Thank you — your project details have been received. Founder Subash Sitaula will personally review your specs and reach back with initial thoughts.
                </p>
              </div>

            </div>
          </div>

        </div>

      </div>
    </section>
  </main>

  <!-- ==================== 18. FOOTER ==================== -->
  <footer class="bg-slate-950 border-t border-white/10 pt-16 pb-12 text-slate-400 text-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="grid grid-cols-2 md:grid-cols-5 gap-8 pb-12 border-b border-white/10">
        
        <!-- Col 1: Brand -->
        <div class="col-span-2 space-y-4">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center text-slate-950 font-bold font-mono">
              R
            </div>
            <span class="font-heading font-bold text-white tracking-wider text-lg">RABSS TECHNOLOGIES</span>
          </div>
          <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
            A modern software and AI development studio helping startups and businesses turn ideas into scalable digital products.
          </p>
          <div class="text-xs font-mono text-slate-500">
            Founded by Subash Sitaula • Kathmandu, Nepal
          </div>
        </div>

        <!-- Col 2: Services -->
        <div class="space-y-3">
          <p class="text-xs font-mono text-white uppercase tracking-wider font-bold">Services</p>
          <ul class="space-y-2 text-xs">
            <li><a href="#services" class="hover:text-cyan-400 transition-colors">AI Development</a></li>
            <li><a href="#services" class="hover:text-cyan-400 transition-colors">Custom Software</a></li>
            <li><a href="#services" class="hover:text-cyan-400 transition-colors">SaaS Development</a></li>
            <li><a href="#services" class="hover:text-cyan-400 transition-colors">MVP Fast-Track</a></li>
            <li><a href="#services" class="hover:text-cyan-400 transition-colors">Web Applications</a></li>
            <li><a href="#services" class="hover:text-cyan-400 transition-colors">Mobile Apps</a></li>
          </ul>
        </div>

        <!-- Col 3: Markets -->
        <div class="space-y-3">
          <p class="text-xs font-mono text-white uppercase tracking-wider font-bold">Markets</p>
          <ul class="space-y-2 text-xs">
            <li><a href="#markets" class="hover:text-cyan-400 transition-colors">🇺🇸 United States</a></li>
            <li><a href="#markets" class="hover:text-cyan-400 transition-colors">🇨🇦 Canada</a></li>
            <li><a href="#markets" class="hover:text-cyan-400 transition-colors">🇦🇪 United Arab Emirates</a></li>
            <li><a href="#markets" class="hover:text-cyan-400 transition-colors">🇶🇦 Qatar</a></li>
          </ul>
        </div>

        <!-- Col 4: Resources -->
        <div class="space-y-3">
          <p class="text-xs font-mono text-white uppercase tracking-wider font-bold">Resources</p>
          <ul class="space-y-2 text-xs">
            <li><a href="#concepts" class="hover:text-cyan-400 transition-colors">What We Build</a></li>
            <li><a href="#tools" class="hover:text-cyan-400 transition-colors">MVP Cost Estimator</a></li>
            <li><a href="#tools" class="hover:text-cyan-400 transition-colors">AI Automation Quiz</a></li>
            <li><a href="#insights" class="hover:text-cyan-400 transition-colors">Insights & Articles</a></li>
            <li><a href="#about" class="hover:text-cyan-400 transition-colors">About & Leadership</a></li>
          </ul>
        </div>

      </div>

      <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
        <p>© 2026 RABSS Technologies. All Rights Reserved. • <a href="privacy.html" class="hover:text-cyan-400 transition-colors">Privacy Policy</a> • <a href="terms.html" class="hover:text-cyan-400 transition-colors">Terms & Conditions</a></p>
        <p class="font-mono">Engineered with Next-Gen Standards • Zero Fabricated Stats</p>
      </div>

    </div>
  </footer>

  <!-- Concept Project Modal -->
  <div id="concept-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-white/20 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl relative">
      <button id="close-modal-btn" class="absolute top-6 right-6 text-slate-400 hover:text-white text-xl">✕</button>
      <div id="modal-content">
        <!-- Injected via JavaScript -->
      </div>
    </div>
  </div>

  <!-- ==================== FLOATING FAQ CHAT WIDGET ==================== -->
  <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
    
    <!-- Floating Chat Panel -->
    <div id="faq-chat-panel" class="w-[90vw] sm:w-[400px] h-[580px] rounded-3xl bg-slate-900/95 border border-indigo-500/30 backdrop-blur-2xl flex flex-col justify-between shadow-2xl relative mb-4 transition-all duration-300 transform scale-100 opacity-100 origin-bottom-right">
      
      <!-- Header -->
      <div class="p-4 sm:p-5 border-b border-white/10 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="relative w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-xl">
            🤖
            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-400 border-2 border-slate-900 rounded-full animate-pulse"></span>
          </div>
          <div>
            <h3 class="text-sm font-bold text-white font-mono uppercase tracking-wider">RABSS FAQ BOT</h3>
            <p class="text-[10px] text-slate-400 font-mono">Active & Streaming responses</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-2 py-0.5 rounded bg-cyan-500/10 border border-cyan-500/25 text-[10px] font-mono text-cyan-300">v1.3</span>
          <button id="faq-chat-close" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-white/5 transition-colors focus:outline-none" aria-label="Close Chat">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      </div>

      <!-- Message Window -->
      <div id="faq-chat-messages" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 scrollbar-thin text-xs leading-relaxed">
        <!-- Welcome Message -->
        <div class="flex gap-2">
          <div class="w-6 h-6 rounded bg-indigo-600/20 flex items-center justify-center shrink-0">🤖</div>
          <div class="p-3 rounded-2xl bg-indigo-950/40 border border-indigo-500/20 text-slate-200">
            Hello! I'm the RABSS FAQ Assistant. Ask me anything about our services, pricing, founder, tech stack, timezone overlap, or hosting costs!
          </div>
        </div>
      </div>

      <!-- Quick Prompts / Tags -->
      <div class="px-4 sm:px-5 py-2 bg-slate-950/40 border-t border-white/5">
        <p class="text-[9px] text-slate-500 font-mono mb-1.5 uppercase">Quick suggestions:</p>
        <div class="flex flex-wrap gap-1.5 max-h-[85px] overflow-y-auto pr-1">
          <button class="faq-chat-suggest px-2.5 py-1 rounded bg-slate-950 hover:bg-indigo-600/20 hover:text-white border border-white/5 text-[10px] font-mono text-slate-400 transition-colors" data-question="Who is the founder?">Founder?</button>
          <button class="faq-chat-suggest px-2.5 py-1 rounded bg-slate-950 hover:bg-indigo-600/20 hover:text-white border border-white/5 text-[10px] font-mono text-slate-400 transition-colors" data-question="How much does a project cost?">Average Cost?</button>
          <button class="faq-chat-suggest px-2.5 py-1 rounded bg-slate-950 hover:bg-indigo-600/20 hover:text-white border border-white/5 text-[10px] font-mono text-slate-400 transition-colors" data-question="What is your tech stack?">Tech Stack?</button>
          <button class="faq-chat-suggest px-2.5 py-1 rounded bg-slate-950 hover:bg-indigo-600/20 hover:text-white border border-white/5 text-[10px] font-mono text-slate-400 transition-colors" data-question="Can I get $0/mo server hosting?">Free Hosting?</button>
          <button class="faq-chat-suggest px-2.5 py-1 rounded bg-slate-950 hover:bg-indigo-600/20 hover:text-white border border-white/5 text-[10px] font-mono text-slate-400 transition-colors" data-question="How do you handle global time zones?">Timezones?</button>
          <button class="faq-chat-suggest px-2.5 py-1 rounded bg-slate-950 hover:bg-indigo-600/20 hover:text-white border border-white/5 text-[10px] font-mono text-slate-400 transition-colors" data-question="Who owns the source code?">IP Ownership?</button>
          <button class="faq-chat-suggest px-2.5 py-1 rounded bg-slate-950 hover:bg-indigo-600/20 hover:text-white border border-white/5 text-[10px] font-mono text-slate-400 transition-colors" data-question="Can you integrate AI agents?">Integrate AI?</button>
          <button class="faq-chat-suggest px-2.5 py-1 rounded bg-slate-950 hover:bg-indigo-600/20 hover:text-white border border-white/5 text-[10px] font-mono text-slate-400 transition-colors" data-question="How do we start a project?">How to start?</button>
        </div>
      </div>

      <!-- Input Bar -->
      <form id="faq-chat-form" class="p-4 sm:p-5 border-t border-white/10 flex gap-2 bg-slate-950/60 rounded-b-3xl">
        <input type="text" id="faq-chat-input" required class="flex-1 bg-slate-950 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500" placeholder="Ask a question...">
        <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold font-mono flex items-center justify-center shrink-0 transition-colors">
          SEND
        </button>
      </form>
    </div>

    <!-- Chat Launcher Trigger Button -->
    <button id="faq-chat-launcher" class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-500 text-white flex items-center justify-center text-2xl shadow-xl shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:scale-105 active:scale-95 transition-all duration-300 relative focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-950" aria-label="Toggle FAQ Chatbot">
      <!-- Pulse Active Notification Badge -->
      <span class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-400 rounded-full border-2 border-slate-950 flex items-center justify-center text-[9px] font-bold text-slate-950 font-mono animate-bounce">1</span>
      <!-- Robot Icon SVG -->
      <svg class="chat-icon-robot w-7 h-7 transition-all duration-300 absolute hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2M12 4a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" fill="currentColor"/>
        <rect x="4" y="6" width="16" height="12" rx="3" stroke-width="2"/>
        <circle cx="9" cy="11" r="1.5" fill="currentColor"/>
        <circle cx="15" cy="11" r="1.5" fill="currentColor"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15h6"/>
        <path d="M2 10h2M20 10h2" stroke-linecap="round"/>
      </svg>
      <!-- Close Cross Icon SVG -->
      <svg class="chat-icon-close w-6 h-6 transition-all duration-300 absolute block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>

  </div>

  <!-- JavaScript -->
  <script src="app.js"></script>
</body>
</html>
