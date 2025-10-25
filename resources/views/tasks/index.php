<?php 
$root_path = dirname(dirname(dirname(__DIR__)));
include $root_path . '/resources/views/layout/header.php'; 
?>

<div class="fade-in">
    <!-- Статистика -->
    <?php if (isset($stats) && $stats['total'] > 0): ?>
    <div class="stats-grid mb-8">
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
    <?php endif; ?>

    <!-- Заголовок и кнопка -->
    <div class="flex justify-between items-center mb-6">
        <h1> Мои задачи</h1>
        <a href="/tasks/create" class="btn ">
             Новая задача
        </a>
    </div>

    <!-- Список задач -->
    <div class="table-container">
        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h3 class="text-gray-700 mb-2">Задач пока нет</h3>
                <p class="text-gray-500 mb-6">Создайте свою первую задачу чтобы начать работу</p>
                <a href="/tasks/create" class="btn btn-primary">
                     Создать первую задачу
                </a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Задача</th>
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
                                <?php if (!empty($task['description'])): ?>
                                    <div class="text-sm text-gray-600 mt-1">
                                        <?php echo htmlspecialchars($task['description']); ?>
                                    </div>
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
                                <?php if ($task['due_date']): ?>
                                    <span class="text-sm <?php 
                                        echo (strtotime($task['due_date']) < time() && $task['status'] == 'pending') 
                                            ? 'text-danger' 
                                            : 'text-gray-600';
                                    ?>">
                                        📅 <?php echo date('d.m.Y', strtotime($task['due_date'])); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">—</span>
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

<?php include $root_path . '/resources/views/layout/footer.php'; ?>