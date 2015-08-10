<?php

	echo 'FotoSorter - Version 1.0 - Made by Me'."\n";

	//================================================================
	// Settings
	//================================================================

	$input  = 'FotoSorter.ini';
	$types  = array("jpeg", "jpg");
	$format = "Y.m.d";
	$move   = TRUE;
	
	//================================================================
	// Script
	//================================================================
	
	include("jpgExif.php");	

	$inputFile = @file($input);
	$files     = array();
	$output    = FALSE;
	
	if ( $inputFile === FALSE ) {
		die("There sould be $input file with IN and OUT dirs.");
	}
	
	echo 'Collecting files'."\n";
	
	foreach ( $inputFile as $line ) {
		if ( strpos($line, "in: ") === 0 ) {
			$files = array_merge($files, getFilesFromDir(substr($line, 4)));
		}
		if ( strpos($line, "out: ") === 0 ) {
			$output = substr($line, 5);
		}
	}
	
	if ( $output === FALSE ) {
		die("There sould be 'out' directory.");
	}
	
	chdir($output);
	
	foreach ( $files as &$file ) {
		$fileInfo = pathinfo($file);
		if ( isset($fileInfo['extension']) ) {
			$ext = strtolower($fileInfo['extension']);
			if ( in_array( $ext, $types) ) {
				
				$exif = exif_read_data($file);
				if ( isset($exif['DateTime']) ) {
					$dir = exif_read_data($file)['DateTime'];
				}
				if ( isset($exif['DateTimeOriginal']) ) {
					$dir = exif_read_data($file)['DateTimeOriginal'];
				}
				if ( isset($dir) && strlen($dir) === 19 ) {
					$dir = str_replace(':', '.', substr($dir, 0, 10));
				} else {
					$dir = date($format, filectime($file));
				}
				
				$dir = explode('.', $dir);
				@mkdir( $dir[0] );
				@mkdir( $dir[0].'/'.$dir[1] );
				@mkdir( $dir[0].'/'.$dir[1].'/'.$dir[2] );
				$dir = $dir[0].'/'.$dir[1].'/'.$dir[2];

				$dest = $dir."/".$fileInfo['filename'].".".$ext;
				
				while( file_exists($dest) ) {
					$dest = $dir."/".$fileInfo['filename'].".".rand().".".$ext;
				}

				if ( $move === TRUE ) {
					rename($file, $dest);
				} else {
					copy($file, $dest);
				}
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
