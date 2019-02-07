<?php

namespace CequensBundle\Command;

use CequensBundle\Service\CacheService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WorkflowTriggerExpiredCommand extends ContainerAwareCommand
{
    protected $entityManager;
    protected $cacheService;
    protected $logger;

    /**
     * WorkflowTriggerExpiredCommand constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, CacheService $cacheService, LoggerInterface $logger)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->cacheService = $cacheService;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('workflow:triggerExpired')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->entityManager->getRepository('CequensBundle:TriggeredKeys');
        $triggers = $repo->findBy(['triggerStatus'=>0]);
        foreach ($triggers as $trigger) {
            $triggerKey = $trigger->getTriggerKey();
            $this->cacheService->setPrefix('sms');
            $result = $this->cacheService->getDataArrayFromCache($triggerKey);
            $this->logger->debug('TriggeredKeyyyyFrom Cache',[$result]);

        }
        exit;
    }

}
