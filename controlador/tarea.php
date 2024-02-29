<?php
    require_once __DIR__ . '/../controlador/sesion.php';
    require_once __DIR__.'/../modelo/tarea.php';
    
    /**
     * Clase tareaController
     * 
     * Controlador para la gestión de tareas y subtareas.
     */
    class tareaController {

        public $titulo;
        public $modelo;
        public $view;
        public $authController;

        /**
         * Constructor de la clase.
         * 
         * Inicializa las propiedades de la clase y crea una instancia del modelo tareaModel.
         */
        public function __construct() {
            $this->modelo = new tareaModel();
            $this->view = "form_tarea";
            $this->titulo = "Añadir tarea";
            $this->authController = new sesionController(); //Hace las comprobaciones de sesiones
        }

        public function menu_tareas() {
            // Check if there's an active session and get the user ID
            $this->authController->checkSession();
            
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Get the user ID from the session
        
                $this->titulo = "Menú TASKS";
                $this->view = "menu_tareas";
        
                // Pass the user ID to the model method
                return $this->modelo->listar_tareas($idUsuario);
            } else {
                // If there's no active session, return a flag indicating that the user should be redirected
                return "redirect_login";
            }
        }
        public function form_tarea() {
            // Check if there's an active session and get the user ID
            $this->authController->checkSession();
            
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Get the user ID from the session
        
                $this->titulo = "Añadir tarea";
                $this->view = "form_tarea";
        
                // Pass the user ID to the model method
                return $this->modelo->listar_tareas($idUsuario);
            } else {
                // If there's no active session, return a flag indicating that the user should be redirected
                return "redirect_login";
            }
        }
        

        /**
         * Método para listar las tareas.
         * 
         * Establece el título de la página como "Listar tareas",
         * la vista como "menu_listar", y realiza una simulación
         * del ID de usuario (en este caso, 4). Luego, llama al
         * método 'listar_tareas' del modelo pasando el ID de usuario
         * como argumento y retorna el resultado.
         */
        public function listar_tarea() {
            // Check if there's an active session and get the user ID
            $this->authController->checkSession();
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Get the user ID from the session
                
        
                $this->titulo = "Listar tareas";
                $this->view = "menu_listar";
                
                // Pass the user ID to the model method
                return $this->modelo->listar_tareas($idUsuario);
            } else {
                // If there's no active session, redirect to the login page
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }
        

        /**
         * Método para ver una tarea específica.
         * 
         * Establece el título de la página como "Listar tareas",
         * la vista como "ver_tarea", y obtiene el ID de la tarea
         * desde la variable $_GET si está presente, de lo contrario,
         * establece $idTarea como null. Realiza una simulación del ID
         * de usuario (en este caso, 4). Luego, llama al método
         * 'obtener_tarea_por_id' del modelo pasando el ID de la tarea
         * y el ID de usuario como argumentos y retorna el resultado.
         */
        public function ver_tarea() {
            // Check if there's an active session and get the user ID
            session_start(); // Start the session if it's not already started
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Get the user ID from the session
        
                $this->titulo = "Listar tareas";
                $this->view = "ver_tarea";
                $idTarea = isset($_GET['id']) ? $_GET['id'] : null;
                
                // Pass both the task ID and the user ID to the model method
                return $this->modelo->obtener_tarea_por_id($idTarea, $idUsuario);
            } else {
                // If there's no active session, redirect to the login page
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }
        

        
        

        /************GUARDAR TAREAS Y SUBTAREAS************/
        /**
         * Método para guardar una tarea principal.
         * 
         * Establece la vista a utilizar como "form_subtarea".
         * Obtiene los datos de la tarea principal desde el formulario POST,
         * incluyendo el título, el número de subtareas y el ID del usuario.
         * Retorna un array con los datos obtenidos.
         */
        public function guardar_tarea() {
            // Check if there's an active session and get the user ID
            session_start(); // Start the session if it's not already started
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Get the user ID from the session
        
                $this->authController->checkSession();
                $this->view = "form_subtarea";
                
                // Obtener los datos de la tarea principal
                $titulo = $_POST['titulo'];
                // Obtener número de subtareas
                $num_subtareas = $_POST['num_subtareas'];
                
                // Crear un array con los datos de la tarea
                $datos = $array_datos = array(
                    "titulo" => $titulo,
                    "num_subtareas" => $num_subtareas,
                    "idUsuario" => $idUsuario
                );
                
                return $datos;
            } else {
                // If there's no active session, redirect to the login page
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }
        

        /**
         * Método para guardar subtareas de una tarea principal.
         * 
         * Establece la vista a utilizar como "form_tarea".
         * Obtiene los datos de la tarea y sus subtareas desde el formulario POST,
         * valida los datos y realiza la inserción en la base de datos.
         * Retorna un mensaje de éxito o error y redirige a la lista de tareas.
         */
        public function guardar_subtareas() {
            // Check if there's an active session and get the user ID
            session_start(); // Start the session if it's not already started
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Get the user ID from the session
        
                $this->view = "form_tarea";
                
                // Obtener los datos de la tarea del formulario
                $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
                $detalle = isset($_POST['detalle']) ? $_POST['detalle'] : '';
                $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
                
                //Comprueba que sea un array
                $subtareas = isset($_POST['subtarea']) ? $_POST['subtarea'] : array();
                
                if (!$this->validarTarea($titulo, $detalle, $fecha, $subtareas)) {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error: Datos de tarea o subtareas no válidos.";
                    return;
                }
        
                // Inicializar la variable $nombre_archivo
                $nombre_archivo = null;
                // Manejo de la subida de archivos
                if (!empty($_FILES['archivo_principal']['name'])) {
                    // Verifica si se ha seleccionado un archivo para cargar
                    $uploadedFile = $_FILES['archivo_principal']; // Asigna el array $_FILES['archivo_principal'] a una variable
                    $carpeta_destino = 'img/archivos/'; // Carpeta donde se guardarán los archivos cargados
                    $ext = pathinfo($uploadedFile["name"], PATHINFO_EXTENSION); // Obtiene la extensión del archivo cargado
                    // Genera un nombre único para el archivo combinando un identificador único y la extensión del archivo
                    $nombre_archivo = uniqid() . "." . $ext;
                    // Mueve el archivo cargado a la carpeta de destino
                    if (!move_uploaded_file($uploadedFile['tmp_name'], $carpeta_destino . $nombre_archivo)) {
                        // En caso de error al mover el archivo, establece un mensaje de error y finaliza la ejecución
                        $_GET["tipomsg"] = "error";
                        $_GET["msg"] = "Error: No se pudo subir el archivo.";
                        return;
                    }
                }
        
                // Insertar la tarea en la base de datos            
                $idTar = $this->modelo->insertar_tarea($titulo, $detalle, $fecha, $subtareas, $nombre_archivo, $idUsuario); // Pasar el idUsuario al método insertar_tarea  
                // Verificar si la tarea se insertó correctamente
                if ($idTar) {
                    $_GET["tipomsg"] = "exito";
                    $_GET["msg"] = "Tarea guardada exitosamente.";
                } else {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error al guardar la tarea. Por favor, inténtelo de nuevo.";
                }
                // Redirigir a la lista de tareas
                return $this->listar_tarea();
            } else {
                // If there's no active session, redirect to the login page
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }
        

        
        /********PROCESO MODIFICACIÓN********/
        /**
        * Método para modificar una tarea existente.
         * 
         * Establece el título de la página como "Modificar tarea"
         * y la vista como "form_modificar". Obtiene el ID de la tarea
         * a modificar desde la variable $_GET. Llama al método del modelo
         * encargado de listar la tarea a modificar y retorna el resultado.
         */
        public function modificar_tarea() {
            $this->titulo = "Modificar tarea";
            $this->view = "form_modificar";
            
            // Obtiene el id
            $idTarea = isset($_GET['id']) ? $_GET['id'] : null;
            
            return $this->modelo->listar_mod($idTarea);
        }
        
        /**
         * Método para guardar la modificación de una tarea.
         * 
         * Obtiene el ID de la tarea a modificar desde el formulario POST,
         * así como los datos modificados de la tarea y sus subtareas.
         * Valida los datos y realiza la modificación en la base de datos.
         * Retorna un mensaje de éxito o error y redirige a la lista de tareas.
         */
        public function guardar_modificacion() {
            // Obtiene el id
            $idTarea = isset($_POST['idTarea']) ? $_POST['idTarea'] : null;
        
            // Obtiene los datos
            $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
            $detalle = isset($_POST['detalle']) ? $_POST['detalle'] : '';
            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
        
            // Obtiene las subtareas
            $subtareas = isset($_POST['subtarea']) ? $_POST['subtarea'] : array();
        
            if (!$this->validarTarea($titulo, $detalle, $fecha, $subtareas)) {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "Error: Datos de tarea o subtareas no válidos.";
                return;
            }
            
            // Inicializar la variable $nombre_archivo
            $nombre_archivo = null;
            // Manejo de la subida de archivos
            if (!empty($_FILES['archivo_principal']['name'])) {
                // Verifica si se ha seleccionado un archivo para cargar
                $uploadedFile = $_FILES['archivo_principal']; // Asigna el array $_FILES['archivo_principal'] a una variable
                $carpeta_destino = 'img/archivos/'; // Carpeta donde se guardarán los archivos cargados
                $ext = pathinfo($uploadedFile["name"], PATHINFO_EXTENSION); // Obtiene la extensión del archivo cargado
                // Genera un nombre único para el archivo combinando un identificador único y la extensión del archivo
                $nombre_archivo = uniqid() . "." . $ext;
                // Mueve el archivo cargado a la carpeta de destino
                if (!move_uploaded_file($uploadedFile['tmp_name'], $carpeta_destino . $nombre_archivo)) {
                    // En caso de error al mover el archivo, establece un mensaje de error y finaliza la ejecución
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error: No se pudo subir el archivo.";
                    return;
                }
            }
            
            // Comprueba que haya subtareas
            if (empty($subtareas)) {
                // Si no hay subtareas, guarda la modificación de la principal
                $resultado = $this->modelo->modificar_tarea($idTarea, $titulo, $detalle, $fecha, $subtareas, $nombre_archivo);
        
                if ($resultado) {
                    $_GET["tipomsg"] = "exito";
                    $_GET["msg"] = "Tarea modificada exitosamente.";
        
                } else {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error al modificar la tarea. Por favor, inténtelo de nuevo.";
                }
            } else {
                // Si hay subtareas, actualiza ambas
                $resultado = $this->modelo->modificar_tarea($idTarea, $titulo, $detalle, $fecha, $subtareas, $nombre_archivo);
        
                if ($resultado) {
                    $_GET["tipomsg"] = "exito";
                    $_GET["msg"] = "Tarea y subtareas modificadas exitosamente.";
        
                } else {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error al modificar la tarea y las subtareas. Por favor, inténtelo de nuevo.";
                }
            }
            return $this->listar_tarea();
        }
        
        /**
         * Método para mostrar el formulario de agregar subtareas.
         * 
         * Establece el título de la página como "Agregar subtarea" y la vista como "form_subtarea2".
         * Obtiene el ID de la tarea desde la URL.
         * Obtiene todas las tareas del usuario.
         * Busca la tarea correspondiente al ID en la lista de tareas.
         * Retorna un array con los datos necesarios para la vista.
         */
        public function vista_subtarea() {
            // Check if there's an active session and get the user ID
            session_start(); // Start the session if it's not already started
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Get the user ID from the session
        
                $this->titulo = "Agregar subtarea";
                $this->view = "form_subtarea2";
                
                // Obtener el ID de la tarea desde la URL
                $idTarea = isset($_GET['id']) ? $_GET['id'] : null;
                
                // Obtener todas las tareas del usuario
                $tareas = $this->modelo->listar_tareas($idUsuario);
                
                // Buscar la tarea correspondiente al ID en la lista de tareas
                $tituloTarea = "";
                foreach ($tareas as $tarea) {
                    if ($tarea['idTar'] == $idTarea) {
                        $tituloTarea = $tarea['titulo'];
                        break;
                    }
                }
                
                // Crear un array con los datos necesarios para la vista
                $datos = array(
                    'idTarea' => $idTarea,
                    'titulo' => $tituloTarea,
                    'idUsuario' => $idUsuario // Agregar el idUsuario a los datos
                );
                
                // Retornar los datos a la vista
                return $datos;
            } else {
                // If there's no active session, redirect to the login page
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }
        
        
        /**
         * Método para agregar una subtarea.
         * 
         * Establece la vista como "form_subtarea2".
         * Obtiene los datos de la subtarea desde el formulario POST.
         * Valida los datos y realiza la inserción en la base de datos.
         * Retorna un mensaje de éxito o error y redirige a la lista de tareas.
         */
        public function agregar_subtarea() {
            $this->view = "form_subtarea2";
            
            // Obtener los datos de la subtarea del formulario
            $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
            $detalle = isset($_POST['detalle']) ? $_POST['detalle'] : '';
            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
            $idTarea = isset($_POST['idTarea']) ? $_POST['idTarea'] : null;
            
            // Comprueba que sea un array
            if (!$this->validarTarea($titulo, $detalle, $fecha)) {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "Error: Datos de subtarea no válidos.";
                return;
            }
            
            // Insertar la subtarea en la base de datos
            $resultado = $this->modelo->agregar_subtarea($idTarea, $titulo, $detalle, $fecha);
            
            if ($resultado) {
                $_GET["tipomsg"] = "exito";
                $_GET["msg"] = "Subtarea agregada exitosamente.";
            } else {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "Error al agregar la subtarea. Por favor, inténtelo de nuevo.";
            }
            return $this->listar_tarea();
        }
        
        /************COMPLETAR SUBTAREA************/
        /**
         * Método para marcar una subtarea como completada.
         * 
         * Obtiene el ID de la subtarea desde la URL.
         * Marca la subtarea correspondiente como completada en la base de datos.
         * Establece un mensaje de éxito y redirige a la lista de subtareas completadas.
         */
        public function marcar_completada() {
            // Check if there's an active session and get the user ID
            session_start(); // Start the session if it's not already started
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Get the user ID from the session
        
                $this->authController->checkSession();
                // Obtiene el ID de la subtarea desde la URL
                $idSubtarea = isset($_GET['idSub']) ? $_GET['idSub'] : null;
                
                // Marca la subtarea como completada
                $this->modelo->marcar_subtarea_completada($idSubtarea);
        
                // Establece un mensaje de éxito
                $_GET["tipomsg"] = "exito";
                $_GET["msg"] = "Subtarea marcada como completada.";
        
                return $this->listar_completadas();
            } else {
                // If there's no active session, redirect to the login page
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }
        
        /**
         * Método para listar las subtareas completadas.
         * 
         * Establece el título de la página como "Subtareas completadas" y la vista como "menu_completa".
         * Obtiene el ID del usuario actual.
         * Retorna las subtareas completadas del usuario actual.
         */
        public function listar_completadas() {
            // Check if there's an active session and get the user ID
            session_start(); // Start the session if it's not already started
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Get the user ID from the session
        
                $this->titulo = "Subtareas completadas";
                $this->view = "menu_completa";
        
                return $this->modelo->listar_completadas($idUsuario);
            } else {
                // If there's no active session, redirect to the login page
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }
        
        
        /************BORRAR TAREAS************/
        
        /**
         * Método para mostrar el formulario de confirmación de borrado de tarea.
         * 
         * Obtiene el ID de la tarea desde la URL.
         * Establece la vista como "form_eliminar".
         * Retorna el ID de la tarea para su posterior procesamiento.
         */
        public function borrado() {
            // Obtiene el id de la tarea
            $id = $_GET['id'] ?? '';
            $this->view = "form_eliminar";

            return isset($_GET['id']) ? $_GET['id'] : null;
        }
        
        /**
         * Método para eliminar una tarea.
         * 
         * Obtiene el ID de la tarea desde la URL.
         * Verifica si la tarea existe en la base de datos.
         * Si la tarea existe, la elimina y establece un mensaje de éxito.
         * Si la tarea no existe, establece un mensaje de error.
         * Retorna la lista actualizada de tareas.
         */
        public function eliminar_tarea() {
            $id = $_GET['id'] ?? '';
            if (!$this->modelo->comprobarExisteTarea($id)) {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "La tarea seleccionada no existe.";
                return $this->listar_tarea();
            }

            $this->modelo->borrar_fila($id);
            $_GET["tipomsg"] = "exito";
            $_GET["msg"] = "Tarea eliminada con éxito.";
            return $this->listar_tarea();
        }

        public function listar_sugerencias() {
            // Check if there's an active session and get the user ID
            session_start(); // Start the session if it's not already started
            if (isset($_SESSION['user_id'])) {
                // If the user is authenticated, proceed to display the suggestions page
                $this->titulo = "(-Sugerencias-)";
                $this->view = "sugerencias";
            } else {
                // If there's no active session, redirect to the login page
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }        

        /************EXPORTAR PDF************/

        /**
         * Método para exportar un listado de tareas y subtareas a un archivo PDF.
         * 
         * Incluye la librería TCPDF necesaria.
         * Crea un nuevo documento PDF.
         * Establece la información del documento y el encabezado.
         * Agrega una página al PDF.
         * Añade el contenido HTML al PDF, incluyendo un listado de tareas y subtareas.
         * Escribe el contenido HTML en el PDF.
         * Muestra el PDF en el navegador para su descarga.
         */
        public function exportar_pdf() {
            // Check if there's an active session and get the user ID
            session_start(); // Start the session if it's not already started
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Get the user ID from the session
        
                // Incluye la librería de TCPDF necesaria
                require_once __DIR__.'/../TCPDF-main/tcpdf.php';
        
                // Crea un nuevo documento PDF
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
                // Información del documento
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Manuel Nieto Benítez');
                $pdf->SetTitle('TASKS - PDF');
                $pdf->SetSubject('Descarga de PDF');
                $pdf->SetKeywords('Tareas, Subtareas, PDF');
        
                // Establece el encabezado
                $pdf->setHeaderData('', PDF_HEADER_LOGO_WIDTH, 'TASKS');
                // Añade una página
                $pdf->AddPage();
        
                // Añade el contenido al PDF
                $html = '<style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 12px;
                    border: 1px solid #ccc;
                    text-align: left;
                    font-size: 14px;
                }
                th {
                    background-color: #f2f2f2;
                    color: #333;
                }
                tr:nth-child(odd) {
                    background-color: #f9f9f9;
                }
                img {
                    max-width: 100px;
                    height: auto;
                }
                .task-spacing {
                    height: 20px; /* Adjust as needed */
                }
                </style>';
                $html .= '<h1 style="text-align: center;">Listado de tareas y subtareas</h1>';
                $html .= '<table>';
                $html .= '<tr><th>TÍTULO</th><th>DETALLE</th><th>FECHA</th></tr>';
        
                // Loop through tasks and subtasks
                foreach ($this->modelo->listar_tareas($idUsuario) as $tarea) {
                    $html .= '<tr>';
                    $html .= '<td style="font-weight: bold;">' . htmlspecialchars($tarea['titulo'], ENT_QUOTES) . '</td>';
                    $html .= '<td>' . htmlspecialchars($tarea['detalle'], ENT_QUOTES) . '</td>';
                    $html .= '<td>' . htmlspecialchars($tarea['fecha'], ENT_QUOTES) . '</td>';
                    $html .= '</tr>';
        
                    if (isset($tarea['subtareas']) && !empty($tarea['subtareas'])) {
                        foreach ($tarea['subtareas'] as $subtarea) {
                            $html .= '<tr>';
                            $html .= '<td style="font-style: italic;">' . htmlspecialchars($subtarea['titulo'], ENT_QUOTES) . '</td>';
                            $html .= '<td>' . htmlspecialchars($subtarea['detalle'], ENT_QUOTES) . '</td>';
                            $html .= '<td>' . htmlspecialchars($subtarea['fecha'], ENT_QUOTES) . '</td>';
                            $html .= '</tr>';
                        }
                    }
                    // Add spacing between tasks
                    $html .= '<tr class="task-spacing"><td colspan="4"></td></tr>';
                }
                $html .= '</table>';
        
                // Escribe el contenido HTML en el PDF
                $pdf->writeHTML($html, true, false, true, false, '');
        
                // Salida del PDF como contenido en línea
                $pdf->Output('tareas.pdf', 'I');
            } else {
                // If there's no active session, redirect to the login page
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }
        


        private function validarTarea($titulo, $detalle, $subtareas) {
            // Valida los campos de los formularios 
            if (empty($titulo)) {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "Debes rellenar el título.";
                return false;
            }
        
            if (is_numeric(substr($titulo, 0, 1))) {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "El título no puede comenzar por un número.";
                return false;
            }
        
            if (strlen($titulo) > 50 || strlen($detalle) > 2000) {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "Uno de los campos excede el límite de caracteres.";
                return false;
            }
        
            // Comprueba que el campo título solo contenga letras, números, espacios y una serie de carácteres concretos
            if (!preg_match('/^[a-zA-ZÑñÁáÉéÍíÓóÚúÜü][a-zA-Z0-9ÑñÁáÉéÍíÓóÚúÜü ]{0,49}$/', $titulo)) {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "El título no puede contener carácteres especiales.";
                return false;
            }
        /*
            // Validar subtareas
            foreach ($subtareas as $subtarea) {
                if (empty($subtarea)) {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Debes completar todas las subtareas.";
                    return false;
                }
        
                if (is_numeric(substr($subtarea, 0, 1))) {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "El título de una subtarea no puede comenzar por un número.";
                    return false;
                }
        
                if (strlen($subtarea) > 50) {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Uno de los títulos de subtarea excede el límite de caracteres.";
                    return false;
                }
        
                // Comprueba que el campo título solo contenga letras, números, espacios y una serie de carácteres concretos
                if (!preg_match('/^[a-zA-ZÑñÁáÉéÍíÓóÚúÜü][a-zA-Z0-9ÑñÁáÉéÍíÓóÚúÜü ]{0,49}$/', $subtarea)) {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "El título de una subtarea no puede contener carácteres especiales.";
                    return false;
                }
            }*/
        
            // Si todas las validaciones pasan, devuelve verdadero
            return true;
        }

    }

?>