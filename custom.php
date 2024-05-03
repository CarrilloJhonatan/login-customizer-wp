<?php
/**
 * Plugin Name: Login Customizer WP
 * Plugin URI: https://clickssmaster.com/
 * Description: Plugin para personalizar el formulario de inicio de sesión de WordPress.
 * Version: 1.0.2
 * Author: DeveloperAnonimous
 * Author URI: https://clickssmaster.com/
 * License: GPL 2+
 * License URI: https://clickssmaster.com/
 */

// Función para agregar la página de configuración al menú de administración
function custom_login_page_settings() {
    add_menu_page(
        'Personalización de Login',          // Título de la página en el menú de administración
        'Login Personalizado',               // Título visible en el menú
        'manage_options',                    // Capacidad requerida para acceder a esta página
        'custom-login-settings',             // Slug único para la página
        'render_custom_login_settings_page'  // Función que renderiza la página de configuración
    );
}
add_action('admin_menu', 'custom_login_page_settings');

// Función para renderizar la página de configuración
function render_custom_login_settings_page() {
    ?>
    <div class="wrap">
        <h1>Personalización de Login</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom-login-settings');  // Agrega campos ocultos de seguridad para las opciones
            do_settings_sections('custom-login-settings');  // Muestra las secciones y campos de configuración
            submit_button('Guardar Cambios');  // Muestra el botón de guardar cambios
            ?>
        </form>
    </div>
    <?php
}

// Función para registrar las opciones de configuración
function custom_login_settings_init() {
    register_setting('custom-login-settings', 'custom_login_logo_url');  // Registra la opción para la URL del logo
    register_setting('custom-login-settings', 'custom_login_background_color');  // Registra la opción para el color de fondo
    register_setting('custom-login-settings', 'custom_primary_button_color');  // Registra la opción para el color del botón primario
}
add_action('admin_init', 'custom_login_settings_init');

// Función para agregar campos de configuración en la página
function add_custom_login_settings_fields() {
    add_settings_section(
        'custom-login-section',                         // ID de la sección
        'Configuración de Login Personalizado',         // Título de la sección
        'custom_login_section_callback',                // Callback para renderizar la sección
        'custom-login-settings'                         // Página a la que se añade la sección
    );

    add_settings_field(
        'custom-login-logo-url',                        // ID del campo
        'URL del Logo',                                 // Título del campo
        'custom_login_logo_url_callback',               // Callback para renderizar el campo
        'custom-login-settings',                        // Página a la que se añade el campo
        'custom-login-section'                          // Sección a la que se añade el campo
    );

    add_settings_field(
        'custom-login-background-color',                // ID del campo
        'Color de Fondo',                               // Título del campo
        'custom_login_background_color_callback',       // Callback para renderizar el campo
        'custom-login-settings',                        // Página a la que se añade el campo
        'custom-login-section'                          // Sección a la que se añade el campo
    );

    add_settings_field(
        'custom-primary-button-color',                  // ID del campo
        'Color del Botón Primario',                     // Título del campo
        'custom_primary_button_color_callback',         // Callback para renderizar el campo
        'custom-login-settings',                        // Página a la que se añade el campo
        'custom-login-section'                          // Sección a la que se añade el campo
    );
}
add_action('admin_init', 'add_custom_login_settings_fields');

// Callback para la sección de configuración
function custom_login_section_callback() {
    echo 'Ingrese los valores personalizados para la página de login:';
}

// Callback para el campo de URL del logo
function custom_login_logo_url_callback() {
    $logo_url = get_option('custom_login_logo_url');
    echo '<input type="text" name="custom_login_logo_url" value="' . esc_attr($logo_url) . '" />';
}

// Callback para el campo de color de fondo
function custom_login_background_color_callback() {
    $bg_color = get_option('custom_login_background_color');
    echo '<input type="text" name="custom_login_background_color" value="' . esc_attr($bg_color) . '" />';
}

// Callback para el campo de color del botón primario
function custom_primary_button_color_callback() {
    $button_color = get_option('custom_primary_button_color');
    echo '<input type="text" name="custom_primary_button_color" value="' . esc_attr($button_color) . '" />';
}

// Función para personalizar el login con las opciones configuradas
function master_login_logo() {
    $logo_url = get_option('custom_login_logo_url');
    $bg_color = get_option('custom_login_background_color');
    $button_color = get_option('custom_primary_button_color');

    ?>
    <style type="text/css">
        #login h1 a,
        .login h1 a {
            background-image: url('<?php echo esc_url($logo_url); ?>');  // URL del logo
            margin-bottom: 0;
            background-size: 90%;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }
        /* TAMANO DE IMAGEN RECOMENDADO 400 X 100 PX */
        .login form {
            border-radius: 20px;
        }

        .wp-core-ui .button-primary {
            background: <?php echo esc_attr($button_color); ?> !important;  // Color del botón primario
            border-color: <?php echo esc_attr($button_color); ?> !important;
            color: #ffffff !important;
            text-decoration: none;
            box-shadow: 0 1px 0 <?php echo esc_attr($button_color); ?> !important;
            text-shadow: 0 -1px 1px <?php echo esc_attr($button_color); ?>, 1px 0 1px <?php echo esc_attr($button_color); ?>, 0 1px 1px <?php echo esc_attr($button_color); ?>, -1px 0 1px <?php echo esc_attr($button_color); ?> !important;
        }

        .wp-core-ui .button-secondary {
			color: <?php echo esc_attr($button_color); ?> !important;
			border-color: #00000000 !important;
		}

        .button {
			color: <?php echo esc_attr($button_color); ?> !important;
			border-color: <?php echo esc_attr($button_color); ?> !important;
			background: #ffffff !important;
			vertical-align: top;
		}

        body.login {
            background-color: <?php echo esc_attr($bg_color); ?>;  // Color de fondo
        }

        .login #backtoblog a,
		.login #nav a {
			color: <?php echo esc_attr($button_color); ?> !important;
		}

        a {
    color: <?php echo esc_attr($button_color); ?> !important;
}
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'master_login_logo');