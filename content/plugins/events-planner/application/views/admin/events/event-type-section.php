
<div class="epl_box epl_ov_a" style="">

    <div class="epl_box_content">

        <ul class="epl_event_type">
            <?php

            foreach ( $epl_event_type as $ev_k => $ev_v ){
                echo $ev_v;
            }
            ?>


            <li><span class="epl_font_red">PRO:</span> One or more days.  The user can pick and choose <span class="epl_font_red">one or more</span> days (if more than one day is available).</li>
            <li><span class="epl_font_red">PRO:</span> Two or more days.  The user registers for <span class="epl_font_red">all</span> days.</li>
            <li><span class="epl_font_red">PRO:</span> A Course (usually more than one day).  When the user registers for the course, they are registering for all the days.
                          The Recurrence Helper will let you construct a calendar of the course.</li>
        </ul>
    </div>
</div>