<?php
/**
 * @var \App\View\AppView $this
 * @var string[] $stores
 * @var \Feedback\Model\Entity\FeedbackItem[]|null $feedbackItems
 */
?>

<div class="feedback index content large-9 medium-8 columns col-sm-8 col-12">

<h1>Feedback</h1>

<?php
$map = [
	\Feedback\Store\DatabaseStore::class => ['controller' => 'FeedbackItems'],
	\Feedback\Store\FilesystemStore::class => ['action' => 'listing'],
];
?>

<h2>Active Stores</h2>
<ul>
<?php
foreach ($stores as $store => $storeName) {
?>
	<li>
		<?php
		if (isset($map[$store])) {
			echo $this->Html->link($storeName, $map[$store]);
		} else {
			echo h($storeName);
		}
		?>
	</li>
  <?php
}
?>
</ul>

<?php if (!empty($feedbackItems)) { ?>
	<h2>New feedback</h2>
	<ul>
		<?php foreach ($feedbackItems as $feedbackItem) { ?>
		<li>
			<?php
			if ($feedbackItem->priority) {
				echo '[' . $feedbackItem::priorities($feedbackItem->priority) . '] ';
			}
			echo $this->Html->link($feedbackItem->subject, ['controller' => 'FeedbackItems', 'action' => 'view', $feedbackItem->id]);
			?>
			<?php if (!empty($feedbackItem->name)) {
				echo ' by ' . h($feedbackItem->name);
			} ?>

			<div><small><?= $this->Time->nice($feedbackItem->created) ?></small></div>
		</li>
		<?php } ?>
	</ul>

<?php } ?>

</div>
