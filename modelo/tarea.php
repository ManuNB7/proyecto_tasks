<?php

    require_once __DIR__.'/conexion.php';
    
    /**
     * Clase tareaModel
     * 
     * Modelo para la gestión de tareas y subtareas.
     */
    class tareaModel extends Conexion{
        
        public $error;
        /**
         * Constructor de la clase.
         * 
         * Inicializa las propiedades de la clase y llama al constructor de la clase padre (Conexion).
         */
        public function __construct() {
            parent::__construct();
        }

        /**
         * Método para listar todas las tareas de un usuario.
         */
        public function listar_tareas($idUsuario){
            $sql = "SELECT * FROM tareas WHERE idUsuario = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $tareas = $resultado->fetch_all(MYSQLI_ASSOC);

            foreach ($tareas as &$tarea) {
                $idTarea = $tarea['idTar'];
                $subtareas = $this->obtener_subtareas($idTarea);
                $tarea['subtareas'] = $subtareas;
            }
            
            return $tareas;
        }        
        
        /**
         * Método para obtener una tarea por su ID.
         */
        public function obtener_tarea_por_id($idTarea) {
            $sql = "SELECT * FROM tareas WHERE idTar = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idTarea);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $tarea = $resultado->fetch_assoc();
            
            // Obtener subtareas de la tarea
            if ($tarea) {
                $tarea['subtareas'] = $this->obtener_subtareas($idTarea);
            }
            
            return $tarea;
        }
        
        /**
         * Método para obtener todas las subtareas de una tarea.
         *
         */
        public function obtener_subtareas($idTarea){
            $sql = "SELECT * FROM subtareas WHERE idTar = ? AND completada = 0";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idTarea);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $subtareas = $resultado->fetch_all(MYSQLI_ASSOC);
            
            return $subtareas;
        }      

        /********PROCESO INSERCIÓN DE TAREAS Y SUBTAREAS********/

        /**
         * Método para insertar una nueva tarea y sus subtareas asociadas en la base de datos.
         * 
         * Prepara y ejecuta una consulta SQL para insertar la tarea en la tabla 'tareas'.
         * Obtiene el ID de la tarea recién insertada.
         * Si hay subtareas proporcionadas, prepara y ejecuta consultas SQL para insertar cada subtarea en la tabla 'subtareas'.
         *
         */
        public function insertar_tarea($titulo, $detalle, $fecha, $subtareas, $nombre_archivo, $idUsuario) {
            try {
                $this->conexion->autocommit(false);
                $this->conexion->begin_transaction();
                
                // Verificar si la fecha está vacía y convertirla en NULL si es necesario
                $detalle_insertar = empty($detalle) ? null : $detalle;
                $fecha_insertar = empty($fecha) ? null : $fecha;
                $file_insertar = ($nombre_archivo !== null) ? $nombre_archivo : null;
        
                // Consulta SQL para insertar en la tabla 'tareas'
                if ($file_insertar !== null) {
                    $sql = "INSERT INTO tareas(titulo, detalle, fecha, archivo, idUsuario) VALUES (?,?,?,?,?)"; 
                } else {
                    $sql = "INSERT INTO tareas(titulo, detalle, fecha, idUsuario) VALUES (?,?,?,?)";
                }
        
                $stmt = $this->conexion->prepare($sql);
                if ($file_insertar !== null) {
                    $stmt->bind_param("ssssi", $titulo, $detalle_insertar, $fecha_insertar, $file_insertar, $idUsuario);
                } else {
                    $stmt->bind_param("sssi", $titulo, $detalle_insertar, $fecha_insertar, $idUsuario);
                }
                $stmt->execute();
                
                // Obtener el ID de la tarea recién insertada
                $idTar = $stmt->insert_id;
                $stmt->close();
                
                // Si hay subtareas, insertarlas
                if (!empty($subtareas) && is_array($subtareas)) {
                    // Consulta SQL para insertar en la tabla 'subtareas'
                    $sql = "INSERT INTO subtareas(titulo, detalle, fecha, idTar) VALUES (?,?,?,?)";
                    $stmt = $this->conexion->prepare($sql);
                    
                    // Itera sobre cada subtarea en el arreglo de subtareas
                    foreach ($subtareas as $subtarea) {
                        // Verifica si la subtarea actual tiene al menos un campo rellenado
                        if (!empty($subtarea['titulo']) || !empty($subtarea['detalle']) || !empty($subtarea['fecha'])) {
                            // Asigna los valores correspondientes o NULL si los campos no están rellenados
                            $titulo_sub = $subtarea['titulo'];
                            $detalle_sub = empty($subtarea['detalle']) ? null : $subtarea['detalle'];
                            $fecha_sub = empty($subtarea['fecha']) ? null : $subtarea['fecha'];
                            
                            $stmt->bind_param("sssi", $titulo_sub, $detalle_sub, $fecha_sub, $idTar);
                            $stmt->execute();
                        }
                    }
                    $stmt->close();
                }
        
                $this->conexion->commit();
                return $idTar;
            } catch (mysqli_sql_exception $e) {
                $this->conexion->rollback();
                $this->error = "Error ".$e->getCode().": Contacte con el administrador.";
                return false;
            }
        }
        
        
        

        /********PROCESO MODIFICACIÓN********/

        /**
         * Método para obtener los detalles de una tarea específica para su modificación.
         */
        public function listar_mod($idTarea) {
            $sql = "SELECT * FROM tareas WHERE idTar = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idTarea);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $tarea = $resultado->fetch_assoc();
            
            if ($tarea) {
                $tarea['subtareas'] = $this->obtener_subtareas($idTarea);
            }
            
            return $tarea;
        }

        /**
         * Método para modificar una tarea y sus subtareas.
         */
        
        public function modificar_tarea($idTarea, $titulo, $detalle, $fecha, $subtareas, $nombre_archivo) {
            if ($titulo === null || $detalle === null || $fecha === null && empty($subtareas)) {
                return true;
            }
        
            try {
                $this->conexion->autocommit(false);
                $this->conexion->begin_transaction();
        
                $detalle_insertar = empty($detalle) ? null : $detalle;
                $fecha_insertar = empty($fecha) ? null : $fecha;
                
        
                // Verifica si se proporcionó un nuevo archivo, de lo contrario, mantiene el archivo existente
                if ($nombre_archivo === null) {
                    $sql_update_tarea = "UPDATE tareas SET titulo = ?, detalle = ?, fecha = ? WHERE idTar = ?";
                    $stmt = $this->conexion->prepare($sql_update_tarea);
                    $stmt->bind_param("sssi", $titulo, $detalle_insertar, $fecha_insertar, $idTarea);
                } else {
                    $sql_update_tarea = "UPDATE tareas SET titulo = ?, detalle = ?, fecha = ?, archivo = ? WHERE idTar = ?";
                    $stmt = $this->conexion->prepare($sql_update_tarea);
                    $stmt->bind_param("ssssi", $titulo, $detalle_insertar, $fecha_insertar, $nombre_archivo, $idTarea);
                }
                
                $stmt->execute();
        
                if (!empty($subtareas) && is_array($subtareas)) {
                    foreach ($subtareas as $subtarea) {
                        if (is_array($subtarea) && (!empty($subtarea['titulo']) || !empty($subtarea['detalle']) || !empty($subtarea['fecha']))) {
                            $idSub = $subtarea['idSub'] ?? null;
                            $titulo_sub = $subtarea['titulo'] ?? '';
                            $detalle_sub = empty($subtarea['detalle']) ? null : $subtarea['detalle'];
                            $fecha_sub = empty($subtarea['fecha']) ? null : $subtarea['fecha'];
        
                            if ($idSub) {
                                $sql = "UPDATE subtareas SET titulo = ?, detalle = ?, fecha = ? WHERE idSub = ?";
                                $stmt = $this->conexion->prepare($sql);
                                $stmt->bind_param("sssi", $titulo_sub, $detalle_sub, $fecha_sub, $idSub);
                                $stmt->execute();
                            }
                        }
                    }
                }
        
                $this->conexion->commit();
                return true;
            } catch (mysqli_sql_exception $e) {
                $this->conexion->rollback();
                $this->error = "Error " . $e->getCode() . ": Contacte con el administrador.";
                return false;
            }
        }

        /*Obtner titulo de la tarea de nuevo si falla la validacion al agregar subtareas*/
        
        public function obtenerTituloTarea($idTarea) {
            $sql = "SELECT titulo FROM subtareas WHERE idTar = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idTarea);
            $stmt->execute();
            $resultado = $stmt->get_result();
        
            if ($resultado->num_rows == 1) {
                $fila = $resultado->fetch_assoc();
                return $fila['titulo'];
            } else {
                return null;
            }
        }

        /**
         * Método para agregar una subtarea a una tarea existente.
         */
        public function agregar_subtarea($idTarea, $titulo, $detalle, $fecha) {
            try {
                $this->conexion->autocommit(false);
                $this->conexion->begin_transaction();

                $detalle_insertar = empty($detalle) ? null : $detalle;
                $fecha_insertar = empty($fecha) ? null : $fecha;
                
                $sql = "INSERT INTO subtareas(titulo, detalle, fecha, idTar) VALUES (?, ?, ?, ?)";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("sssi", $titulo, $detalle_insertar, $fecha_insertar, $idTarea);
                $stmt->execute();

                $this->conexion->commit();

                return true;
            }   catch (mysqli_sql_exception $e) {
                $this->conexion->rollback();
                $this->error = "Error ".$e->getCode().": Contacte con el administrador.";
                return false;
            }
        }

        /********PROCESO COMPLETAR SUBTAREAS********/
        /**
         * Método para marcar una subtarea como completada en la base de datos.
         */
        public function marcar_subtarea_completada($idSubtarea) {
            $sql = "UPDATE subtareas SET completada = 1 WHERE idSub = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idSubtarea);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
        

        /**
         * Método para listar las subtareas completadas de un usuario específico.
         * ordena primero por el título de la tarea y luego por el título de la subtarea.
         */
        public function listar_completadas($idUsuario){
            $sql = "SELECT subtareas.*, tareas.titulo as tarea 
                    FROM subtareas 
                    INNER JOIN tareas ON subtareas.idTar = tareas.idTar 
                    WHERE tareas.idUsuario = ?
                    ORDER BY tareas.titulo, subtareas.titulo"; 
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $subtareas_completadas = $resultado->fetch_all(MYSQLI_ASSOC);
            
            return $subtareas_completadas;
        }


        /**
         * Realiza una consulta SQL para buscar una tarea con el ID especificado.
         */

        /********PROCESO BORRADO DE TAREA********/
        function comprobarExisteTarea($id) {
            $sql = "SELECT idTar FROM tareas WHERE idTar = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();
            $existe = $stmt->num_rows() > 0 ? true : false;
            $stmt->close();
            return $existe;
        }

        /**
         * Función para borrar una tarea y sus subtareas asociadas.
         */
        function borrar_fila($id) {
            try {
                $this->conexion->begin_transaction();
                $sql = "DELETE FROM subtareas WHERE idTar = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                
                $sql = "DELETE FROM tareas WHERE idTar = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                
                $this->conexion->commit();
                return true;
            } catch (Exception $e) {
                $this->conexion->rollback();
                return false;
            }
        }

        /*PROCESO SUSTITUCIÓN DE ARCHIVOS*/
        public function obtenerNombreArchivo($idTarea) {
            $sql = "SELECT archivo FROM tareas WHERE idTar = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idTarea);
            $stmt->execute();
            $stmt->bind_result($archivo);
            $stmt->fetch();
            $stmt->close();
            
            return $archivo;
        }

        /**
         * Método para obtener la tarea con la fecha más próxima.
         * 
         * Busca la tarea cuya fecha sea la más próxima a la actual y la devuelve.
         */
        public function obtenerTareaFechaProxima($idUsuario) {
            $sql = "SELECT idTar, titulo, detalle, fecha FROM tareas WHERE idUsuario = ? AND fecha IS NOT NULL AND fecha >= CURDATE() ORDER BY fecha ASC LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $tarea = $resultado->fetch_assoc();
        
            // Si hay una tarea encontrada, también se obtienen sus subtareas
            if ($tarea) {
                $tarea['subtareas'] = $this->obtener_subtareas($tarea['idTar']);
            }
        
            return $tarea;
        }
    }

?>