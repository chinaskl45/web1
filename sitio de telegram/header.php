<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <!-- Header Principal -->
    <header id="masthead" class="site-header">
        <div class="header-main">
            <div class="container">
                <div class="header-content">
                    <!-- Logo -->
                    <div class="site-branding">
                        <?php if (has_custom_logo()) : ?>
                            <div class="site-logo">
                                <?php the_custom_logo(); ?>
                            </div>
                        <?php else : ?>
                            <h1 class="site-title">
                                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                    <i class="fab fa-telegram-plane"></i>
                                    <?php bloginfo('name'); ?>
                                </a>
                            </h1>
                        <?php endif; ?>
                    </div>

                    <!-- Navegación Principal -->
                    <nav id="site-navigation" class="main-navigation">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_id' => 'primary-menu',
                            'menu_class' => 'nav-menu',
                            'fallback_cb' => 'telegram_groups_fallback_menu',
                        ));
                        ?>
                    </nav>

                    <!-- Botón Añadir Grupo -->
                    <div class="header-actions">
                        <a href="<?php echo esc_url(home_url('/añadir-grupo')); ?>" class="btn-add-group">
                            <i class="fas fa-plus"></i>
                            <span><?php _e('Añadir Grupo', 'telegram-groups'); ?></span>
                        </a>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="sr-only"><?php _e('Menú Principal', 'telegram-groups'); ?></span>
                        <span class="menu-toggle-bar"></span>
                        <span class="menu-toggle-bar"></span>
                        <span class="menu-toggle-bar"></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div id="content" class="site-content">

<style>
/* Header Styles */
.site-header {
    background: linear-gradient(135deg, var(--telegram-blue), var(--telegram-light-blue));
    color: var(--telegram-white);
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0, 136, 204, 0.3);
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 0;
    min-height: 70px;
}

.site-branding .site-title {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
}

.site-branding .site-title a {
    color: var(--telegram-white);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
}

.site-branding .site-title a:hover {
    color: rgba(255, 255, 255, 0.9);
}

.site-logo img {
    max-height: 50px;
    width: auto;
}

/* Navegación */
.main-navigation {
    display: flex;
    align-items: center;
}

.nav-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 2rem;
}

.nav-menu li {
    margin: 0;
}

.nav-menu a {
    color: var(--telegram-white);
    text-decoration: none;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    transition: all 0.3s ease;
    position: relative;
}

.nav-menu a:hover,
.nav-menu .current-menu-item a {
    background: rgba(255, 255, 255, 0.2);
    color: var(--telegram-white);
}

/* Botón Añadir Grupo */
.btn-add-group {
    background: var(--telegram-white);
    color: var(--telegram-blue);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-add-group:hover {
    background: transparent;
    color: var(--telegram-white);
    border-color: var(--telegram-white);
    transform: translateY(-2px);
}

/* Mobile Menu */
.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    flex-direction: column;
    gap: 3px;
}

.menu-toggle-bar {
    width: 25px;
    height: 3px;
    background: var(--telegram-white);
    border-radius: 2px;
    transition: all 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .header-content {
        padding: 0.75rem 0;
    }
    
    .main-navigation {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--telegram-blue);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    
    .main-navigation.active {
        display: block;
    }
    
    .nav-menu {
        flex-direction: column;
        padding: 1rem;
        gap: 0;
    }
    
    .nav-menu li {
        width: 100%;
    }
    
    .nav-menu a {
        display: block;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    
    .mobile-menu-toggle {
        display: flex;
    }
    
    .btn-add-group span {
        display: none;
    }
    
    .btn-add-group {
        padding: 0.75rem;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        justify-content: center;
    }
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
</style>

<?php
// Menú fallback si no hay menú configurado
function telegram_groups_fallback_menu() {
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . home_url('/') . '">' . __('Inicio', 'telegram-groups') . '</a></li>';
    echo '<li><a href="' . get_post_type_archive_link('grupo') . '">' . __('Grupos', 'telegram-groups') . '</a></li>';
    echo '<li><a href="' . home_url('/categorias') . '">' . __('Categorías', 'telegram-groups') . '</a></li>';
    echo '</ul>';
}
?>
