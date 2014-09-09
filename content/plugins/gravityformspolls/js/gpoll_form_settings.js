
jQuery(document).ready(function () {
    var blockRepeatVoters, $expires;
    blockRepeatVoters = jQuery("input[name='_gaddon_setting_blockRepeatVoters']:checked").val() == 1 ? true : false;
    $expires = jQuery("#gaddon-setting-select-cookie");
    $expires.toggle(blockRepeatVoters);

    jQuery("input[name='_gaddon_setting_blockRepeatVoters']").change(function(){
        $expires.toggle('slow');
    });

});