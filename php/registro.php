<?php

    require("../vendor/autoload.php");

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $usuario = trim($_POST["usuario"]);
        $email = trim($_POST["email"]);
        $password =  trim($_POST["password"]);
        $password_repetida = trim($_POST["confirmPassword"]);

        $nombreBBDD  = "videoClub";
        $nombreColeccion = "sesiones";
     
        if ($password !== $password_repetida) {
            echo "<script>alert('Las contraseñas no coinciden'); window.history.back();</script>";
            exit;
        }
        try{
            $uri = getenv('MONGO_URI');

            $cliente = new MongoDB\Client($uri);

            $db = $cliente->selectDatabase($nombreBBDD);
            $colection = $db->selectCollection($nombreColeccion);

            $usuarioExistente = $colection->findOne(["usuario"=>$usuario]);

            if($usuarioExistente){
                echo "<script>alert('El usuario ya existe. Elige otro nombre'); window.location.href='../registro.html';</script>";
            }else{
                $nuevoUsuario = [
                    "usuario"=>$usuario,
                    "email"=>$email,
                    "password"=>$password,
                    "rol"=>"usuario"
                ];
                $resultado = $colection->insertOne($nuevoUsuario);

                if($resultado->getInsertedCount() > 0){
                    echo "<script>alert('Registro exitoso'); window.location.href='../login.html';</script>";
                }else{
                    echo "<script>alert('Error al registrar'); window.location.href='../registro.html';</script>";
                }
            }
        }catch(Exception $error){
            http_response_code(500);
            echo json_encode(['error' => 'Error al conectar con MongoDB: ' . $error->getMessage()]);
            exit;
        }
    }

?>