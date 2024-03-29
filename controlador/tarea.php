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
            $this->authController->checkSession();
            $this->titulo = "Menú TASKS";
            $this->view = "menu_tareas";
        }

        public function form_tarea() {
            $this->authController->checkSession();
            $this->titulo = "Añadir tarea";
            $this->view = "form_tarea";
        }
        
        public function listar_tarea() {
            $this->authController->checkSession();
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id'];
                // Buscar la tarea con la fecha de entrega más cercana
                $tareaCercana = $this->modelo->obtenerTareaFechaProxima($idUsuario);
                // Guardar la ID y la fecha de la tarea más cercana en una cookie
                if ($tareaCercana) {
                    setcookie('tarea_cercana_id', $tareaCercana['idTar'], time() + (86400 * 30), "/"); // Cookie válida por 30 días
                    setcookie('tarea_cercana_fecha', $tareaCercana['fecha'], time() + (86400 * 30), "/"); // Cookie válida por 30 días
                }
                
                $this->titulo = "Listar tareas";
                $this->view = "menu_listar";
                return $this->modelo->listar_tareas($idUsuario);
            } else {
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }

        /**
         * Método para listar las tareas.
         */
        public function ver_tarea() {
            $this->authController->checkSession();
            // Guardar la ID de la tarea consultada en una cookie, última tarea visitada
            $idTarea = isset($_GET['id']) ? $_GET['id'] : null;
            setcookie('ultima_tarea_consultada', $idTarea, time() + (86400 * 30), "/"); // Cookie válida por 30 días
            
            $this->titulo = "Listar tareas";
            $this->view = "ver_tarea";
            
            return $this->modelo->obtener_tarea_por_id($idTarea);
        }
        
        public function ver_sugerencias() {
            $this->authController->checkSession();

            $datos_tarea = array();
            // Verificar si la cookie ultima_tarea_consultada está presente
            if (isset($_COOKIE['ultima_tarea_consultada'])) {
                // Obtener el ID de la última tarea consultada de la cookie
                $idTarea = $_COOKIE['ultima_tarea_consultada'];
                $tarea = $this->modelo->obtener_tarea_por_id($idTarea);
                
                if ($tarea) {
                    // datos de la tarea
                    $datos_tarea = array(
                        "id" => $tarea['idTar'],
                        "titulo" => $tarea['titulo'],
                        "detalle" => $tarea['detalle'],
                        "fecha" => $tarea['fecha']
                    );
                }
            }

            $this->titulo = "Listar tareas";
            $this->view = "ver_sugerencias";
        
            return $datos_tarea;
        }
        
        /************GUARDAR TAREAS Y SUBTAREAS************/
        /**
         * Método para guardar una tarea principal.
         */
        public function guardar_tarea() {
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
            );
            
            return $datos;
        }
        
        /**
         * Método para guardar subtareas de una tarea principal.
         */
        public function guardar_subtareas() {
            $this->authController->checkSession();
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Obtiene el id de la sesión
                
                // Obtener los datos de la tarea del formulario
                $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
                $detalle = isset($_POST['detalle']) ? $_POST['detalle'] : '';
                $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
                
                //Comprueba que sea un array
                $subtareas = isset($_POST['subtarea']) ? $_POST['subtarea'] : array();
                
                //Inicializa la variable
                $nombre_archivo = null;

                // Manejo de la subida de archivos
                if (!empty($_FILES['archivo_principal']['name'])) {
                    // Verifica si se ha seleccionado un archivo para cargar
                    $uploadedFile = $_FILES['archivo_principal']; // Asigna el array $_FILES['archivo_principal'] a una variable
                    $carpeta_destino = 'uploads/'; // Carpeta donde se guardarán los archivos cargados
                    $nombre_archivo = $uploadedFile["name"]; // Utiliza el nombre original del archivo como nombre de archivo

                    // Validar archivo adjunto
                    $error_archivo = $this->validarArchivoAdjunto($uploadedFile);
                    if ($error_archivo !== true) {
                        $_GET["tipomsg"] = "error";
                        $_GET["msg"] = "Error: " . $error_archivo;
                        return;
                    }
                    // Mueve el archivo cargado a la carpeta de destino
                    if (!move_uploaded_file($uploadedFile['tmp_name'], $carpeta_destino . $nombre_archivo)) {
                        // En caso de error al mover el archivo, establece un mensaje de error y finaliza la ejecución
                        $_GET["tipomsg"] = "error";
                        $_GET["msg"] = "Error: No se pudo subir el archivo.";
                        return;
                    }
                }

                // Validar tarea y subtareas
                $error = $this->validarDatos($titulo, $detalle, $subtareas);
                if ($error !== true) {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error: " . $error;
                    return;
                }

                // Insertar la tarea en la base de datos            
                $idTar = $this->modelo->insertar_tarea($titulo, $detalle, $fecha, $subtareas, $nombre_archivo, $idUsuario);
                // Verificar si la tarea se insertó correctamente
                if ($idTar) {
                    $_GET["tipomsg"] = "exito";
                    $_GET["msg"] = "Tarea guardada exitosamente.";
                } else {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error al guardar la tarea. Por favor, inténtelo de nuevo.";
                }
            } else {
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
            // Redirigir a la lista de tareas
            return $this->listar_tarea();
        }
        
        /********PROCESO MODIFICACIÓN********/
        /**
        * Método para modificar una tarea existente.
        */
        public function modificar_tarea() {
            $this->authController->checkSession();
            $this->titulo = "Modificar tarea";
            $this->view = "form_modificar";
            
            // Obtiene el id
            $idTarea = isset($_GET['id']) ? $_GET['id'] : null;
            
            return $this->modelo->listar_mod($idTarea);
        }
        
        /**
         * Método para guardar la modificación de una tarea.
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

            // Inicializar la variable $nombre_archivo
            $nombre_archivo = null;
            // Manejo de la subida de archivos
            if (!empty($_FILES['archivo_principal']['name'])) {
                // Verifica si se ha seleccionado un archivo para cargar
                $uploadedFile = $_FILES['archivo_principal']; // Asigna el array $_FILES['archivo_principal'] a una variable
                $carpeta_destino = 'uploads/'; // Carpeta donde se guardarán los archivos cargados
                $nombre_archivo = $uploadedFile["name"]; // Utiliza el nombre original del archivo como nombre de archivo

                // Validar archivo adjunto
                $error_archivo = $this->validarArchivoAdjunto($uploadedFile);
                if ($error_archivo !== true) {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error: " . $error_archivo;
                    return $this->modificar_tarea();
                }

                // Mueve el archivo cargado a la carpeta de destino
                if (!move_uploaded_file($uploadedFile['tmp_name'], $carpeta_destino . $nombre_archivo)) {
                    // En caso de error al mover el archivo, establece un mensaje de error y finaliza la ejecución
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error: No se pudo subir el archivo.";
                    return;
                }
            }

            // Comprueba si hay un archivo adjunto nuevo
            if (!empty($nombre_archivo)) {
                // Obtiene el nombre del archivo anterior
                $nombre_archivo_anterior = $this->modelo->obtenerNombreArchivo($idTarea);
                // Elimina el archivo anterior si existe
                if (!empty($nombre_archivo_anterior)) {
                    $carpeta_destino = 'uploads/';
                    $ruta_archivo_anterior = $carpeta_destino . $nombre_archivo_anterior;
                    if (file_exists($ruta_archivo_anterior)) {
                        unlink($ruta_archivo_anterior);
                    }
                }
            }

            // Validar tarea y subtareas
            $error = $this->validarDatos($titulo, $detalle, $subtareas, $nombre_archivo);
            if ($error !== true) {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "Error: " . $error;
                return $this->modificar_tarea();
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
         */
        public function vista_subtarea($idTarea = null, $titulo = "") {
            $this->authController->checkSession();
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id'];
        
                $this->titulo = "Agregar subtarea";
                $this->view = "form_subtarea2";
                
                if ($idTarea === null && isset($_GET['id'])) {
                    $idTarea = $_GET['id'];
                }

                if ($titulo == "") {
                    // Obtener todas las tareas del usuario
                    $tareas = $this->modelo->listar_tareas($idUsuario);
                
                    // Buscar la tarea correspondiente al ID en la lista de tareas
                    foreach ($tareas as $tarea) {
                        if ($tarea['idTar'] == $idTarea) {
                            $titulo = $tarea['titulo'];
                            break;
                        }
                    }
                }
                
                // Crear un array con los datos necesarios para la vista
                $datos = array(
                    'idTarea' => $idTarea,
                    'titulo' => $titulo,
                    'idUsuario' => $idUsuario // Agregar el idUsuario a los datos
                );
                
                // Retornar los datos a la vista
                return $datos;
            } else {
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }
        
        
        /**
         * Método para agregar una subtarea.
         */
        public function agregar_subtarea() {
            $this->view = "form_subtarea2";
            
            // Obtener los datos de la subtarea del formulario
            $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
            $detalle = isset($_POST['detalle']) ? $_POST['detalle'] : '';
            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
            $idTarea = isset($_POST['idTarea']) ? $_POST['idTarea'] : null;
        
            // Verificar si $_POST['subtarea'] es un array
            if (isset($_POST['subtarea']) && is_array($_POST['subtarea'])) {
                $subtareas = $_POST['subtarea'];
            } else {
                // Si no es un array, inicializar $subtareas como un array vacío
                $subtareas = array();
            }
        
            // Validar tarea y subtareas
            $error = $this->validarDatos($titulo, $detalle, $subtareas);
            if ($error !== true) {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "Error: " . $error;
                $titulo = $this->modelo->obtenerTituloTarea($idTarea);
                return $this->vista_subtarea($idTarea, $titulo);
            }
            
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
         */
        public function marcar_completada() {
            // Obtiene el ID de la subtarea desde la URL
            $idSubtarea = isset($_GET['idSub']) ? $_GET['idSub'] : null;
            // Marca la subtarea como completada
            $this->modelo->marcar_subtarea_completada($idSubtarea);

            $_GET["tipomsg"] = "exito";
            $_GET["msg"] = "Subtarea marcada como completada.";
            return $this->listar_completadas();
        }
        
        /**
         * Método para listar las subtareas completadas.
         */
        public function listar_completadas() {
            $this->authController->checkSession();
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id'];
        
                $this->titulo = "Subtareas completadas";
                $this->view = "menu_completa";
        
                return $this->modelo->listar_completadas($idUsuario);
            } else {
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }
        
        /************BORRAR TAREAS************/
        /**
         * Método para mostrar el formulario de confirmación de borrado de tarea.
         */
        public function borrado() {
            $this->authController->checkSession();
            $id = $_GET['id'] ?? '';
            $this->view = "form_eliminar";

            return isset($_GET['id']) ? $_GET['id'] : null;
        }
        
        /**
         * Método para eliminar una tarea.
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


        /************VALIDACIONES************/      
        private function validarDatos($titulo, $detalle, $subtareas = null) {
            if (empty($titulo)) {
                return "Debes rellenar el título.";
            }
            if (strlen($titulo) > 50 || strlen($detalle) > 255) {
                return "Uno de los campos de la tarea excede el límite de caracteres.";
            }
            
            // Validar subtareas
            foreach ($subtareas as $subtarea) {
                // Validar título de subtarea
                if (strlen($subtarea['titulo']) > 50 || strlen($subtarea['detalle']) > 255) {
                    return "Uno de los campos de la subtarea excede el límite de caracteres.";
                }
            }
            return true;
        }     
        
        /**
         * Método para validar el archivo adjunto.
         */
        private function validarArchivoAdjunto($uploadedFile) {
            // Verificar si se ha seleccionado un archivo para cargar
            if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
                return "Error al subir el archivo adjunto.";
            }

            // Obtener la extensión del archivo
            $ext = pathinfo($uploadedFile["name"], PATHINFO_EXTENSION);
            $extensiones_permitidas = array('jpg', 'png', 'jpeg', 'gif', 'pdf');

            // Verificar si la extensión del archivo es válida
            if (!in_array(strtolower($ext), $extensiones_permitidas)) {
                return "El archivo adjunto debe tener una de las siguientes extensiones: JPG, PNG, JPEG, GIF, PDF.";
            }

            // Verificar el tamaño del archivo (2 MB máximo)
            if ($uploadedFile['size'] > 2 * 1024 * 1024) {
                return "El archivo adjunto no puede pesar más de 2 MB.";
            }

            // Si pasa todas las validaciones, devolver true
            return true;
        }
        
        /************EXPORTAR PDF************/
        /**
         * Método para exportar un listado de tareas y subtareas a un archivo PDF.
         */
        public function exportar_pdf() {
            session_start();
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id']; // Define $idUsuario dentro del bloque if
                
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
                $pdf->setHeaderData('', PDF_HEADER_LOGO_WIDTH, 'LISTADO TASKS');
                // Añade una página
                $pdf->AddPage();

                // Añade los estilos a la página
                $pdf->writeHTML(true, false, true, false, '');
                
                // Mostrar la información
                foreach ($this->modelo->listar_tareas($idUsuario) as $tarea) {
                    // Contenido HTML para una tabla de tarea
                    $html = '<h1 style="text-align: center;">Tarea: ' . htmlspecialchars($tarea['titulo'], ENT_QUOTES) . '</h1>';
                    $html .= '<table style="border-collapse: collapse; width: 100%;">';
                    $html .= '<tr><th style="border: 1px solid #ccc; background-color: #646fa5; color: #fff; padding: 12px; text-align: left;">TÍTULO</th><th style="border: 1px solid #ccc; background-color: #646fa5; color: #fff; padding: 12px; text-align: left;">DETALLE</th><th style="border: 1px solid #ccc; background-color: #646fa5; color: #fff; padding: 12px; text-align: left;">FECHA</th></tr>';
                    $html .= '<tr>';
                    $html .= '<td style="border: 1px solid #ccc; font-weight: bold; padding: 12px;">' . htmlspecialchars($tarea['titulo'], ENT_QUOTES) . '</td>';
                    $html .= '<td style="border: 1px solid #ccc; padding: 12px;">' . htmlspecialchars($tarea['detalle'], ENT_QUOTES) . '</td>';
                    $html .= '<td style="border: 1px solid #ccc; padding: 12px;">' . htmlspecialchars($tarea['fecha'], ENT_QUOTES) . '</td>';
                    $html .= '</tr>';
                    
                    // Mostrar las subtareas si existen
                    if (isset($tarea['subtareas']) && !empty($tarea['subtareas'])) {
                        // Agregar fila de subtareas
                        $html .= '<tr><td colspan="3" style="border: 1px solid #ccc; background-color: #646fa5; color: #fff; padding: 12px; text-align: center;">Subtareas</td></tr>';
                        
                        // Mostrar cada subtarea
                        foreach ($tarea['subtareas'] as $subtarea) {
                            $html .= '<tr>';
                            $html .= '<td style="border: 1px solid #ccc; font-style: italic; padding: 12px;">' . htmlspecialchars($subtarea['titulo'], ENT_QUOTES) . '</td>';
                            $html .= '<td style="border: 1px solid #ccc; padding: 12px;">' . htmlspecialchars($subtarea['detalle'], ENT_QUOTES) . '</td>';
                            $html .= '<td style="border: 1px solid #ccc; padding: 12px;">' . htmlspecialchars($subtarea['fecha'], ENT_QUOTES) . '</td>';
                            $html .= '</tr>';
                        }
                    }
                    $html .= '</table>';
                    // Escribe el contenido HTML de la tabla de tarea en el PDF
                    $pdf->writeHTML($html, true, false, true, false, '');
                }

                // Cierre y salida del PDF
                $pdf->Output('tareas.pdf', 'I');
            } else {
                header("Location: index.php?controller=sesion&action=mostrar_inicio_sesion");
                exit();
            }
        }    
    }
?>