<% if $Items.Count %>
    <section class="download content-block--padding {$ExtraClasses}">
        <div class="download__wrap">
            <% loop $Items.Sort('SortOrder') %>
                <% with $File %>
                    <a href="{$Link}" class="download-item" download>
                        <div class="download-item__title">
                            <span>{$Up.Title.XML}</span>
                        </div>

                        <div class="download-item__description">
                            <span>{$Up.Summary.XML}</span>
                        </div>

                        <div class="download-item__info">
                            <span>{$Extension.upperCase} {$Size}</span>
                        </div>

                        <div class="download-item__icon">
                            <span></span>
                        </div>
                    </a>
                <% end_with %>
            <% end_loop %>
        </div>
    </section>
<% end_if %>
