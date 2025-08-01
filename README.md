# Sistema de Inventario de Toners

Sistema web desarrollado en PHP y Bootstrap para controlar el inventario de cartuchos de toners de impresoras.

## Características

- **Carga de Datos**: Permite agregar nuevos modelos de toners con sus detalles y cantidades mínimas
- **Ingresos**: Registra la entrada de toners al inventario
- **Egresos**: Registra la salida de toners del inventario
- **Stock**: Muestra el inventario actual con alertas de stock bajo
- **Base de Datos**: Sistema completo con MySQL para almacenar todos los datos

## Instalación

1. Asegúrate de tener XAMPP instalado y funcionando
2. Copia todos los archivos a la carpeta `htdocs/toners`
3. Importa la base de datos ejecutando el archivo `database.sql` en phpMyAdmin o MySQL
4. Configura la conexión a la base de datos en `config/database.php` si es necesario
5. Accede al sistema desde `http://localhost/toners`

## Estructura del Proyecto

```
toners/
├── index.php              # Archivo principal del sistema
├── config/
│   └── database.php       # Configuración de la base de datos
└── database.sql          # Script de creación de la base de datos
```

## Funcionalidades

### Carga de Datos
- Agregar nuevos modelos de toners
- Definir cantidades iniciales y mínimas
- Descripción detallada de cada toner

### Ingresos
- Registrar nuevas entradas de stock
- Actualización automática del inventario
- Historial de ingresos recientes

### Egresos
- Registrar salidas de stock
- Validación de stock disponible
- Actualización automática del inventario
- Historial de egresos recientes

### Stock
- Visualización del inventario actual
- Alertas visuales para stock bajo o crítico
- Estadísticas generales del inventario

## Tecnologías Utilizadas

- PHP 7.4+
- MySQL
- Bootstrap 5.1.3
- Font Awesome 6.0
- JavaScript (vanilla)

## Requisitos del Sistema

- XAMPP (Apache + MySQL + PHP)
- Navegador web moderno
- PHP 7.4 o superior
- MySQL 5.7 o superior
