<?php
//This is a hidden list of form fields avalable
//for selection when creating a new form
if (is_null($params['values'])) return null;

$values = $params['values'];

foreach ( $values as $index => $q_id ):
    $q['epl_field_id'] = $index;

?>
    <tr>
    <td>

        <?php echo $values[$index]['epl_field']; ?>
            <input type="text" name ="epl_form_fields[]" value="<?php echo $index; ?>" />
    </td>
    <td>   <div class="epl_action epl_add"></div></td>

</tr>

<?php endforeach; ?>