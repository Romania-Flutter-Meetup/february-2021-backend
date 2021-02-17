<?php


namespace App\DataPersister;


use ApiPlatform\Core\Api\UrlGeneratorInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Utils\ValidateToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Utils\Mailer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataPersister implements DataPersisterInterface
{
    private $entityManager;
    private $userPasswordEncoder;
    private $userRepository;
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var ValidateToken
     */
    private $validateToken;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, UsersRepository $usersRepository, KernelInterface $kernel, ValidateToken $validateToken, Mailer $mailer, UrlGeneratorInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userRepository = $usersRepository;
        $this->kernel = $kernel;
        $this->validateToken = $validateToken;
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function supports($data): bool
    {

        return $data instanceof  Users;
    }

    /** @param Users $data */
    public function persist($data)
    {
        //Encode password
        if ($data->getPlainPassword() !== null) {
            if ($data->getPlainPassword()) {
                $data->setPassword($this->userPasswordEncoder->encodePassword($data,$data->getPlainPassword()));
                $data->eraseCredentials();
            }
        }

        $token = $this->validateToken->jwtEncode($data);
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        $this->mailer->sendEmail($data->getEmail(), $data->getName(), 'Flutter account confirmation', '', [], [],  $this->router->generate('email_confirm', ['token' => $token], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL));
    }

    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }

}