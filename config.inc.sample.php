<?php
$queue_path = '/path/to/github_deploy_maker_queue/';

$targets = array(
	'https://github.com/<youraccount>/<yourrepo>' => array(
		'refs/heads/master' =>  '/local/path/to/project/root/'
	)
);
