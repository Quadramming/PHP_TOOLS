<?php

	echo 'QloneQiller - Version 2.0 - Made by Me'."\n";

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
	
	if ( count( $files ) < 2 ) {
		die("There sould be more than 1 file.");
	}		
	
	foreach ( $files as &$file ) {
		$file = new file($file);
	}
	
	sort($files, SORT_STRING);
	
	chdir($curDir); // getFilesFromDir can change cDir. So rewind.
	
	file_put_contents($output, "============================ Result ============================\r\n");
	
	$lastIndex = count($files) - 1;
	for ( $i = 0; $i <= $lastIndex; $i++ ) {
		if ( $i == 0 ) {
			if ( $files[$i]->isSameHash($files[$i+1]) ) {
				file_put_contents($output, 'S '.$files[$i]->path."\r\n", FILE_APPEND);
			}
		} else if ($i == $lastIndex) {
			if ( $files[$i]->isSameHash($files[$i-1]) ) {
				file_put_contents($output, 'S '.$files[$i]->path."\r\n", FILE_APPEND);
			}			
		} else {
			if ( $files[$i]->isSameHash($files[$i+1]) || $files[$i]->isSameHash($files[$i-1]) ) {
				file_put_contents($output, 'S '.$files[$i]->path."\r\n", FILE_APPEND);
				if ( ! $files[$i]->isSameHash($files[$i+1]) ) {
					file_put_contents($output, "================================================================\r\n", FILE_APPEND);
				}	
			}
		}
	}

	die("Done");
	
	//================================================================
	// Common
	//================================================================	
	
	class file {
		function __construct($path) {
			$this->path = $path;
			$this->hash = md5_file($path);
			if ( $this->hash == FALSE ) {
				$this->hash = "";
			}
		}
		
		public function __toString() {
			return $this->hash;
		}
		
		public function isSameHash($file) {
			return $file->hash === $this->hash;
		}
		
		public $path;
		public $hash;
	}	
	
	function getIter($array, $offset) {
		$iter = $array->getIterator();
		for ( $i = 0; $i < $offset; $i++ ) {
			$iter->next();
		}
		return $iter;
	}
	
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