{ezscript_require( array( 'megazine.js', 'swfaddress.js', 'swfobject.js' ) )}
{ezcss_require( array('flip.css') )}

<script type="text/javascript">
{literal}
swfobject.embedSWF(
	{/literal}{concat( 'flash/megazine/megazine.swf')|ezdesign}{literal},
	"megazine",
	"100%",
	"600",
	"9.0.115",
	{/literal}{concat( 'flash/swfobject/expressInstall.swf')|ezdesign}{literal}, 
	{
		{/literal}xmlFile : 'application_flip/{$object.id}/magazine_small.xml'{literal},  
		minScale : 1.0,
		maxScale : 1.0,
		top: "20"
	},
	{
	bgcolor : "#fff", 
	wmode : "transparent", 
	allowFullscreen : "true" 
	},
	{id : "megazine"}
);
{/literal}
</script>

<div class="content-view-full">
  <div class="class-flip">
	<h1><a href={$object.main_node.parent.url_alias|ezurl()}>{$object.main_node.parent.name|wash()}</a> &raquo; <a href={$object.main_node.url_alias|ezurl()}>{$object.main_node.name|wash()}</a></h1>
	
	<p><a href={concat("content/download/",$object.id,"/",$object.id,"/file/",$object.data_map.file.content.original_filename)|ezurl}>[Download file]</a> </p>
    
	<div class="outer">    
        <div id="megazine">
            <h2>FlashPlayer 9 required!</h2>
            <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
        </div>
    </div>
	
  </div>
</div>