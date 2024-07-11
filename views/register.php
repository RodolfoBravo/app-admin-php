<?php
// Configuración de la base de datos
require_once '../src/config.php';
require_once '../src/database.php';
require_once '../vendor/autoload.php'; // Cargar la biblioteca JWT

use \Firebase\JWT\JWT;

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Obtener el password sin encriptar
    $role = $_POST['role'];

    // Encriptar el password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Conexión a la base de datos
    $conn = Database::getConnection();

    // Preparar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, email, password, role ) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $role,);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Obtener el ID del usuario insertado (si es necesario)
        $userId = $stmt->insert_id;

        // Configuración del token
        $key = "20202420Ir@"; // Clave secreta para firmar el token, mantenla segura
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  // Tiempo de expiración del token (3600 segundos = 1 hora)
        $issuer = "app-admin"; // Dominio que emite el token (puede ser tu aplicación)

        // Datos del payload del token
        $data = [
            'userId' => $userId, // Supongamos que obtienes el ID del usuario insertado
            'email' => $email
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
        echo "Error al agregar el usuario: " . $stmt->error;
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
    <title>Agregar Usuario</title>
</head>
<body>
    <h1>Agregar Usuario</h1>

    <?php
    // Mostrar mensaje si existe
    if (isset($message)) {
        echo "<p>$message</p>";
    }
    ?>

    <form action="" method="post">
        <label for="firstName">Nombre:</label>
        <input type="text" id="firstName" name="firstName" required><br><br>
        
        <label for="lastName">Apellido:</label>
        <input type="text" id="lastName" name="lastName" required><br><br>
        
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <label for="role">Rol:</label>
        <input type="text" id="role" name="role" required><br><br>
        
        <button type="submit">Agregar Usuario</button>
    </form>
</body>
</html>
