<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php'; 

use Google\Service\Oauth2\Userinfo;

$dsn = 'mysql:host=localhost;dbname=usuarios';
$db_user = 'root';
$db_password = '';

try {
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    die('Error al conectar a la base de datos: ' . $e->getMessage());
}

if (!isset($_SESSION['google_user_info'])) {
    header('Location: login.php');
    exit();
}

$google_user_info_incomplete = $_SESSION['google_user_info'];

if (is_object($google_user_info_incomplete) && get_class($google_user_info_incomplete) === '__PHP_Incomplete_Class') {
    $google_user_info_array = (array) $google_user_info_incomplete;

    $google_user_info = new Userinfo();
    $public_properties = ['id', 'name', 'email', 'picture', 'verifiedEmail', 'givenName', 'familyName'];

    foreach ($public_properties as $property) {
        if (isset($google_user_info_array[$property])) {
            $google_user_info->$property = $google_user_info_array[$property];
        }
    }
} else {
    $google_user_info = $google_user_info_incomplete;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare('INSERT INTO usuarios (google_id, nombre, email, password, fecha) VALUES (:google_id, :nombre, :email, :password, NOW())');
            $stmt->execute([
                'google_id' => $google_user_info->id,
                'nombre' => $google_user_info->name,
                'email' => $google_user_info->email,
                'password' => $hashed_password
            ]);

            $_SESSION['google_user'] = $google_user_info->id;

            unset($_SESSION['google_user_info']);

            header('Location: dashboard.php');
            exit();
        } catch (Exception $e) {
            $error_message = 'Error al guardar la contraseña: ' . $e->getMessage();
        }
    } else {
        $error_message = 'Por favor, ingresa una contraseña.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="styles/set_password.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h3>Configurar Contraseña</h3>
        <form action="" method="post">
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña">
            </div>
            <button type="submit" class="btn btn-primary">Guardar Contraseña</button>
        </form>
    </div>
</body>
</html>