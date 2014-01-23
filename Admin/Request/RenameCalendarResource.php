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

/**
 * RenameCalendarResource request class
 * Rename Calendar Resource.
 *
 * @package    Zimbra
 * @subpackage Admin
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class RenameCalendarResource extends Request
{
    /**
     * Zimbra ID
     * @var string
     */
    private $_id;

    /**
     * New Calendar Resource name
     * @var string
     */
    private $_newName;

    /**
     * Constructor method for RenameCalendarResource
     * @param string $id
     * @param string $newName
     * @return self
     */
    public function __construct($id, $newName)
    {
        parent::__construct();
        $this->property('id', trim($id));
        $this->property('newName', trim($newName));
    }

    /**
     * Gets or sets id
     *
     * @param  string $id
     * @return string|self
     */
    public function id($id = null)
    {
        if(null === $id)
        {
            return $this->property('id');
        }
        return $this->property('id', trim($id));
    }

    /**
     * Gets or sets newName
     *
     * @param  string $newName
     * @return string|self
     */
    public function newName($newName = null)
    {
        if(null === $newName)
        {
            return $this->property('newName');
        }
        return $this->property('newName', trim($newName));
    }
}
