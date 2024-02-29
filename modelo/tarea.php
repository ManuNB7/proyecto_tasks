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
         * 
         * Realiza una consulta a la base de datos para obtener todas las tareas asociadas al usuario especificado.
         * Itera sobre las tareas obtenidas y, para cada una, obtiene sus subtareas llamando al método obtener_subtareas().
         * Agrega las subtareas obtenidas a cada tarea como un nuevo campo 'subtareas'.
         * Retorna un array con todas las tareas y sus subtareas asociadas.
         *
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
         * Método para obtener una tarea por su ID y el ID del usuario.
         * 
         * Realiza una consulta a la base de datos para obtener la tarea con el ID especificado asociada al usuario especificado.
         * Si se encuentra la tarea, se llama al método obtener_subtareas() para obtener sus subtareas asociadas.
         * Agrega las subtareas obtenidas a la tarea como un nuevo campo 'subtareas'.
         * Retorna la tarea encontrada con sus subtareas asociadas, o null si no se encuentra ninguna tarea.
         *
         */
        public function obtener_tarea_por_id($idTarea, $idUsuario) {
            $sql = "SELECT * FROM tareas WHERE idTar = ? AND idUsuario = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ii", $idTarea, $idUsuario);
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
         * Realiza una consulta a la base de datos para obtener todas las subtareas asociadas a la tarea con el ID especificado.
         * Retorna un array con todas las subtareas de la tarea.
         *
         */
        public function obtener_subtareas($idTarea){
            $sql = "SELECT * FROM subtareas WHERE idTar = ? AND completada is NULL";
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
         * Inicia una transacción para garantizar la integridad de los datos durante la inserción.
         * Verifica si la fecha y el detalle de la tarea están vacíos y los convierte en NULL si es necesario.
         * Prepara y ejecuta una consulta SQL para insertar la tarea en la tabla 'tareas'.
         * Obtiene el ID de la tarea recién insertada.
         * Si hay subtareas proporcionadas, prepara y ejecuta consultas SQL para insertar cada subtarea en la tabla 'subtareas'.
         *
         */
        public function insertar_tarea($titulo, $detalle, $fecha, $subtareas, $nombre_archivo, $idUsuario) { // Agregar $idUsuario al método
            try {
                $this->conexion->autocommit(false);
                $this->conexion->begin_transaction();
                
                // Verificar si la fecha está vacía y convertirla en NULL si es necesario
                $detalle_insertar = empty($detalle) ? null : $detalle;
                $fecha_insertar = empty($fecha) ? null : $fecha;
        
                $file_insertar = ($nombre_archivo !== null) ? $nombre_archivo : null;
        
                // Consulta SQL para insertar en la tabla 'tareas'
                if ($file_insertar !== null) {
                    $sql = "INSERT INTO tareas(titulo, detalle, fecha, archivo, idUsuario) VALUES (?,?,?,?,?)"; // Agregar idUsuario al INSERT
                } else {
                    $sql = "INSERT INTO tareas(titulo, detalle, fecha, idUsuario) VALUES (?,?,?,?)"; // Agregar idUsuario al INSERT
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
                        // Verifica si la subtarea actual es un arreglo
                        if (is_array($subtarea)) {
                            // Obtiene el título/detalle/fecja de la subtarea o asigna una cadena vacía si no está definido
                            $titulo_sub = $subtarea['titulo'] ?? '';
                            $detalle_sub = $subtarea['detalle'] ?? '';
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
         * 
         * Realiza una consulta SQL para seleccionar la tarea con el ID proporcionado.
         * Recupera los detalles de la tarea como un arreglo asociativo.
         * Si se encuentra la tarea, también obtiene sus subtareas mediante el método obtener_subtareas.
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
         * 
         * Verifica si los parámetros principales de la tarea (título, detalle, fecha) o las subtareas están vacíos.
         * Si hay subtareas, prepara y ejecuta consultas SQL individuales para actualizar cada subtarea.
         *
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
        
                $sql_update_tarea = "UPDATE tareas SET titulo = ?, detalle = ?, fecha = ?, archivo = ? WHERE idTar = ?";
                $stmt = $this->conexion->prepare($sql_update_tarea);
                $stmt->bind_param("ssssi", $titulo, $detalle_insertar, $fecha_insertar, $nombre_archivo, $idTarea);
                $stmt->execute();
        
                if (!empty($subtareas) && is_array($subtareas)) {
                    foreach ($subtareas as $subtarea) {
                        if (is_array($subtarea)) {
                            $idSub = $subtarea['idSub'] ?? null;
                            $titulo_sub = $subtarea['titulo'] ?? '';
                            $detalle_sub = $subtarea['detalle'] ?? '';
                            $fecha_sub = empty($subtarea['fecha']) ? null : $subtarea['fecha'];
        
                            if ($idSub) {
                                $sql_update_subtarea = "UPDATE subtareas SET titulo = ?, detalle = ?, fecha = ? WHERE idSub = ?";
                                $stmt = $this->conexion->prepare($sql_update_subtarea);
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

        /**
         * Método para agregar una subtarea a una tarea existente.
         * 
         * Verifica si los parámetros de detalle y fecha están vacíos y los convierte en NULL si es necesario.
         * Prepara y ejecuta una consulta SQL para insertar la subtarea en la base de datos.
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
         * 
         * Realiza una consulta SQL para actualizar el estado de completado de la subtarea con el ID proporcionado.
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
         * 
         * Realiza una consulta SQL que selecciona todas las subtareas completadas, junto con el título de la tarea asociada.
         * Vincula el parámetro ID de usuario a la consulta preparada.
         * Recupera todas las subtareas completadas como un arreglo asociativo.
         *
         */
        public function listar_completadas($idUsuario){
            $sql = "SELECT subtareas.*, tareas.titulo as tarea 
                    FROM subtareas 
                    INNER JOIN tareas ON subtareas.idTar = tareas.idTar 
                    WHERE subtareas.completada = 1 
                    AND tareas.idUsuario = ?";
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
         * Prepara y ejecuta consultas SQL para eliminar las subtareas y la tarea principal de la base de datos.
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
    }

?>