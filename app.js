/**
 * RABSS Technologies - Client Script & Interactive Logic
 * Founder & CEO: Subash Sitaula
 * Contact: rabsstechnologies@gmail.com
 */

document.addEventListener('DOMContentLoaded', () => {
  const initializers = [
    { name: 'Navbar Scroll', fn: initNavbarScroll },
    { name: 'Mobile Menu', fn: initMobileMenu },
    { name: 'Hero Tabs', fn: initHeroTabs },
    { name: 'AI Pipeline', fn: initAiPipeline },
    { name: 'Market Clocks', fn: initMarketClocks },
    { name: 'MVP Calculator', fn: initMvpCalculator },
    { name: 'AI Quiz', fn: initAiQuiz },
    { name: 'FAQ Accordion', fn: initFaqAccordion },
    { name: 'Concept Modals', fn: initConceptModals },
    { name: 'Contact Form', fn: initContactForm },
    { name: 'FAQ Chat', fn: initFaqChat }
  ];

  initializers.forEach(item => {
    try {
      item.fn();
    } catch (err) {
      console.error(`[Error] Failed to initialize ${item.name}:`, err);
    }
  });
});

/* 1. Navbar Scroll Blur */
function initNavbarScroll() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;
  window.addEventListener('scroll', () => {
    if (window.scrollY > 30) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
}

/* 11. FAQ AI Chatbot */
function initFaqChat() {
  const form = document.getElementById('faq-chat-form');
  const input = document.getElementById('faq-chat-input');
  const messagesContainer = document.getElementById('faq-chat-messages');
  const suggestions = document.querySelectorAll('.faq-chat-suggest');
  const launcher = document.getElementById('faq-chat-launcher');
  const panel = document.getElementById('faq-chat-panel');
  const closeBtn = document.getElementById('faq-chat-close');
  const openFromFaqBtn = document.getElementById('faq-open-chat-btn');

  if (!form || !input || !messagesContainer || !launcher || !panel) return;

  const responses = [
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
      reply: 'Starting is easy! Simply fill out the project inquiry form below or email us directly at <a href="mailto:rabsstechnologies@gmail.com" class="text-cyan-400 underline">rabsstechnologies@gmail.com</a>. Founder Subash Sitaula will respond within 12-24 hours with an actionable roadmap!'
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
    },
    {
      keywords: ['maintenance', 'support', 'retainer', 'monthly', 'bug', 'fix', 'after', 'post-launch'],
      reply: 'We offer flexible post-launch support and monthly retainer plans. Whether you need ongoing feature additions, server monitoring, or bug fixes, we ensure your product remains healthy and up-to-date.'
    },
    {
      keywords: ['nosql', 'mongodb', 'relational', 'why pgvector', 'vector search'],
      reply: 'While we support NoSQL, we recommend PostgreSQL for most builds. By utilizing extensions like pgVector, we get the best of both worlds: highly relational structured tables alongside high-performance AI vector similarity search.'
    },
    {
      keywords: ['api', 'stripe', 'plaid', 'integration', 'third-party', 'webhook'],
      reply: 'We are integration experts! We regularly wire up Stripe for global payments, Plaid for banking sync, Salesforce/HubSpot CRMs, Twilio for SMS/voice, and custom webhooks for real-time notification architectures.'
    },
    {
      keywords: ['figma', 'ui', 'ux', 'design', 'wireframe', 'mockup'],
      reply: 'Before writing any code, we craft interactive high-fidelity Figma mockups. This allows you to visualize, iterate, and approve the complete look, feel, and user experience of your application risk-free.'
    },
    {
      keywords: ['no-code', 'low-code', 'bubble', 'wordpress', 'custom code', 'scratch'],
      reply: 'We build custom, handwritten, high-performance code because no-code templates often hit scalability walls and incur high platform fees. Custom code guarantees full IP ownership and maximum performance.'
    },
    {
      keywords: ['refactor', 'migration', 'migrate', 'upgrade', 'legacy'],
      reply: 'Yes! We help transition slow or legacy applications to modern stacks (such as Next.js and FastAPI). We clean up database schemas, optimize API structures, and boost overall system performance.'
    },
    {
      keywords: ['scale', 'traffic', 'load', 'millions', 'spike', 'serverless'],
      reply: 'We build serverless and containerized systems on top of AWS ECS/Lambda and Vercel Edge. This architecture scales automatically from 10 users to millions of concurrent requests, without over-provisioning servers.'
    },
    {
      keywords: ['fine-tune', 'train', 'embeddings'],
      reply: 'For 95% of business use-cases, we recommend Retrieval-Augmented Generation (RAG) over costly model fine-tuning. RAG allows us to connect standard LLMs to your dynamic databases, keeping data secure and context fresh!'
    },
    {
      keywords: ['hello', 'hi', 'hey', 'greetings', 'good morning', 'good afternoon', 'good evening'],
      reply: 'Hello! I am your interactive FAQ Assistant. Ask me anything about our services, pricing, founder, tech stack, timezone overlap, or hosting costs!'
    },
    {
      keywords: ['help', 'options', 'menu', 'what can you do', 'questions'],
      reply: 'I can answer a wide range of questions! Try asking about: 1. Founder, 2. Tech Stack, 3. Average Costs, 4. Free Server Hosting, 5. Timezone Support, 6. Intellectual Property, 7. Security, or 8. Post-launch support.'
    }
  ];

  // Toggle Chat Panel visibility with smooth spring animations
  function togglePanel() {
    const isHidden = panel.classList.contains('hidden');
    const robotIcon = launcher.querySelector('.chat-icon-robot');
    const closeIcon = launcher.querySelector('.chat-icon-close');

    if (isHidden) {
      panel.classList.remove('hidden');
      // Trigger browser reflow for transitions
      void panel.offsetWidth;
      panel.classList.remove('scale-95', 'opacity-0');
      panel.classList.add('scale-100', 'opacity-100');
      
      if (robotIcon) {
        robotIcon.classList.remove('block');
        robotIcon.classList.add('hidden');
      }
      if (closeIcon) {
        closeIcon.classList.remove('hidden');
        closeIcon.classList.add('block');
      }

      messagesContainer.scrollTop = messagesContainer.scrollHeight;
      if (window.innerWidth > 640) {
        input.focus();
      }
    } else {
      panel.classList.remove('scale-100', 'opacity-100');
      panel.classList.add('scale-95', 'opacity-0');
      
      if (robotIcon) {
        robotIcon.classList.remove('hidden');
        robotIcon.classList.add('block');
      }
      if (closeIcon) {
        closeIcon.classList.remove('block');
        closeIcon.classList.add('hidden');
      }

      setTimeout(() => {
        panel.classList.add('hidden');
      }, 300);
    }
  }

  launcher.addEventListener('click', togglePanel);
  if (closeBtn) closeBtn.addEventListener('click', togglePanel);
  if (openFromFaqBtn) {
    openFromFaqBtn.addEventListener('click', () => {
      if (panel.classList.contains('hidden')) {
        togglePanel();
      }
    });
  }

  function addMessage(sender, text) {
    const isBot = sender === 'bot';
    const messageEl = document.createElement('div');
    messageEl.className = 'flex gap-2 ' + (isBot ? '' : 'justify-end');
    messageEl.innerHTML = `
      ${isBot ? '<div class="w-6 h-6 rounded bg-indigo-600/20 flex items-center justify-center shrink-0">🤖</div>' : ''}
      <div class="p-3 rounded-2xl max-w-[85%] ${isBot ? 'bg-indigo-950/40 border border-indigo-500/20 text-slate-200' : 'bg-indigo-600 text-white'}">
        ${text}
      </div>
      ${!isBot ? '<div class="w-6 h-6 rounded bg-indigo-500 flex items-center justify-center text-white shrink-0 font-bold">U</div>' : ''}
    `;
    messagesContainer.appendChild(messageEl);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  function streamBotResponse(text) {
    const messageEl = document.createElement('div');
    messageEl.className = 'flex gap-2';
    messageEl.innerHTML = `
      <div class="w-6 h-6 rounded bg-indigo-600/20 flex items-center justify-center shrink-0">🤖</div>
      <div class="p-3 rounded-2xl max-w-[85%] bg-indigo-950/40 border border-indigo-500/20 text-slate-200 message-content"></div>
    `;
    messagesContainer.appendChild(messageEl);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    const contentEl = messageEl.querySelector('.message-content');
    let index = 0;

    const sendBtn = form.querySelector('button[type="submit"]');
    if (sendBtn) sendBtn.disabled = true;
    input.disabled = true;
    suggestions.forEach(btn => btn.setAttribute('disabled', 'true'));

    const interval = setInterval(() => {
      if (index < text.length) {
        if (text[index] === '<') {
          const closingIndex = text.indexOf('>', index);
          if (closingIndex !== -1) {
            contentEl.innerHTML += text.slice(index, closingIndex + 1);
            index = closingIndex + 1;
          } else {
            contentEl.innerHTML += text[index];
            index++;
          }
        } else {
          contentEl.innerHTML += text[index];
          index++;
        }
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      } else {
        clearInterval(interval);
        if (sendBtn) sendBtn.disabled = false;
        input.disabled = false;
        suggestions.forEach(btn => btn.removeAttribute('disabled'));
      }
    }, 12);
  }

  function handleQuery(query) {
    if (!query.trim()) return;

    // Add user message
    addMessage('user', query);
    input.value = '';

    // Show typing indicator
    const typingIndicator = document.createElement('div');
    typingIndicator.className = 'flex gap-2';
    typingIndicator.id = 'faq-typing-indicator';
    typingIndicator.innerHTML = `
      <div class="w-6 h-6 rounded bg-indigo-600/20 flex items-center justify-center shrink-0">🤖</div>
      <div class="p-3 rounded-2xl bg-indigo-950/40 border border-indigo-500/20 text-slate-400 font-mono text-[10px] animate-pulse">
        🤖 FAQ_AGENT is analyzing query...
      </div>
    `;
    messagesContainer.appendChild(typingIndicator);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    setTimeout(() => {
      // Remove typing indicator
      const indicator = document.getElementById('faq-typing-indicator');
      if (indicator) indicator.remove();

      const normalizedQuery = query.toLowerCase();
      let matchedReply = null;

      for (const res of responses) {
        const found = res.keywords.some(keyword => normalizedQuery.includes(keyword));
        if (found) {
          matchedReply = res.reply;
          break;
        }
      }

      if (!matchedReply) {
        matchedReply = `That's an interesting question! While my direct database doesn't have an exact pre-trained reply for "${query}", our Founder & CEO, Subash Sitaula, can review it immediately! Drop your specs in the contact form below or email us directly at <a href="mailto:rabsstechnologies@gmail.com" class="text-cyan-400 underline">rabsstechnologies@gmail.com</a>. In the meantime, try asking me about our pricing, tech stack, free cloud hosting, or timezone support!`;
      }

      streamBotResponse(matchedReply);
    }, 850);
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    handleQuery(input.value);
  });

  suggestions.forEach(btn => {
    btn.addEventListener('click', () => {
      const q = btn.getAttribute('data-question');
      if (q) handleQuery(q);
    });
  });
}

/* 2. Mobile Menu Drawer */
function initMobileMenu() {
  const btn = document.getElementById('mobile-menu-btn');
  const menu = document.getElementById('mobile-menu');
  const links = document.querySelectorAll('.mobile-nav-link');

  if (!btn || !menu) return;

  btn.addEventListener('click', () => {
    menu.classList.toggle('hidden');
    menu.classList.toggle('flex');
  });

  links.forEach(link => {
    link.addEventListener('click', () => {
      menu.classList.add('hidden');
      menu.classList.remove('flex');
    });
  });
}

/* 3. Hero Visualizer Tabs */
function initHeroTabs() {
  const buttons = document.querySelectorAll('.hero-tab-btn');
  const panels = document.querySelectorAll('.hero-view-panel');

  if (buttons.length === 0 || panels.length === 0) return;

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      buttons.forEach(b => {
        b.classList.remove('active', 'bg-indigo-600/30', 'text-indigo-300', 'border-indigo-500/40');
        b.classList.add('bg-slate-900', 'text-slate-400');
      });

      btn.classList.add('active', 'bg-indigo-600/30', 'text-indigo-300', 'border-indigo-500/40');
      btn.classList.remove('bg-slate-900', 'text-slate-400');

      const target = btn.getAttribute('data-view');
      panels.forEach(panel => {
        if (panel.id === `view-${target}`) {
          panel.classList.remove('hidden');
        } else {
          panel.classList.add('hidden');
        }
      });
    });
  });
}

/* 4. Interactive AI Pipeline Simulation */
function initAiPipeline() {
  const runBtn = document.getElementById('run-ai-pipeline');
  const consoleOutput = document.getElementById('pipeline-output');
  if (!runBtn || !consoleOutput) return;

  const steps = [
    { el: document.getElementById('step-1'), log: '⚡ Event Triggered: Ingested enterprise lead from USA partner.' },
    { el: document.getElementById('step-2'), log: '🤖 Vector Embedding & LLM Analysis: Verified contract requirements.' },
    { el: document.getElementById('step-3'), log: '🧠 Autonomous Engine: Evaluated sprint feasibility (Confidence 99.4%).' },
    { el: document.getElementById('step-4'), log: '✓ Dispatched: Synced to RABSS Technologies PostgreSQL database & notified Subash Sitaula (rabsstechnologies@gmail.com).' }
  ];

  if (steps.some(s => !s.el)) return;

  let isRunning = false;

  runBtn.addEventListener('click', () => {
    if (isRunning) return;
    isRunning = true;
    runBtn.textContent = '⏳ Processing...';

    // Reset steps
    steps.forEach(s => {
      s.el.classList.remove('active');
      const badge = s.el.querySelector('.status-badge');
      if (badge) badge.textContent = 'Idle';
    });

    let currentStep = 0;
    const interval = setInterval(() => {
      if (currentStep > 0) {
        steps[currentStep - 1].el.classList.remove('active');
        const badge = steps[currentStep - 1].el.querySelector('.status-badge');
        if (badge) badge.textContent = 'Completed ✓';
      }

      if (currentStep < steps.length) {
        steps[currentStep].el.classList.add('active');
        const badge = steps[currentStep].el.querySelector('.status-badge');
        if (badge) badge.textContent = 'Processing...';
        consoleOutput.innerHTML = `<span class="text-cyan-400 font-bold">// Output:</span> ${steps[currentStep].log}`;
        currentStep++;
      } else {
        clearInterval(interval);
        runBtn.textContent = '▶ Run Again';
        isRunning = false;
      }
    }, 900);
  });
}

/* 5. Live Market Timezone Clocks */
function initMarketClocks() {
  const clocks = document.querySelectorAll('.market-clock');
  if (clocks.length === 0) return;
  function updateTime() {
    clocks.forEach(clock => {
      const tz = clock.getAttribute('data-tz');
      try {
        const timeStr = new Intl.DateTimeFormat('en-US', {
          timeZone: tz,
          hour: '2-digit',
          minute: '2-digit',
          hour12: true
        }).format(new Date());
        
        let label = 'TIME';
        if (tz.includes('New_York')) label = 'EST';
        if (tz.includes('Toronto')) label = 'EST';
        if (tz.includes('Dubai')) label = 'GST';
        if (tz.includes('Qatar')) label = 'AST';

        clock.textContent = `${label}: ${timeStr}`;
      } catch (e) {
        clock.textContent = '--:--';
      }
    });
  }
  updateTime();
  setInterval(updateTime, 10000);
}

/* 6. Interactive MVP Cost Calculator */
function initMvpCalculator() {
  const type = document.getElementById('calc-type');
  const design = document.getElementById('calc-design');
  const ai = document.getElementById('calc-ai');
  const priceOutput = document.getElementById('calc-price-output');
  const timeOutput = document.getElementById('calc-time-output');

  if (!type || !design || !ai || !priceOutput || !timeOutput) return;

  function calculate() {
    let baseMin = 1500;
    let baseMax = 3000;
    let weeksMin = 3;
    let weeksMax = 5;

    if (type.value === 'mobile') { baseMin += 800; baseMax += 1500; weeksMin += 1; weeksMax += 2; }
    if (type.value === 'web-mobile') { baseMin += 1500; baseMax += 3000; weeksMin += 2; weeksMax += 4; }
    if (type.value === 'ai') { baseMin += 1000; baseMax += 2000; weeksMin += 1; weeksMax += 2; }
    if (type.value === 'ecommerce') { baseMin += 500; baseMax += 1200; }

    if (design.value === 'custom') { baseMin += 500; baseMax += 1000; weeksMax += 1; }

    if (ai.value === 'rag') { baseMin += 600; baseMax += 1200; }
    if (ai.value === 'agent') { baseMin += 1200; baseMax += 2200; weeksMin += 1; weeksMax += 2; }

    priceOutput.textContent = `$${baseMin.toLocaleString()} – $${baseMax.toLocaleString()} USD`;
    timeOutput.textContent = `${weeksMin} – ${weeksMax} Weeks`;
  }

  [type, design, ai].forEach(input => input.addEventListener('change', calculate));
  calculate();
}

/* 7. AI Automation Quiz */
function initAiQuiz() {
  const bottleneck = document.getElementById('quiz-bottleneck');
  const recommendation = document.getElementById('quiz-recommendation');

  if (!bottleneck || !recommendation) return;

  bottleneck.addEventListener('change', () => {
    const map = {
      support: 'Recommended: Implement an Autonomous Support Agent + Vector Knowledge Base to resolve 70%+ queries without manual intervention.',
      data: 'Recommended: Build a Multi-Webhook Automation Pipeline syncing CRM, invoices, and databases automatically.',
      docs: 'Recommended: Deploy Document AI with OCR to parse PDFs, bills, and contracts into structured JSON payloads.',
      outreach: 'Recommended: Build an automated AI Research Agent that gathers prospective intelligence and crafts contextual proposals.'
    };
    recommendation.textContent = map[bottleneck.value] || 'Recommended: Custom Workflow Automation Engine.';
  });
}

/* 8. FAQ Accordion */
function initFaqAccordion() {
  const items = document.querySelectorAll('.faq-item');
  items.forEach(item => {
    const btn = item.querySelector('.faq-btn');
    const content = item.querySelector('.faq-content');
    const icon = item.querySelector('.faq-icon');

    if (!btn || !content) return;

    btn.addEventListener('click', () => {
      const isHidden = content.classList.contains('hidden');
      document.querySelectorAll('.faq-content').forEach(c => c.classList.add('hidden'));
      document.querySelectorAll('.faq-icon').forEach(i => {
        if (i) i.textContent = '+';
      });

      if (isHidden) {
        content.classList.remove('hidden');
        if (icon) icon.textContent = '−';
      }
    });
  });
}

/* 9. Concept Project Specs Modal */
function initConceptModals() {
  const modal = document.getElementById('concept-modal');
  const modalContent = document.getElementById('modal-content');
  const closeBtn = document.getElementById('close-modal-btn');
  const buttons = document.querySelectorAll('.view-concept-btn');

  if (!modal || !modalContent || !closeBtn) return;

  const concepts = {
    '01': {
      title: 'Concept 01 — AI Business OS',
      subtitle: 'Autonomous Enterprise Operations Platform',
      tech: 'FastAPI, LangChain, pgVector, React 19, Docker',
      desc: 'An internal blueprint demonstrating how enterprise businesses can combine multi-agent task distribution with live RAG memory to eliminate manual operational handoffs.'
    },
    '02': {
      title: 'Concept 02 — CommerceFlow',
      subtitle: 'Modern Headless Multi-Storefront Engine',
      tech: 'Next.js 15, Stripe Billing, GraphQL, Redis, Tailwind',
      desc: 'High-speed headless commerce engine architected for sub-millisecond checkout experiences and multi-currency international sales across USA, Canada, UAE, and Qatar.'
    },
    '03': {
      title: 'Concept 03 — LifeOS',
      subtitle: 'Personal AI Productivity Hub',
      tech: 'React Native, OpenAI Whisper, SQLite, Tailwind',
      desc: 'Mobile-first prototype illustrating on-device voice memo ingestion, automated agenda decomposition, and semantic personal knowledge search.'
    },
    '04': {
      title: 'Concept 04 — GlobalDesk',
      subtitle: 'Distributed Operations & Handoff Suite',
      tech: 'Node.js, TypeScript, WebSockets, Redis, PostgreSQL',
      desc: 'Demonstration of seamless cross-timezone handoff protocols, real-time SLA tracking, and audit-ready team coordination.'
    },
    '05': {
      title: 'Concept 05 — SmartCRM',
      subtitle: 'AI-Assisted Customer Relationship Engine',
      tech: 'Python, FastAPI, OpenAI API, PostgreSQL',
      desc: 'Demonstration CRM that transcribes sales calls, calculates predictive close probabilities, and automatically generates custom founder proposals.'
    },
    '06': {
      title: 'Concept 06 — FinancePilot',
      subtitle: 'Real-Time Financial Intelligence Dashboard',
      tech: 'React, Plaid API, ClickHouse, Tailwind CSS',
      desc: 'Financial forecasting dashboard prototype computing live runway burn rates, multi-currency conversions, and tax modeling simulations.'
    }
  };

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-concept');
      const data = concepts[id];
      if (!data) return;

      modalContent.innerHTML = `
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-indigo-500/20 text-indigo-300 font-mono text-xs mb-4">
          CONCEPT ARCHITECTURE SPECIFICATION
        </div>
        <h3 class="text-2xl font-bold text-white mb-1">${data.title}</h3>
        <p class="text-sm text-cyan-400 font-mono mb-4">${data.subtitle}</p>
        <p class="text-slate-300 text-sm leading-relaxed mb-6">${data.desc}</p>
        <div class="p-4 rounded-xl bg-slate-950 border border-white/10 font-mono text-xs text-slate-300 mb-6">
          <p class="text-slate-400 text-[11px] mb-1">PROTOTYPE TECH STACK:</p>
          <p class="text-emerald-400">${data.tech}</p>
        </div>
        <a href="#contact" onclick="document.getElementById('concept-modal').classList.add('hidden')" class="inline-flex items-center justify-center w-full py-3.5 bg-gradient-to-r from-indigo-600 to-cyan-500 text-white font-bold rounded-xl text-sm">
          Discuss Building a Similar Product →
        </a>
      `;
      modal.classList.remove('hidden');
    });
  });

  closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.add('hidden');
  });
}

/* 10. Contact Form Submission */
function initContactForm() {
  const form = document.getElementById('project-form');
  const success = document.getElementById('form-success-state');
  const btn = document.getElementById('form-submit-btn');

  if (!form || !success || !btn) return;

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    btn.textContent = 'Sending Details...';
    btn.disabled = true;

    const formData = new FormData(form);
    
    // Robust helper to extract values from inputs with various IDs and names
    const getVal = (fieldName, fallbackIds = []) => {
      let val = formData.get(fieldName);
      if (val && val.trim()) return val.trim();
      for (const id of fallbackIds) {
        const el = document.getElementById(id) || form.querySelector(`[name="${id}"]`) || form.querySelector(`#${id}`);
        if (el && el.value && el.value.trim()) {
          return el.value.trim();
        }
      }
      return '';
    };

    const name = getVal('name', ['fullname', 'full-name', 'name-input', 'inquirer-name']);
    const email = getVal('email', ['email_address', 'email-address', 'email-input']);
    const company = getVal('company', ['company_name', 'company-name', 'company-input']);
    const whatsapp = getVal('whatsapp', ['phone', 'whats-app', 'phone-input', 'whatsapp-input']);
    const country = getVal('country', ['country-input', 'location']);
    const description = getVal('description', ['message', 'msg', 'goals', 'desc', 'description-input']);
    const projectTypeEl = form.querySelector('[name="project_type"]') || form.querySelector('#project_type');
    const project_type = projectTypeEl && projectTypeEl.selectedIndex >= 0 ? projectTypeEl.options[projectTypeEl.selectedIndex].text : 'AI / SaaS MVP';
    const budgetEl = form.querySelector('[name="budget"]') || form.querySelector('#budget');
    const budget = budgetEl && budgetEl.selectedIndex >= 0 ? budgetEl.options[budgetEl.selectedIndex].text : 'Flexible';

    // Dynamically calculate the correct API URL regardless of subfolders or trailing slashes
    const getApiPath = (action) => {
      const loc = window.location;
      let basePath = loc.pathname;
      if (basePath.endsWith('.html') || basePath.endsWith('.php')) {
        basePath = basePath.substring(0, basePath.lastIndexOf('/'));
      }
      if (!basePath.endsWith('/')) {
        basePath += '/';
      }
      return `${loc.protocol}//${loc.host}${basePath}admin/api/index.php?action=${action}`;
    };

    const apiUrl = getApiPath('submit_inquiry');

    // Connects public website inquiry form directly to Super Admin OS
    fetch(apiUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        name,
        email,
        company,
        whatsapp,
        country,
        project_type,
        budget,
        description
      })
    })
    .then(response => {
      return response.text().then(text => {
        try {
          const parsed = JSON.parse(text);
          return { data: parsed, ok: response.ok, status: response.status };
        } catch (e) {
          throw new Error("Server returned an invalid JSON payload.");
        }
      });
    })
    .then(({ data, ok }) => {
      if (ok && data && data.status === 'success') {
        form.classList.add('hidden');
        const titleEl = success.querySelector('h3, .title');
        const descEl = success.querySelector('p, .description');
        if (titleEl) {
          titleEl.textContent = 'Sent to RABSS Technologies!';
        }
        if (descEl) {
          descEl.innerHTML = 'Thank you! Your details have been received. We will get in touch soon. For urgent inquiries, email us at <a href="mailto:rabsstechnologies@gmail.com" class="text-cyan-400 underline">rabsstechnologies@gmail.com</a>.';
        }
        success.classList.remove('hidden');
        const existingErr = form.querySelector('.form-debug-error');
        if (existingErr) existingErr.remove();
      } else {
        throw new Error(data && data.message ? data.message : 'Unknown server response state.');
      }
    })
    .catch(err => {
      console.error('Inquiry sync failed:', err);
      
      const existingErr = form.querySelector('.form-debug-error');
      if (existingErr) existingErr.remove();
      
      const errDiv = document.createElement('div');
      errDiv.className = 'form-debug-error mt-6 p-5 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-xs font-mono space-y-2 text-rose-300 text-left';
      errDiv.innerHTML = `
        <p class="font-bold flex items-center gap-2 text-rose-400">
          <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span> 🚨 INQUIRY SUBMISSION DIAGNOSTICS ACTIVE
        </p>
        <p><strong>Captured Error:</strong> ${err.message}</p>
        <p class="text-slate-400 font-bold mt-2">Troubleshooting Checklist:</p>
        <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-300 pl-1">
          <li>Ensure your local server is active by running <code class="bg-black/40 px-1 py-0.5 rounded text-cyan-300">php -S localhost:8000</code> inside the project root folder.</li>
          <li>Confirm you are accessing the page via <code class="bg-black/40 px-1 py-0.5 rounded text-cyan-300">http://localhost:8000</code> rather than launching the static HTML file directly.</li>
          <li>Check directory permissions for the <code class="bg-black/40 px-1 py-0.5 rounded text-cyan-300">admin/api/</code> folder and verify that <code class="bg-black/40 px-1 py-0.5 rounded text-cyan-300">rabss_os.sqlite</code> is writable.</li>
          <li>Examine <code class="bg-black/40 px-1 py-0.5 rounded text-cyan-300">admin/api/debug_log.txt</code> and <code class="bg-black/40 px-1 py-0.5 rounded text-cyan-300">php_error_log.txt</code> for dynamic logs.</li>
        </ul>
      `;
      form.appendChild(errDiv);

      btn.textContent = 'Send Project Inquiry →';
      btn.disabled = false;
    });
  });
}