<?php

namespace Toast\Blocks;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\UserForms\Form\UserForm;
use SilverStripe\UserForms\Model\UserDefinedForm;

/**
 * Renders the form of the User Defined Form page it is placed on.
 */
class UserFormBlock extends Block
{
    private static $table_name = 'Blocks_UserFormBlock';

    private static $singular_name = 'User form';

    private static $plural_name = 'User forms';

    protected static $icon_class = 'font-icon-block-form';

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->insertAfter(
                'Title',
                LiteralField::create('UserFormNotice', '<div class="message warning"><strong>Note:</strong><br />Form must be configured from the <strong>Form Fields</strong> page tab and only applies to <strong>User Defined Form</strong> page types.</div>')
            );
        });

        return parent::getCMSFields();
    }

    /**
     * The page's user form, or null when the block isn't on a User Defined Form page.
     *
     * @return UserForm|null
     */
    public function Form()
    {
        $page = $this->getParentPage();
        $controller = Controller::curr();

        if (!$page instanceof UserDefinedForm || !$controller) {
            return null;
        }

        $form = UserForm::create($controller, 'Form_' . $page->ID);
        $form->setFormAction(Controller::join_links($page->Link(), 'Form'));

        if ($controller->hasMethod('generateConditionalJavascript')) {
            $controller->generateConditionalJavascript();
        }

        return $form;
    }
}
