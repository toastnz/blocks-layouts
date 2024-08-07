<% if $Items.Count %>
    <section id="{$BlockID}" tabIndex="0" class="default-download [ js-default-download ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <% if $Content %>
            <div class="default-download__header">
                <div class="default-download__content">
                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-download__wrap">
            <div class="default-download__list">
                <% loop $Items.Sort('SortOrder') %>
                    <% with $File %>
                        <a href="{$Link}" class="default-download-item [ js-in-view ]" download="{$Up.Title.ATT}">
                            <div class="default-download-item__background background-colour--{$Top.SecondaryColour.ColourClasses}"></div>

                            <div class="default-download-item__title background-colour--{$Top.SecondaryColour.ColourClasses}">
                                <span>{$Up.Title.XML}</span>
                            </div>

                            <div class="default-download-item__description background-colour--{$Top.SecondaryColour.ColourClasses}">
                                <span>{$Up.Summary.XML}</span>
                            </div>

                            <div class="default-download-item__info background-colour--{$Top.SecondaryColour.ColourClasses}">
                                <span>{$Extension.upperCase} {$Size}</span>
                            </div>

                            <div class="default-download-item__icon background-colour--{$Top.SecondaryColour.ColourClasses}">
                                <span></span>
                            </div>
                        </a>
                    <% end_with %>
                <% end_loop %>
            </div>
        </div>
    </section>
<% end_if %>
