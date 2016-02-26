<table class="epl_form_data_table" cellspacing ="0">
<?php

echo current($epl_genral_fields);


?>
</table>

<?php echo epl_show_ad ('Easily add more fields here with action filters and access their values from anywhere in the program.'); ?>

<input type="hidden" value="<?php echo $_GET['action'];?>" name="post_action" />