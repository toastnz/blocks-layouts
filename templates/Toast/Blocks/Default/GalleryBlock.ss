<colour-block id="{$BlockID}" tabIndex="0" class="default-gallery [ js-default-gallery ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-gallery__header">
                <div class="default-gallery__content">
                    <% if $Heading %>
                        <h2 class="default-gallery__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-gallery__wrap [ js-default-gallery__list ] columns-{$Columns}">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <div id="{$BlockItemID}" class="default-gallery-item [ js-in-view ]">
                        <button class="default-gallery-item__action [ js-default-gallery__action ] <% if $Video %>has-video<% end_if %>">
                            <div class="default-gallery-item__media">
                                <% if $Image %>
                                    <% with $Image %>
                                        <picture>
                                            <% if $Extension="svg" %>
                                                <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" {$SizeAttr} loading="lazy" alt="{$Title.ATT}">
                                            <% else %>
                                                <% with $Convert('webp') %>
                                                    <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                                    <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                                    <img loading="lazy" src="{$ScaleMaxWidth(960).URL}" alt="{$Title.ATT}" {$ScaleMaxWidth(960).SizeAttr} style="object-position: {$FocusPosition}">
                                                <% end_with %>
                                            <% end_if %>
                                        </picture>
                                    <% end_with %>
                                <% else_if $Video %>
                                    <img loading="lazy" src="{$Video.ThumbnailURL('large')}" alt="{$Video.Title}" width="1920" height="1080">
                                <% end_if %>

                                <% if $Video %>
                                    <div class="default-gallery-item__video">
                                        <div class="default-gallery-item__icon"></div>
                                    </div>
                                <% end_if %>
                            </div>
                        </button>
                    </div>
                <% end_loop %>
            <% end_if %>
        </div>

        <div class="default-gallery-modal [ js-default-gallery__modal ]">
            <div class="default-gallery-modal__container [ js-default-gallery__container ]">
                <div class="default-gallery-modal__slider [ js-default-gallery__slider ]">
                    <% if $Items.Count %>
                        <% loop $Items.Sort('SortOrder') %>
                            <div class="default-gallery-modal__item">
                                <div class="default-gallery-modal__media">
                                    <div class="default-gallery-modal__image <% if $Video %>has-video<% end_if %>">
                                        <% if $Image %>
                                            <% with $Image %>
                                                <picture>
                                                    <% if $Extension="svg" %>
                                                        <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" {$SizeAttr} loading="lazy" alt="{$Title.ATT}">
                                                    <% else %>
                                                        <% with $Convert('webp') %>
                                                            <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                                            <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                                                            <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" loading="lazy" alt="{$Title.ATT}" style="object-position: {$FocusPosition}">
                                                        <% end_with %>
                                                    <% end_if %>
                                                </picture>
                                            <% end_with %>
                                        <% else_if $Video %>
                                            <img loading="lazy" src="{$Video.ThumbnailURL('large')}" alt="{$Video.Title}" width="1920" height="1080">
                                        <% end_if %>

                                        <% if $Video %>
                                            <div class="default-gallery-modal__video" data-video="{$Video.IframeURL}">
                                                <div class="default-gallery-item__icon"></div>
                                            </div>
                                        <% end_if %>
                                    </div>
                                </div>
                            </div>
                        <% end_loop %>
                    <% end_if %>
                </div>
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
