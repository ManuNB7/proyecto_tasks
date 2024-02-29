<body>
    <div class="container">
        <div class="hero">
        <?php
            if (isset($_SESSION['user_id'])) {
                echo "<h2>Bienvenid@, " . $_SESSION['nombre'] . ".</h2>";
                echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
            ?>
                <a href='index.php?controller=tarea&action=form_tarea' class="cta-btn">Crear nueva tarea</a>
                <a href='index.php?controller=tarea&action=listar_tarea' class="cta-btn">Ver mis tareas</a>
            <?php
            } else {
            ?>
                <div class="container">
                    <h1>Tasks</h1>
                    <p>Organiza tus tareas de manera efectiva</p>
                </div>
                <h2>Bienvenido a Tasks</h2>
                <p>La mejor manera de organizar tus tareas y subtareas de forma fácil y rápida.</p>
                <a href='index.php?controller=sesion&action=form' class="cta-btn">Iniciar sesión</a>
            <?php
            }
            ?>
        </div>
        <div class="features">
            <div class="feature">
                <h3>Crea tareas</h3>
                <p>Crea nuevas tareas y organízalas según tus necesidades.</p>
            </div>
            <div class="feature">
                <h3>Añade subtareas</h3>
                <p>Desglosa tus tareas en subtareas para un mejor seguimiento.</p>
            </div>
            <div class="feature">
                <h3>Administra tus tareas</h3>
                <p>Modifica, elimina y marca tus tareas como completadas fácilmente.</p>
            </div>
        </div>
    </div>
</body>
</html>
