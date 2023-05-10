<?php

namespace Toast\Blocks;

class ChildrenBlock extends Block
{
    private static $singular_name = 'Children Block';

    private static $plural_name = 'Children Blocks';

    private static $description = 'Children Block';

    private static $table_name = 'ChildrenBlock';

    private static  = [
    ];

    public function getCMSFields()
    {
         = parent::getCMSFields();

        return ;
    }
}


