<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Sum;
use AppBundle\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
class TransactionSumCommand extends ContainerAwareCommand
{
// the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:sum-transaction';


    protected function configure()
    {
        $this->setDescription('Stores the sum of all transactions from previous day.')->setHelp('This command allows you to stores the sum of all transactions from previous day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->sumTransaction());
    }

    private function sumTransaction()
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $repository = $doctrine->getRepository(Transaction::class);
        $query = $repository->createQueryBuilder('t')
            ->select("sum(t.amount) as amount")
            ->where('t.date = :date')
            ->setParameter('date', '' . date("d.m.Y",strtotime("-1 days")))->getQuery();
        $amount = $query->setMaxResults(1)->getOneOrNullResult();
        if ($amount){
            $entityManager = $doctrine->getManager();

            $sum = new Sum();
            $sum->setAmount($amount["amount"]);
            $sum->setDate(date("d.m.Y",strtotime("-1 days")));
            $entityManager->persist($sum);
            $entityManager->flush();
        }
        return "stored";
    }
}