<?php

if ( $params['values'] == '' )
    return null;

$values = $params['values'];

//echo "<pre>" . print_r($values, true). "</pre>";

foreach ( $values as $index => $q_id ):

    if ( $index != '' ):

        $q['epl_field_id'] = $index;

        $d = json_encode( $values[$index] );
?>
        <tr id="<?php echo $index; ?>">
            <td class="epl_w20"> <div class="handle"></div></td>
            <td>
<?php echo (isset($values[$index]['epl_field']) && $values[$index]['epl_field'] )? $values[$index]['epl_field'] : stripslashes_deep($values[$index]['label']); ?>
        <input type="hidden" name ="_order[]" value="<?php echo $index; ?>" />

        <textarea class="data" rows="" cols=""><?php echo $d; ?></textarea>
        <!--<span class="question_attributes"> <?php echo $values[$index]['epl_field_type']; ?> </span>-->
    </td>

    <td class="epl_w100">

        <?php

        $del_cl = 'epl_action epl_delete';
        if ( $values[$index]['epl_system'] != 0 )
            $del_cl = 'epl_lock';
        ?>

        <div class=" <?php echo $del_cl; ?> epl_ajax_delete "></div>

        <div class="epl_action epl_edit"></div>

    </td>

</tr>

<?php endif;
    endforeach; ?>