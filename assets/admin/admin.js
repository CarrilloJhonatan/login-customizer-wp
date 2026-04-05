(function ($) {
	function getVal(selector) {
		var $el = $(selector);
		return $el.length ? $el.val() : '';
	}

	function setVal(selector, value) {
		var $el = $(selector);
		if ($el.length) {
			$el.val(value).trigger('change');
		}
	}

	function normalizeUrl(url) {
		if (!url) return '';
		var v = String(url).trim();
		v = v.replace(/^[`"']+/, '').replace(/[`"']+$/g, '');
		var m = v.match(/url\((.*)\)/i);
		if (m && m[1]) {
			v = String(m[1]).trim();
			v = v.replace(/^[`"']+/, '').replace(/[`"']+$/g, '');
		}
		v = v.replace(/[)\s`'"]+$/g, '');
		return v;
	}

	function setColor(selector, value) {
		var $el = $(selector);
		if (!$el.length) return;
		if ($el.hasClass('wp-color-picker')) {
			$el.wpColorPicker('color', value);
			return;
		}
		$el.val(value).trigger('change');
	}

	function updatePreview() {
		var bg = getVal('#lcw_background_color');
		var formBg = getVal('#lcw_form_background_color');
		var primary = getVal('#lcw_primary_button_color');
		var label = getVal('#lcw_label_color');
		var nav = getVal('#lcw_nav_link_color');
		var inputText = getVal('#lcw_input_text_color');
		var logoUrl = normalizeUrl(getVal('#lcw_logo_url'));

		var $preview = $('#lcw_preview');
		$preview.css('--lcw-bg', bg);
		$preview.css('--lcw-form-bg', formBg);
		$preview.css('--lcw-primary', primary);
		$preview.css('--lcw-label', label);
		$preview.css('--lcw-nav', nav);
		$preview.css('--lcw-input-text', inputText);

		$('#lcw_preview_logo').css('background-image', logoUrl ? 'url(' + logoUrl + ')' : 'none');
	}

	function applyPalette(slug) {
		if (!LCW_ADMIN || !LCW_ADMIN.palettes || !LCW_ADMIN.palettes[slug]) {
			return;
		}
		var colors = LCW_ADMIN.palettes[slug].colors || {};
		if (colors.background_color) setColor('#lcw_background_color', colors.background_color);
		if (colors.form_background_color) setColor('#lcw_form_background_color', colors.form_background_color);
		if (colors.primary_button_color) setColor('#lcw_primary_button_color', colors.primary_button_color);
		if (colors.label_color) setColor('#lcw_label_color', colors.label_color);
		if (colors.nav_link_color) setColor('#lcw_nav_link_color', colors.nav_link_color);
		if (colors.input_text_color) setColor('#lcw_input_text_color', colors.input_text_color);

		updatePreview();
	}

	function openMediaFrame() {
		var frame = wp.media({
			title: LCW_ADMIN.strings.chooseImage,
			button: { text: LCW_ADMIN.strings.useImage },
			multiple: false,
			library: { type: 'image' }
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			setVal('#lcw_logo_attachment_id', attachment.id || 0);
			var url = normalizeUrl(attachment.url || '');
			setVal('#lcw_logo_url', url);
			$('#lcw_logo_preview').attr('src', url);
			updatePreview();
		});

		frame.open();
	}

	function openCropFrame(attachmentId) {
		var attachment = wp.media.attachment(attachmentId);

		attachment.fetch().done(function () {
			var selection = new wp.media.model.Selection([attachment], { multiple: false });

			var cropControl = {
				id: 'lcw_logo_crop',
				params: {
					flex_width: false,
					flex_height: false,
					width: LCW_ADMIN.crop.minWidth,
					height: LCW_ADMIN.crop.minHeight
				}
			};

			var frame = wp.media({
				title: LCW_ADMIN.strings.cropImage,
				button: { text: LCW_ADMIN.strings.useImage },
				multiple: false,
				states: [
					new wp.media.controller.Library({
						title: LCW_ADMIN.strings.chooseImage,
						library: wp.media.query({ type: 'image' }),
						multiple: false,
						selection: selection
					}),
					new wp.media.controller.Cropper({
						control: cropControl,
						imgSelectOptions: function () {
							return {
								aspectRatio: LCW_ADMIN.crop.aspectRatio,
								minWidth: LCW_ADMIN.crop.minWidth,
								minHeight: LCW_ADMIN.crop.minHeight
							};
						}
					})
				]
			});

			frame.on('select', function () {
				var att = frame.state().get('selection').first();
				if (att) {
					frame.setState('cropper');
				}
			});

			frame.on('cropped', function (cropped) {
				if (cropped && cropped.url) {
					var url = normalizeUrl(cropped.url);
					setVal('#lcw_logo_url', url);
					$('#lcw_logo_preview').attr('src', url);
					if (cropped.id) {
						setVal('#lcw_logo_attachment_id', cropped.id);
					}
					updatePreview();
				}
			});

			frame.on('ready', function () {
				frame.setState('cropper');
			});

			frame.open();
		});
	}

	$(function () {
		$('.lcw-color').each(function () {
			var $input = $(this);
			if ($input.data('wpWpColorPicker')) {
				return;
			}
			$input.wpColorPicker({
				change: function () {
					$('#lcw_palette').val('custom');
					updatePreview();
				},
				clear: function () {
					$('#lcw_palette').val('custom');
					updatePreview();
				}
			});
		});

		$('#lcw_palette').on('change', function () {
			var slug = $(this).val();
			if (slug && slug !== 'custom') {
				applyPalette(slug);
			}
			updatePreview();
		});

		$('#lcw_logo_select').on('click', function (e) {
			e.preventDefault();
			openMediaFrame();
		});

		$('#lcw_logo_remove').on('click', function (e) {
			e.preventDefault();
			setVal('#lcw_logo_attachment_id', 0);
			setVal('#lcw_logo_url', '');
			$('#lcw_logo_preview').attr('src', '');
			updatePreview();
		});

		$('#lcw_logo_url').on('blur', function () {
			var v = normalizeUrl($(this).val());
			$(this).val(v);
			$('#lcw_logo_preview').attr('src', v);
			updatePreview();
		});

		var t = null;
		$('#lcw_logo_url').on('input', function () {
			var $el = $(this);
			if (t) {
				clearTimeout(t);
			}
			t = setTimeout(function () {
				var v = normalizeUrl($el.val());
				$('#lcw_logo_preview').attr('src', v);
				updatePreview();
			}, 120);
		});

		$('#lcw_logo_crop').on('click', function (e) {
			e.preventDefault();
			var id = parseInt(getVal('#lcw_logo_attachment_id'), 10);
			if (!id) {
				return;
			}
			openCropFrame(id);
		});

		updatePreview();
	});
})(jQuery);
