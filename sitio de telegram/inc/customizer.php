<?php
/**
 * Telegram Groups Theme Customizer - VERSIÓN FUNCIONAL COMPLETA
 * 
 * @package Telegram_Groups
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configuración principal del customizer
 */
function telegram_groups_customize_register($wp_customize) {
    
    // Configurar live preview
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';

    /* ========================================
       SECCIÓN: IDENTIDAD DEL SITIO (Mejorada)
    ======================================== */
    
    // Eslogan personalizado
    $wp_customize->add_setting('site_tagline', array(
        'default' => 'La mayor comunidad de grupos de Telegram',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('site_tagline', array(
        'label' => __('Eslogan del Sitio', 'telegram-groups'),
        'section' => 'title_tagline',
        'type' => 'text',
        'priority' => 25,
        'description' => __('Texto que aparece bajo el título principal.', 'telegram-groups'),
    ));

    /* ========================================
       SECCIÓN: CONFIGURACIÓN DEL HEADER
    ======================================== */
    
    $wp_customize->add_section('telegram_header', array(
        'title' => __('Header y Navegación', 'telegram-groups'),
        'priority' => 30,
        'description' => __('Personaliza el header y la navegación principal.', 'telegram-groups'),
    ));

    // Logo personalizado (adicional al de WordPress)
    $wp_customize->add_setting('header_logo_text', array(
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('header_logo_text', array(
        'label' => __('Texto del Logo', 'telegram-groups'),
        'section' => 'telegram_header',
        'type' => 'text',
    ));

    // Color del header
    $wp_customize->add_setting('header_bg_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_bg_color', array(
        'label' => __('Color de Fondo del Header', 'telegram-groups'),
        'section' => 'telegram_header',
    )));

    // Header sticky
    $wp_customize->add_setting('header_sticky', array(
        'default' => true,
        'sanitize_callback' => 'telegram_groups_sanitize_checkbox',
    ));

    $wp_customize->add_control('header_sticky', array(
        'label' => __('Header Fijo', 'telegram-groups'),
        'section' => 'telegram_header',
        'type' => 'checkbox',
        'description' => __('Mantener el header visible al hacer scroll.', 'telegram-groups'),
    ));

    // Mostrar botón "Añadir Grupo"
    $wp_customize->add_setting('show_add_group_btn', array(
        'default' => true,
        'sanitize_callback' => 'telegram_groups_sanitize_checkbox',
    ));

    $wp_customize->add_control('show_add_group_btn', array(
        'label' => __('Mostrar Botón "Añadir Grupo"', 'telegram-groups'),
        'section' => 'telegram_header',
        'type' => 'checkbox',
    ));

    // Texto del botón
    $wp_customize->add_setting('add_group_btn_text', array(
        'default' => 'Añadir Grupo',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('add_group_btn_text', array(
        'label' => __('Texto del Botón', 'telegram-groups'),
        'section' => 'telegram_header',
        'type' => 'text',
        'active_callback' => 'telegram_groups_show_add_btn_active',
    ));

    /* ========================================
       SECCIÓN: COLORES PRINCIPALES
    ======================================== */
    
    $wp_customize->add_section('telegram_colors', array(
        'title' => __('Colores del Tema', 'telegram-groups'),
        'priority' => 40,
        'description' => __('Personaliza los colores principales del sitio.', 'telegram-groups'),
    ));

    // Color principal (Azul Telegram)
    $wp_customize->add_setting('primary_color', array(
        'default' => '#0088cc',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label' => __('Color Principal', 'telegram-groups'),
        'section' => 'telegram_colors',
        'description' => __('Color principal del sitio (botones, enlaces, etc.)', 'telegram-groups'),
    )));

    // Color secundario
    $wp_customize->add_setting('secondary_color', array(
        'default' => '#54a9eb',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
        'label' => __('Color Secundario', 'telegram-groups'),
        'section' => 'telegram_colors',
        'description' => __('Color para gradientes y elementos secundarios.', 'telegram-groups'),
    )));

    // Color de fondo
    $wp_customize->add_setting('bg_color', array(
        'default' => '#f8f9fa',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'bg_color', array(
        'label' => __('Color de Fondo', 'telegram-groups'),
        'section' => 'telegram_colors',
    )));

    /* ========================================
       SECCIÓN: PÁGINA DE INICIO
    ======================================== */
    
    $wp_customize->add_section('telegram_home', array(
        'title' => __('Página de Inicio', 'telegram-groups'),
        'priority' => 50,
        'description' => __('Configura el contenido de la página principal.', 'telegram-groups'),
    ));

    // Título principal
    $wp_customize->add_setting('home_title', array(
        'default' => 'Descubre los Mejores Grupos de Telegram',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('home_title', array(
        'label' => __('Título Principal', 'telegram-groups'),
        'section' => 'telegram_home',
        'type' => 'text',
    ));

    // Descripción principal
    $wp_customize->add_setting('home_description', array(
        'default' => 'Únete a comunidades organizadas por categorías, ciudades e intereses.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('home_description', array(
        'label' => __('Descripción Principal', 'telegram-groups'),
        'section' => 'telegram_home',
        'type' => 'textarea',
        'input_attrs' => array('rows' => 3),
    ));

    // Mostrar estadísticas
    $wp_customize->add_setting('show_stats', array(
        'default' => true,
        'sanitize_callback' => 'telegram_groups_sanitize_checkbox',
    ));

    $wp_customize->add_control('show_stats', array(
        'label' => __('Mostrar Estadísticas', 'telegram-groups'),
        'section' => 'telegram_home',
        'type' => 'checkbox',
        'description' => __('Muestra el contador de grupos y miembros.', 'telegram-groups'),
    ));

    // Grupos destacados a mostrar
    $wp_customize->add_setting('featured_groups_count', array(
        'default' => 8,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('featured_groups_count', array(
        'label' => __('Grupos Destacados', 'telegram-groups'),
        'section' => 'telegram_home',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 4,
            'max' => 20,
            'step' => 4,
        ),
        'description' => __('Número de grupos destacados a mostrar.', 'telegram-groups'),
    ));

    /* ========================================
       SECCIÓN: CONFIGURACIÓN AVANZADA
    ======================================== */
    
    $wp_customize->add_section('telegram_advanced', array(
        'title' => __('Configuración Avanzada', 'telegram-groups'),
        'priority' => 80,
        'description' => __('Opciones avanzadas del tema.', 'telegram-groups'),
    ));

    // Google Analytics
    $wp_customize->add_setting('google_analytics', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('google_analytics', array(
        'label' => __('Google Analytics ID', 'telegram-groups'),
        'section' => 'telegram_advanced',
        'type' => 'text',
        'description' => __('Ejemplo: G-XXXXXXXXXX', 'telegram-groups'),
    ));

    // *** CSS PERSONALIZADO - ARREGLADO ***
    $wp_customize->add_setting('custom_css', array(
        'default' => '',
        'sanitize_callback' => 'telegram_groups_sanitize_css',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('custom_css', array(
        'label' => __('CSS Personalizado', 'telegram-groups'),
        'section' => 'telegram_advanced',
        'type' => 'textarea',
        'description' => __('Añade tu CSS personalizado aquí. Los cambios se verán inmediatamente.', 'telegram-groups'),
        'input_attrs' => array(
            'rows' => 15,
            'class' => 'code',
            'style' => 'font-family: monospace; font-size: 12px;',
            'placeholder' => '/* Ejemplo de CSS personalizado:

.mi-boton-personalizado {
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
    color: white;
    padding: 15px 30px;
    border: none;
    border-radius: 25px;
    font-weight: bold;
    cursor: pointer;
}

.mi-boton-personalizado:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.grupo-card {
    border: 2px solid #0088cc;
    box-shadow: 0 4px 20px rgba(0,136,204,0.1);
}

*/',
        ),
    ));
}
add_action('customize_register', 'telegram_groups_customize_register');

/**
 * FUNCIONES DE SANITIZACIÓN
 */
function telegram_groups_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

function telegram_groups_sanitize_css($css) {
    // Limpiar CSS pero mantener la funcionalidad
    $css = wp_strip_all_tags($css);
    
    // Eliminar posibles scripts maliciosos
    $css = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $css);
    $css = preg_replace('/javascript:/i', '', $css);
    $css = preg_replace('/vbscript:/i', '', $css);
    $css = preg_replace('/onload/i', '', $css);
    $css = preg_replace('/onerror/i', '', $css);
    
    return $css;
}

/**
 * CALLBACK PARA CONDICIONES ACTIVAS
 */
function telegram_groups_show_add_btn_active($control) {
    return $control->manager->get_setting('show_add_group_btn')->value();
}

/**
 * CSS DINÁMICO QUE SE APLICA AL SITIO
 */
function telegram_groups_customizer_css() {
    $primary_color = get_theme_mod('primary_color', '#0088cc');
    $secondary_color = get_theme_mod('secondary_color', '#54a9eb');
    $bg_color = get_theme_mod('bg_color', '#f8f9fa');
    $header_bg = get_theme_mod('header_bg_color', '#ffffff');
    $custom_css = get_theme_mod('custom_css', '');
    ?>
    <style type="text/css" id="telegram-customizer-css">
        /* Variables CSS del Customizer */
        :root {
            --telegram-blue: <?php echo esc_attr($primary_color); ?>;
            --telegram-light-blue: <?php echo esc_attr($secondary_color); ?>;
            --telegram-bg: <?php echo esc_attr($bg_color); ?>;
            --telegram-white: <?php echo esc_attr($header_bg); ?>;
        }
        
        /* Aplicar colores */
        body {
            background-color: <?php echo esc_attr($bg_color); ?>;
        }
        
        .site-header,
        .header {
            background-color: <?php echo esc_attr($header_bg); ?>;
        }
        
        /* Header sticky */
        <?php if (get_theme_mod('header_sticky', true)): ?>
        .site-header {
            position: fixed !important;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .site-main,
        body {
            padding-top: 80px;
        }
        <?php endif; ?>
        
        /* Ocultar botón añadir grupo */
        <?php if (!get_theme_mod('show_add_group_btn', true)): ?>
        .btn-add-group,
        .add-group-button {
            display: none !important;
        }
        <?php endif; ?>
        
        /* Ocultar estadísticas */
        <?php if (!get_theme_mod('show_stats', true)): ?>
        .stats-section,
        .estadisticas {
            display: none !important;
        }
        <?php endif; ?>
        
        /* Colores principales aplicados */
        .btn-primary,
        .btn-telegram,
        .btn-telegram-large,
        .button-primary {
            background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?>, <?php echo esc_attr($secondary_color); ?>) !important;
            border-color: <?php echo esc_attr($primary_color); ?> !important;
        }
        
        .btn-primary:hover,
        .btn-telegram:hover,
        .btn-telegram-large:hover {
            background: linear-gradient(135deg, <?php echo esc_attr($secondary_color); ?>, <?php echo esc_attr($primary_color); ?>) !important;
        }
        
        a {
            color: <?php echo esc_attr($primary_color); ?>;
        }
        
        a:hover {
            color: <?php echo esc_attr($secondary_color); ?>;
        }
        
        .badge-destacado,
        .badge-primary {
            background: <?php echo esc_attr($primary_color); ?> !important;
        }
        
        .stat-number,
        .counter {
            color: <?php echo esc_attr($primary_color); ?> !important;
        }
        
        /* ================================
           CSS PERSONALIZADO DEL USUARIO
        ================================ */
        <?php if (!empty($custom_css)): ?>
        /* --- INICIO CSS PERSONALIZADO --- */
        <?php echo $custom_css; ?>
        /* --- FIN CSS PERSONALIZADO --- */
        <?php endif; ?>
    </style>
    <?php
}
add_action('wp_head', 'telegram_groups_customizer_css');

/**
 * JAVASCRIPT PARA LIVE PREVIEW
 */
function telegram_groups_customize_preview_js() {
    ?>
    <script type="text/javascript">
    (function($) {
        'use strict';
        
        // Live preview para CSS personalizado
        wp.customize('custom_css', function(value) {
            value.bind(function(to) {
                // Remover CSS personalizado anterior
                $('#custom-css-live-preview').remove();
                
                // Añadir nuevo CSS personalizado
                if (to && to.trim() !== '') {
                    $('head').append('<style id="custom-css-live-preview">' + to + '</style>');
                }
            });
        });
        
        // Live preview para colores
        wp.customize('primary_color', function(value) {
            value.bind(function(to) {
                updatePrimaryColor(to);
            });
        });
        
        wp.customize('bg_color', function(value) {
            value.bind(function(to) {
                $('body').css('background-color', to);
            });
        });
        
        wp.customize('header_bg_color', function(value) {
            value.bind(function(to) {
                $('.site-header, .header').css('background-color', to);
            });
        });
        
        // Función para actualizar color primario
        function updatePrimaryColor(color) {
            $('#primary-color-live-preview').remove();
            var css = '.btn-primary, .btn-telegram { background-color: ' + color + ' !important; } ';
            css += 'a { color: ' + color + ' !important; } ';
            css += '.stat-number { color: ' + color + ' !important; }';
            $('<style id="primary-color-live-preview">' + css + '</style>').appendTo('head');
        }
        
    })(jQuery);
    </script>
    <?php
}
add_action('customize_preview_init', 'telegram_groups_customize_preview_js');

/**
 * FUNCIONES HELPER PARA USAR EN TEMPLATES
 */
function tg_get_option($option, $default = '') {
    return get_theme_mod($option, $default);
}

function tg_show_add_group_button() {
    return get_theme_mod('show_add_group_btn', true);
}

function tg_add_group_button_text() {
    return esc_html(get_theme_mod('add_group_btn_text', 'Añadir Grupo'));
}