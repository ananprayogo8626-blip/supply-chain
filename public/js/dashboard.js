// public/js/dashboard.js
/* Dashboard UI Helpers - Dark Enterprise Theme */

// Animated counter
function animateNumber(el, target) {
  const duration = 1200;
  const start = 0;
  const startTime = performance.now();
  const step = (now) => {
    const elapsed = now - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const value = Math.floor(start + (target - start) * progress);
    el.textContent = value.toLocaleString();
    if (progress < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}

// Sparkline (simple bar)
function drawSparkline(canvasId, data) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const w = canvas.width = 60;
  const h = canvas.height = 12;
  const max = Math.max(...data);
  ctx.clearRect(0, 0, w, h);
  data.forEach((v, i) => {
    const barHeight = (v / max) * h;
    ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--accent-cyan');
    ctx.fillRect(i * 4, h - barHeight, 2, barHeight);
  });
}

// Leaflet map initialization with dark tiles and colored markers
function initLeafletMap(riskData = [], portData = []) {
  const map = L.map('map-dashboard', {
    zoomControl: true,
    fullscreenControl: true,
    scrollWheelZoom: true
  }).setView([20, 0], 2);

  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/" target="_blank">CARTO</a>',
    maxZoom: 18,
  }).addTo(map);

  const riskColors = {
    Critical: '#EF4444',
    High: '#F97316',
    Medium: '#EAB308',
    Low: '#10B981'
  };

  riskData.forEach(r => {
    if (!r.lat || !r.lng) return;
    const icon = L.divIcon({
      className: 'risk-marker',
      html: `<div style="background:${riskColors[r.riskLevel] || '#666'};width:16px;height:16px;border-radius:50%;border:2px solid #fff;"></div>`,
      iconSize: [20, 20]
    });
    L.marker([r.lat, r.lng], { icon }).addTo(map).bindPopup(`
      <div style="font-family: Inter, sans-serif; color:#f8fafc;">
        <div style="font-size:13px;font-weight:bold;color:${riskColors[r.riskLevel]};margin-bottom:5px;">${r.country_name}</div>
        <div style="font-size:10px;color:#94a3b8;">Risk Score: <strong>${r.total_score}</strong></div>
        <div style="font-size:10px;color:#94a3b8;">Weather: <strong>${r.weather || 'N/A'}</strong></div>
        <div style="font-size:10px;color:#94a3b8;">GDP: <strong>${r.gdp || 'N/A'}</strong></div>
        <div style="font-size:10px;color:#94a3b8;">Currency: <strong>${r.currency || 'N/A'}</strong></div>
        <div style="font-size:10px;color:#94a3b8;">Main Port: <strong>${r.main_port || 'N/A'}</strong></div>
      </div>`);
  });

  const portIcon = L.divIcon({
    className: '',
    html: '<div style="font-size:16px; text-shadow:0 0 4px rgba(56,189,248,0.6);">⚓</div>',
    iconSize: [20, 20],
    iconAnchor: [10, 20],
    popupAnchor: [0, -20]
  });

  portData.forEach(p => {
    if (!p.lat || !p.lng) return;
    L.marker([p.lat, p.lng], { icon: portIcon }).addTo(map).bindPopup(`
      <div style="font-family: Inter, sans-serif; width:180px; color:#f8fafc;">
        <div style="font-size:13px;font-weight:bold;color:#38bdf8;margin-bottom:5px;">⚓ ${p.port_name || 'N/A'}</div>
        <div style="font-size:10px;color:#94a3b8;margin-bottom:2px;">City: <strong>${p.city || 'N/A'}</strong></div>
        <div style="font-size:10px;color:#94a3b8;margin-bottom:2px;">Country: <strong>${p.country_name || 'N/A'}</strong></div>
        <div style="font-size:10px;color:#94a3b8;margin-bottom:6px;">Code: <strong>${p.type || 'N/A'}</strong></div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding-top:6px;border-top:1px solid rgba(255,255,255,0.08);">
          <span style="font-size:9px;font-weight:bold;background:rgba(56,189,248,0.1);color:#38bdf8;border-radius:4px;padding:1.5px 5px;">${p.type || 'Hub'}</span>
          <span style="font-size:9px;font-weight:bold;color:${p.status === 'Active' ? '#10B981' : '#EF4444'};">${p.status || 'Active'}</span>
        </div>
      </div>`);
  });

  window.mapInstance = map; // expose globally for dashboard.js checks
}

// Chart.js default theme & builder
function createChart(id, type, data, options = {}) {
  const ctx = document.getElementById(id).getContext('2d');
  const defaultOpts = {
    responsive: true,
    maintainAspectRatio: false,
    animation: { duration: 1200, easing: 'easeOutQuart' },
    plugins: { legend: { labels: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary') } } },
    scales: {
      x: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary') } },
      y: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary') } }
    }
  };
  return new Chart(ctx, { type, data, options: Object.assign(defaultOpts, options) });
}

// Render activity timeline from API log array
function renderTimeline(logArray) {
  const ul = document.getElementById('activity-timeline');
  if (!ul) return;
  ul.innerHTML = '';
  logArray.forEach(item => {
    const li = document.createElement('li');
    li.innerHTML = `<span>${new Date(item.timestamp).toLocaleString()}</span> - ${item.message}`;
    ul.appendChild(li);
  });
}

// Live clock in header
function startLiveClock() {
  const el = document.getElementById('live-clock');
  if (!el) return;
  setInterval(() => {
    const now = new Date();
    el.textContent = now.toLocaleTimeString();
  }, 1000);
}

// Table search utility
function initTableSearch(tableId, searchInputId) {
  const table = document.getElementById(tableId);
  const input = document.getElementById(searchInputId);
  if (!table || !input) return;
  const rows = Array.from(table.tBodies[0].rows);
  input.addEventListener('input', () => {
    const term = input.value.toLowerCase();
    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(term) ? '' : 'none';
    });
  });
}

// Pagination helper (simple client‑side)
function paginateTable(tableId, perPage = 10) {
  const table = document.getElementById(tableId);
  if (!table) return;
  const tbody = table.tBodies[0];
  const rows = Array.from(tbody.rows);
  const totalPages = Math.ceil(rows.length / perPage);
  let current = 1;
  const renderPage = (page) => {
    const start = (page - 1) * perPage;
    const end = start + perPage;
    rows.forEach((row, i) => {
      row.style.display = (i >= start && i < end) ? '' : 'none';
    });
  };
  renderPage(current);
  // simple prev/next controls could be inserted here if needed
}

// Initialize when DOM loaded
document.addEventListener('DOMContentLoaded', () => {
  startLiveClock();
  // other init actions (chart building etc.) are called from loadDashboard in dashboard.blade.php
});

// --- Rendering Helper Functions ---

let riskDistributionChartInst = null;
function drawRiskDistributionChart(riskProfile) {
  if (!riskProfile || !document.getElementById('riskChart')) return;
  const ctx = document.getElementById('riskChart').getContext('2d');
  const data = [riskProfile.critical, riskProfile.high, riskProfile.medium, riskProfile.low];
  
  if (riskDistributionChartInst) {
    riskDistributionChartInst.data.datasets[0].data = data;
    riskDistributionChartInst.update();
    return;
  }
  
  riskDistributionChartInst = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Critical', 'High', 'Medium', 'Low'],
      datasets: [{
        data: data,
        backgroundColor: ['#EF4444', '#F97316', '#EAB308', '#10B981'],
        borderWidth: 1,
        borderColor: 'rgba(255, 255, 255, 0.05)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
          labels: {
            color: '#cbd5e1',
            font: { family: 'Inter', size: 11 }
          }
        }
      },
      cutout: '70%'
    }
  });
}

let topRisksChartInst = null;
function drawTopRisksChart(topRisks) {
  if (!topRisks || !document.getElementById('topRiskChart')) return;
  const sliced = topRisks.slice(0, 5);
  const labels = sliced.map(r => r.country_name);
  const data = sliced.map(r => r.score);
  const ctx = document.getElementById('topRiskChart').getContext('2d');
  
  if (topRisksChartInst) {
    topRisksChartInst.data.labels = labels;
    topRisksChartInst.data.datasets[0].data = data;
    topRisksChartInst.update();
    return;
  }
  
  topRisksChartInst = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Risk Score',
        data: data,
        backgroundColor: 'rgba(239, 68, 68, 0.75)',
        borderColor: '#EF4444',
        borderWidth: 1,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: {
          ticks: { color: '#94a3b8', font: { family: 'Inter', size: 10 } },
          grid: { display: false }
        },
        y: {
          min: 0,
          max: 100,
          ticks: { color: '#94a3b8', font: { family: 'Inter', size: 10 } },
          grid: { color: 'rgba(255, 255, 255, 0.05)' }
        }
      }
    }
  });
}

function riskBadge(level) {
  const map = {
    Critical: 'bg-red-500/15 text-red-400 border border-red-500/25 px-2.5 py-0.5 text-[10px] font-bold rounded-md',
    High: 'bg-orange-500/15 text-orange-400 border border-orange-500/25 px-2.5 py-0.5 text-[10px] font-bold rounded-md',
    Medium: 'bg-amber-500/15 text-amber-400 border border-amber-500/25 px-2.5 py-0.5 text-[10px] font-bold rounded-md',
    Low: 'bg-green-500/15 text-green-400 border border-green-500/25 px-2.5 py-0.5 text-[10px] font-bold rounded-md'
  };
  return map[level] || 'bg-slate-500/15 text-slate-400 px-2.5 py-0.5 text-[10px] font-bold rounded-md';
}

function drawLists(data) {
  // Top High Risk Countries Table
  const highBody = document.getElementById('top-risks-body');
  if (highBody && data.topRisks) {
    highBody.innerHTML = '';
    data.topRisks.slice(0, 5).forEach(r => {
      const tr = document.createElement('tr');
      tr.className = 'border-b border-white/5 last:border-0';
      tr.innerHTML = `
        <td class="px-4 py-3 flex items-center gap-2">
          ${r.flag ? `<img src="${r.flag}" class="w-4 h-2.5 object-cover rounded-sm border border-white/10">` : ''}
          <span class="font-semibold text-slate-200">${r.country_name}</span>
        </td>
        <td class="px-3 py-3 text-center font-bold text-red-400">${r.score}</td>
        <td class="px-3 py-3 text-center"><span class="${riskBadge(r.riskLevel)}">${r.riskLevel}</span></td>
      `;
      highBody.appendChild(tr);
    });
  }
  // Active Ports Status Table
  const portsBody = document.getElementById('active-ports-body');
  if (portsBody && data.activePorts) {
    portsBody.innerHTML = '';
    data.activePorts.slice(0, 5).forEach(p => {
      const tr = document.createElement('tr');
      tr.className = 'border-b border-white/5 last:border-0';
      tr.innerHTML = `
        <td class="px-4 py-3 font-semibold text-slate-200">⚓ ${p.port_name}</td>
        <td class="px-3 py-3 text-slate-400">${p.city}, ${p.country_name}</td>
        <td class="px-3 py-3 text-center">
          <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-md ${p.status === 'Active' ? 'bg-green-500/15 text-green-400 border border-green-500/25' : 'bg-red-500/15 text-red-400 border border-red-500/25'}">${p.status}</span>
        </td>
      `;
      portsBody.appendChild(tr);
    });
  }
}

function drawApiStatus(statusArray) {
  const container = document.getElementById('api-status-container');
  if (!container) return;
  container.innerHTML = '';
  if (!statusArray || statusArray.length === 0) {
    const p = document.createElement('p');
    p.className = 'text-center text-slate-500 py-4 text-xs';
    p.textContent = 'All APIs operational';
    container.appendChild(p);
    return;
  }
  statusArray.forEach(s => {
    const div = document.createElement('div');
    div.className = 'flex items-center justify-between py-2 border-b border-white/5 last:border-0';
    div.innerHTML = `
      <span class="text-xs text-slate-400">${s.name}</span>
      <span class="text-xs font-bold ${s.status === 'ACTIVE' ? 'text-green-400' : 'text-red-400'}">${s.status}</span>
    `;
    container.appendChild(div);
  });
}

// End of rendering helpers
