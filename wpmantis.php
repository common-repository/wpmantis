<?php

/*
Plugin Name: WPMantis
Plugin URI: http://www.mountaingrafix.at/
Description: This plugin generates automatically links to your <a href="http://www.mantisbt.org/">Mantis Bugtracker</a> into your posts.
Author: MountainGrafix
Author URI: http://www.mountaingrafix.at/
Version: 1.0
*/

define('WPMantisVersion', '1.0');

/**
 * Erzeugt einen neuen Menüpunkt
 */
function WPMantisConfigPage() {
	if (function_exists('add_options_page')) {
		add_options_page('WPMantis', 'WPMantis', 8, 'wpmantis/wpmantis.php', 'WPMantisConfig');
	}
}
add_action('admin_menu', 'WPMantisConfigPage');


/**
 * Repäsentiert das Einstellungsformular
 */
function WPMantisConfig() {	?>

	<div class="wrap">
		
		<?php
		
		if (isset($_POST['Submit'])) {
			
			update_option('WPMantisURL', trim($_POST['WPMantisURL']));
			update_option('WPMantisOpenInNewWindow', $_POST['WPMantisOpenInNewWindow']);
			
			echo "<div id=\"message\" class=\"updated fade\">\n";
			echo "<p><strong>Die Einstellungen wurden gespeichert.</strong></p>\n";
			echo "</div>\n";
		}
		screen_icon();
		
		?>
		<h2>WPMantis <?php echo WPMantisVersion; ?></h2>
		<form method="post" action="./options-general.php?page=wpmantis/wpmantis.php">
			<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="WPMantisURL">Mantis - URL</label></th>
				<td><input name="WPMantisURL" type="text" id="WPMantisURL" value="<?php echo get_option('WPMantisURL'); ?>" class="regular-text" /> <span class="setting-description"><br />Bitte inkl. Slash (/) am Ende der Url eintragen</span></td>
			</tr>
			<tr valign="top">
				<th scope="row">Target</th>
				<td>
					<fieldset>
						<legend class="hidden">Target</legend>
						<label for="WPMantisOpenInNewWindow">
							<input name="WPMantisOpenInNewWindow" type="checkbox" id="WPMantisOpenInNewWindow" value="1" <?php checked('1', get_option('WPMantisOpenInNewWindow')); ?> /> Die Links sollen in einem neuen Browser-Fenster öffnens
						</label>
					</fieldset>
				</td>
			</tr>
			</table>
			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
		</form>
	</div>

<? } 

/**
 * Filtert die Bug-Einträge aus dem Content und
 * verlinkt diese mit dem Bugtracker
 *
 * @param string $content
 */
function WPMantisContentFilter($Content) {
	preg_match_all("/([^&])#(\d+)/i", $Content, $Results);
	
	if (is_array($Results[1])) {
		$Links = array ();
		
		$OptionUrl 			= get_option('WPMantisURL');
		$OptionTargetNew 	= get_option('WPMantisOpenInNewWindow');
		$Target 			= (($OptionTargetNew == '1') ? " target=\"_blank\"" : "");
		
		for ($m = 0; $m < count($Results[0]); $m++) {
			
			$Previous = $Results[1][$m];
			$BugID = trim($Results[2][$m]);
			
			$Links[$m] = sprintf("<a title=\"Zeige diesen Fehler im Bugtracker\" href=\"%sview.php?id=%d\" %s>#%d</a>", $OptionUrl, $BugID, $Target, $BugID);
			$Text = $Previous.$Links[$m];
			
			$Content = str_replace($Results[0][$m], $Text, $Content); 
		}
	}
	
	return $Content;
}
add_filter ('the_content', 'WPMantisContentFilter');

?>