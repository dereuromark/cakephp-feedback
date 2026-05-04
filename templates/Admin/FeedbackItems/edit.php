<?php
/**
 * @var \App\View\AppView $this
 * @var \Feedback\Model\Entity\FeedbackItem $feedbackItem
 */
$cspNonce = (string)$this->getRequest()->getAttribute('cspNonce', '');
?>
<div class="row">
	<aside class="column large-3 medium-4 columns col-sm-4 col-12">
		<ul class="side-nav nav nav-pills flex-column">
			<li class="nav-item heading"><?= __d('feedback', 'Actions') ?></li>
			<li class="nav-item"><?= $this->Form->postButton(
				__d('feedback', 'Delete'),
				['action' => 'delete', $feedbackItem->id],
				[
					'class' => 'side-nav-item btn btn-link text-start w-100',
					'form' => [
						'class' => 'd-inline',
						'data-confirm-message' => __d('feedback', 'Are you sure you want to delete # {0}?', $feedbackItem->id),
					],
				]
				) ?></li>
			<li class="nav-item"><?= $this->Html->link(__d('feedback', 'List Feedback Items'), ['action' => 'index'], ['class' => 'side-nav-item']) ?></li>
		</ul>
	</aside>
	<div class="column-responsive column-80 form large-9 medium-8 columns col-sm-8 col-12">
		<div class="feedbackItems form content">
			<h1><?= __d('feedback', 'Feedback Items') ?></h1>

			<h2><?php echo h($feedbackItem->url_short); ?></h2>
			<?= $this->Form->create($feedbackItem) ?>
			<fieldset>
				<legend><?= __d('feedback', 'Edit Feedback Item') ?></legend>
				<?php
					echo $this->Form->control('subject');
					echo $this->Form->control('feedback');
					echo $this->Form->control('name');
					echo $this->Form->control('email');
					echo $this->Form->control('priority', ['options' => $feedbackItem::priorities()]);
					echo $this->Form->control('status', ['options' => $feedbackItem::statuses()]);
				?>
			</fieldset>
			<?= $this->Form->button(__d('feedback', 'Submit')) ?>
			<?= $this->Form->end() ?>
		</div>
	</div>
</div>
<script<?= $cspNonce !== '' ? ' nonce="' . h($cspNonce) . '"' : '' ?>>
document.querySelectorAll('form[data-confirm-message]').forEach(function(form) {
	form.addEventListener('submit', function(e) {
		if (!confirm(this.dataset.confirmMessage)) {
			e.preventDefault();
		}
	});
});
</script>
