<?php

	echo 'QloneQiller - Version 3.0.0.0 - Made by Me'."\n";
	
	//================================================================
	// Settings
	//================================================================

	$input  = 'QQ_input.txt';
	$output = 'QQ_output.txt';
	define("SHORTHASHSIZE", 512);

	//================================================================
	// Script
	//================================================================
	
	$timeStart = microtime(true);

	$files     = array();
	$curDir    = getcwd();
	$inputFile = @file($input);
	
	if ( $inputFile === FALSE ) {
		die("There sould be $input file with dir at each line.");
	}
	
	echo 'Collecting files'."\n";
	foreach ( $inputFile as $line ) {
		$files = array_merge($files, getFilesFromDir($line));
	}
	
	if ( count( $files ) < 2 ) {
		die("There sould be more than 1 file.");
	}		
	
	foreach ( $files as &$file ) {
		$file = new file($file);
	}
	
	file::$type = "fileSize";
	echo 'Zero sort'."\n";
	sort($files, SORT_STRING);
	echo 'Zero deleteUnique'."\n";
	$files = deleteUnique($files);	
	
	file::$type = "shortHash";
	echo 'First sort'."\n";
	sort($files, SORT_STRING);
	echo 'First deleteUnique'."\n";
	$files = deleteUnique($files);
	
	file::$type = "fullHash";
	echo 'Second sort'."\n";
	sort($files, SORT_STRING);	
	echo 'Second deleteUnique'."\n";
	$files = deleteUnique($files);
	
	echo 'Outputing'."\n";
	chdir($curDir); // getFilesFromDir can change cDir. So rewind.
	file_put_contents($output, "============================ Result ============================\r\n");
	
	$lastIndex = count($files) - 1;
	for ( $i = 0; $i < $lastIndex; $i++ ) {
		file_put_contents($output, 'S '.$files[$i]->path."\r\n", FILE_APPEND);
		if ( (string) $files[$i] != $files[$i+1] ) {
			file_put_contents($output, "================================================================\r\n", FILE_APPEND);
		}
	}
	if ( $lastIndex > 0 ) {
		file_put_contents($output, 'S '.$files[$lastIndex]->path, FILE_APPEND);
	}
	
	$timeEnd = microtime(true);
	$time = $timeEnd - $timeStart;

	echo "Done in $time seconds. Thank you!\n";
	Sleep(3);
	
	//================================================================
	// Common
	//================================================================	
	
	class file {
	
		function __construct($path) {
			$this->shortHash   = FALSE;
			$this->fullHash    = FALSE;
			$this->path        = $path;
			$this->fileSize    = filesize($path);   
			if ( $this->fileSize === FALSE ) {
				$this->fileSize = 0;
			}
			$this->fileSize = str_pad($this->fileSize, 12, '0', STR_PAD_LEFT);
		}

		public function getShortHash() {
			if ( $this->shortHash === FALSE ) {
				$handle = fopen($this->path, 'rb');
				if ( $handle !== FALSE ) {
					$contents = fread($handle, SHORTHASHSIZE);
					fclose($handle);
					$this->shortHash = md5($contents);
				} else {
					$this->shortHash = '';
				}
			}
		}
		
		public function getFullHash() {
			if ( $this->fullHash === FALSE ) {
				$this->fullHash = md5_file($this->path);
				if ( $this->fullHash === FALSE ) {
					$this->fullHash = '';
				}
			}
		}
		
		public function __toString() {
			if ( self::$type == "fileSize" ) {
				return $this->fileSize;
			} else if ( self::$type == "shortHash" ) {
				$this->getShortHash();
				return $this->shortHash;
			} else if ( self::$type == "fullHash" ) {
				$this->getFullHash();
				return $this->fullHash;
			}
			return '';
		}
		
		public function useFullHash($var) {
			$this->useFullHash = $var;
		}
		
		static public $type = FALSE;
		
		public $path;
		public $fileSize;		
		public $shortHash;
		public $fullHash;
	}
	
	function& deleteUnique(&$arr) {
		$result    = array();
		$lastIndex = count($arr) - 1;
		for ( $i = 0; $i <= $lastIndex; $i++ ) {
			if ( $i === 0 ) {
				if ( (string) $arr[$i] == $arr[$i+1] ) {
					$result[] = $arr[$i];
				}
			} else if ($i === $lastIndex) {
				if ( (string) $arr[$i] == $arr[$i-1] ) {
					$result[] = $arr[$i];
				}			
			} else {
				if ( (string) $arr[$i] == $arr[$i-1] || (string) $arr[$i] == $arr[$i+1] ) {
					$result[] = $arr[$i];
				}
			}
		}
		return $result;
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
