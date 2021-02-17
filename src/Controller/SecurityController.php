<?php

namespace App\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Utils\Mailer;
use App\Utils\ValidateToken;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\SerializerInterface;


class SecurityController extends AbstractController
{
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    private $request;
    private $em;
    private $websiteUrl;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, $websiteUrl)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->em = $entityManager;
        $this->websiteUrl = $websiteUrl;
    }


    /**
     * @Route("/api/login", name="api_login")
     */
    public function loginAPI(IriConverterInterface $iriConverter,JWTEncoderInterface $JWTEncoder, UsersRepository $usersRepository, UserPasswordEncoderInterface $passwordEncoder, SerializerInterface $serializer)
    {

        $data = json_decode($this->request->getContent(),true);
        /** @var Users $user */
        $user = $usersRepository->findOneBy([
            'email' => $data['email']
        ]);

        if (!$user) {
            return new JsonResponse(array("message" => "Invalid email"),404);
        }

        if (!$passwordEncoder->isPasswordValid($user,$data['password'])) {
            return new JsonResponse(array("message" => "Invalid password"),401);
        }

        $token = $JWTEncoder->encode([
            'id' => $user->getId(),
            'username' => $user->getEmail()
        ]);


        $json = $serializer->serialize($user,'json', ['groups' => 'users:read']);

        $temp = json_decode($json,true);
        $temp['iri']  = $iriConverter->getIriFromItem($user);
        $temp['token']  = $token;

        return new JsonResponse($temp,200);
    }
//
    /**
     * @Route("/api/recover-password", name="recover_password")
     * @param IriConverterInterface $iriConverter
     * @param JWTEncoderInterface $JWTEncoder
     * @param UsersRepository $usersRepository
     * @param Mailer $mailer
     * @param KernelInterface $kernel
     * @return JsonResponse|Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function recoverPassword(UsersRepository $usersRepository, ValidateToken $validateToken, Mailer $mailer)
    {
        $data = json_decode($this->request->getContent(), true);
        $user = $usersRepository->findOneBy([
            'email' => $data['email']
        ]);
        if (is_null($user)) {
            return new JsonResponse(array("message" => "Invalid email"), 404);
        }
        $token = $validateToken->jwtEncode($user);
        $this->em->persist($user);
        $this->em->flush();
        $result['token'] = $token;
        $mailer->sendEmail($user->getEmail(), $user->getName(), 'Flutter password recovery', '', [], [], $this->websiteUrl.'reset-password/'.$token);

        return new JsonResponse($result, Response::HTTP_OK);
    }


//    /**
//     * @Route("/api/check/email", name="validate_email")
//     * Check if an email is already registered
//     */
//    public function checkEmail(UsersRepository $usersRepository)
//    {
//        $data = json_decode($this->request->getContent(),true);
//        $user = $usersRepository->findOneBy([
//            'email' => $data['email']
//        ]);
//
//        if (is_null($user)) {
//            return new JsonResponse([],204);
//        }
//        return new JsonResponse(array("message" => "E-mail already registered"),401);
//    }
//
    /**
     * @Route("/api/email-confirm/{token}", name="email_confirm")
     * @param Request $request
     * @param $token
     * @param ValidateToken $validateToken
     * @return JsonResponse
     */
    public function EmailConfirm(Request $request, $token, ValidateToken $validateToken)
    {
        $validateResult = $validateToken->validate($token);

        if(isset($validateResult['errors'])) {
            return new JsonResponse(['message' => $validateResult['errors']], 400);
        }
        $user = $validateResult['user'];
        $user->setIsEmailConfirmed(true);
        $user->setIsActive(true);
        $this->em->persist($user);
        $this->em->flush();
        return new JsonResponse([], 204);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
