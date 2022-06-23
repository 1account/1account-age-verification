<?php

namespace OneAccount\OneAccountAgeVerification\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class AvValidation extends AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('User Age Is Not Validated'), 'value' => 'not_validate'],
                ['label' => __('User Age Is Valid'), 'value' => 'valid'],
                ['label' => __('User Age Is Not Valid'), 'value' => 'invalid'],
            ];
        }

        return $this->_options;
    }
}
