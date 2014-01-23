<?php
/**
 * This file is part of the Zimbra API in PHP library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zimbra\Account\Struct;

use Zimbra\Struct\Base;

/**
 * Right struct class
 *
 * @package    Zimbra
 * @subpackage Account
 * @category   Struct
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class Right extends Base
{
    /**
     * Constructor method for Right
     * @param string $right
     * @return self
     */
    public function __construct($right)
    {
		parent::__construct();
        $this->property('right', trim($right));
    }

    /**
     * Gets or sets right
     *
     * @param  string $right
     * @return string|self
     */
    public function right($right = null)
    {
        if(null === $right)
        {
            return $this->property('right');
        }
        return $this->property('right', trim($right));
    }

    /**
     * Returns the array representation of this class 
     *
     * @return array
     */
    public function toArray($name = 'ace')
    {
        return parent::toArray($name);
    }

    /**
     * Method returning the xml representation of this class
     *
     * @return SimpleXML
     */
    public function toXml($name = 'ace')
    {
        return parent::toXml($name);
    }
}
