<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

$dsn = 'mysql:host=localhost;dbname=usuarios';
$db_user = 'root';
$db_password = '';

$error_message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['reset_user_id'])) {
    $email = $_POST['email'];

    try {
        $pdo = new PDO($dsn, $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email AND google_id IS NULL');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['reset_user_id'] = $user['id'];
            $success = true;
        } else {
            $error_message = 'El correo electrónico no está registrado o está asociado a una cuenta de Google.';
        }
    } catch (PDOException $e) {
        $error_message = 'Error al conectar a la base de datos: ' . $e->getMessage();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['reset_user_id'])) {
    $new_password = $_POST['new_password'];

    try {
        $pdo = new PDO($dsn, $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE usuarios SET password = :password, fecha = NOW() WHERE id = :user_id');
        $stmt->execute(['password' => $hashed_password, 'user_id' => $_SESSION['reset_user_id']]);

        unset($_SESSION['reset_user_id']);
        header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        $error_message = 'Error al actualizar la contraseña: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="styles/index.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="card card-forgot-password active" id="forgot-password-card" style="height: 45%">
            <h3>Recuperar Contraseña</h3>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php elseif ($success): ?>
                <form action="" method="post">
                    <div class="form-group">
                        <input type="password" id="new_password" name="new_password" placeholder="Ingresa tu nueva contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Actualizar Contraseña</button>
                </form>
            <?php else: ?>
                <form action="" method="post">
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Ingresa tu correo electrónico" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Validar dirección email</button>
                </form>
            <?php endif; ?>
            <div class="card-footer">
                <a href="index.php">Volver al inicio de sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
