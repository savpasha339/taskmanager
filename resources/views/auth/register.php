<?php 
$root_path = dirname(dirname(dirname(__DIR__)));
include $root_path . '/resources/views/layout/header.php'; 
?>

<div class="container-sm">
    <div class="card fade-in">
        <div class="card-header">
            <h1 class="text-center"> Регистрация</h1>
        </div>
        <div class="card-body">
            <form action="/register" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Имя пользователя</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Придумайте имя пользователя" required
                           value="<?php echo $_POST['username'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email адрес</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="Введите ваш email" required
                           value="<?php echo $_POST['email'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Придумайте надежный пароль" required>
                    <p class="text-sm text-gray-500 mt-1">Минимум 6 символов</p>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn w-full btn-lg">
                        Создать аккаунт
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="text-gray-600">Уже есть аккаунт? 
                        <a href="/login" >Войдите</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $root_path . '/resources/views/layout/footer.php'; ?>