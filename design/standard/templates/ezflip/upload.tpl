{ezscript_require( array( 'ezjsc::jquery', 'ezjsc::jqueryio', 'swfobject.js', 'ezflipupload.js' ) )}
{ezcss_require( array('ezflipupload.css') )}

<script type="text/javascript">
$(document).ready(function() {ldelim}
var uploader = '{"flash/uploadify.swf"|ezdesign(no)}';
var script = '{concat( 'flip/upload/', $parent_node.node_id )|ezurl( 'no' )}';
var fileDesc = "{'Allowed Files'|i18n('extension/ezmultiupload')|wash(javascript)}";
var fileExt = "{$file_types}";
var cancelImg = '{"cancel.png"|ezimage(no)}';
{literal}	
	$("#uploadify").ezflipupload({
		uploader: uploader,
		script: script,
		folder: '/',
		scriptData: {{/literal}'{$session_name}': '{$session_id}','UserSessionHash': '{$user_session_hash}','UploadButton': 'Upload'{literal}},
		checkScript: 'ezflip::checkScript',
		fileExt: fileExt,
		fileDesc: fileDesc,
		cancelImg: cancelImg,
		auto: true,
		multi: true
	});
{/literal}
{rdelim});
</script>

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="content-view-ezmultiupload">
    <div class="class-frontpage">
    
    <div class="attribute-header">
        <h1 class="long">{'Pdf upload'|i18n('extension/ezmultiupload')}</h1>
    </div>
        <div class="attribute-description">
            <p>{'The files are uploaded to'|i18n('extension/ezmultiupload')} <a href={$parent_node.url_alias|ezurl}>{$parent_node.name|wash}</a></p>
			
				<input type="file" name="uploadify" id="uploadify" />
				<p> 
					<a href="javascript:jQuery('#uploadify').uploadifyClearQueue()">Cancel All Uploads</a>
				</p>
        </div>
    </div>
</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>