<?php 
$root_path = dirname(dirname(dirname(__DIR__)));
include $root_path . '/resources/views/layout/header.php'; 
?>

<div class="container-sm">
    <div class="card fade-in">
        <div class="card-header">
            <h1> Новая задача</h1>
        </div>
        <div class="card-body">
            <form action="/tasks/create" method="POST">
                <div class="form-group">
                    <label for="title" class="form-label">Название задачи *</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           placeholder="Введите название задачи" required
                           value="<?php echo $_POST['title'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Описание</label>
                    <textarea id="description" name="description" class="form-control" 
                              rows="4" placeholder="Опишите детали задачи..."><?php echo $_POST['description'] ?? ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="priority" class="form-label">Приоритет</label>
                    <select id="priority" name="priority" class="form-control form-select">
                        <option value="low" <?php echo ($_POST['priority'] ?? 'medium') == 'low' ? 'selected' : ''; ?>>🟢 Низкий</option>
                        <option value="medium" <?php echo ($_POST['priority'] ?? 'medium') == 'medium' ? 'selected' : ''; ?>>🟡 Средний</option>
                        <option value="high" <?php echo ($_POST['priority'] ?? 'medium') == 'high' ? 'selected' : ''; ?>>🔴 Высокий</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="due_date" class="form-label">Срок выполнения</label>
                    <input type="date" id="due_date" name="due_date" class="form-control"
                           value="<?php echo $_POST['due_date'] ?? ''; ?>">
                    <p class="text-sm text-gray-500 mt-1">Оставьте пустым если срок не установлен</p>
                </div>
                
                <div class="flex gap-4">
                    <a href="/tasks" class="btn btn-outline w-full">← Назад к списку</a>
                    <button type="submit" class="btn w-full">Создать задачу</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $root_path . '/resources/views/layout/footer.php'; ?>