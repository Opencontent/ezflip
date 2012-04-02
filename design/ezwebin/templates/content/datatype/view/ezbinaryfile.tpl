{if is_set($icon_size)|not()}
    {def $icon_size='small'}
{/if}
{if is_set($icon_title)|not()}
    {def $icon_title=$attribute.content.mime_type}
{/if}
{if is_set($icon)|not()}
    {def $icon='yes'}
{/if}

{if $attribute.has_content}
{if $attribute.content}
{switch match=$icon}
    {case match='no'}
        {if flip_exists( $attribute.contentobject_id )}
            <a href={$attribute.object.main_node.url_alias|ezurl}>{$attribute.object.main_node.name|wash( xhtml )}</a>
        {else}
            <a href={concat("content/download/",$attribute.contentobject_id,"/",$attribute.id,"/file/",$attribute.content.original_filename)|ezurl}>{$attribute.content.original_filename|wash( xhtml )}</a> {$attribute.content.filesize|si( byte )}
        {/if}
    {/case}
    {case}
        {if flip_exists( $attribute.contentobject_id )}
            <a href={$attribute.object.main_node.url_alias|ezurl}>{$attribute.content.mime_type|mimetype_icon( $icon_size, $icon_title )} {$attribute.object.main_node.name|wash( xhtml )}</a>
        {else}
            <a href={concat("content/download/",$attribute.contentobject_id,"/",$attribute.id,"/file/",$attribute.content.original_filename)|ezurl}>{$attribute.content.mime_type|mimetype_icon( $icon_size, $icon_title )} {$attribute.content.original_filename|wash( xhtml )}</a> {$attribute.content.filesize|si( byte )}
        {/if}
    {/case}
{/switch}
{else}
    <div class="message-error"><h2>{'The file could not be found.'|i18n( 'design/ezwebin/view/ezbinaryfile' )}</h2></div>
{/if}
{/if}