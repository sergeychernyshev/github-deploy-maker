<?php
$post = file_get_contents('php://input');

if ($post) {
	$json = json_decode($post, true);

	if (file_exists(__DIR__ . '/config.inc.php')) {
		require_once(__DIR__ . '/config.inc.php');
	}

	if (!isset($queue_path)) {
		$queue_path = __DIR__ . '/.queue/';
		mkdir($queue_path);
	}

	if (array_key_exists('head_commit', $json)) {
		foreach ($targets as $repo => $target) {
			foreach ($target as $ref => $path) {
				if ($json['ref'] == $ref && $json['repository']['url'] == $repo) {
					# file acts as instruction to trigger make
					file_put_contents($queue_path . '/github_cmd_' . getmypid(), $path);
				}
			}
		}
	}
} else {
	?>
	<h1>GitHub Deploy Maker</h1>
	<p>
	This hook is used to trigger deployments based on GitHub commits.
	</p>
	<p>
	Go to https://github.com/&lt;youraccount&gt;/&lt;yourrepo&gt;/settings/hooks and add the URL of this page to receive application/json payload and listen for "push" events (just paste the url and use default settings).
	</p>
	<?php
	if (!file_exists(__DIR__ . '/config.inc.php') || !is_array($targets)) {
	?>
	<p>
	You also need to create a config.inc.php file in this folder and set $targets array with repo URLs as keys and arrays of refs =&gt; local filesystem paths as values, for example:
<pre style="background-color: #eee; border: 1px solid silver; padding: 1em">
&lt;php
$targets = array(
	'https://github.com/sergeychernyshev/showslow' =&gt; array( 'refs/heads/master' =&gt;  '/path/to/showslow-docroot/' ),
	'https://github.com/sergeychernyshev/showslow' =&gt; array( 'refs/heads/dev' =&gt;  '/path/to/dev-showslow-docroot/' )
);
</pre>
	</p>
	<?php
	}
}
