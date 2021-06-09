<?php

namespace AdminBarcodeScanner\Controller;

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
 * @author Enzo CARNEZ <ecarnez@openstudio.fr>
 */
class ConfigurationController extends BaseAdminController
{
    public function setDefaultTable($option)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'AdminBarcodeScanner', AccessManager::UPDATE)) {
            return $response;
        }

        if (in_array($option, AdminBarcodeScanner::POSSIBLE_CONFIGURATIONS))
          AdminBarcodeScanner::setConfigValue(AdminBarcodeScanner::MAIN_TABLE, $option);
        else
          return new Response('', 404);

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new Response('OK');
        } else {
            return $this->generateRedirect(
                URL::getInstance()->absoluteUrl('admin/module/AdminBarcodeScanner')
            );
        }
    }
}
