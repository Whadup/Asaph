<?php 
require_once( '../lib/asaph_config.class.php' );
header( 'Content-type: text/javascript; charset=utf-8' ); 
?>
function Asaph_RemotePost( postURL, stylesheet ) {
	this.postURL = postURL;
	this.stylesheet = stylesheet;
	
	this.visible = false;
	this.menu = null;
	this.dialog = null;
	this.iframe = null;
	this.checkSuccessInterval = 0;
	this.minImageSize = 32;
	
	
	this.create = function(){
		var that = this;
		var css = document.createElement('link');
		css.type = 'text/css';
		css.rel = 'stylesheet';
		css.href = this.stylesheet;
		if( document.getElementsByTagName("head").item(0) ) {
			document.getElementsByTagName("head").item(0).appendChild( css );
		} else {
			document.getElementsByTagName("body").item(0).appendChild( css );
		}
		
		var closeButton = document.createElement('a');
		closeButton.appendChild( document.createTextNode("x") );
		closeButton.className = 'close';
		closeButton.onclick = function() { return that.toggle(); }
		closeButton.href = '#';
		
		var postButton = document.createElement('a');
		postButton.appendChild( document.createTextNode("Post this Site") );
		postButton.onclick = function() { return that.selectSite(); }
		postButton.href = '#';
		
		var menuBar = document.createElement('div');
		menuBar.id = 'Asaph_Menu';
		menuBar.appendChild( document.createTextNode("Asaph // ") );
		menuBar.appendChild( postButton );
		menuBar.appendChild( closeButton );
		
		this.menu = document.createElement('div');
		this.menu.id = 'Asaph';
		this.menu.className = 'Asaph_Post';
		this.menu.appendChild( menuBar );
		document.body.appendChild( this.menu );
		
		
		var closeDialog = document.createElement('a');
		closeDialog.appendChild( document.createTextNode("^") );
		closeDialog.className = 'close';
		closeDialog.onclick = function() { that.dialog.style.display = 'none'; return false; }
		closeDialog.href = '#';
		
		this.dialog = document.createElement('div');
		this.dialog.id = 'Asaph_Dialog';
		this.iframe = document.createElement('iframe');
		this.iframe.src = 'about:blank';
		this.dialog.appendChild( closeDialog );
		this.dialog.appendChild( this.iframe );
		this.menu.appendChild( this.dialog );

		metaData = document.getElementsByTagName("meta");
		url = getData(metaData,"og:url");
		title = getData(metaData,"og:title");
		type = getData(metaData,"og:type");
		image = getData(metaData,"og:image");
		site_name = getData(metaData,"og:site_name");
		description = getData(metaData,"og:description");
		video = getData(metaData,"og:video");
		width = getData(metaData,"og:width");
		height = getData(metaData,"og:height");
		video_type = getData(metaData,"og:video:type");
		video_width = getData(metaData,"og:video:width");
		video_height = getData(metaData,"og:video:height");
		if(type=="instapp:photo")
		{
			this.loadIFrame( {
				'title': title,
				'image' : image,
				'description' : description,
				'source' : url,
				'xhrLocation': document.location.href.replace(/#.*$/,''),
				'width' :width,
				'height':height
			});
		}
		else if(type=="image")
		{
			this.loadIFrame( {
				'title': title,
				'image' : image,
				'description' : description,
				'source' : url,
				'xhrLocation': document.location.href.replace(/#.*$/,'')
			});
		}
		else if(type=="video")
		{
			alert("video");
			this.loadIFrame( {
				'title': title,
				'video' : video,
				'description' : description,
				'source' : url,
				'xhrLocation': document.location.href.replace(/#.*$/,''),
				'width' :video_width,
				'height':video_height,
				'video_type' : video_type

			});
		}
		else if(image!="")
		{
			//fall back on posting image
			this.loadIFrame( {
				'title': title,
				'image' : image,
				'description' : description,
				'source' : url,
				'xhrLocation': document.location.href.replace(/#.*$/,'')
			});
		}
		else
		{
			//post a link
			//TODO
			alert("not yet supported");
		}
		// TODO: Link & Quote

	}
	
	function getData(array,key)
	{
		for(i = 0;i<array.length;i++)
		{
			if(array[i].getAttribute("property")==key)
			{
				return array[i].getAttribute("content");
			}
		}
		return "";
	}

	this.loadIFrame = function( params ) {
		this.dialog.style.display = 'block';
		var reqUrl = this.postURL + '?nocache=' + parseInt(Math.random()*10000);
		for( p in params ) {
			reqUrl += '&' + p + '=' + encodeURIComponent( params[p] );
		}
		this.iframe.src = reqUrl;
	}
	
	
	this.selectSite = function() {
		var title = document.title;
		var selection = window.getSelection().toString();
		if( selection ) {
			title = '\u201C' + selection + '\u201D';
		}
		this.loadIFrame( {
			'title': title,
			'url': document.location.href,
			'xhrLocation': document.location.href.replace(/#.*$/,'')
		});
		return false;
	}
	
		
	
	this.checkSuccess = function() {
		if( document.location.href.match(/#Asaph_Success/) ) {
			var that = this;
			document.location.href = document.location.href.replace(/#.*$/, '#');
			setTimeout( function() { that.hide() }, 500 );
		}
	}
	
	
	this.show = function() {
		this.visible = true;
		var that = this;
		
		this.checkSuccessInterval = setInterval( function() { that.checkSuccess(); }, 500 );
		this.menu.style.display = 'block';
		
		/*var images = document.getElementsByTagName('img');
		for( var i=0; i<images.length; i++ ) {
			var img = images[i];
			if( img && img.src && img.src.match(/(space|blank)[^\/]*\.gif$/i) ) {
				img.style.display = 'none';
			}
			else if( img && img.src && img.width > this.minImageSize && img.height > this.minImageSize) {
				img.onclick = function( ev ) { 
					ev.stopPropagation(); 
					return that.selectImage(this); 
				};
				img.className = img.className ? img.className + ' Asaph_PostImage' : 'Asaph_PostImage';
			}
		}*/
	}
	
	
	this.hide = function() {
		this.visible = false;
		
		clearInterval( this.checkSuccessInterval );
		this.menu.style.display = 'none';
		this.dialog.style.display = 'none';
		/*
		var images = document.getElementsByTagName('img');
		for( var i=0; i<images.length; i++ ) {
			var img = images[i];
			if( img && img.src && img.width > this.minImageSize && img.height > this.minImageSize) {
				img.onclick = null;
				img.className = img.className.replace(/\s*Asaph_PostImage/, '');
			}
		}*/
	}
	
	
	this.toggle = function() {
		if( !this.visible ) {
			this.show();
		} else {
			this.hide();
		}
		return false;
	}
	
	
	this.create();
}

if( typeof(Asaph_Instance) == 'undefined' )  {
	var Asaph_Instance = new Asaph_RemotePost(
		'<?php echo ASAPH_POST_PHP; ?>', 
		'<?php echo ASAPH_POST_CSS; ?>'
	);
} 
Asaph_Instance.toggle();
