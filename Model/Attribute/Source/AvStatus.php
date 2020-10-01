<?php

namespace OneAccount\OneAccountAgeVerification\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class AvStatus extends AbstractSource
{
    /**
     * Get all options
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Select Status'), 'value' => ''],
                ['label' => __('AV Success'), 'value' => 'success'],
                ['label' => __('AV Failed'), 'value' => 'failed']
            ];
        }

        return $this->_options;
    }
}
