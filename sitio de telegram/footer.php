<?php
/**
 * Footer template
 * 
 * @package Telegram_Groups
 */
?>

    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        
        <!-- Footer Principal -->
        <div class="footer-main">
            <div class="container">
                <div class="footer-content">
                    
                    <!-- Columna 1: Información del sitio -->
                    <div class="footer-column footer-about">
                        <div class="footer-logo">
                            <?php if (has_custom_logo()) : ?>
                                <div class="footer-logo-img">
                                    <?php the_custom_logo(); ?>
                                </div>
                            <?php else : ?>
                                <h3 class="footer-site-title">
                                    <i class="fab fa-telegram-plane"></i>
                                    <?php bloginfo('name'); ?>
                                </h3>
                            <?php endif; ?>
                        </div>
                        
                        <div class="footer-description">
                            <p><?php 
                                $footer_description = get_theme_mod('footer_description', 
                                    __('La mayor comunidad de grupos de Telegram. Encuentra y únete a grupos organizados por categorías, ciudades e intereses.', 'telegram-groups')
                                );
                                echo esc_html($footer_description);
                            ?></p>
                        </div>
                        
                        <!-- Redes sociales -->
                        <div class="footer-social">
                            <?php
                            $social_links = array(
                                'telegram' => get_theme_mod('social_telegram', ''),
                                'twitter' => get_theme_mod('social_twitter', ''),
                                'facebook' => get_theme_mod('social_facebook', ''),
                                'instagram' => get_theme_mod('social_instagram', ''),
                                'youtube' => get_theme_mod('social_youtube', '')
                            );
                            
                            foreach ($social_links as $platform => $url) :
                                if (!empty($url)) :
                                    $icon_class = ($platform === 'telegram') ? 'fab fa-telegram-plane' : 'fab fa-' . $platform;
                            ?>
                                <a href="<?php echo esc_url($url); ?>" 
                                   class="social-link social-<?php echo $platform; ?>"
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   title="<?php echo ucfirst($platform); ?>">
                                    <i class="<?php echo $icon_class; ?>"></i>
                                </a>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                    
                    <!-- Columna 2: Enlaces rápidos -->
                    <div class="footer-column footer-links">
                        <h4 class="footer-title">
                            <i class="fas fa-link"></i>
                            <?php _e('Enlaces Rápidos', 'telegram-groups'); ?>
                        </h4>
                        <ul class="footer-menu">
                            <li><a href="<?php echo home_url('/'); ?>"><?php _e('Inicio', 'telegram-groups'); ?></a></li>
                            <li><a href="<?php echo get_post_type_archive_link('grupo'); ?>"><?php _e('Todos los Grupos', 'telegram-groups'); ?></a></li>
                            <li><a href="<?php echo home_url('/categorias'); ?>"><?php _e('Categorías', 'telegram-groups'); ?></a></li>
                            <li><a href="<?php echo home_url('/añadir-grupo'); ?>"><?php _e('Añadir Grupo', 'telegram-groups'); ?></a></li>
                            <li><a href="<?php echo home_url('/como-funciona'); ?>"><?php _e('Cómo Funciona', 'telegram-groups'); ?></a></li>
                            <li><a href="<?php echo home_url('/contacto'); ?>"><?php _e('Contacto', 'telegram-groups'); ?></a></li>
                        </ul>
                    </div>
                    
                    <!-- Columna 3: Categorías populares -->
                    <div class="footer-column footer-categories">
                        <h4 class="footer-title">
                            <i class="fas fa-th-large"></i>
                            <?php _e('Categorías Populares', 'telegram-groups'); ?>
                        </h4>
                        <ul class="footer-categories-list">
                            <?php
                            $footer_categories = get_terms(array(
                                'taxonomy' => 'categoria_grupo',
                                'hide_empty' => true,
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 6
                            ));
                            
                            if ($footer_categories && !is_wp_error($footer_categories)) :
                                foreach ($footer_categories as $categoria) :
                                    $categoria_style = get_categoria_style($categoria->slug);
                            ?>
                                <li>
                                    <a href="<?php echo get_term_link($categoria); ?>" class="footer-category-link">
                                        <i class="<?php echo $categoria_style['icon']; ?>" style="color: <?php echo $categoria_style['color']; ?>"></i>
                                        <span><?php echo $categoria->name; ?></span>
                                        <span class="category-count">(<?php echo $categoria->count; ?>)</span>
                                    </a>
                                </li>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                    
                    <!-- Columna 4: Estadísticas y newsletter -->
                    <div class="footer-column footer-stats">
                        <h4 class="footer-title">
                            <i class="fas fa-chart-bar"></i>
                            <?php _e('Estadísticas del Sitio', 'telegram-groups'); ?>
                        </h4>
                        
                        <!-- Estadísticas rápidas -->
                        <div class="footer-stats-grid">
                            <?php
                            $total_grupos = wp_count_posts('grupo')->publish;
                            $total_categorias = wp_count_terms('categoria_grupo');
                            $total_ciudades = wp_count_terms('ciudad_grupo');
                            
                            // Calcular total de miembros (opcional)
                            $grupos_sample = get_posts(array(
                                'post_type' => 'grupo',
                                'numberposts' => -1,
                                'meta_key' => 'numero_miembros',
                                'fields' => 'ids'
                            ));
                            
                            $total_miembros = 0;
                            foreach ($grupos_sample as $grupo_id) {
                                $miembros = get_field('numero_miembros', $grupo_id);
                                $total_miembros += intval($miembros);
                            }
                            ?>
                            
                            <div class="footer-stat-item">
                                <span class="stat-number"><?php echo number_format($total_grupos); ?></span>
                                <span class="stat-label"><?php _e('Grupos', 'telegram-groups'); ?></span>
                            </div>
                            
                            <div class="footer-stat-item">
                                <span class="stat-number"><?php echo format_member_count($total_miembros); ?></span>
                                <span class="stat-label"><?php _e('Miembros', 'telegram-groups'); ?></span>
                            </div>
                            
                            <div class="footer-stat-item">
                                <span class="stat-number"><?php echo $total_categorias; ?></span>
                                <span class="stat-label"><?php _e('Categorías', 'telegram-groups'); ?></span>
                            </div>
                            
                            <div class="footer-stat-item">
                                <span class="stat-number"><?php echo $total_ciudades; ?></span>
                                <span class="stat-label"><?php _e('Ciudades', 'telegram-groups'); ?></span>
                            </div>
                        </div>
                        
                        <!-- Newsletter (opcional) -->
                        <?php if (get_theme_mod('enable_newsletter', false)) : ?>
                            <div class="footer-newsletter">
                                <h5><?php _e('Mantente actualizado', 'telegram-groups'); ?></h5>
                                <p><?php _e('Recibe notificaciones de nuevos grupos', 'telegram-groups'); ?></p>
                                <form class="newsletter-form" action="#" method="post">
                                    <div class="newsletter-input-group">
                                        <input type="email" 
                                               name="newsletter_email" 
                                               placeholder="<?php _e('Tu email', 'telegram-groups'); ?>" 
                                               required>
                                        <button type="submit" class="newsletter-btn">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    
                    <!-- Copyright -->
                    <div class="footer-copyright">
                        <p>
                            &copy; <?php echo date('Y'); ?> 
                            <strong><?php bloginfo('name'); ?></strong>. 
                            <?php _e('Todos los derechos reservados.', 'telegram-groups'); ?>
                        </p>
                    </div>
                    
                    <!-- Menú legal -->
                    <div class="footer-legal">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_class' => 'footer-legal-menu',
                            'fallback_cb' => 'telegram_groups_footer_fallback_menu',
                            'depth' => 1
                        ));
                        ?>
                    </div>
                    
                    <!-- Información adicional -->
                    <div class="footer-info">
                        <span class="footer-powered">
                            <?php _e('Desarrollado con', 'telegram-groups'); ?> 
                            <i class="fas fa-heart" style="color: #e74c3c;"></i> 
                            <?php _e('para la comunidad Telegram', 'telegram-groups'); ?>
                        </span>
                    </div>
                    
                </div>
            </div>
        </div>
        
    </footer><!-- #colophon -->
    
</div><!-- #page -->

<!-- Botón volver arriba -->
<button id="back-to-top" class="back-to-top" style="display: none;">
    <i class="fas fa-chevron-up"></i>
</button>

<?php wp_footer(); ?>

<style>
/* Footer Styles */
.site-footer {
    background: var(--telegram-dark);
    color: var(--telegram-white);
    margin-top: 3rem;
}

/* Footer Principal */
.footer-main {
    padding: 3rem 0 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-content {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1.5fr;
    gap: 2rem;
}

.footer-column {
    padding: 0;
}

/* Logo y descripción */
.footer-logo-img img {
    max-height: 60px;
    width: auto;
    filter: brightness(0) invert(1);
}

.footer-site-title {
    margin: 0 0 1rem 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--telegram-white);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.footer-description p {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin: 0 0 1.5rem 0;
}

/* Redes sociales */
.footer-social {
    display: flex;
    gap: 1rem;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--telegram-white);
    background: rgba(255, 255, 255, 0.1);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.social-link:hover {
    transform: translateY(-3px);
    background: var(--telegram-blue);
    color: var(--telegram-white);
}

.social-telegram:hover { background: #0088cc; }
.social-twitter:hover { background: #1da1f2; }
.social-facebook:hover { background: #4267b2; }
.social-instagram:hover { background: #e4405f; }
.social-youtube:hover { background: #ff0000; }

/* Títulos de columnas */
.footer-title {
    color: var(--telegram-white);
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--telegram-blue);
}

.footer-title i {
    color: var(--telegram-blue);
}

/* Menús del footer */
.footer-menu,
.footer-categories-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-menu li,
.footer-categories-list li {
    margin-bottom: 0.75rem;
}

.footer-menu a,
.footer-category-link {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0;
}

.footer-menu a:hover,
.footer-category-link:hover {
    color: var(--telegram-blue);
    transform: translateX(5px);
}

.footer-category-link .category-count {
    font-size: 0.8rem;
    opacity: 0.7;
    margin-left: auto;
}

/* Estadísticas del footer */
.footer-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 2rem;
}

.footer-stat-item {
    text-align: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-stat-item .stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--telegram-blue);
    line-height: 1;
}

.footer-stat-item .stat-label {
    display: block;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 0.25rem;
}

/* Newsletter */
.footer-newsletter {
    background: rgba(255, 255, 255, 0.05);
    padding: 1.5rem;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-newsletter h5 {
    margin: 0 0 0.5rem 0;
    color: var(--telegram-white);
    font-size: 1rem;
}

.footer-newsletter p {
    margin: 0 0 1rem 0;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
}

.newsletter-input-group {
    display: flex;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 25px;
    overflow: hidden;
}

.newsletter-input-group input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: none;
    outline: none;
    background: transparent;
    color: var(--telegram-white);
    font-size: 0.9rem;
}

.newsletter-input-group input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.newsletter-btn {
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: none;
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

.newsletter-btn:hover {
    background: var(--telegram-light-blue);
}

/* Footer Bottom */
.footer-bottom {
    background: rgba(0, 0, 0, 0.3);
    padding: 1.5rem 0;
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.footer-copyright p {
    margin: 0;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
}

.footer-legal-menu {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 2rem;
}

.footer-legal-menu a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.footer-legal-menu a:hover {
    color: var(--telegram-blue);
}

.footer-info {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.85rem;
}

/* Botón volver arriba */
.back-to-top {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--telegram-blue);
    color: var(--telegram-white);
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 136, 204, 0.3);
    transition: all 0.3s ease;
    z-index: 1000;
    font-size: 1.1rem;
}

.back-to-top:hover {
    background: var(--telegram-light-blue);
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 136, 204, 0.4);
}

/* Responsive */
@media (max-width: 1024px) {
    .footer-content {
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
    
    .footer-about {
        grid-column: 1 / -1;
        text-align: center;
        margin-bottom: 1rem;
    }
}

@media (max-width: 768px) {
    .footer-main {
        padding: 2rem 0 1.5rem;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        gap: 2rem;
        text-align: center;
    }
    
    .footer-bottom-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .footer-legal-menu {
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }
    
    .footer-stats-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
    }
    
    .back-to-top {
        bottom: 1rem;
        right: 1rem;
        width: 45px;
        height: 45px;
    }
}

@media (max-width: 480px) {
    .footer-stats-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .footer-social {
        justify-content: center;
    }
    
    .newsletter-input-group {
        flex-direction: column;
        border-radius: 8px;
    }
    
    .newsletter-btn {
        border-radius: 0 0 8px 8px;
    }
}
</style>

<script>
// JavaScript para el botón "volver arriba"
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('back-to-top');
    
    // Mostrar/ocultar botón según scroll
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });
    
    // Scroll suave al hacer clic
    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Newsletter form (si está habilitado)
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[name="newsletter_email"]').value;
            
            // Aquí puedes agregar la lógica para enviar el email
            // Por ejemplo, enviar a una API o servicio de newsletter
            
            alert('<?php _e('¡Gracias por suscribirte! Te mantendremos informado.', 'telegram-groups'); ?>');
            this.reset();
        });
    }
});
</script>

<?php
// Menú fallback para el footer legal
function telegram_groups_footer_fallback_menu() {
    echo '<ul class="footer-legal-menu">';
    echo '<li><a href="' . home_url('/privacidad') . '">' . __('Política de Privacidad', 'telegram-groups') . '</a></li>';
    echo '<li><a href="' . home_url('/terminos') . '">' . __('Términos de Uso', 'telegram-groups') . '</a></li>';
    echo '<li><a href="' . home_url('/cookies') . '">' . __('Cookies', 'telegram-groups') . '</a></li>';
    echo '</ul>';
}
?>

</body>
</html>
