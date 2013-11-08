{foreach from=$styles item=file_style}
    {html::style($file_style)}
{/foreach}
{foreach from=$scripts item=file_script}
    {html::script($file_script)}
{/foreach}


{$content}