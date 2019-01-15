<?php
/**
 * File src/SapRfcConfigA.php
 *
 * Type A configuration.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\classes\AbstractConfigA;

/**
 * Class phpsap\saprfc\SapRfcConfigA
 *
 * Configure connection parameters for SAP remote function calls using a specific
 * SAP application server (type A).
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigA extends AbstractConfigA
{
    /**
     * @var array list all connection parameters available
     */
    protected static $conParamAvail = [
        'ASHOST'    => true,
        'SYSNR'     => true,
        'CLIENT'    => true,
        'USER'      => true,
        'PASSWD'    => true,
        'GWHOST'    => false,
        'GWSERV'    => false,
        'LANG'      => false,
        'TRACE'     => false
    ];

    /**
     * Common code for connection configuration. Implements methods of
     * phpsap\classes\AbstractConfigContainer.
     */
    use SapRfcConfigTrait;
}
