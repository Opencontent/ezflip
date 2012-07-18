{if flip_exists( $node.contentobject_id )}

    {ezpagedata_set( 'extra_menu', false() )}
    {ezpagedata_set( 'left_menu', false() )}

    {def $pageDim = get_page_dimensions( $node.contentobject_id, 'large' )
         $heigth = $pageDim[1]}
    
    {ezscript_require( array( 'megazine.js', 'swfaddress.js', 'swfobject.js' ) )}
    {ezcss_require( array('flip.css') )}
    
    <script type="text/javascript">
    {literal}
    swfobject.embedSWF(
        {/literal}{concat( 'flash/megazine/megazine.swf')|ezdesign}{literal},
        "megazine",
        "100%",
        "{/literal}{$heigth}{literal}",
        "9.0.115",
        {/literal}{concat( 'flash/swfobject/expressInstall.swf')|ezdesign}{literal}, 
        {
            {/literal}xmlFile : 'application_flip/{$node.object.id}/magazine_large.xml'{literal},  
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
    <div id="megazine"></div>
    <div class="attribute-file text-center">
        <p>
            <a href={concat("content/download/",$node.data_map.file.contentobject_id,"/",$node.data_map.file.id,"/file/",$node.data_map.file.content.original_filename)|ezurl}>
                {$node.data_map.file.content.mime_type|mimetype_icon( small, $node.data_map.file.content.mime_type )} {$node.data_map.file.content.original_filename|wash( xhtml )}
            </a> {$node.data_map.file.content.filesize|si( byte )}
        </p>
    </div>
    {undef $pageDim $heigth}
{/if}