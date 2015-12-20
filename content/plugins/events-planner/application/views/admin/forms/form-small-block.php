<?php
if ($params['values'] == '')
    return null;

$values = $params['values'];

foreach ( $values as $index => $q_id ):
    $q['epl_form_id'] = $index;

//find the list of questions

$values[$index]['form_field_list'] = $this->epl->load_view( 'admin/forms/field-list-inside-form', array('values'=>$values[$index]['epl_form_fields']), true );

    $d = json_encode( $values[$index] ); //json encoded data that will be used client side for edit purposes
?>
    <tr id="<?php echo $index; ?>">
        <td class="epl_w20"></td>
        <td>
        <?php echo $values[$index]['epl_form_label']?$values[$index]['epl_form_label']:$values[$index]['epl_field_section']; ?>
            <input type="hidden" name ="_order[]" value="<?php echo $index; ?>" />
        <textarea class="data epl_border"><?php echo $d; ?></textarea>
    </td>

    <td class="epl_w100">

         <?php

        $del_cl = 'epl_action epl_delete';
        if ( $values[$index]['epl_system'] != 0 )
            $del_cl = 'epl_lock';
        ?>

        <div id="<?php echo $index;?>" class="<?php echo $del_cl; ?> epl_ajax_delete"></div>
        <div class="epl_action epl_edit"></div>

        
    </td>

</tr>

<?php endforeach; ?>