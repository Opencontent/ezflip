<div class="content-view-full">
    <div class="class-flip">
        {if $errors|count()}
            <div class="warning message-warning alert alert-warning">
                {foreach $errors as $error}
                    {$error}
                    {delimiter}<br />{/delimiter}
                {/foreach}
            </div>
        {else}
            <h1>{"The file %filename is being processed."|i18n('extension/ezflip', '', hash( '%filename', $attribute.content.original_filename ) )}</h1>
            <p>{"When the system has processed the file, you can <em>flip</em> it online."|i18n('extension/ezflip')}</p>
        {/if}
        {if $reflip}
            <p>{"Do you want to rerun the conversion of the file?"|i18n('extension/ezflip')}</p>
            <a class="btn btn-warning" href={concat("/flip/enqueue/", $attribute.id, '/', $attribute.version, '/2')|ezurl} />{"Reconvert"|i18n('extension/ezflip')}</a>
        {/if}
        <a class="btn" href={$attribute.object.main_node.url_alias|ezurl}>{"Back"|i18n('extension/ezflip')}</a>
    </div>
</div>