// public/js/dashboard.js — Ocean & Terracotta Coastal Theme Dashboard Logic

// Animated counter utility
function animateNumber(el, target) {
  if (!el) return;
  const duration = 1000;
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

// Leaflet map initialization with dark CARTO tiles and custom coastal pins
function initLeafletMap(riskData = [], portData = []) {
  const container = document.getElementById('map-dashboard');
  if (!container || window.mapInstance) return;

  const map = L.map('map-dashboard', {
    zoomControl: true,
    fullscreenControl: true,
    scrollWheelZoom: true
  }).setView([20, 0], 2);

  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> &copy; <a href="https://carto.com/" target="_blank">CARTO</a>',
    maxZoom: 18,
    subdomains: 'abcd'
  }).addTo(map);

  const riskColors = {
    Critical: '#EF4444',
    High: '#F97316',
    Medium: '#F59E0B',
    Low: '#00D2FF'
  };

  riskData.forEach(r => {
    if (!r.lat || !r.lng) return;
    const color = riskColors[r.riskLevel] || '#00D2FF';
    const icon = L.divIcon({
      className: 'custom-risk-pin',
      html: `<div style="background:${color};width:14px;height:14px;border-radius:50%;border:2px solid #ffffff;box-shadow:0 0 14px ${color};"></div>`,
      iconSize: [18, 18],
      iconAnchor: [9, 9]
    });

    L.marker([r.lat, r.lng], { icon }).addTo(map).bindPopup(`
      <div style="font-family: Inter, sans-serif; color:#f8fafc; padding:4px;">
        <div style="font-size:13px;font-weight:700;color:${color};margin-bottom:6px;display:flex;align-items:center;gap:6px;">
          ${r.flag ? `<img src="${r.flag}" style="width:18px;height:12px;object-fit:cover;border-radius:2px;">` : ''}
          ${r.country_name}
        </div>
        <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">Risk Index Score: <strong style="color:#fff;font-family:Outfit,sans-serif;font-size:13px;">${r.total_score || r.score || 'N/A'}</strong></div>
        <div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">Risk Category: <strong style="color:${color};">${r.riskLevel}</strong></div>
        <div style="font-size:10px;color:#64748B;margin-top:6px;padding-top:6px;border-top:1px solid rgba(255,255,255,0.08);">
          Currency: ${r.currency || 'N/A'} &bull; Port: ${r.main_port || 'N/A'}
        </div>
      </div>`);
  });

  const portIcon = L.divIcon({
    className: 'custom-port-pin',
    html: '<div style="font-size:15px; text-shadow:0 0 10px rgba(0,210,255,0.9);">⚓</div>',
    iconSize: [20, 20],
    iconAnchor: [10, 20]
  });

  portData.forEach(p => {
    if (!p.lat || !p.lng) return;
    L.marker([p.lat, p.lng], { icon: portIcon }).addTo(map).bindPopup(`
      <div style="font-family: Inter, sans-serif; width:180px; color:#f8fafc; padding:4px;">
        <div style="font-size:13px;font-weight:bold;color:#00D2FF;margin-bottom:6px;">⚓ ${p.port_name || 'N/A'}</div>
        <div style="font-size:10px;color:#94a3b8;margin-bottom:2px;">City: <strong style="color:#fff;">${p.city || 'N/A'}</strong></div>
        <div style="font-size:10px;color:#94a3b8;margin-bottom:2px;">Country: <strong style="color:#fff;">${p.country_name || 'N/A'}</strong></div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;padding-top:6px;border-top:1px solid rgba(255,255,255,0.08);">
          <span style="font-size:9px;font-weight:bold;background:rgba(0,210,255,0.15);color:#00D2FF;border-radius:4px;padding:2px 6px;">${p.type || 'Hub'}</span>
          <span style="font-size:9px;font-weight:bold;color:${p.status === 'Active' ? '#00D2FF' : '#EF4444'};">${p.status || 'Active'}</span>
        </div>
      </div>`);
  });

  window.mapInstance = map;
}

// Chart.js Plugin for Center Text Overlay in Doughnut Chart
const doughnutCenterTextPlugin = {
  id: 'doughnutCenterText',
  beforeDraw(chart) {
    if (chart.config.type !== 'doughnut') return;
    const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
    ctx.save();
    
    // Draw Center Score / Text
    const centerX = (left + right) / 2;
    const centerY = (top + bottom) / 2;

    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    
    // Primary Text
    ctx.font = '700 22px Outfit, sans-serif';
    ctx.fillStyle = '#F8FAFC';
    ctx.fillText('100%', centerX, centerY - 8);

    // Subtitle
    ctx.font = '500 10px Inter, sans-serif';
    ctx.fillStyle = '#00D2FF';
    ctx.fillText('Global Risk', centerX, centerY + 12);
    
    ctx.restore();
  }
};

// Chart.js Risk Distribution (Doughnut)
let riskDistributionChartInst = null;
function drawRiskDistributionChart(riskProfile) {
  if (!riskProfile || !document.getElementById('riskChart')) return;
  const ctx = document.getElementById('riskChart').getContext('2d');
  const data = [riskProfile.critical || 0, riskProfile.high || 0, riskProfile.medium || 0, riskProfile.low || 0];

  if (riskDistributionChartInst) {
    riskDistributionChartInst.data.datasets[0].data = data;
    riskDistributionChartInst.update();
    return;
  }

  // Coastal Theme Palette: Terracotta Red, Flame Orange, Sand Amber, Ocean Cyan
  const gradCrit = ctx.createLinearGradient(0, 0, 0, 200);
  gradCrit.addColorStop(0, '#EA580C');
  gradCrit.addColorStop(1, '#EF4444');

  const gradHigh = ctx.createLinearGradient(0, 0, 0, 200);
  gradHigh.addColorStop(0, '#F97316');
  gradHigh.addColorStop(1, '#FF8C00');

  const gradMed = ctx.createLinearGradient(0, 0, 0, 200);
  gradMed.addColorStop(0, '#F59E0B');
  gradMed.addColorStop(1, '#D97706');

  const gradLow = ctx.createLinearGradient(0, 0, 0, 200);
  gradLow.addColorStop(0, '#00D2FF');
  gradLow.addColorStop(1, '#06B6D4');

  riskDistributionChartInst = new Chart(ctx, {
    type: 'doughnut',
    plugins: [doughnutCenterTextPlugin],
    data: {
      labels: ['Critical Risk', 'High Risk', 'Medium Risk', 'Low Risk'],
      datasets: [{
        data: data,
        backgroundColor: [gradCrit, gradHigh, gradMed, gradLow],
        borderWidth: 3,
        borderColor: '#050D1A',
        hoverOffset: 8,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      onClick: function(e, elements) {
        window.location.href = '/risk-scores';
      },
      plugins: {
        legend: {
          position: 'right',
          labels: {
            color: '#94A3B8',
            usePointStyle: true,
            pointStyle: 'circle',
            boxWidth: 8,
            padding: 16,
            font: { family: 'Inter', size: 11, weight: '500' }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(9, 21, 39, 0.96)',
          titleFont: { family: 'Outfit', size: 12, weight: '600' },
          bodyFont: { family: 'Inter', size: 11 },
          padding: 12,
          cornerRadius: 10,
          borderColor: 'rgba(0, 210, 255, 0.2)',
          borderWidth: 1,
          callbacks: {
            label: (ctx) => ` ${ctx.label}: ${ctx.raw}%`
          }
        }
      },
      cutout: '72%'
    }
  });
}

// Chart.js Top 5 Sovereign Risk Index (Bar Chart)
let topRisksChartInst = null;
function drawTopRisksChart(topRisks) {
  if (!topRisks || !document.getElementById('topRiskChart')) return;
  const sliced = topRisks.slice(0, 5);
  const labels = sliced.map(r => r.country_name);
  const data = sliced.map(r => r.score || r.total_score || 0);
  const ctx = document.getElementById('topRiskChart').getContext('2d');

  if (topRisksChartInst) {
    topRisksChartInst.data.labels = labels;
    topRisksChartInst.data.datasets[0].data = data;
    topRisksChartInst.update();
    return;
  }

  // Create Terracotta-to-Ocean Gradient Fill
  const barGrad = ctx.createLinearGradient(0, 0, 0, 220);
  barGrad.addColorStop(0, '#F97316');
  barGrad.addColorStop(0.5, '#EA580C');
  barGrad.addColorStop(1, 'rgba(0, 210, 255, 0.25)');

  topRisksChartInst = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Risk Index',
        data: data,
        backgroundColor: barGrad,
        hoverBackgroundColor: '#F97316',
        borderColor: '#F97316',
        borderWidth: 1,
        borderRadius: 10,
        borderSkipped: false,
        barThickness: 32
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      onClick: function(e, elements) {
        window.location.href = '/risk-scores';
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(9, 21, 39, 0.96)',
          titleFont: { family: 'Outfit', size: 12, weight: '600' },
          bodyFont: { family: 'Inter', size: 11 },
          padding: 12,
          cornerRadius: 10,
          borderColor: 'rgba(0, 210, 255, 0.2)',
          borderWidth: 1,
          callbacks: {
            label: (ctx) => ` Risk Index: ${ctx.raw}/100`
          }
        }
      },
      scales: {
        x: {
          ticks: { color: '#CBD5E1', font: { family: 'Inter', size: 11, weight: '500' } },
          grid: { display: false },
          border: { display: false }
        },
        y: {
          min: 0,
          max: 100,
          ticks: { color: '#64748B', font: { family: 'Inter', size: 10 }, stepSize: 25 },
          grid: { color: 'rgba(0, 210, 255, 0.05)' },
          border: { display: false }
        }
      }
    }
  });
}

function riskBadge(level) {
  const map = {
    Critical: 'sg-badge critical',
    High: 'sg-badge high',
    Medium: 'sg-badge medium',
    Low: 'sg-badge low'
  };
  return map[level] || 'sg-badge';
}

function drawLists(data) {
  // 1. Top High Risk Countries Table
  const highBody = document.getElementById('top-risks-body');
  if (highBody && data.topRisks) {
    highBody.innerHTML = '';
    data.topRisks.slice(0, 5).forEach(r => {
      const score = r.score || r.total_score || 0;
      let scoreColorClass = 'red';
      if (score < 26) scoreColorClass = 'emerald';
      else if (score < 51) scoreColorClass = 'amber';

      const tr = document.createElement('tr');
      tr.className = 'sg-table-row';
      tr.title = `Click to view risk analysis for ${r.country_name}`;
      tr.onclick = function() {
        window.location.href = '/risk-scores';
      };
      tr.innerHTML = `
        <td class="py-3 flex items-center gap-2.5">
          ${r.flag ? `<img src="${r.flag}" class="w-5 h-3.5 object-cover rounded-sm border border-white/10 shadow-sm">` : '<span class="text-xs">🌐</span>'}
          <span class="font-medium text-slate-200 text-xs">${r.country_name}</span>
        </td>
        <td class="py-3 text-center">
          <div class="flex items-center justify-center gap-2">
            <span class="font-extrabold text-white font-outfit text-sm">${score}</span>
            <div class="sg-score-track">
              <div class="sg-score-fill ${scoreColorClass}" style="width: ${Math.min(100, score)}%;"></div>
            </div>
          </div>
        </td>
        <td class="py-3 text-center"><span class="${riskBadge(r.riskLevel)}">${r.riskLevel}</span></td>
      `;
      highBody.appendChild(tr);
    });
  }

  // 2. Active Ports Status Table
  const portsBody = document.getElementById('active-ports-body');
  if (portsBody && data.activePorts) {
    portsBody.innerHTML = '';
    data.activePorts.slice(0, 5).forEach(p => {
      const tr = document.createElement('tr');
      tr.className = 'sg-table-row';
      tr.title = `Click to view maritime ports`;
      tr.onclick = function() {
        window.location.href = '/ports';
      };
      tr.innerHTML = `
        <td class="py-3 font-medium text-slate-200 text-xs flex items-center gap-1.5">
          <span class="text-cyan-400">⚓</span> ${p.port_name}
        </td>
        <td class="py-3 text-slate-400 text-xs">${p.city || 'N/A'}, ${p.country_name || ''}</td>
        <td class="py-3 text-center">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[9px] font-bold rounded-full ${p.status === 'Active' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'}">
            <span class="w-1.5 h-1.5 rounded-full ${p.status === 'Active' ? 'bg-cyan-400 animate-pulse' : 'bg-rose-400'}"></span>
            ${p.status || 'Active'}
          </span>
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
    p.className = 'text-center text-slate-400 py-4 text-xs';
    p.textContent = 'All API integrations healthy';
    container.appendChild(p);
    return;
  }
  statusArray.forEach(s => {
    const div = document.createElement('div');
    div.className = 'flex items-center justify-between py-2 border-b border-white/[0.04] last:border-0';
    div.innerHTML = `
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full ${s.status === 'ACTIVE' ? 'bg-cyan-400 shadow-sm shadow-cyan-400' : 'bg-rose-400'}"></span>
        <span class="text-xs font-semibold text-slate-300">${s.name}</span>
      </div>
      <span class="text-[10px] font-extrabold tracking-wider ${s.status === 'ACTIVE' ? 'text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded border border-cyan-500/20' : 'text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/20'}">${s.status}</span>
    `;
    container.appendChild(div);
  });
}
