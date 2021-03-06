<?php
/**
 * Created by PhpStorm.
 * User: karim
 * Date: 04/02/19
 * Time: 08:15 م
 */

namespace CequensBundle\Controller\Api;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Unirest\Request\Body;

class MsisdnController extends ApiBaseController
{
    protected $logger;

    /**
     * MsisdnController constructor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @Route("/{msisdn}",name="msisdn_api_inquire")
     * @Method({"GET"})
     */
    public function inquireAction(Request $request, $msisdn)
    {
        $baseUrl = 'https://cequensnew3ck:A1511Y3u@rest.tyntec.com/nis/v1/gnv?msisdn='.$msisdn;
        $response = \Unirest\Request::get($baseUrl,[],[],'cequensnew3ck','A1511Y3u');
        $this->logger->debug('Response Received From Tyntec API => ',[$response->body]);
        $arrayResponse = json_decode(json_encode($response->body), true);
        if(is_array($arrayResponse) && array_key_exists('errorCode',$arrayResponse) && $arrayResponse['errorCode']==0)
        {
            $array = $arrayResponse;
            $remaining = array_splice($array,19);
            $remaining2 = array_splice($remaining,16);
            $array = array_merge($array,$remaining2);

        }
        else
        {
            $array['errorCode'] = $response->body;
        }
        return new JsonResponse($array,200);
    }

}