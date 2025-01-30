<colour-block id="{$BlockID}" tabIndex="0" class="default-link [ js-default-link ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-link__header">
                <div class="default-link__content">
                    <% if $Heading %>
                        <h2 class="default-link__heading">{$Heading.XML}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-link__wrap columns-{$Columns}">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <div id="{$BlockItemID}" class="default-link-item [ js-in-view ]">
                        <a href="{$Link.LinkURL}" {$Link.LinkAttributes} class="default-link-item__link">
                            <% if $Image %>
                                <div class="default-link-item__media">
                                    <% with $Image %>
                                        <picture>
                                            <% if $Extension="svg" %>
                                                <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" loading="lazy" alt="{$Title.ATT}">
                                            <% else %>
                                                <% with $Convert('webp') %>
                                                    <source media="(max-width: 479px)" srcset="{$FocusFillMax(480,480).URL}">
                                                    <source media="(max-width: 767px)" srcset="{$FocusFillMax(768,768).URL}">
                                                    <img loading="lazy" src="{$FocusFillMax(960,960).URL}" alt="{$Title.ATT}" width="960" height="960" style="object-position: {$FocusPosition}">
                                                <% end_with %>
                                            <% end_if %>
                                        </picture>
                                    <% end_with %>
                                </div>
                            <% end_if %>

                            <div class="default-link-item__details">
                                <% if $Title %>
                                    <span class="default-link-item__title">{$Title.XML}</span>
                                <% end_if %>

                                <% if $Summary %>
                                    <p class="default-link-item__summary">{$Summary.XML}</p>
                                <% end_if %>

                                <span class="default-link-item__button read-more">{$Link.Title}</span>
                            </div>
                        </a>
                    </div>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
