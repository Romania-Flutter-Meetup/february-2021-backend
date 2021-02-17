<?php
namespace App\Serializer;

use App\Entity\Users;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RegisterUserNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private $normalizer;
    /**
     * @var JWTEncoderInterface
     */
    private $JWTEncoder;
    /**
     * @var Security
     */
    private $security;

    public function __construct(NormalizerInterface $normalizer, JWTEncoderInterface $JWTEncoder)
    {
        if (!$normalizer instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException('The normalizer must implement the DenormalizerInterface');
        }

        $this->normalizer = $normalizer;
        $this->JWTEncoder = $JWTEncoder;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->normalizer->denormalize($data, $class, $format, $context);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->normalizer->supportsDenormalization($data, $type, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $result = $this->normalizer->normalize($object, $format, $context);

        //add token on register user
        if ($object instanceof Users and $context['groups'][0] == 'users:read') {
            $token = $this->JWTEncoder->encode([
                'id' => $object->getId(),
                'username' => $object->getEmail()
            ]);
            $result['token'] = $token;
            $result['refreshToken'] = $token;
        }
        return $result;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->normalizer->supportsNormalization($data, $format);
    }
}