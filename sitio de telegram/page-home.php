<?php
/**
 * Template Name: Página de Inicio
 * 
 * Página principal del directorio de grupos de Telegram
 * 
 * @package Grupos_Telegram
 * @since 1.0.0
 */

get_header();

// Obtener datos para estadísticas
$total_grupos = wp_count_posts('grupo')->publish;
$total_categorias = wp_count_terms('categoria_grupo');
$total_miembros = 2300000; // Número ejemplo o calculado

// Obtener categorías principales (2 categorías)
$categorias_principales = get_terms(array(
    'taxonomy' => 'categoria_grupo',
    'number' => 2,
    'orderby' => 'count',
    'order' => 'DESC',
    'hide_empty' => true
));

// Obtener grupos destacados (2 grupos)
$grupos_destacados = new WP_Query(array(
    'post_type' => 'grupo',
    'posts_per_page' => 2,
    'orderby' => 'meta_value_num',
    'meta_key' => 'numero_miembros',
    'order' => 'DESC',
    'meta_query' => array(
        array(
            'key' => 'estado_grupo',
            'value' => 'Activo',
            'compare' => '='
        )
    )
));
?>

<!-- Hero Section -->
<section id="inicio" class="hero-section telegram-gradient py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold text-white mb-6">
            La Mayor Comunidad de <span class="text-blue-200">Grupos de Telegram</span>
        </h1>
        <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
            Bienvenido a la mayor comunidad de <strong>Grupos de Telegram</strong> 
            organizados por aficiones, temáticas, categorías e intereses. 
            Únete al grupo de chat que más te guste. Y si no existe... ¡créalo!
        </p>
        
        <!-- Buscador Principal -->
        <div class="max-w-2xl mx-auto mb-8">
            <?php get_template_part('template-parts/hero-section'); ?>
        </div>
        
        <!-- Estadísticas -->
        <div class="mt-8 flex justify-center items-center space-x-8 text-white">
            <div class="text-center">
                <div class="text-3xl font-bold"><?php echo number_format($total_grupos); ?></div>
                <div class="text-sm">Grupos Activos</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold"><?php echo number_format($total_miembros / 1000000, 1) . 'M+'; ?></div>
                <div class="text-sm">Miembros Totales</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold"><?php echo $total_categorias; ?></div>
                <div class="text-sm">Categorías</div>
            </div>
        </div>
    </div>
</section>

<!-- Sección de Categorías -->
<section id="categorias" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center text-blue-600 mb-12">
            Explora por Categorías
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <?php if ($categorias_principales && !is_wp_error($categorias_principales)) : ?>
                <?php foreach ($categorias_principales as $categoria) : ?>
                    <?php 
                    get_template_part('template-parts/categoria-card', null, array(
                        'categoria' => $categoria,
                        'size' => 'large'
                    )); 
                    ?>
                <?php endforeach; ?>
            <?php else : ?>
                <!-- Categorías por defecto si no hay categorías creadas -->
                <div class="categoria-card bg-purple-50 p-8 rounded-xl hover:shadow-lg transition-all cursor-pointer">
                    <div class="text-center">
                        <i class="fas fa-gamepad text-6xl text-purple-600 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Gaming</h3>
                        <p class="text-gray-600">1,234 grupos</p>
                    </div>
                </div>
                
                <div class="categoria-card bg-blue-50 p-8 rounded-xl hover:shadow-lg transition-all cursor-pointer">
                    <div class="text-center">
                        <i class="fas fa-laptop-code text-6xl text-blue-600 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Tecnología</h3>
                        <p class="text-gray-600">987 grupos</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Sección de Grupos Destacados -->
<section id="grupos" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center text-blue-600 mb-12">
            Grupos Destacados
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-6xl mx-auto">
            <?php if ($grupos_destacados->have_posts()) : ?>
                <?php while ($grupos_destacados->have_posts()) : $grupos_destacados->the_post(); ?>
                    <?php 
                    get_template_part('template-parts/grupo-card', null, array(
                        'variant' => 'destacado',
                        'show_description' => true
                    )); 
                    ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <!-- Grupos por defecto si no hay grupos creados -->
                <div class="grupo-card bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-gamepad text-white text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-bold text-gray-800">Gamers España</h3>
                            <p class="text-sm text-gray-600">Gaming</p>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">
                        Comunidad de gamers españoles. Charlamos sobre videojuegos, compartimos gameplay y organizamos partidas online.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-blue-600 font-semibold">
                            <i class="fas fa-users mr-1"></i>47,823 miembros
                        </span>
                        <span class="text-green-500 text-sm">
                            <i class="fas fa-circle text-xs mr-1"></i>Activo
                        </span>
                    </div>
                    <button class="w-full telegram-gradient text-white py-3 rounded-lg font-semibold hover:opacity-90 transition-opacity">
                        <i class="fab fa-telegram-plane mr-2"></i>Unirse al Grupo
                    </button>
                </div>
                
                <div class="grupo-card bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-laptop-code text-white text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-bold text-gray-800">Programadores JS</h3>
                            <p class="text-sm text-gray-600">Tecnología</p>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">
                        Grupo para desarrolladores JavaScript. Resolvemos dudas, compartimos código y discutimos las últimas tendencias.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-blue-600 font-semibold">
                            <i class="fas fa-users mr-1"></i>32,156 miembros
                        </span>
                        <span class="text-green-500 text-sm">
                            <i class="fas fa-circle text-xs mr-1"></i>Activo
                        </span>
                    </div>
                    <button class="w-full telegram-gradient text-white py-3 rounded-lg font-semibold hover:opacity-90 transition-opacity">
                        <i class="fab fa-telegram-plane mr-2"></i>Unirse al Grupo
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="<?php echo get_post_type_archive_link('grupo'); ?>" 
               class="telegram-gradient text-white px-8 py-4 rounded-full font-semibold text-lg hover:opacity-90 transition-opacity inline-block">
                <i class="fas fa-arrow-right mr-2"></i>Ver Todos los Grupos
            </a>
        </div>
    </div>
</section>

<!-- Sección de Promoción -->
<section id="promocionar" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-blue-600 mb-6">¿Tienes un Grupo de Telegram?</h2>
            <p class="text-xl text-gray-700 mb-8">
                Promociona tu grupo de Telegram y consigue <strong>cientos de miembros gratis</strong>. 
                Únete a nuestra comunidad y haz crecer tu audiencia.
            </p>
            
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 telegram-gradient rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-rocket text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2">Promoción Gratuita</h3>
                        <p class="text-gray-600">Publica tu grupo completamente gratis en nuestra plataforma</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 telegram-gradient rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2">Miles de Usuarios</h3>
                        <p class="text-gray-600">Accede a nuestra base de usuarios activos buscando grupos</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 telegram-gradient rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2">Crecimiento Rápido</h3>
                        <p class="text-gray-600">Ve crecer tu grupo con miembros reales e interesados</p>
                    </div>
                </div>
            </div>
            
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <a href="/añadir-grupo" 
                   class="w-full sm:w-auto telegram-gradient text-white px-8 py-4 rounded-full font-semibold text-lg hover:opacity-90 transition-opacity inline-block">
                    <i class="fas fa-plus mr-2"></i>Promocionar Mi Grupo
                </a>
                <a href="/informacion" 
                   class="w-full sm:w-auto border-2 border-blue-600 text-blue-600 px-8 py-4 rounded-full font-semibold text-lg hover:bg-blue-50 transition-colors inline-block">
                    <i class="fas fa-info-circle mr-2"></i>Más Información
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Sección Informativa -->
<section class="py-16 bg-blue-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-blue-600 mb-8 text-center">¿Qué son los Grupos de Telegram?</h2>
            <div class="bg-white rounded-2xl p-8 shadow-lg">
                <p class="text-lg text-gray-700 mb-6">
                    Los <strong>grupos de Telegram</strong> son salas de chat en la aplicación Telegram. Sirven para mantener conversaciones 
                    entre todos los miembros del grupo bajo la moderación del Administrador del grupo.
                </p>
                <p class="text-lg text-gray-700 mb-6">
                    Puedes encontrar Grupos de Telegram para hacer amigos, para compartir aficiones o para ligar y encontrar el amor de tu vida. 
                    También puedes localizar <strong>Grupos de Telegram en tu ciudad</strong> y así conocer gente cercana a ti.
                </p>
                <p class="text-lg text-gray-700">
                    Aquí encontrarás <strong>miles de grupos de Telegram para unirte</strong>. 
                    También puedes promocionar tu grupo de Telegram publicando tu enlace de invitación.
                </p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>