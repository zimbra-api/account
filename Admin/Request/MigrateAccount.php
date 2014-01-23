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

use Zimbra\Admin\Struct\IdAndAction as Migrate;
use Zimbra\Soap\Request;

/**
 * MigrateAccount request class
 * Migrate an account.
 *
 * @package    Zimbra
 * @subpackage Admin
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class MigrateAccount extends Request
{
    /**
     * Constructor method for MigrateAccount
     * @param  Migrate $migrate Specification for the migration
     * @return self
     */
    public function __construct(Migrate $migrate)
    {
        parent::__construct();
        $this->child('migrate', $migrate);
    }

    /**
     * Gets or sets migrate
     *
     * @param  Migrate $migrate
     * @return Migrate|self
     */
    public function migrate(Migrate $migrate = null)
    {
        if(null === $migrate)
        {
            return $this->child('migrate');
        }
        return $this->child('migrate', $migrate);
    }
}