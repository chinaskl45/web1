<?php
/**
 * Template part para mostrar grupo en vista de lista (búsqueda)
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
$etiquetas = wp_get_post_terms($grupo_id, 'etiqueta_grupo', array('fields' => 'all'));
$es_destacado = has_term('destacado', 'destacados', $grupo_id);

$categoria_principal = !empty($categoria) ? $categoria[0] : null;
$ciudad_principal = !empty($ciudad) ? $ciudad[0] : null;
$categoria_style = $categoria_principal ? get_categoria_style($categoria_principal->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
?>

<article class="grupo-card-search-list <?php echo $es_destacado ? 'destacado' : ''; ?>" data-grupo-id="<?php echo $grupo_id; ?>">
    
    <!-- Imagen -->
    <div class="grupo-imagen-list">
        <?php if ($imagen_grupo) : ?>
            <img src="<?php echo esc_url($imagen_grupo['sizes']['grupo-card'] ?? $imagen_grupo['url']); ?>" 
                 alt="<?php echo esc_attr($imagen_grupo['alt'] ?: get_the_title()); ?>"
                 loading="lazy">
        <?php else : ?>
            <div class="grupo-placeholder-list" style="background: <?php echo $categoria_style['color']; ?>">
                <i class="<?php echo $categoria_style['icon']; ?>"></i>
            </div>
        <?php endif; ?>
        
        <?php if ($es_destacado) : ?>
            <div class="badge-destacado-list">
                <i class="fas fa-star"></i>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Contenido principal -->
    <div class="grupo-content-list">
        
        <!-- Header con título y meta -->
        <div class="grupo-header-list">
            <h3 class="grupo-titulo-list">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            
            <div class="grupo-meta-list">
                <?php if ($categoria_principal) : ?>
                    <span class="meta-categoria-list">
                        <i class="<?php echo $categoria_style['icon']; ?>" style="color: <?php echo $categoria_style['color']; ?>"></i>
                        <a href="<?php echo get_term_link($categoria_principal); ?>">
                            <?php echo $categoria_principal->name; ?>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if ($ciudad_principal) : ?>
                    <span class="meta-ciudad-list">
                        <i class="fas fa-map-marker-alt"></i>
                        <a href="<?php echo get_term_link($ciudad_principal); ?>">
                            <?php echo $ciudad_principal->name; ?>
                        </a>
                    </span>
                <?php endif; ?>
                
                <span class="meta-estado-list estado-<?php echo strtolower($estado_grupo); ?>">
                    <i class="fas fa-circle"></i>
                    <?php echo $estado_grupo; ?>
                </span>
            </div>
        </div>
        
        <!-- Descripción -->
        <?php if ($descripcion_corta) : ?>
            <div class="grupo-descripcion-list">
                <p><?php echo wp_trim_words($descripcion_corta, 25, '...'); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Etiquetas -->
        <?php if (!empty($etiquetas) && count($etiquetas) > 0) : ?>
            <div class="grupo-etiquetas-list">
                <?php foreach (array_slice($etiquetas, 0, 3) as $etiqueta) : ?>
                    <a href="<?php echo get_term_link($etiqueta); ?>" class="etiqueta-tag-list">
                        #<?php echo $etiqueta->name; ?>
                    </a>
                <?php endforeach; ?>
                <?php if (count($etiquetas) > 3) : ?>
                    <span class="etiquetas-more">+<?php echo count($etiquetas) - 3; ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>
    
    <!-- Sidebar con estadísticas y acciones -->
    <div class="grupo-sidebar-list">
        
        <!-- Estadísticas -->
        <div class="grupo-stats-list">
            <div class="stat-item-list">
                <i class="fas fa-users"></i>
                <span class="stat-number-list"><?php echo format_member_count($numero_miembros); ?></span>
                <span class="stat-label-list"><?php _e('miembros', 'telegram-groups'); ?></span>
            </div>
            
            <?php if ($fecha_actualizacion) : ?>
                <div class="stat-item-list">
                    <i class="fas fa-clock"></i>
                    <span class="stat-text-list"><?php echo time_since_activity($fecha_actualizacion); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Acciones -->
        <div class="grupo-acciones-list">
            <a href="<?php the_permalink(); ?>" class="btn btn-ver-list">
                <i class="fas fa-eye"></i>
                <?php _e('Ver más', 'telegram-groups'); ?>
            </a>
            
            <?php if ($enlace_telegram) : ?>
                <a href="<?php echo esc_url($enlace_telegram); ?>" 
                   class="btn btn-telegram-list" 
                   target="_blank" 
                   rel="noopener noreferrer">
                    <i class="fab fa-telegram-plane"></i>
                    <?php _e('Unirse', 'telegram-groups'); ?>
                </a>
            <?php endif; ?>
        </div>
        
    </div>
    
</article>

<style>
/* Grupo Card Search List Styles */
.grupo-card-search-list {
    background: var(--telegram-white);
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0, 136, 204, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    display: grid;
    grid-template-columns: 120px 1fr auto;
    gap: 1rem;
    padding: 1rem;
    align-items: start;
}

.grupo-card-search-list:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0, 136, 204, 0.15);
}

.grupo-card-search-list.destacado {
    border-left: 4px solid #ffd700;
    background: linear-gradient(90deg, #fffbf0, var(--telegram-white));
}

/* Imagen list */
.grupo-imagen-list {
    position: relative;
    width: 120px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.grupo-imagen-list img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.grupo-placeholder-list {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--telegram-white);
    font-size: 1.5rem;
}

.badge-destacado-list {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: linear-gradient(45deg, #ffd700, #ff8c00);
    color: var(--telegram-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
}

/* Contenido list */
.grupo-content-list {
    min-width: 0;
    flex: 1;
}

.grupo-header-list {
    margin-bottom: 0.75rem;
}

.grupo-titulo-list {
    margin: 0 0 0.5rem 0;
    font-size: 1.2rem;
    font-weight: 700;
    line-height: 1.3;
}

.grupo-titulo-list a {
    color: var(--telegram-dark);
    text-decoration: none;
    transition: color 0.3s ease;
}

.grupo-titulo-list a:hover {
    color: var(--telegram-blue);
}

.grupo-meta-list {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.85rem;
    color: var(--telegram-gray);
}

.meta-categoria-list,
.meta-ciudad-list,
.meta-estado-list {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.meta-categoria-list a,
.meta-ciudad-list a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.meta-categoria-list a:hover,
.meta-ciudad-list a:hover {
    color: var(--telegram-blue);
}

.meta-estado-list.estado-activo {
    color: var(--telegram-success);
}

.meta-estado-list.estado-inactivo {
    color: var(--telegram-danger);
}

.grupo-descripcion-list {
    margin-bottom: 0.75rem;
}

.grupo-descripcion-list p {
    font-size: 0.9rem;
    color: var(--telegram-gray);
    line-height: 1.5;
    margin: 0;
}

/* Etiquetas list */
.grupo-etiquetas-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.etiqueta-tag-list {
    background: rgba(0, 136, 204, 0.1);
    color: var(--telegram-blue);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.etiqueta-tag-list:hover {
    background: var(--telegram-blue);
    color: var(--telegram-white);
}

.etiquetas-more {
    font-size: 0.75rem;
    color: var(--telegram-gray);
    font-style: italic;
}

/* Sidebar list */
.grupo-sidebar-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: flex-end;
    text-align: right;
    min-width: 150px;
}

.grupo-stats-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
}

.stat-item-list {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: var(--telegram-gray);
}

.stat-item-list i {
    color: var(--telegram-blue);
    width: 15px;
    text-align: center;
}

.stat-number-list {
    font-weight: 700;
    color: var(--telegram-blue);
}

.stat-label-list {
    font-size: 0.75rem;
}

.stat-text-list {
    font-size: 0.8rem;
}

/* Acciones list */
.grupo-acciones-list {
    display: flex;
    gap: 0.5rem;
    flex-direction: column;
}

.btn-ver-list,
.btn-telegram-list {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 100px;
}

.btn-ver-list {
    background: #f8f9fa;
    color: var(--telegram-gray);
    border: 1px solid #e9ecef;
}

.btn-ver-list:hover {
    background: #e9ecef;
    color: var(--telegram-dark);
}

.btn-telegram-list {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
}

.btn-telegram-list:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(0, 136, 204, 0.3);
    color: var(--telegram-white);
}

/* Responsive */
@media (max-width: 1024px) {
    .grupo-card-search-list {
        grid-template-columns: 100px 1fr;
        grid-template-areas: 
            "imagen content"
            "sidebar sidebar";
    }
    
    .grupo-imagen-list {
        grid-area: imagen;
        width: 100px;
        height: 70px;
    }
    
    .grupo-content-list {
        grid-area: content;
    }
    
    .grupo-sidebar-list {
        grid-area: sidebar;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }
    
    .grupo-acciones-list {
        flex-direction: row;
    }
}

@media (max-width: 768px) {
    .grupo-card-search-list {
        grid-template-columns: 1fr;
        grid-template-areas: 
            "imagen"
            "content"
            "sidebar";
        text-align: center;
    }
    
    .grupo-imagen-list {
        width: 100%;
        height: 120px;
        margin: 0 auto;
    }
    
    .grupo-meta-list {
        justify-content: center;
    }
    
    .grupo-etiquetas-list {
        justify-content: center;
    }
    
    .grupo-sidebar-list {
        align-items: center;
        text-align: center;
    }
    
    .grupo-stats-list {
        flex-direction: row;
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .grupo-card-search-list {
        padding: 0.75rem;
    }
    
    .grupo-meta-list {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .grupo-acciones-list {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-ver-list,
    .btn-telegram-list {
        width: 100%;
    }
}
</style>