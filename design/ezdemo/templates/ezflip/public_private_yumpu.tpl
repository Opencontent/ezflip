{if and( is_set( $id ), is_set( $version ), is_set( $view ), flip_exists( $id, $version, $view ) )}
    {def $data = flip_data( $id, $version )}

    {if and( fetch( user, current_user ).is_logged_in, $data.versions|contains( 'private' ) )}
      {if is_set( $data.private.document[0].iframe_src )}
          <iframe width="100%" height="{$data.private.document[0].height}px" frameborder="0" allowtransparency="true" allowfullscreen="true" src="{$data.private.document[0].iframe_src}"></iframe>
      {else}
          {$data.private.document[0].embed_code}
      {/if}
    {elseif $data.versions|contains( 'public' )}
      {if is_set( $data.public.document[0].iframe_src )}
          <iframe width="100%" height="{$data.public.document[0].height}px" frameborder="0" allowtransparency="true" allowfullscreen="true" src="{$data.public.document[0].iframe_src}"></iframe>
      {else}
          {$data.public.document[0].embed_code}
      {/if}
    {/if}
    
    {undef $data}
{/if}