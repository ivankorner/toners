<?php
session_start();
require_once 'config/database.php';

// Si ya está logueado, redirigir al sistema
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$mensaje = '';

// Verificar si viene de logout
if (isset($_GET['logout'])) {
    $mensaje = 'Sesión cerrada correctamente';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $contrasena = $_POST['contrasena'];
    
    if (!empty($usuario) && !empty($contrasena)) {
        try {
            $stmt = $pdo->prepare("SELECT id, nombre_usuario, contrasena FROM usuarios WHERE nombre_usuario = ?");
            $stmt->execute([$usuario]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($contrasena, $user['contrasena'])) {
                // Login exitoso
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
                $_SESSION['login_time'] = time();
                $_SESSION['ultimo_acceso'] = time();
                
                // Actualizar último acceso en la base de datos
                try {
                    $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);
                } catch (Exception $e) {
                    // Ignorar errores de actualización
                }
                
                // Redirigir al sistema
                header('Location: index.php');
                exit();
            } else {
                $error = 'Usuario o contraseña incorrectos';
            }
        } catch (PDOException $e) {
            $error = 'Error en el sistema: ' . $e->getMessage();
        }
    } else {
        $error = 'Por favor complete todos los campos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema - Inventario de Toners</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            margin: 20px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-icon {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 20px;
        }
        
        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            margin-bottom: 20px;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #007bff;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .btn-login:hover {
            background: #0056b3;
        }
        
        .credentials-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-print"></i>
            </div>
            <h2>Sistema de Inventario</h2>
            <p class="text-muted">Ingrese sus credenciales</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="usuario" class="form-label">
                    <i class="fas fa-user me-2"></i>Usuario
                </label>
                <input type="text" class="form-control" id="usuario" name="usuario" 
                       placeholder="Ingrese su usuario" required value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>">
            </div>
            
            <div class="mb-3">
                <label for="contrasena" class="form-label">
                    <i class="fas fa-lock me-2"></i>Contraseña
                </label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" 
                       placeholder="Ingrese su contraseña" required>
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
            </button>
        </form>
        
        
        
        
    </div>

    <script>
        document.getElementById('usuario').focus();
    </script>
</body>
</html>