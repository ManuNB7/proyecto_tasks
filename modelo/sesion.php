<?php

    require_once __DIR__.'/conexion.php';

    /**
     * Clase sesionModel
     * 
     * Modelo para la gestión de sesiones de usuario.
     */
    class sesionModel extends Conexion{

        private $tabla;
        private $id;
        private $correo;
        private $pw;
        private $nombre;
        public $error;
        /**
         * Constructor de la clase.
         * 
         * Inicializa las propiedades de la clase y llama al constructor de la clase padre (Conexion).
         */
        public function __construct() {
            parent::__construct();
            $this->tabla = "usuarios";
            $this->id = "id";
            $this->correo = "correo";
            $this->pw = "pw";
            $this->nombre = "nombre";
        }
        
        /**
         * Comprueba si las credenciales del usuario son válidas.
         */
        function comprobar_usuario($login,$pw){
            $sql = "SELECT ".$this->id.",".$this->nombre.", ".$this->pw." 
            FROM ".$this->tabla." 
            WHERE ".$this->nombre."= ? OR ".$this->correo." = ?;";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('ss',$login,$login);
            $stmt->execute();
            $stmt->bind_result($id,$nombre,$pwhash);
            $stmt->fetch();
            $stmt->close();
            $pwcorrecta = password_verify($pw,$pwhash);
            if(!$pwcorrecta){
                $this->error="Usuario y/o contraseña incorrectos";
                return false;
            } else {
                $_SESSION["id"] = $id;
                $_SESSION["nombre"] = $nombre;
                return true;
            }
        }

        public function obtener_id_usuario($nombre_usuario) {
            // Perform a database query to retrieve the user ID based on the username
            $sql = "SELECT ".$this->id." FROM ".$this->tabla." WHERE ".$this->nombre." = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('s', $nombre_usuario);
            $stmt->execute();
            $stmt->bind_result($id);
            $stmt->fetch();
            $stmt->close();
            
            // If the user is found, return the user ID
            if ($id) {
                return $id;
            } else {
                return false;
            }
        }
    }

?>