<div class="ai1ec-clearfix">
	<h2 class="timely-logo">
		<a href="http://time.ly/?utm_source=dashboard&nbsp;utm_medium=button&nbsp;utm_term=ai1ec-pro&nbsp;utm_content=1.11.4&nbsp;utm_campaign=logo"
			title="<?php esc_attr_e( 'Timely', AI1EC_PLUGIN_NAME ); ?>"
			target="_blank">
		</a>
	</h2>

	<div class="timely-intro">
		<h2>
			<?php _e( 'Timely’s All-in-One Event Calendar is a<br />revolutionary new way to find and share events.', AI1EC_PLUGIN_NAME ); ?>
		</h2>
		<div class="ai1ec-support-buttons ai1ec-row">
			<div class="ai1ec-col-lg-4" id="ai1ec-addons-col">
				<a class="ai1ec-btn ai1ec-btn-info ai1ec-btn-block ai1ec-btn-lg"
					target="_blank"
					href="http://time.ly/add-ons/?utm_source=dashboard&amp;utm_medium=button&amp;utm_campaign=addons">
					<i class="ai1ec-fa ai1ec-fa-magic ai1ec-fa-fw"></i>
					<?php _e( 'Get Add-ons', AI1EC_PLUGIN_NAME ); ?>
				</a>
			</div>
			<div class="ai1ec-col-lg-4" id="ai1ec-support-col">
				<a class="ai1ec-btn ai1ec-btn-primary ai1ec-btn-block ai1ec-btn-lg"
					target="_blank"
					href="http://time.ly/support/?utm_source=dashboard&amp;utm_medium=button&amp;utm_campaign=support">
					<i class="ai1ec-fa ai1ec-fa-comments ai1ec-fa-fw"></i>
					<?php _e( 'Support', AI1EC_PLUGIN_NAME ); ?>
				</a>
			</div>
			<div class="ai1ec-col-lg-4" id="ai1ec-events-col">
				<a class="ai1ec-btn ai1ec-btn-primary ai1ec-btn-block ai1ec-btn-lg"
					target="_blank"
					href="http://time.ly/calendar/?utm_source=dashboard&amp;utm_medium=button&amp;utm_campaign=events">
					<i class="ai1ec-fa ai1ec-fa-calendar ai1ec-fa-fw"></i>
					<?php _e( 'Timely Events', AI1EC_PLUGIN_NAME ); ?>
				</a>
			</div>
		</div>
	</div>
	<div class="ai1ec-news">
		<h2>
			<i class="ai1ec-fa ai1ec-fa-bullhorn"></i>
			<?php _e( 'Timely News', AI1EC_PLUGIN_NAME ); ?>
			<small>
				<a href="http://time.ly/blog?utm_source=dashboard&nbsp;utm_medium=blog&nbsp;utm_term=ai1ec-pro&nbsp;utm_content=1.11.4&nbsp;utm_campaign=news"
					target="_blank">
					<?php _e( 'view all news', AI1EC_PLUGIN_NAME ); ?>
					<i class="ai1ec-fa ai1ec-fa-arrow-right"></i>
				</a>
			</small>
		</h2>
		<div>
		<?php if ( count( $news ) > 0 ) : ?>
			<?php foreach ( $news as $n ) :
				$ga_args = array(
					'utm_source'   => 'dashboard',
					'utm_medium'   => 'blog',
					'utm_campaign' => 'news',
					'utm_term'     => urlencode(
						strtolower( substr( $n->get_title(), 0, 40 ) )
					),
				);
				$href = esc_attr( add_query_arg( $ga_args, $n->get_permalink() ) );
				$desc = preg_replace( '/\s+?(\S+)?$/', '', $n->get_description() );
				$desc = wp_trim_words(
					$desc,
					40,
					' <a href="' . $href . '" target="_blank">[&hellip;]</a>'
				);
				?>
				<article>
					<header>
						<h4>
							<a href="<?php echo $href; ?>" target="_blank">
								<?php echo $n->get_title(); ?>
							</a>
						</h4>
					</header>
					<p><?php echo $desc; ?></p>
				</article>
			<?php endforeach; ?>
		<?php else : ?>
			<p><em>No news available.</em></p>
		<?php endif; ?>
		</div>
	</div>

	<div class="ai1ec-follow-fan">
		<div class="ai1ec-facebook-like-top">
			<script src="//connect.facebook.net/en_US/all.js#xfbml=1"></script>
			<fb:like href="http://www.facebook.com/timelycal" layout="button_count"
				show_faces="true" width="110" font="lucida grande"></fb:like>
		</div>
		<a href="http://twitter.com/_Timely" class="twitter-follow-button">
			<?php _e( 'Follow @_Timely', AI1EC_PLUGIN_NAME ) ?>
		</a>
		<script src="//platform.twitter.com/widgets.js" type="text/javascript">
		</script>
	</div>
</div>
