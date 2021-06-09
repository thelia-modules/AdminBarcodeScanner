<?php
/*************************************************************************************/
/*                                                                                   */
/*      This file is not free software                                               */
/*                                                                                   */
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*************************************************************************************/

/**
 * Created by Franck Allimant, CQFDev <franck@cqfdev.fr>
 * Date: 02/05/2016 10:40
 */

namespace AdminBarcodeScanner\Controller;

use AdminBarcodeScanner\Model\ProductEan;
use AdminBarcodeScanner\Model\ProductEanQuery;
use ClassicRide\Model\ProductSaleElementsEan13Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Model\Base\ProductQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ProductSaleElementsQuery;
use Thelia\Model\ProductSaleElements;
use AdminBarcodeScanner\AdminBarcodeScanner;
use Thelia\Tools\URL;
use Thelia\Core\HttpFoundation\Response;

class SearchController extends BaseFrontController
{
    public function search()
    {
        $codeEan = $this->getRequest()->get('code');
        $config = AdminBarcodeScanner::getConfigValue(AdminBarcodeScanner::MAIN_TABLE);
        if ($config == 'default') {
            $pse = ProductSaleElementsQuery::create()->findOneByEanCode($codeEan);
            if ($pse) {
                return $this->generateRedirect(
                    URL::getInstance()->absoluteUrl(
                        'admin/products/update',
                        [
                            'product_id' => $pse->getProduct()->getId(),
                            'current_tab' => 'prices'
                        ]
                    )
                );
            }

            return new Response('', 404);
        }

        $pse = ProductSaleElementsEan13Query::create()->findOneByEan13($codeEan);

        if (null === $pse && $codeEan != null) {
            if ('0' === substr($codeEan, 0, 1) || str_contains($codeEan, '-')) {
                $barcode = $codeEan;
                $codeEan = str_replace("-", "", $codeEan);
                $pse = ProductSaleElementsEan13Query::create()->findOneByEan13(ltrim($codeEan, '0'));
                $product = ProductQuery::create()
                    ->filterByProductSaleElements($pse->getProductSaleElements())
                    ->findOne();

                $destinationEmail = ConfigQuery::read('notification_picking_wrong_barcode', 'classicride@orange.fr');
                $this->getMailer()->sendSimpleEmailMessage(
                    [ ConfigQuery::getStoreEmail() => 'Picking Module' ],
                    [ $destinationEmail => ConfigQuery::getStoreName() ],
                    'Erreur d\'EAN',
                    '',
                    'Une erreur d\'EAN a été détectée lors du scan du produit '.URL::getInstance()->absoluteUrl('/admin/products/update?product_id='.$product->getId()).', l\'ID du PSE est '.$pse->getProductSaleElements()->getId().'. L\'EAN lu sur l\'étiquette du produit est '.$barcode
                );
            } else {
                $pse = ProductSaleElementsEan13Query::create()->findOneByEan13('0' . $codeEan);
            }
        }

        if (null === $pse) {
            return new Response('', 404);
        }

        return new JsonResponse([
            'url' => URL::getInstance()->absoluteUrl(
                'admin/products/update',
                [
                    'product_id' => $pse->getProductSaleElements()->getProduct()->getId(),
                    'current_tab' => 'prices'
                ])
            ],
            200
        );

    }

    public function searchOrders($codeEan)
    {
        $pse = ProductSaleElementsEan13Query::create()->findOneByEan13($codeEan);

        if (null === $pse) {
            if ('0' === substr($codeEan, 0, 1)) {
                $pse = ProductSaleElementsEan13Query::create()->findOneByEan13(ltrim($codeEan, '0'));
            } else {
                $pse = ProductSaleElementsEan13Query::create()->findOneByEan13('0' . $codeEan);
            }
        }

        if (null === $pse) {
            return new Response('', 404);
        }

        return $this->generateRedirect(
            URL::getInstance()->absoluteUrl(
                'admin/products/update',
                [
                    'product_id' => $pse->getProductSaleElements()->getProduct()->getId(),
                    'current_tab' => 'modules'
                ]
            )
        );
    }
}
