<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= (int)$stats['today_bookings'] ?></div>
        <div class="stat-label">Reservas Hoje</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= (int)$stats['pending_bookings'] ?></div>
        <div class="stat-label">Pendentes</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= money($stats['month_revenue']) ?></div>
        <div class="stat-label">Receita do Mês</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= (int)$stats['total_bookings'] ?></div>
        <div class="stat-label">Total de Reservas</div>
    </div>
</div>

<!-- Chart -->
<div class="admin-card">
    <h3>Reservas - Últimos 30 dias</h3>
    <canvas id="bookingsChart" height="80"></canvas>
</div>

<!-- Recent Bookings -->
<div class="admin-card">
    <div class="card-header">
        <h3>Reservas Recentes</h3>
        <a href="/admin/reservas" class="btn btn-sm btn-outline">Ver Todas</a>
    </div>
    <table class="table">
        <thead>
            <tr><th>Número</th><th>Cliente</th><th>Total</th><th>Status</th><th>Data</th></tr>
        </thead>
        <tbody>
        <?php foreach ($recentBookings as $b): ?>
        <tr>
            <td><a href="/admin/reservas/<?= (int)$b['id'] ?>"><?= e($b['booking_number']) ?></a></td>
            <td><?= e($b['customer_name'] ?? $b['billing_first_name'] . ' ' . $b['billing_last_name']) ?></td>
            <td><?= money((float)$b['total']) ?></td>
            <td><span class="badge badge-<?= booking_status_class($b['status']) ?>"><?= booking_status_label($b['status']) ?></span></td>
            <td><?= time_ago($b['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
const chartData = <?= json_encode($chartData) ?>;
</script>
