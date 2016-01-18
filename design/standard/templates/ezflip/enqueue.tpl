<div class="content-view-full">
    <div class="class-flip">
        {if $errors|count()}
            <h1>Attenzione</h1>
            <ul>
            {foreach $errors as $error}
                <li>{$error}</li>
            {/foreach}
            </ul>
        {else}
            <h1>Il file {$attribute.content.original_filename} &egrave; in elaborazione</h1>
            <p>Appena il sistema avr&agrave; elaborato il file, sar&agrave; possibile sfogliarlo online.</p>
        {/if}
        {if $reflip}
            <p>Vuoi eseguire di nuovo la conversione del file? <a href={concat("/flip/enqueue/", $attribute_id, '/', $contentobject_version, '/', $object_id, '/', $node_id, '/2')|ezurl} />Clicca qui</a></p>
        {/if}
        <a href={$node.url_alias|ezurl}>Indietro</a>
    </div>
</div>