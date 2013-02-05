<?php
/**
 * This file is part of LEPTON2 Core, released under the GNU GPL
 * Please see LICENSE and COPYING files in your package for details, specially for terms and warranties.
 * 
 * NOTICE:LEPTON CMS Package has several different licenses.
 * Please see the individual license in the header of each single file or info.php of modules and templates.
 *
 * @author			LEPTON2 Project
 * @copyright		2012, LEPTON2 Project
 * @link			http://lepton2.org
 * @license			http://www.gnu.org/licenses/gpl.html
 * @license_terms	please see LICENSE and COPYING files in your package
 *
 */
 

// include class.secure.php to protect this file and the whole CMS!
if (defined('CAT_PATH')) {
	include(CAT_PATH . '/framework/class.secure.php');
} else {
	$oneback = "../";
	$root = $oneback;
	$level = 1;
	while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
		$root .= $oneback;
		$level += 1;
	}
	if (file_exists($root.'/framework/class.secure.php')) {
		include($root.'/framework/class.secure.php');
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}
// end include class.secure.php

// ================================= 
// ! Include the WB functions file   
// ================================= 
include_once(CAT_PATH . '/framework/functions.php');

require_once(CAT_PATH . '/framework/class.admin.php');
$admin			= new admin('Media', 'media', false);

header('Content-type: application/json');

$ajax['folder_path'] = $admin->get_post('folder_path');

if ( $ajax['folder_path'] == '' || $admin->get_permission('media_create') !== true )
{
	$ajax	= array(
		'message'	=> 'You don\'t have the permission to create a folder. Check your system settings.',
		'deleted'	=> false
	);
	//header( 'Location: ' . CAT_ADMIN_URL );
	print json_encode( $ajax );
	exit();
}

else {
	// ================================ 
	// ! Check if folder is writeable   
	// ================================ 
	if ( is_writable(CAT_PATH . $ajax['folder_path']) )
	{
		$create_folder		= CAT_PATH . $ajax['folder_path'].'/' . $admin->lang->translate('New folder');
		$counter			= 1;
		while ( is_dir($create_folder) )
		{
			$create_folder	= CAT_PATH . $ajax['folder_path'] . '/' . $admin->lang->translate('New folder') . ' ' . $counter;
			$counter++;
		}
		// ============================ 
		// ! Try to create new folder   
		// ============================ 
		if(make_dir($create_folder))
		{

			change_mode($create_folder);
			// Needs to be replaced with function create_access_file() or similar
			if( is_writable($create_folder) )
			{
				// =================================== 
				// ! Create default "index.php" file   
				// =================================== 
				$rel_pages_dir = str_replace( CAT_PATH . MEDIA_DIRECTORY, '', dirname( $create_folder ) );
				$step_back = str_repeat( '../', substr_count($rel_pages_dir, '/') + 1 );

				$content  = '<?php'."\n";
				$content .= '// This file is generated by Black Cat CMS Ver.' . CAT_VERSION . ';' . "\n";
				$content .= "\t".'header(\'Location: ' . $step_back . 'index.php\');' . "\n";
				$content .= '?>';

				$filename = $create_folder.'/index.php';

				// =========================== 
				// ! write content into file   
				// =========================== 
				$handle = fopen($filename, 'w');
				fwrite($handle, $content);
				fclose($handle);
				change_mode($filename, 'file');

				$ajax['message']	= $admin->lang->translate( 'Folder created successfully' );
				$ajax['created']	= true;

			}
			else {
				$ajax['message']	= $admin->lang->translate( 'Unable to write to the target directory' );
				$ajax['created']	= false;
			}
		}
		else {
			$ajax['message'] = $admin->lang->translate( 'Unable to write to the target directory' );
			$ajax['created']	= false;
		}
	}
	else {
		$ajax['message'] = $admin->lang->translate( 'Unable to write to the target directory' );
		$ajax['created']	= false;
	}
	print json_encode( $ajax );
}

?>