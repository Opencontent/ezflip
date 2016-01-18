{if flip_exists( $node.contentobject_id )}
{ezpagedata_set( 'extra_menu', false() )}
{ezpagedata_set( 'left_menu', false() )}
{/if}


<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="content-view-full">
    <div class="class-file">

    <div class="attribute-header">
        <h1>{if $node.parent_node_id|ne(2)}<a href={$node.parent.url_alias|ezurl}><span>{$node.parent.name|wash} | </span></a>{/if}{$node.name|wash()}</h1>
    </div>

    {if $node.data_map.description.content.is_empty|not}
        <div class="attribute-long">
            {attribute_view_gui attribute=$node.data_map.description}
        </div>
    {/if}

    {if flip_exists( $node.contentobject_id )}
        
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
                {/literal}xmlFile : '{symlink_flip_dir()}/{$node.object.id}/magazine_large.xml'{literal},  
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
        
        
            
            
        <div id="megazine">
                
            {def $page_limit = 10
                 $classes = array()
                 $children_count = ''}
        
            {set $classes = array( 'image' )
                 $children_count=fetch_alias( 'children_count', hash( 'parent_node_id', $node.object.main_node_id,
                                                                      'class_filter_type', 'include',
                                                                      'class_filter_array', $classes ) )}
        
            
            {if $children_count}
                {include name=gallery_line uri='design:node/view/line_gallery.tpl' nodes=fetch_alias( 'children', hash( 'parent_node_id', $node.object.main_node_id,
                                                                'offset', $view_parameters.offset,
                                                                'sort_by', array( 'name', true() ),
                                                                'class_filter_type', 'include',
                                                                'class_filter_array', $classes
                                                                ) )}
            {/if}
                
        </div>
    
    
    {/if}

        <div class="attribute-file">
            <p>
                <a href={concat("content/download/",$node.data_map.file.contentobject_id,"/",$node.data_map.file.id,"/file/",$node.data_map.file.content.original_filename)|ezurl}>
                    {$node.data_map.file.content.mime_type|mimetype_icon( small, $node.data_map.file.content.mime_type )} {$node.data_map.file.content.original_filename|wash( xhtml )}
                </a> {$node.data_map.file.content.filesize|si( byte )}
            </p>
        </div>

    </div>
</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>
