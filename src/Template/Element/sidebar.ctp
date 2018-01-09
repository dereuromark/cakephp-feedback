<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;
use Cake\Core\Plugin;

if (!Configure::read('Feedback')) {
	throw new RuntimeException('No Feedback plugin config found.');
}

// You can also include it manually with the rest of the CSS files in head section.
if (!Configure::read('Feedback.skipCss')) {
	echo $this->Html->css('Feedback.sidebar', ['block' => false]);
}

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

$enablecopybyemail = Configure::read('Feedback.enablecopybyemail');

$enableacceptterms = Configure::read('Feedback.enableacceptterms');
$termstext = '';
if ($enableacceptterms) {
	$termstext = Configure::read('Feedback.termstext') ? __d('feedback', 'When you submit, a screenshot (of only this website) will be taken to aid us in processing your feedback or bugreport.') : '';
}

//Assemble optional vars if AuthComponent is loaded
$username = '';
$email = '';
//FIXME $this->AuthUser->id() using TinyAuth or Session->read('Auth.User.id')
if(class_exists('AuthComponent')) {
	$username = AuthComponent::user('name') ?: AuthComponent::user('username') ?: AuthComponent::user('account') ?: '';
	$email = AuthComponent::user('mail') ?: AuthComponent::user('email') ?: '';
}
?>

<script>
	//Create URL using cake's url helper, this is used in feedbackit-functions.js
	<?php $formposturl = $this->Url->build(array("plugin"=>"Feedback", "controller"=>"Feedback", "action"=>"save"),true); ?>
	window.formURL = '<?php echo $formposturl; ?>';
</script>

<div id="feedbackit-slideout">
	<?php echo $this->Html->image('Feedback.feedback.png');?>
</div>
<div id="feedbackit-slideout_inner">
	<div class="feedbackit-form-elements">
		<div class="pull-right"><i class="tab-hide fa fa-window-close" title="<?php echo __d('feedback', 'Hide this tab completely.'); ?>"></i></div>
		<p>
			<?php echo __d('feedback','Send your feedback or bugreport!');?>
		</p>
		<form id="feedbackit-form" autocomplete="off">
			<div class="form-group">
				<input
					type="text"
					name="name"
					id="feedbackit-name"
					class="<?php if (!empty($username)) echo 'feedbackit-input"'; ?> form-control"
					value="<?php echo $username; ?>"
					placeholder="<?php echo __d('feedback','Your name '); if( !$forceauthusername ) echo ' (optional)'; ?>"
					<?php if( $forceauthusername && !empty($username) ) echo 'readonly="readonly"'; ?>
					>
			</div>
			<div class="form-group">
				<input
					type="email"
					name="email"
					id="feedbackit-email"
					class="<?php if (!empty($email)) echo 'feedbackit-input"'; ?> form-control"
					value="<?php echo $email; ?>"
					placeholder="<?php echo __d('feedback','Your e-mail '); if( !$forceemail ) echo ' (optional)'; ?>"
					<?php if( $forceemail && !empty($email) ) echo 'readonly="readonly"'; ?>
					>
			</div>
			<div class="form-group">
				<input
					type="text"
					name="subject"
					id="feedbackit-subject"
					class="feedbackit-input form-control"
					required="required"
					placeholder="<?php echo __d('feedback','Subject'); ?>"
					>
			</div>
			<div class="form-group">
				<textarea name="feedback" id="feedbackit-feedback" class="feedbackit-input form-control" required="required" placeholder="<?php echo __d('feedback','Feedback or suggestion'); ?>" rows="3"></textarea>
			</div>
			<div class="form-group">
				<div>
					<button
						class="btn btn-info"
						data-loading-text="<?php echo __d('feedback','Click anywhere on website'); ?>"
						id="feedbackit-highlight"
						onclick="return false;">
						<i class="icon-screenshot icon-white"></i><span class="glyphicon glyphicon-screenshot"></span> <?php echo __d('feedback','Highlight something'); ?>
					</button>
				</div>
				<div class="form-group" <?php if (!$enableacceptterms) echo 'style="display:none;"'; ?>>
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
					<button class="btn btn-success" id="feedbackit-submit" disabled="disabled" type="submit"><i class="icon-envelope icon-white"></i><span class="glyphicon glyphicon-envelope"></span> <?php echo __d('feedback','Submit'); ?></button>
					<button class="btn btn-danger" id="feedbackit-cancel" onclick="return false;"><i class="icon-remove icon-white"></i><span class="glyphicon glyphicon-remove"></span> <?php echo __d('feedback','Cancel'); ?></button>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="feedbackit-highlight-holder"><?php echo $this->Html->image('Feedback.circle.gif');?></div>

<!-- Modal for confirmation -->
<div class="modal fade" id="feedbackit-modal" tabindex="-1" role="dialog" aria-labelledby="feedbackit-modalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="feedbackit-modalLabel"><?php echo __d('feedback', 'Feedback submitted');?></h4>
			</div>
			<div class="modal-body">
				Loading...
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __d('feedback', 'Close');?></button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

