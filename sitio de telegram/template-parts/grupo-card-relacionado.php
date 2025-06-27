<?php
/**
 * Template part para mostrar tarjeta de grupo relacionado (más compacta)
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
$categoria_principal = !empty($categoria) ? $categoria[0] : null;
$categoria_style = $categoria_principal ? get_categoria_style($categoria_principal->slug) : array('icon' => 'fas fa-users', 'color' => '#0088cc');
?>

<article class="grupo-card-relacionado" data-grupo-id="<?php echo $grupo_id; ?>">
    
    <!-- Imagen del grupo -->
    <div class="grupo-imagen-relacionado">
        <?php if ($imagen_grupo) : ?>
            <img src="<?php echo esc_url($imagen_grupo['sizes']['grupo-card'] ?? $imagen_grupo['url']); ?>" 
                 alt="<?php echo esc_attr($imagen_grupo['alt'] ?: get_the_title()); ?>">
        <?php else : ?>
            <div class="grupo-placeholder-relacionado" style="background: <?php echo $categoria_style['color']; ?>">
                <i class="<?php echo $categoria_style['icon']; ?>"></i>
            </div>
        <?php endif; ?>
        
        <!-- Badge de categoría -->
        <?php if ($categoria_principal) : ?>
            <div class="categoria-badge-relacionado" style="background: <?php echo $categoria_style['color']; ?>">
                <i class="<?php echo $categoria_style['icon']; ?>"></i>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Contenido -->
    <div class="grupo-content-relacionado">
        <!-- Título -->
        <h3 class="grupo-titulo-relacionado">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <!-- Meta información -->
        <div class="grupo-meta-relacionado">
            <?php if ($categoria_principal) : ?>
                <span class="meta-categoria-relacionado" style="color: <?php echo $categoria_style['color']; ?>">
                    <i class="<?php echo $categoria_style['icon']; ?>"></i>
                    <?php echo $categoria_principal->name; ?>
                </span>
            <?php endif; ?>
        </div>
        
        <!-- Descripción corta -->
        <?php if ($descripcion_corta) : ?>
            <div class="grupo-descripcion-relacionado">
                <p><?php echo wp_trim_words($descripcion_corta, 12, '...'); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Estadísticas -->
        <div class="grupo-stats-relacionado">
            <span class="stat-miembros-relacionado">
                <i class="fas fa-users"></i>
                <?php echo format_member_count($numero_miembros); ?>
            </span>
            <span class="stat-estado-relacionado estado-<?php echo strtolower($estado_grupo); ?>">
                <i class="fas fa-circle"></i>
                <?php echo $estado_grupo; ?>
            </span>
        </div>
        
        <!-- Acciones -->
        <div class="grupo-acciones-relacionado">
            <?php if ($enlace_telegram) : ?>
                <a href="<?php echo esc_url($enlace_telegram); ?>" 
                   class="btn-unirse-relacionado" 
                   target="_blank" 
                   rel="noopener noreferrer">
                    <i class="fab fa-telegram-plane"></i>
                    <?php _e('Unirse', 'telegram-groups'); ?>
                </a>
            <?php endif; ?>
            <a href="<?php the_permalink(); ?>" class="btn-ver-relacionado">
                <i class="fas fa-eye"></i>
                <?php _e('Ver', 'telegram-groups'); ?>
            </a>
        </div>
    </div>
</article>

<style>
/* Grupo Card Relacionado Styles */
.grupo-card-relacionado {
    background: var(--telegram-white);
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 136, 204, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.grupo-card-relacionado:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 136, 204, 0.15);
}

/* Imagen relacionado */
.grupo-imagen-relacionado {
    position: relative;
    height: 120px;
    overflow: hidden;
}

.grupo-imagen-relacionado img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.grupo-placeholder-relacionado {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--telegram-white);
    font-size: 2rem;
}

.categoria-badge-relacionado {
    position: absolute;
    top: 8px;
    left: 8px;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--telegram-white);
    font-size: 0.7rem;
    z-index: 2;
}

/* Contenido relacionado */
.grupo-content-relacionado {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.grupo-titulo-relacionado {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.3;
}

.grupo-titulo-relacionado a {
    color: var(--telegram-dark);
    text-decoration: none;
    transition: color 0.3s ease;
}

.grupo-titulo-relacionado a:hover {
    color: var(--telegram-blue);
}

.grupo-meta-relacionado {
    margin-bottom: 0.5rem;
}

.meta-categoria-relacionado {
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 3px;
}

.grupo-descripcion-relacionado {
    margin-bottom: 0.75rem;
    flex: 1;
}

.grupo-descripcion-relacionado p {
    font-size: 0.8rem;
    color: var(--telegram-gray);
    line-height: 1.4;
    margin: 0;
}

/* Estadísticas relacionado */
.grupo-stats-relacionado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.75rem;
}

.stat-miembros-relacionado {
    color: var(--telegram-blue);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 3px;
}

.stat-estado-relacionado {
    display: flex;
    align-items: center;
    gap: 3px;
}

.stat-estado-relacionado.estado-activo {
    color: var(--telegram-success);
}

.stat-estado-relacionado.estado-inactivo {
    color: var(--telegram-danger);
}

/* Acciones relacionado */
.grupo-acciones-relacionado {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.btn-unirse-relacionado,
.btn-ver-relacionado {
    padding: 6px 10px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-unirse-relacionado {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
}

.btn-unirse-relacionado:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 136, 204, 0.3);
    color: var(--telegram-white);
}

.btn-ver-relacionado {
    background: #f8f9fa;
    color: var(--telegram-gray);
    border: 1px solid #e9ecef;
}

.btn-ver-relacionado:hover {
    background: #e9ecef;
    color: var(--telegram-dark);
}

/* Responsive */
@media (max-width: 480px) {
    .grupo-imagen-relacionado {
        height: 100px;
    }
    
    .grupo-content-relacionado {
        padding: 0.75rem;
    }
    
    .grupo-acciones-relacionado {
        grid-template-columns: 1fr;
    }
}
</style>
