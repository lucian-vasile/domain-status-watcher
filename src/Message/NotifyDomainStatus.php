<?php

namespace App\Message;

/**
 * Class NotifyDomainStatus
 *
 * @package App\Message
 */
final class NotifyDomainStatus
{
    
    /**
     * @var string $message
     */
    private $message;
    
    /**
     * NotifyDomainStatus constructor.
     *
     * @param string $message
     */
    public function __construct (string $message)
    {
        $this->message = $message;
    }
    
    
    /**
     * @return string
     */
    public function getMessage (): string
    {
        return $this->message;
    }
    
    
}
