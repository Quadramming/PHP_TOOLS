<?php
    $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

    $msg = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $len = strlen($msg);

	while ( true ) {
		socket_sendto($sock, $msg, $len, 0, '172.16.0.1', 777);
		time_nanosleep(0, 100);
	}
    socket_close($sock);
?>