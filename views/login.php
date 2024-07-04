<?php
// Configuración de la base de datos
require_once '../src/config.php';
require_once '../src/database.php';
require_once '../vendor/autoload.php'; // Cargar la biblioteca JWT

use \Firebase\JWT\JWT;

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Conexión a la base de datos
    $conn = Database::getConnection();

    // Preparar la consulta SQL para obtener el usuario por email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Obtener la fila del usuario
        $user = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Configuración del token
            $key = "20202420Ir@"; // Clave secreta para firmar el token, mantenla segura
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600;  // Tiempo de expiración del token (3600 segundos = 1 hora)
            $issuer = "app-admin"; // Dominio que emite el token (puede ser tu aplicación)

            // Datos del payload del token
            $data = [
                'userId' => $user['id'],
                'email' => $user['email']
            ];

            // Generar el token JWT
            $token = JWT::encode(
                [
                    'iss' => $issuer,
                    'iat' => $issuedAt,
                    'exp' => $expirationTime,
                    'data' => $data
                ],
                $key,
                'HS256'
            );

            // Devolver el token como respuesta (puedes devolverlo como JSON u otra estructura según tu aplicación)
            echo json_encode([
                'token' => $token
            ]);

        } else {
            echo "Contraseña incorrecta.";
        }

    } else {
        echo "Usuario no encontrado.";
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>

    <form action="" method="post">
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Iniciar Sesión</button>
    </form>

    <p>¿No tienes una cuenta? <a href="index.php?action=register">Regístrate aquí</a></p>
</body>
</html>
