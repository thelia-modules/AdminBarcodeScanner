<?php
/*************************************************************************************/
/*                                                                                   */
/*      Copyright (c) CQFDev                                                         */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*************************************************************************************/

namespace AdminBarcodeScanner\Controller;

use AdminBarcodeScanner\Model\ProductEan;
use AdminBarcodeScanner\Model\ProductEanQuery;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ProductQuery;
use Thelia\Model\ProductSaleElements;
use Thelia\Model\ProductSaleElementsQuery;
use Thelia\Tools\URL;
use AdminBarcodeScanner\AdminBarcodeScanner;

/**
 * @author Franck Allimant <franck@cqfdev.fr>
 */
class ProductController extends BaseAdminController
{
    public function setCode($productId, $codeEan)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'AdminBarcodeScanner', AccessManager::UPDATE)) {
            return $response;
        }

        $config = AdminBarcodeScanner::getConfigValue(AdminBarcodeScanner::MAIN_TABLE);

        if ($config == 'default')
            $pseList = ProductSaleElementsQuery::create()->findByProductId($productId);
        else
            $pseList = ProductEanQuery::create()->useProductSaleElementsQuery()
                ->filterByProductId($productId)
                ->endUse()
                ->find();

        foreach ($pseList as $pse) {
            $pse->setEanCode($codeEan)
                ->save();
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new Response('OK');
        } else {
            return $this->generateRedirect(
                URL::getInstance()->absoluteUrl('admin/products/update', ['product_id' => $productId, 'current_tab' => 'prices'])
            );
        }
    }
}
