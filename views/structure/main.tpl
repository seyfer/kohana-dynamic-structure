{foreach from=$styles item=file_style}
    {HTML::style($file_style)}
{/foreach}
{foreach from=$scripts item=file_script}
    {HTML::script($file_script)}
{/foreach}


{$content}