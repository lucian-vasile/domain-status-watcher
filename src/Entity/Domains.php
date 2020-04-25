<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomainsRepository")
 */
class Domains
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $domain;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $current_status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $checked_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expires_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_owned;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $raw_whois_response = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getCurrentStatus(): ?string
    {
        return $this->current_status;
    }

    public function setCurrentStatus(?string $current_status): self
    {
        $this->current_status = $current_status;

        return $this;
    }

    public function getCheckedAt(): ?\DateTimeInterface
    {
        return $this->checked_at;
    }

    public function setCheckedAt(?\DateTimeInterface $checked_at): self
    {
        $this->checked_at = $checked_at;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expires_at;
    }

    public function setExpiresAt(?\DateTimeInterface $expires_at): self
    {
        $this->expires_at = $expires_at;

        return $this;
    }

    public function getIsOwned(): ?bool
    {
        return $this->is_owned;
    }

    public function setIsOwned(bool $is_owned): self
    {
        $this->is_owned = $is_owned;

        return $this;
    }

    public function getRawWhoisResponse(): ?array
    {
        return $this->raw_whois_response;
    }

    public function setRawWhoisResponse(?array $raw_whois_response): self
    {
        $this->raw_whois_response = $raw_whois_response;

        return $this;
    }
}
