<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Grupos_Telegram
 */

if ( ! function_exists( 'grupos_telegram_posted_on' ) ) :
    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function grupos_telegram_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr( get_the_date( DATE_W3C ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( DATE_W3C ) ),
            esc_html( get_the_modified_date() )
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x( 'Publicado el %s', 'post date', 'grupos-telegram' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
        );

        echo '<span class="posted-on"><i class="fas fa-calendar-alt"></i> ' . $posted_on . '</span>';
    }
endif;

if ( ! function_exists( 'grupos_telegram_posted_by' ) ) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function grupos_telegram_posted_by() {
        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x( 'por %s', 'post author', 'grupos-telegram' ),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
        );

        echo '<span class="byline"><i class="fas fa-user"></i> ' . $byline . '</span>';
    }
endif;

if ( ! function_exists( 'grupos_telegram_entry_footer' ) ) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function grupos_telegram_entry_footer() {
        // Hide category and tag text for pages.
        if ( 'post' === get_post_type() ) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list( esc_html__( ', ', 'grupos-telegram' ) );
            if ( $categories_list ) {
                /* translators: 1: list of categories. */
                printf( '<span class="cat-links"><i class="fas fa-folder"></i> ' . esc_html__( 'Publicado en %1$s', 'grupos-telegram' ) . '</span>', $categories_list );
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'grupos-telegram' ) );
            if ( $tags_list ) {
                /* translators: 1: list of tags. */
                printf( '<span class="tags-links"><i class="fas fa-tags"></i> ' . esc_html__( 'Etiquetado %1$s', 'grupos-telegram' ) . '</span>', $tags_list );
            }
        }

        if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
            echo '<span class="comments-link"><i class="fas fa-comments"></i> ';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __( 'Deja un comentario<span class="screen-reader-text"> en %s</span>', 'grupos-telegram' ),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post( get_the_title() )
                )
            );
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __( 'Editar <span class="screen-reader-text">%s</span>', 'grupos-telegram' ),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post( get_the_title() )
            ),
            '<span class="edit-link"><i class="fas fa-edit"></i> ',
            '</span>'
        );
    }
endif;

if ( ! function_exists( 'grupos_telegram_post_navigation' ) ) :
    /**
     * Display navigation to next/previous post when applicable.
     */
    function grupos_telegram_post_navigation() {
        $prev_post = get_previous_post();
        $next_post = get_next_post();

        if ( $prev_post || $next_post ) : ?>
            <nav class="navigation post-navigation telegram-gradient" role="navigation" aria-label="<?php esc_attr_e( 'Navegación de entradas', 'grupos-telegram' ); ?>">
                <div class="nav-links">
                    <?php if ( $prev_post ) : ?>
                        <div class="nav-previous">
                            <a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" rel="prev">
                                <span class="nav-subtitle"><i class="fas fa-arrow-left"></i> <?php esc_html_e( 'Anterior', 'grupos-telegram' ); ?></span>
                                <span class="nav-title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ( $next_post ) : ?>
                        <div class="nav-next">
                            <a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" rel="next">
                                <span class="nav-subtitle"><?php esc_html_e( 'Siguiente', 'grupos-telegram' ); ?> <i class="fas fa-arrow-right"></i></span>
                                <span class="nav-title"><?php echo esc_html( get_the_title( $next_post ) ); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif;
    }
endif;

if ( ! function_exists( 'grupos_telegram_the_posts_navigation' ) ) :
    /**
     * Display navigation to next/previous set of posts when applicable.
     */
    function grupos_telegram_the_posts_navigation() {
        the_posts_navigation( array(
            'prev_text'          => '<i class="fas fa-chevron-left"></i> ' . __( 'Entradas más antiguas', 'grupos-telegram' ),
            'next_text'          => __( 'Entradas más recientes', 'grupos-telegram' ) . ' <i class="fas fa-chevron-right"></i>',
            'screen_reader_text' => __( 'Navegación de entradas', 'grupos-telegram' ),
        ) );
    }
endif;

if ( ! function_exists( 'grupos_telegram_breadcrumbs' ) ) :
    /**
     * Display breadcrumbs navigation.
     */
    function grupos_telegram_breadcrumbs() {
        if ( is_home() || is_front_page() ) {
            return;
        }

        $separator = ' <i class="fas fa-chevron-right"></i> ';
        $breadcrumbs = array();

        // Add home link
        $breadcrumbs[] = '<a href="' . home_url() . '"><i class="fas fa-home"></i> ' . __( 'Inicio', 'grupos-telegram' ) . '</a>';

        if ( is_category() || is_single() ) {
            $category = get_the_category();
            if ( ! empty( $category ) ) {
                $breadcrumbs[] = '<a href="' . get_category_link( $category[0]->cat_ID ) . '">' . $category[0]->cat_name . '</a>';
            }
        }

        if ( is_tax( 'categoria_grupo' ) ) {
            $term = get_queried_object();
            $breadcrumbs[] = '<a href="' . get_post_type_archive_link( 'grupo' ) . '">' . __( 'Grupos', 'grupos-telegram' ) . '</a>';
            $breadcrumbs[] = $term->name;
        }

        if ( is_single() ) {
            $breadcrumbs[] = get_the_title();
        }

        if ( is_page() ) {
            $breadcrumbs[] = get_the_title();
        }

        if ( is_search() ) {
            $breadcrumbs[] = sprintf( __( 'Resultados de búsqueda para: %s', 'grupos-telegram' ), get_search_query() );
        }

        if ( is_404() ) {
            $breadcrumbs[] = __( 'Página no encontrada', 'grupos-telegram' );
        }

        echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'grupos-telegram' ) . '">';
        echo implode( $separator, $breadcrumbs );
        echo '</nav>';
    }
endif;

if ( ! function_exists( 'grupos_telegram_grupo_meta' ) ) :
    /**
     * Display grupo meta information.
     */
    function grupos_telegram_grupo_meta( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $numero_miembros = get_field( 'numero_miembros', $post_id );
        $estado_grupo = get_field( 'estado_grupo', $post_id );
        $categoria_principal = wp_get_post_terms( $post_id, 'categoria_grupo' );

        echo '<div class="grupo-meta">';

        if ( ! empty( $categoria_principal ) ) {
            $categoria = $categoria_principal[0];
            echo '<span class="grupo-categoria"><i class="fas fa-tag"></i> ' . esc_html( $categoria->name ) . '</span>';
        }

        if ( $numero_miembros ) {
            echo '<span class="grupo-miembros"><i class="fas fa-users"></i> ' . number_format( $numero_miembros ) . ' miembros</span>';
        }

        if ( $estado_grupo ) {
            $estado_class = strtolower( $estado_grupo );
            echo '<span class="grupo-estado estado-' . esc_attr( $estado_class ) . '"><i class="fas fa-circle"></i> ' . esc_html( $estado_grupo ) . '</span>';
        }

        echo '</div>';
    }
endif;

if ( ! function_exists( 'grupos_telegram_grupo_stats' ) ) :
    /**
     * Display grupo statistics.
     */
    function grupos_telegram_grupo_stats( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $numero_miembros = get_field( 'numero_miembros', $post_id );
        $fecha_creacion = get_field( 'fecha_creacion', $post_id );
        $ultima_actividad = get_field( 'ultima_actividad', $post_id );

        ?>
        <div class="grupo-stats-detailed">
            <?php if ( $numero_miembros ) : ?>
                <div class="stat-item">
                    <i class="fas fa-users telegram-blue"></i>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo number_format( $numero_miembros ); ?></span>
                        <span class="stat-label">Miembros</span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( $fecha_creacion ) : ?>
                <div class="stat-item">
                    <i class="fas fa-calendar-plus telegram-blue"></i>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo date( 'd/m/Y', strtotime( $fecha_creacion ) ); ?></span>
                        <span class="stat-label">Creado</span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( $ultima_actividad ) : ?>
                <div class="stat-item">
                    <i class="fas fa-clock telegram-blue"></i>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo grupos_telegram_time_ago( $ultima_actividad ); ?></span>
                        <span class="stat-label">Última actividad</span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="stat-item">
                <i class="fas fa-eye telegram-blue"></i>
                <div class="stat-content">
                    <span class="stat-number"><?php echo number_format( get_post_meta( $post_id, '_grupos_views', true ) ?: 0 ); ?></span>
                    <span class="stat-label">Visualizaciones</span>
                </div>
            </div>
        </div>
        <?php
    }
endif;

if ( ! function_exists( 'grupos_telegram_categoria_badge' ) ) :
    /**
     * Display categoria badge with icon and color.
     */
    function grupos_telegram_categoria_badge( $categoria, $show_count = false ) {
        if ( ! $categoria ) {
            return;
        }

        $estilo_categoria = obtener_estilo_categoria( $categoria->slug );
        
        ?>
        <span class="categoria-badge" style="background-color: <?php echo esc_attr( $estilo_categoria['color'] ); ?>;">
            <i class="<?php echo esc_attr( $estilo_categoria['icon'] ); ?>"></i>
            <?php echo esc_html( $categoria->name ); ?>
            <?php if ( $show_count ) : ?>
                <span class="badge-count">(<?php echo $categoria->count; ?>)</span>
            <?php endif; ?>
        </span>
        <?php
    }
endif;

if ( ! function_exists( 'grupos_telegram_share_buttons' ) ) :
    /**
     * Display social share buttons.
     */
    function grupos_telegram_share_buttons( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $url = urlencode( get_permalink( $post_id ) );
        $title = urlencode( get_the_title( $post_id ) );
        
        ?>
        <div class="share-buttons">
            <span class="share-label"><i class="fas fa-share-alt"></i> Compartir:</span>
            
            <a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" 
               target="_blank" rel="noopener" class="share-button twitter">
                <i class="fab fa-twitter"></i>
            </a>
            
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" 
               target="_blank" rel="noopener" class="share-button facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            
            <a href="https://wa.me/?text=<?php echo $title; ?>%20<?php echo $url; ?>" 
               target="_blank" rel="noopener" class="share-button whatsapp">
                <i class="fab fa-whatsapp"></i>
            </a>
            
            <a href="https://t.me/share/url?url=<?php echo $url; ?>&text=<?php echo $title; ?>" 
               target="_blank" rel="noopener" class="share-button telegram">
                <i class="fab fa-telegram-plane"></i>
            </a>
            
            <button onclick="navigator.clipboard.writeText('<?php echo get_permalink( $post_id ); ?>')" 
                    class="share-button copy" title="Copiar enlace">
                <i class="fas fa-copy"></i>
            </button>
        </div>
        <?php
    }
endif;

if ( ! function_exists( 'grupos_telegram_telegram_button' ) ) :
    /**
     * Display Telegram join button.
     */
    function grupos_telegram_telegram_button( $post_id = null, $size = 'normal' ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $enlace_telegram = get_field( 'enlace_telegram', $post_id );
        $estado_grupo = get_field( 'estado_grupo', $post_id );

        if ( ! $enlace_telegram ) {
            return;
        }

        $button_class = 'btn-telegram ' . esc_attr( $size );
        if ( $estado_grupo !== 'Activo' ) {
            $button_class .= ' disabled';
        }

        ?>
        <a href="<?php echo esc_url( $enlace_telegram ); ?>" 
           class="<?php echo $button_class; ?>"
           target="_blank" rel="noopener"
           <?php echo ( $estado_grupo !== 'Activo' ) ? 'aria-disabled="true"' : ''; ?>>
            <i class="fab fa-telegram-plane"></i>
            <?php if ( $estado_grupo === 'Activo' ) : ?>
                Unirse al Grupo
            <?php else : ?>
                Grupo Inactivo
            <?php endif; ?>
        </a>
        <?php
    }
endif;

if ( ! function_exists( 'grupos_telegram_excerpt' ) ) :
    /**
     * Custom excerpt function.
     */
    function grupos_telegram_excerpt( $length = 20, $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $excerpt = get_field( 'descripcion_corta', $post_id );
        
        if ( ! $excerpt ) {
            $excerpt = get_the_excerpt( $post_id );
        }
        
        if ( ! $excerpt ) {
            $content = get_post_field( 'post_content', $post_id );
            $excerpt = wp_strip_all_tags( $content );
        }

        return wp_trim_words( $excerpt, $length, '...' );
    }
endif;

if ( ! function_exists( 'grupos_telegram_time_ago' ) ) :
    /**
     * Time ago function.
     */
    function grupos_telegram_time_ago( $datetime ) {
        $time = time() - strtotime( $datetime );

        if ( $time < 60 ) {
            return 'hace un momento';
        } elseif ( $time < 3600 ) {
            return sprintf( 'hace %d minutos', floor( $time / 60 ) );
        } elseif ( $time < 86400 ) {
            return sprintf( 'hace %d horas', floor( $time / 3600 ) );
        } elseif ( $time < 2592000 ) {
            return sprintf( 'hace %d días', floor( $time / 86400 ) );
        } else {
            return date( 'd/m/Y', strtotime( $datetime ) );
        }
    }
endif;

if ( ! function_exists( 'grupos_telegram_read_time' ) ) :
    /**
     * Calculate read time.
     */
    function grupos_telegram_read_time( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $content = get_post_field( 'post_content', $post_id );
        $word_count = str_word_count( strip_tags( $content ) );
        $reading_time = ceil( $word_count / 200 );

        return sprintf( '%d min de lectura', $reading_time );
    }
endif;

if ( ! function_exists( 'grupos_telegram_related_posts' ) ) :
    /**
     * Display related posts.
     */
    function grupos_telegram_related_posts( $post_id = null, $limit = 3 ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $categories = wp_get_post_terms( $post_id, 'categoria_grupo', array( 'fields' => 'ids' ) );

        $related_posts = new WP_Query( array(
            'post_type'      => 'grupo',
            'posts_per_page' => $limit,
            'post__not_in'   => array( $post_id ),
            'tax_query'      => array(
                array(
                    'taxonomy' => 'categoria_grupo',
                    'field'    => 'term_id',
                    'terms'    => $categories,
                ),
            ),
            'orderby'        => 'rand',
        ) );

        if ( $related_posts->have_posts() ) {
            echo '<div class="related-posts">';
            echo '<h3><i class="fas fa-link"></i> Grupos Relacionados</h3>';
            echo '<div class="related-posts-grid">';

            while ( $related_posts->have_posts() ) {
                $related_posts->the_post();
                get_template_part( 'template-parts/grupo-card', null, array( 'variant' => 'small' ) );
            }

            echo '</div>';
            echo '</div>';

            wp_reset_postdata();
        }
    }
endif;

if ( ! function_exists( 'grupos_telegram_popular_tags' ) ) :
    /**
     * Display popular tags.
     */
    function grupos_telegram_popular_tags( $limit = 10 ) {
        $tags = get_tags( array(
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => $limit,
            'hide_empty' => true,
        ) );

        if ( $tags ) {
            echo '<div class="popular-tags">';
            foreach ( $tags as $tag ) {
                echo '<a href="' . get_tag_link( $tag ) . '" class="tag-link">#' . $tag->name . '</a>';
            }
            echo '</div>';
        }
    }
endif;

if ( ! function_exists( 'grupos_telegram_archive_title' ) ) :
    /**
     * Custom archive title.
     */
    function grupos_telegram_archive_title() {
        if ( is_category() ) {
            echo single_cat_title( '', false );
        } elseif ( is_tag() ) {
            echo 'Etiqueta: ' . single_tag_title( '', false );
        } elseif ( is_author() ) {
            echo 'Autor: ' . get_the_author();
        } elseif ( is_tax( 'categoria_grupo' ) ) {
            $term = get_queried_object();
            echo 'Categoría: ' . $term->name;
        } elseif ( is_year() ) {
            echo get_the_date( 'Y' );
        } elseif ( is_month() ) {
            echo get_the_date( 'F Y' );
        } elseif ( is_day() ) {
            echo get_the_date();
        } elseif ( is_post_type_archive( 'grupo' ) ) {
            echo 'Todos los Grupos';
        } else {
            echo 'Archivo';
        }
    }
endif;