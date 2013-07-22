<p class="error"><?php echo __d('cake_schema', 'Worksheet has some errors:'); ?></p>
<!-- form tag seems weird but it is because cake.generic.css reason -->
<form>
<div class="error">
	<?php foreach ($errors as $error): ?>
		<div class="error-message"><?php echo h($error); ?></div>
	<?php endforeach; ?>
</div>
</form>
