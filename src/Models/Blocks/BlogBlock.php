<?php

namespace Toast\Blocks;

class BlogBlock extends Block
{
    private static $singular_name = 'Blog Block';

    private static $plural_name = 'Blog Blocks';

    private static $description = 'Blog Block';

    private static $table_name = 'BlogBlock';

    private static  = [
    ];

    public function getCMSFields()
    {
         = parent::getCMSFields();

        return ;
    }
}


