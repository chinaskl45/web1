<?php
/**
 * Template part para mostrar tarjeta de grupo en resultados de búsqueda
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

// Obtener taxonomías
$categoria = wp_get_post_terms($grupo_id, 'categoria_grupo', array('fields' => 'all'));
$ciudad = wp_get_post_terms($grupo_id, 'ciudad_grupo', array('fields' => 'all'));
$es_destacado = has_term('destacado', 'destacados', $grupo_id);

$categoria_principal = !empty($categoria) ? $categoria[0] : null;
$ciudad_principal = !empty($ciudad) ? $ciudad[0] : null;
$categoria_style = $categoria_principal ? get_categoria_style($categoria_principal->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
?>

<article class="grupo-card-search <?php echo $es_destacado ? 'destacado' : ''; ?>" data-grupo-id="<?php echo $grupo_id; ?>">
    
    <!-- Imagen del grupo -->
    <div class="grupo-imagen-search">
        <?php if ($imagen_grupo) : ?>
            <img src="<?php echo esc_url($imagen_grupo['sizes']['grupo-card'] ?? $imagen_grupo['url']); ?>" 
                 alt="<?php echo esc_attr($imagen_grupo['alt'] ?: get_the_title()); ?>"
                 loading="lazy">
        <?php else : ?>
            <div class="grupo-placeholder-search" style="background: <?php echo $categoria_style['color']; ?>">
                <i class="<?php echo $categoria_style['icon']; ?>"></i>
            </div>
        <?php endif; ?>
        
        <!-- Badge destacado -->
        <?php if ($es_destacado) : ?>
            <div class="badge-destacado-search">
                <i class="fas fa-star"></i>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Contenido -->
    <div class="grupo-content-search">
        <!-- Título -->
        <h3 class="grupo-titulo-search">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <!-- Meta información -->
        <div class="grupo-meta-search">
            <?php if ($categoria_principal) : ?>
                <span class="meta-categoria-search">
                    <i class="<?php echo $categoria_style['icon']; ?>" style="color: <?php echo $categoria_style['color']; ?>"></i>
                    <a href="<?php echo get_term_link($categoria_principal); ?>">
                        <?php echo $categoria_principal->name; ?>
                    </a>
                </span>
            <?php endif; ?>
            
            <?php if ($ciudad_principal) : ?>
                <span class="meta-ciudad-search">
                    <i class="fas fa-map-marker-alt"></i>
                    <a href="<?php echo get_term_link($ciudad_principal); ?>">
                        <?php echo $ciudad_principal->name; ?>
                    </a>
                </span>
            <?php endif; ?>
            
            <span class="meta-miembros-search">
                <i class="fas fa-users"></i>
                <?php echo format_member_count($numero_miembros); ?>
            </span>
        </div>
        
        <!-- Descripción -->
        <?php if ($descripcion_corta) : ?>
            <div class="grupo-descripcion-search">
                <p><?php echo wp_trim_words($descripcion_corta, 20, '...'); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Acciones -->
        <div class="grupo-acciones-search">
            <a href="<?php the_permalink(); ?>" class="btn btn-ver-search">
                <i class="fas fa-eye"></i>
                <?php _e('Ver más', 'telegram-groups'); ?>
            </a>
            
            <?php if ($enlace_telegram) : ?>
                <a href="<?php echo esc_url($enlace_telegram); ?>" 
                   class="btn btn-telegram-search" 
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
/* Grupo Card Search Styles */
.grupo-card-search {
    background: var(--telegram-white);
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0, 136, 204, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.grupo-card-search:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(0, 136, 204, 0.2);
}

.grupo-card-search.destacado {
    border: 2px solid #ffd700;
}

/* Imagen search */
.grupo-imagen-search {
    position: relative;
    height: 140px;
    overflow: hidden;
}

.grupo-imagen-search img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.grupo-placeholder-search {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--telegram-white);
    font-size: 2rem;
}

.badge-destacado-search {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(45deg, #ffd700, #ff8c00);
    color: var(--telegram-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    z-index: 2;
}

/* Contenido search */
.grupo-content-search {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.grupo-titulo-search {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 700;
    line-height: 1.3;
}

.grupo-titulo-search a {
    color: var(--telegram-dark);
    text-decoration: none;
    transition: color 0.3s ease;
}

.grupo-titulo-search a:hover {
    color: var(--telegram-blue);
}

.grupo-meta-search {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    font-size: 0.8rem;
    color: var(--telegram-gray);
}

.meta-categoria-search,
.meta-ciudad-search,
.meta-miembros-search {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.meta-categoria-search a,
.meta-ciudad-search a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.meta-categoria-search a:hover,
.meta-ciudad-search a:hover {
    color: var(--telegram-blue);
}
.meta-miembros-search {
    color: var(--telegram-blue);
    font-weight: 600;
}

.grupo-descripcion-search {
    margin-bottom: 1rem;
    flex: 1;
}

.grupo-descripcion-search p {
    font-size: 0.85rem;
    color: var(--telegram-gray);
    line-height: 1.5;
    margin: 0;
}

/* Acciones search */
.grupo-acciones-search {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-top: auto;
}

.btn-ver-search,
.btn-telegram-search {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-ver-search {
    background: #f8f9fa;
    color: var(--telegram-gray);
    border: 1px solid #e9ecef;
}

.btn-ver-search:hover {
    background: #e9ecef;
    color: var(--telegram-dark);
}

.btn-telegram-search {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
}

.btn-telegram-search:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(0, 136, 204, 0.3);
    color: var(--telegram-white);
}

/* Responsive */
@media (max-width: 480px) {
    .grupo-imagen-search {
        height: 120px;
    }
    
    .grupo-content-search {
        padding: 0.75rem;
    }
    
    .grupo-meta-search {
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>