<?php
/**
 * VISTA: /Vistas/layouts/header.php
 * Propósito: Inicia la estructura HTML, el head y la conexión CSS/FA.
 * REQUERIDA: BASE_URL (definida en index.php)
 */
$titulo_pagina = $titulo_pagina ?? 'Comercio Master';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <?php if (!empty($page_css) && is_array($page_css)): ?>
        <?php foreach ($page_css as $css): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL . ltrim($css, '/'); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div id="main-wrapper"> 
    ```

### B. `/Vistas/layouts/footer.php`

```php
<?php
/**
 * VISTA: /Vistas/layouts/footer.php
 * Propósito: Finaliza la estructura HTML.
 */
?>
    </div> <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Comercio Project Master. Todos los derechos reservados.</p>
        </div>
    </footer>
    <script src="<?php echo BASE_URL; ?>public/js/funciones.js"></script>
</body>
</html>