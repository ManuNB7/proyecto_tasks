<?php

    /**
     * Clase Conexion para manejar la conexión a la base de datos.
     */
    class Conexion{
        /**
         * @var mysqli|null $conexion Conexión a la base de datos.
         */
        protected $conexion = null;

        /**
         * Constructor de la clase que establece la conexión a la base de datos.
         *
         * @throws mysqli_sql_exception Si ocurre un error al intentar establecer la conexión.
         */
        public function __construct(){
            // Incluye el archivo de configuración de la base de datos
            include __DIR__.'/../config/configdb.php';
            
            // Intenta establecer la conexión a la base de datos
            $this->conexion = new mysqli($servidorbd, $usuario, $contraseña, $basedatos);

            // Establece la codificación de caracteres a utf8
            $this->conexion->set_charset("utf8");
            
            // Configura el controlador de mysqli para informar sobre errores
            $controlador = new mysqli_driver();
            $controlador->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
        }
    }

?>