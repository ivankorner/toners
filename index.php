<?php
require_once 'auth_check.php';
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario de Toners</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .stock-low {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .stock-critical {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .btn-group-sm .btn {
            font-size: 0.875rem;
        }
        .user-info {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 8px 20px;
            margin: 0 15px;
            display: flex;
            align-items: center;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 14px;
        }
        .session-info {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        .logout-btn {
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            background-color: rgba(220, 53, 69, 0.1) !important;
            border-color: #dc3545 !important;
            color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-print me-2"></i>
                Sistema de Inventario de Toners
            </a>
            <div class="navbar-nav ms-auto d-flex align-items-center">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></div>
                        <div class="session-info">
                            <i class="fas fa-clock me-1"></i>
                            Conectado desde: <?php echo date('H:i', $_SESSION['login_time']); ?>
                        </div>
                    </div>
                </div>
                <span class="navbar-text me-3">
                    <i class="fas fa-calendar-alt me-1"></i>
                    <?php echo date('d/m/Y'); ?>
                </span>
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog me-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="fas fa-user-circle me-2"></i>
                                <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <span class="dropdown-item-text">
                                <small class="text-muted">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    Último acceso: <?php echo date('d/m/Y H:i', $_SESSION['ultimo_acceso']); ?>
                                </small>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item logout-btn" href="logout.php" onclick="return confirmarLogout()">
                                <i class="fas fa-sign-out-alt me-2 text-danger"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Mensaje de bienvenida (solo se muestra una vez) -->
        <?php if (!isset($_SESSION['bienvenida_mostrada'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>¡Bienvenido <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>!</strong> 
                Has iniciado sesión exitosamente. Tiempo de conexión: <?php echo date('H:i:s'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php $_SESSION['bienvenida_mostrada'] = true; ?>
        <?php endif; ?>

        <!-- Tabs de navegación -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="carga-tab" data-bs-toggle="tab" data-bs-target="#carga" type="button" role="tab">
                    <i class="fas fa-plus-circle me-1"></i>Carga de Datos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edicion-tab" data-bs-toggle="tab" data-bs-target="#edicion" type="button" role="tab">
                    <i class="fas fa-edit me-1"></i>Editar Toners y Drums
                </button>
            </li>
            <li class="nav-item dropdown" role="presentation">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                    <i class="fas fa-arrows-alt-v me-1"></i>Movimientos
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" data-bs-toggle="tab" data-bs-target="#ingresos" href="#"><i class="fas fa-arrow-up me-1"></i>Ingresos Toners</a></li>
                    <li><a class="dropdown-item" data-bs-toggle="tab" data-bs-target="#egresos" href="#"><i class="fas fa-arrow-down me-1"></i>Egresos Toners</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" data-bs-toggle="tab" data-bs-target="#ingresos-drums" href="#"><i class="fas fa-arrow-up me-1"></i>Ingresos Drums</a></li>
                    <li><a class="dropdown-item" data-bs-toggle="tab" data-bs-target="#egresos-drums" href="#"><i class="fas fa-arrow-down me-1"></i>Egresos Drums</a></li>
                </ul>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button" role="tab">
                    <i class="fas fa-boxes me-1"></i>Stock
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab">
                    <i class="fas fa-users me-1"></i>Usuarios
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Tab Carga de Datos -->
            <div class="tab-pane fade show active" id="carga" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-database me-2"></i>Carga de Datos - Nuevos Toners</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'cargar_toner') {
                            try {
                                $pdo->beginTransaction();
                                
                                // Insertar el toner
                                $stmt = $pdo->prepare("INSERT INTO toners (modelo, detalle, modelo_impresora, implementada, cantidad_actual, cantidad_minima) VALUES (?, ?, ?, ?, ?, ?)");
                                $stmt->execute([$_POST['modelo'], $_POST['detalle'], $_POST['modelo_impresora'], $_POST['implementada'], $_POST['cantidad'], $_POST['cantidad_minima']]);
                                $toner_id = $pdo->lastInsertId();
                                
                                // Insertar el drum asociado si se especificó
                                if (!empty($_POST['drum_cantidad']) || !empty($_POST['drum_cantidad_minima']) || !empty($_POST['drum_modelo'])) {
                                    $drum_cantidad = !empty($_POST['drum_cantidad']) ? $_POST['drum_cantidad'] : 0;
                                    $drum_minima = !empty($_POST['drum_cantidad_minima']) ? $_POST['drum_cantidad_minima'] : 0;
                                    $drum_modelo = !empty($_POST['drum_modelo']) ? $_POST['drum_modelo'] : '';
                                    
                                    $stmt = $pdo->prepare("INSERT INTO drums (toner_id, modelo, cantidad_actual, cantidad_minima) VALUES (?, ?, ?, ?)");
                                    $stmt->execute([$toner_id, $drum_modelo, $drum_cantidad, $drum_minima]);
                                }
                                
                                $pdo->commit();
                                echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Toner' . (!empty($_POST['drum_cantidad']) || !empty($_POST['drum_cantidad_minima']) || !empty($_POST['drum_modelo']) ? ' y drum' : '') . ' agregado exitosamente</div>';
                            } catch(PDOException $e) {
                                $pdo->rollback();
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        
                        // Procesar edición de toner
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'editar_toner') {
                            try {
                                $pdo->beginTransaction();
                                
                                // Actualizar el toner
                                $stmt = $pdo->prepare("UPDATE toners SET modelo = ?, detalle = ?, modelo_impresora = ?, implementada = ?, cantidad_actual = ?, cantidad_minima = ? WHERE id = ?");
                                $stmt->execute([$_POST['modelo'], $_POST['detalle'], $_POST['modelo_impresora'], $_POST['implementada'], $_POST['cantidad'], $_POST['cantidad_minima'], $_POST['toner_id']]);
                                
                                // Procesar drum asociado
                                $stmt = $pdo->prepare("SELECT id FROM drums WHERE toner_id = ?");
                                $stmt->execute([$_POST['toner_id']]);
                                $drum_existente = $stmt->fetch();
                                
                                if (!empty($_POST['drum_modelo']) || !empty($_POST['drum_cantidad']) || !empty($_POST['drum_cantidad_minima'])) {
                                    // Hay datos de drum para procesar
                                    $drum_modelo = !empty($_POST['drum_modelo']) ? $_POST['drum_modelo'] : '';
                                    $drum_cantidad = !empty($_POST['drum_cantidad']) ? $_POST['drum_cantidad'] : 0;
                                    $drum_minima = !empty($_POST['drum_cantidad_minima']) ? $_POST['drum_cantidad_minima'] : 0;
                                    
                                    if ($drum_existente) {
                                        // Actualizar drum existente
                                        $stmt = $pdo->prepare("UPDATE drums SET modelo = ?, cantidad_actual = ?, cantidad_minima = ? WHERE toner_id = ?");
                                        $stmt->execute([$drum_modelo, $drum_cantidad, $drum_minima, $_POST['toner_id']]);
                                    } else {
                                        // Crear nuevo drum
                                        $stmt = $pdo->prepare("INSERT INTO drums (toner_id, modelo, cantidad_actual, cantidad_minima) VALUES (?, ?, ?, ?)");
                                        $stmt->execute([$_POST['toner_id'], $drum_modelo, $drum_cantidad, $drum_minima]);
                                    }
                                } else {
                                    // No hay datos de drum, eliminar si existe
                                    if ($drum_existente) {
                                        $stmt = $pdo->prepare("DELETE FROM drums WHERE toner_id = ?");
                                        $stmt->execute([$_POST['toner_id']]);
                                    }
                                }
                                
                                $pdo->commit();
                                echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Toner y drum actualizado exitosamente</div>';
                            } catch(PDOException $e) {
                                $pdo->rollback();
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        
                        // Procesar eliminación de toner
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'eliminar_toner') {
                            try {
                                $pdo->beginTransaction();
                                
                                // Verificar si tiene movimientos de toner
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM ingresos WHERE modelo_id = ?");
                                $stmt->execute([$_POST['toner_id']]);
                                $ingresos = $stmt->fetchColumn();
                                
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM egresos WHERE modelo_id = ?");
                                $stmt->execute([$_POST['toner_id']]);
                                $egresos = $stmt->fetchColumn();
                                
                                // Verificar si tiene drums con movimientos
                                $stmt = $pdo->prepare("SELECT id FROM drums WHERE toner_id = ?");
                                $stmt->execute([$_POST['toner_id']]);
                                $drum_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                
                                $movimientos_drums = 0;
                                if (!empty($drum_ids)) {
                                    $placeholders = str_repeat('?,', count($drum_ids) - 1) . '?';
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ingresos_drums WHERE drum_id IN ($placeholders)");
                                    $stmt->execute($drum_ids);
                                    $movimientos_drums += $stmt->fetchColumn();
                                    
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM egresos_drums WHERE drum_id IN ($placeholders)");
                                    $stmt->execute($drum_ids);
                                    $movimientos_drums += $stmt->fetchColumn();
                                }
                                
                                if ($ingresos > 0 || $egresos > 0 || $movimientos_drums > 0) {
                                    echo '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>No se puede eliminar: El toner o sus drums tienen movimientos registrados</div>';
                                } else {
                                    $stmt = $pdo->prepare("DELETE FROM toners WHERE id = ?");
                                    $stmt->execute([$_POST['toner_id']]);
                                    echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Toner y drums asociados eliminado exitosamente</div>';
                                }
                                
                                $pdo->commit();
                            } catch(PDOException $e) {
                                $pdo->rollback();
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        
                        // Procesar ingreso de drum
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'registrar_ingreso_drum') {
                            try {
                                $pdo->beginTransaction();
                                
                                // Registrar el ingreso de drum
                                $stmt = $pdo->prepare("INSERT INTO ingresos_drums (fecha_ingreso, drum_id, cantidad) VALUES (?, ?, ?)");
                                $stmt->execute([$_POST['fecha_ingreso'], $_POST['drum_id'], $_POST['cantidad']]);
                                
                                // Actualizar el stock de drum
                                $stmt = $pdo->prepare("UPDATE drums SET cantidad_actual = cantidad_actual + ? WHERE id = ?");
                                $stmt->execute([$_POST['cantidad'], $_POST['drum_id']]);
                                
                                $pdo->commit();
                                echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Ingreso de drum registrado exitosamente</div>';
                            } catch(PDOException $e) {
                                $pdo->rollback();
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        
                        // Procesar egreso de drum
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'registrar_egreso_drum') {
                            try {
                                // Verificar stock disponible de drum
                                $stmt = $pdo->prepare("SELECT cantidad_actual FROM drums WHERE id = ?");
                                $stmt->execute([$_POST['drum_id']]);
                                $stock_actual = $stmt->fetchColumn();
                                
                                if ($stock_actual >= $_POST['cantidad']) {
                                    $pdo->beginTransaction();
                                    
                                    // Registrar el egreso de drum
                                    $stmt = $pdo->prepare("INSERT INTO egresos_drums (fecha_egreso, drum_id, cantidad) VALUES (?, ?, ?)");
                                    $stmt->execute([$_POST['fecha_egreso'], $_POST['drum_id'], $_POST['cantidad']]);
                                    
                                    // Actualizar el stock de drum
                                    $stmt = $pdo->prepare("UPDATE drums SET cantidad_actual = cantidad_actual - ? WHERE id = ?");
                                    $stmt->execute([$_POST['cantidad'], $_POST['drum_id']]);
                                    
                                    $pdo->commit();
                                    echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Egreso de drum registrado exitosamente</div>';
                                } else {
                                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: Stock de drum insuficiente. Stock disponible: ' . $stock_actual . '</div>';
                                }
                            } catch(PDOException $e) {
                                $pdo->rollback();
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        
                        // Procesar crear usuario
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'crear_usuario') {
                            try {
                                // Verificar que el nombre de usuario no exista
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre_usuario = ?");
                                $stmt->execute([$_POST['nombre_usuario']]);
                                $usuario_existe = $stmt->fetchColumn();
                                
                                if ($usuario_existe == 0) {
                                    // Encriptar la contraseña
                                    $contrasena_hash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
                                    
                                    // Insertar nuevo usuario
                                    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, contrasena) VALUES (?, ?)");
                                    $stmt->execute([$_POST['nombre_usuario'], $contrasena_hash]);
                                    
                                    echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Usuario creado exitosamente</div>';
                                } else {
                                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: El nombre de usuario ya existe</div>';
                                }
                            } catch(PDOException $e) {
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        
                        // Procesar editar usuario
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'editar_usuario') {
                            try {
                                // Verificar que el nombre de usuario no exista para otro usuario
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre_usuario = ? AND id != ?");
                                $stmt->execute([$_POST['nombre_usuario'], $_POST['usuario_id']]);
                                $usuario_existe = $stmt->fetchColumn();
                                
                                if ($usuario_existe == 0) {
                                    if (!empty($_POST['contrasena'])) {
                                        // Si se proporciona nueva contraseña, actualizarla
                                        $contrasena_hash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
                                        $stmt = $pdo->prepare("UPDATE usuarios SET nombre_usuario = ?, contrasena = ? WHERE id = ?");
                                        $stmt->execute([$_POST['nombre_usuario'], $contrasena_hash, $_POST['usuario_id']]);
                                    } else {
                                        // Solo actualizar el nombre de usuario
                                        $stmt = $pdo->prepare("UPDATE usuarios SET nombre_usuario = ? WHERE id = ?");
                                        $stmt->execute([$_POST['nombre_usuario'], $_POST['usuario_id']]);
                                    }
                                    
                                    echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Usuario actualizado exitosamente</div>';
                                } else {
                                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: El nombre de usuario ya existe</div>';
                                }
                            } catch(PDOException $e) {
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        
                        // Procesar eliminar usuario
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'eliminar_usuario') {
                            try {
                                // No permitir eliminar el usuario admin (id = 1)
                                if ($_POST['usuario_id'] != 1) {
                                    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
                                    $stmt->execute([$_POST['usuario_id']]);
                                    
                                    echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Usuario eliminado exitosamente</div>';
                                } else {
                                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: No se puede eliminar el usuario administrador</div>';
                                }
                            } catch(PDOException $e) {
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        ?>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="cargar_toner">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="modelo" class="form-label">Modelo del Toner</label>
                                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="modelo_impresora" class="form-label">Modelo de Impresora</label>
                                        <input type="text" class="form-control" id="modelo_impresora" name="modelo_impresora" placeholder="Ej: HP LaserJet Pro M404dn">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="implementada" class="form-label">Implementada (Oficinas/Ubicaciones)</label>
                                        <input type="text" class="form-control" id="implementada" name="implementada" placeholder="Ej: Oficina Central, Recepción, Sala de Reuniones">
                                        <div class="form-text">Especifica en qué oficinas o ubicaciones está instalada esta impresora</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cantidad" class="form-label">Cantidad Inicial</label>
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="detalle" class="form-label">Detalle del Toner</label>
                                        <textarea class="form-control" id="detalle" name="detalle" rows="3" placeholder="Descripción adicional del toner (color, tipo, características especiales)"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cantidad_minima" class="form-label">Cantidad Mínima</label>
                                        <input type="number" class="form-control" id="cantidad_minima" name="cantidad_minima" min="0" required>
                                        <div class="form-text">Alerta cuando el stock sea menor a esta cantidad</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección DRUM -->
                            <hr class="my-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-circle me-2"></i>Drum Asociado (Opcional)</h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="drum_modelo" class="form-label">Drum</label>
                                        <input type="text" class="form-control" id="drum_modelo" name="drum_modelo" placeholder="Ej: DR-3479, Drum para HP LaserJet Pro M404">
                                        <div class="form-text">Especifica el modelo o descripción del drum asociado al toner</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="drum_cantidad" class="form-label">Cantidad Inicial de Drums</label>
                                        <input type="number" class="form-control" id="drum_cantidad" name="drum_cantidad" min="0" placeholder="0">
                                        <div class="form-text">Deja en blanco si no maneja drums</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="drum_cantidad_minima" class="form-label">Cantidad Mínima de Drums</label>
                                        <input type="number" class="form-control" id="drum_cantidad_minima" name="drum_cantidad_minima" min="0" placeholder="0">
                                        <div class="form-text">Alerta cuando el stock de drums sea menor a esta cantidad</div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Toner
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tab Editar Toners y Drums -->
            <div class="tab-pane fade" id="edicion" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Editar/Eliminar Toners y Drums</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Obtener toner para editar si se especifica
                        $toner_editar = null;
                        $drum_editar = null;
                        if (isset($_GET['editar']) && !empty($_GET['editar'])) {
                            $stmt = $pdo->prepare("SELECT * FROM toners WHERE id = ?");
                            $stmt->execute([$_GET['editar']]);
                            $toner_editar = $stmt->fetch();
                            
                            // Obtener drum asociado si existe
                            if ($toner_editar) {
                                $stmt = $pdo->prepare("SELECT * FROM drums WHERE toner_id = ?");
                                $stmt->execute([$toner_editar['id']]);
                                $drum_editar = $stmt->fetch();
                            }
                        }
                        ?>
                        
                        <?php if ($toner_editar): ?>
                        <!-- Formulario de edición -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Editando toner: <strong><?php echo htmlspecialchars($toner_editar['modelo']); ?></strong>
                            <a href="?#edicion" class="btn btn-sm btn-outline-info ms-2">Cancelar</a>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="editar_toner">
                            <input type="hidden" name="toner_id" value="<?php echo $toner_editar['id']; ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="modelo_edit" class="form-label">Modelo del Toner</label>
                                        <input type="text" class="form-control" id="modelo_edit" name="modelo" value="<?php echo htmlspecialchars($toner_editar['modelo']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="modelo_impresora_edit" class="form-label">Modelo de Impresora</label>
                                        <input type="text" class="form-control" id="modelo_impresora_edit" name="modelo_impresora" value="<?php echo htmlspecialchars($toner_editar['modelo_impresora']); ?>" placeholder="Ej: HP LaserJet Pro M404dn">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="implementada_edit" class="form-label">Implementada (Oficinas/Ubicaciones)</label>
                                        <input type="text" class="form-control" id="implementada_edit" name="implementada" value="<?php echo htmlspecialchars($toner_editar['implementada']); ?>" placeholder="Ej: Oficina Central, Recepción, Sala de Reuniones">
                                        <div class="form-text">Especifica en qué oficinas o ubicaciones está instalada esta impresora</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cantidad_edit" class="form-label">Cantidad Actual</label>
                                        <input type="number" class="form-control" id="cantidad_edit" name="cantidad" value="<?php echo $toner_editar['cantidad_actual']; ?>" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="detalle_edit" class="form-label">Detalle del Toner</label>
                                        <textarea class="form-control" id="detalle_edit" name="detalle" rows="3" placeholder="Descripción adicional del toner (color, tipo, características especiales)"><?php echo htmlspecialchars($toner_editar['detalle']); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cantidad_minima_edit" class="form-label">Cantidad Mínima</label>
                                        <input type="number" class="form-control" id="cantidad_minima_edit" name="cantidad_minima" value="<?php echo $toner_editar['cantidad_minima']; ?>" min="0" required>
                                        <div class="form-text">Alerta cuando el stock sea menor a esta cantidad</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección DRUM -->
                            <hr class="my-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-circle me-2"></i>Drum Asociado (Opcional)</h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="drum_modelo_edit" class="form-label">Drum</label>
                                        <input type="text" class="form-control" id="drum_modelo_edit" name="drum_modelo" value="<?php echo $drum_editar ? htmlspecialchars($drum_editar['modelo']) : ''; ?>" placeholder="Ej: DR-3479, Drum para HP LaserJet Pro M404">
                                        <div class="form-text">Especifica el modelo o descripción del drum asociado al toner</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="drum_cantidad_edit" class="form-label">Cantidad Actual de Drums</label>
                                        <input type="number" class="form-control" id="drum_cantidad_edit" name="drum_cantidad" value="<?php echo $drum_editar ? $drum_editar['cantidad_actual'] : ''; ?>" min="0" placeholder="0">
                                        <div class="form-text">Deja en blanco si no maneja drums</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="drum_cantidad_minima_edit" class="form-label">Cantidad Mínima de Drums</label>
                                        <input type="number" class="form-control" id="drum_cantidad_minima_edit" name="drum_cantidad_minima" value="<?php echo $drum_editar ? $drum_editar['cantidad_minima'] : ''; ?>" min="0" placeholder="0">
                                        <div class="form-text">Alerta cuando el stock de drums sea menor a esta cantidad</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Actualizar Toner y Drum
                                </button>
                                <a href="?#edicion" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                        <?php else: ?>
                        <!-- Lista de toners para editar -->
                        <p class="mb-3">Selecciona un toner para editar sus datos y los del drum asociado:</p>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Modelo Toner</th>
                                        <th>Modelo Impresora</th>
                                        <th>Implementada</th>
                                        <th>Stock Toner</th>
                                        <th>Stock Drum</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT t.*, d.modelo as drum_modelo, d.cantidad_actual as drum_cantidad, d.cantidad_minima as drum_minima 
                                                        FROM toners t 
                                                        LEFT JOIN drums d ON t.id = d.toner_id 
                                                        ORDER BY t.modelo");
                                    while ($row = $stmt->fetch()) {
                                        $clase_fila = '';
                                        if ($row['cantidad_actual'] <= 0) {
                                            $clase_fila = 'table-danger';
                                        } elseif ($row['cantidad_actual'] <= $row['cantidad_minima']) {
                                            $clase_fila = 'table-warning';
                                        }
                                        
                                        echo "<tr class='{$clase_fila}'>";
                                        echo "<td><strong>{$row['modelo']}</strong>";
                                        if (!empty($row['detalle'])) {
                                            echo "<br><small class='text-muted'>{$row['detalle']}</small>";
                                        }
                                        echo "</td>";
                                        echo "<td>" . (!empty($row['modelo_impresora']) ? $row['modelo_impresora'] : '<span class="text-muted">-</span>') . "</td>";
                                        echo "<td>" . (!empty($row['implementada']) ? $row['implementada'] : '<span class="text-muted">-</span>') . "</td>";
                                        echo "<td>";
                                        if ($row['cantidad_actual'] <= 0) {
                                            echo "<span class='badge bg-danger'>{$row['cantidad_actual']}</span>";
                                        } elseif ($row['cantidad_actual'] <= $row['cantidad_minima']) {
                                            echo "<span class='badge bg-warning'>{$row['cantidad_actual']}</span>";
                                        } else {
                                            echo "<span class='badge bg-success'>{$row['cantidad_actual']}</span>";
                                        }
                                        echo "<br><small class='text-muted'>Mín: {$row['cantidad_minima']}</small>";
                                        echo "</td>";
                                        echo "<td>";
                                        if (!empty($row['drum_cantidad']) || !empty($row['drum_cantidad'])) {
                                            $drum_nombre = !empty($row['drum_modelo']) ? $row['drum_modelo'] : 'Drum';
                                            if ($row['drum_cantidad'] <= 0) {
                                                echo "<span class='badge bg-danger'>{$row['drum_cantidad']}</span>";
                                            } elseif ($row['drum_cantidad'] <= $row['drum_minima']) {
                                                echo "<span class='badge bg-warning'>{$row['drum_cantidad']}</span>";
                                            } else {
                                                echo "<span class='badge bg-success'>{$row['drum_cantidad']}</span>";
                                            }
                                            echo "<br><small class='text-muted'>{$drum_nombre} (Mín: {$row['drum_minima']})</small>";
                                        } else {
                                            echo "<span class='text-muted'>Sin drum</span>";
                                        }
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<div class='btn-group btn-group-sm' role='group'>";
                                        echo "<a href='?editar={$row['id']}#edicion' class='btn btn-outline-primary'>";
                                        echo "<i class='fas fa-edit me-1'></i>Editar";
                                        echo "</a>";
                                        echo "<button type='button' class='btn btn-outline-danger' onclick='confirmarEliminacion({$row['id']}, \"{$row['modelo']}\")'>";
                                        echo "<i class='fas fa-trash me-1'></i>Eliminar";
                                        echo "</button>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tab Ingresos -->
            <div class="tab-pane fade" id="ingresos" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-arrow-up me-2"></i>Ingresos al Inventario</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'registrar_ingreso') {
                            try {
                                $pdo->beginTransaction();
                                
                                // Registrar el ingreso
                                $stmt = $pdo->prepare("INSERT INTO ingresos (fecha_ingreso, modelo_id, cantidad) VALUES (?, ?, ?)");
                                $stmt->execute([$_POST['fecha_ingreso'], $_POST['modelo_id'], $_POST['cantidad']]);
                                
                                // Actualizar el stock
                                $stmt = $pdo->prepare("UPDATE toners SET cantidad_actual = cantidad_actual + ? WHERE id = ?");
                                $stmt->execute([$_POST['cantidad'], $_POST['modelo_id']]);
                                
                                $pdo->commit();
                                echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Ingreso registrado exitosamente</div>';
                            } catch(PDOException $e) {
                                $pdo->rollback();
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        ?>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="registrar_ingreso">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                                        <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="modelo_id" class="form-label">Modelo de Toner</label>
                                        <select class="form-select" id="modelo_id" name="modelo_id" required>
                                            <option value="">Seleccionar modelo...</option>
                                            <?php
                                            $stmt = $pdo->query("SELECT id, modelo FROM toners ORDER BY modelo");
                                            while ($row = $stmt->fetch()) {
                                                echo "<option value='{$row['id']}'>{$row['modelo']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cantidad_ingreso" class="form-label">Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad_ingreso" name="cantidad" min="1" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Registrar Ingreso
                            </button>
                        </form>
                        
                        <!-- Historial de ingresos recientes -->
                        <hr>
                        <h6><i class="fas fa-history me-2"></i>Ingresos Recientes</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Modelo</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT i.fecha_ingreso, t.modelo, i.cantidad 
                                                        FROM ingresos i 
                                                        JOIN toners t ON i.modelo_id = t.id 
                                                        ORDER BY i.fecha_registro DESC 
                                                        LIMIT 5");
                                    while ($row = $stmt->fetch()) {
                                        echo "<tr>";
                                        echo "<td>" . date('d/m/Y', strtotime($row['fecha_ingreso'])) . "</td>";
                                        echo "<td>{$row['modelo']}</td>";
                                        echo "<td><span class='badge bg-success'>+{$row['cantidad']}</span></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Egresos -->
            <div class="tab-pane fade" id="egresos" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-arrow-down me-2"></i>Egresos del Inventario</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'registrar_egreso') {
                            try {
                                // Verificar stock disponible
                                $stmt = $pdo->prepare("SELECT cantidad_actual FROM toners WHERE id = ?");
                                $stmt->execute([$_POST['modelo_id']]);
                                $stock_actual = $stmt->fetchColumn();
                                
                                if ($stock_actual >= $_POST['cantidad']) {
                                    $pdo->beginTransaction();
                                    
                                    // Registrar el egreso
                                    $stmt = $pdo->prepare("INSERT INTO egresos (fecha_egreso, modelo_id, cantidad) VALUES (?, ?, ?)");
                                    $stmt->execute([$_POST['fecha_egreso'], $_POST['modelo_id'], $_POST['cantidad']]);
                                    
                                    // Actualizar el stock
                                    $stmt = $pdo->prepare("UPDATE toners SET cantidad_actual = cantidad_actual - ? WHERE id = ?");
                                    $stmt->execute([$_POST['cantidad'], $_POST['modelo_id']]);
                                    
                                    $pdo->commit();
                                    echo '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Egreso registrado exitosamente</div>';
                                } else {
                                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: Stock insuficiente. Stock disponible: ' . $stock_actual . '</div>';
                                }
                            } catch(PDOException $e) {
                                $pdo->rollback();
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error: ' . $e->getMessage() . '</div>';
                            }
                        }
                        ?>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="registrar_egreso">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="fecha_egreso" class="form-label">Fecha de Egreso</label>
                                        <input type="date" class="form-control" id="fecha_egreso" name="fecha_egreso" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="modelo_id_egreso" class="form-label">Modelo de Toner</label>
                                        <select class="form-select" id="modelo_id_egreso" name="modelo_id" required onchange="mostrarStock(this.value)">
                                            <option value="">Seleccionar modelo...</option>
                                            <?php
                                            $stmt = $pdo->query("SELECT id, modelo, cantidad_actual FROM toners ORDER BY modelo");
                                            while ($row = $stmt->fetch()) {
                                                echo "<option value='{$row['id']}' data-stock='{$row['cantidad_actual']}'>{$row['modelo']} (Stock: {$row['cantidad_actual']})</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cantidad_egreso" class="form-label">Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad_egreso" name="cantidad" min="1" required>
                                        <div class="form-text" id="stock-info"></div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-minus me-2"></i>Registrar Egreso
                            </button>
                        </form>
                        
                        <!-- Historial de egresos recientes -->
                        <hr>
                        <h6><i class="fas fa-history me-2"></i>Egresos Recientes</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Modelo</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT e.fecha_egreso, t.modelo, e.cantidad 
                                                        FROM egresos e 
                                                        JOIN toners t ON e.modelo_id = t.id 
                                                        ORDER BY e.fecha_registro DESC 
                                                        LIMIT 5");
                                    while ($row = $stmt->fetch()) {
                                        echo "<tr>";
                                        echo "<td>" . date('d/m/Y', strtotime($row['fecha_egreso'])) . "</td>";
                                        echo "<td>{$row['modelo']}</td>";
                                        echo "<td><span class='badge bg-danger'>-{$row['cantidad']}</span></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Ingresos Drums -->
            <div class="tab-pane fade" id="ingresos-drums" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-arrow-up me-2"></i>Ingresos de Drums al Inventario</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="registrar_ingreso_drum">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="fecha_ingreso_drum" class="form-label">Fecha de Ingreso</label>
                                        <input type="date" class="form-control" id="fecha_ingreso_drum" name="fecha_ingreso" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="drum_id" class="form-label">Drum (Toner)</label>
                                        <select class="form-select" id="drum_id" name="drum_id" required>
                                            <option value="">Seleccionar drum...</option>
                                            <?php
                                            $stmt = $pdo->query("SELECT d.id, t.modelo, d.modelo as drum_modelo FROM drums d JOIN toners t ON d.toner_id = t.id ORDER BY t.modelo");
                                            while ($row = $stmt->fetch()) {
                                                $drum_desc = !empty($row['drum_modelo']) ? " ({$row['drum_modelo']})" : "";
                                                echo "<option value='{$row['id']}'>Drum para {$row['modelo']}{$drum_desc}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cantidad_ingreso_drum" class="form-label">Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad_ingreso_drum" name="cantidad" min="1" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Registrar Ingreso de Drum
                            </button>
                        </form>
                        
                        <!-- Historial de ingresos recientes de drums -->
                        <hr>
                        <h6><i class="fas fa-history me-2"></i>Ingresos Recientes de Drums</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Toner (Drum)</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT id.fecha_ingreso, t.modelo, d.modelo as drum_modelo, id.cantidad 
                                                        FROM ingresos_drums id 
                                                        JOIN drums d ON id.drum_id = d.id 
                                                        JOIN toners t ON d.toner_id = t.id 
                                                        ORDER BY id.fecha_registro DESC 
                                                        LIMIT 5");
                                    while ($row = $stmt->fetch()) {
                                        $drum_desc = !empty($row['drum_modelo']) ? " ({$row['drum_modelo']})" : "";
                                        echo "<tr>";
                                        echo "<td>" . date('d/m/Y', strtotime($row['fecha_ingreso'])) . "</td>";
                                        echo "<td>Drum para {$row['modelo']}{$drum_desc}</td>";
                                        echo "<td><span class='badge bg-success'>+{$row['cantidad']}</span></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Egresos Drums -->
            <div class="tab-pane fade" id="egresos-drums" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-arrow-down me-2"></i>Egresos de Drums del Inventario</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="registrar_egreso_drum">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="fecha_egreso_drum" class="form-label">Fecha de Egreso</label>
                                        <input type="date" class="form-control" id="fecha_egreso_drum" name="fecha_egreso" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="drum_id_egreso" class="form-label">Drum (Toner)</label>
                                        <select class="form-select" id="drum_id_egreso" name="drum_id" required onchange="mostrarStockDrum(this.value)">
                                            <option value="">Seleccionar drum...</option>
                                            <?php
                                            $stmt = $pdo->query("SELECT d.id, t.modelo, d.modelo as drum_modelo, d.cantidad_actual FROM drums d JOIN toners t ON d.toner_id = t.id ORDER BY t.modelo");
                                            while ($row = $stmt->fetch()) {
                                                $drum_desc = !empty($row['drum_modelo']) ? " ({$row['drum_modelo']})" : "";
                                                echo "<option value='{$row['id']}' data-stock='{$row['cantidad_actual']}'>Drum para {$row['modelo']}{$drum_desc} (Stock: {$row['cantidad_actual']})</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cantidad_egreso_drum" class="form-label">Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad_egreso_drum" name="cantidad" min="1" required>
                                        <div class="form-text" id="stock-info-drum"></div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-minus me-2"></i>Registrar Egreso de Drum
                            </button>
                        </form>
                        
                        <!-- Historial de egresos recientes de drums -->
                        <hr>
                        <h6><i class="fas fa-history me-2"></i>Egresos Recientes de Drums</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Toner (Drum)</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT ed.fecha_egreso, t.modelo, d.modelo as drum_modelo, ed.cantidad 
                                                        FROM egresos_drums ed 
                                                        JOIN drums d ON ed.drum_id = d.id 
                                                        JOIN toners t ON d.toner_id = t.id 
                                                        ORDER BY ed.fecha_registro DESC 
                                                        LIMIT 5");
                                    while ($row = $stmt->fetch()) {
                                        $drum_desc = !empty($row['drum_modelo']) ? " ({$row['drum_modelo']})" : "";
                                        echo "<tr>";
                                        echo "<td>" . date('d/m/Y', strtotime($row['fecha_egreso'])) . "</td>";
                                        echo "<td>Drum para {$row['modelo']}{$drum_desc}</td>";
                                        echo "<td><span class='badge bg-danger'>-{$row['cantidad']}</span></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Stock -->
            <div class="tab-pane fade" id="stock" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Stock Actual de Toners</h5>
                            </div>
                            <div class="col-auto">
                                <a href="generar_pdf_inventario.php" target="_blank" class="btn btn-success btn-sm me-2">
                                    <i class="fas fa-file-pdf me-2"></i>Ver Reporte Completo
                                </a>
                                <a href="generar_pdf_inventario.php?pdf=1" target="_blank" class="btn btn-danger btn-sm">
                                    <i class="fas fa-download me-2"></i>Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            $stmt = $pdo->query("SELECT t.*, d.id as drum_id, d.modelo as drum_modelo, d.cantidad_actual as drum_cantidad, d.cantidad_minima as drum_minima 
                                               FROM toners t 
                                               LEFT JOIN drums d ON t.id = d.toner_id 
                                               ORDER BY t.modelo");
                            while ($row = $stmt->fetch()) {
                                $clase_alerta = '';
                                $icono_alerta = '';
                                
                                if ($row['cantidad_actual'] <= 0) {
                                    $clase_alerta = 'stock-critical';
                                    $icono_alerta = '<i class="fas fa-exclamation-triangle text-danger me-2"></i>';
                                } elseif ($row['cantidad_actual'] <= $row['cantidad_minima']) {
                                    $clase_alerta = 'stock-low';
                                    $icono_alerta = '<i class="fas fa-exclamation-circle text-warning me-2"></i>';
                                }
                                
                                echo "<div class='col-md-6 col-lg-4 mb-3'>";
                                echo "<div class='card {$clase_alerta}'>";
                                echo "<div class='card-body'>";
                                echo "<h6 class='card-title'>{$icono_alerta}{$row['modelo']}</h6>";
                                if (!empty($row['modelo_impresora'])) {
                                    echo "<p class='card-text text-primary mb-1'><i class='fas fa-print me-1'></i><strong>{$row['modelo_impresora']}</strong></p>";
                                }
                                if (!empty($row['implementada'])) {
                                    echo "<p class='card-text text-info mb-2'><i class='fas fa-map-marker-alt me-1'></i><small>{$row['implementada']}</small></p>";
                                }
                                echo "<p class='card-text text-muted'>{$row['detalle']}</p>";
                                
                                // Mostrar stock de toner
                                echo "<div class='d-flex justify-content-between align-items-center mb-2'>";
                                echo "<span><strong>Toner: {$row['modelo']}</strong></span>";
                                echo "<div>";
                                echo "<span class='h5 mb-0'>{$row['cantidad_actual']}</span>";
                                echo "<small class='text-muted ms-2'>Mín: {$row['cantidad_minima']}</small>";
                                echo "</div>";
                                echo "</div>";
                                
                                // Mostrar stock de drum si existe
                                if (!empty($row['drum_id'])) {
                                    $drum_clase = '';
                                    $drum_icono = '';
                                    if ($row['drum_cantidad'] <= 0) {
                                        $drum_clase = 'text-danger';
                                        $drum_icono = '<i class="fas fa-exclamation-triangle text-danger me-1"></i>';
                                    } elseif ($row['drum_cantidad'] <= $row['drum_minima']) {
                                        $drum_clase = 'text-warning';
                                        $drum_icono = '<i class="fas fa-exclamation-circle text-warning me-1"></i>';
                                    }
                                    
                                    $drum_titulo = !empty($row['drum_modelo']) ? $row['drum_modelo'] : 'Drum';
                                    
                                    echo "<div class='d-flex justify-content-between align-items-center {$drum_clase}'>";
                                    echo "<span><strong>{$drum_icono}Drum: {$drum_titulo}</strong></span>";
                                    echo "<div>";
                                    echo "<span class='h6 mb-0'>{$row['drum_cantidad']}</span>";
                                    echo "<small class='text-muted ms-2'>Mín: {$row['drum_minima']}</small>";
                                    echo "</div>";
                                    echo "</div>";
                                } else {
                                    echo "<div class='d-flex justify-content-between align-items-center text-muted'>";
                                    echo "<span><strong>Drum:</strong></span>";
                                    echo "<span>No configurado</span>";
                                    echo "</div>";
                                }
                                
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                        
                        <!-- Resumen del stock -->
                        <hr>
                    
                        <div class="row text-center">
                            <?php
                            $stats = $pdo->query("SELECT 
                                COUNT(*) as total_modelos,
                                SUM(cantidad_actual) as total_stock,
                                COUNT(CASE WHEN cantidad_actual <= cantidad_minima THEN 1 END) as alertas_stock
                                FROM toners")->fetch();
                                
                            $drum_stats = $pdo->query("SELECT 
                                COUNT(*) as total_drums,
                                SUM(cantidad_actual) as total_stock_drums,
                                COUNT(CASE WHEN cantidad_actual <= cantidad_minima THEN 1 END) as alertas_drums
                                FROM drums")->fetch();
                            ?>
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5><?php echo $stats['total_modelos']; ?></h5>
                                        <p class="mb-0">Total Modelos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5><?php echo $stats['total_stock']; ?></h5>
                                        <p class="mb-0">Total Toners</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5><?php echo $drum_stats['total_stock_drums']; ?></h5>
                                        <p class="mb-0">Total Drums</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5><?php echo ($stats['alertas_stock'] + $drum_stats['alertas_drums']); ?></h5>
                                        <p class="mb-0">Alertas Totales</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pestaña Usuarios -->
            <div class="tab-pane fade" id="usuarios" role="tabpanel" aria-labelledby="usuarios-tab">
                <div class="row">
                    <!-- Crear Usuario -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-user-plus me-2"></i>Crear Usuario
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="crear_usuario">
                                    <div class="mb-3">
                                        <label for="nombre_usuario_crear" class="form-label">Nombre de Usuario</label>
                                        <input type="text" class="form-control" id="nombre_usuario_crear" name="nombre_usuario" required maxlength="50">
                                    </div>
                                    <div class="mb-3">
                                        <label for="contrasena_crear" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="contrasena_crear" name="contrasena" required minlength="6">
                                        <div class="form-text">La contraseña debe tener al menos 6 caracteres</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Crear Usuario
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de Usuarios -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <i class="fas fa-users me-2"></i>Lista de Usuarios
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Usuario</th>
                                                <th>Creado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {
                                                $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY fecha_creacion DESC");
                                                while($usuario = $stmt->fetch()) {
                                                    echo '<tr>';
                                                    echo '<td>' . $usuario['id'] . '</td>';
                                                    echo '<td>' . htmlspecialchars($usuario['nombre_usuario']) . '</td>';
                                                    echo '<td>' . date('d/m/Y', strtotime($usuario['fecha_creacion'])) . '</td>';
                                                    echo '<td>';
                                                    
                                                    // Botón editar
                                                    echo '<button type="button" class="btn btn-sm btn-warning me-1" onclick="editarUsuario(' . 
                                                         $usuario['id'] . ', \'' . htmlspecialchars($usuario['nombre_usuario']) . '\')" title="Editar">';
                                                    echo '<i class="fas fa-edit"></i>';
                                                    echo '</button>';
                                                    
                                                    // Botón eliminar (solo si no es el admin)
                                                    if ($usuario['id'] != 1) {
                                                        echo '<button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminacionUsuario(' . 
                                                             $usuario['id'] . ', \'' . htmlspecialchars($usuario['nombre_usuario']) . '\')" title="Eliminar">';
                                                        echo '<i class="fas fa-trash"></i>';
                                                        echo '</button>';
                                                    } else {
                                                        echo '<span class="badge bg-secondary">Admin</span>';
                                                    }
                                                    
                                                    echo '</td>';
                                                    echo '</tr>';
                                                }
                                            } catch(PDOException $e) {
                                                echo '<tr><td colspan="4" class="text-danger">Error al cargar usuarios: ' . $e->getMessage() . '</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal para Editar Usuario -->
                <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEditarUsuarioLabel">
                                    <i class="fas fa-user-edit me-2"></i>Editar Usuario
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <form method="POST" id="formEditarUsuario">
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="editar_usuario">
                                    <input type="hidden" name="usuario_id" id="usuario_id_editar">
                                    <div class="mb-3">
                                        <label for="nombre_usuario_editar" class="form-label">Nombre de Usuario</label>
                                        <input type="text" class="form-control" id="nombre_usuario_editar" name="nombre_usuario" required maxlength="50">
                                    </div>
                                    <div class="mb-3">
                                        <label for="contrasena_editar" class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control" id="contrasena_editar" name="contrasena" minlength="6">
                                        <div class="form-text">Dejar en blanco para mantener la contraseña actual</div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-2"></i>Actualizar Usuario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function mostrarStock(modeloId) {
            const select = document.getElementById('modelo_id_egreso');
            const stockInfo = document.getElementById('stock-info');
            const cantidadInput = document.getElementById('cantidad_egreso');
            
            if (modeloId) {
                const option = select.querySelector(`option[value="${modeloId}"]`);
                const stock = option.getAttribute('data-stock');
                stockInfo.textContent = `Stock disponible: ${stock}`;
                cantidadInput.max = stock;
            } else {
                stockInfo.textContent = '';
                cantidadInput.removeAttribute('max');
            }
        }
        
        function mostrarStockDrum(drumId) {
            const select = document.getElementById('drum_id_egreso');
            const stockInfo = document.getElementById('stock-info-drum');
            const cantidadInput = document.getElementById('cantidad_egreso_drum');
            
            if (drumId) {
                const option = select.querySelector(`option[value="${drumId}"]`);
                const stock = option.getAttribute('data-stock');
                stockInfo.textContent = `Stock disponible: ${stock}`;
                cantidadInput.max = stock;
            } else {
                stockInfo.textContent = '';
                cantidadInput.removeAttribute('max');
            }
        }
        
        function confirmarEliminacion(id, modelo) {
            if (confirm(`¿Estás seguro de que deseas eliminar el toner "${modelo}"?\n\nEsta acción no se puede deshacer. El toner solo se puede eliminar si no tiene movimientos registrados`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'eliminar_toner';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'toner_id';
                idInput.value = id;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function editarUsuario(id, nombreUsuario) {
            document.getElementById('usuario_id_editar').value = id;
            document.getElementById('nombre_usuario_editar').value = nombreUsuario;
            document.getElementById('contrasena_editar').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
            modal.show();
        }
        
        function confirmarEliminacionUsuario(id, nombreUsuario) {
            if (confirm(`¿Estás seguro de que deseas eliminar el usuario "${nombreUsuario}"?\n\nEsta acción no se puede deshacer.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'eliminar_usuario';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'usuario_id';
                idInput.value = id;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                                              form.submit();
            }
        }
        
        // Activar la pestaña correspondiente si hay un parámetro de edición en la URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('editar')) {
                const edicionTab = new bootstrap.Tab(document.getElementById('edicion-tab'));
                edicionTab.show();
            }
        });
        
        // Función para confirmar cierre de sesión
        function confirmarLogout() {
            return confirm('¿Está seguro de que desea cerrar la sesión?');
        }
    </script>
</body>
</html>
