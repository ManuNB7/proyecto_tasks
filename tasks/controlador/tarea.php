<?php

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
        /**
         * Constructor de la clase.
         * 
         * Inicializa las propiedades de la clase y crea una instancia del modelo tareaModel.
         */
        public function __construct() {
            $this->modelo = new tareaModel();
            $this->view = "form_tarea";
            $this->titulo = "Añadir tarea";
        }

        public function listar_tarea() {
            $this->titulo = "Listar tareas";
            $this->view = "menu_listar";
            
            return $this->modelo->listar_tareas();
        }

        /************GUARDAR TAREAS Y SUBTAREAS************/
        public function guardar_tarea() {
            $this->view = "form_subtarea";
            // Obtener los datos de la tarea principal
            $titulo = $_POST['titulo'];
            
            // Obtener número de subtareas
            $num_subtareas = $_POST['num_subtareas'];
            
            $datos = $array_datos = array(
                "titulo" => $titulo,
                "num_subtareas" => $num_subtareas
            );
            
            return $datos;
        }
        
        public function guardar_subtareas() {
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
            // File upload handling for the main task
            if (isset($_FILES['archivo_principal']) && $_FILES['archivo_principal']['error'] === UPLOAD_ERR_OK) {
                $uploadedFile = $_FILES['archivo_principal'];
                $carpeta_destino = 'img/archivos/';
                $nombre_archivo = $carpeta_destino . basename($uploadedFile['name']);
                
                // Check if the file already exists in the destination directory
                if (file_exists($nombre_archivo)) {
                    // If the file already exists, generate a unique file name
                    $nombre_archivo = $carpeta_destino . uniqid() . '_' . basename($uploadedFile['name']);
                }
                
                // Move uploaded file to destination directory
                if (move_uploaded_file($uploadedFile['tmp_name'], $nombre_archivo)) {
                    // File uploaded successfully
                } else {
                    // Error handling if file upload fails
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error: No se pudo subir el archivo.";
                    return;
                }
            } else {
                // No file uploaded, check if a previous file exists
                if (isset($_POST['nombre_archivo_anterior'])) {
                    $nombre_archivo = $_POST['nombre_archivo_anterior'];
                } else {
                    $nombre_archivo = null;
                }
            }


        
            // Insert the task data into the database, along with the file name
            $idTar = $this->modelo->insertar_tarea($titulo, $detalle, $fecha, $subtareas, $nombre_archivo);
            
            if ($idTar) {
                $_GET["tipomsg"] = "exito";
                $_GET["msg"] = "Tarea guardada exitosamente.";
            } else {
                $_GET["tipomsg"] = "error";
                $_GET["msg"] = "Error al guardar la tarea. Por favor, inténtelo de nuevo.";
            }
        }
        
        
        
        
        
        
        /********PROCESO MODIFICACIÓN********/
        public function modificar_tarea() {
            $this->titulo = "Modificar tarea";
            $this->view = "form_modificar";
            
            // Obtiene el id
            $idTarea = isset($_GET['id']) ? $_GET['id'] : null;
            
            return $this->modelo->listar_mod($idTarea);
        }

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
            // File upload handling for the main task
            if (isset($_FILES['archivo_principal']) && $_FILES['archivo_principal']['error'] === UPLOAD_ERR_OK) {
                $uploadedFile = $_FILES['archivo_principal'];
                $carpeta_destino = 'img/archivos/';
                $nombre_archivo = $carpeta_destino . basename($uploadedFile['name']);
                
                // Check if the file already exists in the destination directory
                if (file_exists($nombre_archivo)) {
                    // If the file already exists, generate a unique file name
                    $nombre_archivo = $carpeta_destino . uniqid() . '_' . basename($uploadedFile['name']);
                }
                
                // Move uploaded file to destination directory
                if (move_uploaded_file($uploadedFile['tmp_name'], $nombre_archivo)) {
                    // File uploaded successfully
                } else {
                    // Error handling if file upload fails
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error: No se pudo subir el archivo.";
                    return;
                }
            } else {
                // No file uploaded, check if a previous file exists
                if (isset($_POST['nombre_archivo_anterior'])) {
                    $nombre_archivo = $_POST['nombre_archivo_anterior'];
                } else {
                    $nombre_archivo = null;
                }
            }
        
            // Comprueba que haya subtareas
            if (empty($subtareas)) {
                // Si no hay subtareas, guarda la modificación de la principal
                $resultado = $this->modelo->modificar_tarea($idTarea, $titulo, $detalle, $fecha, array(), $nombre_archivo);
        
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
            return $this->modelo->listar_tareas();
        }
        
        
        public function vista_subtarea(){
            $this->titulo = "Agregar subtarea";
            $this->view = "form_subtarea2";
            // Devuelve el id de la tarea
            return isset($_GET['id']) ? $_GET['id'] : null;
        }

        public function agregar_subtarea() {
            $this->titulo = "Agregar subtarea";
            $this->view = "menu_listar";
            
            // Comprueba si se envió y obtiene los datos
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Retrieve form data
                $idTarea = $_POST['idTarea']; // Obtiene el id de la tarea
                $titulo = $_POST['titulo'];
                $detalle = $_POST['detalle'];
                $fecha = $_POST['fecha'];

                $result = $this->modelo->agregar_subtarea($idTarea, $titulo, $detalle, $fecha);
                
                if ($result) {
                    $_GET["tipomsg"] = "exito";
                    $_GET["msg"] = "Tarea y subtareas modificadas exitosamente.";
                } else {
                    $_GET["tipomsg"] = "error";
                    $_GET["msg"] = "Error al modificar la tarea y las subtareas. Por favor, inténtelo de nuevo.";
                }
            }
            
            return $this->modelo->listar_tareas();
        }
        
        
        /************COMPLETAR SUBTAREA************/
        public function marcar_completada() {
            $this->titulo = "Subtareas completadas";
            $this->view = "menu_listar";
            // Obtiene le id de la subtarea
            $idSubtarea = isset($_GET['idSub']) ? $_GET['idSub'] : null;

            // Marca como completa la subtarea
            $this->modelo->marcar_subtarea_completada($idSubtarea);
            
            return $this->modelo->listar_tareas();
        }
        
        public function listar_completadas() {
            $this->titulo = "Subtareas completadas";
            $this->view = "menu_completa";

            return $this->modelo->listar_completadas();
        }
        
        /************BORRAR TAREAS************/
        
        public function borrado() {
            // Obtiene el id de la tarea
            $id = $_GET['id'] ?? '';
            $this->view = "form_eliminar";

            return isset($_GET['id']) ? $_GET['id'] : null;
        }
        
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


        /************SUGERENCIAS************/
        public function listar_sugerencias() {
            $this->titulo = "(-Sugerencias-)";
            $this->view = "sugerencias";
            
        }

        /************VALIDACIONES************/
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
        
            /*// Add validation for subtasks if needed
            foreach ($subtareas as $subtarea) {
                // Perform validation for each subtask
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
                // You can add more specific validations for each subtask here if needed
            }*/
            
        
            // If all validations pass, return true
            return true;
        }
        
        
    }

?>