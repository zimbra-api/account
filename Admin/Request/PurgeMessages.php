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
use Zimbra\Admin\Struct\MailboxByAccountIdSelector as Mailbox;

/**
 * PurgeMessages request class
 * Purges aged messages out of trash, spam, and entire mailbox.
 *
 * @package    Zimbra
 * @subpackage Admin
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class PurgeMessages extends Request
{
    /**
     * Constructor method for PurgeMessages
     * @param Mailbox $mbox Mailbox information
     * @return self
     */
    public function __construct(Mailbox $mbox = null)
    {
        parent::__construct();
        if($mbox instanceof Mailbox)
        {
            $this->child('mbox', $mbox);
        }
    }

    /**
     * Gets or sets Mailbox
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
}
