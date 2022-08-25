<?php

namespace SilverStripe\UserForms\Form;

use SilverStripe\Dev\Debug;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\UserForms\Model\Submission\SubmittedForm;
use Firesphere\PartialUserforms\Models\PartialFormSubmission;


class UserFormExtension extends DataExtension
{

    // var_dump('dfdsd');

    private static $defaults = [
        'Content' => '',
        'OnCompleteMessage' => '<p>Thanks for getting in touch, we will get in touch with you soon.</p>'
    ];

}
