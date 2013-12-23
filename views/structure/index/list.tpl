{function menu_vert level =0}
    {foreach $data as $entry}

        {if isset($entry.children)}
            <div style='margin-left:{($entry.lvl-1)*50}px'>

                <div>
                    <a href="{$entry.link}">

                        {if !empty($entry.img)}
                            <div>
                                <img src='/structure/upload/{$entry.img}'>
                            </div>
                        {/if}

                        {if !empty($entry.title)}
                            <div>{$entry.title}</div>
                        {else}
                            <div>noname</div>
                        {/if}

                    </a>
                </div>

            </div>
            {menu_vert data=$entry.children level=level+1}
        {else}
            <div style='margin-left:{($entry.lvl-1)*50}px'>

                <div>
                    <a href="{$entry.link}">

                        {if !empty($entry.img)}
                            <div>
                                <img src='/media/img/icons/{$entry.img}'>
                            </div>
                        {/if}

                        <div>
                            {$entry.title}
                        </div>
                    </a>
                </div>

            </div>
        {/if}

    {/foreach}
{/function}

{menu_vert data=$structure}

{*{$structure|var_dump}*}