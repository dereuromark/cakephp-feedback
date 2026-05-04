<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="paginator">
	<ul class="pagination">
		<?= $this->Paginator->first('<< ' . __d('feedback', 'first')) ?>
		<?= $this->Paginator->prev('< ' . __d('feedback', 'previous')) ?>
		<?= $this->Paginator->numbers() ?>
		<?= $this->Paginator->next(__d('feedback', 'next') . ' >') ?>
		<?= $this->Paginator->last(__d('feedback', 'last') . ' >>') ?>
	</ul>
	<p><?= $this->Paginator->counter('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total') ?></p>
</div>
