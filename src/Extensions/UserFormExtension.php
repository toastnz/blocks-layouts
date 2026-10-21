<?php

/**
 * Note: this class lives in the userforms namespace for historic reasons. It is not applied by the module;
 * apply it to SilverStripe\UserForms\Model\UserDefinedForm in your project to use its defaults.
 */

namespace SilverStripe\UserForms\Form;

use SilverStripe\Core\Extension;

/**
 * Sensible defaults for new User Defined Form pages.
 */
class UserFormExtension extends Extension
{
    private static $defaults = [
        'Content' => '',
        'OnCompleteMessage' => '<p>Thanks for getting in touch, we will get in touch with you soon.</p>'
    ];
}
