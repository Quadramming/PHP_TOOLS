<?php
	
	echo 'QloneQiller - Version 1.0.0.0 - Made by Me'."\n";
	
	//================================================================
	// Settings
	//================================================================

	$input  = 'QQ_input.txt';
	$output = 'QQ_output.txt';

	//================================================================
	// Script
	//================================================================
	
	$files     = array();
	$curDir    = getcwd();	
	$inputFile = @file($input);
	
	if ( $inputFile == FALSE ) {
		die("There sould be $input file with dir at each line.");
	}

	foreach ( $inputFile as $line ) {
		$files = array_merge($files, getFilesFromDir($line));
	}

	if ( count( $files ) < 3 ) {
		die("There sould be more than 2 files.");
	}	
	
	asort($files, SORT_STRING);
	
	chdir($curDir); // getFilesFromDir can change cDir. So rewind.
	
	file_put_contents($output, "============================ Result ============================\r\n");
	
	$files    = new ArrayObject($files);
	$iPrev    = getIter($files, 0);
	$iCurrent = getIter($files, 1);
	$iNext    = getIter($files, 2);
	
	// Fisrt element
	if ( $iPrev->current() == $iCurrent->current() ) {
		file_put_contents($output, 'S '.$iPrev->key()."\r\n", FILE_APPEND);
	}
	// Middle elements
	while ( $iNext->valid() ) {
		if ( $iCurrent->current() == $iPrev->current() || $iCurrent->current() == $iNext->current() ) {
			file_put_contents($output, 'S '.$iCurrent->key()."\r\n", FILE_APPEND);
			if ( $iCurrent->current() != $iNext->current() ) {
				file_put_contents($output, "================================================================\r\n", FILE_APPEND);
			}			
		}
		$iPrev->next();
		$iCurrent->next();
		$iNext->next();
	}
	// Last element
	if ( $iCurrent->current() == $iPrev->current() ) {
		file_put_contents($output, 'S '.$iCurrent->key()."\r\n", FILE_APPEND);
	}
	
	//================================================================
	// Common functions
	//================================================================	
	
	function getIter($array, $offset) {
		$iter = $array->getIterator();
		for ( $i = 0; $i < $offset; $i++ ) {
			$iter->next();
		}
		return $iter;
	}
	
	function getFilesFromDir($dir, $isFirst = true) {
		$dir = trim($dir);
		$arrfiles = array();
		if (is_dir($dir)) {
			if ($handle = opendir($dir)) {
				chdir($dir);
				while (false !== ($file = readdir($handle))) { 
					if ($file != "." && $file != "..") { 
						if (is_dir($file)) { 
							$arr = getFilesFromDir($file, false);
							foreach ($arr as $key => $value) {
								$fullName = $dir."/".$key;
								if ( $isFirst ) {
									$arrfiles[ $fullName ] = md5_file($fullName);
								} else {
									$arrfiles[ $fullName ] = "";
								}
							}
						} else {
							$fullName = $dir."/".$file;
							if ( $isFirst ) {
								$arrfiles[ $fullName ] = md5_file($fullName);
							} else {
								$arrfiles[ $fullName ] = "";
							}
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
