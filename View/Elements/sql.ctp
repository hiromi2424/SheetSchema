<script type="syntaxhighlighter" class="brush: sql; toolbar: false;"><?php
foreach ($sql as $s) {
	if (strpos($s, 'CREATE TABLE') !== false) {
		$s = preg_replace("/,\n?/s", ",\n", $s);
		$s = preg_replace("/\)\s*\t;/", "\n);", $s);
	}
	echo trim($s, "\r\n"), "\n";
}
?></script>
