<script>
<?php // Break out of iframe or AJAX ?>
top.location.href = "<?php echo url_for("pkContextCMS/cleanSigninPhase2") ?>";
</script>
<?php // Just in case of surprises ?>
<?php echo link_to("Click here to continue.", "pkContextCMS/cleanSigninPhase2") ?>