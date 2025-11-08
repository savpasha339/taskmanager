<?php 
$root_path = dirname(dirname(dirname(__DIR__)));
include $root_path . '/resources/views/layout/header.php'; 

$sort_by = $_GET['sort'] ?? 'created_at';
$sort_order = $_GET['order'] ?? 'DESC';
?>

<div class="fade-in">
    <!-- Заголовок и навигация -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1><span class="icon icon-history"></span> История задач</h1>
            <p class="text-secondary mt-2">Выполненные и архивные задачи</p>
        </div>
        <div class="flex gap-4">
            <a href="/tasks" class="btn btn-outline">
                ← Активные задачи
            </a>
        </div>
    </div>

    <!-- Статистика выполненных задач -->
    <?php if (isset($stats) && $stats['completed'] > 0): ?>
    <div class="stats-grid mb-6">
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['completed']; ?></span>
            <span class="stat-label">Всего выполнено</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['total']; ?></span>
            <span class="stat-label">Всего создано</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo round(($stats['completed'] / max($stats['total'], 1)) * 100); ?>%</span>
            <span class="stat-label">Процент выполнения</span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Сортировка -->
    <div class="card mb-6">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <span class="text-sm text-secondary">Сортировка:</span>
                <div class="flex gap-2">
                    <select id="sortSelect" class="form-control form-select" style="width: auto;">
                        <option value="created_at" <?php echo $sort_by == 'created_at' ? 'selected' : ''; ?>>По дате создания</option>
                        <option value="updated_at" <?php echo $sort_by == 'updated_at' ? 'selected' : ''; ?>>По дате завершения</option>
                        <option value="due_date" <?php echo $sort_by == 'due_date' ? 'selected' : ''; ?>>По сроку выполнения</option>
                        <option value="title" <?php echo $sort_by == 'title' ? 'selected' : ''; ?>>По названию</option>
                        <option value="priority" <?php echo $sort_by == 'priority' ? 'selected' : ''; ?>>По приоритету</option>
                    </select>
                    <select id="orderSelect" class="form-control form-select" style="width: auto;">
                        <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>По убыванию</option>
                        <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>По возрастанию</option>
                    </select>
                    <button onclick="applySort()" class="btn btn-outline btn-sm">Применить</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Список выполненных задач -->
    <div class="table-container">
        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📚</div>
                <h3 class="text-primary mb-2">История пуста</h3>
                <p class="text-secondary mb-6">Здесь появятся выполненные задачи</p>
                <a href="/tasks" class="btn">
                Перейти к активным задачам
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
                        <th>Завершено</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr class="status-completed">
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
                                    <span class="text-sm text-secondary">
                                        <span class="icon icon-date"></span> <?php echo date('d.m.Y', strtotime($task['due_date'])); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted text-sm">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-sm text-secondary">
                                    <span class="icon icon-time"></span><?php echo date('d.m.Y H:i', strtotime($task['updated_at'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="/tasks/update-status?id=<?php echo $task['id']; ?>&status=pending" 
                                       class="btn btn-outline btn-sm" title="Вернуть в работу">
                                        <span class="icon icon-back"></span>
                                    </a>
                                    <a href="/tasks/edit?id=<?php echo $task['id']; ?>" 
                                       class="btn btn-outline btn-sm" title="Редактировать">
                                        <span class="icon icon-edit"></span>
                                    </a>
                                    <a href="/tasks/delete?id=<?php echo $task['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirmDelete()"
                                       title="Удалить">
                                        <span class="icon icon-delete"></span>
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

<script>
    function applySort() {
        const sortBy = document.getElementById('sortSelect').value;
        const sortOrder = document.getElementById('orderSelect').value;
        window.location.href = `/tasks/history?sort=${sortBy}&order=${sortOrder}`;
    }

    function confirmDelete(message = 'Вы уверены что хотите удалить эту задачу?') {
        return confirm(message);
    }
</script>

<?php include $root_path . '/resources/views/layout/footer.php'; ?>