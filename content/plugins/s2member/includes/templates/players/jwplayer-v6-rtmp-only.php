<?php
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");
?>

<div id="%%player_id%%" class="s2member-jwplayer-v6"></div>
<script type="text/javascript" src="%%player_path%%"></script>
<script type="text/javascript">
	if(typeof jwplayer.key !== 'string' || !jwplayer.key)
		jwplayer.key = '%%player_key%%';

	jwplayer('%%player_id%%').setup
		({
			playlist:
				[{
					title: '%%player_title%%',
					image: '%%player_image%%',

					mediaid: '%%player_mediaid%%',
					description: '%%player_description%%',

					captions: %%player_captions%%,

					sources: %%player_sources%%
				}],

			controls: %%player_controls%%,
			skin: '%%player_skin%%',
			stretching: '%%player_stretching%%',
			width: %%player_width%%,
			height: %%player_height%%,
			aspectratio: '%%player_aspectratio%%',

			autostart: %%player_autostart%%,
			fallback: %%player_fallback%%,
			mute: %%player_mute%%,
			primary: '%%player_primary%%',
			repeat: %%player_repeat%%,
			startparam: '%%player_startparam%%',

			%%player_option_blocks%%
		});
</script>
