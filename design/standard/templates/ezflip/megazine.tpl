{if and( is_set( $id ), is_set( $version ), is_set( $view ), flip_exists( $id, $version, $view ) )}
  {def $pageDim = get_page_dimensions( $id, $version, $view )
       $heigth = $pageDim[1]}

  {ezscript_require( array( 'megazine.js', 'swfaddress.js', 'swfobject.js' ) )}
  {ezcss_require( array('flip.css') )}
  <script type="text/javascript">
    {literal}
    swfobject.embedSWF(
        {/literal}{concat( 'flash/megazine/megazine.swf')|ezdesign}{literal},
        "megazine-{/literal}{$id}-{$version}{literal}",
        "100%",
        "{/literal}{$heigth}{literal}",
        "9.0.115",
        {/literal}{concat( 'flash/swfobject/expressInstall.swf')|ezdesign}{literal},
        {
          {/literal}xmlFile : '{flip_dir( $id, $version)}/magazine_{$view}.xml'{literal},
          minScale : 1.0,
          maxScale : 1.0,
          top: "20"
        },
        {
          bgcolor : "#fff",
          wmode : "transparent",
          allowFullscreen : "true"
        },
        {id : "megazine-{/literal}{$id}-{$version}{literal}"}
    );
    {/literal}
  </script>
  <div id="megazine-{$id}-{$version}"></div>
  {undef $pageDim $heigth}
{/if}