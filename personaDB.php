<?php
    require_once 'db/db.php';
    class PersonaDB
    {
        protected $mysqliconn;      //Variable para instancia de clase de base de datos

        /**
         * Constructor de clase
         */
        public function __construct()
        {   
            try
            {
                //Iniciando conexión
                $this->mysqliconn = DevTestDB::conectar();
            }
            catch(mysqli_sql_exception $e){
                //Mostrando respuesta error
                http_response_code(400);
                exit;
            }
        }

        /**
         * Función para obtener registros
         */
        function GetPeople()
        {
            //Ejecutando consulta con ordenamiento TABLA 'persona'
            $resultado = $this -> mysqliconn -> query(' SELECT * FROM persona ORDER BY nombre, apellido');
            $personas = $resultado -> fetch_all(MYSQLI_ASSOC);      //Creando arreglo asociativo con los resultados
            $resultado -> close();               //Cerrando la conexión
            
            //Enviando respuesta con formato JSON
            echo json_encode($personas, JSON_PRETTY_PRINT);
        }

        /**
         * Función para agregar un nuevo registro
         */
        function SavePeople()
        {
            //Obteniendo información de petición
            $data = json_decode(file_get_contents('php://input'));
            //Verificando que exista información para trabajar
            if(isset($data)){
                //Alojando variables de la respuesta
                $nombre = $data -> nombre;
                $apellido = $data -> apellido;
                $fechaNacimiento = $data -> fechaNacimiento;
                $salario = $data -> salario;
                //Verificando que existan los datos necesarios
                if(isset($nombre,$apellido,$fechaNacimiento,$salario)){
                    //Colocando sentencia preparada
                    $stmt = $this -> mysqliconn -> prepare(' INSERT INTO persona(nombre,apellido,fechaNacimiento,salario) VALUES(?,?,?,?);');
                    $stmt -> bind_param('sssd', $nombre, $apellido, $fechaNacimiento, $salario);    //Ingresando parametros a sentencia
                    $stmt -> execute();     //Ejecutando

                    //Verificando registros afectados
                    if($stmt -> affected_rows == 1)
                    {
                        //Respuesta correcta
                        $this -> Response(200, 'success', 'Ingresado correctamente');
                    }
                    else
                    {
                        //Respuesta con error porque no se ingresaron registros
                        $this -> Response(400, 'error', 'No se ha podido ingresar');
                    }
                }
                else
                {
                    //Respuesta con error porque no existen los datos necesarios
                    $this -> Response(400, 'incomplete', 'No se permite campos vacíos en la actualización');
                }
            }
            else
            {
                //Respuesta con error porque no hay información suficiente
                $this -> Response(400, 'incomplete', 'Sin información');
            }
        }

        /**
         * Función para actualización de personas
         */
        function UpdatePeople()
        {   
            $data = json_decode(file_get_contents('php://input'));
            if(isset($data)){
                $id = $data -> id;
                if(isset($id))
                {
                    $nombre = $data -> nombre;
                    $apellido = $data -> apellido;
                    $fechaNacimiento = $data -> fechaNacimiento;
                    $salario = $data -> salario;

                    if(isset($nombre, $apellido, $fechaNacimiento, $salario))
                    {

                        $stmt = $this -> mysqliconn -> prepare('UPDATE persona SET nombre = ?, apellido = ?, fechaNacimiento = ?, salario = ? WHERE id = ?');
                        $stmt -> bind_param('sssdi', $nombre, $apellido, $fechaNacimiento, $salario, $id);
                        $stmt -> execute();
                        if($stmt -> affected_rows == 1)
                        {
                            //Respuesta correcta
                            $this -> Response(200, 'success', 'Modificado correctamente');
                        }
                        else
                        {
                            //Respuesta con error porque no se modificaron registros
                            $this -> Response(400, 'error', 'No se ha podido modificar');
                        }
                    }
                    else
                    {
                        //Respuesta con error porque no existen los datos necesarios
                        $this -> Response(400, 'incomplete', 'No se permite campos vacíos en la actualización');
                    }
                }
                else
                {
                    //Respuesta con error por falta de identificador
                    $this -> Response(400, 'incomplete', 'El identificador no está agregado');
                }
            }
            else
            {
                //Respuesta con error porque no hay información suficiente
                $this -> Response(400, 'incomplete', 'Sin información');
            }

            
        }

        /**
         * Función para borrado de personas
         */
        function DeletePeople()
        {
            $data = json_decode(file_get_contents('php://input'));
            if(isset($data)){
                $id = $data -> id;
                if(isset($id)){
                    $stmt = $this -> mysqliconn -> prepare('DELETE FROM persona WHERE id = ?');
                    $stmt -> bind_param('i', $id);
                    $stmt -> execute();
                    if($stmt -> affected_rows = 1)
                    {
                        //Respuesta correcta
                        $this -> Response(200, 'success', 'Ingresado correctamente');
                    }
                    else
                    {
                        //Respuesta con error porque no se eliminaron registros
                        $this -> Response(400, 'error', 'No se ha podido eliminar');
                    }
                }
                else
                {
                    //Respuesta con error por falta de identificador
                    $this -> Response(400, 'Incomplete', 'El identificador no está agregado');
                }
            }
            else
            {
                //Respuesta con error porque no hay información suficiente
                $this -> Response(400, 'incomplete', 'Sin información');
            }
            
        }

        /**
         * Función para enviar respuesta
         */
        function Response($code=200, $status="", $message ="")
        {
            //Enviando cóðigo de respuesta 
            http_response_code($code);
            //Verificando status y message diferente de vacío
            if(!empty($status) && !empty($message))
            {
                $response = array("status"=>$status, "message"=>$message);      //Creando arreglo para envío de información
                echo json_encode($response, JSON_PRETTY_PRINT);                 //Envío de arreglo informativo en formato JSON
            }

        }
    }
?>