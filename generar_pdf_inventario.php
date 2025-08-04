<?php
require_once 'config/database.php';

// Verificar si se debe generar PDF o mostrar HTML
$generar_pdf = isset($_GET['pdf']) && $_GET['pdf'] == '1';

if ($generar_pdf) {
    // Configurar headers para PDF usando wkhtmltopdf o similar
    // Por ahora, mostraremos una versión optimizada para imprimir
    header('Content-Type: text/html; charset=UTF-8');
} else {
    header('Content-Type: text/html; charset=UTF-8');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inventario de Toners - <?php echo date('d/m/Y'); ?></title>
    <style>
        <?php if ($generar_pdf): ?>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
        <?php endif; ?>
        
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px;
            margin: <?php echo $generar_pdf ? '10px' : '20px'; ?>;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }
        .summary h3 {
            margin-top: 0;
            color: #495057;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 3px;
            border: 1px solid #dee2e6;
        }
        .summary-item .number {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
        }
        .summary-item .label {
            font-size: 11px;
            color: #6c757d;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: <?php echo $generar_pdf ? '10px' : '12px'; ?>;
        }
        th, td {
            border: 1px solid #ddd;
            padding: <?php echo $generar_pdf ? '4px' : '8px'; ?>;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        .alert-low {
            background-color: #fff3cd;
            color: #856404;
        }
        .alert-critical {
            background-color: #f8d7da;
            color: #721c24;
        }
        .stock-normal {
            color: #155724;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        .drum-info {
            font-size: 10px;
            color: #6c757d;
            font-style: italic;
        }
        .btn-group {
            margin-bottom: 20px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #1e7e34;
        }
        .alerts-section {
            margin-top: 30px;
            border: 2px solid #dc3545;
            border-radius: 5px;
            padding: 15px;
            background-color: #f8d7da;
        }
        .alerts-section h3 {
            color: #721c24;
            margin-top: 0;
        }
        .alert-list {
            margin: 10px 0;
            padding-left: 20px;
        }
        .alert-list li {
            margin: 5px 0;
        }
    </style>
    <?php if ($generar_pdf): ?>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
    <?php endif; ?>
</head>
<body>
    <?php if (!$generar_pdf): ?>
    <div class="btn-group no-print">
        <a href="?pdf=1" class="btn btn-success" target="_blank">
            🖨️ Imprimir / Guardar como PDF
        </a>
        <a href="javascript:history.back()" class="btn">
            ⬅️ Volver al Sistema
        </a>
    </div>
    <?php endif; ?>

    <div class="header">
        <h1>📋 INVENTARIO DE TONERS</h1>
        <p>Reporte generado el: <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>Sistema de Gestión de Inventario</p>
    </div>

    <?php
    try {
        // Obtener estadísticas generales
        $stats = $pdo->query("SELECT 
            COUNT(*) as total_modelos,
            SUM(cantidad_actual) as total_stock,
            COUNT(CASE WHEN cantidad_actual <= cantidad_minima THEN 1 END) as alertas_stock,
            COUNT(CASE WHEN cantidad_actual <= 0 THEN 1 END) as sin_stock
            FROM toners")->fetch();
            
        $drum_stats = $pdo->query("SELECT 
            COUNT(*) as total_drums,
            SUM(cantidad_actual) as total_stock_drums,
            COUNT(CASE WHEN cantidad_actual <= cantidad_minima THEN 1 END) as alertas_drums,
            COUNT(CASE WHEN cantidad_actual <= 0 THEN 1 END) as drums_sin_stock
            FROM drums")->fetch();
    ?>

    <div class="summary">
        <h3>📊 Resumen del Inventario</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="number"><?php echo $stats['total_modelos']; ?></div>
                <div class="label">Total Modelos</div>
            </div>
            <div class="summary-item">
                <div class="number"><?php echo $stats['total_stock']; ?></div>
                <div class="label">Total Toners</div>
            </div>
            <div class="summary-item">
                <div class="number"><?php echo $drum_stats['total_stock_drums']; ?></div>
                <div class="label">Total Drums</div>
            </div>
            <div class="summary-item">
                <div class="number"><?php echo ($stats['alertas_stock'] + $drum_stats['alertas_drums']); ?></div>
                <div class="label">Alertas Totales</div>
            </div>
        </div>
    </div>

    <h3>📦 Detalle del Inventario</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 22%;">Modelo Toner</th>
                <th style="width: 18%;">Impresora</th>
                <th style="width: 15%;">Ubicación</th>
                <th style="width: 10%;">Stock Toner</th>
                <th style="width: 8%;">Mín.</th>
                <th style="width: 12%;">Drum</th>
                <th style="width: 8%;">Stock</th>
                <th style="width: 7%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT t.*, d.id as drum_id, d.modelo as drum_modelo, d.cantidad_actual as drum_cantidad, d.cantidad_minima as drum_minima 
                               FROM toners t 
                               LEFT JOIN drums d ON t.id = d.toner_id 
                               ORDER BY t.modelo");
            
            while ($row = $stmt->fetch()) {
                // Determinar clases de alerta para toner
                $toner_class = '';
                $toner_status = '';
                if ($row['cantidad_actual'] <= 0) {
                    $toner_class = 'alert-critical';
                    $toner_status = '⚠️ CRÍTICO';
                } elseif ($row['cantidad_actual'] <= $row['cantidad_minima']) {
                    $toner_class = 'alert-low';
                    $toner_status = '⚠️ BAJO';
                } else {
                    $toner_class = 'stock-normal';
                    $toner_status = '✅ OK';
                }
                
                // Determinar clases de alerta para drum
                $drum_class = '';
                $drum_status = '';
                if (!empty($row['drum_id'])) {
                    if ($row['drum_cantidad'] <= 0) {
                        $drum_class = 'alert-critical';
                        $drum_status = '⚠️';
                    } elseif ($row['drum_cantidad'] <= $row['drum_minima']) {
                        $drum_class = 'alert-low';
                        $drum_status = '⚠️';
                    } else {
                        $drum_class = 'stock-normal';
                        $drum_status = '✅';
                    }
                }
                
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($row['modelo']) . "</strong>";
                if (!empty($row['detalle']) && !$generar_pdf) {
                    echo "<br><small>" . htmlspecialchars(substr($row['detalle'], 0, 40)) . "...</small>";
                }
                echo "</td>";
                echo "<td>" . htmlspecialchars($row['modelo_impresora'] ?? 'N/A') . "</td>";
                echo "<td><small>" . htmlspecialchars(substr($row['implementada'] ?? 'N/A', 0, 30)) . "</small></td>";
                echo "<td class='{$toner_class}' style='text-align: center;'><strong>" . $row['cantidad_actual'] . "</strong></td>";
                echo "<td style='text-align: center;'>" . $row['cantidad_minima'] . "</td>";
                
                if (!empty($row['drum_id'])) {
                    $drum_name = !empty($row['drum_modelo']) ? $row['drum_modelo'] : 'Drum';
                    echo "<td>" . htmlspecialchars(substr($drum_name, 0, 15)) . "</td>";
                    echo "<td class='{$drum_class}' style='text-align: center;'><strong>" . $row['drum_cantidad'] . "</strong></td>";
                    echo "<td style='text-align: center;'>{$drum_status}</td>";
                } else {
                    echo "<td class='drum-info'>No config.</td>";
                    echo "<td class='drum-info' style='text-align: center;'>-</td>";
                    echo "<td style='text-align: center;'>-</td>";
                }
                
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
    // Sección de alertas si existen
    $alertas = $pdo->query("SELECT modelo, cantidad_actual, cantidad_minima FROM toners 
                           WHERE cantidad_actual <= cantidad_minima 
                           ORDER BY cantidad_actual ASC")->fetchAll();
    
    $alertas_drums = $pdo->query("SELECT d.modelo as drum_modelo, d.cantidad_actual, d.cantidad_minima, t.modelo as toner_modelo 
                                 FROM drums d 
                                 INNER JOIN toners t ON d.toner_id = t.id 
                                 WHERE d.cantidad_actual <= d.cantidad_minima 
                                 ORDER BY d.cantidad_actual ASC")->fetchAll();
    
    if (!empty($alertas) || !empty($alertas_drums)) {
        echo "<div class='alerts-section'>";
        echo "<h3>⚠️ ALERTAS DE STOCK BAJO</h3>";
        
        if (!empty($alertas)) {
            echo "<h4>🖨️ Toners con Stock Bajo:</h4>";
            echo "<ul class='alert-list'>";
            foreach ($alertas as $alerta) {
                echo "<li><strong>" . htmlspecialchars($alerta['modelo']) . "</strong> - Stock: " . $alerta['cantidad_actual'] . " (Mín: " . $alerta['cantidad_minima'] . ")</li>";
            }
            echo "</ul>";
        }
        
        if (!empty($alertas_drums)) {
            echo "<h4>🥁 Drums con Stock Bajo:</h4>";
            echo "<ul class='alert-list'>";
            foreach ($alertas_drums as $alerta) {
                $drum_name = !empty($alerta['drum_modelo']) ? $alerta['drum_modelo'] : 'Drum';
                echo "<li><strong>" . htmlspecialchars($drum_name) . "</strong> (para " . htmlspecialchars($alerta['toner_modelo']) . ") - Stock: " . $alerta['cantidad_actual'] . " (Mín: " . $alerta['cantidad_minima'] . ")</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
    }
    
    } catch(PDOException $e) {
        echo "<div style='color: red; padding: 20px; border: 1px solid red; background: #ffebee;'>";
        echo "<h3>Error al generar el reporte</h3>";
        echo "<p>No se pudo acceder a la base de datos: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    ?>

    <div class="footer">
        <p><strong>Sistema de Gestión de Inventario de Toners</strong></p>
        <p>Reporte generado automáticamente el <?php echo date('d/m/Y \a \l\a\s H:i:s'); ?></p>
        <p>© <?php echo date('Y'); ?> Todos los derechos reservados.</p>
    </div>
</body>
</html>
