<?php

namespace LoginCustomizerWP\Modules\Admin;

use LoginCustomizerWP\Settings\SettingsRepository;
use LoginCustomizerWP\Utils\Logger;
use LoginCustomizerWP\Utils\PluginHooks;

/**
 * Settings page controller.
 */
final class SettingsController {
	/**
	 * @var SettingsRepository
	 */
	private $settings;

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var PluginHooks
	 */
	private $hooks;

	/**
	 * @var string|null
	 */
	private $page_hook;

	/**
	 * @param SettingsRepository $settings Settings.
	 * @param Logger             $logger Logger.
	 * @param PluginHooks        $hooks Hooks.
	 */
	public function __construct( SettingsRepository $settings, Logger $logger, PluginHooks $hooks ) {
		$this->settings = $settings;
		$this->logger   = $logger;
		$this->hooks    = $hooks;
	}

	/**
	 * @return void
	 */
	public function register_menu() {
		$this->page_hook = add_menu_page(
			__( 'Login Customizer', LCW_TEXTDOMAIN ),
			__( 'Login Customizer', LCW_TEXTDOMAIN ),
			'manage_options',
			'lcw-settings',
			array( $this, 'render_page' ),
			'dashicons-admin-customizer'
		);

		add_submenu_page(
			'lcw-settings',
			__( 'Login Customizer', LCW_TEXTDOMAIN ),
			__( 'Configuración', LCW_TEXTDOMAIN ),
			'manage_options',
			'custom-login-settings',
			array( $this, 'render_page' )
		);
	}

	/**
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'lcw_settings_group',
			SettingsRepository::OPTION_NAME,
			array(
				'sanitize_callback' => array( $this->settings, 'sanitize' ),
				'type'              => 'array',
				'default'           => $this->settings->defaults(),
			)
		);
	}

	/**
	 * @param string $hook_suffix Hook suffix.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( ! is_string( $this->page_hook ) || $hook_suffix !== $this->page_hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style(
			'lcw-admin',
			LCW_URL . 'assets/admin/admin.css',
			array(),
			LCW_VERSION
		);

		wp_enqueue_script(
			'lcw-admin',
			LCW_URL . 'assets/admin/admin.js',
			array( 'jquery', 'wp-color-picker', 'media-editor', 'media-views', 'wp-util' ),
			LCW_VERSION,
			true
		);

		$palettes = $this->hooks->apply_filters(
			'lcw_color_palettes',
			array(
				'default'  => array(
					'label'  => __( 'Predeterminada', LCW_TEXTDOMAIN ),
					'colors' => array(
						'background_color'      => '#f1f1f1',
						'form_background_color' => '#ffffff',
						'primary_button_color'  => '#2271b1',
						'label_color'           => '#1d2327',
						'nav_link_color'        => '#1d2327',
						'input_text_color'      => '#1d2327',
					),
				),
				'dark'     => array(
					'label'  => __( 'Oscura', LCW_TEXTDOMAIN ),
					'colors' => array(
						'background_color'      => '#0b1220',
						'form_background_color' => '#111827',
						'primary_button_color'  => '#22c55e',
						'label_color'           => '#e5e7eb',
						'nav_link_color'        => '#e5e7eb',
						'input_text_color'      => '#111827',
					),
				),
				'pastel'   => array(
					'label'  => __( 'Pastel', LCW_TEXTDOMAIN ),
					'colors' => array(
						'background_color'      => '#fff7ed',
						'form_background_color' => '#ffffff',
						'primary_button_color'  => '#fb7185',
						'label_color'           => '#334155',
						'nav_link_color'        => '#334155',
						'input_text_color'      => '#0f172a',
					),
				),
				'corporate' => array(
					'label'  => __( 'Corporativa', LCW_TEXTDOMAIN ),
					'colors' => array(
						'background_color'      => '#eef2ff',
						'form_background_color' => '#ffffff',
						'primary_button_color'  => '#4f46e5',
						'label_color'           => '#111827',
						'nav_link_color'        => '#111827',
						'input_text_color'      => '#111827',
					),
				),
			)
		);

		wp_localize_script(
			'lcw-admin',
			'LCW_ADMIN',
			array(
				'optionName'     => SettingsRepository::OPTION_NAME,
				'settings'       => $this->settings->all(),
				'palettes'       => $palettes,
				'cropNonce'      => wp_create_nonce( 'crop-image' ),
				'strings'        => array(
					'chooseImage' => __( 'Elegir imagen', LCW_TEXTDOMAIN ),
					'removeImage' => __( 'Quitar', LCW_TEXTDOMAIN ),
					'cropImage'   => __( 'Recortar', LCW_TEXTDOMAIN ),
					'useImage'    => __( 'Usar imagen', LCW_TEXTDOMAIN ),
				),
				'crop'           => array(
					'aspectRatio' => 4 / 1,
					'minWidth'    => 400,
					'minHeight'   => 100,
				),
			)
		);
	}

	/**
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'No tienes permisos para acceder a esta página.', LCW_TEXTDOMAIN ) );
		}

		$settings = $this->settings->all();
		$view     = LCW_PATH . 'includes/Modules/Admin/views/settings-page.php';

		if ( is_readable( $view ) ) {
			require $view;
			return;
		}

		echo '<div class="wrap"><h1>' . esc_html__( 'Login Customizer', LCW_TEXTDOMAIN ) . '</h1></div>';
	}
}
