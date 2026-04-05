<?php

$redirect  = isset( $attributes['redirect'] ) ? (string) $attributes['redirect'] : '';
$show_logo = isset( $attributes['showLogo'] ) ? (bool) $attributes['showLogo'] : true;

$out = '';
if ( $show_logo ) {
	$out .= do_shortcode( '[lcw_login_logo]' );
}

$shortcode = '[lcw_login_box redirect="' . esc_attr( $redirect ) . '"]';
$out      .= do_shortcode( $shortcode );

echo $out;

