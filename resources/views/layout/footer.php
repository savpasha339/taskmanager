    </div>
    </div> <!-- закрываем .content -->
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">Ptichnik</div>
                    <p class="footer-text">
                        Система управления задачами<br>
                        Разработано в Индии
                    </p>
                </div>
                <div class="footer-section">
                    <h4>Навигация</h4>
                    <ul class="footer-links">
                        <li><a href="/tasks">Мои задачи</a></li>
                        <li><a href="/tasks/create">Создать задачу</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Технологии</h4>
                    <ul class="footer-links">
                        <li>PHP 8+</li>
                        <li>MySQL</li>
                        <li>HTML5 & CSS3</li>
                        <li>Composer PSR-4</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 VVSU Development. Думайте. Подписаться. </p>
                <div class="footer-tech">
                    <span class="tech-badge">PHP</span>
                    <span class="tech-badge">MySQL</span>
                    <span class="tech-badge">CSS3</span>
                </div>
            </div>
        </div>
    </footer>
    </div> 

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('form input:not([type="hidden"])');
            if (firstInput) firstInput.focus();
        });
        
        // Подтверждение удаления
        function confirmDelete(message = 'Вы уверены что хотите удалить эту задачу?') {
            return confirm(message);
        }
        
        // Валидация форм в реальном времени
        document.querySelectorAll('form').forEach(form => {
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.style.borderColor = 'var(--danger)';
                    } else {
                        this.style.borderColor = 'var(--success)';
                    }
                });
                
                input.addEventListener('input', function() {
                    this.style.borderColor = 'var(--laravel-gray-300)';
                });
            });
        });
    </script>
</body>
</html>