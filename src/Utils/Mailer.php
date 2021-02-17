<?php

namespace App\Utils;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Mime\Address;

class Mailer extends AbstractController
{

    private $senderEmail;
    private $senderName;

    public function __construct($senderEmail, $senderName)
    {
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    // $toAddress = Email address of type string
    // $toName = Name of type string
    // $subject = Subject of the email of type string
    // $template = Template to render --- just the name of twig without path and extension
    // $context = Array with variables that are sent to TWIG template
    /**
     * @param string $toAddress
     * @param string $toName
     * @param string $subject
     * @param string $template
     * @param array $context
     * @param array $paths
     * @param string $content
     * @return bool
     */
    public function sendEmail(string $toAddress, string $toName, string $subject, string $template, array $context, array $paths = [], string $content = null)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom('no-reply@flutters.ro', 'Flutters Api Platform');
        $email->addTo($toAddress,$toName);
        $email->setSubject($subject);
        $email->addContent(
            "text/html", $content
        );

        $sendgrid = new \SendGrid('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');
        try {
            $response = $sendgrid->send($email);

        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return true;
    }
}