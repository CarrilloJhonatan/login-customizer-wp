# Login Customizer WP

Plugin para personalizar la página de inicio de sesión de WordPress (logo, colores, navegación, inputs, mensajes/notices) con panel moderno, shortcodes, REST API y bloque Gutenberg.

## Características

- Panel de configuración con WordPress Color Picker (paletas predefinidas + personalización) y vista previa en tiempo real.
- Selector de imagen nativo (Media Library) con selección/subida y opción de recorte.
- Logo en login usando la imagen original (full) cuando se selecciona desde la Media Library.
- Colores configurables:
  - Fondo (`background_color`)
  - Fondo del formulario (`form_background_color`)
  - Botón primario (`primary_button_color`)
  - Texto/etiquetas (`label_color`)
  - Links de navegación (`nav_link_color`)
  - Texto en inputs (`input_text_color`)
- Los mensajes de estado (`#login_error`, `.message`, `.success`, `.notice`) usan el color de Texto/etiquetas.
- CSS con variables (`--lcw-*`) para facilitar futuras modificaciones y compatibilidad con temas (claro/oscuro).
- Validación/sanitización de datos con helpers y funciones nativas de WordPress.
- Caché inteligente del CSS del login con invalidación automática.
- Sistema de plantillas sobrescribibles desde el tema.
- Shortcodes dinámicos.
- REST API para leer/actualizar settings.
- Compatibilidad Gutenberg mediante `register_block_type()` (bloque dinámico).
- Internacionalización (textdomain + `.pot`).
- Logger estructurado (JSON) opcional.
- Tests unitarios (scaffolding PHPUnit + Brain Monkey).

## Requisitos

- WordPress 6.x recomendado (debe incluir Media Library y Color Picker).
- PHP >= 7.4.

## Instalación

1. Descarga el plugin y súbelo como ZIP desde `Plugins > Añadir nuevo > Subir plugin`.
2. Actívalo.
3. En el admin abre `Login Customizer` para configurar.

## Configuración (Admin)

Menú: `Login Customizer`

- Logo:
  - Selecciona una imagen desde la Media Library (opcionalmente recórtala).
  - También puedes pegar una URL de imagen (se normaliza y sanitiza).
- Paletas:
  - Selecciona una paleta predefinida o usa “Personalizada” para editar colores manualmente.
- Vista previa:
  - Refleja cambios de colores e imagen en vivo.

## Opciones guardadas (wp_options)

Se guardan en una sola opción:

- `lcw_settings` (array)

Claves principales:

- `logo_attachment_id` (int)
- `logo_url` (string)
- `background_color` (hex)
- `form_background_color` (hex)
- `primary_button_color` (hex)
- `label_color` (hex)
- `nav_link_color` (hex)
- `input_text_color` (hex)
- `palette` (string)
- `template` (string)
- `cache_ttl` (int, mínimo 60)
- `enable_logging` (bool)
- `login_header_url` (url)
- `login_header_text` (string)

Compatibilidad: si existen opciones legacy (`custom_login_*`), el plugin las lee como fallback.

## Shortcodes

- `[lcw_login_box]` Inserta un formulario de login.
  - Atributos: `redirect`, `remember`, `template`, `cache`.
- `[lcw_login_logo]` Inserta el logo configurado.
  - Atributos: `size`, `template`, `cache`.

## Plantillas

Plantillas incluidas en `templates/` del plugin:

- `login-box.php`
- `logo.php`

Override desde el tema:

- Copia las plantillas a:
  - `wp-content/themes/TU-TEMA/login-customizer-wp/templates/`
  - o `wp-content/themes/TU-CHILD/login-customizer-wp/templates/`

## Gutenberg

Bloque dinámico:

- `Login Box (LCW)` (categoría Widgets)
- Renderiza en frontend usando los shortcodes.

Archivos del bloque:

- `blocks/login-customizer/block.json`
- `blocks/login-customizer/editor.js`
- `blocks/login-customizer/render.php`

## REST API

Namespace: `lcw/v1`

- `GET /wp-json/lcw/v1/settings`
- `POST /wp-json/lcw/v1/settings`

Permisos: requiere `manage_options`.

## Caché

- El CSS del login se cachea usando transients.
- La invalidación se hace automáticamente cuando:
  - Se guardan settings (`lcw_cache_version` cambia).
  - Cambia la versión del plugin.
  - Cambia el archivo generador de estilos (mtime).

Si necesitas forzar, abre ajustes y pulsa “Guardar cambios”.

## Hooks y filtros

Filtros principales:

- `lcw_settings_defaults`
- `lcw_sanitize_settings`
- `lcw_allowed_templates`
- `lcw_color_palettes`
- `lcw_template_paths`
- `lcw_cache_ttl`
- `lcw_min_contrast_ratio`
- `lcw_login_css`

Action:

- `lcw_settings_updated`

## i18n

- Text Domain: `login-customizer-wp`
- Archivo `.pot`: `languages/login-customizer-wp.pot`

## Logs

Si activas “Habilitar logs estructurados”, se enviarán eventos a `error_log` en formato JSON (útil para debugging).

## Desarrollo y tests

Dependencias de desarrollo vía Composer:

1. `composer install`
2. Ejecuta tests con PHPUnit (según tu entorno):
   - `vendor/bin/phpunit`

Archivos:

- `phpunit.xml.dist`
- `tests/`

## Estructura del proyecto

- `custom.php`: bootstrap del plugin.
- `includes/`: código del plugin (módulos, settings, utils, templates loader).
- `assets/admin/`: JS/CSS del panel.
- `templates/`: plantillas base.
- `blocks/`: bloque Gutenberg.
- `languages/`: i18n.
- `tests/`: tests unitarios.

## Solución de problemas

- Si ves estilos antiguos en el login:
  - Guarda ajustes una vez.
  - Limpia caché del navegador y cualquier plugin de caché/minificación.
- Si el logo no se actualiza en vista previa:
  - Verifica que la URL sea válida (sin backticks, sin `url(...)` pegado, sin `)` al final).
