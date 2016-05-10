<?php
/**
 * Overwrite Ho_Import Mapper to add configurable attributes
 * currently doesn't work with storeview context
 *
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr_ConfigurableImport
 * @author      Benedikt Volkmer <bv@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2015 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_ConfigurableImport_Model_Mapper extends Ho_Import_Model_Mapper
{

    /**
     * Get the config for a specific field or the config for all the fields.
     * @param null $fieldName
     * @param null $profile
     *
     * @return mixed
     */
    public function getFieldConfig($fieldName = null, $profile = null)
    {
        if (is_null($profile)) {
            $profile = $this->getProfileName();
        }

        $fieldMapPath = sprintf(self::IMPORT_FIELD_CONFIG_PATH, $profile);

        if (! isset($this->_fieldConfig[$fieldMapPath])) {
            $fieldMapNode = Mage::getConfig()->getNode($fieldMapPath);

            // KK START
            foreach($this->getAttributes($this->getProfileName()) as $attribute) {
                $elem = new Mage_Core_Model_Config_Element('<' . $attribute .' field="' . $attribute .'"/>');
                $fieldMapNode->appendChild($elem);
            }
            // KK END

            if (! $fieldMapNode) {
                Mage::throwException(sprintf("Config path not found %s", $fieldMapPath));
            }

            if ($usePath = $fieldMapNode->getAttribute('use')) {
                $fieldMapPath = sprintf(self::IMPORT_FIELD_CONFIG_PATH, $usePath);
                $fieldMapNode = Mage::getConfig()->getNode($fieldMapPath);

                if (! $fieldMapNode) {
                    Mage::throwException(sprintf("Incorrect 'use' in <fieldmap use=\"%s\" />", $usePath));
                }
            }

            $columns = $fieldMapNode->children();
            $columnsData = array();

            $stores = array();
            foreach (Mage::app()->getStores() as $store) {
                /** @var $store Mage_Core_Model_Store */
                $stores[] = $store->getCode();
            }

            /** @var $column Mage_Core_Model_Config_Element */
            foreach ($columns as $key => $column) {

                foreach ($stores as $storeCode) {
                    if ($column->store_view->$storeCode) {
                        $columnsData[$storeCode][$key] = $column->store_view->$storeCode->asArray();
                    }
                }

                $columnsData['admin'][$key] = $columns->$key->asArray();
            }

            $this->_fieldConfig[$fieldMapPath] = $columnsData;
        }

        if (! is_null($fieldName)) {
            if (! isset($this->_fieldConfig[$fieldMapPath][$this->getStoreCode()][$fieldName])) {
                return null;
            }
            return $this->_fieldConfig[$fieldMapPath][$this->getStoreCode()][$fieldName];
        }

        return $this->_fieldConfig[$fieldMapPath];
    }

    protected function getAttributes($profile)
    {
        $config = unserialize(Mage::getStoreConfig('configurable_import/general/custom_attr'));

        if ($config) {
            foreach ($config as $set => $configArray) {
                if (!empty($configArray['profile']) && $configArray['profile'] == $profile) {
                    return $configArray['attribute'];
                }
            }
        }

        return array();
    }
}
