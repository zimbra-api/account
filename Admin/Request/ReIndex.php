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

use Zimbra\Admin\Struct\ReindexMailboxInfo as Mailbox;
use Zimbra\Enum\ReIndexAction as Action;
use Zimbra\Soap\Request;

/**
 * ReIndex request class
 * ReIndex.
 *
 * @package    Zimbra
 * @subpackage Admin
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class ReIndex extends Request
{
    /**
     * Constructor method for ReIndex
     * @param Mailbox $mbox Specify reindexing to perform
     * @param Action $action Action to perform
     * @return self
     */
    public function __construct(Mailbox $mbox, Action $action = null)
    {
        parent::__construct();
        $this->child('mbox', $mbox);
        if($action instanceof Action)
        {
            $this->property('action', $action);
        }
    }

    /**
     * Gets or sets mbox
     *
     * @param  Mailbox $mbox
     * @return Mailbox|self
     */
    public function mbox(Mailbox $mbox = null)
    {
        if(null === $mbox)
        {
            return $this->child('mbox');
        }
        return $this->child('mbox', $mbox);
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
            return $this->property('action');
        }
        return $this->property('action', $action);
    }
}
