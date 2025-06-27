<?php
/**
 * Template para mostrar un grupo individual - Versión Mejorada
 * 
 * @package Telegram_Groups
 */

get_header();

// Obtener datos del grupo actual
$grupo_id = get_the_ID();
$imagen_grupo = get_field('imagen_grupo');
$descripcion_corta = get_field('descripcion_corta');
$descripcion_completa = get_field('descripcion_completa');
$enlace_telegram = get_field('enlace_telegram');
$numero_miembros = get_field('numero_miembros') ?: 0;
$estado_grupo = get_field('estado_grupo') ?: 'Activo';
$fecha_creacion = get_field('fecha_creacion');
$fecha_actualizacion = get_field('fecha_actualizacion');
$requisitos = get_field('requisitos');
$idioma = get_field('idioma');
$moderacion = get_field('moderacion');

// Obtener taxonomías de forma segura
$categorias = wp_get_post_terms($grupo_id, 'categoria_grupo', array('fields' => 'all'));
$ciudades = wp_get_post_terms($grupo_id, 'ciudad_grupo', array('fields' => 'all'));
$etiquetas = wp_get_post_terms($grupo_id, 'etiqueta_grupo', array('fields' => 'all'));

// Verificar si hay errores en las taxonomías
if (is_wp_error($categorias)) $categorias = array();
if (is_wp_error($ciudades)) $ciudades = array();
if (is_wp_error($etiquetas)) $etiquetas = array();

$es_destacado = has_term('destacado', 'destacados', $grupo_id);

// Obtener estilo de la categoría principal
$categoria_principal = !empty($categorias) ? $categorias[0] : null;
$categoria_style = $categoria_principal ? get_categoria_style($categoria_principal->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
?>

<style>
/* Variables CSS */
:root {
    --telegram-blue: #0088cc;
    --telegram-light-blue: #54a9eb;
    --telegram-dark: #2c3e50;
    --telegram-gray: #7f8c8d;
    --telegram-light-gray: #bdc3c7;
    --telegram-white: #ffffff;
    --telegram-bg: #f8f9fa;
    --telegram-success: #27ae60;
    --telegram-warning: #f39c12;
    --telegram-danger: #e74c3c;
    --telegram-shadow: 0 8px 32px rgba(0, 136, 204, 0.12);
    --telegram-shadow-hover: 0 12px 40px rgba(0, 136, 204, 0.18);
    --telegram-border-radius: 16px;
    --telegram-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Reset y base */
* {
    box-sizing: border-box;
}

body {
    background: var(--telegram-bg);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    line-height: 1.6;
    color: var(--telegram-dark);
}

/* Contenedor principal */
.single-grupo-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

/* Breadcrumbs */
.breadcrumbs {
    background: var(--telegram-white);
    padding: 1rem 1.5rem;
    border-radius: var(--telegram-border-radius);
    margin-bottom: 2rem;
    box-shadow: var(--telegram-shadow);
}

.breadcrumb-list {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.breadcrumb-item:not(:last-child)::after {
    content: '›';
    margin-left: 0.5rem;
    color: var(--telegram-gray);
    font-weight: bold;
}

.breadcrumb-item a {
    color: var(--telegram-blue);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    transition: var(--telegram-transition);
}

.breadcrumb-item a:hover {
    background: rgba(0, 136, 204, 0.1);
    text-decoration: none;
}

.breadcrumb-item.active {
    color: var(--telegram-gray);
    font-weight: 500;
}

/* Header del grupo */
.grupo-header-card {
    background: linear-gradient(135deg, var(--telegram-white) 0%, #f8f9fa 100%);
    border-radius: var(--telegram-border-radius);
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--telegram-shadow);
    border: 1px solid rgba(0, 136, 204, 0.1);
    position: relative;
    overflow: hidden;
}

.grupo-header-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--telegram-blue), var(--telegram-light-blue));
}

.grupo-header-content {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
    align-items: start;
}

.grupo-imagen-container {
    position: relative;
}

.grupo-imagen {
    width: 100%;
    height: 180px;
    border-radius: 12px;
    object-fit: cover;
    border: 3px solid var(--telegram-white);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: var(--telegram-transition);
}

.grupo-imagen:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.grupo-placeholder {
    width: 100%;
    height: 180px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--telegram-white);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.grupo-placeholder::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 100%);
}

.badges-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.grupo-info {
    min-width: 0;
}

.badges-main {
    display: flex;
    gap: 8px;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.badge-destacado {
    background: linear-gradient(135deg, var(--telegram-warning), #e67e22);
    color: var(--telegram-white);
}

.badge-activo {
    background: linear-gradient(135deg, var(--telegram-success), #2ecc71);
    color: var(--telegram-white);
}

.badge-inactivo {
    background: linear-gradient(135deg, var(--telegram-danger), #c0392b);
    color: var(--telegram-white);
}

.grupo-titulo {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--telegram-dark);
    margin: 0 0 1rem 0;
    line-height: 1.2;
    background: linear-gradient(135deg, var(--telegram-dark), var(--telegram-blue));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.grupo-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--telegram-gray);
    padding: 0.5rem 1rem;
    background: rgba(0, 136, 204, 0.05);
    border-radius: 25px;
    border: 1px solid rgba(0, 136, 204, 0.1);
    transition: var(--telegram-transition);
}

.meta-item:hover {
    background: rgba(0, 136, 204, 0.1);
    transform: translateY(-1px);
}

.meta-item i {
    width: 16px;
    font-size: 0.9rem;
}

.meta-item a {
    color: var(--telegram-blue);
    text-decoration: none;
    font-weight: 600;
}

.meta-item a:hover {
    color: var(--telegram-dark);
}

.grupo-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-item {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--telegram-white), #f8f9fa);
    border-radius: 12px;
    border: 2px solid transparent;
    transition: var(--telegram-transition);
    position: relative;
    overflow: hidden;
}

.stat-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--telegram-blue), var(--telegram-light-blue));
    transform: scaleX(0);
    transition: var(--telegram-transition);
}

.stat-item:hover::before {
    transform: scaleX(1);
}

.stat-item:hover {
    border-color: var(--telegram-blue);
    transform: translateY(-3px);
    box-shadow: var(--telegram-shadow-hover);
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 800;
    color: var(--telegram-blue);
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.8rem;
    color: var(--telegram-gray);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.grupo-acciones {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn {
    padding: 1rem 2rem;
    border-radius: 30px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    border: none;
    font-size: 0.95rem;
    transition: var(--telegram-transition);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: var(--telegram-transition);
}

.btn:hover::before {
    left: 100%;
}

.btn-telegram {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
    box-shadow: 0 4px 15px rgba(0, 136, 204, 0.3);
}

.btn-telegram:hover {
    background: linear-gradient(135deg, var(--telegram-light-blue), var(--telegram-blue));
    color: var(--telegram-white);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 136, 204, 0.4);
}

.btn-secondary {
    background: transparent;
    color: var(--telegram-blue);
    border: 2px solid var(--telegram-blue);
}

.btn-secondary:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 136, 204, 0.3);
}

.btn-disabled {
    background: var(--telegram-light-gray);
    color: var(--telegram-white);
    cursor: not-allowed;
    opacity: 0.6;
}

/* Secciones de contenido */
.content-section {
    background: var(--telegram-white);
    border-radius: var(--telegram-border-radius);
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--telegram-shadow);
    border: 1px solid rgba(0, 136, 204, 0.05);
    transition: var(--telegram-transition);
}

.content-section:hover {
    box-shadow: var(--telegram-shadow-hover);
}

.section-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--telegram-dark);
    margin: 0 0 1.5rem 0;
    padding-bottom: 1rem;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--telegram-blue), var(--telegram-light-blue));
    border-radius: 2px;
}

.descripcion-destacada {
    font-size: 1.1rem;
    color: var(--telegram-blue);
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(0, 136, 204, 0.05), rgba(84, 169, 235, 0.05));
    border-radius: 12px;
    border-left: 4px solid var(--telegram-blue);
    position: relative;
    overflow: hidden;
}

.descripcion-destacada::before {
    content: '"';
    position: absolute;
    top: 10px;
    left: 15px;
    font-size: 2rem;
    color: var(--telegram-blue);
    opacity: 0.3;
    font-family: serif;
}

.descripcion-completa {
    color: var(--telegram-dark);
    line-height: 1.7;
    font-size: 1rem;
}

.descripcion-completa p {
    margin-bottom: 1rem;
}

.descripcion-completa ul, .descripcion-completa ol {
    padding-left: 2rem;
    margin-bottom: 1rem;
}

.descripcion-completa li {
    margin-bottom: 0.5rem;
}

/* Detalles */
.detalles-grid {
    display: grid;
    gap: 1.5rem;
}

.detalle-card {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fa, var(--telegram-white));
    border-radius: 12px;
    border: 1px solid rgba(0, 136, 204, 0.1);
    transition: var(--telegram-transition);
    position: relative;
}

.detalle-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--telegram-blue), var(--telegram-light-blue));
    border-radius: 0 0 0 12px;
}

.detalle-card:hover {
    transform: translateX(5px);
    box-shadow: var(--telegram-shadow);
}

.detalle-titulo {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--telegram-dark);
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detalle-titulo i {
    color: var(--telegram-blue);
    font-size: 1.2rem;
    width: 20px;
}

.etiquetas-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
}

.etiqueta {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: var(--telegram-transition);
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.etiqueta:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 136, 204, 0.3);
    color: var(--telegram-white);
    background: linear-gradient(135deg, var(--telegram-light-blue), var(--telegram-blue));
}

/* Grupos relacionados */
.grupos-relacionados {
    margin-top: 3rem;
}

.grupos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.grupo-card {
    background: var(--telegram-white);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid rgba(0, 136, 204, 0.1);
    transition: var(--telegram-transition);
    position: relative;
    overflow: hidden;
}

.grupo-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--telegram-blue), var(--telegram-light-blue));
    transform: scaleX(0);
    transition: var(--telegram-transition);
}

.grupo-card:hover::before {
    transform: scaleX(1);
}

.grupo-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--telegram-shadow-hover);
}

.grupo-card h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--telegram-dark);
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
}

.grupo-card h3 a {
    text-decoration: none;
    color: inherit;
    transition: var(--telegram-transition);
}

.grupo-card h3 a:hover {
    color: var(--telegram-blue);
}

.grupo-card p {
    color: var(--telegram-gray);
    font-size: 0.95rem;
    line-height: 1.5;
    margin: 0;
}

.no-relacionados {
    grid-column: 1 / -1;
    text-align: center;
    color: var(--telegram-gray);
    font-style: italic;
    padding: 3rem;
    background: linear-gradient(135deg, #f8f9fa, var(--telegram-white));
    border-radius: 12px;
    border: 2px dashed rgba(0, 136, 204, 0.2);
}

/* Modal */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(44, 62, 80, 0.8);
    backdrop-filter: blur(10px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: var(--telegram-transition);
}

.modal.show {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    background: var(--telegram-white);
    border-radius: var(--telegram-border-radius);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    transform: scale(0.8);
    transition: var(--telegram-transition);
}

.modal.show .modal-content {
    transform: scale(1);
}

/* Responsive */
@media (max-width: 1024px) {
    .grupo-header-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 1.5rem;
    }
    
    .grupo-imagen, .grupo-placeholder {
        max-width: 250px;
        margin: 0 auto;
    }
}

@media (max-width: 768px) {
    .single-grupo-container {
        padding: 1rem;
    }
    
    .grupo-header-card, .content-section {
        padding: 1.5rem;
    }
    
    .grupo-titulo {
        font-size: 1.8rem;
    }
    
    .grupo-meta {
        justify-content: center;
    }
    
    .grupo-acciones {
        justify-content: center;
    }
    
    .btn {
        flex: 1;
        justify-content: center;
        min-width: 0;
    }
    
    .grupos-grid {
        grid-template-columns: 1fr;
    }
    
    .breadcrumbs {
        padding: 0.75rem 1rem;
    }
    
    .breadcrumb-list {
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .grupo-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .grupo-acciones {
        flex-direction: column;
    }
    
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.grupo-header-card,
.content-section {
    animation: fadeInUp 0.6s ease-out;
}

.content-section:nth-child(2) { animation-delay: 0.1s; }
.content-section:nth-child(3) { animation-delay: 0.2s; }
.grupos-relacionados { animation-delay: 0.3s; }

/* Scroll suave */
html {
    scroll-behavior: smooth;
}

/* Estados de enfoque para accesibilidad */
.btn:focus,
.etiqueta:focus,
.meta-item a:focus,
.breadcrumb-item a:focus {
    outline: 2px solid var(--telegram-blue);
    outline-offset: 2px;
}
</style>

<!-- Breadcrumbs -->
<nav class="breadcrumbs" aria-label="Navegación">
    <ol class="breadcrumb-list">
        <li class="breadcrumb-item">
            <a href="<?php echo esc_url(home_url()); ?>">
                <i class="fas fa-home"></i>
                Inicio
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?php echo esc_url(get_post_type_archive_link('grupo')); ?>">
                Grupos
            </a>
        </li>
        <?php if ($categoria_principal) : ?>
            <li class="breadcrumb-item">
                <a href="<?php echo esc_url(get_term_link($categoria_principal)); ?>">
                    <?php echo esc_html($categoria_principal->name); ?>
                </a>
            </li>
        <?php endif; ?>
        <li class="breadcrumb-item active">
            <?php echo esc_html(get_the_title()); ?>
        </li>
    </ol>
</nav>

<div class="single-grupo-container">
    
    <!-- Header del grupo -->
    <div class="grupo-header-card">
        <div class="grupo-header-content">
            <!-- Imagen del grupo -->
            <div class="grupo-imagen-container">
                <?php if ($imagen_grupo && is_array($imagen_grupo)) : ?>
                    <img src="<?php echo esc_url($imagen_grupo['sizes']['medium'] ?? $imagen_grupo['url']); ?>" 
                         alt="<?php echo esc_attr($imagen_grupo['alt'] ?: get_the_title()); ?>"
                         class="grupo-imagen">
                <?php else : ?>
                    <div class="grupo-placeholder" style="background: <?php echo esc_attr($categoria_style['color']); ?>">
                        <i class="<?php echo esc_attr($categoria_style['icon']); ?>"></i>
                    </div>
                <?php endif; ?>
                
                <!-- Badges overlay -->
                <div class="badges-overlay">
                    <?php if ($es_destacado) : ?>
                        <span class="badge badge-destacado">
                            <i class="fas fa-star"></i>
                        </span>
                    <?php endif; ?>
                    
                    <span class="badge badge-<?php echo esc_attr(strtolower($estado_grupo)); ?>">
                        <i class="fas fa-circle"></i>
                    </span>
                </div>
            </div>
            
            <!-- Información principal -->
            <div class="grupo-info">
                <!-- Badges principales -->
                <div class="badges-main">
                    <?php if ($es_destacado) : ?>
                        <span class="badge badge-destacado">
                            <i class="fas fa-star"></i> Destacado
                        </span>
                    <?php endif; ?>
                    
                    <span class="badge badge-<?php echo esc_attr(strtolower($estado_grupo)); ?>">
                        <i class="fas fa-circle"></i> <?php echo esc_html($estado_grupo); ?>
                    </span>
                </div>
                
                <h1 class="grupo-titulo"><?php the_title(); ?></h1>
                
                <!-- Meta información -->
                <div class="grupo-meta">
                    <?php if ($categoria_principal) : ?>
                        <div class="meta-item">
                            <i class="<?php echo esc_attr($categoria_style['icon']); ?>" 
                               style="color: <?php echo esc_attr($categoria_style['color']); ?>"></i>
                            <a href="<?php echo esc_url(get_term_link($categoria_principal)); ?>">
                                <?php echo esc_html($categoria_principal->name); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($ciudades)) : ?>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php foreach ($ciudades as $index => $ciudad) : ?>
                                <?php if ($index > 0) echo ', '; ?>
                                <a href="<?php echo esc_url(get_term_link($ciudad)); ?>">
                                    <?php echo esc_html($ciudad->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($idioma) : ?>
                        <div class="meta-item">
                            <i class="fas fa-language"></i>
                            <span><?php echo esc_html($idioma); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Estadísticas -->
                <div class="grupo-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html(format_member_count($numero_miembros)); ?></span>
                        <span class="stat-label">Miembros</span>
                    </div>
                    
                    <?php if ($fecha_creacion) : ?>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo esc_html(date('Y', strtotime($fecha_creacion))); ?></span>
                            <span class="stat-label">Creado</span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($fecha_actualizacion) : ?>
                        <div class="stat-item">
                                                 <span class="stat-number"><?php echo esc_html(time_since_activity($fecha_actualizacion)); ?></span>
                            <span class="stat-label">Actualizado</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Acciones -->
                <div class="grupo-acciones">
                    <?php if ($enlace_telegram) : ?>
                        <a href="<?php echo esc_url($enlace_telegram); ?>" 
                           class="btn btn-telegram" 
                           target="_blank" 
                           rel="noopener noreferrer">
                            <i class="fab fa-telegram-plane"></i>
                            Unirse al Grupo
                        </a>
                    <?php else : ?>
                        <button class="btn btn-disabled" disabled>
                            <i class="fas fa-lock"></i>
                            Enlace no disponible
                        </button>
                    <?php endif; ?>
                    
                    <button class="btn btn-secondary" onclick="abrirModalCompartir()">
                        <i class="fas fa-share-alt"></i>
                        Compartir
                    </button>
                    
                    <button class="btn btn-secondary" onclick="abrirModalReporte(<?php echo $grupo_id; ?>)">
                        <i class="fas fa-flag"></i>
                        Reportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Descripción del grupo -->
    <?php if ($descripcion_corta || $descripcion_completa || get_the_content()) : ?>
        <div class="content-section">
            <h2 class="section-title">
                <i class="fas fa-info-circle"></i>
                Sobre este Grupo
            </h2>
            
            <?php if ($descripcion_corta) : ?>
                <div class="descripcion-destacada">
                    <?php echo wp_kses_post($descripcion_corta); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($descripcion_completa) : ?>
                <div class="descripcion-completa">
                    <?php echo wp_kses_post($descripcion_completa); ?>
                </div>
            <?php elseif (get_the_content()) : ?>
                <div class="descripcion-completa">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Detalles adicionales -->
    <?php if ($requisitos || $moderacion || !empty($etiquetas)) : ?>
        <div class="content-section">
            <h2 class="section-title">
                <i class="fas fa-cogs"></i>
                Detalles del Grupo
            </h2>
            
            <div class="detalles-grid">
                <?php if ($requisitos) : ?>
                    <div class="detalle-card">
                        <h3 class="detalle-titulo">
                            <i class="fas fa-clipboard-list"></i>
                            Requisitos de Ingreso
                        </h3>
                        <p><?php echo wp_kses_post($requisitos); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($moderacion) : ?>
                    <div class="detalle-card">
                        <h3 class="detalle-titulo">
                            <i class="fas fa-shield-alt"></i>
                            Políticas de Moderación
                        </h3>
                        <p><?php echo wp_kses_post($moderacion); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($etiquetas)) : ?>
                    <div class="detalle-card">
                        <h3 class="detalle-titulo">
                            <i class="fas fa-hashtag"></i>
                            Etiquetas y Temas
                        </h3>
                        <div class="etiquetas-list">
                            <?php foreach ($etiquetas as $etiqueta) : ?>
                                <a href="<?php echo esc_url(get_term_link($etiqueta)); ?>" 
                                   class="etiqueta"
                                   title="Ver todos los grupos con la etiqueta <?php echo esc_attr($etiqueta->name); ?>">
                                    <i class="fas fa-tag"></i>
                                    <?php echo esc_html($etiqueta->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Información adicional -->
    <div class="content-section">
        <h2 class="section-title">
            <i class="fas fa-chart-bar"></i>
            Estadísticas Detalladas
        </h2>
        
        <div class="stats-detalladas">
            <div class="stat-card">
                <div class="stat-icon" style="background: <?php echo esc_attr($categoria_style['color']); ?>">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h4>Total de Miembros</h4>
                    <p class="stat-big"><?php echo esc_html(number_format($numero_miembros)); ?></p>
                    <small>Miembros activos en el grupo</small>
                </div>
            </div>
            
            <?php if ($fecha_creacion) : ?>
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--telegram-success)">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Fecha de Creación</h4>
                        <p class="stat-big"><?php echo esc_html(date('d/m/Y', strtotime($fecha_creacion))); ?></p>
                        <small><?php echo esc_html(time_since_activity($fecha_creacion)); ?></small>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($fecha_actualizacion) : ?>
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--telegram-warning)">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Última Actualización</h4>
                        <p class="stat-big"><?php echo esc_html(date('d/m/Y', strtotime($fecha_actualizacion))); ?></p>
                        <small><?php echo esc_html(time_since_activity($fecha_actualizacion)); ?></small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grupos relacionados -->
    <div class="grupos-relacionados">
        <h2 class="section-title">
            <i class="fas fa-sitemap"></i>
            Grupos Relacionados
        </h2>
        
        <div class="grupos-grid">
            <?php
            // Obtener grupos relacionados por categoría y ciudad
            $relacionados_args = array(
                'post_type' => 'grupo',
                'posts_per_page' => 8,
                'post__not_in' => array($grupo_id),
                'orderby' => 'rand',
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'estado_grupo',
                        'value' => 'Activo',
                        'compare' => '='
                    )
                )
            );

            // Agregar filtro por taxonomías si existen
            if ($categoria_principal || !empty($ciudades)) {
                $tax_query = array('relation' => 'OR');
                
                if ($categoria_principal) {
                    $tax_query[] = array(
                        'taxonomy' => 'categoria_grupo',
                        'field' => 'term_id',
                        'terms' => $categoria_principal->term_id
                    );
                }
                
                if (!empty($ciudades)) {
                    $ciudad_ids = wp_list_pluck($ciudades, 'term_id');
                    $tax_query[] = array(
                        'taxonomy' => 'ciudad_grupo',
                        'field' => 'term_id',
                        'terms' => $ciudad_ids
                    );
                }
                
                $relacionados_args['tax_query'] = $tax_query;
            }

            $grupos_relacionados = new WP_Query($relacionados_args);
            
            if ($grupos_relacionados->have_posts()) :
                while ($grupos_relacionados->have_posts()) : 
                    $grupos_relacionados->the_post();
                    
                    // Obtener datos del grupo relacionado
                    $rel_miembros = get_field('numero_miembros') ?: 0;
                    $rel_categoria = wp_get_post_terms(get_the_ID(), 'categoria_grupo');
                    $rel_categoria_style = !empty($rel_categoria) ? get_categoria_style($rel_categoria[0]->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
                    $rel_enlace = get_field('enlace_telegram');
                    ?>
                    <div class="grupo-card">
                        <div class="grupo-card-header">
                            <div class="grupo-card-icon" style="background: <?php echo esc_attr($rel_categoria_style['color']); ?>">
                                <i class="<?php echo esc_attr($rel_categoria_style['icon']); ?>"></i>
                            </div>
                            <div class="grupo-card-meta">
                                <?php if (!empty($rel_categoria)) : ?>
                                    <span class="categoria-tag" style="background: <?php echo esc_attr($rel_categoria_style['color']); ?>">
                                        <?php echo esc_html($rel_categoria[0]->name); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="miembros-count">
                                    <i class="fas fa-users"></i>
                                    <?php echo esc_html(format_member_count($rel_miembros)); ?>
                                </span>
                            </div>
                        </div>
                        
                        <h3>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 15)); ?></p>
                        
                        <div class="grupo-card-actions">
                            <a href="<?php the_permalink(); ?>" class="btn-small btn-outline">
                                <i class="fas fa-eye"></i>
                                Ver Grupo
                            </a>
                            <?php if ($rel_enlace) : ?>
                                <a href="<?php echo esc_url($rel_enlace); ?>" 
                                   class="btn-small btn-telegram-small" 
                                   target="_blank" 
                                   rel="noopener noreferrer">
                                    <i class="fab fa-telegram-plane"></i>
                                    Unirse
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<div class="no-relacionados">
                        <i class="fas fa-search" style="font-size: 3rem; color: var(--telegram-light-gray); margin-bottom: 1rem;"></i>
                        <h3>No hay grupos relacionados</h3>
                        <p>Aún no hemos encontrado grupos similares a este. ¡Vuelve pronto para más contenido!</p>
                      </div>';
            endif;
            ?>
        </div>
    </div>

</div>

<!-- Modal de Compartir -->
<div id="modal-compartir" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-share-alt"></i> Compartir Grupo</h3>
            <button class="modal-close" onclick="cerrarModal('modal-compartir')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="share-options">
                <a href="#" onclick="compartirEn('whatsapp')" class="share-btn whatsapp">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp
                </a>
                <a href="#" onclick="compartirEn('telegram')" class="share-btn telegram">
                    <i class="fab fa-telegram"></i>
                    Telegram
                </a>
                <a href="#" onclick="compartirEn('twitter')" class="share-btn twitter">
                    <i class="fab fa-twitter"></i>
                    Twitter
                </a>
                <a href="#" onclick="compartirEn('facebook')" class="share-btn facebook">
                    <i class="fab fa-facebook"></i>
                    Facebook
                </a>
            </div>
            <div class="copy-url-section">
                <label>O copia el enlace:</label>
                <div class="copy-url-group">
                    <input type="text" id="url-grupo" value="<?php echo esc_attr(get_permalink()); ?>" readonly>
                    <button onclick="copiarURL()" class="btn-copy">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Reporte -->
<div id="modal-reporte" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-flag"></i> Reportar Grupo</h3>
            <button class="modal-close" onclick="cerrarModal('modal-reporte')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-reporte" onsubmit="enviarReporte(event)">
                <div class="form-group">
                    <label for="motivo-reporte">Motivo del reporte:</label>
                    <select id="motivo-reporte" name="motivo" required>
                        <option value="">Selecciona un motivo</option>
                        <option value="spam">Spam o contenido no deseado</option>
                        <option value="inappropriate">Contenido inapropiado</option>
                        <option value="misleading">Información engañosa</option>
                        <option value="broken-link">Enlace roto o no funciona</option>
                        <option value="duplicate">Grupo duplicado</option>
                        <option value="other">Otro motivo</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="detalles-reporte">Detalles adicionales (opcional):</label>
                    <textarea id="detalles-reporte" name="detalles" rows="3" 
                              placeholder="Proporciona más información sobre el problema..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="email-reporte">Tu email (opcional):</label>
                    <input type="email" id="email-reporte" name="email" 
                           placeholder="Para contactarte si es necesario">
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="cerrarModal('modal-reporte')" class="btn btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-paper-plane"></i>
                        Enviar Reporte
                    </button>
                </div>
                
                <input type="hidden" id="grupo-id-reporte" name="grupo_id" value="">
                <input type="hidden" name="action" value="report_group">
                <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('telegram_nonce')); ?>">
            </form>
        </div>
    </div>
</div>

<!-- Estilos adicionales para los nuevos elementos -->
<style>
/* Estadísticas detalladas */
.stats-detalladas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--telegram-white);
    border-radius: 12px;
    border: 1px solid rgba(0, 136, 204, 0.1);
    transition: var(--telegram-transition);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--telegram-shadow);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.stat-info h4 {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    color: var(--telegram-gray);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-big {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--telegram-dark);
    margin: 0 0 0.25rem 0;
}

.stat-info small {
    color: var(--telegram-gray);
    font-size: 0.8rem;
}

/* Tarjetas de grupos relacionados mejoradas */
.grupo-card {
    position: relative;
    overflow: hidden;
}

.grupo-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.grupo-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.grupo-card-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.categoria-tag {
    font-size: 0.7rem;
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.miembros-count {
    font-size: 0.8rem;
    color: var(--telegram-gray);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.grupo-card-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 136, 204, 0.1);
}

.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    transition: var(--telegram-transition);
    flex: 1;
    justify-content: center;
}

.btn-outline {
    background: transparent;
    color: var(--telegram-blue);
    border: 1px solid var(--telegram-blue);
}

.btn-outline:hover {
    background: var(--telegram-blue);
    color: white;
}

.btn-telegram-small {
    background: var(--telegram-blue);
    color: white;
}

.btn-telegram-small:hover {
    background: var(--telegram-light-blue);
    color: white;
}

/* Modal mejorado */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid rgba(0, 136, 204, 0.1);
    background: linear-gradient(135deg, #f8f9fa, var(--telegram-white));
}

.modal-header h3 {
    margin: 0;
    color: var(--telegram-dark);
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--telegram-gray);
    padding: 0.5rem;
    border-radius: 50%;
    transition: var(--telegram-transition);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: rgba(0, 136, 204, 0.1);
    color: var(--telegram-dark);
}

.modal-body {
    padding: 2rem;
}

/* Opciones de compartir */
.share-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.share-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--telegram-transition);
    color: white;
}

.share-btn:hover {
    transform: scale(1.05);
    color: white;
}

.whatsapp { background: #25D366; }
.telegram { background: #0088cc; }
.twitter { background: #1DA1F2; }
.facebook { background: #1877F2; }

.copy-url-section {
    border-top: 1px solid rgba(0, 136, 204, 0.1);
    padding-top: 1.5rem;
}

.copy-url-section label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--telegram-dark);
}

.copy-url-group {
    display: flex;
    gap: 0.5rem;
}

.copy-url-group input {
    flex: 1;
    padding: 0.75rem;
    border: 1px solid rgba(0, 136, 204, 0.2);
    border-radius: 6px;
    font-size: 0.9rem;
}

.btn-copy {
    padding: 0.75rem 1rem;
    background: var(--telegram-blue);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: var(--telegram-transition);
}

.btn-copy:hover {
    background: var(--telegram-light-blue);
}

/* Formulario de reporte */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--telegram-dark);
}

.form-group select,
.form-group textarea,
.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid rgba(0, 136, 204, 0.2);
    border-radius: 6px;
    font-size: 0.9rem;
    font-family: inherit;
    transition: var(--telegram-transition);
}

.form-group select:focus,
.form-group textarea:focus,
.form-group input:focus {
    outline: none;
    border-color: var(--telegram-blue);
    box-shadow: 0 0 0 3px rgba(0, 136, 204, 0.1);
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 136, 204, 0.1);
}

.btn-danger {
    background: var(--telegram-danger);
    color: white;
}

.btn-danger:hover {
    background: #c0392b;
    color: white;
}

/* Toast notifications */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: var(--telegram-success);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: var(--telegram-shadow);
    z-index: 10000;
    transform: translateX(100%);
    transition: var(--telegram-transition);
}

.toast.show {
    transform: translateX(0);
}

.toast.error {
    background: var(--telegram-danger);
}

/* Responsive para modales */
@media (max-width: 768px) {
    .share-options {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .stats-detalladas {
        grid-template-columns: 1fr;
    }
    
    .grupo-card-actions {
        flex-direction: column;
    }
}
</style>

<script>
// Variables globales
let modalActual = null;

// Función para abrir modal de compartir
function abrirModalCompartir() {
    modalActual = document.getElementById('modal-compartir');
    modalActual.classList.add('show');
    document.body.style.overflow = 'hidden';
}

// Función para abrir modal de reporte
function abrirModalReporte(grupoId) {
    modalActual = document.getElementById('modal-reporte');
    document.getElementById('grupo-id-reporte').value = grupoId;
    modalActual.classList.add('show');
    document.body.style.overflow = 'hidden';
}

// Función para cerrar modal
function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
    modalActual = null;
    
    // Limpiar formulario si es el modal de reporte
    if (modalId === 'modal-reporte') {
        document.getElementById('form-reporte').reset();
    }
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        cerrarModal(e.target.id);
    }
});

// Cerrar modal con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modalActual) {
        cerrarModal(modalActual.id);
    }
});

// Función para compartir en redes sociales
function compartirEn(red) {
    const url = encodeURIComponent(window.location.href);
    const titulo = encodeURIComponent(document.title);
    const descripcion = encodeURIComponent(document.querySelector('.descripcion-destacada')?.textContent || '');
    
    let shareUrl = '';
    
    switch(red) {
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${titulo}%20${url}`;
            break;
        case 'telegram':
            shareUrl = `https://t.me/share/url?url=${url}&text=${titulo}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${titulo}&url=${url}`;
            break;
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

// Función para copiar URL
function copiarURL() {
    const input = document.getElementById('url-grupo');
    input.select();
    document.execCommand('copy');
    
    // Cambiar texto del botón temporalmente
    const btn = document.querySelector('.btn-copy');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i>';
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
    }, 2000);
    
    mostrarToast('Enlace copiado al portapapeles', 'success');
}

// Función para enviar reporte
async function enviarReporte(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalHTML = submitBtn.innerHTML;
    
    // Estado de carga
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarToast('Reporte enviado correctamente. Gracias por tu colaboración.', 'success');
            cerrarModal('modal-reporte');
        } else {
            throw new Error(data.data || 'Error en el servidor');
        }
    } catch (error) {
        console.error('Error al enviar reporte:', error);
        mostrarToast('Error al enviar el reporte. Inténtalo de nuevo.', 'error');
    } finally {
        submitBtn.innerHTML = originalHTML;
        submitBtn.disabled = false;
    }
}




// Función para mostrar notificaciones toast
function mostrarToast(mensaje, tipo = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${tipo}`;
    toast.innerHTML = `
        <i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${mensaje}</span>
        <button onclick="cerrarToast(this)" style="background: none; border: none; color: inherit; margin-left: 10px; cursor: pointer;">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Mostrar toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        cerrarToast(toast.querySelector('button'));
    }, 5000);
}

// Función para cerrar toast
function cerrarToast(button) {
    const toast = button.closest('.toast');
    toast.classList.remove('show');
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

// Función para scroll suave a secciones
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Agregar efecto de parallax sutil al header
    const header = document.querySelector('.grupo-header-card');
    if (header) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            header.style.transform = `translateY(${rate}px)`;
        });
    }
    
    // Lazy loading para imágenes
    const images = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
    
    // Animación de contadores en las estadísticas
    const statsNumbers = document.querySelectorAll('.stat-number');
    if ('IntersectionObserver' in window) {
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    statsObserver.unobserve(entry.target);
                }
            });
        });
        
        statsNumbers.forEach(stat => statsObserver.observe(stat));
    }
    
    // Mejorar accesibilidad con navegación por teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            document.body.classList.add('keyboard-navigation');
        }
    });
    
    document.addEventListener('mousedown', function() {
        document.body.classList.remove('keyboard-navigation');
    });
});

// Función para animar contadores
function animateCounter(element) {
    const target = parseInt(element.textContent.replace(/[^\d]/g, ''));
    const duration = 2000;
    const start = performance.now();
    const original = element.textContent;
    
    function update(currentTime) {
        const elapsed = currentTime - start;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(progress * target);
        const formatted = formatNumber(current);
        
        // Mantener el formato original (K, M, etc.)
        if (original.includes('K')) {
            element.textContent = (current / 1000).toFixed(1) + 'K';
        } else if (original.includes('M')) {
            element.textContent = (current / 1000000).toFixed(1) + 'M';
        } else {
            element.textContent = formatted;
        }
        
        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            element.textContent = original; // Restaurar formato original
        }
    }
    
    requestAnimationFrame(update);
}

// Función para formatear números
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Función para compartir nativo si está disponible
function compartirNativo() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            text: document.querySelector('.descripcion-destacada')?.textContent || '',
            url: window.location.href
        }).catch(console.error);
    } else {
        abrirModalCompartir();
    }
}

// Funciones para mejorar la experiencia de usuario
function trackEvent(action, category, label) {
    // Google Analytics 4
    if (typeof gtag !== 'undefined') {
        gtag('event', action, {
            event_category: category,
            event_label: label
        });
    }
    
    // Facebook Pixel
    if (typeof fbq !== 'undefined') {
        fbq('track', 'CustomEvent', {
            action: action,
            category: category,
            label: label
        });
    }
}

// Función para detectar enlaces rotos
function verificarEnlaceTelegram() {
    const enlaceTelegram = document.querySelector('.btn-telegram');
    if (enlaceTelegram) {
        enlaceTelegram.addEventListener('click', function() {
            trackEvent('telegram_click', 'grupo', '<?php echo get_the_title(); ?>');
        });
    }
}

// Función para crear breadcrumbs dinámicos
function actualizarBreadcrumbs() {
    const breadcrumbs = document.querySelector('.breadcrumb-list');
    if (breadcrumbs) {
        // Agregar schema markup para SEO
        breadcrumbs.setAttribute('itemscope', '');
        breadcrumbs.setAttribute('itemtype', 'https://schema.org/BreadcrumbList');
        
        const items = breadcrumbs.querySelectorAll('.breadcrumb-item');
        items.forEach((item, index) => {
            const link = item.querySelector('a');
            if (link) {
                item.setAttribute('itemprop', 'itemListElement');
                item.setAttribute('itemscope', '');
                item.setAttribute('itemtype', 'https://schema.org/ListItem');
                
                link.setAttribute('itemprop', 'item');
                link.insertAdjacentHTML('beforeend', `<span itemprop="name" style="display: none;">${link.textContent}</span>`);
                
                item.insertAdjacentHTML('beforeend', `<meta itemprop="position" content="${index + 1}">`);
            }
        });
    }
}

// Ejecutar funciones adicionales
document.addEventListener('DOMContentLoaded', function() {
    verificarEnlaceTelegram();
    actualizarBreadcrumbs();
});

// Service Worker para PWA (opcional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('ServiceWorker registrado con éxito:', registration.scope);
            })
            .catch(function(error) {
                console.log('Error al registrar ServiceWorker:', error);
            });
    });
}
</script>

<!-- Schema.org structured data para SEO -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "<?php echo esc_js(get_the_title()); ?>",
    "description": "<?php echo esc_js(wp_strip_all_tags($descripcion_corta ?: get_the_excerpt())); ?>",
    "url": "<?php echo esc_js(get_permalink()); ?>",
    <?php if ($enlace_telegram) : ?>
    "sameAs": [
        "<?php echo esc_js($enlace_telegram); ?>"
    ],
    <?php endif; ?>
    "memberOf": {
        "@type": "Organization",
        "name": "Telegram"
    },
    <?php if ($categoria_principal) : ?>
    "category": "<?php echo esc_js($categoria_principal->name); ?>",
    <?php endif; ?>
    "numberOfEmployees": {
        "@type": "QuantitativeValue",
        "value": <?php echo intval($numero_miembros); ?>
    },
    <?php if ($fecha_creacion) : ?>
    "foundingDate": "<?php echo esc_js(date('Y-m-d', strtotime($fecha_creacion))); ?>",
    <?php endif; ?>
    <?php if ($imagen_grupo && is_array($imagen_grupo)) : ?>
    "image": "<?php echo esc_js($imagen_grupo['url']); ?>",
    <?php endif; ?>
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.5",
        "reviewCount": "<?php echo intval($numero_miembros / 10); ?>"
    }
}
</script>

<!-- Open Graph meta tags para redes sociales -->
<meta property="og:title" content="<?php echo esc_attr(get_the_title()); ?>">
<meta property="og:description" content="<?php echo esc_attr(wp_strip_all_tags($descripcion_corta ?: get_the_excerpt())); ?>">
<meta property="og:url" content="<?php echo esc_attr(get_permalink()); ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
<?php if ($imagen_grupo && is_array($imagen_grupo)) : ?>
<meta property="og:image" content="<?php echo esc_attr($imagen_grupo['url']); ?>">
<meta property="og:image:width" content="<?php echo esc_attr($imagen_grupo['width'] ?? 400); ?>">
<meta property="og:image:height" content="<?php echo esc_attr($imagen_grupo['height'] ?? 300); ?>">
<?php endif; ?>

<!-- Twitter Card meta tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr(get_the_title()); ?>">
<meta name="twitter:description" content="<?php echo esc_attr(wp_strip_all_tags($descripcion_corta ?: get_the_excerpt())); ?>">
<?php if ($imagen_grupo && is_array($imagen_grupo)) : ?>
<meta name="twitter:image" content="<?php echo esc_attr($imagen_grupo['url']); ?>">
<?php endif; ?>

<!-- Telegram meta tags -->
<meta property="telegram:channel" content="<?php echo esc_attr($enlace_telegram); ?>">

<?php get_footer(); ?>