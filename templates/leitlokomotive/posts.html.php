<html>
  <head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php echo ASAPH_LINK_PREFIX; ?>feed" />
    <link href='http://fonts.googleapis.com/css?family=Grand+Hotel' rel='stylesheet' type='text/css'>
    <link href='<?php echo Asaph_Config::$absolutePath; ?>templates/leitlokomotive/css/style.css' rel='stylesheet' type='text/css'>
    <title>leitlokomotive.de</title>
  </head>
  <body>
    <div id="header" class="header">
      leitlokomotive
      <div style="font-size:14pt;line-height:15pt;">
      	<?php if( $pages['prev'] ) { ?>
      		<a href="<?php echo ASAPH_LINK_PREFIX.'page/'.$pages['prev']?>" class="pageleft">«</a>
		<?php } else { ?>
			<a href="" style="visibility:false" class="pageleft">«</a>
		<?php } ?>
        <?php 
        	for($i=1;$i<=$pages['total'];$i++)
        	{
        		if($i==$pages['current'])
        		{
        			echo "<a style=\"color:white;\" href=\"".ASAPH_LINK_PREFIX.'page/'.$i."\">".$i."</a> ";
        		}
        		else
        		{
        			echo "<a href=\"".ASAPH_LINK_PREFIX.'page/'.$i."\">".$i."</a> ";
        		}
        	} 
        ?>
        <?php if( $pages['next'] ) { ?>
			<a href="<?php echo ASAPH_LINK_PREFIX.'page/'.$pages['next']?>" class="pageright">»</a>
		<?php } else { ?>
			<a href="" style="visibility:false" class="pageright">»</a>
		<?php } ?>
        
      </div>
    </div>

    <div id="main" class="main">
    	<?php foreach( $posts as $p ) { ?>
			<div class="post" style="margin-bottom:50px;">
				<?php if( $p['image'] ) { ?>
					 <img class="content" src="<?php echo $p['image']['image']; ?>" />
				<?php } elseif( $p['video'] ) { ?>
					<embed src="<?php echo $p['video']['src']; ?>" type="<?php echo $p['video']['type'];?>" width="612" height="<?php echo ($p['video']['height']*612/$p['video']['width']);?>" />
				<?php } else { ?>
					<p>
						<a href="<?php echo $p['source']; ?>"><?php echo nl2br($p['title']); ?></a>
					</p>
				<?php } ?>
				<div class="headline">
			    	<h1><?php echo $p['title']; ?></h1>
					<h2><?php echo date("d/m/y",$p['created']); ?></h2>
				</div>
				<p style="<?php if(strlen($p['description'])<200) echo "column-span: all;-webkit-column-span: all;" ?>"><?php echo $p['description']; ?></p>
			</div>
		<?php } ?>
    <script src="<?php echo Asaph_Config::$absolutePath; ?>templates/leitlokomotive/js/index.js" type="text/javascript"></script>

  </body>
</html>
</body>
</html>