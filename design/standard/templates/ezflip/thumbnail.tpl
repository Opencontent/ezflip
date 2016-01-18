{if $result.errors|count()|gt(0)}
    <div class="error-block">
        <h2>{'Error'|i18n('extension/ezmultiupload')}</h2>
        <ul>
        {foreach $result.errors as $error}
            <li>{$error.description}</li>
        {/foreach}
        </ul>
    </div>
{elseif is_set( $result.contentobject )}
    
	{def $node = $result.contentobject.main_node}	
	<div class="content-view-line-thumbnail">
		<div class="class-{$node.object.class_identifier} float-break">
			{if is_set( $node.url_alias )}
			<h2><a href="{$node.url_alias|ezurl('no')}" title="{$node.name|wash}">{$node.name}</a></h2>
			{else}
			<h2>{$node.name}</h2>
			{/if}
			<div class="content-file">
			{def $attribute=$node.data_map.file}
				<p>
					{set-block scope=global variable=cache_ttl}0{/set-block}
					{def $icon_size='normal'
						 $icon_title=$attribute.content.mime_type
						 $icon='yes'}
					{if $attribute.has_content}
					{if $attribute.content}
					{switch match=$icon}
						{case match='no'}
							<a href={concat("content/download/",$attribute.contentobject_id,"/",$attribute.id,"/file/",$attribute.content.original_filename)|ezurl}>{$attribute.content.original_filename|wash( xhtml )}</a> {$attribute.content.filesize|si( byte )}
						{/case}
						{case}
							{if $attribute.content.mime_type|eq('application/pdf')}
								<div class="attribute-image object-right">
								<img src={$attribute.content.filepath|pdfpreview( 200, 200, 1, "My PDF.pdf" )|ezroot} alt="Preview">
								</div>
							{/if}
							<h3>Nome del file: </h3>
							<p>{$attribute.content.original_filename|wash( xhtml )}</p>
							<h3>Scarica il file:</h3>
							<p><a href={concat("content/download/",$attribute.contentobject_id,"/",$attribute.id,"/file/",$attribute.content.original_filename)|ezurl}>{$attribute.content.original_filename|wash( xhtml )}</a> </p>
							<h3>Dimensioni:</h3> 
							</p>{$attribute.content.filesize|si( byte )}</p>

						{/case}
					{/switch}
					{else}
						<div class="message-error"><h2>{'The file could not be found.'|i18n( 'design/ezwebin/view/ezbinaryfile' )}</h2></div>
					{/if}
					{/if}			
				</p>
			</div>
		</div>
	</div>

	
{/if}
