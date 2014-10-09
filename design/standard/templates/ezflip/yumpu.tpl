{if and( is_set( $id ), is_set( $version ), is_set( $view ), flip_exists( $id, $version, $view ) )}
    {def $data = flip_data( $id, $version )}
    {$data.document[0].embed_code}
    {undef $data}
{/if}