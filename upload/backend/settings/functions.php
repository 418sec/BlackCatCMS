<?php

/**
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 3 of the License, or (at
 *   your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful, but
 *   WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *   General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 *   @author          Black Cat Development
 *   @copyright       2013, Black Cat Development
 *   @link            http://blackcat-cms.org
 *   @license         http://www.gnu.org/licenses/gpl.html
 *   @category        CAT_Core
 *   @package         CAT_Core
 *
 */

if (defined('CAT_PATH')) {
    if (defined('CAT_VERSION')) include(CAT_PATH.'/framework/class.secure.php');
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
    include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php');
} else {
    $subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));    $dir = $_SERVER['DOCUMENT_ROOT'];
    $inc = false;
    foreach ($subs as $sub) {
        if (empty($sub)) continue; $dir .= '/'.$sub;
        if (file_exists($dir.'/framework/class.secure.php')) {
            include($dir.'/framework/class.secure.php'); $inc = true;    break;
        }
    }
    if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}

require_once(CAT_PATH.'/framework/functions-utf8.php');

global $groups, $allow_tags_in_fields, $allow_empty_values, $boolean, $numeric;
$groups = array(
    'seo' => array('website_title','website_description','website_keywords'),
    'frontend' => array('default_template','website_header','website_footer'),
    'backend' => array('default_theme','default_theme_variant','wysiwyg_editor','er_level','redirect_timer','token_lifetime','max_attempts'),
    'system' => array('maintenance_mode','fc_maintenance_page','page_level_limit','page_trash','manage_sections','section_blocks','multiple_menus'),
    'users' => array('frontend_signup','frontend_login','home_folders'),
    'server' => array('operating_system','pages_directory','page_extension','media_directory','page_spacer','upload_allowed','app_name','sec_anchor'),
    'mail' => array('server_email','catmailer_default_sendername','catmailer_routine','catmailer_smtp_host','catmailer_smtp_username','catmailer_smtp_password'),
);
$allow_tags_in_fields = array('website_header', 'website_footer');
$allow_empty_values   = array('website_header', 'website_footer', 'sec_anchor', 'pages_directory','catmailer_smtp_host','catmailer_smtp_username','catmailer_smtp_password');
$boolean              = array('auto_disable_users','frontend_login','home_folders','manage_sections','multiple_menus','page_trash','prompt_mysql_errors','section_blocks','maintenance_mode','homepage_redirection','intro_page','page_languages','users_allow_mailaddress');
$numeric              = array('redirect_timer','frontend_signup','maintenance_page','err_page','page_level_limit','token_lifetime','max_attempts');

/**
 * get data from settings table
 **/
function getSettingsTable() {
    $settings = CAT_Registry::getSettings();
    $data     = array();
    foreach($settings as $key => $value)
    {
        $data[strtolower($key)] = $value;
    }
    return $data;
}   // end function getSettingsTable()

/**
 * get settings from search table
 **/
function getSearchSettings()
{
    $backend = CAT_Backend::getInstance('Settings', 'settings_advanced');
    $data = array();
    if (
        (
             ($res_search = $backend->db()->query(sprintf('SELECT * FROM `%ssearch` WHERE `extra` = \'\' ',CAT_TABLE_PREFIX)))
          && ($res_search->numRows() > 0)
        )
    ) {
        while ( $row = $res_search->fetchRow() )
        {
            $data[$row['name']]
                = htmlspecialchars(($row['value']));
        }
    }
    return $data;
}   // end function getSearchSettings()

/**
 * get a list of installed templates
 **/
function getTemplateList($for='frontend') {
    if($for=='backend')
        return CAT_Helper_Addons::get_addons( CAT_Registry::get('DEFAULT_THEME'), 'template', 'theme' );
    else
        return CAT_Helper_Addons::get_addons( CAT_Registry::get('DEFAULT_TEMPLATE'), 'template', 'template' );
}   // end function getTemplateList()

/**
 * get a list of defined error levels
 **/
function getErrorLevels() {
    $ER_LEVELS = CAT_Registry::get('ER_LEVELS','array');
    $counter   = 0;
    $data      = array();
    foreach ( $ER_LEVELS AS $value => $title )
    {
        $data[$counter] = array(
            'NAME'     => $title,
            'VALUE'    => $value,
            'SELECTED' => (ER_LEVEL == $value) ? true : false
        );
        $counter++;
    }
    return $data;
}   // end function getErrorLevels()

/**
 *
 **/
function getPagesList($fieldname,$selected,$add_empty=false) {
    $pages_list = CAT_Helper_Page::getPages();
    $deleted    = CAT_Helper_Page::getPagesByVisibility('deleted');
    if(count($deleted))
    {
        $arrh = CAT_Helper_Array::getInstance();
        foreach($deleted as $page)
        {
            $arrh->ArrayRemove( $page, $pages_list, 'page_id' );
        }
    }
    if($add_empty)
        array_unshift($pages_list,array('page_id'=>-1,'parent'=>0,'level'=>0,'menu_title'=>CAT_Helper_Page::getInstance()->lang()->translate('[none (use internal)]')));
    return CAT_Helper_ListBuilder::getInstance(true)
           ->config(array('space' => '|-- '))
           ->dropdown( $fieldname, $pages_list, 0, $selected );
}   // end function getPagesList()

/**
 *
 **/
function getLanguages() {
    $langs = CAT_Helper_Addons::get_addons(0,'language');
    $data  = array();
    foreach($langs as $addon)
    {
        $l_codes[$addon['NAME']] = $addon['VALUE'];
        $l_names[$addon['NAME']] = entities_to_7bit($addon['NAME']); // sorting-problem workaround
    }
    asort($l_names);
    $counter=0;
    foreach($l_names as $l_name=>$v)
    {
        $data[$counter]['CODE']    = $l_codes[$l_name];
        $data[$counter]['NAME']    = $l_name;
        $data[$counter]['SELECTED'] = (DEFAULT_LANGUAGE == $l_codes[$l_name]) ? true : false;
        $counter++;
    }
    return $data;
}   // end function getLanguages()

/**
 *
 **/
function getTimezones() {
    $timezone_table = CAT_Helper_DateTime::getTimezones();
    $counter        = 0;
    $data           = array();
    foreach( $timezone_table as $title )
    {
        $data[$counter] = array(
            'NAME'            => $title,
            'SELECTED'        => ( DEFAULT_TIMEZONE_STRING == $title ) ? true : false
        );
        $counter++;
    }
    return $data;
}

/**
 *
 **/
function getCharsets() {
    $CHARSETS = CAT_Helper_I18n::getInstance()->getCharsets();
    $counter  = 0;
    $data     = array();
    foreach ( $CHARSETS AS $code => $title )
    {
        $data[$counter] = array(
            'NAME'            => $title,
            'VALUE'            => $code,
            'SELECTED'        => ( DEFAULT_CHARSET == $code ) ? true : false
        );
        $counter++;
    }
    return $data;
}

/**
 *
 **/
function getDateformats() {
    $DATE_FORMATS = CAT_Helper_DateTime::getDateFormats();
    $counter      = 0;
    $data         = array();
    foreach ( $DATE_FORMATS AS $format => $title )
    {
        $data[$counter] = array(
            'NAME'     => $title,
            'VALUE'    => ( $format != 'system_default' )    ? $format : '',
            'SELECTED' => ( DEFAULT_DATE_FORMAT == $format ) ? true    : false
        );
        $counter++;
    }
    return $data;
}

/**
 *
 **/
function getTimeformats() {
    $TIME_FORMATS = CAT_Helper_DateTime::getTimeFormats();
    $counter      = 0;
    $data         = array();
    foreach ( $TIME_FORMATS AS $format => $title )
    {
        $format = str_replace('|', ' ', $format); // Add's white-spaces (not able to be stored in array key)
        $data[$counter] = array(
            'NAME'     => $title,
            'VALUE'    => ( $format != 'system_default' ) ? $format : '',
            'SELECTED' => ( DEFAULT_TIME_FORMAT == $format ) ? true : false
        );
        $counter++;
    }
    return $data;
}

/**
 *
 **/
function getMailerLibs() {
    $data = array();
    $mailer_libs = CAT_Helper_Addons::getInstance()->getLibraries('mail');
    if ( count($mailer_libs) )
    {
        foreach ( $mailer_libs as $item )
        {
            $data[] = $item;
        }
    }
    return $data;
}

/**
 *
 **/
function saveServer($backend) {
    saveGroup($backend,'server');
    if (CAT_Helper_Validate::sanitizePost('world_writeable') == 'true')
    {
         $string_file_mode = '0666';
         $string_dir_mode  = '0777';
    }
    else {
         $string_file_mode = '0644';
         $string_dir_mode = '0755';
    }
    $backend->db()->query(sprintf(
        'UPDATE `%ssettings` SET `value`=\'%s\' WHERE `name`=\'%s\'',
        CAT_TABLE_PREFIX, $string_file_mode, 'string_file_mode'
    ));
    $backend->db()->query(sprintf(
        'UPDATE `%ssettings` SET `value`=\'%s\' WHERE `name`=\'%s\'',
        CAT_TABLE_PREFIX, $string_dir_mode, 'string_dir_mode'
    ));
}

/**
 *
 **/
function saveDatetime($backend) {
    $settings     = array();
    $old_settings = getSettingsTable();
    $val          = CAT_Helper_Validate::getInstance();
    // language must be 2 upercase letters only
    $default_language = strtoupper($val->sanitizePost('default_language'));
    $settings['default_language']
       = $backend->lang()->checkLang($default_language)
       ? $default_language
       : $old_settings['default_language'];
    // check date format
    $settings['default_date_format']
       = CAT_Helper_DateTime::checkDateformat($val->sanitizePost('default_date_format'))
       ? $val->sanitizePost('default_date_format')
       : $old_settings['default_date_format'];
    // check time format
    $settings['default_time_format']
       = CAT_Helper_DateTime::checkTimeformat($val->sanitizePost('default_time_format'))
       ? $val->sanitizePost('default_time_format')
       : $old_settings['default_date_format'];
    // check timezone string
    $settings['default_timezone_string']
       = CAT_Helper_DateTime::checkTZ($val->sanitizePost('default_timezone_string'))
       ? $val->sanitizePost('default_timezone_string')
       : $old_settings['default_timezone_string'];
    // check charset
    $CHARSETS = $backend->lang()->getCharsets();
    $char_set = $val->sanitizePost('default_charset');
    $settings['default_charset']
       = (array_key_exists($char_set,$CHARSETS)
       ? $char_set
       : $old_settings['default_charset']);
    saveSettings($settings);
}

/**
 *
 **/
function saveMail($backend) {
    $settings     = array();
    $old_settings = getSettingsTable();
    $val          = CAT_Helper_Validate::getInstance();

    global $groups, $err_msg;

    foreach ( $groups['mail'] as $key )
        $settings[$key] = $val->sanitizePost($key);

    // email should be validated by core
    // Work-out which wbmailer routine should be checked
    if ((isset ($settings['server_email'])) && (!$val->validate_email($settings['server_email'])))
    {
         $err_msg[] = $backend->lang()->translate('Invalid default sender eMail address!');
    }
    $catmailer_default_sendername = (isset ($settings['catmailer_default_sendername'])) ? $settings['catmailer_default_sendername'] : $old_settings['catmailer_default_sendername'];
    if (($catmailer_default_sendername <> ''))
         $settings['catmailer_default_sendername'] = $catmailer_default_sendername;
    else
         $err_msg[] = $backend->lang()->translate('This field is required').': '.$backend->lang()->translate('Default Sender Name');

    $catmailer_routine = isset ($settings['catmailer_routine']) ? $settings['catmailer_routine'] : $old_settings['catmailer_routine'];
    if (($catmailer_routine == 'smtp'))
    {
        // Work-out return the 1th mail domain from a poassible textblock
        $pattern = '#https?://([A-Z0-9][^:][A-Z.0-9_-]+[a-z]{2,6})#ix';
        $catmailer_smtp_host = (isset ($settings['catmailer_smtp_host'])) ? $settings['catmailer_smtp_host'] : $old_settings['catmailer_smtp_host'];
        if (preg_match($pattern, $catmailer_smtp_host, $array))
        {
             $catmailer_smtp_host = $array [0];
        }
        if ((isset ($catmailer_smtp_host)))
        {
             if ((isset ($catmailer_smtp_host)) && ($catmailer_smtp_host != ''))
             {
                  $settings['catmailer_smtp_host'] = $catmailer_smtp_host;
             }
             else
             {
                  $err_msg[] = $backend->lang()->translate('You must enter details for the following fields').': '.$backend->lang()->translate('SMTP Host');
             }
        }
        // Work-out if SMTP authentification should be checked
        $settings['catmailer_smtp_auth'] = $settings['catmailer_smtp_auth']=='' ? 'false' : 'true';
        if ($settings['catmailer_smtp_auth'] == 'true')
        {
            // later change min and max lenght with variables
            $pattern = '/^[a-zA-Z0-9_]{4,30}$/';
            $catmailer_smtp_username = (isset ($settings['catmailer_smtp_username'])) ? $settings['catmailer_smtp_username'] : $old_settings['catmailer_smtp_username'];
            if (($catmailer_smtp_username == '') && !preg_match($pattern, $catmailer_smtp_username))
            {
                 $err_msg[] = $backend->lang()->translate('SMTP').': '.$backend->lang()->translate('Username or password incorrect');
            }
            else
            {
                 $settings['catmailer_smtp_username'] = $catmailer_smtp_username;
            }
            // receive password vars and calculate needed action
            $pattern = '/[^'.$admin->password_chars.']/';
            $current_password = $val->sanitizePost('catmailer_smtp_password');
            $current_password = ($current_password == null ? '' : $current_password);
            if (($current_password == ''))
            {
                 $err_msg[] = $backend->lang()->translate('SMTP').': '.$backend->lang()->translate('Username or password incorrect');
            }
            elseif (preg_match($pattern, $current_password))
            {
                 $err_msg[] = $backend->lang()->translate('Invalid password chars used, valid chars are: a-z\A-Z\0-9\_\-\!\#\*\+');
            }
        }
        // If SMTP-Authentification is disabled delete USER and PASSWORD for securityreasons
        else {
             $settings['catmailer_smtp_username'] = '-';
             $settings['catmailer_smtp_password'] = '-';
        }
    }
    if(!count($err_msg)) {
        saveSettings($settings);
    }
}

/**
 *
 **/
function saveGroup($backend,$group) {
    global $groups;
    $settings     = array();
    $val          = CAT_Helper_Validate::getInstance();
    foreach ( $groups[$group] as $key )
    {
        $settings[$key] = $val->sanitizePost($key);
    }
    saveSettings($settings);
}   // end function saveGroup()

/**
 *
 **/
function saveSettings($settings) {
    global $database;
    global $groups, $allow_tags_in_fields, $allow_empty_values, $boolean, $numeric;
    $old_settings = getSettingsTable();
    foreach($settings as $key => $value)
    {
        $value = trim($value);
        // allow HTML?
        if (!in_array($key, $allow_tags_in_fields))
            $value = strip_tags($value);
        // check boolean
        if (in_array($key,$boolean))
            $value = ( ! $value || $value == '' ) ? 'false' : 'true';
        // check numeric
        if (in_array($key,$numeric))
            if( !is_numeric($value) )
                continue;
        // function for this special item?
        if(function_exists('check_'.$key))
        {
            $func  = 'check_'.$key;
            $value = $func($value,$old_settings[$key]);
        }
        if ( $value != '' || in_array($key,$allow_empty_values) )
        {
            $database->query(sprintf(
                'UPDATE `%ssettings` SET `value`=\'%s\' WHERE `name`=\'%s\'',
                CAT_TABLE_PREFIX, $value, $key
            ));
        }
    }
}   // end function saveSettings()

/**
 * special function to sanitize token lifetime (-1<value<10000)
 **/
function check_token_lifetime($value,$oldvalue) {
    return ( $value > -1 && $value < 10000 )
        ? $value
        : $oldvalue;
}

/**
 * special function to sanitize redirect timer (-1<value<10000)
 **/
function check_redirect_timer($value,$oldvalue) {
    return ( $value > -1 && $value < 10000 )
        ? $value
        : $oldvalue;
}

/**
 * special function to sanitize page_level_limit (0<value<10)
 **/
function check_page_level_limit($value,$oldvalue) {
    return ( $value >= 1 && $value < 10 )
        ? $value
        : $oldvalue;
}

/**
 * special function to sanitize max login attempts (0<value<10)
 **/
function check_max_attempts($value,$oldvalue) {
    return ( $value >= 1 && $value < 10 )
        ? $value
        : $oldvalue;
}

/**
 *
 **/
function check_pages_directory($value,$oldvalue) {
    $bad     = array('"','`','!','@','#','$','%','^','&','*','=','+','|',';',':',',','?');
    $value   = str_replace($bad, '', $value);
    $value   = str_replace('\\', '/', $value);
    $pattern = '#[/][a-z,0-9_-]+#';
    preg_match($pattern, $value, $array);
    $value   = (isset($array['0']) ? $array['0'] : $oldvalue);
    return $value;
}

/**
 *
 **/
function check_sec_anchor($value,$oldvalue) {
    if(!empty($value))
    {
         // must begin with a letter
         $pattern = '/^[a-z][a-z_0-9]*$/i';
         if(!preg_match($pattern, $value, $array))
             return $oldvalue;
         return $value;
    }
    return $oldvalue;
}

/**
 * remove home folders for all users if the option is changed to "false"
 **/
function check_home_folders($value,$oldvalue) {
    if ( !$value && $oldvalue ) {
        global $database;
        $database->query(sprintf(
            'UPDATE `%susers` SET `home_folder` = \'\';',
            CAT_TABLE_PREFIX
        ));
    }
    return $value;
}

/**
 *
 **/
function check_er_level($value,$oldvalue) {
    $ER_LEVELS = CAT_Registry::get('ER_LEVELS','array');
    return
         (isset ($value) && (array_key_exists($value, $ER_LEVELS)))
       ? intval($value)
       : $oldvalue;
}

/**
 *
 **/
function check_frontend_signup($value,$oldvalue) {
    global $database;
    if (($result = $database->query(sprintf('SELECT count(*) AS `tcount` FROM `%sgroups`',CAT_TABLE_PREFIX))) && ($result->numRows() > 0))
    {
        $row = $result->fetchRow();
        return
             ($value > 1) && ($value <= $row['tcount'])
           ? intval($value)
           : $oldvalue
           ;
    }
}

