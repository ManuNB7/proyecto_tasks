<?php
    /**
     * Controlador que gestiona el menú principal.
     */
    class menuController{

        // Propiedades de la clase
        /**
         * @var string Título de la página.
         */
        public $titulo;
        /**
         * @var string Vista por defecto.
         */
        public $view;

        /**
         * Constructor de la clase menuController.
         *
         * Este constructor inicializa las propiedades de la clase, estableciendo un título por defecto
         * y la vista asociada al menú principal.
         *
         * @return void
         */
        public function __construct() {
            $this->titulo = 'Menú Principal';
            $this->view = "menu_tareas";
        }

        
    }
?>
