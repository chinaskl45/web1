<?php
/**
 * Template part para mostrar tarjeta de grupo
 * 
 * @package Telegram_Groups
 */

// Obtener datos del grupo
$grupo_id = get_the_ID();
$imagen_grupo = get_field('imagen_grupo');
$descripcion_corta = get_field('descripcion_corta');
$enlace_telegram = get_field('enlace_telegram');
$numero_miembros = get_field('numero_miembros') ?: 0;
$estado_grupo = get_field('estado_grupo') ?: 'Activo';
$fecha_actualizacion = get_field('fecha_actualizacion');

// Obtener taxonomías
$categoria = wp_get_post_terms($grupo_id, 'categoria_grupo', array('fields' => 'all'));
$ciudad = wp_get_post_terms($grupo_id, 'ciudad_grupo', array('fields' => 'all'));
$es_destacado = has_term('destacado', 'destacados', $grupo_id);

$categoria_principal = !empty($categoria) ? $categoria[0] : null;
$ciudad_principal = !empty($ciudad) ? $ciudad[0] : null;

// Estilos de categoría
$categoria_style = $categoria_principal ? get_categoria_style($categoria_principal->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
?>

<article class="grupo-card <?php echo $es_destacado ? 'destacado' : ''; ?>" data-grupo-id="<?php echo $grupo_id; ?>">
    
    <!-- Imagen del grupo -->
    <div class="grupo-imagen">
        <?php if ($imagen_grupo) : ?>
            <img src="<?php echo esc_url($imagen_grupo['sizes']['grupo-card'] ?? $imagen_grupo['url']); ?>" 
                 alt="<?php echo esc_attr($imagen_grupo['alt'] ?: get_the_title()); ?>">
        <?php else : ?>
            <div class="grupo-placeholder" style="background: <?php echo $categoria_style['color']; ?>">
                <i class="<?php echo $categoria_style['icon']; ?>"></i>
            </div>
        <?php endif; ?>
        
        <!-- Badges -->
        <div class="grupo-badges">
            <?php if ($es_destacado) : ?>
                <span class="badge badge-destacado">
                    <i class="fas fa-star"></i>
                    <?php _e('Destacado', 'telegram-groups'); ?>
                </span>
            <?php endif; ?>
            
            <span class="badge badge-estado estado-<?php echo strtolower($estado_grupo); ?>">
                <i class="fas fa-circle"></i>
                <?php echo $estado_grupo; ?>
            </span>
        </div>
        
        <!-- Categoría badge -->
        <?php if ($categoria_principal) : ?>
            <div class="categoria-badge" style="background: <?php echo $categoria_style['color']; ?>">
                <i class="<?php echo $categoria_style['icon']; ?>"></i>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Contenido -->
    <div class="grupo-content">
        <!-- Header -->
        <div class="grupo-header">
            <h3 class="grupo-titulo">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            
            <div class="grupo-meta">
                <?php if ($categoria_principal) : ?>
                    <span class="meta-categoria">
                        <i class="<?php echo $categoria_style['icon']; ?>" style="color: <?php echo $categoria_style['color']; ?>"></i>
                        <?php echo $categoria_principal->name; ?>
                    </span>
                <?php endif; ?>
                
                <?php if ($ciudad_principal) : ?>
                    <span class="meta-ciudad">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo $ciudad_principal->name; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Descripción -->
        <?php if ($descripcion_corta) : ?>
            <div class="grupo-descripcion">
                <p><?php echo wp_trim_words($descripcion_corta, 15, '...'); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Estadísticas -->
        <div class="grupo-stats">
            <div class="stat-miembros">
                <i class="fas fa-users"></i>
                <span><?php echo format_member_count($numero_miembros); ?></span>
            </div>
            
            <?php if ($fecha_actualizacion) : ?>
                <div class="stat-actualizado">
                    <i class="fas fa-clock"></i>
                    <span><?php echo time_since_activity($fecha_actualizacion); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Acciones -->
        <div class="grupo-acciones">
            <a href="<?php the_permalink(); ?>" class="btn btn-secondary">
                <i class="fas fa-eye"></i>
                <?php _e('Ver más', 'telegram-groups'); ?>
            </a>
            
            <?php if ($enlace_telegram) : ?>
                <a href="<?php echo esc_url($enlace_telegram); ?>" 
                   class="btn btn-telegram" 
                   target="_blank" 
                   rel="noopener noreferrer">
                    <i class="fab fa-telegram-plane"></i>
                    <?php _e('Unirse', 'telegram-groups'); ?>
                </a>
            <?php else : ?>
                <button class="btn btn-disabled" disabled>
                    <i class="fas fa-lock"></i>
                    <?php _e('No disponible', 'telegram-groups'); ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
</article>

<style>
/* Grupo Card Styles */
.grupo-card {
    background: var(--telegram-white);
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0, 136, 204, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
    width: 100%;
    box-sizing: border-box;
}

.grupo-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 136, 204, 0.2);
}

.grupo-card.destacado {
    border: 2px solid #ffd700;
}

.grupo-card.destacado::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ffd700, #ff8c00);
    z-index: 1;
}

/* Imagen */
.grupo-imagen {
    position: relative;
    height: 180px;
    overflow: hidden;
}

.grupo-imagen img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.grupo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--telegram-white);
    font-size: 2.5rem;
}

/* Badges */
.grupo-badges {
    position: absolute;
    top: 8px;
    right: 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    z-index: 2;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 3px;
    color: var(--telegram-white);
}

.badge-destacado {
    background: linear-gradient(45deg, #ffd700, #ff8c00);
}

.badge-estado.estado-activo {
    background: var(--telegram-success);
}

.badge-estado.estado-inactivo {
    background: var(--telegram-danger);
}

.categoria-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--telegram-white);
    font-size: 1rem;
    z-index: 2;
}

/* Contenido */
.grupo-content {
    padding: 1rem;
}

.grupo-header {
    margin-bottom: 0.75rem;
}

.grupo-titulo {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1.3;
}

.grupo-titulo a {
    color: var(--telegram-dark);
    text-decoration: none;
    transition: color 0.3s ease;
}

.grupo-titulo a:hover {
    color: var(--telegram-blue);
}

.grupo-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    font-size: 0.8rem;
}

.meta-categoria,
.meta-ciudad {
    display: flex;
    align-items: center;
    gap: 3px;
    color: var(--telegram-gray);
}

/* Descripción */
.grupo-descripcion {
    margin-bottom: 0.75rem;
}

.grupo-descripcion p {
    font-size: 0.85rem;
    color: var(--telegram-gray);
    line-height: 1.4;
    margin: 0;
}

/* Estadísticas */
.grupo-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 0.8rem;
    color: var(--telegram-gray);
}

.stat-miembros,
.stat-actualizado {
    display: flex;
    align-items: center;
    gap: 4px;
}

.stat-miembros {
    font-weight: 600;
    color: var(--telegram-blue);
}

/* Acciones */
.grupo-acciones {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: 0rem;
}

.btn {
    padding: 2px 4px;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary {
    background: #f8f9fa;
    color: var(--telegram-gray);
    border: 1px solid #e9ecef;
}

.btn-secondary:hover {
    background: #e9ecef;
    color: var(--telegram-dark);
    text-decoration: none;
}

.btn-telegram {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
}

.btn-telegram:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(0, 136, 204, 0.3);
    color: var(--telegram-white);
    text-decoration: none;
}

.btn-disabled {
    background: #f8f9fa;
    color: var(--telegram-gray);
    cursor: not-allowed;
    opacity: 0.6;
}

/* Fix para grid principal */
.grupos-grid {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 2rem !important;
    width: 100% !important;
}

@media (min-width: 1400px) {
    .grupos-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .grupos-grid {
        grid-template-columns: 1fr !important;
    }
    
    .grupo-imagen {
        height: 140px;
    }
    
    .grupo-content {
        padding: 1rem;
    }
    
    .grupo-acciones {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
}



/* Optimización específica para grupo-cards en móvil */
@media (max-width: 768px) {
    .grupo-card {
        border-radius: 12px !important;
        overflow: hidden !important;
        transition: transform 0.2s ease !important;
    }
    
    .grupo-card:hover {
        transform: translateY(-2px) !important;
    }
    
    /* Hacer la imagen más pequeña en móvil horizontal */
    .grupo-card .grupo-imagen-container {
        height: auto !important;
    }
    
    .grupo-card .grupo-imagen {
        border-radius: 8px !important;
    }
    
    /* Optimizar texto */
    .grupo-card .grupo-titulo {
        line-height: 1.2 !important;
        margin-bottom: 0.5rem !important;
    }
    
    .grupo-card .grupo-descripcion {
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
        margin-bottom: 0.75rem !important;
    }
    
    /* Hacer los badges más pequeños */
    .grupo-card .badge {
        font-size: 0.7rem !important;
        padding: 0.2rem 0.5rem !important;
    }
    
    /* Compactar las estadísticas */
    .grupo-card .grupo-stats {
        flex-wrap: wrap !important;
        justify-content: space-between !important;
    }
    
    .grupo-card .stat-item {
        font-size: 0.75rem !important;
    }
    
    /* Hacer los botones más pequeños pero tocables */
    .grupo-card .btn {
        min-height: 36px !important;
        line-height: 1 !important;
    }
}

/* Específico para orientation landscape en móviles */
@media (max-width: 768px) and (orientation: landscape) {
    .container {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .taxonomy-filters-top {
        padding: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .taxonomy-toolbar {
        padding: 0.75rem 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .active-filters {
        padding: 1rem !important;
        margin-bottom: 1rem !important;
    }
}


</style>