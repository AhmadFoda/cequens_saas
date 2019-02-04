<?php
/**
 * Created by PhpStorm.
 * User: karim
 * Date: 13/01/19
 * Time: 08:12 ص
 */

namespace CequensBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var ValidatorInterface
     */
    protected $validator;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param ContainerInterface $container
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ContainerInterface $container
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->container = $container;
    }


    public function getUsers()
    {
        $users = $this->entityManager->getRepository('CequensBundle:User')->findAll();
        return $users;

    }



}