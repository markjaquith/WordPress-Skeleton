<?php
/* Template Name: donate page */
get_header();
?>
<div class="content-section">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="title-section">
					<h2>Please help our cause and donate kindly</h2>
				</div>	
			</div>
		</div>	
		<div class="row">
			<div class="col-md-6">
				<p> A dream is sometimes seen as something that is make believe, something out of the ordinary, something unattainable...</p>
				<p>But we don't believe that here!</p>
				<p>Help our youth express their talents through music and performing arts and encouraging them to be passionate high achievers and becoming the creative future of tomorrow... </p>
			</div>
			<div class="col-md-6">
				<div>
					<h4 style="margin-top:0px;"><i>To make a donation, please click the link below</i></h4>
				</div>
				<div class="paypal-donation">
					<br>
					<?php echo do_shortcode('[paypal-donation]'); ?>
				</div>
			</div>
		</div>
		<hr>
			<h4>The Arches Project would like to thank the following for their donations and support</h4>
			<br>
			<div class="row">
				<div class="col-md-3">
					<a href="http://www.truckandtrack.com/">
						<img class="img-responsive" src="<?php echo home_url(); ?>/content/uploads/2016/02/donatetruckandtrack.png"/>
					</a>	
				</div>
				<div class="col-md-3">
					<a href="http://philipbatestrust.co.uk/">
						<img class="img-responsive" src="<?php echo home_url(); ?>/content/uploads/2016/02/donatephilipbates.png"/>
					</a>	
				</div>
				<div class="col-md-3">
					<a href="http://www.pegasus-electrical.co.uk/">
						<img class="img-responsive" src="<?php echo home_url(); ?>/content/uploads/2016/02/donatepegasus1.png"/>
					</a>	
				</div>
				<div class="col-md-3">
					<a href="https://www.easyfundraising.org.uk/Bounce.php?link=login_url&url=/panel/">
						<img class="img-responsive" src="<?php echo home_url(); ?>/content/uploads/2016/02/donateeasyfunding.png"/>
					</a>	
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<a href="http://contract-pc.co.uk/">
						<img class="img-responsive" src="<?php echo home_url(); ?>/content/uploads/2016/02/donatecontractpowercoating.png"/>
					</a>	
				</div>
				<div class="col-md-3">
					<a href="#">
						<img class="img-responsive" src="<?php echo home_url(); ?>/content/uploads/2016/02/donateoneillpainting.png"/>
					</a>	
				</div>	
				<div class="col-md-3">
					<a href="http://marsdendirect.co.uk/exhibition-services/">
						<img class="img-responsive" src="<?php echo home_url(); ?>/content/uploads/2016/02/donatemarsden2.png"/>
					</a>	
				</div>
				<div class="col-md-3">
					<a href="http://www.co-operative.coop/membership/local-communities/community-fund/">
						<img class="img-responsive" src="<?php echo home_url(); ?>/content/uploads/2016/02/donate_coop.png"/>
					</a>	
				</div>
				<div class="col-md-3">
					<a href="http://www.trinitycollege.com/site/?id=274">
						<img class="img-responsive" src="<?php echo home_url(); ?>/content/uploads/2016/02/arts_award.png"/>
					</a>	
				</div>	
			</div>
	</div>
</div>
<?php get_footer(); ?>