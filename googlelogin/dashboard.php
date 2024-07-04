<?php

session_start();

$dsn = 'mysql:host=localhost;dbname=usuarios';
$db_user = 'root';
$db_password = '';

try {
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['google_user'])) {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE google_id = :google_id');
        $stmt->execute(['google_id' => $_SESSION['google_user']]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
	} elseif (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = :user_id');
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
	}

    if (!$user) {
        header('Location: index.php');
        exit();
    }
} catch (PDOException $e) {
    die('Error al conectar a la base de datos: ' . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="styles/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>!</h1>
        <p>Has iniciado sesión correctamente.</p>
        <a href="index.php" class="btn">Cerrar Sesión</a>
    </div>
</body>
</html>