<?php

namespace Toast\Blocks;

class TextBlock extends Block
{
    private static $table_name = 'Blocks_TextBlock';

    private static $singular_name = 'Text';

    private static $plural_name = 'Text';

    protected static $icon_class = 'font-icon-block-content';

    private static $required_fields = [
        'Content',
    ];
}
