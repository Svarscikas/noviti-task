<?php

namespace App\Controller;

use App\Entity\Loan;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class NovitiController extends AbstractController
{
    #[Route('/noviti', name: 'app_noviti')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/NovitiController.php',
        ]);
    }

    #[Route('/loan', name: 'app_loan', methods:['POST'])]
    public function loan(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Get the JSON data from the request body
        $jsonData = $request->getContent();

        // Decode the JSON data into an associative array
        $data = json_decode($jsonData, true);

        // Get the request parameters from the decoded JSON data
        $loanAmount = $data['loan_amount'] ?? null;
        $interest = $data['interest'] ?? null;
        $numberOfPayments = $data['num_of_payments'] ?? null;
        $futureValue = 0;

        // Validate loan amount, interest rate, and number of payments
        if (!is_numeric($loanAmount) || $loanAmount < 5000 || $loanAmount > 50000) {
            return new JsonResponse('Loan amount must be a number between 5000 and 50000', JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!is_numeric($interest) || $interest < 1 || $interest > 100) {
            return new JsonResponse('Interest rate must be a number between 1 and 100', JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!is_int($numberOfPayments) || $numberOfPayments <= 0) {
            return new JsonResponse('Number of payments must be a positive whole number', JsonResponse::HTTP_BAD_REQUEST);
        }
        // Create a new Loan object
        $loan = new Loan();
        $loan->setLoanAmount($loanAmount);
        $loan->setInterestRate($interest);
        $loan->setNumberOfPayments($numberOfPayments);
        $loan->setCreatedDate(new DateTime());

        // Persist the Loan object to the database
        $entityManager->persist($loan);
        $entityManager->flush();

        // Convert interest rate to monthly rate
        $interestRate = ($interest / 100)/12;

        // Calculate present value interest factor
        $presentValueInterestFactor = pow((1 + $interestRate), $numberOfPayments);

        // Calculate payment amount
        $payment = $interestRate * $loanAmount * ($presentValueInterestFactor + $futureValue) / ($presentValueInterestFactor - 1);

        // Round the payment amount to 2 decimal places
        $roundedPayment = round($payment, 2);
        $responseData = ['pmt' => $roundedPayment];
        $monthlyPayments = [];
        function monthlyPayment($amount, $pmt, $interestRate, &$monthlyPayments): void
        {
            $principalPart = null;
            $interestAmount = ($amount / 12) * $interestRate;
            if($amount < $pmt) {
                $principalPart = $amount;
            }
            else {
                $principalPart = $pmt - $interestAmount;
            }
            $remainingAmount = $amount - $principalPart;
            $totalAmount = $interestAmount + $principalPart;

            //Round numbers
            $remainingAmount = round($remainingAmount, 2);
            $principalPart = round($principalPart, 2);
            $interestAmount = round($interestAmount, 2);
            $totalAmount = round($totalAmount, 2);

            $monthlyPayments[] = [
                'remainingAmount' => $remainingAmount,
                'principalPart' => $principalPart,
                'interestAmount' => $interestAmount,
                'totalAmount' => $totalAmount
            ];
        }
        $currentAmount = $loanAmount;
        for ($i = 0; $i < $numberOfPayments; $i++) {
            monthlyPayment($currentAmount,$roundedPayment,$interest/100,$monthlyPayments);
            $currentAmount = $monthlyPayments[$i]['remainingAmount'];
        }
        return new JsonResponse($monthlyPayments);
    }
}
