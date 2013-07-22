<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Post: Asaph</title>
	<link rel="stylesheet" type="text/css" href="<?php echo ASAPH_POST_CSS; ?>" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
	<!-- Place inside the <head> of your HTML -->
	<script src="<?php echo Asaph_Config::$absolutePath;?>tinymce/js/tinymce/tinymce.min.js"></script>
	<script type="text/javascript">
	tinymce.init({
	    selector: "textarea#description",
	    width: "100%",
	    height: 231,
	    menubar : false,
	   	toolbar: "undo redo | bold italic | link image | code",
	   	plugins: "link,code",
	   	skin: "plain",
	   	statusbar : false,
	   	convert_urls: true
	 });
	</script>
	<style>
		dd > div.mce-container
		{
			float:left;
			width:100%;
		}
	</style>
<!-- Place this in the body of the page content -->
</head>
<body class="Asaph_Post" onload="document.getElementById('title').focus();">
	<!--<h1>
		Post
		<?php if( !empty($_POST['image']) || !empty($_GET['image']) ) { ?>
			Image:
		<?php } else { ?>
			Site:
		<?php } ?>
	</h1>-->
	
	<?php if( !empty($status) ) { ?>
		<div class="warn">
			<?php if( $status == 'not-logged-in' ) { ?>The name or password was not correct!<?php } ?>
			<?php if( $status == 'download-failed' ) { ?>Couldn't load the image!<?php } ?>
			<?php if( $status == 'duplicate-image' ) { ?>This image was already posted!<?php } ?>
			<?php if( $status == 'thumbnail-failed' ) { ?>Couldn't create a thumbnail of the image!<?php } ?>
		</div>
	<?php } ?>
	<?php if( !empty($_POST['image']) || !empty($_GET['image']) ) { ?>
		<div id="image" style="background-color:rgb(64,64,64);background-image:url(<?php printReqVar('image'); ?>);background-size:cover;
    	width: 350px;
    	height:350px;
    	float: left;
    	margin-right: 10px;
    	background-position:center;
    	border-top-left-radius: 10px;
    	"> </div>
    <?php } else if( !empty($_POST['video']) || !empty($_GET['video']) ) { ?>
		<!-- <embed 
			src="<?php printReqVar('video'); ?>" 
			type="<?php printReqVar('type'); ?>" 
			width="350" 
			height="350" 
			style="float:left;margin-right:10px;max-width: 350px;max-height 350px;background-color:black;border-top-left-radius: 10px;"
		/> -->
		<img id="image" style="background-color:rgb(64,64,64);background-image:url(<?php printReqVar('thumb'); ?>);background-size:cover;
    	width: 350px;
    	height:350px;
    	float: left;
    	margin-right: 10px;
    	background-position:center;
    	border-top-left-radius: 10px;
    	margin-bottom:0px;
    	" src="<?php echo Asaph_Config::$absolutePath;?>templates/whiteout/playbutton.svg"/>
    <?php } ?>

	<form action="post.php" method="post"  style="display: block;"><!--;float: left;width: 420px-->
		<input type="hidden" name="xhrLocation" value="<?php printReqVar('xhrLocation'); ?>"/>
		<?php if( !empty($loginError) ) { ?><span class="warn">The name or password was not correct!</span><?php } ?>
		
			
		<?php if( !empty($_POST['image']) || !empty($_GET['image']) ) { ?>
			<dl><!--<dt>Title:</dt>-->
				<dd><input id="title" type="text" placeholder="Title" name="title" class="long" style="font-weight:bold;font-size:12pt;" value="<?php printReqVar('title'); ?>"/></dd>
				<!-- <dt>Image:</dt> -->
				<!-- <dd> -->
					<input type="hidden" name="image" placeholder="Image URL" id ="imageForm" class="long" value="<?php printReqVar('image'); ?>"/>
				<!-- </dd> -->
				<!-- <dt>Source:</dt> -->
				<!-- <dd> -->
					<input type="hidden" name="source" placeholder="Reference" class="long" value="<?php printReqVar('source'); ?>"/>
				<!-- </dd> -->
				<!-- <dt>Description:</dt> -->
				<dd><textarea id="description" type="text" name="description" class="long"><?php printReqVar('description'); ?></textarea></dd>
				
		<?php } elseif( !empty($_POST['video']) || !empty($_GET['video']) ) { ?>
			<dl>	
				<!-- <dt>Title:</dt> -->
				<dd><input id="title" type="text" name="title" class="long" value="<?php printReqVar('title'); ?>"/></dd>
				
				<input type="hidden" name="video" class="long" value="<?php printReqVar('video'); ?>"/>
				<input type="hidden" name="thumb" class="long" value="<?php printReqVar('thumb'); ?>"/>
				
				<!-- <dt>Source:</dt> -->
				<dd>
					<input type="hidden" name="source" class="long" value="<?php printReqVar('source'); ?>"/>
				</dd>
				<!-- <dt>Description:</dt> -->
				<dd><textarea id="description" type="text" name="description" class="long"><?php printReqVar('description'); ?></textarea></dd>
				
				<input type="hidden" name="height" value="<?php printReqVar('height'); ?>"/>
				<input type="hidden" name="width" value="<?php printReqVar('width'); ?>"/>
				<input type="hidden" name="video_type" value="<?php printReqVar('video_type'); ?>"/>
		<?php } elseif( !empty($_POST['quote']) || !empty($_GET['quote']) ) { ?>
			<dl style="width:350px;margin-right:10px;"> <!-- <dt>Title:</dt> -->
				<!-- <dt>Quote:</dt> -->
				
				<dd><textarea id="quote" type="text" placeholder = "A Deep and Meaningful Quote" name="quote" class="long"><?php printReqVar('quote'); ?></textarea></dd>
				
				<!-- <dt>Speaker:</dt> -->
				<dd><input id="speaker" type="text" name="speaker" placeholder = "by an intelligent Person" class="long" value="<?php printReqVar('speaker'); ?>"/></dd>
				<!-- <dt>Source:</dt> -->
			</dl>
			<dl >
				<dd><input id="title" type="text" name="title" placeholder = "Title" class="long" value="<?php printReqVar('title'); ?>"/></dd>
				<dd>
					<input type="hidden" name="source" class="long" value="<?php printReqVar('source'); ?>"/>
				</dd>
				<!-- <dt>Description:</dt> -->
				<dd><textarea id="description" type="text" name="description" class="long"><?php printReqVar('description'); ?></textarea></dd>
				<!-- TODO: Link-->
		<?php } else { ?>
			<dl style="width:750px">
				<!-- <dt>Titel:</dt> -->
				<dd><input type="text" placeholder="Title" id="title" name="title" value="<?php printReqVar('title'); ?>" /></dd>
				<!-- <dt>Site:</dt> -->
				<dd>
					<input type="hidden" name="url" class="long" value="<?php printReqVar('url'); ?>"/>
				</dd>
				<!-- <dt>Description:</dt> -->
				<dd><textarea id="description" type="text" name="description" class="long"><?php printReqVar('description'); ?></textarea></dd>
				
			<?php } ?>
			<dd><input type="submit" name="post" value="Publish this Post!" class="button"/></dd>
		</dl>
	</form>
	<?php if( !empty($_POST['image']) || !empty($_GET['image']) ) { ?>
	<script>

    		$("#imageForm").change(function(){
    			$("#image").css("background-image","url("+$("#imageForm").val()+")");
    		});
    	</script>
    <?php } ?>
</body>
</html>