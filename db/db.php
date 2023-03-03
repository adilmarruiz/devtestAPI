<?php

    require_once('config.php');

    class DevTestDB{
        protected $conexion;
        //Conexión con la base de datos
        public static function conectar()
        {
            $conexion = mysqli_init();      //Inicialización de mysqsli
            $conexion -> ssl_set(null, null, 'db/cacert.pem', null, null);      //configuración de vertificado para planet scale
            $conexion -> real_connect(HOST,USERNAME,PASSWORD,DATABASE);         //Conectando con las constantes

            //Evaluando si se obtiene un error
            if($conexion->connect_errno)
            {
                die("Lo sentimos, no se puede establecer la conexión con MySQL/MariaDB").mysqli_errno($conexion);
            }
            else
            {
                //Selección de la base de datos
                $db = mysqli_select_db($conexion, DATABASE);
                //Si no existe mostrar el mensaje
                if($db == 0)
                {
                    die("Lo sentimos, no se puede establecer la conexión con la base de datos: ").DATABASE;
                }
            }
            
            return $conexion;
        }

        //Cerrando la conexion a la base de datos
        public static function desconectar($conexion)
        {
            //Cerrando la conexion
            if($conexion)
            {
                mysqli_close($conexion);
            }
        }

    }

?>