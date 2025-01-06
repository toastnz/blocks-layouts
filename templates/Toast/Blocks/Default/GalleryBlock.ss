<colour-block id="{$BlockID}" tabIndex="0" class="default-gallery [ js-default-gallery ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-gallery__header">
                <div class="default-gallery__content">
                    <% if $Heading %>
                        <h2 class="default-gallery__heading">{$Heading.XML}</h2>
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
                                    <picture>
                                        <% if $Extension="svg" %>
                                            <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="{$Width}" height="{$tHeight}" loading="lazy" alt="{$Title.ATT}">
                                        <% else %>
                                            <source media="(max-width: 479px)" srcset="{$Image.ScaleMaxWidth(480).URL}">
                                            <source media="(max-width: 767px)" srcset="{$Image.ScaleMaxWidth(768).URL}">
                                            <img loading="lazy" src="{$Image.ScaleMaxWidth(960).URL}" alt="{$Image.Title.ATT}" width="{$Image.Width}" height="{$Image.Height}" style="object-position: {$getImageFocusPosition($Image.ID)}">
                                        <% end_if %>
                                    </picture>
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
                                        <% with $Image %>
                                            <picture>
                                                <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                                <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                                                <source media="(max-width: 1919px)" srcset="{$ScaleMaxWidth(1920).URL}">
                                                <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" width="{$getWidth()}" height="{$getHeight()}" loading="lazy" alt="{$Title.ATT}" style="object-position: {$FocusPosition}">
                                            </picture>
                                        <% end_with %>

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
