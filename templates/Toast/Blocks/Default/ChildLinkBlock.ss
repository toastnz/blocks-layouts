<% if $Items %>
    <section class="linkBlock contentBlock" data-equalize="summary,heading">
        <div class="linkBlock__wrap row md-up-2 xmd-up-$Columns">
            <% loop $Items %>
                <a href="<% if $LinkID %>$Link.LinkURL<% else %>$AbsoluteLink<% end_if %>" class="linkBlock__wrap__item column">
                    <div class="linkBlock__wrap__item__details" data-equalize-watch="heading">
                        <% if $Top.Columns == 2 %>
                            <h5>$Title.XML</h5>
                        <% else %>
                            <h6>$Title.XML</h6>
                        <% end_if %>
                    </div>
                    <div class="linkBlock__wrap__item__media media" style="background-image: url('{$Image.fill(640,640).URL}');">
                        <div class="linkBlock__wrap__item__media__content hoverContent">
                            <div class="alignContent">
                                <% if $Blocks__ContentSummary %>
                                    <div class="contentRow">
                                        <div class="verticalAlign verticalAlign--top" data-equalize-watch="summary">
                                            <p>
                                                $Blocks__ContentSummary.LimitCharacters(50)
                                            </p>
                                        </div>
                                    </div>
                                <% end_if %>
                                <div class="contentRow">
                                    <div class="verticalAlign verticalAlign--bottom">
                                        <span class="linkBlock__wrap__item__link link redirect--arrow">Read more</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <% end_loop %>
        </div>
    </section>
<% end_if %>