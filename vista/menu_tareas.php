<body>
    <header>
        <div class="container">
            <h1>Tasks</h1>
            <p>Organiza tus tareas de manera efectiva</p>
        </div>
    </header>
    <div class="container">
        <div class="hero">
            <?php
                // Check if the session variable 'nombre' (username) is set
                if (isset($_SESSION['nombre'])) {
                    // If the session variable is set, display a personalized greeting
                    echo "<h2>Bienvenid@, " . $_SESSION['nombre'] . ".</h2>";

                    // Check if the session variable 'user_id' is set (assuming 'user_id' is the user ID)
                    if (isset($_SESSION['user_id'])) {
                        echo "<p>Your user ID: " . $_SESSION['user_id'] . "</p>";
                    }
            ?>
                    <p>La mejor manera de organizar tus tareas y subtareas de forma fácil y rápida.</p>
                    <a href='index.php?controller=tarea&action=form_tarea' class="cta-btn">Crear nueva tarea</a>
                    <a href='index.php?controller=tarea&action=listar_tarea' class="cta-btn">Ver mis tareas</a>
            <?php
                } else {
                    // If the session variable is not set, display a generic message and a login button
            ?>
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
