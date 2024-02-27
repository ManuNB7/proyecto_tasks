<?php

    require_once __DIR__.'/../modelo/registro.php';

    /**
     * Clase adminController
     * 
     * Controlador para la gestión de súper usuarios.
     */
    class registroController {

        public $titulo;
        public $view;
        public $modelo;
                
        /**
         * Constructor de la clase.
         * 
         * Inicializa las propiedades de la clase y crea una instancia del modelo registroModel.
         */
        public function __construct() {
            $this->view = "registro"; 
            $this->titulo = "Registro usuario"; 
            $this->modelo = new registroModel(); 
        }
        
        function mostrar_registro(){
            $this->view = "registro_usuario";
            $this->titulo = "Registro usuarios";
        }
        
        // Cambiando el nombre de la función y la lógica para registrar usuarios
        function registrar_usuario(){
            $nombre = $_POST["nombre"];
            $correo = $_POST["correo"];
            $pw = $_POST["pw"];
            
            // Validación de campos antes de registrar
            if($this->validar_campos($nombre,$correo,$pw)){
                $resultado = $this->modelo->registrar_usuario($nombre,$correo,$pw);
                
                if($resultado){
                    $this->view = "registro_usuario";
                    $this->titulo = "Registrarse";
                    $_GET["exito"] = "Usuario añadido con éxito";
                } else {
                    $_GET["error"] = $this->modelo->error;
                    $this->view = "registro_usuario";
                    $this->titulo = "Registro usuarios";
                }
            } else{
                //Actualiza la vista
                $this->view = "registro_usuario";
                $this->titulo = "Registro usuarios";
            }
        }
        
        function validar_campos($nombre,$correo,$pw){
            if(empty($nombre)||empty($correo)||empty($pw)){
                $_GET["error"] = "Debes rellenar el correo, el nombre y la contraseña";
                return false;
            }
            return true;
        }
    }

?>