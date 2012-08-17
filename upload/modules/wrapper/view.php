<?php

/**
 * This file is part of an ADDON for use with LEPTON Core.
 * This ADDON is released under the GNU GPL.
 * Additional license terms can be seen in the info.php of this module.
 *
 * @module          wrapper
 * @author          WebsiteBaker Project
 * @author          LEPTON Project
 * @copyright       2004-2010, WebsiteBaker Project
 * @copyright       2010-2011, LEPTON Project
 * @link            http://www.LEPTON-cms.org
 * @license         http://www.gnu.org/licenses/gpl.html
 * @license_terms   please see info.php of this module
 *
 * @reformatted     2011-09-28
 *
 */

// include class.secure.php to protect this file and the whole CMS!
if ( defined( 'WB_PATH' ) )
{
	include( WB_PATH . '/framework/class.secure.php' );
}
elseif ( file_exists( $_SERVER[ 'DOCUMENT_ROOT' ] . '/framework/class.secure.php' ) )
{
	include( $_SERVER[ 'DOCUMENT_ROOT' ] . '/framework/class.secure.php' );
}
else
{
	$subs = explode( '/', dirname( $_SERVER[ 'SCRIPT_NAME' ] ) );
	$dir  = $_SERVER[ 'DOCUMENT_ROOT' ];
	$inc  = false;
	foreach ( $subs as $sub )
	{
		if ( empty( $sub ) )
			continue;
		$dir .= '/' . $sub;
		if ( file_exists( $dir . '/framework/class.secure.php' ) )
		{
			include( $dir . '/framework/class.secure.php' );
			$inc = true;
			break;
		}
	}
	if ( !$inc )
		trigger_error( sprintf( "[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER[ 'SCRIPT_NAME' ] ), E_USER_ERROR );
}
// end include class.secure.php

global $parser;

// check if module language file exists for the language set by the user (e.g. DE, EN)
if ( !file_exists( WB_PATH . '/modules/wrapper/languages/' . LANGUAGE . '.php' ) )
{
	// no module language file exists for the language set by the user, include default module language file EN.php
	require_once( WB_PATH . '/modules/wrapper/languages/EN.php' );
}
else
{
	// a module language file exists for the language defined by the user, load it
	require_once( WB_PATH . '/modules/wrapper/languages/' . LANGUAGE . '.php' );
}

// get url
$get_settings   = $database->query( "SELECT url,height,width,wtype FROM " . TABLE_PREFIX . "mod_wrapper WHERE section_id = '$section_id'" );
$fetch_settings = $get_settings->fetchRow( MYSQL_ASSOC );
$url            = ( $fetch_settings[ 'url' ] );

if ( !isset($fetch_settings['wtype']) || ($fetch_settings['wtype']) == '' ) {
    $fetch_settings['wtype'] = 'iframe';
}

if ( !file_exists(WB_PATH.'/modules/wrapper/htt/'.$fetch_settings['wtype'].'.lte') ) {
	echo "ERROR: No such type!<br />";
}
else {
	$data = array(
	    'MOD_WRAPPER' => $MOD_WRAPPER,
	    'SETTINGS'    => $fetch_settings
	);
	$parser->setPath( WB_PATH.'/modules/wrapper/htt' );
	$parser->output( $fetch_settings['wtype'].'.lte', $data );
}

?>