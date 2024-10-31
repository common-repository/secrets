<?php
/*
Plugin Name: Secrets
Plugin URI: http://meandmymac.net/plugins/secrets/
Description: Options which are otherwise hidden from everyone...
Author: Arnan de Gans
Version: 0.3.3
Author URI: http://meandmymac.net
*/ 

secrets_check_config();
add_action('admin_menu', 'secrets_add_pages');
//add_action('template_redirect', 'secrets_header');

if(isset($_POST['secrets_submit_options']) AND $_GET['updated'] == "true") {
	add_action('init', 'secrets_options_submit');
}

function secrets_add_pages() {
	add_options_page('Secrets', 'Secrets', 10, basename(__FILE__), 'secrets_options_page');
}
$secrets_config = get_option('secrets_config');

#################################################################
# SECRETS  SECRETS  SECRETS  SECRETS  SECRETS  SECRETS  SECRETS #
#################################################################
if($secrets_config['001'] == 0) { // livewriter
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link');
}

if($secrets_config['002'] == 0) { // postformatting
	remove_filter('the_content', 'wpautop');
}

if($secrets_config['003'] == 0) { // drafts (cron)
	define('WP_POST_REVISIONS', false);
	add_action('admin_print_scripts', 'plugin_deregister_autosave');

	function plugin_deregister_autosave() {
		wp_deregister_script('autosave');
	}
}

if($secrets_config['004'] == 0) { // auto-updater
	remove_action('init', 'wp_version_check');
}

if($secrets_config['005'] == 0) { // plugin updater
	remove_action('load-plugins.php', 'wp_update_plugins');
	remove_action('admin_init', '_maybe_update_plugins');
	remove_action('wp_update_plugins', 'wp_update_plugins');
}
#################################################################
# END														    #
#################################################################

/*-------------------------------------------------------------
 Name:      secrets_options_page

 Purpose:   Admin options page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function secrets_options_page() {
	$secrets_config = get_option('secrets_config');
?>
	<div class="wrap">
	  	<h2>Secrets</h2>
	  	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>&amp;updated=true">
	    	<input type="hidden" name="secrets_submit_options" value="true" />

	    	<table class="form-table">

		      	<tr valign="top">
					<td colspan="2" bgcolor="#DDD"><span style="color:#f00; font-weight:bold;">WARNING: Use this page with caution! If you do not know what to do, don't use it!<br />Some options are marked with a number like [2.5] This is the minimum version of Wordpress required for that option</span></td>
		      	</tr>

		      	<tr valign="top">
			        <td width="80%"><span style="font-weight:bold;">XMLRPC support</span><br /><em>Allow external blogging tools to post on your site?</em></td>
			        <td><select name="001">';
					<?php if($secrets_config['001'] == 1) { ?>
				        <option value="1">enabled (default)</option>
						<option value="0">disabled</option>
					<?php } else { ?>
						<option value="0">disabled</option>
				        <option value="1">enabled (default)</option>
					<?php } ?>
					</select></td>
		      	</tr>

		      	<tr valign="top">
			        <td width="80%"><span style="font-weight:bold;">Autoformatting on posts</span><br /><em>This keeps Wordpress from filtering things or re-arranging html (etc.) in posts</em></td>
			        <td><select name="002">';
					<?php if($secrets_config['002'] == 1) { ?>
				        <option value="1">enabled (default)</option>
						<option value="0">disabled</option>
					<?php } else { ?>
						<option value="0">disabled</option>
				        <option value="1">enabled (default)</option>
					<?php } ?>
					</select></td>
		      	</tr>

		      	<tr valign="top">
			        <td width="80%"><span style="font-weight:bold;">[2.6.x] Save drafts/revisions when writing and editing posts</span><br /><em>Disables automatic saving of drafts</em></td>
			        <td><select name="003">';
					<?php if($secrets_config['003'] == 1) { ?>
				        <option value="1">enabled (default)</option>
						<option value="0">disabled</option>
					<?php } else { ?>
						<option value="0">disabled</option>
				        <option value="1">enabled (default)</option>
					<?php } ?>
					</select></td>
		      	</tr>
		      	
		      	<tr valign="top">
			        <td width="80%"><span style="font-weight:bold;">[2.5] Wordpress version checker</span><br /><em>Wordpress no longer checks for new versions. Make sure no message telling you there is a new version is present or it will not go away!</em></td>
			        <td><select name="004">';
					<?php if($secrets_config['004'] == 1) { ?>
				        <option value="1">enabled (default)</option>
						<option value="0">disabled</option>
					<?php } else { ?>
						<option value="0">disabled</option>
				        <option value="1">enabled (default)</option>
					<?php } ?>
					</select></td>
		      	</tr>
		      	
		      	<tr valign="top">
			        <td width="80%"><span style="font-weight:bold;">[2.5] Wordpress plugin update checker</span><br /><em>Wordpress no longer checks for updates on plugins. Make sure no update notices are shown or they won't go away!</em></td>
			        <td><select name="005">';
					<?php if($secrets_config['005'] == 1) { ?>
				        <option value="1">enabled (default)</option>
						<option value="0">disabled</option>
					<?php } else { ?>
						<option value="0">disabled</option>
				        <option value="1">enabled (default)</option>
					<?php } ?>
					</select></td>
		      	</tr>

		      	<tr valign="top">
					<td colspan="2" bgcolor="#DDD"><span style="font-weight:bold;">For support <a href="http://forum.at.meandmymac.net" target="_blank" title="Support forums - new window">go here</a>. Have secrets you want added? <a href="http://forum.at.meandmymac.net/forum/secrets" target="_blank" title="Submit your secrets! - new window">Submit them</a>!</span></td>
		      	</tr>

	    	</table>
		    <p class="submit">
		      	<input type="submit" name="Submit" value="Update Options &raquo;" />
		    </p>
		</form>
	</div>
<?php
}	

/*-------------------------------------------------------------
 Name:      secrets_check_config

 Purpose:   Create or update the options
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function secrets_check_config() {
	if ( !$option = get_option('secrets_config') ) {
		// Default Options
		$option['001']		= 1;
		$option['002']		= 1;
		$option['003']		= 1;
		$option['004']		= 1;
		$option['005']		= 1;
		update_option('secrets_config', $option);
	}
}

/*-------------------------------------------------------------
 Name:      secrets_options_submit

 Purpose:   Save options
 Receive:   $_POST
 Return:    -none-
-------------------------------------------------------------*/
function secrets_options_submit() {
	//options page
	$option['001'] 			= $_POST['001'];
	$option['002'] 			= $_POST['002'];
	$option['003']			= $_POST['003'];
	$option['004']			= $_POST['004'];
	$option['005']			= $_POST['005'];
	update_option('secrets_config', $option);
}
?>