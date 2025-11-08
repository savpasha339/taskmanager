<?php 
$root_path = dirname(dirname(dirname(__DIR__)));
include $root_path . '/resources/views/layout/header.php'; 

$sort_by = $_GET['sort'] ?? 'priority';
$sort_order = $_GET['order'] ?? 'ASC';

$priority_counts = [
    'high' => 0,
    'medium' => 0, 
    'low' => 0
];

if (isset($tasks) && is_array($tasks)) {
    foreach ($tasks as $task) {
        if (isset($priority_counts[$task['priority']])) {
            $priority_counts[$task['priority']]++;
        }
    }
}
?>

<div class="fade-in">
    <!-- Статистика -->
    <?php if (isset($stats) && $stats['total'] > 0): ?>
    <div class="stats-grid mb-6">
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['total']; ?></span>
            <span class="stat-label">Всего задач</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['completed']; ?></span>
            <span class="stat-label">Выполнено</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['pending']; ?></span>
            <span class="stat-label">В работе</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['high_priority']; ?></span>
            <span class="stat-label">Высокий приоритет</span>
        </div>
    </div>

    <!-- Графики -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-title">📊 Статусы задач</div>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-title">🎯 Приоритеты задач</div>
            <div class="chart-container">
                <canvas id="priorityChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-title">📈 Прогресс выполнения</div>
            <div class="chart-container">
                <canvas id="progressChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-title">📅 Задачи по неделям</div>
            <div class="chart-container">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Заголовок и кнопка -->
    <div class="flex justify-between items-center mb-6">
        <h1>📋 Активные задачи</h1>
        <div class="flex gap-4">
            <a href="/tasks/history" class="btn btn-outline">
                📚 История
            </a>
            <a href="/tasks/create" class="btn ">
                 Новая задача
            </a>
        </div>
    </div>

    <!-- Сортировка -->
    <div class="card mb-6">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <span class="text-sm text-secondary">Сортировка активных задач:</span>
                <div class="flex gap-2">
                    <select id="sortSelect" class="form-control form-select" style="width: auto;">
                        <option value="priority" <?php echo $sort_by == 'priority' ? 'selected' : ''; ?>>По приоритету</option>
                        <option value="due_date" <?php echo $sort_by == 'due_date' ? 'selected' : ''; ?>>По сроку выполнения</option>
                        <option value="created_at" <?php echo $sort_by == 'created_at' ? 'selected' : ''; ?>>По дате создания</option>
                        <option value="title" <?php echo $sort_by == 'title' ? 'selected' : ''; ?>>По названию</option>
                    </select>
                    <select id="orderSelect" class="form-control form-select" style="width: auto;">
                        <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>По возрастанию</option>
                        <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>По убыванию</option>
                    </select>
                    <button onclick="applySort()" class="btn btn-outline btn-sm">Применить</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Список задач -->
    <div class="table-container">
        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h3 class="text-primary mb-2">Задач пока нет</h3>
                <p class="text-secondary mb-6">Создайте свою первую задачу чтобы начать работу</p>
                <a href="/tasks/create" class="btn ">
                    Создать первую задачу
                </a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Задача</th>
                        <th>Комментарий</th>
                        <th>Приоритет</th>
                        <th>Срок</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr class="<?php echo $task['status'] == 'completed' ? 'status-completed' : ''; ?>">
                            <td>
                                <div class="font-semibold"><?php echo htmlspecialchars($task['title']); ?></div>
                                <?php if (!empty($task['description']) && trim($task['description']) !== ''): ?>
                                    <div class="text-sm text-secondary mt-1">
                                        <?php echo htmlspecialchars($task['description']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($task['comment']) && trim($task['comment']) !== ''): ?>
                                    <div class="text-sm text-secondary">
                                        💬 <?php echo htmlspecialchars($task['comment']); ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted text-sm">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $task['priority']; ?>">
                                    <?php 
                                    $priority_labels = [
                                        'high' => 'Высокий',
                                        'medium' => 'Средний', 
                                        'low' => 'Низкий'
                                    ];
                                    echo $priority_labels[$task['priority']];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($task['due_date'] && $task['due_date'] != '0000-00-00'): ?>
                                    <span class="text-sm <?php 
                                        echo (strtotime($task['due_date']) < time() && $task['status'] == 'pending') 
                                            ? 'text-error' 
                                            : 'text-secondary';
                                    ?>">
                                        📅 <?php echo date('d.m.Y', strtotime($task['due_date'])); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted text-sm">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($task['status'] == 'completed'): ?>
                                    <span class="badge badge-completed">✅ Выполнено</span>
                                <?php else: ?>
                                    <span class="badge badge-pending">⏳ В работе</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <?php if ($task['status'] == 'pending'): ?>
                                        <a href="/tasks/update-status?id=<?php echo $task['id']; ?>&status=completed" 
                                           class="btn btn-success btn-sm" title="Отметить выполненной">
                                            ✅
                                        </a>
                                    <?php else: ?>
                                        <a href="/tasks/update-status?id=<?php echo $task['id']; ?>&status=pending" 
                                           class="btn btn-outline btn-sm" title="Вернуть в работу">
                                            ↩️
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="/tasks/edit?id=<?php echo $task['id']; ?>" 
                                       class="btn btn-outline btn-sm" title="Редактировать">
                                        ✏️
                                    </a>
                                    <a href="/tasks/delete?id=<?php echo $task['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirmDelete()"
                                       title="Удалить">
                                        🗑️
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript для графиков -->
<script>
    const stats = {
        total: <?php echo $stats['total'] ?? 0; ?>,
        completed: <?php echo $stats['completed'] ?? 0; ?>,
        pending: <?php echo $stats['pending'] ?? 0; ?>,
        high_priority: <?php echo $priority_counts['high'] ?? 0; ?>,
        medium_priority: <?php echo $priority_counts['medium'] ?? 0; ?>,
        low_priority: <?php echo $priority_counts['low'] ?? 0; ?>
    };

    const colors = {
        primary: '#ffffff',
        secondary: '#666666',
        success: '#10b981',
        warning: '#f59e0b',
        error: '#ef4444',
        info: '#3b82f6',
        background: 'rgba(255, 255, 255, 0.1)',
        border: 'rgba(255, 255, 255, 0.2)'
    };

    document.addEventListener('DOMContentLoaded', function() {
        if (stats.total > 0) {
            initializeCharts();
            animateCharts();
        } else {
            const chartsGrid = document.querySelector('.charts-grid');
            if (chartsGrid) {
                chartsGrid.style.display = 'none';
            }
        }
    });

    function initializeCharts() {
        // График статусов задач
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Выполнено', 'В работе'],
                datasets: [{
                    data: [stats.completed, stats.pending],
                    backgroundColor: [colors.success, colors.warning],
                    borderColor: [colors.success, colors.warning],
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: colors.primary,
                            font: { size: 12 },
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: colors.background,
                        titleColor: colors.primary,
                        bodyColor: colors.primary,
                        borderColor: colors.border,
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%',
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        // График приоритетов
        const priorityCtx = document.getElementById('priorityChart').getContext('2d');
        new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: ['Высокий', 'Средний', 'Низкий'],
                datasets: [{
                    label: 'Количество задач',
                    data: [stats.high_priority, stats.medium_priority, stats.low_priority],
                    backgroundColor: [colors.error, colors.warning, colors.success],
                    borderColor: [colors.error, colors.warning, colors.success],
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: colors.background,
                        titleColor: colors.primary,
                        bodyColor: colors.primary,
                        borderColor: colors.border,
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: colors.border },
                        ticks: { color: colors.secondary, stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: colors.secondary }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });

        // График прогресса
        const progressCtx = document.getElementById('progressChart').getContext('2d');
        new Chart(progressCtx, {
            type: 'line',
            data: {
                labels: ['Всего', 'Выполнено', 'В работе'],
                datasets: [{
                    label: 'Прогресс',
                    data: [stats.total, stats.completed, stats.pending],
                    borderColor: colors.info,
                    backgroundColor: colors.info + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.info,
                    pointBorderColor: colors.primary,
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: colors.background,
                        titleColor: colors.primary,
                        bodyColor: colors.primary,
                        borderColor: colors.border,
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: colors.border },
                        ticks: { color: colors.secondary, stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: colors.secondary }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });

        // График по неделям
        const timelineCtx = document.getElementById('timelineChart').getContext('2d');
        const weeks = ['Неделя 1', 'Неделя 2', 'Неделя 3', 'Неделя 4'];
        const weeklyData = weeks.map(() => Math.floor(Math.random() * 10) + 1);
        
        new Chart(timelineCtx, {
            type: 'bar',
            data: {
                labels: weeks,
                datasets: [{
                    label: 'Задач создано',
                    data: weeklyData,
                    backgroundColor: colors.info + '80',
                    borderColor: colors.info,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: colors.background,
                        titleColor: colors.primary,
                        bodyColor: colors.primary,
                        borderColor: colors.border,
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: colors.border },
                        ticks: { color: colors.secondary, stepSize: 1 }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: colors.secondary }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    function animateCharts() {
        const charts = document.querySelectorAll('.chart-card');
        charts.forEach((chart, index) => {
            chart.style.opacity = '0';
            chart.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                chart.style.transition = 'all 0.5s ease-out';
                chart.style.opacity = '1';
                chart.style.transform = 'translateY(0)';
            }, index * 200);
        });
    }

    function applySort() {
        const sortBy = document.getElementById('sortSelect').value;
        const sortOrder = document.getElementById('orderSelect').value;
        window.location.href = `/tasks?sort=${sortBy}&order=${sortOrder}`;
    }

    function confirmDelete(message = 'Вы уверены что хотите удалить эту задачу?') {
        return confirm(message);
    }
</script>

<?php include $root_path . '/resources/views/layout/footer.php'; ?>