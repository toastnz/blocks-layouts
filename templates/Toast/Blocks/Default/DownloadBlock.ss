<colour-block id="{$BlockID}" tabIndex="0" class="default-download [ js-default-download ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-download__header">
                <div class="default-download__content">
                    <% if $Heading %>
                        <h2 class="default-download__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-download__wrap">
            <div class="default-download__list">
                <% if $Items.Count %>
                    <% loop $Items.Sort('SortOrder') %>
                        <% with $File %>
                            <a id="{$Up.BlockItemID}" href="{$Link}" class="default-download-item [ js-in-view ]" download="{$Up.Title.ATT}">
                                <div class="default-download-item__background"></div>

                                <div class="default-download-item__title">
                                    <span>{$Up.Title}</span>
                                </div>

                                <div class="default-download-item__description">
                                    <span>{$Up.Summary}</span>
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
                <% end_if %>
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
