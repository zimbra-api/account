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
use Zimbra\Enum\AutoProvTaskAction as Action;

/**
 * AutoProvTaskControl request class
 * Under normal situations, the EAGER auto provisioning task(thread) should be started/stopped automatically by the server when appropriate.
 * The task should be running when zimbraAutoProvPollingInterval is not 0 and zimbraAutoProvScheduledDomains is not empty.
 * The task should be stopped otherwise.
 * This API is to manually force start/stop or query status of the EAGER auto provisioning task.
 * It is only for diagnosis purpose and should not be used under normal situations.
 *
 * @package    Zimbra
 * @subpackage Admin
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class AutoProvTaskControl extends Request
{
    /**
     * Constructor method for AutoProvTaskControl
     * @param Action $action Action to perform - one of start|status|stop
     * @return self
     */
    public function __construct(Action $action)
    {
        parent::__construct();
        $this->property('action', $action);
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
