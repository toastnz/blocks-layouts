<% if $Tabs.Count %>
    <section class="default-tabbed-content background-colour--{$getColourClassName($BGColour)} {$getLightOrDark($BGColour)} {$IncludeClasses} {$ExtraClasses} [ js-tabs ]">
        <div class="default-tabbed-content__wrap">
            <div class="default-tabbed-content__header">
                <% loop $Tabs.Sort('SortOrder') %>
                    <div class="default-tab-link">
                        <button class="default-tab-link__button background-colour--{$getColourClassName($Top.AccentColour)} {$Top.getLightOrDark($Top.AccentColour)} [ js-tabs--link ]">
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
                                <div class="default-tab-item__image" data-src="{$Image.ScaleMaxWidth(800).URL}">
                                    <img src="{$Image.ScaleMaxWidth(20).URL}" alt="{$Image.Title.ATT}">
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