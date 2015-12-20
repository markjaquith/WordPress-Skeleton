<?php

if ( $values == '' )
    return null;
?>

<tbody>
    <?php

    foreach ((array) $values as $index => $q_id ):
        $q['epl_form_id'] = $q_id;
    ?>
        <tr>
            <td class="epl_w20"> <div class="handle"></div></td>
            <td><input type="hidden" name ="epl_form_fields[]" value="<?php echo $q_id; ?>" />
            <?php echo (isset($epl_fields[$q_id]['epl_field']))?$epl_fields[$q_id]['epl_field']:$epl_fields[$q_id]['label']; ?>
        </td><td>
            <div class="epl_action epl_delete"></div>
        </td></tr>

    <?php endforeach; ?>
</tbody>