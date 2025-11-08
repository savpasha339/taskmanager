    </div>
    </div> <!-- закрываем .content -->
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">TaskManager</div>
                    <p class="footer-text">
                        Простая и эффективная система управления задачами<br>
                        Разработано на PHP с современным дизайном
                    </p>
                </div>
                <div class="footer-section">
                    <h4>Навигация</h4>
                    <ul class="footer-links">
                        <li><a href="/tasks">Активные задачи</a></li>
                        <li><a href="/tasks/history">История задач</a></li>
                        <li><a href="/tasks/create">Создать задачу</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Технологии</h4>
                    <ul class="footer-links">
                        <li>PHP 8+</li>
                        <li>MySQL</li>
                        <li>HTML5 & CSS3</li>
                        <li>Chart.js</li>
                        <li>Composer PSR-4</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 TaskManager. Все права защищены.</p>
                <div class="footer-tech">
                    <span class="tech-badge">PHP</span>
                    <span class="tech-badge">MySQL</span>
                    <span class="tech-badge">CSS3</span>
                    <span class="tech-badge">Chart.js</span>
                </div>
            </div>
        </div>
    </footer>
    </div> <!-- закрываем .main-wrapper -->

    <script>
        // Автофокус на первом поле формы
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
                        this.style.borderColor = 'var(--error)';
                    } else {
                        this.style.borderColor = 'var(--success)';
                    }
                });
                
                input.addEventListener('input', function() {
                    this.style.borderColor = 'var(--border-primary)';
                });
            });
        });

        // Динамическое обновление минимальной даты для сроков выполнения
        const dueDateInput = document.getElementById('due_date');
        if (dueDateInput) {
            const today = new Date().toISOString().split('T')[0];
            dueDateInput.min = today;
        }
    </script>
</body>
</html>