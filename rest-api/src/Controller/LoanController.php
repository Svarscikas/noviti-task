<?php
// src/Controller/LoanController.php
namespace App\Controller;

use App\Entity\Loan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class LoanController extends AbstractController
{

    #[Route('/loans', name: 'app_loans', methods: ['GET'])]
    public function getLoans(EntityManagerInterface $entityManager) : JsonResponse
    {
        $loans = $entityManager->getRepository(Loan::class)->findAll();

        if (!$loans) {
            throw $this->createNotFoundException(
                'No loans in the database'
            );
        }
        $loanData = [];
        foreach ($loans as $loan) {
            $loanData[] = [
                'id' => $loan->getId(),
                'amount' => $loan->getLoanAmount(),
                'interestRate' => $loan->getInterestRate(),
                'date' => $loan->getCreatedDate(),
                'numOfPayments' => $loan->getNumberOfPayments(),
                // Add other fields as necessary
            ];
        }
        return new JsonResponse($loanData);

    }
}
