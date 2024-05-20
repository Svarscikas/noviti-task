<?php
// src/Entity/Loan.php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Loan
{

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $loanAmount;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $interestRate;

    /**
     * @ORM\Column(type="integer")
     */
    private $numberOfPayments;

    // Existing methods...

    public function getCreatedDate(): ?DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLoanAmount(): ?float
    {
        return $this->loanAmount;
    }

    public function setLoanAmount(float $loanAmount): self
    {
        $this->loanAmount = $loanAmount;

        return $this;
    }

    public function getInterestRate(): ?float
    {
        return $this->interestRate;
    }

    public function setInterestRate(float $interestRate): self
    {
        $this->interestRate = $interestRate;

        return $this;
    }

    public function getNumberOfPayments(): ?int
    {
        return $this->numberOfPayments;
    }

    public function setNumberOfPayments(int $numberOfPayments): self
    {
        $this->numberOfPayments = $numberOfPayments;

        return $this;
    }
}