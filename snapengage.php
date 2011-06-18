<?php
/*
Plugin Name: SnapEngage
Plugin URI: http://www.SnapEngage.com/wordpress
Description: Enables SnapEngage on your web site.
Version: 1.0
Author: Phill Kenoyer - Orange Room Software
Author URI: http://www.OrangeRoomSoftware.com/
License: BSD
*/

/**
 * Initialize Options and Options page
 */
add_action( 'admin_init', 'snapengage_options_init' );
function snapengage_options_init(){
  register_setting( 'snapengage_options', 'snapengage' );
}

/**
 * Set defaults on activation
 */
register_activation_hook(__FILE__, 'snapengage_defaults');
function snapengage_defaults()
{
  $defaults = array(
    'api_key'      => '',
    'button_type'  => 0,
    'position'     => 0,
    'offset'       => '55',
    'offset_units' => '%',
    'show_screenshot' => true,
    'allow_offline' => true
  );
  update_option( 'snapengage', $defaults );
}

/**
 * Add menu item
 */
add_action( 'admin_menu', 'snapengage_options_add_page' );
function snapengage_options_add_page() {
  add_options_page(
    'SnapEngage Options',
    'SnapEngage',
    'manage_options',
    'snapengage_options',
    'snapengage_options_do_page');
}

/**
 * Draw the options page
 */
function snapengage_options_do_page() {
?>
  <div class="wrap">
    <h2>SnapEngage Options</h2>
    <form method="post" action="options.php">
      <?php settings_fields('snapengage_options'); ?>
      <?php $options = get_option('snapengage'); ?>
      <table class="form-table">
        <tr valign="top"><th scope="row">API Key</th>
          <td>
            <input type="text" name="snapengage[api_key]" size="55" maxlength="100" value="<?php echo $options['api_key']; ?>" />
          </td>
        </tr>
        <tr><td><br/></td></tr>
        <tr valign="top"><th scope="row">Button Type</th>
          <td>
            <select name="snapengage[button_type]">
              <option value='0' <?php selected( $options['button_type'], 0 ); ?>>Default</option>
              <option value='1' <?php selected( $options['button_type'], 1 ); ?>>Dynamic Live Chat</option>
              <option value='2' <?php selected( $options['button_type'], 2 ); ?>>No Button</option>
            </select>
          </td>
        </tr>
        <tr valign="top"><th scope="row">Align button to the</th>
          <td>
            <select name="snapengage[position]">
              <option value='0' <?php selected( $options['position'], 0 ); ?>>Left</option>
              <option value='1' <?php selected( $options['position'], 1 ); ?>>Right</option>
              <option value='2' <?php selected( $options['position'], 2 ); ?>>Top</option>
              <option value='3' <?php selected( $options['position'], 3 ); ?>>Bottom</option>
            </select>
          </td>
        </tr>
        <tr valign="top"><th scope="row">With an offset of</th>
          <td>
            <input type="text" name="snapengage[offset]" size="3" maxlength="5" value="<?php echo $options['offset']; ?>" />
            <select name="snapengage[offset_units]">
              <option value='%' <?php selected( $options['offset_units'], '%' ); ?>>%</option>
              <option value='px' <?php selected( $options['offset_units'], 'px' ); ?>>px</option>
            </select> from the top
          </td>
        </tr>
        <tr valign="top"><th scope="row">Show Screenshot Option?</th>
            <td>
                <select name="snapengage[show_screenshot]">
                  <option value='1' <?php selected( $options['show_screenshot'], 1 ); ?>>Yes</option>
                  <option value='0' <?php selected( $options['show_screenshot'],0 ); ?>>No</option>
                </select>
            </td>
         </tr>
        <tr valign="top"><th scope="row">Allow Offline Engagement?</th>
            <td>
                <select name="snapengage[allow_offline]">
                  <option value='1' <?php selected( $options['allow_offline'],1 ); ?>>Yes</option>
                  <option value='0' <?php selected( $options['allow_offline'],0 ); ?>>No</option>
                </select>
            </td>
         </tr>
      </table>
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
    </form>
  </div>
  <?php
}

/**
 * Remote Javascript
 */
function snapengage_remote_js() {
  $s = 'http://www.snapengage.com'; if ($_SERVER['SERVER_PORT']==443) $s = 'https://snapabug.appspot.com';
  wp_enqueue_script('snapengage', "$s/snapabug.js", false, null, true);
}

/**
 * Footer script
 */
function snapengage_js() {
  $snapengage_options = get_option('snapengage');
  print "<script type='text/javascript'>";
  switch ($snapengage_options['button_type']) {
    case 2:
      print "SnapABug.init('".$snapengage_options['api_key']."');";
      break;
    case 1:
      print "SnapABug.addButton('".$snapengage_options['api_key']."', '".$snapengage_options['position']."', '".$snapengage_options['offset']."".$snapengage_options['offset_units']."', true);";
      break;
    case 0:
      print "SnapABug.addButton('".$snapengage_options['api_key']."', '".$snapengage_options['position']."', '".$snapengage_options['offset']."".$snapengage_options['offset_units']."');";
  }

  if (!$snapengage_options['show_screenshot']) {
      print 'SnapABug.showScreenshotOption(false);';
  }

  if (!$snapengage_options['allow_offline']) {
      print 'SnapABug.allowOffline(false);';
  }

  print "</script>";
}

/**
 * When not in Admin load scripts
 */
if (!is_admin()) {
  add_action('wp_print_scripts', 'snapengage_remote_js', 20);
  add_action('wp_footer', 'snapengage_js', 30);
}
