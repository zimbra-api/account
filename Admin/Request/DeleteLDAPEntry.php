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
 * DeleteLDAPEntry request class
 * Delete an LDAP entry.
 *
 * @package    Zimbra
 * @subpackage Admin
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class DeleteLDAPEntry extends Request
{
    /**
     * Constructor method for DeleteLDAPEntry
     * @param  string $dn A valdn LDAP DN String (RFC 2253) that describes the DN to delete
     * @return self
     */
    public function __construct($dn)
    {
        parent::__construct();
        $this->property('dn', trim($dn));
    }

    /**
     * Gets or sets dn
     *
     * @param  string $dn
     * @return string|self
     */
    public function dn($dn = null)
    {
        if(null === $dn)
        {
            return $this->property('dn');
        }
        return $this->property('dn', trim($dn));
    }
}
