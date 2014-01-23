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

use Zimbra\Admin\Struct\LimitedQuery as Query;
use Zimbra\Enum\GalConfigAction as Action;
use Zimbra\Soap\Request\Attr;

/**
 * CheckGalConfig request class
 * Check Global Addressbook Configuration.
 * Notes: 
 *    zimbraGalMode must be set to ldap, even if you eventually want to set it to "both". 
 *    <action> is optional. GAL-action can be autocomplete|search|sync. Default is search.
 *    <query> is ignored if <action> is "sync".
 *    AuthMech can be none|simple|kerberos5.
 *
 * @package    Zimbra
 * @subpackage Admin
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class CheckGalConfig extends Attr
{
    /**
     * Constructor method for CheckGalConfig
     *
     * @param Query  $query The query
     * @param Action $action The action. Can be autocomplete|search|sync. Default is search
     * @param array  $attrs The attributes
     * @return self
     */
    public function __construct(
        Query $query = null,
        Action $action = null,
        array $attrs = array()
    )
    {
        parent::__construct($attrs);
        if($query instanceof Query)
        {
            $this->child('query', $query);
        }
        if($action instanceof Action)
        {
            $this->child('action', $action);
        }
    }

    /**
     * Gets or sets query
     *
     * @param  Query $query
     * @return Query|self
     */
    public function query(Query $query = null)
    {
        if(null === $query)
        {
            return $this->child('query');
        }
        return $this->child('query', $query);
    }

    /**
     * Gets or sets action
     *
     * @param  Action $action
     * @return Action|self
     */
    public function action(Action $action = null)
    {
        if(null === $action)
        {
            return $this->child('action');
        }
        return $this->child('action', $action);
    }
}
