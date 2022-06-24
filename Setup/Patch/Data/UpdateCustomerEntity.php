<?php

namespace OneAccount\OneAccountAgeVerification\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use OneAccount\OneAccountAgeVerification\Model\Attribute\Source\AvValidation;

class UpdateCustomerEntity implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SalesSetupFactory        $salesSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $setup = $this->moduleDataSetup;
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
        $salesInstaller->addAttribute('order', 'order_av', [
            'type' => 'varchar',
            'label' => 'AV Validation',
            'input' => 'select',
            'required' => false,
            'visible' => true,
            'source' => AvValidation::class,
            'user_defined' => true,
            'sort_order' => 275,
            'position' => 400,
            'default' => '',
            'system' => false,
            'is_used_in_grid' => 1,
            'is_visible_in_grid' => 1,
            'is_filterable_in_grid' => 1,
            'is_searchable_in_grid' => 1,
        ]);

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
