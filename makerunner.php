<?php
if (file_exists(__DIR__ . '/config.inc.php')) {
	require_once(__DIR__ . '/config.inc.php');
}

if (!isset($queue_path)) {
	$queue_path = __DIR__ . '/.queue/';
	mkdir($queue_path);
}

while(1) {
	if ($files = glob($queue_path . '/github_cmd_*')) {
		foreach ($files as $file) {
			$requested_path = trim(file_get_contents($file));
			echo "Got request to make $requested_path\n";
			foreach ($targets as $repo => $target) {
				foreach ($target as $ref => $path) {
					if ($path == $requested_path) { 
						system("cd $path; git pull; make");
					}
				}
			}
			unlink($file);
		}
	}
	sleep(60);
}
