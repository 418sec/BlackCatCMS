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
 *   @category        CAT_Modules
 *   @package         blackcatFilter
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

$module_description	  = 'Erweiter- und konfigurierbare Ausgabefilter für das Frontend';

$LANG = array(
    'Provided by (module)' => 'Definiert in (Modul)',
    'Enabled' => 'Aktiviert',
// --- filter descriptions ---
    'Obfuscates eMail addresses'
        => 'Maskiert eMail-Adressen',
    'Highlights search terms'
        => 'Hebt Suchbegriffe hervor',
    'Fixes date formats'
        => 'Korrigiert Datumsformate',
// --- legend ---
    'The code is located in a PHP file which resides in <tt>./&lt;Module&gt;/filter</tt>' => 'Der Code ist in einer PHP Datei enthalten, Verzeichnis ist <tt>./&lt;Module&gt;/filter</tt>',
    'The code is located in the database table <tt>&lt;Prefix&gt;mod_filter</tt>' => 'Der Code steht in der Datenbank, Tabelle <tt>&lt;Prefix&gt;mod_filter</tt>',
// --- map fieldnames to strings ---
    'file' => 'Datei',
    'name' => 'Filtername',
    'description' => 'Beschreibung',
    'code' => 'Code',
// --- new filter form ---
    'Please fill out the field: {{ name }}' => 'Bitte das Feld ausfüllen: {{ name }}',
    'Add entry' => 'Eintrag hinzufügen',
    'Add new filter' => 'Neuen Filter anlegen',
    'Filter name' => 'Filtername',
    'Filter description' => 'Beschreibung',
    'Code' => 'Code',
    'Filter is active' => 'Filter aktivieren',
    'Upload file' => 'Als Datei hochladen',
);