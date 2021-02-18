<?php
/**
 * @var \App\View\AppView $this
 * @var \Feedback\Model\Entity\FeedbackItem $feedbackItem
 */
?>
<div class="row">
	<aside class="column actions large-3 medium-4 col-sm-4 col-xs-12">
		<ul class="side-nav nav nav-pills flex-column">
			<li class="nav-item heading"><?= __('Actions') ?></li>
			<li class="nav-item"><?= $this->Html->link(__('Edit {0}', __('Feedback Item')), ['action' => 'edit', $feedbackItem->id], ['class' => 'side-nav-item']) ?></li>
			<li class="nav-item"><?= $this->Form->postLink(__('Delete {0}', __('Feedback Item')), ['action' => 'delete', $feedbackItem->id], ['confirm' => __('Are you sure you want to delete # {0}?', $feedbackItem->id), 'class' => 'side-nav-item']) ?></li>
			<li class="nav-item"><?= $this->Html->link(__('List {0}', __('Feedback Items')), ['action' => 'index'], ['class' => 'side-nav-item']) ?></li>
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

					echo $this->Form->postLink($value, ['action' => 'edit', $feedbackItem->id], ['data' => ['status' => $key], 'class' => 'btn btn-' . $class, 'confirm' => 'Sure?']);
					?>
				<?php } ?>
			<?php } ?>

			<?php
			$screenshot = $feedbackItem->data['screenshot'] ?? null;
			unset($feedbackItem->data['screenshot']);
			?>

			<div class="text">
				<strong><?= __('Feedback') ?></strong>
				<blockquote>
					<?= $this->Text->autoParagraph(h($feedbackItem->feedback)); ?>
				</blockquote>
			</div>

			<table class="table table-striped">
				<tr>
					<th><?= __('Sid') ?></th>
					<td><?= h($feedbackItem->sid) ?></td>
				</tr>
				<tr>
					<th><?= __('Url') ?></th>
					<td><?= $this->Html->link($feedbackItem->url_short, $feedbackItem->url) ?></td>
				</tr>
				<tr>
					<th><?= __('Name') ?></th>
					<td><?= h($feedbackItem->name) ?></td>
				</tr>
				<tr>
					<th><?= __('Email') ?></th>
					<td><?= h($feedbackItem->email) ?></td>
				</tr>
				<tr>
					<th><?= __('Subject') ?></th>
					<td><?= h($feedbackItem->subject) ?></td>
				</tr>
				<tr>
					<th><?= __('Data') ?></th>
					<td><pre><?= print_r(h($feedbackItem->data), true); ?></pre></td>
				</tr>
				<tr>
					<th><?= __('Priority') ?></th>
					<td><?= $feedbackItem->priority ? $feedbackItem::priorities($feedbackItem->priority) : '' ?></td>
				</tr>
				<tr>
					<th><?= __('Status') ?></th>
					<td><?= $feedbackItem->status !== null ? $feedbackItem::statuses($feedbackItem->status) : '' ?></td>
				</tr>
				<tr>
					<th><?= __('Created') ?></th>
					<td><?= $this->Time->nice($feedbackItem->created) ?></td>
				</tr>
			</table>

			<div class="screenshot">
				<?php
				if ($screenshot) {
					$img = '<img class="screenshot responsive img-fluid" src="data:image/png;base64,' . $screenshot . '"/>';
					echo $this->Html->link($img, ['plugin'=>'Feedback','controller'=>'FeedbackItems','action'=>'viewimage', $feedbackItem->id], ['escapeTitle' => false, 'target' => '_blank']);
				}
				?>
			</div>

		</div>
	</div>
</div>
