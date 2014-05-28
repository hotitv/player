<?php
/*
    hoti™ - Venezuelan Artistic Material
    Copyright (C) 2013-2014 Marcos Colina
    GNU General Public License
	
	Contributors
		Marcos Colina <ceo@hoti.tv>
		Alexander Salas <a.salas@ieee.org>
*/
/*********************************************************************/
/***                                                               ***/
/***                     SOUNDCLOUD UTILITIES                      ***/
/***                                                               ***/
/*********************************************************************/
    
require_once 'includes/Mobile_Detect.php';


function get_hoti_player_types(){
    $m = array('Mini', 'Standard', 'Artwork', 'html5');
    return $m;
}
function get_hoti_wordpress_sizes(){
    $px = "px";
    $hotiWordpressSizes = array(
                                                "thumbnail" => array(
                                                                    get_option( 'thumbnail_size_w' ).$px,
                                                                    get_option( 'thumbnail_size_h' ).$px
                                                                    ),
                                                "medium" => array(
                                                                get_option( 'medium_size_w' ).$px,
                                                                get_option( 'medium_size_w' ).$px
                                                                ),
                                                "large" => array(
                                                                get_option( 'large_size_w' ).$px,
                                                                get_option( 'large_size_w' ).$px
                                                                )
                                              );
    return $hotiWordpressSizes;
}
function get_hoti_default_width($settings){
    return $settings[$settings['type']];
}
function get_hoti_default_settings_for_js(){
	$options = get_option('hoti_options');
	//printl($options);
	$hotiActiveUser = isset($options['hoti_active_user']) ? $options['hoti_active_user'] : '';
	$hotiSettings = isset($options['hoti_settings']) ? $options['hoti_settings'] : '';
	$hotiPlayerType = isset($options['hoti_playerType']) ? $options['hoti_playerType'] : '';
	$hotiWidthSettings = isset($options['hoti_width_settings']) ? $options['hoti_width_settings'] : '';
	$hotiClasses = isset($options['hoti_classes']) ? $options['hoti_classes'] : '';
	$hotiColor = isset($options['hoti_color']) ? $options['hoti_color'] : ''; 
	
	echo 'hotiUser_default = "'.$hotiActiveUser.'"; ';
	echo 'hotiAutoPlay_default = '.((!isset($hotiSettings[0]) || $hotiSettings[0] == '') ? 'false' : 'true') .'; ';
	echo 'hotiComments_default = '.((!isset($hotiSettings[1]) || $hotiSettings[1] == '') ? 'false' : 'true') .'; ';
	echo 'hotiArtwork_default = '.((!isset($hotiSettings[2]) || $hotiSettings[2] == '') ? 'false' : 'true') .'; ';
	echo 'hotiPlayerType_default = "'.$hotiPlayerType.'"; ';
	echo 'hotiWidth_default = "'.get_hoti_default_width($hotiWidthSettings).'"; ';
	echo 'hotiClasses_default = "'.$hotiClasses.'"; ';
	echo 'hotiColor_default = "'.$hotiColor.'"; ';
}
function get_hotiUserNumber(){
	$options = get_option('hoti_options');
	$hotiActiveUser = isset($options['hoti_active_user']) ? $options['hoti_active_user'] : '';
	
	$hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiActiveUser.'.xml?client_id=43195eb2f2b85520cb5f65e78d6501bf';
	$hotiApiResponse = get_hoti_api_response($hotiApiCall);
	$result['tracks'] = ($hotiApiResponse['response']->{'track-count'} == 0) ? '0' : $hotiApiResponse['response']->{'track-count'};
	$result['sets'] = ($hotiApiResponse['response']->{'playlist-count'} == 0) ? '0' : $hotiApiResponse['response']->{'playlist-count'};
	$result['favorites'] = ($hotiApiResponse['response']->{'public-favorites-count'} == 0) ? '0' : $hotiApiResponse['response']->{'public-favorites-count'};
	return $result;
}
function get_hoti_username_interface($options, $hotiUsers){
	?>
	<!-- Active User -->
			<ul id="hotiActiveUserContainer">
			    <li class="hotiUserContainer" style="background-image:URL('<?php echo $options['hoti_users'][$options['hoti_active_user']][1] ?>')">
				<span id="hotiActiveLabel">&nbsp;</span>
				<div>
				    <span class="hotiRemoveUser" />&nbsp;</span>
				    <input type="hidden" value="<?php echo $options['hoti_users'][$options['hoti_active_user']][0]?>" name="hoti_options[hoti_users][<?php echo $options['hoti_active_user'] ?>][0]" />
				    <input type="hidden" value="<?php echo $options['hoti_users'][$options['hoti_active_user']][1]?>" name="hoti_options[hoti_users][<?php echo $options['hoti_active_user'] ?>][1]" />
				    <p><?php echo $options['hoti_active_user'] ?></p>
				</div>
			    </li>
			    <li class="hidden">
				<input type="hidden" id="hotiActiveUser" value="<?php echo $options['hoti_active_user'] ?>" name="hoti_options[hoti_active_user]" />
			    </li>
			</ul>
			<!-- Add user -->
			<ul id="hotiAddUserContainer">
				<li class="soundcloudMMLoading" style="display:none">&nbsp;</li>
				<li id="hotiUserError" class="orangeGradient soundcloudMMRounder">
					<p>error message</p>
					<a href="#" class="soundcloudMMBt soundcloudMMBtSmall blue soundcloudMMRounder ">close</a>
				</li>
				<li>
					<input type="text" name="hotiNewUser" id="hotiNewUser"/>
					<a id="hotiAddUser" href="#" class="soundcloudMMBt blue soundcloudMMRounder soundcloudMMBtSmall" />Add Username</a>
				</li>
			</ul>
			<!-- All inactive Users -->
			<div id="hotiUsernameCarouselWrapper">
			    <ul id="hotiUsernameCarousel">
				<?php foreach($hotiUsers as $key => $user): ?>
				    <?php if($user[0] != $options['hoti_active_user']) :?>
				    <li class="hotiUserContainer"  style="background-image:URL('<?php echo $user[1] ?>')">
					<span class="hotiRemoveUser" />&nbsp;</span>
					<div>
					    <input type="hidden" value="<?php echo $user[0]?>" name="hoti_options[hoti_users][<?php echo $key ?>][0]" />
					    <input type="hidden" value="<?php echo $user[1]?>" name="hoti_options[hoti_users][<?php echo $key ?>][1]" />
					    <p><?php echo $user[0] ?></p>
					</div>
				    </li>
				<?php endif; endforeach; ?>
			    </ul>
			    <div id="hotiUsernameCarouselNav"></div>
			</div>
	<?php
}

/**
 * Get User's Latest track
 * $hotiApiCall: API request (url)
 **/
function get_hoti_latest_track_id($hotiUser, $format = "tracks"){
	$soundcouldMMId = "";
	if($format == "tracks") $hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiUser.'/tracks.xml?limit=1&client_id=43195eb2f2b85520cb5f65e78d6501bf';
	if($format == "sets") $hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiUser.'/playlists.xml?limit=1&client_id=43195eb2f2b85520cb5f65e78d6501bf';
	if($format == "favorites") $hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiUser.'/favorites.xml?limit=1&client_id=43195eb2f2b85520cb5f65e78d6501bf';
	
	$hotiApiResponse = get_hoti_api_response($hotiApiCall);
	if(isset($hotiApiResponse['response']) && $hotiApiResponse['response']){
	    foreach($hotiApiResponse['response'] as $soundcloudMMLatestTrack){
		$soundcouldMMId = (string)$soundcloudMMLatestTrack->id;
	    }
	}
	return $soundcouldMMId;
}

/**
 * Get User's Latest track
 * $hotiApiCall: API request (url)
 **/
function get_hoti_multiple_tracks_id($hotiUser, $nbr = 1, $random = false, $format){
	//Get all tracks if random
	$getNbr = $nbr;
	if($random) $getNbr = 50;
	$soundcouldMMIds= array();

	if($format == 'tracks') $hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiUser.'/tracks.xml?limit='.$getNbr.'&client_id=43195eb2f2b85520cb5f65e78d6501bf';
	if($format == 'sets') $hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiUser.'/playlists.xml?limit='.$getNbr.'&client_id=43195eb2f2b85520cb5f65e78d6501bf';
	if($format == 'favorites') $hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiUser.'/favorites.xml?limit='.$getNbr.'&client_id=43195eb2f2b85520cb5f65e78d6501bf';
	
	
	$hotiApiResponse = get_hoti_api_response($hotiApiCall);
	
	if(isset($hotiApiResponse['response']) && $hotiApiResponse['response']){
	    foreach($hotiApiResponse['response'] as $soundcloudMMLatestTrack){
		$soundcouldMMIds[] .= (string)$soundcloudMMLatestTrack->id;
	    }
	}
	if($random) return array_random($soundcouldMMIds, $nbr);	
	return $soundcouldMMIds;
}

/**
 * Get Soundcloud API Response
 * $hotiApiCall: API request (url)
 **/
function get_hoti_api_response($hotiApiCall){
	//Set Error default message && default XML state
	$hotiRespError = false;
	$hotiResp = false;
	//Check is cURL extension is loaded
	if(extension_loaded("curl")){
		// create a new cURL resource
		$hotiCURL = curl_init();
		//Set cURL Options
		curl_setopt($hotiCURL, CURLOPT_URL, $hotiApiCall);
		curl_setopt($hotiCURL, CURLOPT_RETURNTRANSFER, true);//return a string
		curl_setopt($hotiCURL, CURLOPT_USERAGENT, "user_agent : FOOBAR");
		// Get XML as a string
		$hotiXmlString = curl_exec($hotiCURL);
		//Check for cURL errors
		if($hotiXmlString === false) $hotiRespError = 'Curl error: ' . curl_error($hotiCURL);
		//No cURL Errors: Load the call and captured xml returned by the API
		else $hotiResp = simplexml_load_string($hotiXmlString);
		// close cURL resource, and free up system resources
		curl_close($hotiCURL);
	}
	//No cURL: Try loading the XML directly with simple_xml_load_file
	else $hotiResp = simplexml_load_file($hotiApiCall);

	//Add response and error to array
	$hotiResponseArray = array('response' => $hotiResp, 'error' => $hotiRespError);
	return $hotiResponseArray;
}
/*Pagination
hoti_pagination($totalItems, $currentPage, $perPage)
*/
function hoti_pagination($format, $totalItems, $currentPage, $perPage, $post_ID){
	
	// The items on the current page.
	$offset = ($currentPage - 1) * $perPage;
	$firstItem = $offset + 1;
	$lastItem = $offset + $perPage < $totalItems ? $offset + $perPage : $totalItems;
	
	// Some useful variables for making links.
	$firstPage = 1;
	$lastPage = ceil($totalItems / $perPage);
	$prevPage = $currentPage - 1 > 0 ? $currentPage - 1 : 1;
	$nextPage = $currentPage + 1 > $lastPage ? $lastPage : $currentPage + 1;
	
	$disableFirst = ($currentPage == $firstPage) ? ' disabled' : '';
	$disableLast = ($currentPage == $lastPage) ? ' disabled' : '';
	
	$output = '<div class="tablenav-pages"><span class="displaying-num">'.$totalItems.' tracks</span>';
	$output .= '<span class="pagination-links">';
	$output .= '<a href="?post_id='.$post_ID.'&tab=hoti&selectFormat='.$format.'&paged='.$firstPage.'&TB_iframe=1&width=640&height=584" title="Go to the first page" class="first-page'.$disableFirst.'">&laquo;</a>';
	$output .= '<a href="?post_id='.$post_ID.'&tab=hoti&selectFormat='.$format.'&paged='.$prevPage.'&TB_iframe=1&width=640&height=584" title="Go to the previous page" class="prev-page'.$disableFirst.'">&lsaquo;</a>';
	$output .= '<span class="paging-input">page '.$currentPage.' of <span class="total-pages">'.$lastPage.'</span></span>';
	$output .= '<a href="?post_id='.$post_ID.'&tab=hoti&selectFormat='.$format.'&paged='.$nextPage.'&TB_iframe=1&width=640&height=584" title="Go to the next page" class="next-page'.$disableLast.'">&rsaquo;</a>';
	$output .= '<a href="?post_id='.$post_ID.'&tab=hoti&selectFormat='.$format.'&paged='.$lastPage.'&TB_iframe=1&width=640&height=584" title="Go to the last page" class="last-page'.$disableLast.'">&raquo;</a>';
	$output .= '</span></div>';
	
	return $output;
}
/*Select Tracks / Favorites / Sets
*/
function hoti_select_tracks_favs_sets($selectedFormat, $hotiNumbers, $post_ID){
	$formats = array('tracks', 'sets', 'favorites');
	$output = '<ul id="soundcloudMMSelectTracksFavsSets" class="subsubsub">';
	foreach($formats as $key => $format){
		$current = ($format == $selectedFormat) ? 'current' : '';
		$seperator = ($key != 0) ? ' | ' : ' ';
		$output .= $seperator.'<li><a href="?post_id='.$post_ID.'&tab=hoti&selectFormat='.$format.'&paged=1" class="'.$current.'">'.$format.' <span class="count">('.$hotiNumbers[$format].')</span></a></li>';
	}
	$output .= '</ul>';
	return $output;
}

/*Add Hotï Player Plugin to TinyMce*/
function hoti_mce_plugin($plugin_array) {
    $plugin_array['hoti']  =  SIG_PLUGIN_DIR.'tinymce-plugin/wp-hoti-player-editor_plugin.js';
    return $plugin_array;
}
function hoti_mce_button( $buttons ) {
	// add a separation before our button, here our button's id is "mygallery_button"
	array_push( $buttons, '|', 'hotibtns' );
	return $buttons;
}
function hoti_mce_css($mce_css) {
  if (! empty($mce_css)) $mce_css .= ',';
  $mce_css .= SIG_PLUGIN_DIR.'/tinymce-plugin/wp-hoti-player-editor_plugin.css';
  return $mce_css; 
}

/* Random Values from Array */
function array_random($arr, $num = 1) {
    shuffle($arr);
    //check if requested number is bigger than array length
    if(count($arr) < $num){
	$tempArray = $arr;
	$repeat = ceil($num/count($arr));
	for($i=0; $i<$repeat; $i++){
		$arr = array_merge($arr, $tempArray);
	}
    }
    $r = array();
    for ($i = 0; $i < $num; $i++) {
        $r[] = $arr[$i];
    }
   // return $num == 1 ? $r[0] : $r;
   return $r;
}

/* Debug */
if(!function_exists('printl')){
	function printl($val){
		printf("<pre>%s</pre>", print_r($val, true));
	}
}

/*********************************************************************/
/***                                                               ***/
/***                   SOUNDCLOUD MEDIA UPLOAD TAB                 ***/
/***                                                               ***/
/*********************************************************************/
/* Add a new Tab */
function hoti_media_upload_tab($tabs) {
	$newtab = array('hoti' => __('Hotï Player', 'hoti'));
	return array_merge($tabs, $newtab);
}
add_filter('media_upload_tabs', 'hoti_media_upload_tab');

/* Add Scripts and Styles to New Tab **/
add_action('admin_print_scripts-media-upload-popup', 'hoti_option_scripts', 2000);
add_action('admin_print_styles-media-upload-popup', 'hoti_option_styles', 2000);
	
	
/* Hotï Player Tab (Iframe) content*/
function media_hoti_process() {
	media_upload_header();
	get_hoti_user_tracks();
}
/* load Iframe in the tab page */
function hoti_media_menu_handle() {
    return wp_iframe( 'media_hoti_process');
}
add_action('media_upload_hoti', 'hoti_media_menu_handle');


/*Add Soundcloud Button to Upload/Insert*/
function plugin_media_button($context) {
	global $post_ID;
	$plugin_media_button = ' %s' . '<a id="add_hoti" title="Insert Soundcloud Player" href="media-upload.php?post_id='.$post_ID.'&tab=hoti&selectFormat=tracks&paged=1&TB_iframe=1&width=640&height=584" class="thickbox"><img alt="Insert Soundcloud Player" src="'.SIG_PLUGIN_DIR.'images/wp-hoti-player-icon.png"></a>';
	return sprintf($context, $plugin_media_button);
  }
add_filter('media_buttons_context', 'plugin_media_button');
  
/** Populate the new Hotï Player Tab **/
function get_hoti_user_tracks(){
	//Default Settings
	$options = get_option('hoti_options');
	//printl($options);
	$hotiActiveUser = isset($options['hoti_active_user']) ? $options['hoti_active_user'] : '';
	$hotiUsers = isset($options['hoti_users']) ? $options['hoti_users'] : '';
	$hotiSettings = isset($options['hoti_settings']) ? $options['hoti_settings'] : '';
	$hotiPlayerType = isset($options['hoti_playerType']) ? $options['hoti_playerType'] : '';
	$hotiPlayerTypeDefault = empty($hotiPlayerType) ? TRUE : FALSE;
	$hotiWidthSettings = isset($options['hoti_width_settings']) ? $options['hoti_width_settings'] : '';
	$hotiWidth = get_hoti_default_width($hotiWidthSettings);
	$hotiClasses = isset($options['hoti_classes']) ? $options['hoti_classes'] : '';
	$hotiColor = isset($options['hoti_color']) ? $options['hoti_color'] : ''; 
    
	//Default Pagination Settings
	$hotiTracksPerPage = 25;
	$hotiPage = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : '1';
	$post_id = $_REQUEST['post_id'];
	$hotiApiOffset = $hotiTracksPerPage*($hotiPage-1);
	
	//API Call
	$hotiSelectedFormat = isset($_REQUEST['selectFormat']) ? $_REQUEST['selectFormat'] : 'tracks';
	if($hotiSelectedFormat == 'tracks') $hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiActiveUser.'/tracks.xml?limit='.$hotiTracksPerPage.'&offset='.$hotiApiOffset.'&client_id=43195eb2f2b85520cb5f65e78d6501bf';
	if($hotiSelectedFormat == 'sets') $hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiActiveUser.'/playlists.xml?limit='.$hotiTracksPerPage.'&offset='.$hotiApiOffset.'&client_id=43195eb2f2b85520cb5f65e78d6501bf';
	if($hotiSelectedFormat == 'favorites') $hotiApiCall = 'http://api.soundcloud.com/users/'.$hotiActiveUser.'/favorites.xml?limit='.$hotiTracksPerPage.'&offset='.$hotiApiOffset.'&client_id=43195eb2f2b85520cb5f65e78d6501bf';
	$hotiApiResponse = get_hoti_api_response($hotiApiCall);
	
	//Pagination and Actions
	$hotiNumbers = get_hotiUserNumber($hotiSelectedFormat);
	$hotiPagination = hoti_pagination($hotiSelectedFormat, $hotiNumbers[$hotiSelectedFormat], $hotiPage, $hotiTracksPerPage, $post_id);
	$hotiSelectTracksFavsSets = hoti_select_tracks_favs_sets($hotiSelectedFormat, $hotiNumbers, $post_id);
	
	//Usernames
	echo '<div class="soundcloudMMWrapper">';
		echo '<div id="soundcloudMMUsernameHeader"><img src="'.$hotiUsers[$hotiActiveUser][1].'" width="50" height="50"/><span>'.$hotiUsers[$hotiActiveUser][0].'</span> <a href="#" id="soundcloudMMShowUsernames">show users options</a><a href="#" id="soundcloudMMHideUsernames" class="hidden">hide users options</a></div>';
		echo '<div id="soundcloudMMUsermameTab">';
		get_hoti_username_interface($options, $hotiUsers);
	echo '</div></div>';
	
	echo '<div id="soundcloudMMTabActions" class="tablenav">';
		//Select Tracks / Sets / Favs
		echo (isset($hotiSelectTracksFavsSets)) ? $hotiSelectTracksFavsSets : '';
		//Pagination
		echo (isset($hotiPagination)) ? $hotiPagination : '';
	echo '</div>';
	
	//Sorting Menu
	echo '<form id="library-form" class="media-upload-form validate" action="" method="post" enctype="multipart/form-data"><div id="media-items" class="media-items-'.$post_id.'">';
	?>
	
	<script type="text/javascript">
	<!--
    jQuery.noConflict();
	(function($){
	$(function() {	
		var preloaded = $(".media-item.preloaded");
		if ( preloaded.length > 0 ) {
			preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
			//updateMediaForm();
		}
});
})(jQuery);
	-->
	
	//Set default Hotï Player Settings
	<?php get_hoti_default_settings_for_js(); ?>
		
		
	</script>
	
	<?php
	if (isset($hotiApiResponse['response']) && $hotiApiResponse['response']) {
			foreach($hotiApiResponse['response'] as $hotitrack): ?>
			
				<div class="media-item preloaded" id="media-item-<?php echo $hotitrack->id ?>">
					<a href="#" class="toggle describe-toggle-on soundcloud" id="show-<?php echo $hotitrack->id ?>">Show</a>
					<a href="#" class="toggle describe-toggle-off soundcloudMM">Hide</a>
					<div class="filename new"><span class="title soundcloudMMTitle" id="soundcloudMMTitle-<?php echo $hotitrack->id ?>"><?php echo $hotitrack->title ?></span></div>
					<table class="slidetoggle describe startclosed soundcloudMMWrapper soundcloudMMMainWrapper <?php echo $hotiSelectedFormat ?>">
						<thead id="media-head-<?php echo $hotitrack->id ?>" class="media-item-info">
							<tr valign="top">
								<td id="thumbnail-head-<?php echo $hotitrack->id ?>" class="A1B1">
									<p><a href="<?php echo $hotitrack->{'permalink-url'}?>" title="Go to the Soundcloud page" target="_blank"><img id="soundcloudMMThumb-<?php echo $hotitrack->id ?>" style="margin-top: 3px;" alt="" src="<?php echo ($hotitrack->{'artwork-url'} != '') ? $hotitrack->{'artwork-url'} : SIG_PLUGIN_DIR."images/noThumbnail.gif" ?>" class="thumbnail"></a></p>
								</td>
								<td>
								<p><strong>Title:</strong> <?php echo $hotitrack->title ?></p>
								<p id="soundcloudMMId-<?php echo $hotitrack->id ?>" class="soundcloudMMId"><strong>id:</strong> <?php echo $hotitrack->id ?></p>
								<p><strong>Upload date:</strong> <?php echo $hotitrack->{'created-at'} ?></p>
								<p><strong>Duration:</strong> <span id="media-dims-<?php echo $hotitrack->id ?>"><?php echo $hotitrack->duration ?></span></p>
								<p><strong>Url:</strong> <a id="videoUrl-<?php echo $hotitrack->id ?>" href="<?php echo $hotitrack->{'permalink-url'} ?>" title="Go to the video page" target="_blank"><?php echo $hotitrack->{'permalink-url'}?></a></p>
								</td>
								<td>
								<tbody>
									<tr class="soundcloudMM_description">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Description</span><br class="clear"></label></th>
										<td class="field">
											<p class="text soundcloudMMDescription" id="soundcloudMMDescription-<?php echo $hotitrack->id ?>"><?php echo $hotitrack->description ?></p>
										</td>
									</tr>
									<tr class="soundcloudMM_settings">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Settings</span><br class="clear"></label></th>
										<td class="field">
											<input type="checkbox" <?php echo ($hotiSettings[0]) ? 'checked="checked"' : '' ?> id="soundcloudMMAutoPlay-<?php echo $hotitrack->id ?>" class="text soundcloudMMAutoPlay">
											<label >Play Automaticly</label>
											<input type="checkbox" <?php echo ($hotiSettings[1]) ? 'checked="checked"' : '' ?> id="soundcloudMMShowComments-<?php echo $hotitrack->id ?>" class="text soundcloudMMShowComments">
											<label >Show comments <small>(standard and artwork player)</small></label>
											<input type="checkbox" <?php echo ($hotiSettings[2]) ? 'checked="checked"' : '' ?> id="soundcloudMMShowArtwork-<?php echo $hotitrack->id ?>" class="text soundcloudMMShowArtwork">
											<label >Show artwork <small>(html5 player)</small></label>
											<!-- <input type="text" class="soundcloudPlayercolor" value""/> -->
											
										</td>
									</tr>
									<tr class="soundcloudMM_playerType">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Player Type</span><br class="clear"></label></th>
										<td class="field">
											<?php foreach(get_hoti_player_types() as $type) :?>
											<input type="radio" id="soundcloudMMMiniPlayer-<?php echo $hotitrack->id ?>" value="<?php echo $type ?>" name="soundcloudMMPlayerType-<?php echo $hotitrack->id ?>" class="text soundcloudMMPlayerType" <?php echo ($hotiPlayerType === $type) ? 'checked="checked"' : '' ?>>
											<label><?php echo $type; if($type == 'Artwork') echo ' <small>(not available on free soundcloud account)</small>' ?></label>
											<?php endforeach; ?>
										</td>
									</tr>
									<tr class="soundcloudMM_size">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Width</span><br class="clear"></label></th>
										<td class="field">
											<ul id="soundcloudMMWidthSetting" class="subSettings texts soundcloudMMTabWidthSettings">
												<li>
												    <input name="soundcloudMMWidthType-<?php echo $hotitrack->id ?>" <?php echo ($hotiWidthSettings['type'] == "wp") ? 'checked="checked"' : ''; ?> id="soundcloudMMWpWidth-<?php echo $hotitrack->id ?>" value="wp" type="radio" class="soundcloudMMWpWidth soundcloudMMWidthType"/><label for="soundcloudMMWpWidth-<?php echo $hotitrack->id ?>">Media Width</label>
												    <select class="soundcloudMMInput soundcloudMMWidth" name="hoti_width_settings[wp]">
												    <?php foreach(get_hoti_wordpress_sizes() as $key => $hotiMediaSize) : ?>
													<?php $hotiMediaSelected = ($hotiMediaSize[0] == $hotiWidthSettings['wp']) ? 'selected="selected"' : ''; ?>
													<option <?php echo $hotiMediaSelected ?> value="<?php echo $hotiMediaSize[0]?>" class="soundcloudMMWPSelectedWidth soundcloudMMWidth"><?php echo $key.': '.$hotiMediaSize[0]?></option>
												    <?php endforeach; ?>
												    </select>
												</li>
												<li>
												    <input name="soundcloudMMWidthType-<?php echo $hotitrack->id ?>" <?php echo ($hotiWidthSettings['type'] == "custom") ? 'checked="checked"' : ''; ?> id="soundcloudMMCustomWidth-<?php echo $hotitrack->id ?>" value="custom" type="radio" class="soundcloudMMCustomWidth soundcloudMMWidthType"/><label for="soundcloudMMCustomWidth-<?php echo $hotitrack->id ?>">Custom Width</label>
												    <input name="soundcloudMMCustomSelectedWidth-<?php echo $hotitrack->id ?>" id="soundcloudMMCustomSelectedWidth-<?php echo $hotitrack->id ?>" class="soundcloudMMInput soundcloudMMWidth soundcloudMMCustomSelectedWidth" type="text" value="<?php echo $hotiWidthSettings['custom'] ?>" />
												</li>
											</ul>
										</td>
									</tr>
									<tr class="soundcloudMM_color">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Colour</span><br class="clear"></label></th>
										<td class="field">
											<div class="soundcloudMMColorPickerContainer" id="soundcloudMMColorPickerContainer-<?php echo $hotitrack->id ?>">
												<input type="text" id="soundcloudMMColor-<?php echo $hotitrack->id ?>" class="soundcloudMMColor" name="soundcloudMMColor-<?php echo $hotitrack->id ?>" value="<?php echo $hotiColor ?>" style="background-color:<?php echo $hotiColor ?>"/><a href="#" class="soundcloudMMBt soundcloudMMBtSmall inline blue soundcloudMMRounder soundcloudMMResetColor">reset to default</a>
												<div id="soundcloudMMColorPicker-<?php echo $hotitrack->id ?>" class="shadow soundcloudMMColorPicker" ><div id="soundcloudMMColorPickerSelect-<?php echo $hotitrack->id ?>" class="soundcloudMMColorPickerSelect"></div><a id="soundcloudMMColorPickerClose-<?php echo $hotitrack->id ?>" class="blue soundcloudMMBt soundcloudMMColorPickerClose">done</a></div>
											</div>
										</td>
									</tr>
									<tr class="soundcloudMM_classes">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Extra classes</span><br class="clear"></label></th>
										<td class="field">
											<input type="text" class="text soundcloudMMClasses" id="soundcloudMMClasses-<?php echo $hotitrack->id ?>" value="<?php echo $hotiClasses ?>">
											<p class="help">In case you need extra css classes (seperate with a space, no commas!)</p>
										</td>
									</tr>
									<tr class="soundcloudMM_player">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Preview</span><br class="clear"></label></th>
										<td>
											<p id="soundcloudMMEmbed-<?php echo $hotitrack->id ?>" class="field soundcloudMMEmbed" style="text-align:center">
												<!-- Soundcloud Preview here -->
											</p>
											<p class="soundcloudMMLoading soundcloudMMPreviewLoading" style="display:none"></p>
										</td>
									</tr>
									<tr class="soundcloudMM_shortcode">
										<th valign="top" class="label" scope="row"><label><span class="alignleft">Shortcode</span><br class="clear"></label></th>
										<td class="field">
											<input id="soundcloudMMShortcode-<?php echo $hotitrack->id ?>" type="text" class="text soundcloudMMShortcode" value="[soundcloud <?php echo 'id='.$hotitrack->id ?>']">
										</td>
									</tr>
									 <tr class="soundcloudMM_submit">
										<td></td>
										<td class="savesend">
											<a href="#" id="soundcloudMMInsert-<?php echo $hotitrack->id ?>" class="button soundcloudMMInsert">Insert Soundcloud Player</a>

											<!-- <input type="submit" value="Insert into Post" name="" class="button"> -->
											<!-- <input type="button" id="soundcloudMMAddToGallery-<?php echo $hotitrack->id ?>" value="Add to post's gallery" name="" class="button soundcloudMMAddToGallery">
											<a href="#" id="soundcloudMMFeat-<?php echo $hotitrack->id ?>" class="soundcloudMMFeat">Use as featured Soundcloud</a> -->
											
										</td>
									</tr>
								</tbody>
								</td>
							</tr>
						</thead>
					</table>
				</div>
			<?php endforeach;
		}
		//Error getting XML
		else{
			if($hotiApiResponse['error'] === false) $hotiApiResponse['error'] = 'XML error';
			echo '<div class="soundcloudMMXmlError"><p>Oups! There\'s been a error while getting the tracks from soundcloud. Please reload the page.</p><p class="error">'.$hotiApiResponse['error'].'</p></div>';
		}
	echo '<div id="colorpicker"></div>';
	echo '</div></form>';
}





/******************************************************/
/**                                                  **/
/**                     SHORTCODE                    **/
/**                                                  **/
/******************************************************/
add_shortcode('soundcloud', 'hoti_shortcode');
function hoti_shortcode($atts){
	$options = get_option('hoti_options');
	$hotiSettings = isset($options['hoti_settings']) ? $options['hoti_settings'] : '';
	$hotiPlayerType = isset($options['hoti_playerType']) ? $options['hoti_playerType'] : '';
	$hotiWidthSettings = isset($options['hoti_width_settings']) ? $options['hoti_width_settings'] : '';
	$hotiClasses = isset($options['hoti_classes']) ? $options['hoti_classes'] : '';
	$hotiColor = isset($options['hoti_color']) ? $options['hoti_color'] : ''; 
   
	//Only use lowercase as atts!
	extract( shortcode_atts( array(
					'id' => '1',
					'user' => 'null',
					'autoplay' => ((!isset($hotiSettings[0]) || $hotiSettings[0] == '') ? 'false' : 'true'),
					'comments' => ((!isset($hotiSettings[1]) || $hotiSettings[1] == '') ? 'false' : 'true'),
					'artwork' => ((!isset($hotiSettings[2]) || $hotiSettings[2] == '') ? 'false' : 'true'),
					'width' => get_hoti_default_width($hotiWidthSettings),
					'classes' => $hotiClasses,
					'playertype' => $hotiPlayerType,
					'color' => $hotiColor,
					'format' => 'tracks'
				), $atts )
		);
	return hoti_player($id, $user, $autoplay, $comments, $width, $classes, $playertype, $color, $artwork, $format);
}



/******************************************************/
/**                                                  **/
/**                     OUTPUT                       **/
/**                                                  **/
/******************************************************/


/** The Player **/
function hoti_player($id, $user, $autoPlay, $comments, $width, $classes, $playerTypes, $color, $artwork, $format){
	//XSS Protection on data coming from fields
	//$xssProtection = "/^[A-Za-z0-9 \,]{2,15}$/";
	//if (!preg_match($xssProtection, $width)) $width == NULL;
	//if (!preg_match($xssProtection, $classes)) $classes == NULL;


	$options = get_option('hoti_options');
	$hotiSettings = isset($options['hoti_settings']) ? $options['hoti_settings'] : '';
	$hotiPlayerType = isset($options['hoti_playerType']) ? $options['hoti_playerType'] : '';
	$hotiWidthSettings = isset($options['hoti_width_settings']) ? $options['hoti_width_settings'] : '';
	$hotiClasses = isset($options['hoti_classes']) ? $options['hoti_classes'] : '';
	$hotiColor = isset($options['hoti_color']) ? $options['hoti_color'] : ''; 
   
	//Default values: Needed when not called trough shortode (like in the ajax preview)
	if(!isset($autoPlay)) $autoPlay = ((!isset($hotiSettings[0]) || $hotiSettings[0] == '') ? 'false' : 'true');
	if(!isset($comments)) $comments = ((!isset($hotiSettings[1]) || $hotiSettings[1] == '') ? 'false' : 'true');
	if(!isset($artwork)) $artwork = ((!isset($hotiSettings[2]) || $hotiSettings[2] == '') ? 'false' : 'true');
	if(!isset($width)) $width = get_hoti_default_width($hotiWidthSettings);
	if(!isset($classes)) $classes = $hotiClasses;
	if(!isset($playerTypes)) $playerTypes = $hotiPlayerType;
	if(!isset($color)) $color = $hotiColor;
	if(!isset($format)) $format = 'tracks';
	if($format == 'sets' || $format == 'set') $format = 'playlists';
	$html5Player = false;
	
	$color = str_replace('#', '', $color);
	
	//In case of requesting latest track
	if(isset($user) && $user != "null"){
		$returnedId = get_hoti_latest_track_id($user, $format);
		if($returnedId != "") $id = $returnedId;
	}
	
	if($format == 'favorites') $format = "tracks"; //Reset Favorites to tracks as soundcloud treats them as tracks.
	
	//Player types sizes
	switch($playerTypes){
		case 'Standard':
			$height = ($format == 'tracks') ? '81px' : '165px';
			$playerType = 'standard';
			break;
		case 'Artwork':
			$height = $width;
			$playerType = 'artwork';
			break;
		case 'Mini':
			$height = '18px';
			$playerType = 'tiny';
			break;
		case 'html5':
			$height = ($format == 'tracks') ? '166px' : '450px';
			$html5Player = true;
			break;
	}

    $player = '<div class="hoti '.esc_attr($classes).'" id="soundcloud-'.esc_attr($id).'">';
	$detect = new Mobile_Detect;
	if($detect->isIOS()){
 		//$iOS = 'window.stream.play();';
		$ap = 'false';
	}else{
		$iOS = '';
		$ap = 'true'; 
	}
	//Flash Player
	if(!$html5Player){
		$player .= '<object height="'.esc_attr($height).'" width="'.esc_attr($width).'">';
		$player .= '<param name="movie" value="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2F'.esc_attr($format).'%2F'.$id.'&amp;auto_play='.esc_attr($autoPlay).'&amp;player_type='.esc_attr($playerType).'&amp;show_comments='.esc_attr($comments).'&amp;color='.esc_attr($color).'"></param>';
		$player .= '<param name="allowscriptaccess" value="always"></param>';
		$player .= '<param name="wmode" value="transparent"></param>';
		$player .= '<embed wmode="transparent" allowscriptaccess="always" height="'.esc_attr($height).'" src="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2F'.esc_attr($format).'%2F'.esc_attr($id).'&amp;auto_play='.esc_attr($autoPlay).'&amp;player_type='.esc_attr($playerType).'&amp;show_comments='.esc_attr($comments).'&amp;color='.esc_attr($color).'" type="application/x-shockwave-flash" width="'.esc_attr($width).'"></embed>';
		$player .= '</object>';	
	}
	//Html5 Player
	else{
		$dir = SIG_PLUGIN_DIR.'images/400.jpg';
/* 		$player .= '<iframe width="'.esc_attr($width).'" height="'.esc_attr($height).'" scrolling="no" frameborder="no" src="http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2F'.esc_attr($format).'%2F'.esc_attr($id).'&amp;auto_play='.esc_attr($autoPlay).'&amp;show_artwork='.esc_attr($artwork).'&amp;color='.esc_attr($color).'"></iframe>'; */

/*********************************************************************/
/***                                                               ***/
/***                           Tracks                              ***/
/***                                                               ***/
/*********************************************************************/        
if($format == 'tracks') {
	$player .= <<<MY_MARKER
	<script>
    jQuery.noConflict();
	(function($){
	$(function() {
	var block = false;
	window.addEventListener("load", function load(event){
		window.removeEventListener("load", load, false); 
		SC.get("/tracks/$id", function (track) {
			if(track.downloadable){
				$("#download").addClass('downloadable');
				$("#download").attr('onclick', "window.location.href='"+track.download_url+"?consumer_key=43195eb2f2b85520cb5f65e78d6501bf'");
$("#download").show();
}else{
                $("#download").attr("class","");
                $("#download").attr("onclick","");
                $("#download").hide();
}
			document.querySelector('.hoti').style.backgroundImage="url('"+track.artwork_url.split("large").join("crop")+"')"
			SC.stream(track.uri, {autoPlay: true}, function (stream) {
				window.stream = stream;
				if($ap){
					block = true;
					$("#toggle").toggleClass("pause");
				}
				//window.stream = stream.play();
			});
		});
		$("#toggle").on("click", function () {
			if(!block){
				window.stream.play();
				block = true;
			}else{
				window.stream.pause();
                block = false;
			}
			$("#toggle").toggleClass("pause");
		});
	},false);
});
})(jQuery);
	</script>
        <ul>
            <li id="toggle" class="play"></li>
            <li id="download"></li>
        </ul>
MY_MARKER;
}else 

/*********************************************************************/
/***                                                               ***/
/***                    playlists for iOS	                       ***/
/***                                                               ***/
/*********************************************************************/

if($detect->isIOS()){
	$player .= <<<MY_MARKER
	<script>
    jQuery.noConflict();
	(function($){
	$(function() {
	var playlists = {};
	var current = 0;
	var artwork_url= "";
	var block = false;
	var objImage = new Image(400,400); 
	function imagesLoaded(){
		document.querySelector('.hoti').style.backgroundImage="url('"+objImage.src+"')";
	}
	window.addEventListener("load", function load(event){
		window.removeEventListener("load", load, false); 
		SC.get("/playlists/$id", function (playlist) {
			playlists = playlist.tracks;
			artwork_url = playlist.artwork_url;
			playSong(0);
			if(playlist.artwork_url != null){
				if (document.images)
				{
				  objImage.onLoad=imagesLoaded();
				  objImage.src= playlist.artwork_url.split("large").join("crop");
				}
	}
		});

		$("#toggle").on("click", function () {
			if(!block){
				window.stream.play();
				block = true;
			}else{
				window.stream.pause();
			}
			$("#toggle").toggleClass("pause");
		});
		$("#next").on("click", function () { 
			window.stream.stop();
			$("#toggle").attr("class","play pause");
			playNextSound();
		});
		$("#prev").on("click", function () { 
			window.stream.stop();
			$("#toggle").attr("class","play pause");
			playPrevSound();
		});
	},false);

function padDigits(number) {
    return Array(Math.max(3 - String(number).length + 1, 0)).join(0) + number;
}
	
	function playSong(i){
			current = i;
			var track = playlists[i];
			document.getElementById('title').innerHTML = track.title;
			if(track.artwork_url != null){
				if (document.images)
				{
				  objImage.onLoad=imagesLoaded();
				  objImage.src= track.artwork_url.split("large").join("crop");
				}
			}else if(artwork_url != null){
				  objImage.onLoad=imagesLoaded();
				  objImage.src= artwork_url.split("large").join("crop");
			}
			
				//document.querySelector('.hoti').style.backgroundImage="url('"+track.artwork_url.split("large").join("crop")+"')";
			if(track.downloadable){
				$("#download").addClass('downloadable');
				$("#download").attr('onclick', "window.location.href='"+track.download_url+"?consumer_key=43195eb2f2b85520cb5f65e78d6501bf'");
				$("#download").show();
			}else{
				$("#download").attr("class","");
				$("#download").attr("onclick","");
				$("#download").hide();
			}
			SC.stream(track.uri, {autoPlay: false, onfinish:playNextSound}, function (audioManager) {
			  var player = audioManager._player;
			  player.on("stateChange", function(evt){
			    console.log(evt);
			    switch(evt) {
			      case "ended":
			      	playNextSound();
			        break;
			    }
			  });
			  window.stream = player;
			  window.stream.play();
			});
	}
	
	function playNextSound(){
		if(playlists.length-1 > current)
			playSong(current + 1);
		else
			playSong(0);
	}
	
	function playPrevSound(){
		if(current==0)
			playSong(playlists.length-1);
		else
			playSong(current - 1);
	}
});
})(jQuery);
	</script>
        <ul>
            <li id="toggle" class="play"></li>
            <li id="next"></li>
            <li id="prev"></li>
            <li id="download"></li>
        </ul>
		
MY_MARKER;
/*********************************************************************/
/***                                                               ***/
/***                    playlists  for others browsers             ***/
/***                                                               ***/
/*********************************************************************/
}else{
	$player .= <<<MY_MARKER
	<script>
    jQuery.noConflict();
	(function($){
	$(function() {
	var playlists = {};
	var current = 0;
	var block = false;
	var artwork_url= "";
	var objImage = new Image(400,400); 
	function imagesLoaded(){
		document.querySelector('.hoti').style.backgroundImage="url('"+objImage.src+"')";
	}
	window.addEventListener("load", function load(event){
		window.removeEventListener("load", load, false); 
		SC.get("/playlists/$id", function (playlist) {
			playlists = playlist.tracks;
			artwork_url = playlist.artwork_url;
			  if($ap){
				playSong(0);
				block = true;
				$("#toggle").toggleClass("pause");
			  }
			if(playlist.artwork_url != null){
				if (document.images)
				{
				  objImage.onLoad=imagesLoaded();
				  objImage.src= playlist.artwork_url.split("large").join("crop");
				}
	}
		});

		$("#toggle").on("click", function () {
			if(!block){
				window.stream.play();
				block = true;
			}else{
				window.stream.pause();
                block = false;
			}
			$("#toggle").toggleClass("pause");
		});
		$("#next").on("click", function () { 
			window.stream.stop();
			$("#toggle").attr("class","play pause");
			playNextSound();
		});
		$("#prev").on("click", function () { 
			window.stream.stop();
			$("#toggle").attr("class","play pause");
			playPrevSound();
		});
	},false);

function padDigits(number) {
    return Array(Math.max(3 - String(number).length + 1, 0)).join(0) + number;
}
	
	function playSong(i){
			current = i;
			var track = playlists[i];
			document.getElementById('title').innerHTML = track.title;
			console.log(track.artwork_url);
			if(track.artwork_url != null){
				if (document.images)
				{
				  objImage.onLoad=imagesLoaded();
				  objImage.src= track.artwork_url.split("large").join("crop");
				}
			}else if(artwork_url != null){
				  objImage.onLoad=imagesLoaded();
				  objImage.src= artwork_url.split("large").join("crop");
			}
			
				//document.querySelector('.hoti').style.backgroundImage="url('"+track.artwork_url.split("large").join("crop")+"')";
			if(track.downloadable){
				$("#download").addClass('downloadable');
				$("#download").attr('onclick', "window.location.href='"+track.download_url+"?consumer_key=43195eb2f2b85520cb5f65e78d6501bf'");
				$("#download").show();
			}else{
				$("#download").attr("class","");
				$("#download").attr("onclick","");
				$("#download").hide();
			}
		SC.stream(track.uri, {autoPlay: false, onfinish:playNextSound}, function (stream) {
			window.stream = stream;
            window.stream.play();
		});
	}
	
	function playNextSound(){
		if(playlists.length-1 > current)
			playSong(current + 1);
		else
			playSong(0);
	}
	
	function playPrevSound(){
		if(current==0)
			playSong(playlists.length-1);
		else
			playSong(current - 1);
	}
});
})(jQuery);
	</script>
        <ul>
            <li id="toggle" class="play"></li>
            <li id="next"></li>
            <li id="prev"></li>
            <li id="download"></li>
        </ul>
		
MY_MARKER;
}
	}
//if($format == 'sets' || $format == 'set') $format = 'playlists';
	$player .= '</div>';
	$player .= '<h5 id="track"></h5>';
	$player .= '<h2 id="title"></h2>';
    
	return $player;

}

/*******************************************/
/**                                       **/
/**                  AJAX                 **/
/**                                       **/
/*******************************************/
/** Preview **/
add_action('wp_ajax_hoti_player_preview', 'hoti_player_preview');
function hoti_player_preview(){
	if(isset($_POST['request'])) echo hoti_player($_POST['ID'], $_POST['user'], $_POST['autoPlay'], $_POST['comments'], $_POST['width'], $_POST['classes'], $_POST['playerType'], $_POST['color'], $_POST['artwork'], $_POST['format']);
	die;
}
/** viewer Ajax **/
add_action('wp_ajax_get_soundcloud_player', 'get_soundcloud_player');
add_action('wp_ajax_nopriv_get_soundcloud_player', 'get_soundcloud_player');
function get_soundcloud_player(){
	echo hoti_player($_POST['id'], $_POST['width'], $_POST['comments'], $_POST['autoPlay'], $_POST['type'], $_POST['color'], $_POST['format']);
	die();
}
/** Add username **/
add_action('wp_ajax_hoti_add_user', 'hoti_add_user');
function hoti_add_user(){
	if(isset($_POST['request'])){
		$options = get_option('hoti_options');
		if(isset($options['hoti_users'])){
			$return = 'error';
			//Check if username doesn't exist already and is not blank
			if(!empty($_POST['username']) && !array_key_exists($_POST['username'], $options['hoti_users'])){
				$newUsername = str_replace(" ", "-", trim($_POST['username']));
				//Get user info
				$userInfo = get_hoti_api_response("http://api.soundcloud.com/users/".$newUsername.".xml?client_id=43195eb2f2b85520cb5f65e78d6501bf");
				if(isset($userInfo) && isset($userInfo['response']->permalink)){
					$newUsername = (string)$userInfo['response']->permalink;
					$newUsernameImg = (string)$userInfo['response']->{'avatar-url'}[0];
					
					$return = '<li class="hotiUserContainer" style="background-image:URL('.$newUsernameImg.')">';
					$return .= '<span class="hotiRemoveUser" />&nbsp;</span>';
					$return .= '<div>';
					$return .= '<input type="hidden" value="'.$newUsername.'" name="hoti_options[hoti_users]['.$newUsername.'][0]" />';
					$return .= '<input type="hidden" value="'.$newUsernameImg.'" name="hoti_options[hoti_users]['.$newUsername.'][1]" />';
					$return .= '<p>'.$newUsername.'</p>';
					$return .= '</div>';
					$return .= '</li>';
					
					//Tab: extra actions
					if($_POST['updateOption'] == '1'){
						$options['hoti_users'][$newUsername][0] = $newUsername;
						$options['hoti_users'][$newUsername][1] = $newUsernameImg;
						update_option( 'hoti_options', $options );
					}
				}
			}
			echo $return;
		}
	}
	die;
}
/** Set Active User **/
add_action('wp_ajax_hoti_set_active_user', 'hoti_set_active_user');
function hoti_set_active_user(){
	$message = 'error';
	if(isset($_POST['request'])){
		$options = get_option('hoti_options');
		if(isset($options['hoti_active_user'])){
			//Check if username exist and is not blank
			if(!empty($_POST['username']) && array_key_exists($_POST['username'], $options['hoti_users'])){
				$options['hoti_active_user'] = $_POST['username'];
				update_option( 'hoti_options', $options );
				$message = 'done';
			}
		}
	}
	echo $message;
	die;
}
/** Delete User **/
add_action('wp_ajax_hoti_delete_user', 'hoti_delete_user');
function hoti_delete_user(){
	$message = 'error';
	if(isset($_POST['request'])){
		$options = get_option('hoti_options');
		if(isset($options['hoti_active_user'])){
			//Check username exist and isn't blank
			if(!empty($_POST['username']) && array_key_exists($_POST['username'], $options['hoti_users'])){
				//Remove from users
				unset($options['hoti_users'][$_POST['username']]);
				//If active user, set the first element to be active
				if($options['hoti_active_user'] == $_POST['username']){
					$newActiveUser = array_shift(array_values($options['hoti_users']));
					$options['hoti_active_user'] = $newActiveUser[0];
				}
				update_option( 'hoti_options', $options );
				$message = 'done';
			}
		}
	}
	
	echo $message;
	die;
}

/*******************************************/
/**                                       **/
/**                WIDGET                 **/
/**                                       **/
/*******************************************/
// register hoti_Widget
add_action( 'widgets_init', create_function( '', 'register_widget( "hoti_widget" );' ) );
class hoti_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'hoti_widget', // Base ID
			'Hotï Player', // Name
			array( 'description' => __( 'Show your Latest Tracks, Favorites or Sets for one or multiple users. If you\'re crasy go random for everything!', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$user = $instance['user'];
		$playertype = $instance['playertype'];
		$autoplay = $instance['autoplay'] ? 'true' : 'false';
		$comments = $instance['comments'] ? 'true' : 'false';
		$artwork = $instance['artwork'] ? 'true' : 'false';
		$classes = $instance['classes'];
		$widthType = $instance['type'];
		$wp = $instance['wp'];
		$custom = $instance['custom'];
		$width = ($widthType == 'wp') ? $wp : $custom;
		$behavior = $instance['behavior'];
		$number = $instance['number'];
		$format = $instance['format'];
		
		//Random User
		if($user == "randomUser") {
			$options = get_option('hoti_options');
			$hotiUsers = isset($options['hoti_users']) ? array_random($options['hoti_users'], 1) : '';
			//printl($hotiUsers[0][0]);
			if(isset($hotiUsers))  $user = $hotiUsers[0][0];
		}
		
		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		
		//Random User per Track
		if($user == "randomUsers") {
			$options = get_option('hoti_options');
			if(isset($options['hoti_users'])){
				//Never select more tracks than there is users.
				$number = (count($options['hoti_users']) <= $number) ? count($options['hoti_users']) : $number;
				$hotiUsers = array_random($options['hoti_users'], $number);
			}
			if(isset($hotiUsers)){
				foreach($hotiUsers as $userKey=>$user){
					if($userKey == 1) $autoplay = false;
					foreach(get_hoti_multiple_tracks_id($user[0], 1, ($behavior == "latest") ? FALSE : TRUE, $format) as $key=>$ids){
						if($format == "favorites") $format = "tracks"; //Soundcloud treats Favorites as Tracks for the player.
						echo hoti_player($ids, NULL, $autoplay, $comments, $width, $classes, $playertype, NULL, $artwork, $format);
					}
				}
			}
		}
		//One User
		else{	
			foreach(get_hoti_multiple_tracks_id($user, $number, ($behavior == "latest") ? FALSE : TRUE, $format) as $key=>$ids){
				if($key == 1) $autoplay = false;
				if($format == "favorites") $format = "tracks"; //Soundcloud treats Favorites as Tracks for the player.
				echo hoti_player($ids, NULL, $autoplay, $comments, $width, $classes, $playertype, NULL, $artwork, $format);
			}		
		}
		
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['user'] = strip_tags( $new_instance['user'] );
		$instance['format'] = strip_tags( $new_instance['format'] );
		$instance['behavior'] = strip_tags( $new_instance['behavior'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		$instance['playertype'] = strip_tags( $new_instance['playertype'] );
		$instance['autoplay'] = strip_tags( $new_instance['autoplay'] );
		$instance['comments'] = strip_tags( $new_instance['comments'] );
		$instance['artwork'] = strip_tags( $new_instance['artwork'] );
		$instance['classes'] = strip_tags( $new_instance['classes'] );
		$instance['type'] = strip_tags( $new_instance['type'] );
		$instance['wp'] = strip_tags( $new_instance['wp'] );
		$instance['custom'] = strip_tags( $new_instance['custom'] );
		
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Latest', 'text_domain' );
		}
		?>
		<!-- Title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<!-- Users -->
		<p>
			<label for="<?php echo $this->get_field_id('user'); ?>"><?php _e( 'Username:' ); ?></label>
			<select name="<?php echo $this->get_field_name('user'); ?>" id="<?php echo $this->get_field_id('user'); ?>" class="widefat">
				<?php
				$options = get_option('hoti_options');
				foreach($options['hoti_users'] as $user) : ?>
					<option value="<?php echo $user[0] ?>"<?php selected( $instance['user'], $user[0] ); ?>><?php _e($user[0]); ?></option>	
				<?php endforeach; ?>
				<option value="randomUser"<?php selected( $instance['user'], "randomUser" ); ?>><?php _e("Pick a Random User"); ?></option>	
				<option value="randomUsers"<?php selected( $instance['user'], "randomUsers" ); ?>><?php _e("Pick a Random User per Track"); ?></option>	
			</select>
		</p>
		<!-- Main options -->
		<?php
			$autoplay = $instance['autoplay'] ? 'checked="checked"' : '';
			$comments = $instance['comments'] ? 'checked="checked"' : '';
			$artwork = $instance['artwork'] ? 'checked="checked"' : '';
		?>
		<p>
			<label for=""><?php _e( 'Settings:' ); ?></label>
			<select name="<?php echo $this->get_field_name('format'); ?>" id="<?php echo $this->get_field_id('format'); ?>" class="widefat">
				<option value="tracks"<?php selected( $instance['format'], "tracks" ); ?>><?php _e("tracks"); ?></option>
				<option value="favorites"<?php selected( $instance['format'], "favorites" ); ?>><?php _e("favorites"); ?></option>
				<option value="sets"<?php selected( $instance['format'], "sets" ); ?>><?php _e("sets"); ?></option>
			</select>
			<br/>
			<br/>
			<select name="<?php echo $this->get_field_name('behavior'); ?>" id="<?php echo $this->get_field_id('behavior'); ?>" class="widefat">
				<option value="latest"<?php selected( $instance['behavior'], "latest" ); ?>><?php _e("Latest"); ?></option>
				<option value="random"<?php selected( $instance['behavior'], "random" ); ?>><?php _e("Random"); ?></option>
			</select>
			<br/>
			<br/>
			<select name="<?php echo $this->get_field_name('number'); ?>" id="<?php echo $this->get_field_id('number'); ?>" class="widefat">
				<?php
				for($i=1; $i<=5; $i++) : ?>
					<option value="<?php echo $i ?>"<?php selected( $instance['number'], $i ); ?>><?php _e($i); ?></option>	
				<?php endfor; ?>
			</select>
			<br/>
			<br/>
			<input class="checkbox" type="checkbox" <?php echo $autoplay; ?> id="<?php echo $this->get_field_id('autoplay'); ?>" name="<?php echo $this->get_field_name('autoplay'); ?>" /> <label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Play Automatically'); ?></label>
			<br/>
			<input class="checkbox" type="checkbox" <?php echo $comments; ?> id="<?php echo $this->get_field_id('comments'); ?>" name="<?php echo $this->get_field_name('comments'); ?>" /> <label for="<?php echo $this->get_field_id('comments'); ?>"><?php _e('Show comments <small>(Standard and Artwork player)</small>'); ?></label>
			<br/>
			<input class="checkbox" type="checkbox" <?php echo $artwork; ?> id="<?php echo $this->get_field_id('artwork'); ?>" name="<?php echo $this->get_field_name('artwork'); ?>" /> <label for="<?php echo $this->get_field_id('artwork'); ?>"><?php _e('Show Artwork <small>(html5 player)</small>'); ?></label>
		</p>
		<!-- Player -->
		<p>
			<label for="<?php echo $this->get_field_id('playertype'); ?>"><?php _e( 'Player Type:' ); ?></label>
			<select name="<?php echo $this->get_field_name('playertype'); ?>" id="<?php echo $this->get_field_id('playertype'); ?>" class="widefat">
				<?php
				$playertypes = array("Mini", "Standard", "Artwork", "html5");
				foreach($playertypes as $playertype) : ?>
					<option value="<?php echo $playertype ?>"<?php selected( $instance['playertype'], $playertype ); ?>><?php _e($playertype); ?></option>	
				<?php endforeach; ?>
			</select>
		</p>
		<!-- Width -->
		<?php
		
		?>
		<p>
			<label for=""><?php _e( 'Width:' ); ?></label>
			<p>
				<input type="radio" <?php checked( $instance['type'], "wp" ); ?> value="wp" id="wp" name="<?php echo $this->get_field_name('type'); ?>" /><label for="wp">Media Width</label>
				<br/>
				<select name="<?php echo $this->get_field_name('wp'); ?>" id="<?php echo $this->get_field_id('wp'); ?>" class="widefat">
				<?php foreach(get_hoti_wordpress_sizes() as $key => $hotiMediaSize) : ?>
					<option value="<?php echo $hotiMediaSize[0]?>" <?php selected( $instance['wp'], $hotiMediaSize[0] ); ?>><?php _e($key.': '.$hotiMediaSize[0]); ?></option>
				<?php endforeach; ?>
				</select>
			</p>
			<p>
				<input type="radio" <?php checked( $instance['type'], "custom" ); ?> value="custom" id="custom" name="<?php echo $this->get_field_name('type'); ?>" /><label for="custom">Custom Width</label>
				<br/>
				<input type="text" value="<?php echo $instance['custom'] ? $instance['custom'] : "100%" ?>" id="<?php echo $this->get_field_id('custom'); ?>" name="<?php echo $this->get_field_name('custom'); ?>"/>
			</p>
		</p>
		<!-- Classes -->
		<p>
			<label for="<?php echo $this->get_field_id('classes'); ?>"><?php _e( 'Classes <small>(no commas)</small>:' ); ?></label>
			<input type="text" value="<?php echo $instance['classes'] ?>" id="<?php echo $this->get_field_id('classes'); ?>" name="<?php echo $this->get_field_name('classes'); ?>"/>
		</p>
		<?php
	}
	
} // class Foo_Widget

/*********************************************************************/
/***                                                               ***/
/***                     Theme Files		                       ***/
/***                                                               ***/
/*********************************************************************/

    
function prefix_add_my_stylesheet() {
	wp_register_style( 'wp-hoti-player', plugins_url('includes/theme-hoti-player.css', __FILE__) );
	wp_enqueue_style( 'wp-hoti-player' );
}

function my_soundcloud_enqueue() {
   wp_deregister_script('soundcloud');
   wp_register_script('soundcloud', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://connect.soundcloud.com/sdk-2.0.0.js", false, null);
   wp_enqueue_script('soundcloud');
}

/* function my_scripts_method() {
	wp_enqueue_script(
		'custom-script',
		plugins_url( 'includes/script-hoti-player.js', __FILE__ ),
		array( 'jquery' )
	);
} */

function my_scripts_method() {
	wp_register_script( 'wp-hoti-player' , plugins_url( 'includes/script-hoti-player.js', __FILE__ ));
	wp_enqueue_script( 'wp-hoti-player' );
}

add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );
add_action("wp_enqueue_scripts", "my_soundcloud_enqueue");
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );

?>
