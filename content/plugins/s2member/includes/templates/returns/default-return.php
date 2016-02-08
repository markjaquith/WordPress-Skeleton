<?php
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");
?>

%%doctype_html_head%%
<!-- Note. The DOCTYPE and HEAD Replacement Code can be removed if you would rather build your own. -->
<!-- Note. It is OK to use PHP code inside this template file (when/if needed). -->
	<body class="s2member-return-body s2member-default-return-body">

		<!-- Header Section (contains information and possible custom code from the originating site/domain). -->
		<div id="s2member-default-return-header-section" class="s2member-return-section s2member-return-header-section s2member-default-return-header-section">
			<div id="s2member-default-return-header-div" class="s2member-return-div s2member-return-header-div s2member-default-return-header-div">
				%%header%% <!-- (this is auto-filled by s2Member, based on configuration). -->
			</div>
			<div style="clear:both;"></div>
		</div>

		<!-- Response Section (this is auto-filled by s2Member, based on what action has taken place). -->
		<!-- Although NOT recommended, you can remove the response Replacement Code and build your own message if you prefer. -->
		<!-- It is NOT recommended, because the dynamic response message may vary, depending on what action has taken place. -->
		<div id="s2member-default-return-response-section" class="s2member-return-section s2member-return-response-section s2member-default-return-response-section">
			<div id="s2member-default-return-response-div" class="s2member-return-div s2member-return-response-div s2member-default-return-response-div">
				%%response%% <!-- (this is auto-filled by s2Member, based on what action has taken place). -->
				<div id="s2member-default-return-continue" class="s2member-return-continue s2member-default-return-continue">
					%%continue%% <!-- (auto-filled by s2Member, based on what action has taken place). -->
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>

		<!-- Support Section (contains information about how a Customer can contact support). -->
		<div id="s2member-default-return-support-section" class="s2member-return-section s2member-return-support-section s2member-default-return-support-section">
			<div id="s2member-default-return-support-div" class="s2member-return-div s2member-return-support-div s2member-default-return-support-div">
				%%support%% <!-- (this is auto-filled by s2Member, based on configuration). -->
			</div>
			<div style="clear:both;"></div>
		</div>

		%%tracking%% <!-- (this is auto-filled, supports tracking codes integrated w/ s2Member). -->

	</body>
</html>
