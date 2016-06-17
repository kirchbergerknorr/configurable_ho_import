<?php
/**
 * Custom Attribute config resource - add resource
 *
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr_ImportSeo
 * @author      Benedikt Volkmer <bv@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Kirchbergerknorr_ConfigurableImport_Block_Resource_CustomAttributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoAttributes;

    public function __construct()
    {
        $this->addColumn('profile', array(
            'label' => Mage::helper('adminhtml')->__('Import Profile'),
            'size'  => 28,
        ));
        $this->addColumn('attribute', array(
            'label' => Mage::helper('adminhtml')->__('Custom Attributes'),
            'size'  => 28
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add attribute to profile');

        parent::__construct();

        $this->setTemplate('customattributes/array.phtml');

    }

    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($columnName == 'attribute') {
            $attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setOrder('attribute_code', 'ASC')
                ->load();

            $attributes = $attributeCollection->getItems();

            $rendered = '<select name="'.$inputName.'[]" id="' . $columnName . '#{_id}" size="10" multiple="multiple">';
            foreach($attributes as $attribute) {
                $rendered .= '<option value="' . $attribute->getAttributeCode() . '">' . $attribute->getName() .'</option>';
            }
        } else {
            /** @var Ho_Import_Model_Import $import */
            $import   = Mage::getModel('ho_import/import');

            $rendered = '<select name="'.$inputName.'" id="' . $columnName . '#{_id}">';
            foreach($import->getProfiles() as $name => $profile) {
                $rendered .= '<option value="' . $name . '">' . $name .'</option>';
            }
        }
        $rendered .= '</select>';

        return $rendered;
    }

}