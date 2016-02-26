<?php

if ( $response_view == 0 || $input_type == 'hidden' ) :
    echo $field;

elseif ( $response_view == 1 ) :
?>
    <td><?php echo $field ?></td>

<?php elseif ( $response_view == 2 ) : ?>
        <tr valign="top">
            <th scope="row"><?php echo $label; ?></th>
            <td><?php echo $field . $description; ?></td>
        </tr>

<?php elseif ( $response_view == 3 ) : ?>
            <tr valign="middle">
                <td><?php echo $label; ?></td>
                <td><?php echo $field . $description; ?></td>
            </tr>
<?php elseif ( $response_view == 4 ) : ?>
                <table>
                    <tr valign="top">
                        <th scope="row" valign="top"><?php echo $label; ?></th>
                        <td><?php echo $field . $description; ?></td>
                    </tr>
                </table>
<?php elseif ( $response_view == 5 ) : ?>
                    <tr valign="top">
                        <th valign="top" style="padding: 0;"><?php echo $label; ?></th>
                    </tr>
                    <tr class="epl_field">
                        <td><?php echo $field . $description; ?></td>
                    </tr>
<?php

                    elseif ( $response_view == 6 ) :

                        return $field;

                    elseif ( $response_view == 20 ) :
?>
                        <li class="epl_border_radius_5">

                            <div style="display:block;"><?php echo $field; ?> </div>
                            <div> <?php echo $label; ?>
                                <small><?php echo $description; ?></small>
                            </div>

                        </li>

<?php endif; ?>




