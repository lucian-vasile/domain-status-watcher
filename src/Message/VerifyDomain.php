<?php

namespace App\Message;

final class VerifyDomain
{
    
    private $domain_id;
    
    public function __construct (int $domain_id)
    {
        $this->domain_id = $domain_id;
    }
    
    /**
     * @return int
     */
    public function getDomainId (): int
    {
        return $this->domain_id;
    }
    
}