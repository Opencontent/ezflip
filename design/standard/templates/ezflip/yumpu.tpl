{if and( is_set( $id ), is_set( $version ), is_set( $view ), flip_exists( $id, $version, $view ) )}
    {def $data = flip_data( $id, $version )}

    {if is_set( $data.document[0].iframe_src )}
        <iframe width="100%" height="{$data.document[0].height}px" frameborder="0" allowtransparency="true" allowfullscreen="true" src="{$data.document[0].iframe_src}"></iframe>
    {else
        {$data.document[0].embed_code}
    {/if}

    {undef $data}
{/if}