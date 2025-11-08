<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Таск-менеджер</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            margin: var(--space-6) 0;
        }
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: var(--space-6);
            margin-bottom: var(--space-8);
        }
        .chart-card {
            background: var(--bg-card);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-lg);
            padding: var(--space-6);
        }
        .chart-title {
            font-size: var(--font-size-lg);
            font-weight: 600;
            margin-bottom: var(--space-4);
            color: var(--text-primary);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
    <div class="header">
        <div class="container header-content">
            <a href="/tasks" class="logo">TaskManager</a>
            <div class="nav">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="text-sm">Привет, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
                    <a href="/tasks" class="<?php echo $_SERVER['REQUEST_URI'] == '/tasks' ? 'active' : ''; ?>">Активные</a>
                    <a href="/tasks/history" class="<?php echo $_SERVER['REQUEST_URI'] == '/tasks/history' ? 'active' : ''; ?>">История</a>
                    <a href="/tasks/create" class="<?php echo $_SERVER['REQUEST_URI'] == '/tasks/create' ? 'active' : ''; ?>">Новая</a>
                    <a href="/logout" class="btn btn-outline btn-sm">🚪 Выйти</a>
                <?php else: ?>
                    <a href="/login" class="<?php echo $_SERVER['REQUEST_URI'] == '/login' ? 'active' : ''; ?>">Войти</a>
                    <a href="/register" class="<?php echo $_SERVER['REQUEST_URI'] == '/register' ? 'active' : ''; ?>">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="content">
    <div class="container">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success fade-in">✅ <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error fade-in">❌ <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>