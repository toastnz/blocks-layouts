<% if $Items.Count %>
    <section id="{$HTMLID}" class="default-download [ js-default-download ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-download__wrap">
            <div class="default-download__list">
                <% loop $Items.Sort('SortOrder') %>
                    <% with $File %>
                        <a href="{$Link}" class="default-download-item background-colour--{$Top.SecondaryColour.getColourClasses}" download>
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