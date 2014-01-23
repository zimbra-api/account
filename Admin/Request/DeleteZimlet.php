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
use Zimbra\Struct\NamedElement as Zimlet;

/**
 * DeleteZimlet request class
 * Delete a Zimlet.
 *
 * @package    Zimbra
 * @subpackage Admin
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class DeleteZimlet extends Request
{
    /**
     * Search task information
     * @var Zimlet
     */
    private $_zimlet;

    /**
     * Constructor method for DeleteZimlet
     * @param Zimlet $zimlet
     * @return self
     */
    public function __construct(Zimlet $zimlet)
    {
        parent::__construct();
        $this->child('zimlet', $zimlet);
    }

    /**
     * Gets or sets zimlet
     *
     * @param  Zimlet $zimlet
     * @return Zimlet|self
     */
    public function zimlet(Zimlet $zimlet = null)
    {
        if(null === $zimlet)
        {
            return $this->child('zimlet');
        }
        return $this->child('zimlet', $zimlet);
    }
}
