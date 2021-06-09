<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace AdminBarcodeScanner;

use AdminBarcodeScanner\Model\ProductSaleElementsRealEanQuery;
use Thelia\Module\BaseModule;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;

class AdminBarcodeScanner extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'adminbarcodescanner';

    /** @var string */
    const MAIN_TABLE = 'adminbarcodescanner_main_table';

    /** @var array */
    const POSSIBLE_CONFIGURATIONS = array(
      'default',
      'custom'
    );
}
