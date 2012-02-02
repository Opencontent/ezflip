{if is_set($scope)|not() }
    {def $scope = false()}
{/if}

{if is_set( $node )}

    {if $node.object.class_identifier|eq('gallery')}
        <h2><a href={$node.url_alias|ezurl()}>{$node.name|wash()}</a></h2>
    {/if}
    
    {def $nodes = fetch( 'content', 'list',  hash( 'parent_node_id', $node.node_id,
                                                   'class_filter_type', 'include',
                                                   'class_filter_array', array('image', 'flash_player', 'ezflowmedia'),
                                                   'limit', 30 ) )
         $node_id = $node.node_id}
    
{elseif is_set( $nodes )}
    
    {def $node_id = rand( 0, 10000 )}
    
{/if}

{ezcss_require(array( 'overlay-gallery.css', 'carousel.css' ) )}
{ezscript_require(array( 'ezjsc::jquery', 'jcarousel.js', 'jquery.tools.min.js' ) )}

{run-once}
<script type="text/javascript">
{literal}
<!--//--><![CDATA[//><!--
$(document).ready(function() {
    $('.simple_overlay').each( function(){
        $('body').append($(this));
    });
});
//--><!]]>
{/literal}
</script>
{/run-once}

<script type="text/javascript">
{literal}
<!--//--><![CDATA[//><!--
$(document).ready(function() {
    $("#banner_carousel-{/literal}{$node_id}{literal}").jcarousel({scroll:2});
    $("#banner_carousel-{/literal}{$node_id}{literal} .attribute-image img[rel]").overlay({ expose: '#f1f1f1' });
});
//--><!]]>
{/literal}
</script>

<div class="banner-carousel block float-break">

    <ul id="banner_carousel-{$node_id}" class="jcarousel-list">
    
    {foreach $nodes as $banner}
    <li class="banner-carousel-item  jcarousel-item">
        <div class="attribute-image">
            <p class="no-js-hide gallery">
                <img src={$banner.data_map.image.content.carouselthumbnail.url|ezroot} alt="{$banner.name|wash}" rel="#gallery_{$banner.node_id}"/>
            </p>
            <p class="no-js-show">
                {attribute_view_gui attribute=$banner.data_map.image image_class=carouselthumbnail href=$banner.url_alias|ezurl}            
            </p>
        </div>
        <div class="attribute-name">
            <p class="no-js-hide gallery-name">{$banner.name|shorten(80)|wash}</p>
            <a class="no-js-show gallery-name" href={$banner.url_alias|ezurl()}>{$banner.name|shorten(80)|wash}</a>
        </div>
        <div class="simple_overlay" id="gallery_{$banner.node_id}" style="display:none;">
            {attribute_view_gui attribute=$banner.data_map.image image_class=carouseloverlay}
        </div>
    </li>
    {/foreach}
    
    </ul>

</div>
