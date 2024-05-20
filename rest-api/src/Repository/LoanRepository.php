<?php
// src/Repository/LoanRepository.php
namespace App\Repository;

use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Loan>
 */
class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    /**
     * @return Loan[]
     */
    public function findAllLoansOrderedByDateDesc(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.createdDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
