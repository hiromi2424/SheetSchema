<?php $this->extend('menu'); ?>
<h2><?php echo __d('sheet_schema', 'Sheet List'); ?></h2>

<?php echo __d('sheet_schema', 'Filter'); ?>: <input type="text" value="" id="SheetSchema-filter">

<table>
<thead>
	<tr>
		<th><?php echo __d('sheet_schema', 'Title'); ?></th>
		<th><?php echo __d('sheet_schema', 'Action'); ?></th>
	</tr>
</thead>
<tbody id="SheetSchema-sheets">
	<?php foreach ($spreadsheets->entry as $spreadsheet): ?>
	<tr title="<?php echo $spreadsheet->title; ?>">
		<td><?php echo $this->Html->link($spreadsheet->title, array('action' => 'view', $spreadsheet->key)); ?></td>
		<td><?php echo $this->Html->link(__d('sheet_schema', 'Process'), array('action' => 'process', $spreadsheet->key)); ?></td>
	</tr>
	<?php endforeach;  ?>
</tbody>

</table>


<?php echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js'); ?>

<script type="text/javascript">

$(function() {
	$('#SheetSchema-filter').keyup(function() {
		var filterText = $(this).val();
		$('#SheetSchema-sheets tr').each(function(idx, elem) {
			$elem = $(elem);
			if ($elem.attr("title").match(filterText)) {
				$elem.show();
			} else {
				$elem.hide();
			}
		});
	});
});

</script>

