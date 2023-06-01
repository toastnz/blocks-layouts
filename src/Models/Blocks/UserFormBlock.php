<?php

namespace Toast\Blocks;

use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\UserForms\Model\UserDefinedForm;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Control\Controller;
use SilverStripe\UserForms\Form\UserForm;

class UserFormBlock extends Block
{
    private static $table_name = 'Blocks_UserFormBlock';
    
    private static $singular_name = 'User form';
    
    private static $plural_name = 'User forms';

    private static $db = [
        'Content' => 'HTMLText'
    ];

    public function getCMSFields()
    {        
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                HTMLEditorField::create('Content', 'Content')
            ])->setDescription('Form will render automatically on the page.');

            $fields->insertAfter('Title',
                LiteralField::create('', '<div class="message warning"><strong>Note:</strong><br />Form must be configured from the <strong>Form Fields</strong> page tab and only applies to <strong>User Defined Form</strong> page types.</div>')
            );

        });

        return parent::getCMSFields();
    }

    public function Form()
    {
        if ($page = $this->getParentPage()) {
            if ($page->ClassName == UserDefinedForm::class) {
                $controller = Controller::curr();
                $form = UserForm::create($controller, 'Form_' . $page->ID);
                $form->setFormAction(Controller::join_links($page->Link(), 'Form'));
                $controller->generateConditionalJavascript();
                return $form;
            }
        }
    }


    
    public function getContentSummary()
    {
        if ($page = $this->getParentPage()) {
            return DBField::create_field('HTMLText', '<strong>Applied to page:</strong><br />' . $page->MenuTitle);
        }
    }

}