<?php
//dl("tidy.so");
ob_start();
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title></title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

		<link rel="stylesheet" href="<?php echo Asaph_Config::$absolutePath; ?>templates/zwitscher/css/normalize.css">
		<link rel="stylesheet" href="<?php echo Asaph_Config::$absolutePath; ?>templates/zwitscher/css/main.css">
		<script src="<?php echo Asaph_Config::$absolutePath; ?>templates/zwitscher/js/vendor/modernizr-2.6.2.min.js"></script>
	</head>
	<body lang="en">
		<!--[if lt IE 7]>
			<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
		<![endif]-->

		<!-- Add your site or application content here -->
		<!--<div class="header">

		</div>-->
		
		<ul>
			<?php foreach( $posts as $p ): ?>
				<li>
					<?php if( $p['image'] ): ?>
						<img class="content" src="<?php echo $p['image']['image']; ?>" width="612" height="<?php echo ($p['image']['height']*612/$p['image']['width']);?>"/>
					<?php elseif( $p['video'] ): ?>
						<embed src="<?php echo $p['video']['src']; ?>" type="<?php echo $p['video']['type'];?>" width="612" height="<?php echo ($p['video']['height']*612/$p['video']['width']);?>" />
					<?php elseif( $p['quote'] ) : ?>
						<quote <?php if(strlen($p['quote']['quote'])>200) echo "length=\"long\""; ?>>
							»<?php echo $p['quote']['quote']; ?>«
							<div style="font-style:normal;line-height:200%;font-size:80%;">- <?php echo $p['quote']['speaker']; ?> -</div>
						</quote>          
					<?php endif; ?>
					<?php echo $p['description']; ?>
					<div class="footer">
						- <strong>Lukas</strong> on <?php echo date("m/d/y",$p['created']); ?>
						<?php if($p['source']!="") {?>
							via <a href="<?php echo $p['source'];?>"><?php echo $p['sourceDomain']; ?></a>
						<?php } ?>
					</div>
				</li>
			<?php endforeach; ?>

		</ul>

		<div class="navigation">
			<?php if( $pages['prev']) { ?>
				<a href="<?php echo ASAPH_LINK_PREFIX.'page/'.$pages['prev']?>" class="pageleft">«</a>
			<?php } else { ?>
				<a href="" style="visibility:hidden" class="pageleft">«</a>
			<?php } ?>
			<?php 
				for($i=1;$i<=$pages['total'];$i++)
				{
					if($i==$pages['current'])
					{
						echo "<a style=\"color:black;\" href=\"".ASAPH_LINK_PREFIX.'page/'.$i."\">".$i."</a> ";
					}
					else
					{
						echo "<a href=\"".ASAPH_LINK_PREFIX.'page/'.$i."\">".$i."</a> ";
					}
				} 
			?>

			<?php if( $pages['next']) { ?>
				<a href="<?php echo ASAPH_LINK_PREFIX.'page/'.$pages['next']?>" class="pageright">»</a>
			<?php } else { ?>
				<a href="" style="visibility:hidden" class="pageright">»</a>
			<?php } ?>
	  	</div>
	  	<div class="impressum"><a href="">Impressum</a></div>
		<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" />
		<script>window.jQuery || document.write('<script src="<?php echo Asaph_Config::$absolutePath; ?>templates/zwitscher/js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
		<script src="<?php echo Asaph_Config::$absolutePath; ?>templates/zwitscher/js/plugins.js" />
		<script src="<?php echo Asaph_Config::$absolutePath; ?>templates/zwitscher/js/main.js" />

		
		<script>
			(function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
			function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
			e=o.createElement(i);r=o.getElementsByTagName(i)[0];
			e.src='//www.google-analytics.com/analytics.js';
			r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
			ga('create','UA-XXXXX-X');ga('send','pageview');
		</script>-->
	</body>
</html>
<?php
	$html = ob_get_clean();

	// Specify configuration
	$config = array('indent' => true, 'new-blocklevel-tags' => 'quote', 'vertical-space' => false, 'wrap' => 0);

	// Tidy
	$tidy = new tidy;
	$tidy->parseString($html, $config, 'utf8');
	$tidy->cleanRepair();

	// Output
	echo $tidy; 
?>
