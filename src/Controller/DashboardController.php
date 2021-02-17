<?php

namespace App\Controller;

use App\Entity\Apartments;
use App\Entity\MonthlyPayments;
use App\Entity\WaterConsumptions;
use App\Form\FormResetPasswordType;
use App\Utils\ValidateToken;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class DashboardController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $em, Security $security, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->security = $security;
        $this->logger = $logger;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/reset-password/{token}", name="reset-password-form", methods={"GET","POST"})
     */
    public function index(string $token, Request $request, ValidateToken $validateToken, UserPasswordEncoderInterface $userPasswordEncoder, JWTEncoderInterface $JWTEncoder)
    {
        $form = $this->createForm(FormResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validateResult = $validateToken->validate($token);
            if(isset($validateResult['errors'])) {
                return new JsonResponse(['message' => $validateResult['errors']], RESPONSE::HTTP_BAD_REQUEST);
            }
            $user = $validateResult['user'];

            $password = $form->get('plainPassword')->getData();

            $user->setPassword($userPasswordEncoder->encodePassword($user, $password));
            $this->em->persist($user);
            $this->em->flush();

            return new JsonResponse(['message' => 'Password changed with success.'], Response::HTTP_OK);
        }
        return $this->render('dashboard/index.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request             $request
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse|Response
     *
     * @Route("/api/water_consumptions/add", name="water_consumption_add", methods={"POST"})
     */
    public function waterConsumption(Request $request, SerializerInterface $serializer) {

        $requiredFields = ['bathroomHot', 'bathroomCold' ,'kitchenHot', 'kitchenCold'];
        $data = json_decode($request->getContent(),true);
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(["message" => $field . " not found"],Response::HTTP_BAD_REQUEST);
            }
        }

        /** @var Apartments $apartment */
        $apartment = $this->em->getRepository(Apartments::class)->findOneBy(['user' => $this->security->getUser()]);

        if ($apartment === null) {
            if (!isset($data[$field])) {
                return new JsonResponse(["message" => "This user doesn't have an apartment."],Response::HTTP_BAD_REQUEST);
            }
        }

        $currentDate = new \DateTime();

        $qb = $this->em->createQueryBuilder();

        $waterConsumption = $qb->select("wc")->from(WaterConsumptions::class,"wc")
            ->where("wc.createdAt >= '" . $currentDate->format('Y-m-') . "01'")
            ->andWhere("wc.createdAt <= '" . $currentDate->format('Y-m-') . "31'")
            ->andWhere("wc.apartment = " . $apartment->getId())
            ->getQuery()->getResult();



        if ($waterConsumption) {
            return new JsonResponse(["message" => "This user has already entered water consumption for the current month"],Response::HTTP_BAD_REQUEST);
        }

        $newWaterConsumption = new WaterConsumptions();

        $newWaterConsumption->setApartment($apartment);
        $newWaterConsumption->setBathroomCold($data['bathroomCold']);
        $newWaterConsumption->setBathroomHot($data['bathroomHot']);
        $newWaterConsumption->setKitchenCold($data['kitchenCold']);
        $newWaterConsumption->setKitchenHot($data['kitchenHot']);

        $this->em->persist($newWaterConsumption);
        $this->em->flush();

        $json  = $serializer->serialize([$newWaterConsumption], 'json', [
            'groups'=>['water:read'],
            'jsonld_has_context' => true,
            'resource_class' => WaterConsumptions::class,
            'request_uri' => $request->getRequestUri(),
            'uri' => $request->getUri()
        ]);

        return new Response($json, Response::HTTP_OK, ['content-type'=>'application/json']);
    }

    /**
     * @param Request             $request
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse|Response
     *
     * @Route("/api/monthly_payments/add", name="monthly_payments_add", methods={"POST"})
     */
    public function monthlyPayments(Request $request, SerializerInterface $serializer) {
        $requiredFields = ['apartment', 'coldWater' ,'hotWater', 'total'];
        $data = json_decode($request->getContent(),true);
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(["message" => $field . " not found"],Response::HTTP_BAD_REQUEST);
            }
        }

        /** @var Apartments $apartment */
        $apartment = $this->em->getRepository(Apartments::class)->find((int)$data['apartment']);

        if ($apartment === null) {
            if (!isset($data[$field])) {
                return new JsonResponse(["message" => "This apartment doesn't exist."],Response::HTTP_BAD_REQUEST);
            }
        }

        $currentDate = new \DateTime();

        $qb = $this->em->createQueryBuilder();

        $monthlyPayments = $qb->select("mp")->from(MonthlyPayments::class,"mp")
            ->where("mp.createdAt >= '" . $currentDate->format('Y-m-') . "01'")
            ->andWhere("mp.createdAt <= '" . $currentDate->format('Y-m-') . "31'")
            ->andWhere("mp.apartment = " . $apartment->getId())
            ->getQuery()->getResult();



        if ($monthlyPayments) {
            return new JsonResponse(["message" => "For this user you already entered a payment for the current month"],Response::HTTP_BAD_REQUEST);
        }

        $newMonthlyPayments = new MonthlyPayments();

        $newMonthlyPayments->setApartment($apartment);
        $newMonthlyPayments->setStatus(false);
        $newMonthlyPayments->setColdWater($data['coldWater']);
        $newMonthlyPayments->setHotWater($data['hotWater']);
        $newMonthlyPayments->setTotal($data['total']);

        $this->em->persist($newMonthlyPayments);
        $this->em->flush();

        $json  = $serializer->serialize([$newMonthlyPayments], 'json', [
            'groups'=>['monthly:read'],
            'jsonld_has_context' => true,
            'resource_class' => MonthlyPayments::class,
            'request_uri' => $request->getRequestUri(),
            'uri' => $request->getUri()
        ]);

        return new Response($json, Response::HTTP_OK, ['content-type'=>'application/json']);
    }
}