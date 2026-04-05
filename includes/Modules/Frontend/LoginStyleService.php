<?php

namespace LoginCustomizerWP\Modules\Frontend;

use LoginCustomizerWP\Settings\SettingsRepository;
use LoginCustomizerWP\Utils\Color;
use LoginCustomizerWP\Utils\PluginHooks;

/**
 * Generates login CSS based on settings and templates.
 */
final class LoginStyleService {
	/**
	 * @var SettingsRepository
	 */
	private $settings;

	/**
	 * @var PluginHooks
	 */
	private $hooks;

	/**
	 * @param SettingsRepository $settings Settings.
	 * @param PluginHooks        $hooks Hooks.
	 */
	public function __construct( SettingsRepository $settings, PluginHooks $hooks ) {
		$this->settings = $settings;
		$this->hooks    = $hooks;
	}

	/**
	 * @return string
	 */
	public function build_css() {
		$logo_css   = $this->logo_css();
		$bg         = sanitize_hex_color( (string) $this->settings->get( 'background_color', '#f1f1f1' ) ) ?: '#f1f1f1';
		$form_bg    = sanitize_hex_color( (string) $this->settings->get( 'form_background_color', '#ffffff' ) ) ?: '#ffffff';
		$primary    = sanitize_hex_color( (string) $this->settings->get( 'primary_button_color', '#2271b1' ) ) ?: '#2271b1';
		$label      = sanitize_hex_color( (string) $this->settings->get( 'label_color', '#1d2327' ) ) ?: '#1d2327';
		$nav        = sanitize_hex_color( (string) $this->settings->get( 'nav_link_color', $label ) ) ?: $label;
		$input_text = sanitize_hex_color( (string) $this->settings->get( 'input_text_color', $label ) ) ?: $label;
		$template   = (string) $this->settings->get( 'template', 'default' );

		$min_ratio      = (float) $this->hooks->apply_filters( 'lcw_min_contrast_ratio', 4.5 );
		$nav_safe       = Color::ensure_contrast( $nav, $bg, $min_ratio, array( '#111827', '#ffffff', $label ) );
		$input_bg       = '#ffffff';
		$input_text_safe = Color::ensure_contrast( $input_text, $input_bg, $min_ratio, array( '#111827', '#ffffff', $label ) );

		$css = "
body.login{
	--lcw-bg:{$bg};
	--lcw-form-bg:{$form_bg};
	--lcw-primary:{$primary};
	--lcw-label:{$label};
	--lcw-nav:{$nav_safe};
	--lcw-input-bg:{$input_bg};
	--lcw-input-text:{$input_text_safe};
	background-color:var(--lcw-bg);
	color:var(--lcw-label);
}
.login form{
	border-radius:20px;
	background-color:var(--lcw-form-bg) !important;
	border-color:var(--lcw-form-bg) !important;
}
.login #loginform{
	color:var(--lcw-label);
}
.login #loginform label,
.login .forgetmenot label,
.login #loginform p{
	color:var(--lcw-label) !important;
}
.login #login_error,
.login .message,
.login .success{
	color:var(--lcw-label) !important;
	border-left-color:var(--lcw-primary);
	background:var(--lcw-form-bg);
}
.login .notice{
	color:var(--lcw-label) !important;
}
.login .notice.message{
	color:var(--lcw-label) !important;
}
.login div.notice.notice-info.message{
	color:var(--lcw-label) !important;
}
.login .message p,
.login .notice p,
.login #login_error p,
.login .success p{
	color:inherit !important;
}
.login #backtoblog a,
.login #nav a,
.login .privacy-policy-link{
	color:var(--lcw-nav) !important;
}
.login #backtoblog a:focus-visible,
.login #nav a:focus-visible,
.login .privacy-policy-link:focus-visible{
	outline:2px solid var(--lcw-primary);
	outline-offset:2px;
	border-radius:4px;
}
.login #loginform input[type=\"text\"],
.login #loginform input[type=\"password\"],
.login #loginform input[type=\"email\"],
.login #loginform input[type=\"url\"],
.login #loginform input[type=\"tel\"],
.login #loginform input[type=\"number\"]{
	background-color:var(--lcw-input-bg) !important;
	color:var(--lcw-input-text) !important;
}
.login #loginform input::placeholder{
	color:var(--lcw-input-text);
	opacity:.6;
}
.login #loginform input:focus-visible{
	outline:2px solid var(--lcw-primary);
	outline-offset:2px;
	box-shadow:none;
}
.wp-core-ui .button-primary{
	background:var(--lcw-primary) !important;
	border-color:var(--lcw-primary) !important;
	color:#fff !important;
	box-shadow:0 1px 0 var(--lcw-primary) !important;
}
.wp-core-ui .button-secondary,
.button{
	color:var(--lcw-primary) !important;
	border-color:var(--lcw-primary) !important;
}
#wfls-prompt-overlay{background-color:var(--lcw-form-bg) !important;}
{$logo_css}
";

		$css .= $this->template_css( $template );

		return (string) $this->hooks->apply_filters( 'lcw_login_css', $css, $this->settings->all() );
	}

	/**
	 * @return string
	 */
	private function logo_css() {
		$attachment_id = (int) $this->settings->get( 'logo_attachment_id', 0 );
		$logo_url      = (string) $this->settings->get( 'logo_url', '' );

		$full_url = '';
		if ( $attachment_id > 0 ) {
			$full = wp_get_attachment_image_src( $attachment_id, 'full' );
			if ( is_array( $full ) && ! empty( $full[0] ) ) {
				$full_url = $this->css_url( $full[0] );
			}
		}

		if ( '' === $full_url && '' !== $logo_url ) {
			$full_url = $this->css_url( $logo_url );
		}

		if ( '' === $full_url ) {
			return '';
		}

		return "
#login h1 a,.login h1 a{
	background-image:url(\"{$full_url}\");
	margin-bottom:0;
	background-size:contain;
	width:100%;
	height:130px;
	margin-left:auto;
	margin-right:auto;
}
";
	}

	/**
	 * @param string $url URL.
	 * @return string
	 */
	private function css_url( $url ) {
		$url = is_string( $url ) ? $url : '';
		$url = trim( $url );
		$url = preg_replace( '/^[`"\']+|[`"\']+$/', '', $url );
		if ( preg_match( '/url\((.*)\)/i', $url, $m ) && isset( $m[1] ) ) {
			$url = trim( (string) $m[1] );
			$url = preg_replace( '/^[`"\']+|[`"\']+$/', '', $url );
		}
		$url = preg_replace( '/[)\s`"\']+$/', '', $url );
		return esc_url_raw( $url );
	}

	/**
	 * @param string $template Template slug.
	 * @return string
	 */
	private function template_css( $template ) {
		switch ( (string) $template ) {
			case 'minimal':
				return "
.login form{box-shadow:none;border:1px solid rgba(0,0,0,.08);}
";
			case 'centered':
				return "
body.login{display:flex;align-items:center;justify-content:center;}
#login{padding:0;}
";
			default:
				return '';
		}
	}
}
