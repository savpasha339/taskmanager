<?php 
$root_path = dirname(dirname(dirname(__DIR__)));
include $root_path . '/resources/views/layout/header.php'; 

if (!isset($task)) {
    header("Location: /tasks");
    exit;
}
?>

<div class="container-sm">
    <div class="card fade-in">
        <div class="card-header">
            <h1>✏️ Редактирование задачи</h1>
        </div>
        <div class="card-body">
            <form action="/tasks/edit" method="POST">
                <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                
                <div class="form-group">
                    <label for="title" class="form-label">Название задачи *</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           value="<?php echo htmlspecialchars($task['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Описание</label>
                    <textarea id="description" name="description" class="form-control" 
                              rows="4" placeholder="Опишите детали задачи..."><?php echo htmlspecialchars($task['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="status" class="form-label">Статус</label>
                    <select id="status" name="status" class="form-control form-select">
                        <option value="pending" <?php echo $task['status'] == 'pending' ? 'selected' : ''; ?>>⏳ В работе</option>
                        <option value="completed" <?php echo $task['status'] == 'completed' ? 'selected' : ''; ?>>✅ Выполнено</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="priority" class="form-label">Приоритет</label>
                    <select id="priority" name="priority" class="form-control form-select">
                        <option value="low" <?php echo $task['priority'] == 'low' ? 'selected' : ''; ?>>🟢 Низкий</option>
                        <option value="medium" <?php echo $task['priority'] == 'medium' ? 'selected' : ''; ?>>🟡 Средний</option>
                        <option value="high" <?php echo $task['priority'] == 'high' ? 'selected' : ''; ?>>🔴 Высокий</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="due_date" class="form-label">Срок выполнения</label>
                    <input type="date" id="due_date" name="due_date" class="form-control"
                           value="<?php echo $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : ''; ?>">
                </div>
                
                <div class="flex gap-4">
                    <a href="/tasks" class="btn btn-outline w-full">← Отмена</a>
                    <button type="submit" class="btn btn-primary w-full">💾 Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $root_path . '/resources/views/layout/footer.php'; ?>