<?php
/**
 * @var \App\View\AppView $this
 * @var string[] $stores
 */
?>

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
