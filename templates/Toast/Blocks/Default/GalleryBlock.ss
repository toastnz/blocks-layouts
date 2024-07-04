<section id="{$HTMLID}" tabIndex="0" class="default-gallery [ js-default-gallery ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-gallery__header">
            <div class="default-gallery__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <% if $Items.Count %>
        <div class="default-gallery__wrap [ js-default-gallery__list ] columns-{$Columns}">
            <% loop $Items.Sort('SortOrder') %>
                <div class="default-gallery-item [ js-in-view ]">
                    <button class="default-gallery-item__action background-colour--{$Top.SecondaryColour.ColourClasses} [ js-default-gallery__action ] <% if $Video %>has-video<% end_if %>">
                        <div class="default-gallery-item__media">
                            <% if $Image %>
                                <picture>
                                    <source media="(max-width: 479px)" srcset="{$Image.ScaleMaxWidth(480).URL}">
                                    <source media="(max-width: 767px)" srcset="{$Image.ScaleMaxWidth(768).URL}">
                                    <img loading="lazy" src="{$Image.ScaleMaxWidth(960).URL}" alt="{$Image.Title.ATT}" width="{$Image.Width}" height="{$Image.Height}" style="object-position: {$getImageFocusPosition($Image.ID)}">
                                </picture>
                            <% else_if $Video %>
                                <img loading="lazy" src="{$Video.ThumbnailURL('large')}" alt="{$Video.Title}" width="1920" height="1080">
                            <% end_if %>

                            <% if $Video %>
                                <div class="default-gallery-item__video">
                                    <div class="default-gallery-item__icon colour--{$Top.SecondaryColour.ColourClasses}"></div>
                                </div>
                            <% end_if %>
                        </div>
                    </button>
                </div>
            <% end_loop %>
        </div>

        <div class="default-gallery-modal colour--{$Top.SecondaryColour.ColourClasses} [ js-default-gallery__modal ]">
            <div class="default-gallery-modal__slider [ js-default-gallery__slider ]">
                <% loop $Items.Sort('SortOrder') %>
                    <div class="default-gallery-modal__item">
                        <div class="default-gallery-modal__media">
                            <div class="default-gallery-modal__image <% if $Video %>has-video<% end_if %>">
                                <picture>
                                    <source media="(max-width: 767px)" srcset="{$Image.ScaleMaxWidth(768).URL}">
                                    <source media="(max-width: 1439px)" srcset="{$Image.ScaleMaxWidth(1440).URL}">
                                    <source media="(max-width: 1919px)" srcset="{$Image.ScaleMaxWidth(1920).URL}">
                                    <img loading="lazy" src="{$Image.ScaleMaxWidth(1920).URL}" alt="{$Image.Title.ATT}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}" style="object-position: {$getImageFocusPosition($Image.ID)}">
                                </picture>

                                <% if $Video %>
                                    <div class="default-gallery-modal__video" data-video="{$Video.IframeURL}">
                                        <div class="default-gallery-item__icon colour--{$Top.SecondaryColour.ColourClasses}"></div>
                                    </div>
                                <% end_if %>
                            </div>
                        </div>
                    </div>
                <% end_loop %>
            </div>
        </div>
    <% end_if %>
</section>
