/**
 * Customizer Live Preview
 */
(function ($) {
    'use strict';

    // Título del sitio
    wp.customize('blogname', function (value) {
        value.bind(function (to) {
            $('.site-title a').text(to);
        });
    });

    // Descripción del sitio
    wp.customize('blogdescription', function (value) {
        value.bind(function (to) {
            $('.site-description').text(to);
        });
    });

    // Color primario
    wp.customize('telegram_primary_color', function (value) {
        value.bind(function (to) {
            $(':root').css('--telegram-blue', to);
        });
    });

    // Color secundario
    wp.customize('telegram_secondary_color', function (value) {
        value.bind(function (to) {
            $(':root').css('--telegram-light-blue', to);
        });
    });

    // Color de fondo
    wp.customize('telegram_bg_color', function (value) {
        value.bind(function (to) {
            $('body').css('background-color', to);
            $(':root').css('--telegram-bg', to);
        });
    });

    // Color de texto
    wp.customize('telegram_text_color', function (value) {
        value.bind(function (to) {
            $('body').css('color', to);
            $(':root').css('--telegram-dark', to);
        });
    });

    // Altura del header
    wp.customize('header_height', function (value) {
        value.bind(function (to) {
            $('.site-header').css('min-height', to + 'px');
            $('.header-content').css('min-height', to + 'px');
            $(':root').css('--header-height', to + 'px');
        });
    });

    // Título del hero
    wp.customize('hero_title', function (value) {
        value.bind(function (to) {
            $('.hero-title').text(to);
        });
    });

    // Descripción del hero
    wp.customize('hero_description', function (value) {
        value.bind(function (to) {
            $('.hero-description').text(to);
        });
    });

})(jQuery);