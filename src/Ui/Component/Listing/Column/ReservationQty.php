<?php
/**
 * LenovoBARBO | ReservationQtyAdmin
 *
 * @vendor  LenovoBARBO
 * @package ReservationQtyAdmin
 *
 * @copyright Copyright (c) 2025 LenovoBARBO
 *
 * @author Leandro Barboza dos Santos <barboza63sd@hotmail.com>
 **/

declare(strict_types=1);

namespace LenovoBARBO\ReservationQtyAdmin\src\Ui\Component\Listing\Column;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface;

/**
 * Add grid column with reservations quantity data
 */
class ReservationQty extends Column
{

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param GetReservationsQuantityInterface $getReservationsQuantity
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface                                  $context,
        UiComponentFactory                                $uiComponentFactory,
        private readonly GetProductTypesBySkusInterface   $getProductTypesBySkus,
        private readonly GetReservationsQuantityInterface $getReservationsQuantity,
        array                                             $components = [],
        array                                             $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        if ($dataSource['data']['totalRecords'] > 0) {
            foreach ($dataSource['data']['items'] as &$row) {
                $row['reservation_quantity'] = $this->getReservationQuantityItemData($row['sku']);
            }
        }
        unset($row);

        return $dataSource;
    }

    /**
     * Get reservations quantity data for product
     *
     * @param string $sku
     * @return array
     */
    private function getReservationQuantityItemData(string $sku): array
    {
        $sku = htmlspecialchars_decode($sku, ENT_QUOTES | ENT_SUBSTITUTE);

        $reservationData = [];

        $productType = $this->getProductTypesBySkus->execute([$sku]);

        if (!in_array('simple', $productType)) {
            return [];
        }

        $orderReservationsQty = (string) $this->getReservationsQuantity->execute($sku, 1);
        if (str_contains($orderReservationsQty, '-')) {
            $orderReservationsQty = str_replace('-', '', $orderReservationsQty);
        }

        $reservationData['order_reservations'] = (int) $orderReservationsQty;

        return $reservationData;
    }
}
