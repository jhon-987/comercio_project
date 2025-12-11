<?php 
/**
 * VISTA: /Vistas/cliente/catalogo.php
 * FINAL: Implementa lógica de filtros, diseño lateral, y marcadores de posición para imágenes (Placeholders).
 */
include 'menu_cliente.php'; 

// Variables necesarias:
// $productos (resultado de la búsqueda/filtro)
// $categorias (lista de todas las categorías disponibles)

// Recuperar parámetros de búsqueda/filtro de la URL (GET)
$search_term = $_GET['search'] ?? '';
$selected_category = $_GET['category'] ?? '';

// Aseguramos que $categorias esté inicializado para evitar errores
$categorias = $categorias ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/cliente-catalogo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
    <style>
        /* Estilos CSS para el layout de filtros */
        .catalogo-layout {
            display: flex;
            gap: 25px;
            margin-top: 20px;
        }
        .sidebar {
            flex: 0 0 250px;
            padding: 15px;
            background-color: #f8f8f8;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .main-content-grid {
            flex-grow: 1;
        }
        .category-list {
            list-style: none;
            padding: 0;
        }
        .category-list li a {
            display: block;
            padding: 8px 10px;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .category-list li a:hover,
        .category-list li a.active {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .search-form input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        /* Estilo para la imagen */
        .producto-imagen {
            width: 100%;
            height: 200px; 
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container main-content">
        <h1><i class="fas fa-th-large"></i> Catálogo de Productos</h1>
        
        <div class="catalogo-layout">
            
            <div class="sidebar">
                
                <h3><i class="fas fa-search"></i> Búsqueda Rápida</h3>
                <form method="GET" action="<?php echo BASE_URL; ?>index.php" class="search-form">
                    <input type="hidden" name="c" value="cliente">
                    <input type="hidden" name="a" value="catalogo">
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($selected_category); ?>">

                    <input type="text" name="search" placeholder="Buscar por nombre..." value="<?php echo htmlspecialchars($search_term); ?>">
                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                        Buscar
                    </button>
                    <?php if (!empty($search_term)): ?>
                        <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=catalogo" class="btn btn-secondary btn-sm mt-2" style="width: 100%;">
                            <i class="fas fa-redo"></i> Limpiar Búsqueda
                        </a>
                    <?php endif; ?>
                </form>
                
                <h3 class="mt-4"><i class="fas fa-tags"></i> Categorías</h3>
                <ul class="category-list">
                    <li>
                        <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=catalogo" 
                           class="<?php echo empty($selected_category) ? 'active' : ''; ?>">
                            Todas (<?php echo $search_term ? 'Filtro Activo' : 'Ver todo'; ?>)
                        </a>
                    </li>
                    <?php foreach ($categorias as $categoria): ?>
                        <li>
                            <?php 
                                $cat_id = $categoria['id'] ?? null;
                                $cat_nombre = $categoria['nombre'] ?? 'Sin Nombre';
                                $category_link = BASE_URL . "index.php?c=cliente&a=catalogo&category=" . urlencode($cat_id);
                                if (!empty($search_term)) {
                                    $category_link .= "&search=" . urlencode($search_term);
                                }
                            ?>
                            <a href="<?php echo htmlspecialchars($category_link); ?>" 
                               class="<?php echo ($selected_category == $cat_id) ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat_nombre); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
            </div>
            <div class="main-content-grid">
                
                <?php if (!empty($search_term) || !empty($selected_category)): ?>
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i> Resultados filtrados.
                        <?php if (!empty($search_term)): ?>
                            **Término:** "<?php echo htmlspecialchars($search_term); ?>".
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($productos) || !is_array($productos)): ?>
                    <p class="alert alert-warning">No se encontraron productos que coincidan con los criterios de búsqueda.</p>
                <?php else: ?>
                    
                    <div class="product-grid">
                        <?php foreach ($productos as $producto): ?>
                            <div class="card producto-card">
                                
                                <?php 
                                    $imagen_db = $producto['imagen_url'] ?? '';
                                    $id_producto = $producto['id'] ?? 0;
                                    
                                    // 1. Ruta base local (Asumimos /public/img/ en la raíz del proyecto web)
                                    // AJUSTA ESTA RUTA SI TUS IMAGENES REALES VAN A OTRA CARPETA
                                    $ruta_base_imagenes = BASE_URL . 'public/img/'; 
                                    
                                    // 2. Determinar la ruta final
                                    // Si la DB tiene una ruta que no es la de nuestro placeholder inicial ni está vacía:
                                    if (!empty($imagen_db) && $imagen_db !== 'url_imagen/default.jpg') {
                                        $src_final = $ruta_base_imagenes . htmlspecialchars($imagen_db);
                                    } else {
                                        // Usar Placeholder si la ruta es inválida, vacía o es el valor por defecto
                                        $src_final = 'https://picsum.photos/seed/' . $id_producto . '/300/200';
                                    }
                                ?>

                                <img 
                                    src="<?php echo htmlspecialchars($src_final); ?>" 
                                    alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                    class="producto-imagen"
                                />
                                <div class="producto-detalle">
                                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                    <?php if (!empty($producto['categoria_nombre'])): ?>
                                        <p class="categoria-tag"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                                    <?php endif; ?>
                                    
                                    <p class="precio">S/. <?php echo number_format($producto['precio'], 2); ?></p>

                                    <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=cliente&a=agregarCarrito">
                                        <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto['id']); ?>">
                                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                        <input type="hidden" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>">

                                        <div class="form-group-inline">
                                            <input type="number" name="cantidad" value="1" min="1" max="<?php echo htmlspecialchars($producto['stock'] ?? '1'); ?>" required class="input-cantidad">
                                            
                                            <?php if (($producto['stock'] ?? 0) > 0): ?>
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa fa-cart-plus"></i> Agregar
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-danger btn-sm" disabled>
                                                    Agotado
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                <?php endif; ?>
            </div>
            </div>
    </div>
</body>
</html>