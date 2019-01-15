<?php
/**
 * File src/SapRfcConfigB.php
 *
 * Type B configuration.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\classes\AbstractConfigB;

/**
 * Class phpsap\saprfc\SapRfcConfigB
 *
 * Configure connection parameters for SAP remote function calls using load
 * balancing (type B).
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigB extends AbstractConfigB
{
    /**
     * @var array list all connection parameters available
     */
    protected static $conParamAvail = [
        'CLIENT'    => true,
        'USER'      => true,
        'PASSWD'    => true,
        'MSHOST'    => true,
        'R3NAME'    => true,
        'GROUP'     => true,
        'LANG'      => false,
        'TRACE'     => false
    ];

    use SapRfcConfigTrait;
}
