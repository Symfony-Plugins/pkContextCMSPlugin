<?php // Case must be correct! ?>
<?php use_helper('jQuery') ?>

<div id="pk-signin">
  <form action="<?php echo url_for('@sf_guard_signin') ?>" method="post" id="pk-signin-form" <?php echo ($form->hasErrors())? 'class="has-errors"':''; ?>>

		<div class="pk-form-row">
    	<?php echo $form['username']->renderLabel() ?>
    	<?php echo $form['username']->render() ?>
    	<?php echo $form['username']->renderError() ?>
		</div>
		
		<div class="pk-form-row">		
    	<?php echo $form['password']->renderLabel() ?>
    	<?php echo $form['password']->render() ?>
    	<?php echo $form['password']->renderError() ?>
		</div>

		<div class="pk-form-row">
    	<?php echo $form['remember']->renderRow() ?>
		</div>
		
		<ul class="pk-form-row submit">
    	<li><input type="submit" value="<?php echo __('sign in') ?>" class="pk-submit" /></li>
			<li><?php echo jq_link_to_function('Cancel', "$('#pk-login-form-container').fadeOut('fast'); $('.pk-page-overlay').fadeOut('fast');", array('class' => 'cancel', )) ?></li>
		</ul>
		
  </form>
</div>
