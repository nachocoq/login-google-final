<?php
require_once __DIR__ . '/vendor/autoload.php';

session_start();

session_unset(); 
session_destroy(); 

session_start();

$dsn = 'mysql:host=localhost;dbname=usuarios';
$db_user = 'root';
$db_password = '';

try {
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error al conectar a la base de datos: ' . $e->getMessage());
}

$client = new Google\Client();
$client->setClientId('795300324487-mrocbj2vj7291l2esnvtq0r4t4sg2gp6.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri("http://localhost:81/googlelogin/index.php");
$client->addScope("email");
$client->addScope("profile");
$client->setPrompt('select_account');

$error_message = '';

$activeCard = 'login';

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token['access_token']);

        $oauth2 = new Google\Service\Oauth2($client);
        $google_user_info = $oauth2->userinfo->get();

        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE google_id = :google_id');
        $stmt->execute(['google_id' => $google_user_info->getId()]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['google_user_info'] = $google_user_info;
            header('Location: set_password.php');
            exit();
        }

        $_SESSION['google_user'] = $google_user_info->getId();

        $update_stmt = $pdo->prepare('UPDATE usuarios SET fecha = NOW() WHERE google_id = :google_id');
        $update_stmt->execute(['google_id' => $google_user_info->getId()]);

        header('Location: dashboard.php');
        exit();
    } catch (Exception $e) {
        $error_message = 'Error al iniciar sesión con Google: ' . $e->getMessage();
    }
}

$activeCard = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
		$username = $_POST['login-username'];
        $password = $_POST['login-password'];

        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE nombre = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            $update_stmt = $pdo->prepare('UPDATE usuarios SET fecha = NOW() WHERE id = :id');
            $update_stmt->execute(['id' => $user['id']]);

            header('Location: dashboard.php');
            exit();
        } else {
            $error_message = 'Usuario o contraseña incorrectos';
        }
    } elseif (isset($_POST['register'])) {
		$username = $_POST['register-username'];
        $email = $_POST['register-email'];
        $password = $_POST['register-password'];

        if (empty($username) || empty($email) || empty($password)) {
            $error_message = 'Todos los campos son obligatorios';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE nombre = :username OR email = :email');
            $stmt->execute(['username' => $username, 'email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $error_message = 'El usuario o el correo electrónico ya están registrados';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password, fecha) VALUES (:username, :email, :password, NOW())');
                $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashed_password]);
            }
        }
		$activeCard = 'register';
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="styles/index.css" rel="stylesheet">
</head>
<body>
    <div class="container">
		<div class="card card-login <?php echo $activeCard === 'login' ? 'active' : 'disabled'; ?>" id="login-card">
			<h3>Iniciar Sesión</h3>
			<form action="" method="post">
				<?php if (!empty($error_message) && $activeCard === 'login'): ?>
					<div class="error-message"><?php echo $error_message; ?></div>
				<?php endif; ?>
				<div class="form-group">
					<label for="login-username">Nombre</label>
					<input type="text" id="login-username" name="login-username" placeholder="Ingresa tu usuario" required>
				</div>
				<div class="form-group">
					<label for="login-password">Contraseña</label>
					<input type="password" id="login-password" name="login-password" placeholder="Ingresa tu contraseña" required>
				</div>
				<button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Iniciar Sesión</button>
				<a href="<?php echo $client->createAuthUrl(); ?>" class="btn btn-google">Iniciar con Google</a>
			</form>
			<div class="card-footer">
				<div>
					<a href="#" id="show-register">¿No tienes una cuenta? Regístrate</a>
				</div>
				o
				<div>
					<a href="forgot_password.php" class="forgot-password-link">¿Olvidaste tu contraseña?</a>
				</div>
			</div>
		</div>
        <div class="card card-register <?php echo $activeCard === 'register' ? 'active' : 'disabled'; ?>" id="register-card">
            <h3>Registro</h3>
            <form action="" method="post">
                <?php if (!empty($error_message) && $activeCard === 'register'): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="register-username">Usuario</label>
                    <input type="text" id="register-username" name="register-username" placeholder="Ingresa tu usuario" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Correo Electrónico</label>
                    <input type="email" id="register-email" name="register-email" placeholder="Ingresa tu correo electrónico" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Contraseña</label>
                    <input type="password" id="register-password" name="register-password" placeholder="Ingresa tu contraseña" required>
                </div>
                <button type="submit" name="register" class="btn btn-primary" style="width: 100%;">Registrarse</button>
            </form>
            <div class="card-footer">
                <a href="#" id="show-login">¿Ya tienes una cuenta? Inicia sesión</a>
            </div>
        </div>
    </div>
    <script src="scripts/login.js" type="text/javascript"></script>
</body>
</html>
