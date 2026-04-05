<?php

namespace LoginCustomizerWP\Modules\Rest;

use LoginCustomizerWP\Settings\SettingsRepository;
use LoginCustomizerWP\Utils\Logger;
use LoginCustomizerWP\Utils\PluginHooks;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST controller for plugin settings.
 */
final class SettingsRestController {
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
	public function register_routes() {
		register_rest_route(
			'lcw/v1',
			'/settings',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'permission_callback' => array( $this, 'can_manage' ),
					'callback'            => array( $this, 'get_settings' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'permission_callback' => array( $this, 'can_manage' ),
					'callback'            => array( $this, 'update_settings' ),
					'args'                => array(
						'settings' => array(
							'type'     => 'object',
							'required' => true,
						),
					),
				),
			)
		);
	}

	/**
	 * @return bool
	 */
	public function can_manage() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @return WP_REST_Response
	 */
	public function get_settings() {
		return new WP_REST_Response(
			array(
				'settings' => $this->settings->all(),
			),
			200
		);
	}

	/**
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function update_settings( WP_REST_Request $request ) {
		$raw  = (array) $request->get_param( 'settings' );
		$data = $this->settings->sanitize( $raw );

		update_option( SettingsRepository::OPTION_NAME, $data );
		$this->maybe_log( 'settings_updated_via_rest', array( 'user_id' => get_current_user_id() ) );

		return new WP_REST_Response(
			array(
				'settings' => $this->settings->all(),
			),
			200
		);
	}

	/**
	 * @param string $event Event.
	 * @param array  $context Context.
	 * @return void
	 */
	private function maybe_log( $event, array $context = array() ) {
		if ( ! $this->settings->get( 'enable_logging', false ) ) {
			return;
		}
		$this->logger->info( (string) $event, $context );
	}
}
