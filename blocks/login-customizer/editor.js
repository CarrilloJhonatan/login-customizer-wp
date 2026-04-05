(function (wp) {
	var el = wp.element.createElement;
	var registerBlockType = wp.blocks.registerBlockType;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;

	registerBlockType('login-customizer-wp/login-box', {
		edit: function (props) {
			var attrs = props.attributes;
			return [
				el(
					InspectorControls,
					{ key: 'controls' },
					el(
						PanelBody,
						{ title: 'Ajustes', initialOpen: true },
						el(TextControl, {
							label: 'Redirect URL',
							value: attrs.redirect,
							onChange: function (val) {
								props.setAttributes({ redirect: val });
							}
						}),
						el(ToggleControl, {
							label: 'Mostrar logo',
							checked: !!attrs.showLogo,
							onChange: function (val) {
								props.setAttributes({ showLogo: !!val });
							}
						})
					)
				),
				el(
					'div',
					{ key: 'preview', className: props.className },
					el('p', null, 'Login Box (LCW)'),
					el('p', null, 'Este bloque se renderiza en el frontend.')
				)
			];
		},
		save: function () {
			return null;
		}
	});
})(window.wp);

