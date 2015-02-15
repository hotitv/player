<?php
/*
Plugin Name: hoti™ player
Plugin URI: https://github.com/marcoscolina/player
Description: Reproductor musical diseñado para hoti™ plataforma.artistica
Author: Alexander Salas & Marcos Colina
Author URI: http://hoti.tv/contacto
Version: 1.8
Tags: hoti,plataforma,artistica
License: GNU General Public License v2.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

define ('SIG_PLUGIN_DIR', WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );
require_once('wp-hoti-player-functions.php');

/** Get Plugin Version **/
function get_hoti_version() {
	$plugin_data = get_plugin_data( __FILE__ );
	$plugin_version = $plugin_data['Version'];
	return $plugin_version;
}

/*** Plugin Init ***/
add_action( 'admin_init', 'hoti_admin_init' );
function hoti_admin_init() {
    register_setting( 'hoti_options', 'hoti_options' );
    wp_register_script('wp-hoti-player-js', SIG_PLUGIN_DIR.'wp-hoti-player-js.js', array('jquery', 'farbtastic'));
    wp_register_script('carouFredSel', SIG_PLUGIN_DIR.'includes/jquery.carouFredSel-5.5.0-packed.js', array('jquery'));
    wp_register_style('wp-hoti-player-css', SIG_PLUGIN_DIR.'wp-hoti-player-css.css');
    //wp_register_style('ChunkFive', SIG_PLUGIN_DIR.'includes/ChunkFive-fontfacekit/stylesheet.css');
    //wp_register_style('Quicksand', SIG_PLUGIN_DIR.'includes/Quicksand-fontfacekit/stylesheet.css');
    wp_register_style('wp-hoti-player-editor-css', SIG_PLUGIN_DIR.'tinymce-plugin/wp-hoti-player-editor_plugin.css');
}
//Plugin option scripts
function hoti_option_scripts() {
    wp_enqueue_script('farbtastic');
    wp_enqueue_script('wp-hoti-player-js');
    wp_enqueue_script('carouFredSel');
}
//Plugin option style
function hoti_option_styles() {
  wp_enqueue_style('wp-hoti-player-css');
  wp_enqueue_style('farbtastic');
}
//Plugin Options' Fonts
/* function hoti_option_fonts() {
  wp_enqueue_style('ChunkFive');
  wp_enqueue_style('Quicksand');
} */
/*** Add Admin Menu ***/
/* add_action('admin_menu', 'hoti_menu');
function hoti_menu() {
	//Main
	$hotiPage = add_menu_page('Hotï Player: Options', 'Hotï Player', 'activate_plugins', __FILE__, 'hoti_options', SIG_PLUGIN_DIR.'images/wp-hoti-player-icon.png');
	add_action( "admin_print_scripts-$hotiPage", 'hoti_option_scripts' ); // Add script
	add_action( "admin_print_styles-$hotiPage", 'hoti_option_styles' ); // Add Style
	//add_action( "admin_print_styles-$hotiPage", 'hoti_option_fonts' ); // Add Fonts
} */
function hoti_advanced_options() {
	//include('wp-hoti-player-advanced.php');
}
/*** Link to Settings from the plugin Page ***/
/* function hoti_settings_link($links, $file) { 
    if ( $file == plugin_basename( __FILE__ ) ) {
	$settings_link = '<a href="admin.php?page=wp-hoti-player-master/wp-hoti-player.php">'.__('Settings').'</a>'; 
	array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter("plugin_action_links", 'hoti_settings_link', 10, 2 ); */

/*** Add tint Mce Hotï Player Plugin ***/
add_filter("mce_external_plugins", 'hoti_mce_plugin');
//add_filter( 'mce_buttons', 'hoti_mce_button' );
add_filter('mce_css', 'hoti_mce_css');


/*** Options and Utilities***/
register_activation_hook(__FILE__, 'hoti_add_defaults');
function hoti_add_defaults() {
    $tmp = get_option('hoti_options');
    //First Time install or upgrade from version previous to 1.0.7
    if(empty($tmp)) {
	$hotiDefaultUsers = array(
					    'hotitv' => array('hotitv', 'http://i1.sndcdn.com/avatars-000076814116-uxpdjr-large.jpg?30a2558')
					    );
	$hotiDefaultUser = $hotiDefaultUsers[array_rand($hotiDefaultUsers, 1)][0];
	if(get_option('hoti_user')){
	    $hotiDefaultUser = get_option('hoti_user');
	    $userInfo = get_hoti_api_response("http://api.soundcloud.com/users/".$hotiDefaultUser.".xml?client_id=43195eb2f2b85520cb5f65e78d6501bf");
	    $newUsername = (string)$userInfo['response']->permalink;
	    $newUsernameImg = (string)$userInfo['response']->{'avatar-url'}[0];
	    $hotiDefaultUsers[$newUsername][0] = $newUsername;
	    $hotiDefaultUsers[$newUsername][1] = $newUsernameImg;
	}
	$hotiDefaultSettings = array(
                                        false,
                                        true,
					true
	);
	$hotiWitdhDefaultSettings = array(
                                       "type" => "custom",
                                       "wp" => "medium",
                                       "custom" => "100%"                
	);
	//Either use previous settings from version prior to 1.0.7 or use defaults is first time install
	$args = array(
	    'hoti_users' => $hotiDefaultUsers,
	    'hoti_active_user' => $hotiDefaultUser,
	    'hoti_settings' => (get_option('hoti_settings')) ? get_option('hoti_settings') : $hotiDefaultSettings,
	    'hoti_playerType' => (get_option('hoti_playerType')) ? get_option('hoti_playerType') : 'html5',
	    'hoti_width_settings' => (get_option('hoti_width_settings')) ? get_option('hoti_width_settings') : $hotiWitdhDefaultSettings,
	    'hoti_classes' => (get_option('hoti_classes')) ? get_option('hoti_classes') : '',
	    'hoti_color' => (get_option('hoti_color')) ? get_option('hoti_color') : 'ff7700'
		      );
	//Update with old/default values
	update_option('hoti_options', $args);
	//Delete old entries in db
	delete_option("hoti_user");
	delete_option("hoti_settings");
	delete_option("hoti_playerType");
	delete_option("hoti_width_settings");
	delete_option("hoti_classes");
	delete_option("hoti_color");
    }
}
// Delete options table entries ONLY when plugin deactivated AND deleted
register_uninstall_hook(__FILE__, 'hoti_delete_plugin_options');
function hoti_delete_plugin_options() {
	delete_option("hoti_options");
}
/*** Options Output ***/
function hoti_options(){
    $options = get_option('hoti_options');
    //printl($options);
    $hotiActiveUser = isset($options['hoti_active_user']) ? $options['hoti_active_user'] : '';
    $hotiUsers = isset($options['hoti_users']) ? $options['hoti_users'] : '';
    $hotiSettings = isset($options['hoti_settings']) ? $options['hoti_settings'] : '';
    $hotiPlayerType = isset($options['hoti_playerType']) ? $options['hoti_playerType'] : '';
    $hotiPlayerTypeDefault = empty($hotiPlayerType) ? TRUE : FALSE;
    $hotiWidthSettings = isset($options['hoti_width_settings']) ? $options['hoti_width_settings'] : '';
    $hotiClasses = isset($options['hoti_classes']) ? $options['hoti_classes'] : '';
    $hotiColor = isset($options['hoti_color']) ? $options['hoti_color'] : ''; 
    
    $hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiActiveUser.'/tracks.xml?limit=1&client_id=43195eb2f2b85520cb5f65e78d6501bf';
    $hotiApiResponse = get_hoti_api_response($hotiApiCall);
    if(isset($hotiApiResponse['response']) && $hotiApiResponse['response']){
	foreach($hotiApiResponse['response'] as $soundcloudMMLatestTrack){
	    $soundcouldMMId = (string)$soundcloudMMLatestTrack->id;
	}
    }
    $soundcouldMMShortcode = '[soundcloud id='.$soundcouldMMId.']';

?>
    
    <script type="text/javascript">
	//Set default Hotï Player Settings
        <?php get_hoti_default_settings_for_js(); ?>
    </script>
    
    <div class="soundcloudMMWrapper soundcloudMMOptions soundcloudMMMainWrapper">
        <div id="soundcloudMMTop" class="darkGreyGradient">
            <a id="soundcloudMMLogo" class="orangeGradient" href="http://www.soundcloud.com" title="visit SoundCloud website"><img src="<?php echo SIG_PLUGIN_DIR ?>/images/soundcloud-logo-sc.png" width="107" height="71" alt="Soundcloud Logo"/></a>
            <a id="soundcloudMMHeader" class="mediumGreyGradient textShadow" href="http://www.mightymess.com/wp-hoti-player-wordpress-plugin" alt="Visit Mighty Mess for more cool stuff">
                <span class="soundcloudMMTitle">Hotï Player <small>by Thomas Michalak</small></span>
                <span class="soundcloudMMUrl">www.mightymess.com/wp-hoti-player-wordpress-plugin</span>
            </a>
	    <p id="soundcloudMMVersion">version <?php echo get_hoti_version($options) ?></p>
        </div>
        
        <div id="soundcloudMMMain" class="lightBlueGradient">
            <form method="post" action="options.php" id="soundcloudMMMainForm" name="soundcloudMMMainForm" class="">
	    <p class="hidden soundcloudMMId" id="soundcloudMMId-<?php echo $soundcouldMMId ?>"><?php echo $soundcouldMMId ?></p>
            <?php settings_fields('hoti_options'); ?>
                <ul id="soundcloudMMSettings">
                    <!-- Username -->
		    <li class="soundcloudMMBox"><label class="optionLabel">User Name</label>
			<?php get_hoti_username_interface($options, $hotiUsers) ?>
		    </li>
		    <!-- Default Settings -->
                    <li class="soundcloudMMBox"><label class="optionLabel">Default Settings</label>
                        <ul class="subSettings checkboxes">
                            <li><input type="checkbox" <?php echo (isset($hotiSettings[0]) && $hotiSettings[0]) ? 'checked="checked"' : ''?> name="hoti_options[hoti_settings][0]" value="true" class="soundcloudMMAutoPlay" id="soundcloudMMAutoPlay"/><label for="soundcloudMMAutoPlay">Play Automatically</label></li>
                            <li><input type="checkbox" <?php echo (isset($hotiSettings[1]) && $hotiSettings[1]) ? 'checked="checked"' : ''?> name="hoti_options[hoti_settings][1]" value="true" class="soundcloudMMShowComments" id="soundcloudMMShowComments"/><label for="soundcloudMMShowComments">Show comments <small>(Standard and Artwork player)</small></label></li>
			    <li><input type="checkbox" <?php echo (isset($hotiSettings[2]) && $hotiSettings[2]) ? 'checked="checked"' : ''?> name="hoti_options[hoti_settings][2]" value="true" class="soundcloudMMShowArtwork" id="soundcloudMMShowArtwork"/><label for="soundcloudMMShowArtwork">Show Artwork <small>(html5 player)</small></label></li>
                        </ul>
                    </li>
		    <!-- Player Type -->
                    <li class="soundcloudMMBox"><label class="optionLabel">Default Player Type</label>
                        <ul class="subSettings radios">
                            <?php
                            foreach(get_hoti_player_types() as $type) : ?>
                                <li><input name="hoti_options[hoti_playerType]" id="<?php echo $type ?>" class="soundcloudMMPlayerType" type="radio" value="<?php echo $type ?>" <?php if($hotiPlayerTypeDefault && $type == 'Standard') echo 'checked="checked"'; else echo ($hotiPlayerType === $type) ? 'checked="checked"' : '' ?> /><label for="<?php echo $type ?>"><?php echo $type; if($type == 'Artwork') echo ' <small>(not available on free soundcloud account)</small>'; if($type == 'html5') echo ' <small>new! (beta)</small>' ?></label></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
		    <!-- Width -->
                    <li class="soundcloudMMBox"><label class="optionLabel">Default Width</label>
                        <ul id="soundcloudMMWidthSetting" class="subSettings texts">
                            <li>
                                <input name="hoti_options[hoti_width_settings][type]" <?php echo ($hotiWidthSettings['type'] == "wp") ? 'checked="checked"' : ''; ?> id="soundcloudMMWpWidth" value="wp" type="radio" class="soundcloudMMWpWidth soundcloudMMWidthType radio"/><label for="soundcloudMMWpWidth">Media Width</label>
                                <select class="soundcloudMMInput soundcloudMMWidth" name="hoti_options[hoti_width_settings][wp]">
                                <?php foreach(get_hoti_wordpress_sizes() as $key => $hotiMediaSize) : ?>
                                    <?php
                                    //First Time, then Other Times
                                    if($hotiWidthSettings['wp'] == 'medium') $hotiMediaSelected = ($key == $hotiWidthSettings['wp']) ? 'selected="selected"' : '';  
                                    else $hotiMediaSelected = ($hotiMediaSize[0] == $hotiWidthSettings['wp']) ? 'selected="selected"' : ''; ?>
                                    <option <?php echo $hotiMediaSelected ?> value="<?php echo $hotiMediaSize[0]?>" class="soundcloudMMWpSelectedWidth"><?php echo $key.': '.$hotiMediaSize[0]?></option>
                                <?php endforeach; ?>
                                </select>
                            </li>
                            <li>
                                <input name="hoti_options[hoti_width_settings][type]" <?php echo ($hotiWidthSettings['type'] == "custom") ? 'checked="checked"' : ''; ?> id="soundcloudMMCustomWidth" value="custom" type="radio" class="soundcloudMMCustomWidth soundcloudMMWidthType radio"/><label for="soundcloudMMCustomWidth">Custom Width</label>
                                <input name="hoti_options[hoti_width_settings][custom]" id="soundcloudMMCustomSelectedWidth" class="soundcloudMMInput soundcloudMMWidth soundcloudMMCustomSelectedWidth" type="text" name="hoti_options[soundcloudMMCustomSelectedWidth]" value="<?php echo $hotiWidthSettings['custom'] ?>" />
                            </li>
                        </ul>
                    </li>
		    <!-- Color and Classes -->
                    <li class="soundcloudMMBox"><label class="optionLabel">Extras</label>
                        <ul class="subSettings texts">
                            <li>
                                <label>Color</label>
                                <div class="soundcloudMMColorPickerContainer" id="soundcloudMMColorPickerContainer">
                                    <input type="text" class="soundcloudMMInput soundcloudMMColor" id="soundcloudMMColor" name="hoti_options[hoti_color]" value="<?php echo $hotiColor ?>" style="background-color:<?php echo $hotiColor ?>"/><a href="#" class="soundcloudMMBt soundcloudMMBtSmall inline blue soundcloudMMRounder soundcloudMMResetColor">reset to default</a>
                                    <div id="soundcloudMMColorPicker" class="shadow soundcloudMMColorPicker"><div id="soundcloudMMColorPickerSelect" class="soundcloudMMColorPickerSelect"></div><a id="soundcloudMMColorPickerClose" class="blue soundcloudMMBt soundcloudMMColorPickerClose">done</a></div>
                                </div>
                            </li>
                            <li class="clear">
                                <label>Classes <small>(no commas)</small></label><input class="soundcloudMMInput soundcloudMMClasses" type="text" name="hoti_options[hoti_classes]" value="<?php echo $hotiClasses ?>" />
                            </li>
                        </ul>
                    </li>
		    <!-- Advance Options -->
		    <!-- <li class="hidden soundcloudMMBox"><label class="optionLabel">Advanced Options</label>
			<?php //hoti_advanced_options() ?>
		    </li> -->
		    <!-- Preview -->
                    <li class="soundcloudMMBox"><label class="optionLabel previewLabel">Live Preview <small>(your latest track)</small></label>
                        <?php if($hotiApiResponse['response']) :?>
                        <p class="soundcloudMMEmbed soundcloudMMEmbedOptions" style="text-align:center;">
			    <!-- Soundcloud Preview here -->
			</p>
                        <p class="soundcloudMMLoading soundcloudMMPreviewLoading" style="display:none"></p>
                        <?php else : ?>
                        <!-- Error getting XML -->
                        <div class="soundcloudMMXmlError"><p><?php echo $hotiApiResponse['error'] ? $hotiApiResponse['error'] : "Oups! There's been a error while getting the tracks from soundcloud. Please reload the page."?></p></div>
                        <?php endif; ?>
                    </li>
                </ul>
		<!-- Submit -->
                <p id="soundcloudMMSubmit"><input type="submit" name="Submit" value="<?php _e('Save Your SoundCloud Settings') ?>" class="soundcloudMMBt blue"/></p>
	    </form>
        </div>
            <ul id="soundcloudMMExtras" class="lightGreyGradient">
                <li><a href="http://soundcloud.com/t-m" title="TM's music on SoundCloud" class="soundcloudMMBt orangeGradient soundcloudMMRounder">TM on SoundCloud</a></li>
                <li><a href="http://www.mightymess.com" title="Thomas Michalak's Website" class="soundcloudMMBt orangeGradient soundcloudMMRounder">More Mighty Mess</a></li>
                <li><a href="http://wordpress.org/tags/wp-hoti-player?forum_id=10" title="Hotï Player Forum" class="soundcloudMMBt orangeGradient soundcloudMMRounder">Forum</a></li>
                <li>
                <form class="soundcloudMMBtForm" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="hosted_button_id" value="9VGA6PYQWETGY">
                        <input type="submit" name="submit" value="Donate via PayPal" class="soundcloudMMBt darkGrey soundcloudMMRounder" alt="PayPal - The safer, easier way to pay online.">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
                    </form>
                </li>
            </ul>
        <p id="disclaimer">SoundCloud and SoundCloud Logo are trademarks of SoundCloud Ltd.</p>
    </div>
    
    <?php
}


?>