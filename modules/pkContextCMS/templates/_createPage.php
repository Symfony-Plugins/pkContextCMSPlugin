<?php echo jq_link_to_function("Add Page", 
	'$("#pk-breadcrumb-create-childpage-form").fadeIn(250, function(){ $(".pk-breadcrumb-create-childpage-title").focus(); }); 
	$("#pk-breadcrumb-create-childpage-button").hide(); 
	$("#pk-breadcrumb-create-childpage-button").prev().hide();
	$(".pk-breadcrumb-create-childpage-controls a.pk-cancel").parent().show();', 
	array(
		'id' => 'pk-breadcrumb-create-childpage-button', 
		'class' => 'pk-btn icon pk-add', 
)) ?>

<form method="POST" action="<?php echo url_for('pkContextCMS/create') ?>" id="pk-breadcrumb-create-childpage-form" class="pk-breadcrumb-form add">

	<?php $form = new pkContextCMSCreateForm($page) ?>
	<?php echo $form['parent'] ?>
	<?php echo $form['title'] ?>

	<ul class="pk-form-controls pk-breadcrumb-create-childpage-controls">
	  <li>
			<input type="submit" value="Create Page" class="pk-submit" />
		</li>
	  <li>
			<?php echo jq_link_to_function("cancel", 
				'$("#pk-breadcrumb-create-childpage-form").hide(); 
				$("#pk-breadcrumb-create-childpage-button").fadeIn(); 
				$("#pk-breadcrumb-create-childpage-button").prev(".pk-i").fadeIn();', 
				array(
					'class' => 'pk-btn icon pk-cancel', 
			)) ?>
		</li>
	</ul>

</form>