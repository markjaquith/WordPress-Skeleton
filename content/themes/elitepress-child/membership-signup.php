<?php
/* Template Name: membership sign_up */
get_header();
?>
<div class="content-section">
	<div class="container">
		<div class="title-section">
			<div class="row">
				<div class="col-md-12">
					<h1>Artist Membership Sign up</h1>
				</div>
			</div>
		</div>
    <div class="row">
      <div class="col-md-6">
        <p>Thank you for taking an interest in becoming an Artist member and joining The Arches Project Network.</p>
        <p>We hope to see you online posting awesome content and unleashing your creativity to the world!</p>
        <p>Click the PayPal button below to start the registration process.</p>
        <hr>
				<?php // Change these to the date you want the annual cycle to officially start

					$cycle_start_year = '2016';

					$cycle_start_month = '7';

					$cycle_start_day = '31';

					$cdate = mktime(0, 0, 0, $cycle_start_month, $cycle_start_day, $cycle_start_year);

					$today = time();

					$difference = $cdate - $today;

					if ($difference < 0) { $difference = 0; }

					$trial_weeks = ceil($difference/60/60/168); // Calculated number of weeks until yearly cycle should start

					echo do_shortcode('[s2Member-PayPal-Button level="1" ccaps="" desc="Artist Member / description and pricing details here." ps="paypal" lc="" cc="GBP" dg="0" ns="1" custom="www.eunuigbe" ta="0" tp="'.$trial_weeks.'" tt="W" ra="30" rp="1" rt="Y" rr="1" rrt="" rra="1" image="default" output="button" /]');
				?>
			</div>
      <div class="col-md-5 col-md-offset-1">
        <p><strong>To sign up and register on the artist directory is free until 31st July 2016 after this date registration fee will be £30 for 12 months.</strong></p>
        <p><strong>Note:</strong> You will be prompted to make a subscription through paypal. Once the subscription has been made you will be sent a registration link to the email address associated with your paypal account.</p>
        <p>Click the registration link in your email in order to create your membership account</p>
        <p>For any enquires please don't hesitate to <strong><a href="<?php echo site_url('contact-us') ?>">contact us</a></strong> or phone us on <strong>0121 772 0852</strong></p>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>
