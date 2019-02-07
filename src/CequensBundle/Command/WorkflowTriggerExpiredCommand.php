<?php

namespace CequensBundle\Command;

use CequensBundle\Service\CacheService;
use CequensBundle\Service\RestcommService;
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
    protected $restcommService;

    /**
     * WorkflowTriggerExpiredCommand constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, CacheService $cacheService, LoggerInterface $logger, RestcommService $restcommService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->cacheService = $cacheService;
        $this->logger = $logger;
        $this->restcommService = $restcommService;
    }

    protected function configure()
    {
        $this
            ->setName('workflow:triggerExpired')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->entityManager->getRepository('CequensBundle:TriggeredKeys');
        $triggers = $repo->findBy(['triggerStatus' => 0]);
        foreach ($triggers as $trigger) {
            $triggerKey = $trigger->getTriggerKey();
            $this->cacheService->setPrefix('sms');
            $result = $this->cacheService->getDataArrayFromCache($triggerKey);
            if (!empty($result) && count($result) > 0) {
                $expireAfter = $result['expireAfter'];
                $dteStart = new \DateTime($expireAfter);
                $dteEnd = new \DateTime();
                $isPassed = ($dteStart < $dteEnd) ? true : false;
                $this->logger->debug('Trigger isPassed => ', [$isPassed]);
                if ($isPassed) {
                    $fallBackFlow = $result['expireFallback'];
                    $from = $result['from'];
                    $to = $result['to'];
                    $result = $this->restcommService->triggerCall(
                        $to,
                        $from,
                        $fallBackFlow
                    );
                    $this->logger->debug('CallingRestcommService', [$result]);
                    if (array_key_exists('sid', $result)) {
                        $this->cacheService->addCapturedDigitsToCache($result['sid']);
                        $trigger->setTriggerStatus(1);
                        $this->entityManager->flush();
                    }
                }
            } else {
                $trigger->setTriggerStatus(1);
                $this->entityManager->flush();
            }
        }
    }

}
