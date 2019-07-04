<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Sum;
use AppBundle\Entity\Transaction;
use AppBundle\Form\CustomerType;
use AppBundle\Form\TransactionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestController extends Controller
{
    /**
     * @Route("/customer", methods={"POST"})
     */
    public function addCustomerAction(Request $request)
    {
        /**
         * Request: name, cnp
         */
        parse_str($request->getQueryString(),$data_arr);

        $entityManager = $this->getDoctrine()->getManager();
        $customer = new Customer();

        $form = $this->createForm(CustomerType::class, $customer);
        $form->submit($data_arr);

        if (!empty($customer->getCnp()) && !empty($customer->getName())){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();
            return new JsonResponse(["customerId" => $customer->getCustomerId()],200);
        }
        return new Response("fail");
    }

    /**
     * @Route("/transaction/{customerId}/{transactionId}", methods={"GET"})
     */
    public function getTransactionAction($customerId,$transactionId)
    {
        $repository = $this->getDoctrine()->getRepository(Transaction::class);
        $transaction = $repository->findOneBy([
            "transactionId" => $transactionId,
            "customerId" => $customerId
        ]);
        if($transaction){
            return new JsonResponse([
                "transactionId" => $transaction->getTransactionId(),
                "amount" => $transaction->getAmount(),
                "date" => $transaction->getDate(),
            ]);
        }
        return new Response("fail");
    }

    /**
     * @Route("/transactionByFilter/{customerId}/{amount}/{date}/{offset}/{limit}")
     */
    public function getTransactionByFilterAction($customerId,$amount,$date,$offset,$limit)
    {
        $repository = $this->getDoctrine()->getRepository(Transaction::class);
        $transactions = $repository->findBy(
            [
                'customerId' => $customerId,
                'amount' => $amount
            ],
            ['transactionId' => 'ASC'],
            $limit,
            $offset
        );

        if ($transactions){
            $data = [];
            foreach ($transactions as $transaction){
                $data[] = [
                    'transactionId' => $transaction->getTransactionId(),
                    'customerId' => $transaction->getCustomerId(),
                    'amount' => $transaction->getAmount(),
                    'date' => $transaction->getDate(),
                ];
            }
            return new JsonResponse($data);
        }
        return new Response("fail");
    }

    /**
     * @Route("/transaction", methods={"POST"})
     */
    public function addTransactionAction(Request $request)
    {
        /**
         * Request: customerId, amount
         */
        $entityManager = $this->getDoctrine()->getManager();
        $data = $request->getQueryString();
        parse_str($data,$data_arr);
        $data_arr["date"] = date("d.m.Y",time());

        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->submit($data_arr);

        if (!empty($transaction->getCustomerId()) && !empty($transaction->getAmount())){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transaction);
            $entityManager->flush();
            return new JsonResponse([
                "transactionId" => $transaction->getTransactionId(),
                "customerId" => $transaction->getCustomerId(),
                "amount" => $transaction->getAmount(),
                "date" => $transaction->getDate()
            ],200);
        }
        return new Response("fail");
    }

    /**
     * @Route("/transaction", methods={"PUT"})
     */
    public function updateTransactionAction(Request $request)
    {
        /**
         * Request: transactionId, amount
         */
        $entityManager = $this->getDoctrine()->getManager();

        parse_str($request->getQueryString(),$data_arr);

        $transaction = $entityManager->getRepository(Transaction::class)->find($data_arr["transactionId"]);
        if (!$transaction) {
            return new Response("fail");
        }
        $transaction->setAmount($data_arr["amount"]);
        $entityManager->flush();

        return new JsonResponse([
            "transactionId" => $transaction->getTransactionId(),
            "customerId" => $transaction->getCustomerId(),
            "amount" => $transaction->getAmount(),
            "date" => $transaction->getDate()
        ],200);
    }

    /**
     * @Route("/transaction", methods={"DELETE"})
     */
    public function deleteTransactionAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        parse_str($request->getQueryString(),$data_arr);
        $transaction = $entityManager->getRepository(Transaction::class)->find($data_arr["transactionId"]);
        if (!$transaction) {
           return new Response("fail");
        }
        $entityManager->remove($transaction);
        $entityManager->flush();

        return new Response("success");
    }

    /**
     * @Route("/admin")
     */
    public function adminAction()
    {
        $repository = $this->getDoctrine()->getRepository(Transaction::class);
        $transactions = $repository->findAll();
        return $this->render('default/transaction.html.twig', [
            'transactions' => $transactions,
        ]);

    }
}
