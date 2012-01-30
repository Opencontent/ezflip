{*  
  {if and( 
            or( ezini( 'MultiUploadSettings', 'AvailableSubtreeNode', 'ezflip.ini' )|contains( $current_node.node_id ),
                ezini( 'MultiUploadSettings', 'AvailableClasses', 'ezflip.ini' )|contains( $current_node.class_identifier ) ) ,
            and( $content_object.can_create, $is_container) 
         )}
    <a href={concat("/flip/upload/",$current_node.node_id)|ezurl} title="{'Pdf upload'|i18n('extension/ezmultiupload')}"><img src={"ezwt-icon-pdf-upload.gif"|ezimage} alt="{'Pdf upload'|i18n('extension/ezflip')}" /></a>
  {/if}
*}

{if is_set( $content_object.data_map.file )}
{if $content_object.data_map.file.has_content}
    {if eq( $content_object.data_map.file.content.mime_type, 'application/pdf' )}
        {if flip_exists( $content_object.id )|not() }
            <a href={concat("/flip/enqueue/", $content_object.data_map.file.content.contentobject_attribute_id, '/', $content_object.data_map.file.content.version, '/', $content_object.id, '/', $content_object.main_node_id)|ezurl} title="Rendi sfogliabile il file {$content_object.data_map.file.content.original_filename}"><img src={"ezwt-icon-pdf-upload.gif"|ezimage} alt="Rendi sfogliabile il file {$content_object.data_map.file.content.original_filename}" /></a>
        {else}
            <a href={concat("/flip/enqueue/", $content_object.data_map.file.content.contentobject_attribute_id, '/', $content_object.data_map.file.content.version, '/', $content_object.id, '/', $content_object.main_node_id, '/1')|ezurl} title="Rendi sfogliabile il file {$content_object.data_map.file.content.original_filename}"><img src={"ezwt-icon-pdf-upload.gif"|ezimage} alt="Rendi sfogliabile il file {$content_object.data_map.file.content.original_filename}" /></a>

        {/if}
    {/if}
{/if}
{/if}