/**
 * ADMIN PANEL JavaScript
 */
(function() {
    'use strict';

    // Sidebar toggle (mobile)
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
    }
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => sidebar.classList.remove('open'));
    }

    // Settings tabs
    document.querySelectorAll('.settings-tabs .tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.settings-tabs .tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            const target = document.getElementById('tab-' + btn.dataset.tab);
            if (target) target.classList.add('active');
        });
    });

    // Confirm delete
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Tem certeza?')) e.preventDefault();
        });
    });

    // Simple chart (if canvas exists)
    const chartCanvas = document.getElementById('bookingsChart');
    if (chartCanvas && typeof chartData !== 'undefined') {
        const ctx = chartCanvas.getContext('2d');
        const maxVal = Math.max(...chartData.map(d => d.count), 1);
        const width = chartCanvas.parentElement.offsetWidth - 40;
        chartCanvas.width = width;
        chartCanvas.height = 200;
        const barWidth = Math.max(8, (width / chartData.length) - 4);

        ctx.fillStyle = '#0077b6';
        chartData.forEach((d, i) => {
            const barHeight = (d.count / maxVal) * 160;
            const x = i * (barWidth + 4) + 20;
            const y = 180 - barHeight;
            ctx.fillRect(x, y, barWidth, barHeight);
        });
    }
})();
