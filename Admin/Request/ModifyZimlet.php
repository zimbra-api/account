<?php
/**
 * This file is part of the Zimbra API in PHP library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zimbra\Admin\Request;

use Zimbra\Soap\Request;
use Zimbra\Admin\Struct\ZimletAclStatusPri as Zimlet;

/**
 * ModifyZimlet request class
 * Modify Zimlet.
 *
 * @package    Zimbra
 * @subpackage Admin
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class ModifyZimlet extends Request
{
    /**
     * Constructor method for ModifyZimlet
     * @param Zimlet $zimlet Zimlet information
     * @return self
     */
    public function __construct(Zimlet $zimlet)
    {
        parent::__construct();
        $this->child('zimlet', $zimlet);
    }

    /**
     * Gets or sets Zimlet
     *
     * @param  Zimlet $zimlet
     * @return Zimlet|self
     */
    public function Zimlet(Zimlet $zimlet = null)
    {
        if(null === $zimlet)
        {
            return $this->child('zimlet');
        }
        return $this->child('zimlet', $zimlet);
    }
}
