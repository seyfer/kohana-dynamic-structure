{function menu_vert level =0}
    {foreach $data as $entry}

        {if isset($entry.children)}
            <div class='row elementMenu' nom='{$entry.id}'
                 style='margin-left:{($entry.lvl-1)*50}px'>

                <div class='oneline addStruct'>
                    <a href = '{$routePath}/add/{$entry.id}'>
                        <img src='{$routePath}/media/img/add.png'
                             style='width:24px;height:24px;'>
                    </a>
                </div>

                <div class='oneline deleteStruct'>
                    <a href ='{$routePath}/delete/{$entry.id}'>
                        <img src='{$routePath}/media/img/delete.png'
                             style='width:24px;height:24px;'>
                    </a>
                </div>

                {if $entry.visible}
                    <div class='oneline'>
                        <img src='{$routePath}/media/img/eyeBig.png'
                             style='width:24px;height:24px;'>
                    </div>
                {/if}

                <div class='oneline'>
                    <a href="{$routePath}/edit/{$entry.id}" class="red_link dir">
                        {if !empty($entry.img)}
                            <div class='oneline'>
                                <img src='{$routePath}/upload/{$entry.img}'>
                            </div>
                        {/if}
                        {if !empty($entry.title)}
                            <div class='oneline'>{$entry.title}</div>
                        {else}
                            <div class='oneline'>noname</div>
                        {/if}
                    </a>
                </div>

            </div>
            {menu_vert data=$entry.children level=level+1}
        {else}
            <div class='row elementMenu {if $param==$entry.id}selected{/if}'
                 nom='{$entry.id}' style='margin-left:{($entry.lvl-1)*50}px'>

                <div class='oneline addStruct'>
                    <a href = '{$routePath}/add/{$entry.id}'>
                        <img src='{$routePath}/media/img/add.png'
                             style='width:24px;height:24px;'>
                    </a>
                </div>

                <div class='oneline deleteStruct'>
                    <a href ='{$routePath}/delete/{$entry.id}'>
                        <img src='{$routePath}/media/img/delete.png'
                             style='width:24px;height:24px;'>
                    </a>
                </div>

                {if $entry.visible}
                    <div class='oneline'>
                        <img src='{$routePath}/media/img/eyeBig.png'
                             style='width:24px;height:24px;'>
                    </div>
                {/if}

                <div class='oneline'>
                    <a href="{$routePath}/edit/{$entry.id}" class="red_oneline dir">
                        {if !empty($entry.img)}
                            <div class='oneline'>
                                <img src='{$routePath}/upload/{$entry.img}'>
                            </div>
                        {/if}

                        <div class='oneline'>
                            {$entry.title}
                        </div>
                    </a>
                </div>

            </div>
        {/if}

    {/foreach}
{/function}

<div>
    <a href ="{$routePath}/addRoot/">
        <img src='{$routePath}/media/img/add.png'
             style='width:24px;height:24px;'>
        Добавить корневой узел
    </a>
</div>

{menu_vert data=$left_menu_arr routePath=$routePath}

