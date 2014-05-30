<?php
require_once(__DIR__ . '/socket.inc.php');

$post = file_get_contents('php://input');

if ($post) {
	file_put_contents('/tmp/github_hook_' . getmypid() , $post); #debug

	$json = json_decode($post, true);

	if (file_exists(__DIR__ . '/config.inc.php')) {
		require_once(__DIR__ . '/config.inc.php');
	}

	if (array_key_exists('head_commit', $json)) {
		foreach ($targets as $repo => $target) {
			foreach ($target as $ref => $path) {
				error_log(var_export(array(
					'$ref' => $ref,
					'$repo' => $repo,
					'$path' => $path,
					'$json[ref]' => $json['ref'],
					'$json[repository][url]' => $json['repository']['url']
				), true));
				if ($json['ref'] == $ref && $json['repository']['url'] == $repo) {
					$socket = open_socket();
					socket_write($sock, $path);
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