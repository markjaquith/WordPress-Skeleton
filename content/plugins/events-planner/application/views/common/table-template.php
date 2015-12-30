<table id="<?php echo (isset( $class )) ? $class : ''; ?>" class ="<?php echo (isset( $id )) ? $id : ''; ?>">
    <thead>
        <tr>
            <?php echo (isset( $header )) ? $header : ''; ?>

        </tr>
    </thead>
    <tfoot>

            <?php echo (isset( $footer )) ? $footer : ''; ?>

    </tfoot>
    <tbody>
        <tr>

            <?php echo (isset( $body )) ? $body : ''; ?>

        </tr>

    </tbody>
</table>