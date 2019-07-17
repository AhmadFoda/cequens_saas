<?php

namespace CequensBundle\Controller;

use CequensBundle\Service\BotService;
use JMS\Serializer\Handler\StdClassHandler;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Chatkit\Chatkit;
use Symfony\Component\Workflow\StateMachine;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 *
 */
class InboxController extends Controller
{
    protected $botService;
    protected $logger;

    /**
     * AudioLibraryController constructor.
     */

    public function __construct(BotService $botService, LoggerInterface $logger)
    {
        $this->botService = $botService;
        $this->logger = $logger;
    }

    /**
     * @Route("/conversations",name="conversations_list_all")
     */
    public function indexAction()
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $bots = $this->getDoctrine()->getRepository('CequensBundle:Bot')->findBy(['userId' => $userId]);
        return $this->render('@Cequens/Admin/pages/Inbox/conversations.html.twig', array('bots' => $bots));
    }

    /**
     * @Route("/users",name="users_list_all")
     */
    public function usersAction()
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $bots = $this->getDoctrine()->getRepository('CequensBundle:Bot')->findBy(['userId' => $userId]);
        $users = $this->getDoctrine()->getRepository('CequensBundle:BotUser')->findAll();
        return $this->render('@Cequens/Admin/pages/Inbox/users.html.twig', array('bots' => $bots, 'users'=>$users));
    }

    /**
     * @Route("/listAll", name="getInboxConversations")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getInboxConversationsAction()
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $conversations =
            $this->getDoctrine()->getRepository('CequensBundle:ConversationMessage')->findUserMessagesOrderedByDate();
        $list = array();
        $i = 0;
        foreach ($conversations as $conversation) {
            $stdClass = new \stdClass();
            $stdClass->id = $i;
            $stdClass->subject = "";
            $stdClass->to = [$conversation['senderId']];
            $stdClass->body = "";
            $stdClass->time = "5 Mins ago";
            $stdClass->datetime = "Today at 1:33pm";
            $stdClass->from = "201020737333";
            $stdClass->dp = $this->getParameter('assets_url') . "/img/profiles/avatar.jpg";
            if ($conversation['senderType'] == 'FB-Bot') {
                $stdClass->dp = $this->getParameter('assets_url') . "/img/profiles/bot.png";
                $stdClass->dpRetina = "/img/profiles/bot.png";
            } elseif ($conversation['senderType'] == 'FB-User') {
                $stdClass->dp = $this->getParameter('assets_url') . "/img/profiles/fb.png";
                $stdClass->dpRetina = "/img/profiles/fb.png";
            } elseif ($conversation['senderType'] == 'WhatsApp-User') {
                $stdClass->dp = $this->getParameter('assets_url') . "/img/profiles/whatsapp.png";
                $stdClass->dpRetina = "/img/profiles/whatsapp.png";
            }

            $stdClass->messages = [];
            $conversationsPerUser =
                $this->getDoctrine()->getRepository('CequensBundle:ConversationMessage')->findByUserIdOrderedByDate(
                    $conversation['senderId']
                );
            $lastMessageDateTime = '';
            foreach ($conversationsPerUser as $conversationPerUser) {
                $stdClassMessage = new \stdClass();
                $stdClassMessage->id = $conversationPerUser->getId();
                $stdClassMessage->subject = "";
                $stdClassMessage->to = [$conversationPerUser->getRecId()];
                $stdClassMessage->body = $conversationPerUser->getMessage();
                $stdClassMessage->time = "5 Mins ago";
                $stdClassMessage->datetime = $conversationPerUser->getSendDate()->format('Y-m-d H:i:s');
                $lastMessageDateTime = $conversationPerUser->getSendDate()->format('Y-m-d H:i:s');
                $stdClassMessage->from = $conversationPerUser->getSenderId();
                $stdClassMessage->dp = $this->getParameter('assets_url') . "/img/profiles/avatar.jpg";
                if ($conversationPerUser->getSenderType() == 'FB-Bot') {
                    $stdClassMessage->dp = $this->getParameter('assets_url') . "/img/profiles/bot.png";
                } elseif ($conversationPerUser->getSenderType() == 'FB-User') {
                    $stdClassMessage->dp = $this->getParameter('assets_url') . "/img/profiles/fb.png";
                } elseif ($conversationPerUser->getSenderType() == 'WhatsApp-User') {
                    $stdClassMessage->dp = $this->getParameter('assets_url') . "/img/profiles/whatsapp.png";
                }
                $stdClassMessage->dpRetina = "img/profiles/fb.png";
                $sentiment = ($conversationPerUser->getNlp() != 'empty') ? json_decode(
                    $conversationPerUser->getNlp(),
                    true
                ) : [];
                $sentiment_text = '';
                if (array_key_exists('entities', $sentiment)) {
                    if (array_key_exists('sentiment', $sentiment['entities'])) {
                        $sentiment_text = $sentiment['entities']['sentiment'][0]['value'];
                    }
                }
                $stdClassMessage->sentiment = $sentiment_text;
                $stdClass->messages[] = $stdClassMessage;
            }

            //TimeElapse
            $full = false;
            $now = new \DateTime();
            $ago = new \DateTime($lastMessageDateTime);
            $diff = $now->diff($ago);

            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;

            $string = array(
                'y' => 'year',
                'm' => 'month',
                'w' => 'week',
                'd' => 'day',
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            );
            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            }

            if (!$full) {
                $string = array_slice($string, 0, 1);
            }
            $timeAgo = $string ? implode(', ', $string) . ' ago' : 'just now';
            $stdClass->time = $timeAgo;

            $list[$conversation['senderId']] = $stdClass;
            $i++;
        }
        //echo $conversations[0]->getMessage();exit;

        /*$stdClass = new \stdClass();
        $stdClass->id =0;
        $stdClass->subject="";
        $stdClass->to= ["201020737333"];
        $stdClass->body= "Hello, Please i need to know more about Cequens offers ";
        $stdClass->time= "5 Mins ago";
        $stdClass->datetime= "Today at 1:33pm";
        $stdClass->from= "201020737333";
        $stdClass->dp= "img/profiles/whatsapp.png";
        $stdClass->dpRetina= "img/profiles/whatsapp.png";
        $stdClass->messages = [];
        $stdClassMessage = new \stdClass();
        $stdClassMessage->id =0;
        $stdClassMessage->subject="";
        $stdClassMessage->to= ["201020737333"];
        $stdClassMessage->body= "Hello, Please i need to know more about Cequens offers ";
        $stdClassMessage->time= "5 Mins ago";
        $stdClassMessage->datetime= "Today at 1:33pm";
        $stdClassMessage->from= "201020737333";
        $stdClassMessage->dp= "img/profiles/whatsapp.png";
        $stdClassMessage->dpRetina= "img/profiles/whatsapp.png";
        $stdClass->messages[] = $stdClassMessage;
        $stdClassMessage = new \stdClass();
        $stdClassMessage->id =0;
        $stdClassMessage->subject="";
        $stdClassMessage->to= ["201020737333"];
        $stdClassMessage->body= "Hello, Please i need to know more about Cequens offers ";
        $stdClassMessage->time= "5 Mins ago";
        $stdClassMessage->datetime= "Today at 1:33pm";
        $stdClassMessage->from= "201020737333";
        $stdClassMessage->dp= "img/profiles/whatsapp.png";
        $stdClassMessage->dpRetina= "img/profiles/whatsapp.png";
        $stdClass->messages[] = $stdClassMessage;
        $list['201020737333'] = $stdClass;

        $stdClass = new \stdClass();
        $stdClass->id =1;
        $stdClass->subject="";
        $stdClass->to= ["201020737334"];
        $stdClass->body= "Hello, Please i need to know more about Cequens offers";
        $stdClass->time= "5 Mins ago";
        $stdClass->datetime= "Today at 1:33pm";
        $stdClass->from= "201020737334";
        $stdClass->dp= "img/profiles/rcs.ico";
        $stdClass->dpRetina= "img/profiles/rcs.ico";
        $stdClassMessage = new \stdClass();
        $stdClassMessage->id =0;
        $stdClassMessage->subject="";
        $stdClassMessage->to= ["201020737333"];
        $stdClassMessage->body= "Hello, Please i need to know more about Cequens offers ";
        $stdClassMessage->time= "5 Mins ago";
        $stdClassMessage->datetime= "Today at 1:33pm";
        $stdClassMessage->from= "201020737333";
        $stdClassMessage->dp= "img/profiles/whatsapp.png";
        $stdClassMessage->dpRetina= "img/profiles/whatsapp.png";
        $stdClass->messages = array($stdClassMessage);
        $list['201020737334'] = $stdClass;*/
        $date = new \DateTime('now');
        $conversations = [
            'emails' => [
                [
                    'group' => 'Today April 23',//$date->format('Y-m-d H:i:s'),
                    'list' => $list,
                ],
            ],
        ];

        return new JsonResponse($conversations);
    }

    /**
     * @Route("/sendMessage", name="sendMessage")
     * @Method("POST")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendMessageAction(Request $request)
    {
        $room_id = $request->request->get('roome_id');
        $message = $request->request->get('message');
        $room_name = $request->request->get('room_name');
        $this->logger->debug('[InboxController] $room_id '.$room_id);
        $this->logger->debug('[InboxController] $message '.$message);
        $this->logger->debug('[InboxController] $message '.$room_name);
        $sender_id = 'k.mohamed@cequens.com';
        $chatkit = new Chatkit(
            [
                'instance_locator' => $this->getParameter('chatkit_instance_locator'),
                'key' => $this->getParameter('chatkit_key'),
            ]
        );
        $this->logger->debug('[InboxController] getting room by id '.$room_id);

        $room = $chatkit->getRoom(['id' => $room_id]);

        if ($room['status'] == 200) {
            $this->logger->debug('Rooooooom Info', array($room['body']['custom_data']['botId']));
            $chatkit->sendMessage(
                [
                    'sender_id' => $sender_id,
                    'room_id' => $room_id,
                    'text' => json_encode(['nlp' => [], 'type' => 'text', 'text' => $message], JSON_UNESCAPED_UNICODE),
                    'bot_id' => $room['body']['custom_data']['botId'],
                ]
            );

            $this->botService->sendMessage($room['body']['custom_data']['botId'], $room_name, $message);
        }


        return new JsonResponse(['success' => true], 200);
    }

    /**
     * @Route("/updateConversaionStatus", name="updateConversationStatus")
     * @Method("POST")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateConversationStatusAction(Request $request)
    {
        $room_id = $request->request->get('room_id');
        $status = $request->request->get('status');
        $chatkit = new Chatkit(
            [
                'instance_locator' => $this->getParameter('chatkit_instance_locator'),
                'key' => $this->getParameter('chatkit_key'),
            ]
        );

        if ($status == 'close') {
            $chatkit->deleteRoom(['id' => $room_id]);
        }

        return new Response('ok', 200);
    }

    /**
     * @Route("/triggerIsTyping", name="triggerIsTyping")
     * @Method("POST")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function triggerIsTypingAction(Request $request)
    {

        $room_id = $request->request->get('room_id');
        $room_name = $request->request->get('room_name');
        $sender_id = 'k.mohamed@cequens.com';
        $chatkit = new Chatkit(
            [
                'instance_locator' => $this->getParameter('chatkit_instance_locator'),
                'key' => $this->getParameter('chatkit_key'),
            ]
        );
        $room = $chatkit->getRoom(['id' => $room_id]);
        if ($room['status'] == 200) {
            $this->logger->debug('Rooooooom Info', array($room['body']['custom_data']['botId']));
            $response = $this->botService->triggerIsTyping($room['body']['custom_data']['botId'], $room_name, $room_id);
        }

        return new JsonResponse(['success' => true, 'data' => $response], 200);
    }
}
