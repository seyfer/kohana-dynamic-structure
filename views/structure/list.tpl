{function menu_vert level =0}
    {foreach $data as $entry}

        {if isset($entry.children)}
            <div class='row elementMenu' nom='{$entry.id}'
                 style='margin-left:{($entry.lvl-1)*50}px'>

                <div class='oneline addStruct'>
                    <a href = '/structure/add/{$entry.id}'></a>
                </div>

                <div class='oneline deleteStruct'>
                    <a href ='/structure/delete/{$entry.id}'></a>
                </div>

                {if $entry.visible}
                    <div class='oneline'>
                        <img src='/structure/media/img/eyeBig.png'
                             style='width:24px;height:24px;'>
                    </div>
                {/if}

                <div class='oneline'>
                    <a href="/structure/edit/{$entry.id}" class="red_link dir">
                        {if !empty($entry.img)}
                            <div class='oneline'>
                                <img src='/structure/upload/{$entry.img}'>
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
                    <a href = '/structure/add/{$entry.id}'></a>
                </div>

                <div class='oneline deleteStruct'>
                    <a href = '/structure/delete/{$entry.id}'></a>
                </div>

                {if $entry.visible}
                    <div class='oneline'>
                        <img src='/public/img/eyeBig.png'
                             style='width:24px;height:24px;'>
                    </div>
                {/if}

                <div class='oneline'>
                    <a href="/structure/edit/{$entry.id}" class="red_oneline dir">
                        {if !empty($entry.img)}
                            <div class='oneline'>
                                <img src='/structure/upload/{$entry.img}'>
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
    <a href ="/structure/addRoot/">Добавить корневой узел</a>
</div>

{menu_vert data=$left_menu_arr}

