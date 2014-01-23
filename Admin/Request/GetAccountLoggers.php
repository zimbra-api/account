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
use Zimbra\Struct\AccountSelector as Account;

/**
 * GetAccountLoggers request class
 * Returns custom loggers created for the given account since the last server start.
 * If the request is sent to a server other than the one that the account resides on, it is proxied to the correct server.
 *
 * @package    Zimbra
 * @subpackage Admin
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class GetAccountLoggers extends Request
{
    /**
     * Constructor method for GetAccountLoggers
     * @param  Account $account Use to select account
     * @return self
     */
    public function __construct(Account $account = null)
    {
        parent::__construct();
        if($account instanceof Account)
        {
            $this->child('account', $account);
        }
    }

    /**
     * Gets or sets account
     *
     * @param  Account $account
     * @return Account|self
     */
    public function account(Account $account = null)
    {
        if(null === $account)
        {
            return $this->child('account');
        }
        return $this->child('account', $account);
    }
}
