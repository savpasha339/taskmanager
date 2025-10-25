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
</head>
<body>
    <div class="main-wrapper">
    <div class="header">
        <div class="container header-content">
            <a href="/tasks" class="logo">Ptichnik</a>
            <div class="nav">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="text-sm">Привет, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
                    <a href="/tasks" class="<?php echo $_SERVER['REQUEST_URI'] == '/tasks' ? 'active' : ''; ?>">📋 Мои задачи</a>
                    <a href="/tasks/create" class="<?php echo $_SERVER['REQUEST_URI'] == '/tasks/create' ? 'active' : ''; ?>"> Новая задача</a>
                    <a href="/logout" class="btn btn-outline btn-sm"> Выйти</a>
                <?php else: ?>
                    <a href="/login" class="<?php echo $_SERVER['REQUEST_URI'] == '/login' ? 'active' : ''; ?>"> Войти</a>
                    <a href="/register" class="<?php echo $_SERVER['REQUEST_URI'] == '/register' ? 'active' : ''; ?>"> Регистрация</a>
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