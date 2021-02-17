<?php

namespace App\Utils;


use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\HttpKernel\KernelInterface;

class ValidateToken
{
    private $entityManager;
    private $kernel;
    /**
     * @var JWTEncoderInterface
     */
    private $JWTEncoder;

    public function __construct(EntityManagerInterface $entityManager, KernelInterface $kernel, JWTEncoderInterface $JWTEncoder)
    {
        $this->entityManager = $entityManager;
        $this->kernel = $kernel;
        $this->JWTEncoder = $JWTEncoder;
    }

    public function validate($token) {

        if(!isset($token) or $token == '') {
            $result['errors'] = 'The token is required';
            return $result;
        }

        try {
            $arrayJWT = $this->JWTEncoder->decode($token);
        }
        catch (JWTDecodeFailureException $exception){
            $result['errors'] = 'Token decoding error';
            return $result;
        }

        $userRepository = $this->entityManager->getRepository(Users::class);
        $user = $userRepository->findOneBy(array("email" => $arrayJWT['username']));
        if ($user === null) {
            $result['errors'] = 'The user does not exist';
            return $result;
        }
        $today = date_timestamp_get(new \DateTime());
        $expire = $arrayJWT['exp'];
        if($today > $expire) {
            $result['errors'] = 'The token expired';
            return $result;
        }
        $result['success'] = $arrayJWT;
        $result['user'] = $user;
        return $result;
    }

    function jwtEncode($user){

        return $this->JWTEncoder->encode([
            'id' => $user->getId(),
            'username' => $user->getEmail()
        ]);
    }

}