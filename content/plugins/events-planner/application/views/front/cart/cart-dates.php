<?php

foreach ($date as $k =>$field):
?>

<div>

    <?php echo $field['field'];?>

     <?php if (isset($time[$k])):?>

    <div class="epl_ind_time_wrapper"> Time: <?php echo $time[$k]; ?></div>


    <?php endif; ?>
     <?php if (isset($price[$k])):?>

    <div class="epl_ind_price_wrapper"> <?php echo $prices[$k]; ?></div>


    <?php endif; ?>
</div>


<?php
endforeach;

?>

