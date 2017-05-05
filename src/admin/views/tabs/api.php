<code>
	<pre><?php
		echo htmlspecialchars(file_get_contents( dirname(__FILE__) . '/api.txt' ), ENT_COMPAT, 'UTF-8');?>
	</pre>
</code>