<div class="epl_feedback_form_wrapper">
    <p>Please use this form to let us know what you think about this section, your needs, any issues that you have encountered or any input about the plugin.
        This plugin is very powerful but we need your help to make it better. </p>
    <p>This form will sent an email to help@wpeventsplanner.com.  It will not send out any information about this blog, just what is included in these fields.  Moreover, your
        email will only be used for correspondence about this message!</p>
    <p>Thanks very much!</p>

    <form id="epl_feedback_form">
        <fieldset>

            <table cellspacing="0" class="epl_form_data_table">
                <tbody>
                    <tr valign="middle">
                        <td><label for="">Name*</label></td>
                        <td><input type="text" value="<?php echo (isset($name))?$name:''; ?>" style="" rel="" class="required epl_w300" name="name" id="">
                        </td>
                    </tr>
                    <tr valign="middle">
                        <td><label for="">Email*</label></td>
                        <td><input type="text" value="<?php echo (isset($email))?$email:''; ?>" style="" rel="" class="required epl_w300" name="email" id="">
                        </td>
                    </tr>
                    <tr valign="middle">
                        <td><label for="">Reason*</label></td>
                        <td><select style="" class="required" id="" name="reason">
                                <option value="Bug">Bug</option>
                                <option value="Feature">Feature Request</option>
                                <option value="Question">Question</option>
                                <option value="Other">Other</option>

                            </select></td>
                    </tr>
                    <tr valign="middle">
                        <td><label for="">Subject*</label></td>
                        <td><input type="text" value="<?php echo (isset($section))?$section:''; ?>" style="" rel="" class="required epl_w300" name="section" id="">
                            <span class="description">You can change the pre-populated value.</span>
                        </td>
                    </tr>

                    <tr valign="middle">
                        <td><label for="">Message*</label></td>
                        <td><textarea style="" class="required" name="message" id="" rows="5" cols="60"></textarea>
                        </td>
                    </tr>


                </tbody></table>
                <input type="submit" name="submit" value="Send" />
        </fieldset>

    </form>
</div>