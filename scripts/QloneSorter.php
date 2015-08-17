<?php
	
	echo 'QloneSorter - Version 1.0.0.0 - Made by Me'."\n";
	
	//================================================================
	// Settings
	//================================================================

	$input  = 'QQ_output.txt';
	$output = 'QQ_output.txt';

	//================================================================
	// Script
	//================================================================
	
	$inputFile = @file($input);
	
	if ( $inputFile == FALSE ) {
		die("There sould be $input file.");
	}

	$blocks   = array();
	$curBlock = -1;
	
	foreach ( $inputFile as $line ) {
		if ( $line[0] === '=' ) {
			$curBlock++;
			$blocks[$curBlock] = array();
		} else {
			$blocks[$curBlock][] = trim($line);
		}
	}
	
	foreach ( $blocks as &$block ) {
		sort($block, SORT_STRING);
	}
	
	file_put_contents($output, "========================== CloneSorter =========================\r\n");
	file_put_contents($output, "============================ Result ============================", FILE_APPEND);
	foreach ( $blocks as &$block ) {
		foreach ( $block as $key => $value ) {
			if ( $key === 0 ) {
				file_put_contents($output, "\r\n================================================================", FILE_APPEND);
				file_put_contents($output, "\r\n".$value, FILE_APPEND);
			} else {
				$value[0] = 'D';
				file_put_contents($output, "\r\n".$value, FILE_APPEND);
			}
		}
	}
	print_r($blocks);
	
	echo "Done. Thank you!\n";
	Sleep(3);
?>
