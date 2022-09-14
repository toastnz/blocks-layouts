<% if $Items.Count %>
    <section class="default-download background-colour--{$getColourClassName($BGColour)} {$getLightOrDark($BGColour)} {$ExtraClasses}">
        <div class="default-download__wrap">
            <div class="default-download__list">
                <% loop $Items.Sort('SortOrder') %>
                    <% with $File %>
                        <a href="{$Link}" class="default-download-item background-colour--{$getColourClassName($Top.AccentColour)} {$Top.getLightOrDark($Top.AccentColour)}" download>
                            <div class="default-download-item__title">
                                <span>{$Up.Title.XML}</span>
                            </div>
    
                            <div class="default-download-item__description">
                                <span>{$Up.Summary.XML}</span>
                            </div>
    
                            <div class="default-download-item__info">
                                <span>{$Extension.upperCase} {$Size}</span>
                            </div>
    
                            <div class="default-download-item__icon">
                                <span></span>
                            </div>
                        </a>
                    <% end_with %>
                <% end_loop %>
            </div>
        </div>
    </section>
<% end_if %>
