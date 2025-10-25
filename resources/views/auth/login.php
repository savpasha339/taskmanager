<?php 
$root_path = dirname(dirname(dirname(__DIR__)));
include $root_path . '/resources/views/layout/header.php'; 
?>

<div class="container-sm">
    <div class="card fade-in">
        <div class="card-header">
            <h1 class="text-center">Вход в систему</h1>
        </div>
        <div class="card-body">
            <form action="/login" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Имя пользователя</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Введите ваше имя пользователя" required
                           value="<?php echo $_POST['username'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Введите ваш пароль" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn w-full btn-lg">
                        Войти в систему
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="text-gray-600">Еще нет аккаунта? 
                        <a href="/register" class="text-gray-700 font-semibold">Зарегистрируйтесь</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $root_path . '/resources/views/layout/footer.php'; ?>