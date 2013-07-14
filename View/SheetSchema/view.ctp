<?php $this->extend('menu'); ?>
<h2><?php echo h($worksheets->title); ?></h2>

<p>
	<?php echo $this->Html->link(__d('sheet_schema', 'Open this spreadsheet'), $worksheets->link[0]['href']); ?>
</p>

<p>
	<?php echo __d('sheet_schema', 'Following tables will be created'); ?>:
</p>

<ul>
	<?php foreach ($worksheets->entry as $worksheet): ?>
		<?php if (empty($settings['ignored_worksheet']) || $worksheet->title != $settings['ignored_worksheet']): ?>
			<li><?php echo h($worksheet->title); ?></li>
		<?php endif; ?>
	<?php endforeach;  ?>
</ul>

