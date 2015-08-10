<?php

	echo 'MP3TagRemover - Version 1.0 - Made by Me'."\n";

	//================================================================
	// Settings
	//================================================================

	require_once('getID3-1.9.9/getid3/getid3.php');
	require_once('getID3-1.9.9/getid3/write.php');	
	
	$input  = 'mp3_input.txt';

	//================================================================
	// Script
	//================================================================
	
	$curDir    = getcwd();
	$inputFile = @file($input);
	$files     = array();
	
	if ( $inputFile === FALSE ) {
		die("There sould be $input file with dir at each line.");
	}
	
	echo 'Collecting files'."\n";
	foreach ( $inputFile as $line ) {
		$files = array_merge($files, getFilesFromDir($line));
	}
	
	chdir($curDir); // getFilesFromDir can change cDir. So rewind.

	$getID3    = new getID3;
	$tagwriter = new getid3_writetags;	
	$tagwriter->overwrite_tags    = true;
	$tagwriter->remove_other_tags = true;	
	
	foreach ( $files as &$file ) {
		if ( strripos($file, '.mp3') === (strlen($file) - 4) ) {
			$tagwriter->filename          = $file;
			if ( ! $tagwriter->WriteTags() ) {
				echo $file.' FAILDE'."\n";
			}
		}
	}
	
	echo 'Done'."\n";
	
	//================================================================
	// Common functions
	//================================================================	
	
	function getFilesFromDir($dir) {		
		$dir = trim($dir);
		$arrfiles = array();
		if (is_dir($dir)) {
			if ($handle = opendir($dir)) {
				chdir($dir);
				while (false !== ($file = readdir($handle))) { 
					if ($file != "." && $file != "..") { 
						if (is_dir($file)) { 
							$arr = getFilesFromDir($file, false);
							foreach ($arr as $value) {
								$arrfiles[] = $dir."/".$value;
							}
						} else {
							$arrfiles[] = $dir."/".$file;
						}
					}
				}
				chdir("../");
			}
			closedir($handle);
		}
		return $arrfiles;
	}
	
?>
