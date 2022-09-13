<% if $Items.Count %>
    <section class="default-download background-colour--{$BGColourClassName} {$getLightOrDark($BGColourClassName)} {$ExtraClasses}">
        <div class="default-download__wrap">
            <div class="default-download__list">
                <% loop $Items.Sort('SortOrder') %>
                    <% with $File %>
                        <a href="{$Link}" class="default-download-item background-colour--{$Top.AccentColourClassName} {$Top.getLightOrDark($Top.AccentColourClassName)}" download>
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
