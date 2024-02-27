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

        function listar_tareas(){
            $sql = "SELECT * FROM tareas";
            $stmt = $this->conexion->prepare($sql);
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
        
        function obtener_subtareas($idTarea){
            $sql = "SELECT * FROM subtareas WHERE idTar = ? AND completada is NULL";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idTarea);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $subtareas = $resultado->fetch_all(MYSQLI_ASSOC);
            
            return $subtareas;
        }      

        /********PROCESO INSERCIÓN DE TAREAS Y SUBTAREAS********/

        public function insertar_tarea($titulo, $detalle, $fecha, $subtareas, $nombre_archivo) {
            try {
                $this->conexion->autocommit(false);
                $this->conexion->begin_transaction();
                
                // Verificar si la fecha está vacía y convertirla en NULL si es necesario
                $detalle_insertar = empty($detalle) ? null : $detalle;
                $fecha_insertar = empty($fecha) ? null : $fecha;
        
                $file_insertar = ($nombre_archivo !== null) ? $nombre_archivo : null;
        
                // Consulta SQL para insertar en la tabla 'tareas'
                if ($file_insertar !== null) {
                    $sql = "INSERT INTO tareas(titulo, detalle, fecha, archivo) VALUES (?,?,?,?)";
                } else {
                    $sql = "INSERT INTO tareas(titulo, detalle, fecha) VALUES (?,?,?)";
                }
        
                $stmt = $this->conexion->prepare($sql);
                if ($file_insertar !== null) {
                    $stmt->bind_param("ssss", $titulo, $detalle_insertar, $fecha_insertar, $file_insertar);
                } else {
                    $stmt->bind_param("sss", $titulo, $detalle_insertar, $fecha_insertar);
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
                    
                    foreach ($subtareas as $subtarea) {
                        if (is_array($subtarea)) {
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
        function listar_mod($idTarea) {
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
        
        function listar_completadas(){
            $sql = "SELECT subtareas.*, tareas.titulo as tarea FROM subtareas INNER JOIN tareas ON subtareas.idTar = tareas.idTar WHERE subtareas.completada = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $subtareas_completadas = $resultado->fetch_all(MYSQLI_ASSOC);
            
            return $subtareas_completadas;
        }    
        
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

        public function agregar_subtarea($idTarea, $titulo, $detalle, $fecha) {
            try {
                $this->conexion->autocommit(false);
                $this->conexion->begin_transaction();

                $detalle_insertar = empty($detalle) ? null : $detalle;
                $fecha_insertar = empty($fecha) ? null : $fecha;
                
                $sql = "INSERT INTO subtareas(titulo, detalle, fecha, idTar) VALUES (?, ?, ?, ?)";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("sssi", $titulo, $detalle, $fecha_insertar, $idTarea);
                $stmt->execute();

                $this->conexion->commit();

                return true;
            }   catch (mysqli_sql_exception $e) {
                $this->conexion->rollback();
                $this->error = "Error ".$e->getCode().": Contacte con el administrador.";
                return false;
            }
        }
        
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

        function borrar_fila($id) {
            try {
                $this->conexion->begin_transaction();
                $sqlDeleteSubtareas = "DELETE FROM subtareas WHERE idTar = ?";
                $stmtSubtareas = $this->conexion->prepare($sqlDeleteSubtareas);
                $stmtSubtareas->bind_param("i", $id);
                $stmtSubtareas->execute();
                
                $sqlDeleteTarea = "DELETE FROM tareas WHERE idTar = ?";
                $stmtTarea = $this->conexion->prepare($sqlDeleteTarea);
                $stmtTarea->bind_param("i", $id);
                $stmtTarea->execute();
                
                $this->conexion->commit();
                return true;
            } catch (Exception $e) {
                $this->conexion->rollback();
                return false;
            }
        }
    }

?>