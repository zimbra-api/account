<?php
/**
 * This file is part of the Zimbra API in PHP library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zimbra\Account\Request;

use Zimbra\Account\Struct\Right as ACE;
use Zimbra\Common\TypedSequence;

/**
 * GetRights request class
 * Get account level rights.
 *
 * @package    Zimbra
 * @subpackage Account
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class GetRights extends Base
{
    /**
     * Specify Access Control Entries
     * @var TypedSequence<Right>
     */
    private $_aces;

    /**
     * Constructor method for GetRights
     * @param array $ace Specify Access Control Entries
     * @return self
     */
    public function __construct(array $aces = [])
    {
        parent::__construct();
        $this->setAces($aces);
    
        $this->on('before', function(Base $sender)
        {
            if($sender->getAces()->count())
            {
                $sender->setChild('ace', $sender->getAces()->all());
            }
        });
    }

    /**
     * Add an ace
     *
     * @param  ACE $ace
     * @return self
     */
    public function addAce(ACE $ace)
    {
        $this->_aces->add($ace);
        return $this;
    }

    /**
     * Sets ace sequence
     *
     * @param  array $aces
     * @return self
     */
    public function setAces(array $aces)
    {
        $this->_aces = new TypedSequence('Zimbra\Account\Struct\Right', $aces);
        return $this;
    }

    /**
     * Gets ace sequence
     *
     * @return Sequence
     */
    public function getAces()
    {
        return $this->_aces;
    }
}
