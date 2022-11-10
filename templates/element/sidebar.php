<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;
use Feedback\Store\Priorities;

if (!Configure::read('Feedback')) {
	throw new RuntimeException('No Feedback plugin config found.');
}

// You can also include it manually with the rest of the CSS files in head section.
if (!Configure::read('Feedback.skipCss')) {
	echo $this->Html->css('Feedback.sidebar', ['block' => false]);
}

$icons = (array)Configure::read('Feedback.icons') + [
	'close' => 'fa fa-window-close',
	'screenshot' => 'fa fa-crosshairs', // formerly glyphicon glyphicon-screenshot
	'submit' => 'fa fa-envelope-o', // formerly glyphicon glyphicon-envelope
	'cancel' => 'fa fa-times', // formerly glyphicon glyphicon-remove
];

// This will be rendered with the rest of the JS files in the end part of body section.
echo $this->Html->script(
	[
		'Feedback.html2canvas/html2canvas', //html2canvas.js for screenshot function
		'Feedback.functions'
	], ['block' => true]
);

//Get config vars used in this view
$forceauthusername = Configure::read('Feedback.forceauthusername');
$forceemail = Configure::read('Feedback.forceemail');

$displayExisting = Configure::read('Feedback.displayExisting');

$enablecopybyemail = Configure::read('Feedback.enablecopybyemail');

$enableacceptterms = Configure::read('Feedback.enableacceptterms');
$termstext = '';
if ($enableacceptterms) {
	$termstext = Configure::read('Feedback.termstext') ? __d('feedback', 'When you submit, a screenshot (of only this website) will be taken to aid us in processing your feedback or bugreport.') : '';
}

$priorities = Priorities::getList();

$map = (array)Configure::read('Feedback.authMap') + [
	'username' => 'username',
	'email' => 'email',
];
if (isset($this->AuthUser)) {
	$name = $this->AuthUser->user($map['username']) ?: $this->AuthUser->user($map['username']) ?: '';
	$email = $this->AuthUser->user($map['email']) ?: $this->AuthUser->user($map['email']) ?: '';
} else {
	$name = '';
	$email = '';
	if (!empty($map['username'])) {
		$name = $this->request->getSession()->read($map['username']);
	}
	if (!empty($map['email'])) {
		$email = $this->request->getSession()->read($map['email']);
	}
}
?>

<script>
	//Create URL using cake's url helper, this is used in feedbackit-functions.js
	<?php $formUrl = $this->Url->build(['prefix' => false, 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'save'], ['fullBase' => true]); ?>
	window.formURL = '<?php echo $formUrl; ?>';
</script>

<div id="feedbackit-slideout">
	<?php echo $this->Html->image('Feedback.feedback.png');?>
</div>
<div id="feedbackit-slideout_inner">
	<div class="feedbackit-form-elements">
		<div class="pull-right float-right">
			<i class="tab-hide <?php echo $icons['close']; ?>" title="<?php echo __d('feedback', 'Hide this tab completely.'); ?>"></i>
		</div>
		<p>
			<?php echo __d('feedback','Send your feedback or bugreport!');?>
		</p>
		<form id="feedbackit-form" autocomplete="off">
			<?php if ($displayExisting !== false || empty($name)) { ?>
			<div class="form-group">
				<input
					type="text"
					name="name"
					id="feedbackit-name"
					maxlength="150"
					class="<?php if (!empty($name)) echo 'feedbackit-input'; ?> form-control"
					value="<?php echo $name; ?>"
					placeholder="<?php echo __d('feedback','Your name '); if( !$forceauthusername ) echo ' (optional)'; ?>"
					<?php if ($forceauthusername) echo 'required="required"'; ?>
					>
			</div>
			<?php } ?>

			<?php if ($displayExisting !== false || empty($email)) { ?>
			<div class="form-group">
				<input
					type="email"
					name="email"
					id="feedbackit-email"
					maxlength="150"
					class="<?php if (!empty($email)) echo 'feedbackit-input'; ?> form-control"
					value="<?php echo $email; ?>"
					placeholder="<?php echo __d('feedback','Your e-mail'); if( !$forceemail) echo ' (optional)'; ?>"
					<?php if ($forceemail) echo 'required="required"'; ?>
					>
			</div>
			<?php } ?>

			<div class="form-group">
				<input
					type="text"
					name="subject"
					id="feedbackit-subject"
					maxlength="150"
					class="feedbackit-input form-control"
					required="required"
					placeholder="<?php echo __d('feedback','Subject'); ?>"
					>
			</div>
			<div class="form-group">
				<textarea name="feedback" id="feedbackit-feedback" class="feedbackit-input form-control" required="required"
					placeholder="<?php echo __d('feedback','Feedback or suggestion'); ?>" rows="3"></textarea>
			</div>

			<?php if ($priorities) { ?>
			<div class="form-group">
				<?php echo $this->Form->select('priority', $priorities, ['id' => 'feedbackit-priority', 'class' => 'feedbackit-input form-control']); ?>
			</div>
			<?php } ?>

			<div class="form-group">
				<p>
					<button
						class="btn btn-info"
						data-loading-text="<?php echo __d('feedback','Click anywhere on website'); ?>"
						id="feedbackit-highlight"
						onclick="return false;">
						<i class="icon-screenshot icon-white"></i><span class="icon <?php echo $icons['screenshot']; ?>"></span> <?php echo __d('feedback','Highlight something'); ?>
					</button>
				</p>
				<div <?php if (!$enableacceptterms) echo 'style="display:none;"'; ?>>
					<label class="checkbox checkbox-inline">
						<input type="checkbox"
							   required id="feedbackit-okay"
								<?php
								if (!$enableacceptterms) {
								   echo 'class="isinvisible"';
								   echo 'checked="checked"';
								} else {
								   echo 'class="isvisible"';
								}
								?>
							>
						<?php
						$confirmation = '<b><a id="feedbackit-okay-message" href="#" onclick="return false;" data-toggle="tooltip" title="' . h($termstext) . '">'. __d('feedback','this'). '</a></b>';
						?>
						<?php echo __d('feedback','I am okay with {0}.', $confirmation); ?>
					</label>
				</div>
				<?php
				if ($enablecopybyemail) {
				?>
				<div class="form-group">
					<label class="checkbox checkbox-inline">
						<input type="checkbox" name="copyme" id="feedbackit-copyme" >
						<?php echo __d('feedback','E-mail me a copy'); ?>
					</label>
				</div>
				<?php
				}
				?>

				<div class="btn-group">
					<button class="btn btn-success" id="feedbackit-submit" disabled="disabled" type="submit"><i class="icon-envelope icon-white"></i><span class="icon <?php echo $icons['submit']; ?>"></span> <?php echo __d('feedback','Submit'); ?></button>
					<button class="btn btn-danger" id="feedbackit-cancel" onclick="return false;"><i class="icon-remove icon-white"></i><span class="icon <?php echo $icons['cancel']; ?>"></span> <?php echo __d('feedback','Cancel'); ?></button>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="feedbackit-highlight-holder"><?php echo $this->Html->image('Feedback.circle.gif');?></div>

<?php echo $this->element('Feedback.sidebar_modal');?>
