{* File - Full view *}
{if flip_exists( $node.data_map.file.id, $node.data_map.file.version )}
    {ezpagedata_set( 'extra_menu', false() )}
    {ezpagedata_set( 'left_menu', false() )}
{/if}

<section class="content-view-full">
    <article class="class-file">

        <div class="attribute-header">
            <h1>{$node.name|wash()}</h1>
        </div>

        {if $node.data_map.description.has_content}
            <div class="attribute-long">
                {attribute_view_gui attribute=$node.data_map.description}
            </div>
        {/if}

        {include uri='design:parts/flipper.tpl'
                 id=$node.data_map.file.id
                 version=$node.data_map.file.version
                 view='large'}

        <div class="attribute-file">
            <p><a href="{concat( 'content/download/', $node.data_map.file.contentobject_id, '/', $node.data_map.file.id,'/version/', $node.data_map.file.version , '/file/', $node.data_map.file.content.original_filename|urlencode )|ezurl( 'no' )}" class="btn btn-warning">{$node.data_map.file.content.original_filename|wash( xhtml )} {$node.data_map.file.content.filesize|si( byte )}</a></p>
        </div>

        <div class="attribute-tags">
            {attribute_view_gui attribute=$node.data_map.tags}
        </div>

        <div class="attribute-star-rating">
            {attribute_view_gui attribute=$node.data_map.star_rating}
        </div>

        <div class="attribute-comments">
            {attribute_view_gui attribute=$node.data_map.comments}
        </div>
    </article>
</section>