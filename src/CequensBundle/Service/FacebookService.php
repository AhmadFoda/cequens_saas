<?php
/**
 * Created by PhpStorm.
 * User: karim
 * Date: 29/11/18
 * Time: 02:18 م
 */

namespace CequensBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Facebook\Facebook;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FacebookService
{
    protected $facebookLib;
    protected $entityManager;
    protected $validator;
    protected $container;
    protected $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ContainerInterface $container,
        LoggerInterface $logger
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->container = $container;
        $this->logger = $logger;
        $this->facebookLib = new Facebook([
            'app_id' => $this->container->getParameter('cequens_fb_app_id'),
            'app_secret' => $this->container->getParameter('cequens_fb_app_secret'),
        ]);

    }

    public function subscribePageToApp($pageId, $accessToken)
    {
        $return_array = ['success'=>false];
        try
        {
            $response = $this->facebookLib->post('/'.$pageId.'/subscribed_apps',[
                'subscribed_fields'=>['messages']
            ],$accessToken);
            $graphNode = $response->getGraphNode();
            $return_array['success'] = true;
            $return_array['data'] = $graphNode->asArray();
        }
        catch (\Exception $exception)
        {
            $return_array['msg'] = $exception->getMessage();
        }

        return $return_array;
    }




}