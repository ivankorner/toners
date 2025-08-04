<?php

// Archivo de diagnóstico para el sistema de login
echo "<h2>🔍 Diagnóstico del Sistema de Login</h2>";
echo "<hr>";

// 1. Verificar configuración PHP
echo "<h3>1. Configuración PHP</h3>";
echo "✅ Versión PHP: " . phpversion() . "<br>";
echo "✅ Soporte de sesiones: " . (function_exists('session_start') ? 'SÍ' : 'NO') . "<br>";
echo "✅ Directorio de sesiones: " . session_save_path() . "<br>";
echo "✅ Extensión PDO: " . (extension_loaded('pdo') ? 'SÍ' : 'NO') . "<br>";
echo "✅ PDO MySQL: " . (extension_loaded('pdo_mysql') ? 'SÍ' : 'NO') . "<br>";
echo "<br>";

// 2. Verificar archivos necesarios
echo "<h3>2. Archivos del Sistema</h3>";
$archivos = [
    'config/database.php' => 'Configuración de Base de Datos',
    'login.php' => 'Página de Login',
    'index.php' => 'Página Principal',
    'logout.php' => 'Logout',
    'auth_check.php' => 'Verificación de Autenticación'
];

foreach ($archivos as $archivo => $descripcion) {
    $existe = file_exists($archivo);
    echo ($existe ? "✅" : "❌") . " $descripcion ($archivo): " . ($existe ? "EXISTE" : "NO EXISTE") . "<br>";
}
echo "<br>";

// 3. Verificar conexión a base de datos
echo "<h3>3. Conexión a Base de Datos</h3>";
try {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        echo "✅ Conexión a base de datos: EXITOSA<br>";
        
        // Verificar tabla usuarios
        $stmt = $pdo->query("DESCRIBE usuarios");
        $campos = $stmt->fetchAll();
        echo "✅ Tabla 'usuarios' existe con " . count($campos) . " campos<br>";
        
        // Verificar usuarios en la tabla
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
        $total = $stmt->fetch()['total'];
        echo "✅ Total de usuarios registrados: $total<br>";
        
        // Mostrar usuarios (solo nombres)
        $stmt = $pdo->query("SELECT id, nombre_usuario, fecha_creacion FROM usuarios");
        $usuarios = $stmt->fetchAll();
        echo "<strong>Usuarios en la base de datos:</strong><br>";
        foreach ($usuarios as $user) {
            echo "- ID: {$user['id']}, Usuario: {$user['nombre_usuario']}, Creado: {$user['fecha_creacion']}<br>";
        }
        
    } else {
        echo "❌ No se encuentra el archivo config/database.php<br>";
    }
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}
echo "<br>";

// 4. Probar hash de contraseña
echo "<h3>4. Verificación de Contraseñas</h3>";
$password_test = 'password';
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
$verifica = password_verify($password_test, $hash);
echo "✅ Test de password_verify('password', hash): " . ($verifica ? "CORRECTO" : "INCORRECTO") . "<br>";

// Crear un nuevo hash para verificar
$nuevo_hash = password_hash('password', PASSWORD_DEFAULT);
$verifica_nuevo = password_verify('password', $nuevo_hash);
echo "✅ Test con nuevo hash: " . ($verifica_nuevo ? "CORRECTO" : "INCORRECTO") . "<br>";
echo "<br>";

// 5. Verificar sesiones
echo "<h3>5. Verificación de Sesiones</h3>";
session_start();
echo "✅ Session ID: " . session_id() . "<br>";
echo "✅ Estado de sesión: " . session_status() . "<br>";
echo "✅ Variables de sesión actuales: " . count($_SESSION) . "<br>";
if (!empty($_SESSION)) {
    foreach ($_SESSION as $key => $value) {
        echo "- $key: " . (is_string($value) ? $value : gettype($value)) . "<br>";
    }
}
echo "<br>";

// 6. Formulario de test rápido
echo "<h3>6. Test de Login</h3>";
echo '<form method="POST" style="background: #f8f9fa; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
    <label>Usuario: <input type="text" name="test_user" value="admin" style="margin-left: 10px;"></label><br><br>
    <label>Contraseña: <input type="password" name="test_pass" value="password" style="margin-left: 10px;"></label><br><br>
    <input type="submit" name="test_login" value="Probar Login" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 3px;">
</form>';

if (isset($_POST['test_login'])) {
    echo "<h4>Resultado del Test:</h4>";
    $usuario = $_POST['test_user'];
    $contrasena = $_POST['test_pass'];
    
    try {
        $stmt = $pdo->prepare("SELECT id, nombre_usuario, contrasena FROM usuarios WHERE nombre_usuario = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ Usuario encontrado en la base de datos<br>";
            echo "- ID: {$user['id']}<br>";
            echo "- Nombre: {$user['nombre_usuario']}<br>";
            echo "- Hash en BD: " . substr($user['contrasena'], 0, 20) . "...<br>";
            
            $verifica_login = password_verify($contrasena, $user['contrasena']);
            echo "✅ Verificación de contraseña: " . ($verifica_login ? "CORRECTA" : "INCORRECTA") . "<br>";
            
            if ($verifica_login) {
                $_SESSION['test_usuario_id'] = $user['id'];
                $_SESSION['test_nombre_usuario'] = $user['nombre_usuario'];
                echo "✅ Sesión de prueba creada exitosamente<br>";
                echo "<strong style='color: green;'>🎉 EL LOGIN DEBERÍA FUNCIONAR</strong><br>";
            }
        } else {
            echo "❌ Usuario no encontrado en la base de datos<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error en la consulta: " . $e->getMessage() . "<br>";
    }
}

echo "<br><hr>";
echo "<h3>🔧 Acciones Recomendadas:</h3>";
echo "<a href='login.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; margin-right: 10px;'>Ir al Login</a>";
echo "<a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; margin-right: 10px;'>Ir al Sistema</a>";
echo "<a href='logout.php' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px;'>Cerrar Sesión</a>";
?>