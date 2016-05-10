<?php
/**
 * Rewrite ImportExport CSV write Adapter, because Magento > 1.9 works different with values starting
 * with mathematical operators
 *
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr_ConfigurableImport
 * @author      Benedikt Volkmer <bv@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Kirchbergerknorr_ConfigurableImport_Model_Export_Adapter_Csv extends Mage_ImportExport_Model_Export_Adapter_Csv
{
    /**
     * Write row data to source file. If version greater than 1.9, use overwritten
     * behaviour for mathematical operators
     *
     * @param array $rowData
     * @throws Exception
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    public function writeRow(array $rowData)
    {
        if (version_compare(Mage::getVersion(), '1.9', '>=')){
            //version is 1.9 or greater
            if (null === $this->_headerCols) {
                $this->setHeaderCols(array_keys($rowData));
            }

            /**
             * Security enchancement for CSV data processing by Excel-like applications.
             * @see https://bugzilla.mozilla.org/show_bug.cgi?id=1054702
             */
            $data = array_merge($this->_headerCols, array_intersect_key($rowData, $this->_headerCols));

            /* Reset behaviour to magento version 1.8*/
            foreach ($data as $key => $value) {
                if (substr($value, 0, 1) === '=') {
                    $data[$key] = ' ' . $value;
                }
            }

            fputcsv(
                $this->_fileHandler,
                $data,
                $this->_delimiter,
                $this->_enclosure
            );

            return $this;
        }

        return parent::writeRow($rowData);
    }
}