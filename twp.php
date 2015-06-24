<?php
/**
 * @package Telegram for Wordpress
 * @version 1.0
 */
/*
Plugin Name: Telegram for WordPress
Description: Allows admins to recieve thier WordPress site notifications in their Telegram account. This plugin based on notifygram.org by Anton Ilzheev.
Author: Ameer Mousavi | Baloot Studio
Version: 1.0
Author URI: http://ameer.ir/
License: GPLv2 or later.
Text Domain: twp-plugin
Domain Path: /lang
*/
$twp_settings = 
// create custom plugin settings menu
add_action('admin_menu', 'twp_create_menu');
function twp_create_menu() {
	//create new top-level menu
	add_menu_page('TWP Plugin Settings', 'TWP Settings', 'administrator', __FILE__, 'twp_settings_page',plugins_url('icon.png', __FILE__));
	//call register settings function
	add_action( 'admin_init', 'register_twp_settings' );
}
function register_twp_settings() {
	//register our settings
	register_setting( 'twp-settings-group', 'twp_api_key' );
	register_setting( 'twp-settings-group', 'twp_api_token' );
	register_setting( 'twp-settings-group', 'twp_project_name' );
}
function twp_settings_page() {
?>
<div class="wrap">
    <h2><?php  echo __("Telegram for WordPress", "twp-plugin") ?></h2>
    <form method="post" action="options.php">
        <?php settings_fields( 'twp-settings-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo __("Instructions", "twp-plugin") ?></th>
                <td><p><?php printf(__("Login into %s using phone number registered in Telegram. Notifygram will send confirmation code via Telegram. Once you receive code, enter it in Notifygram and click confirm. After login, in 'Add Project' section, enter a custom name; this could be anything but we recommend to use your site name. After adding project, you will see the project view (users, api , etc). You can also add other admin of your website in the list but they have to confirm using confirmation code. Click on API link at the right of the project name and copy paste those codes in this page. Save and Enjoy! ",  "twp-plugin"), "<a href='https://notifygram.org/login' target='_blank'>Notifygram</a>") ?> </p></td>
            </tr>
            <tr valign="top">
                <th scope="row">API Key</th>
                <td><input type="text" name="twp_api_key" value="<?php echo get_option('twp_api_key'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">API Token</th>
                <td><input type="text" name="twp_api_token" value="<?php echo get_option('twp_api_token'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php  echo __("Show Project name in messages", "twp-plugin") ?></th>
                <td><input type="checkbox" name="twp_project_name" value="1" <?php checked( '1', get_option( 'twp_project_name' ) ); ?> /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php  echo __("Send a test Message", "twp-plugin") ?></th>
                <td><input type="button" name="twp_test" value='<?php  echo __("Send now!", "twp-plugin") ?>' onclick="if(jQuery('input[name=twp_api_key]').val() != '' && jQuery('input[name=twp_api_token]').val() != '' ) {jQuery.post('<?php echo plugins_url( 'test.php', __FILE__ ) ?>', { message: '<?php  echo __("This is a test message", "twp-plugin") ?>', api_key: '<?php echo get_option('twp_api_key'); ?>', api_token:'<?php echo get_option('twp_api_token'); ?>'}); } else {alert(' <?php  echo __("API key or API token are empty", "twp-plugin") ?>') }"/></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
</div>
<?php }
    add_action( 'plugins_loaded', 'twp_load_textdomain' );
    /**
     * Load plugin textdomain.
     *
     * @since 1.0.0
     */
    function twp_load_textdomain() {
      load_plugin_textdomain( 'twp-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); 
    }
    /**
    * Add action links to the plugin list for TWP.
    *
    * @param $links
    * @return array
    */
    function twp_plugin_action_links($links) {
    $links[] = '<a href="http://hamyarwp.com/telegram-for-wp/">' . __('Persian Tutorial in HamyarWP', 'twp-plugin') . '</a>';
    return $links;
    }
    add_action('plugin_action_links_' . plugin_basename(__FILE__), 'twp_plugin_action_links');

// Checks if TOKEN and API has been set. If not, show a warning message.
if (get_option('twp_api_key') && get_option('twp_api_token') ) {

    require_once("Notifygram.class.php");
	//This will get information about sent mail from PHPMailer and send it to user
    function twp_mail_action($result, $to, $cc, $bcc, $subject, $body){
        $nt = new Notifygram();
        $_apikey = get_option('twp_api_key');
        $_apitoken = get_option('twp_api_token');
        $_projectname = get_option('twp_project_name');
        $nt->Notifygram($_apikey,$_apitoken, $_projectname );
        $nt->notify($body);
    }

    /**
     * Setup a custom PHPMailer action callback. This will let us to fire our action every time a mail sent
     * Thanks to Birgire (http://xlino.com/) for creating this code snippet.
     */
    add_action( 'phpmailer_init', function( $phpmailer ){
        $phpmailer->action_function = 'twp_mail_action';
        } );
    
    } else {
        function twp_api_error_notice() {
        $class = "error";
        $message = sprintf(__('Your API key or API token are not set. Please go to %s and set them.','twp-plugin'), "<a href='".admin_url('admin.php?page=twp/twp.php')."'>".__("TWP Settings", "twp-plugin")."</a>");
            echo"<div class=\"$class\"> <p>$message</p></div>"; 
        }
    add_action( 'admin_notices', 'twp_api_error_notice' ); 
    }
