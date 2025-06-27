<?php
/**
 * Configuración de Campos Personalizados ACF PRO
 * Tema: Grupos de Telegram
 * 
 * @package GruposTelegram
 * @version 1.0.0
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar campos personalizados si ACF está activo
 */
add_action('acf/init', 'grupos_telegram_register_acf_fields');

function grupos_telegram_register_acf_fields() {
    
    // Verificar que ACF esté activo
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    /**
     * GRUPO DE CAMPOS: Información del Grupo
     * Para el post type 'grupo'
     */
    acf_add_local_field_group(array(
        'key' => 'group_informacion_grupo',
        'title' => 'Información del Grupo',
        'fields' => array(
            // Nombre del Grupo
            array(
                'key' => 'field_nombre_grupo',
                'label' => 'Nombre del Grupo',
                'name' => 'nombre_grupo',
                'type' => 'text',
                'instructions' => 'Nombre del grupo de Telegram',
                'required' => 1,
                'placeholder' => 'Ej: Gamers España',
                'maxlength' => 100,
            ),
            // Descripción Corta
            array(
                'key' => 'field_descripcion_corta',
                'label' => 'Descripción Corta',
                'name' => 'descripcion_corta',
                'type' => 'textarea',
                'instructions' => 'Descripción breve para mostrar en las tarjetas',
                'required' => 1,
                'rows' => 3,
                'maxlength' => 200,
                'placeholder' => 'Descripción breve del grupo...',
            ),
            // Descripción Completa
            array(
                'key' => 'field_descripcion_completa',
                'label' => 'Descripción Completa',
                'name' => 'descripcion_completa',
                'type' => 'wysiwyg',
                'instructions' => 'Descripción detallada para la página individual del grupo',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
            ),
            // Imagen del Grupo
            array(
                'key' => 'field_imagen_grupo',
                'label' => 'Imagen del Grupo',
                'name' => 'imagen_grupo',
                'type' => 'image',
                'instructions' => 'Imagen principal del grupo (recomendado: 400x400px)',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'min_width' => 200,
                'min_height' => 200,
                'max_size' => '2MB',
            ),
            // Enlace de Telegram
            array(
                'key' => 'field_enlace_telegram',
                'label' => 'Enlace de Telegram',
                'name' => 'enlace_telegram',
                'type' => 'url',
                'instructions' => 'URL de invitación al grupo de Telegram',
                'required' => 1,
                'placeholder' => 'https://t.me/nombregrupo',
            ),
            // Número de Miembros
            array(
                'key' => 'field_numero_miembros',
                'label' => 'Número de Miembros',
                'name' => 'numero_miembros',
                'type' => 'number',
                'instructions' => 'Cantidad aproximada de miembros del grupo',
                'required' => 1,
                'min' => 1,
                'max' => 200000,
                'step' => 1,
                'placeholder' => '1000',
            ),
            // Estado del Grupo
            array(
                'key' => 'field_estado_grupo',
                'label' => 'Estado del Grupo',
                'name' => 'estado_grupo',
                'type' => 'select',
                'instructions' => 'Estado actual del grupo',
                'required' => 1,
                'choices' => array(
                    'Activo' => 'Activo',
                    'Inactivo' => 'Inactivo',
                    'Moderado' => 'Moderado',
                ),
                'default_value' => 'Activo',
                'allow_null' => 0,
                'return_format' => 'value',
            ),
            // Etiquetas del Grupo
            array(
                'key' => 'field_etiquetas_grupo',
                'label' => 'Etiquetas del Grupo',
                'name' => 'etiquetas_grupo',
                'type' => 'text',
                'instructions' => 'Etiquetas separadas por comas (ej: #gaming, #español, #online)',
                'required' => 0,
                'placeholder' => '#gaming, #español, #comunidad',
            ),
            // Grupos Relacionados
            array(
                'key' => 'field_grupos_relacionados',
                'label' => 'Grupos Relacionados',
                'name' => 'grupos_relacionados',
                'type' => 'relationship',
                'instructions' => 'Selecciona grupos relacionados para mostrar como sugerencias',
                'required' => 0,
                'post_type' => array('grupo'),
                'taxonomy' => array(),
                'filters' => array('search', 'taxonomy'),
                'elements' => array('featured_image'),
                'min' => 0,
                'max' => 6,
                'return_format' => 'object',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'grupo',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array(),
    ));

    /**
     * GRUPO DE CAMPOS: Configuración de Display
     * Para personalizar la visualización del grupo
     */
    acf_add_local_field_group(array(
        'key' => 'group_configuracion_display',
        'title' => 'Configuración de Display',
        'fields' => array(
            // Destacar en Home
            array(
                'key' => 'field_destacar_home',
                'label' => 'Destacar en Página Principal',
                'name' => 'destacar_home',
                'type' => 'true_false',
                'instructions' => 'Mostrar este grupo en la sección destacados de la página principal',
                'required' => 0,
                'message' => 'Sí, destacar este grupo',
                'default_value' => 0,
            ),
            // Color de Icono
            array(
                'key' => 'field_color_icono',
                'label' => 'Color del Icono',
                'name' => 'color_icono',
                'type' => 'color_picker',
                'instructions' => 'Color personalizado para el icono del grupo',
                'required' => 0,
                'default_value' => '#0088cc',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
            // Icono FontAwesome
            array(
                'key' => 'field_icono_fontawesome',
                'label' => 'Icono FontAwesome',
                'name' => 'icono_fontawesome',
                'type' => 'text',
                'instructions' => 'Clase del icono FontAwesome (ej: fas fa-gamepad)',
                'required' => 0,
                'placeholder' => 'fas fa-users',
            ),
            // Orden de Visualización
            array(
                'key' => 'field_orden_display',
                'label' => 'Orden de Visualización',
                'name' => 'orden_display',
                'type' => 'number',
                'instructions' => 'Orden de aparición (menor número aparece primero)',
                'required' => 0,
                'default_value' => 0,
                'min' => 0,
                'max' => 999,
                'step' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'grupo',
                ),
            ),
        ),
        'menu_order' => 1,
        'position' => 'side',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ));

    /**
     * GRUPO DE CAMPOS: Estadísticas del Grupo
     * Para métricas y datos adicionales
     */
    acf_add_local_field_group(array(
        'key' => 'group_estadisticas_grupo',
        'title' => 'Estadísticas del Grupo',
        'fields' => array(
            // Fecha de Creación
            array(
                'key' => 'field_fecha_creacion',
                'label' => 'Fecha de Creación del Grupo',
                'name' => 'fecha_creacion',
                'type' => 'date_picker',
                'instructions' => 'Fecha en que se creó el grupo de Telegram',
                'required' => 0,
                'display_format' => 'd/m/Y',
                'return_format' => 'd/m/Y',
                'first_day' => 1,
            ),
            // Última Actividad
            array(
                'key' => 'field_ultima_actividad',
                'label' => 'Última Actividad',
                'name' => 'ultima_actividad',
                'type' => 'date_time_picker',
                'instructions' => 'Fecha y hora de la última actividad registrada',
                'required' => 0,
                'display_format' => 'd/m/Y g:i a',
                'return_format' => 'd/m/Y g:i a',
                'first_day' => 1,
            ),
            // Promedio de Mensajes Diarios
            array(
                'key' => 'field_promedio_mensajes',
                'label' => 'Promedio de Mensajes Diarios',
                'name' => 'promedio_mensajes',
                'type' => 'number',
                'instructions' => 'Número promedio de mensajes por día',
                'required' => 0,
                'min' => 0,
                'max' => 10000,
                'step' => 1,
                'placeholder' => '50',
            ),
            // Nivel de Actividad
            array(
                'key' => 'field_nivel_actividad',
                'label' => 'Nivel de Actividad',
                'name' => 'nivel_actividad',
                'type' => 'select',
                'instructions' => 'Nivel general de actividad del grupo',
                'required' => 0,
                'choices' => array(
                    'Muy Alto' => 'Muy Alto',
                    'Alto' => 'Alto',
                    'Medio' => 'Medio',
                    'Bajo' => 'Bajo',
                    'Muy Bajo' => 'Muy Bajo',
                ),
                'default_value' => 'Medio',
                'allow_null' => 1,
                'return_format' => 'value',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'grupo',
                ),
            ),
        ),
        'menu_order' => 2,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array(),
    ));

    /**
     * GRUPO DE CAMPOS: Configuración de Categorías
     * Para taxonomía 'categoria_grupo'
     */
    acf_add_local_field_group(array(
        'key' => 'group_configuracion_categoria',
        'title' => 'Configuración de Categoría',
        'fields' => array(
            // Icono de la Categoría
            array(
                'key' => 'field_icono_categoria',
                'label' => 'Icono de la Categoría',
                'name' => 'icono_categoria',
                'type' => 'text',
                'instructions' => 'Clase del icono FontAwesome para esta categoría',
                'required' => 0,
                'placeholder' => 'fas fa-gamepad',
            ),
            // Color de la Categoría
            array(
                'key' => 'field_color_categoria',
                'label' => 'Color de la Categoría',
                'name' => 'color_categoria',
                'type' => 'color_picker',
                'instructions' => 'Color representativo de esta categoría',
                'required' => 0,
                'default_value' => '#0088cc',
                'enable_opacity' => 0,
                'return_format' => 'string',
            ),
            // Descripción Extendida
            array(
                'key' => 'field_descripcion_categoria',
                'label' => 'Descripción Extendida',
                'name' => 'descripcion_categoria',
                'type' => 'wysiwyg',
                'instructions' => 'Descripción detallada de la categoría',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ),
            // Imagen de Fondo
            array(
                'key' => 'field_imagen_fondo_categoria',
                'label' => 'Imagen de Fondo',
                'name' => 'imagen_fondo_categoria',
                'type' => 'image',
                'instructions' => 'Imagen de fondo para la página de la categoría',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'min_width' => 800,
                'min_height' => 400,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'categoria_grupo',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ));

    /**
     * PÁGINA DE OPCIONES: Configuración General del Tema
     */
    acf_add_options_page(array(
        'page_title' => 'Configuración del Tema',
        'menu_title' => 'Opciones del Tema',
        'menu_slug' => 'tema-grupos-telegram',
        'capability' => 'edit_posts',
        'icon_url' => 'dashicons-admin-generic',
        'position' => 30,
    ));

    /**
     * GRUPO DE CAMPOS: Opciones Generales
     * Para la página de opciones
     */
    acf_add_local_field_group(array(
        'key' => 'group_opciones_generales',
        'title' => 'Opciones Generales',
        'fields' => array(
            // Texto del Hero
            array(
                'key' => 'field_texto_hero',
                'label' => 'Texto Principal del Hero',
                'name' => 'texto_hero',
                'type' => 'text',
                'instructions' => 'Texto principal que aparece en la sección hero',
                'required' => 0,
                'default_value' => 'La Mayor Comunidad de Grupos de Telegram',
            ),
            // Descripción del Hero
            array(
                'key' => 'field_descripcion_hero',
                'label' => 'Descripción del Hero',
                'name' => 'descripcion_hero',
                'type' => 'textarea',
                'instructions' => 'Descripción que aparece debajo del título principal',
                'required' => 0,
                'rows' => 4,
                'default_value' => 'Bienvenido a la mayor comunidad de Grupos de Telegram organizados por aficiones, temáticas, categorías e intereses.',
            ),
            // Email de Contacto
            array(
                'key' => 'field_email_contacto',
                'label' => 'Email de Contacto',
                'name' => 'email_contacto',
                'type' => 'email',
                'instructions' => 'Email principal de contacto del sitio',
                'required' => 0,
                'placeholder' => 'contacto@grupostelegram.com',
            ),
            // Redes Sociales
            array(
                'key' => 'field_redes_sociales',
                'label' => 'Redes Sociales',
                'name' => 'redes_sociales',
                'type' => 'repeater',
                'instructions' => 'Enlaces a redes sociales',
                'required' => 0,
                'min' => 0,
                'max' => 10,
                'layout' => 'table',
                'button_label' => 'Añadir Red Social',
                'sub_fields' => array(
                    array(
                        'key' => 'field_red_social_nombre',
                        'label' => 'Nombre',
                        'name' => 'nombre',
                        'type' => 'text',
                        'placeholder' => 'Facebook',
                        'width' => 25,
                    ),
                    array(
                        'key' => 'field_red_social_url',
                        'label' => 'URL',
                        'name' => 'url',
                        'type' => 'url',
                        'placeholder' => 'https://facebook.com/...',
                        'width' => 50,
                    ),
                    array(
                        'key' => 'field_red_social_icono',
                        'label' => 'Icono',
                        'name' => 'icono',
                        'type' => 'text',
                        'placeholder' => 'fab fa-facebook',
                        'width' => 25,
                    ),
                ),
            ),
            // Google Analytics
            array(
                'key' => 'field_google_analytics',
                'label' => 'Google Analytics ID',
                'name' => 'google_analytics',
                'type' => 'text',
                'instructions' => 'ID de Google Analytics (ej: GA_MEASUREMENT_ID)',
                'required' => 0,
                'placeholder' => 'G-XXXXXXXXXX',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'tema-grupos-telegram',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ));

}

/**
 * Funciones auxiliares para ACF
 */

/**
 * Obtener estilo de categoría por slug
 */
function obtener_estilo_categoria_acf($categoria_slug) {
    $categoria = get_term_by('slug', $categoria_slug, 'categoria_grupo');
    
    if ($categoria) {
        $icono = get_field('icono_categoria', $categoria);
        $color = get_field('color_categoria', $categoria);
        
        return array(
            'icon' => $icono ? $icono : 'fas fa-users',
            'color' => $color ? $color : '#0088cc'
        );
    }
    
    return array(
        'icon' => 'fas fa-users',
        'color' => '#0088cc'
    );
}

/**
 * Validar enlace de Telegram
 */
add_filter('acf/validate_value/name=enlace_telegram', 'validar_enlace_telegram', 10, 4);

function validar_enlace_telegram($valid, $value, $field, $input) {
    if ($valid !== true) {
        return $valid;
    }
    
    if ($value && strpos($value, 't.me') === false && strpos($value, 'telegram.me') === false) {
        $valid = 'El enlace debe ser de Telegram (t.me o telegram.me)';
    }
    
    return $valid;
}

/**
 * Sanitizar etiquetas del grupo
 */
add_filter('acf/update_value/name=etiquetas_grupo', 'sanitizar_etiquetas_grupo', 10, 3);

function sanitizar_etiquetas_grupo($value, $post_id, $field) {
    if ($value) {
        // Añadir # al inicio si no lo tiene
        $etiquetas = explode(',', $value);
        $etiquetas_limpias = array();
        
        foreach ($etiquetas as $etiqueta) {
            $etiqueta = trim($etiqueta);
            if ($etiqueta && substr($etiqueta, 0, 1) !== '#') {
                $etiqueta = '#' . $etiqueta;
            }
            if ($etiqueta) {
                $etiquetas_limpias[] = $etiqueta;
            }
        }
        
        $value = implode(', ', $etiquetas_limpias);
    }
    
    return $value;
}

/**
 * Actualizar contador de grupos en categorías
 */
add_action('save_post_grupo', 'actualizar_contador_categoria');
add_action('before_delete_post', 'actualizar_contador_categoria_delete');

function actualizar_contador_categoria($post_id) {
    if (get_post_type($post_id) === 'grupo') {
        wp_cache_delete('grupos_por_categoria', 'grupos_telegram');
    }
}

function actualizar_contador_categoria_delete($post_id) {
    if (get_post_type($post_id) === 'grupo') {
        wp_cache_delete('grupos_por_categoria', 'grupos_telegram');
    }
}

?>