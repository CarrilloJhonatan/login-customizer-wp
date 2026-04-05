<?php

/**
 * @var array $settings
 */

$option_name = \LoginCustomizerWP\Settings\SettingsRepository::OPTION_NAME;
$templates   = apply_filters(
	'lcw_allowed_templates',
	array( 'default', 'minimal', 'centered' )
);
$palettes    = apply_filters(
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

?>
<div class="wrap lcw-wrap">
	<h1><?php echo esc_html__( 'Login Customizer', LCW_TEXTDOMAIN ); ?></h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'lcw_settings_group' ); ?>

		<div class="lcw-grid">
			<div class="lcw-card">
				<h2 class="lcw-card__title"><?php echo esc_html__( 'Logo', LCW_TEXTDOMAIN ); ?></h2>

				<input type="hidden" id="lcw_logo_attachment_id" name="<?php echo esc_attr( $option_name ); ?>[logo_attachment_id]" value="<?php echo esc_attr( (int) ( $settings['logo_attachment_id'] ?? 0 ) ); ?>" />
				<input type="text" class="regular-text" id="lcw_logo_url" name="<?php echo esc_attr( $option_name ); ?>[logo_url]" value="<?php echo esc_attr( (string) ( $settings['logo_url'] ?? '' ) ); ?>" placeholder="<?php echo esc_attr__( 'URL del logo o selecciona una imagen', LCW_TEXTDOMAIN ); ?>" />

				<div class="lcw-logo">
					<img id="lcw_logo_preview" src="<?php echo esc_url( (string) ( $settings['logo_url'] ?? '' ) ); ?>" alt="" />
				</div>

				<div class="lcw-actions">
					<button type="button" class="button" id="lcw_logo_select"><?php echo esc_html__( 'Elegir imagen', LCW_TEXTDOMAIN ); ?></button>
					<button type="button" class="button" id="lcw_logo_crop"><?php echo esc_html__( 'Recortar', LCW_TEXTDOMAIN ); ?></button>
					<button type="button" class="button button-link-delete" id="lcw_logo_remove"><?php echo esc_html__( 'Quitar', LCW_TEXTDOMAIN ); ?></button>
				</div>

				<p class="description"><?php echo esc_html__( 'Recomendado: 400×100 px (relación 4:1).', LCW_TEXTDOMAIN ); ?></p>
			</div>

			<div class="lcw-card">
				<h2 class="lcw-card__title"><?php echo esc_html__( 'Colores', LCW_TEXTDOMAIN ); ?></h2>

				<div class="lcw-field">
					<label for="lcw_palette"><?php echo esc_html__( 'Paleta', LCW_TEXTDOMAIN ); ?></label>
					<select id="lcw_palette" name="<?php echo esc_attr( $option_name ); ?>[palette]">
						<?php foreach ( (array) $palettes as $slug => $data ) : ?>
							<option value="<?php echo esc_attr( (string) $slug ); ?>" <?php selected( (string) ( $settings['palette'] ?? 'default' ), (string) $slug ); ?>>
								<?php echo esc_html( (string) ( $data['label'] ?? $slug ) ); ?>
							</option>
						<?php endforeach; ?>
						<option value="custom" <?php selected( (string) ( $settings['palette'] ?? '' ), 'custom' ); ?>><?php echo esc_html__( 'Personalizada', LCW_TEXTDOMAIN ); ?></option>
					</select>
				</div>

				<div class="lcw-colors">
					<div class="lcw-field">
						<label for="lcw_background_color"><?php echo esc_html__( 'Fondo', LCW_TEXTDOMAIN ); ?></label>
						<input type="text" class="lcw-color" id="lcw_background_color" name="<?php echo esc_attr( $option_name ); ?>[background_color]" value="<?php echo esc_attr( (string) ( $settings['background_color'] ?? '' ) ); ?>" data-default-color="#f1f1f1" />
					</div>

					<div class="lcw-field">
						<label for="lcw_form_background_color"><?php echo esc_html__( 'Fondo del formulario', LCW_TEXTDOMAIN ); ?></label>
						<input type="text" class="lcw-color" id="lcw_form_background_color" name="<?php echo esc_attr( $option_name ); ?>[form_background_color]" value="<?php echo esc_attr( (string) ( $settings['form_background_color'] ?? '' ) ); ?>" data-default-color="#ffffff" />
					</div>

					<div class="lcw-field">
						<label for="lcw_primary_button_color"><?php echo esc_html__( 'Botón primario', LCW_TEXTDOMAIN ); ?></label>
						<input type="text" class="lcw-color" id="lcw_primary_button_color" name="<?php echo esc_attr( $option_name ); ?>[primary_button_color]" value="<?php echo esc_attr( (string) ( $settings['primary_button_color'] ?? '' ) ); ?>" data-default-color="#2271b1" />
					</div>

					<div class="lcw-field">
						<label for="lcw_label_color"><?php echo esc_html__( 'Texto/etiquetas', LCW_TEXTDOMAIN ); ?></label>
						<input type="text" class="lcw-color" id="lcw_label_color" name="<?php echo esc_attr( $option_name ); ?>[label_color]" value="<?php echo esc_attr( (string) ( $settings['label_color'] ?? '' ) ); ?>" data-default-color="#1d2327" />
					</div>

					<div class="lcw-field">
						<label for="lcw_nav_link_color"><?php echo esc_html__( 'Links de navegación', LCW_TEXTDOMAIN ); ?></label>
						<input type="text" class="lcw-color" id="lcw_nav_link_color" name="<?php echo esc_attr( $option_name ); ?>[nav_link_color]" value="<?php echo esc_attr( (string) ( $settings['nav_link_color'] ?? '' ) ); ?>" data-default-color="#1d2327" />
					</div>

					<div class="lcw-field">
						<label for="lcw_input_text_color"><?php echo esc_html__( 'Texto en inputs', LCW_TEXTDOMAIN ); ?></label>
						<input type="text" class="lcw-color" id="lcw_input_text_color" name="<?php echo esc_attr( $option_name ); ?>[input_text_color]" value="<?php echo esc_attr( (string) ( $settings['input_text_color'] ?? '' ) ); ?>" data-default-color="#1d2327" />
					</div>
				</div>
			</div>

			<div class="lcw-card">
				<h2 class="lcw-card__title"><?php echo esc_html__( 'Plantillas', LCW_TEXTDOMAIN ); ?></h2>

				<div class="lcw-field">
					<label for="lcw_template"><?php echo esc_html__( 'Plantilla activa', LCW_TEXTDOMAIN ); ?></label>
					<select id="lcw_template" name="<?php echo esc_attr( $option_name ); ?>[template]">
						<?php foreach ( (array) $templates as $slug ) : ?>
							<option value="<?php echo esc_attr( (string) $slug ); ?>" <?php selected( (string) ( $settings['template'] ?? 'default' ), (string) $slug ); ?>>
								<?php echo esc_html( ucwords( str_replace( '-', ' ', (string) $slug ) ) ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<p class="description">
					<?php echo esc_html__( 'Puedes sobrescribir plantillas en tu tema: /login-customizer-wp/templates/', LCW_TEXTDOMAIN ); ?>
				</p>
			</div>

			<div class="lcw-card">
				<h2 class="lcw-card__title"><?php echo esc_html__( 'Rendimiento y logs', LCW_TEXTDOMAIN ); ?></h2>

				<div class="lcw-field">
					<label for="lcw_cache_ttl"><?php echo esc_html__( 'Caché (TTL, segundos)', LCW_TEXTDOMAIN ); ?></label>
					<input type="number" min="60" step="60" id="lcw_cache_ttl" name="<?php echo esc_attr( $option_name ); ?>[cache_ttl]" value="<?php echo esc_attr( (int) ( $settings['cache_ttl'] ?? 3600 ) ); ?>" />
				</div>

				<div class="lcw-field lcw-field--inline">
					<label>
						<input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[enable_logging]" value="1" <?php checked( ! empty( $settings['enable_logging'] ) ); ?> />
						<?php echo esc_html__( 'Habilitar logs estructurados', LCW_TEXTDOMAIN ); ?>
					</label>
				</div>
			</div>

			<div class="lcw-card">
				<h2 class="lcw-card__title"><?php echo esc_html__( 'Enlaces del logo', LCW_TEXTDOMAIN ); ?></h2>

				<div class="lcw-field">
					<label for="lcw_login_header_url"><?php echo esc_html__( 'URL del logo', LCW_TEXTDOMAIN ); ?></label>
					<input type="url" class="regular-text" id="lcw_login_header_url" name="<?php echo esc_attr( $option_name ); ?>[login_header_url]" value="<?php echo esc_attr( (string) ( $settings['login_header_url'] ?? home_url( '/' ) ) ); ?>" />
				</div>

				<div class="lcw-field">
					<label for="lcw_login_header_text"><?php echo esc_html__( 'Texto del logo', LCW_TEXTDOMAIN ); ?></label>
					<input type="text" class="regular-text" id="lcw_login_header_text" name="<?php echo esc_attr( $option_name ); ?>[login_header_text]" value="<?php echo esc_attr( (string) ( $settings['login_header_text'] ?? '' ) ); ?>" />
				</div>
			</div>

			<div class="lcw-card lcw-card--preview">
				<h2 class="lcw-card__title"><?php echo esc_html__( 'Vista previa', LCW_TEXTDOMAIN ); ?></h2>

				<div id="lcw_preview" class="lcw-preview">
					<div class="lcw-preview__logo" id="lcw_preview_logo"></div>
					<div class="lcw-preview__notice notice notice-info message">
						<p><?php echo esc_html__( 'Please enter your username or email address. You will receive an email message with instructions on how to reset your password.', LCW_TEXTDOMAIN ); ?></p>
					</div>
					<div class="lcw-preview__form">
						<label><?php echo esc_html__( 'Usuario o correo', LCW_TEXTDOMAIN ); ?></label>
						<input type="text" value="" />
						<label><?php echo esc_html__( 'Contraseña', LCW_TEXTDOMAIN ); ?></label>
						<input type="password" value="" />
						<button type="button" class="button button-primary"><?php echo esc_html__( 'Acceder', LCW_TEXTDOMAIN ); ?></button>
					</div>
					<div class="lcw-preview__nav">
						<a href="#"><?php echo esc_html__( '¿Olvidaste tu contraseña?', LCW_TEXTDOMAIN ); ?></a>
						<span class="lcw-preview__dot">•</span>
						<a href="#"><?php echo esc_html__( 'Volver al sitio', LCW_TEXTDOMAIN ); ?></a>
					</div>
				</div>

				<p class="description"><?php echo esc_html__( 'La vista previa se actualiza en tiempo real.', LCW_TEXTDOMAIN ); ?></p>
			</div>
		</div>

		<?php submit_button( __( 'Guardar cambios', LCW_TEXTDOMAIN ) ); ?>
	</form>
</div>
