<?php
// FileManager Configuration

// start-directory for the File-Manager (no trailing slash)
$basepath = '.';

// list of File-Names/File-Extensions not shown
$hiddenFiles = array('.cache', '.htaccess');
$badExtensions = array('php', 'exe');

// Language-Labels
$labels = array (
	'level_up' => 'übergeordnetes Verzeichnis',
	'dir_not_writable' => 'Verzeichnis ist schreibgeschützt',
	'file_not_writable' => 'Datei konnte nicht angelegt werden',
	'create_new_directory' => 'neues Verzeichnis anlegen',
	'directory_name' => 'Verzeichnisname',
	'upload_file' => 'Datei hochladen',
	'delete_file' => 'Datei löschen',
	'file_not_deletable' => 'Datei nicht löschbar',
	'get_filepath' => 'Dateipfad übernehmen',
	'really_delete' => 'wirklich löschen',
	'file_type_not_allowed' => 'Dateityp ist nicht erlaubt',
);
