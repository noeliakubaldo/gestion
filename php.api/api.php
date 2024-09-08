<?php
// Incluir el archivo de conexión
require 'db.php';

// Obtener el método de la solicitud (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Ruta de la solicitud (por ejemplo, /api.php/usuario o /api.php/distrito)
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

// Verifica si se especifica la tabla (usuario o distrito)
if (!isset($request[0])) {
    http_response_code(400);
    echo json_encode(["error" => "Tabla no especificada"]);
    exit;
}

$table = $request[0]; // Nombre de la tabla (usuario o distrito)

// Verifica si se especifica un ID en la solicitud
$id = isset($request[1]) ? intval($request[1]) : null;

// Manejar las diferentes operaciones dependiendo del método HTTP
switch ($method) {
    case 'GET':
        if ($id) {
            // Obtener un registro por ID
            $sql = "SELECT * FROM $table WHERE id = $id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                echo json_encode(["message" => "Registro no encontrado"]);
            }
        } else {
            // Obtener todos los registros
            $sql = "SELECT * FROM $table";
            $result = $conn->query($sql);
            $rows = [];
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        }
        break;

    case 'POST':
        // Obtener los datos enviados en el cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
        if ($table == 'usuario') {
            $nombre = $data['nombre'];
            $correo = $data['correo'];
            $contrasena = password_hash($data['contrasena'], PASSWORD_DEFAULT);
            $distrito_id = $data['distrito_id'];
            $fecha_registro = $data['fecha_registro'];
            $sql = "INSERT INTO usuario (nombre, correo, contraseña, distrito_id, fecha_registro) VALUES ('$nombre', '$correo', '$contrasena', $distrito_id, '$fecha_registro')";
        } else if ($table == 'distrito') {
            $nombre = $data['nombre'];
            $sql = "INSERT INTO distrito (nombre) VALUES ('$nombre')";
        }
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Registro creado exitosamente"]);
        } else {
            echo json_encode(["error" => "Error al crear registro: " . $conn->error]);
        }
        break;

    case 'PUT':
        if ($id) {
            // Obtener los datos enviados en el cuerpo de la solicitud
            $data = json_decode(file_get_contents('php://input'), true);
            if ($table == 'usuario') {
                $nombre = $data['nombre'];
                $correo = $data['correo'];
                $contrasena = isset($data['contrasena']) ? password_hash($data['contrasena'], PASSWORD_DEFAULT) : null;
                $distrito_id = $data['distrito_id'];
                $fecha_registro = $data['fecha_registro'];
                $sql = "UPDATE usuario SET nombre = '$nombre', correo = '$correo', distrito_id = $distrito_id, fecha_registro = '$fecha_registro'";
                if ($contrasena) {
                    $sql .= ", contraseña = '$contrasena'";
                }
                $sql .= " WHERE id = $id";
            } else if ($table == 'distrito') {
                $nombre = $data['nombre'];
                $sql = "UPDATE distrito SET nombre = '$nombre' WHERE id = $id";
            }
            if ($conn->query($sql) === TRUE) {
                echo json_encode(["message" => "Registro actualizado exitosamente"]);
            } else {
                echo json_encode(["error" => "Error al actualizar registro: " . $conn->error]);
            }
        }
        break;

    case 'DELETE':
        if ($id) {
            // Eliminar un registro por ID
            $sql = "DELETE FROM $table WHERE id = $id";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(["message" => "Registro eliminado exitosamente"]);
            } else {
                echo json_encode(["error" => "Error al eliminar registro: " . $conn->error]);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}

$conn->close();
?>
