<% if $Tabs.Count %>
    <section id="{$HTMLID}" class="default-tabbed-content [ js-default-tabbed-content ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-tabbed-content__wrap">
            <div class="default-tabbed-content__header">
                <% loop $Tabs.Sort('SortOrder') %>
                    <div class="default-tab-link">
                        <button class="default-tab-link__button background-colour--{$Top.SecondaryColour.getColourClasses} [ js-tabs--link ]">
                            <span class="default-tab-link__text">{$Title}</span>
                        </button>
                    </div>
                <% end_loop %>
            </div>
    
            <div class="default-tabbed-content__main">
                <% loop $Tabs.Sort('SortOrder') %>
                    <div class="default-tab-item [ js-tabs--item ]">
                        <% if $Image %>
                            <div class="default-tab-item__media">
                                <div class="default-tab-item__image" data-src="{$Image.ScaleMaxWidth(800).URL}" style="background-position: {$getImageFocusPosition($Image.ID)}">
                                    <img loading="lazy" src="{$Image.ScaleMaxWidth(8).URL}" alt="{$Image.Title.ATT}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}">
                                </div>
                            </div>
                        <% end_if %>
                        
                        <% if $Content %>
                            <div class="default-tab-item__main">
                                {$Content}
                            </div>
                        <% end_if %>
                    </div>
                <% end_loop %>
            </div>
        </div>
    </section>
<% end_if %>