<?php
// this file just provides shared socket opening code
function open_socket()
{
	$sock = socket_create(AF_UNIX, SOCK_STREAM, 0);
	if ($sock === false) {
		return false;
	} else {
		if(socket_connect($sock, '/tmp/GITHUB_DEPLOY_MAKER')){
			socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 15, 'usec' => 0));
			return $sock;
		}
		else{
			return false;
		}
	}
}
