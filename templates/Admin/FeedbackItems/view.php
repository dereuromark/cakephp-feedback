<?php
/**
 * @var \App\View\AppView $this
 * @var \Feedback\Model\Entity\FeedbackItem $feedbackItem
 */
$cspNonce = (string)$this->getRequest()->getAttribute('cspNonce', '');
?>
<div class="row">
	<aside class="column actions large-3 medium-4 col-sm-4 col-xs-12">
		<ul class="side-nav nav nav-pills flex-column">
			<li class="nav-item heading"><?= __d('feedback', 'Actions') ?></li>
			<li class="nav-item"><?= $this->Html->link(__d('feedback', 'Edit {0}', __d('feedback', 'Feedback Item')), ['action' => 'edit', $feedbackItem->id], ['class' => 'side-nav-item']) ?></li>
			<li class="nav-item"><?= $this->Form->postButton(__d('feedback', 'Delete {0}', __d('feedback', 'Feedback Item')), ['action' => 'delete', $feedbackItem->id], [
				'class' => 'side-nav-item btn btn-link text-start w-100',
				'form' => [
					'class' => 'd-inline',
					'data-confirm-message' => __d('feedback', 'Are you sure you want to delete # {0}?', $feedbackItem->id),
				],
			]) ?></li>
			<li class="nav-item"><?= $this->Html->link(__d('feedback', 'List {0}', __d('feedback', 'Feedback Items')), ['action' => 'index'], ['class' => 'side-nav-item']) ?></li>
		</ul>
	</aside>
	<div class="column-responsive column-80 content large-9 medium-8 col-sm-8 col-xs-12">
		<div class="feedbackItems view content">
			<h1><?= h($this->Text->truncate($feedbackItem->subject)) ?></h1>
			<?php if ($feedbackItem->status === $feedbackItem::STATUS_NEW) { ?>
				<?php
				$classes = [
					'primary',
					'secondary',
				];
				$statuses = $feedbackItem::statuses();
				?>
				<?php foreach ($statuses as $key => $value) { ?>
					<?php if ($key === $feedbackItem::STATUS_NEW) {
						continue;
					}
					$class = array_shift($classes) ?: 'default';

					echo $this->Form->postButton($value, ['action' => 'edit', $feedbackItem->id], [
						'data' => ['status' => $key],
						'class' => 'btn btn-' . $class,
						'form' => [
							'class' => 'd-inline',
							'data-confirm-message' => 'Sure?',
						],
					]);
					?>
				<?php } ?>
			<?php } ?>

			<?php
			$screenshot = $feedbackItem->data['screenshot'] ?? null;
			unset($feedbackItem->data['screenshot']);
			?>

			<div class="text">
				<strong><?= __d('feedback', 'Feedback') ?></strong>
				<blockquote>
					<?= $this->Text->autoParagraph(h($feedbackItem->feedback)); ?>
				</blockquote>
			</div>

			<table class="table table-striped">
				<tr>
					<th><?= __d('feedback', 'Sid') ?></th>
					<td><?= h($feedbackItem->sid) ?></td>
				</tr>
				<tr>
					<th><?= __d('feedback', 'Url') ?></th>
					<td><?= $this->Html->link($feedbackItem->url_short, $feedbackItem->url) ?></td>
				</tr>
				<tr>
					<th><?= __d('feedback', 'Name') ?></th>
					<td><?= h($feedbackItem->name) ?></td>
				</tr>
				<tr>
					<th><?= __d('feedback', 'Email') ?></th>
					<td><?= h($feedbackItem->email) ?></td>
				</tr>
				<tr>
					<th><?= __d('feedback', 'Subject') ?></th>
					<td><?= h($feedbackItem->subject) ?></td>
				</tr>
				<tr>
					<th><?= __d('feedback', 'Data') ?></th>
					<td><pre><?= print_r(h($feedbackItem->data), true); ?></pre></td>
				</tr>
				<tr>
					<th><?= __d('feedback', 'Priority') ?></th>
					<td><?= $feedbackItem->priority ? $feedbackItem::priorities($feedbackItem->priority) : '' ?></td>
				</tr>
				<tr>
					<th><?= __d('feedback', 'Status') ?></th>
					<td><?= $feedbackItem->status !== null ? $feedbackItem::statuses($feedbackItem->status) : '' ?></td>
				</tr>
				<tr>
					<th><?= __d('feedback', 'Created') ?></th>
					<td><?= $this->Time->nice($feedbackItem->created) ?></td>
				</tr>
			</table>

			<div class="screenshot">
				<?php
				if ($screenshot) {
					$img = '<img class="screenshot responsive img-fluid" src="data:image/png;base64,' . h($screenshot) . '"/>';
					echo $this->Html->link($img, ['plugin'=>'Feedback','controller'=>'FeedbackItems','action'=>'viewimage', $feedbackItem->id], ['escapeTitle' => false, 'target' => '_blank']);
				}
				?>
			</div>

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
