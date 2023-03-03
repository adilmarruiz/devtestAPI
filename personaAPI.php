<?php
   

    //Importando el archivo personaDB.php para la utilización de la clase
    require_once "personaDB.php";

    //Clase controladora
    class PersonaAPI
    {   
        //atributo de clase
        protected $peopleDB;

        /**
         * Metodo contructor de la clase
         * No requiere parametros
         */
        public function __construct()
        {
            $this-> peopleDB = new PersonaDB();
        }

        /**
         * Función publica para utilización de la información 
         * según sea el metodo de la petición
         * No requiere parametros
         */
        public function API()
        {

            header('Content-Type: application/JSON');

            //Obtención del metodo de la petición
            $method = $_SERVER['REQUEST_METHOD'];

            //Evaluando el metodo obtenido en la petición
            switch($method)
            {
                case 'GET':   //Si es GET es una consulta SELECT
                    $this->peopleDB->GetPeople();
                break;
                case 'POST':   //Si es POST inserta un registro
                    $this->peopleDB->SavePeople();
                break;
                case 'PUT':   //Si es PUT actualiza un registroT
                    $this->peopleDB->UpdatePeople();
                break;
                case 'DELETE':   //Si es DELETE elimina un registro
                    $this->peopleDB->DeletePeople();
                break;
                default:   //Metodo no soportado
                    $this->peopleDB->Response(405);
                break;
            }

        }
    }
?>