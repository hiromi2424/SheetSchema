<?php

$this->Html->css('//alexgorbatchev.com/pub/sh/current/styles/shThemeDefault.css', null, array('inline' => false));
$this->Html->script(array(
	'//alexgorbatchev.com/pub/sh/current/scripts/shCore.js',
	'//alexgorbatchev.com/pub/sh/current/scripts/shBrushSql.js'
), array('inline' => false));
?>
<script type="text/javascript">
SyntaxHighlighter.all();
</script>

<?php $this->start('css'); ?>
<style type="text/css">
div.syntaxhighlighter table {
	width: auto;
}
</style>
<?php $this->end('css'); ?>
