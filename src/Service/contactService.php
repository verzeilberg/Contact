<?php

namespace Contact\Service;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Laminas\Paginator\Paginator;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Mail;
use Laminas\Mime\Part as MimePart;
use Laminas\Mime\Message as MimeMessage;

/*
 * Entities
 */
use Contact\Entity\Contact;

class contactService implements contactServiceInterface {
    private mixed $entityManager;

    /**
     * Constructor.
     */
    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Get array of contacts
     */
    public function getContacts()
    {

        $qb = $this->entityManager->getRepository(Contact::class)->createQueryBuilder('c')
            ->orderBy('c.dateCreated', 'DESC');
        return $qb->getQuery();
    }

    /**
     *
     * Get blog object based on id
     * @param       id  $id The id to fetch the blog from the database
     * @return      object
     */
    public function getContactFormById($id): object
    {
        $contactForm = $this->entityManager->getRepository(Contact::class)
                ->findOneBy(['id' => $id], []);

        return $contactForm;
    }
    
        /**
     *
     * Delete a Blog object from database
     * @param       blog $blog object
     * @return      void
     *
     */
    public function deleteContactForm($contactForm) {
        $this->entityManager->remove($contactForm);
        $this->entityManager->flush();
    }

    /**
     *
     * Send mail
     * 
     * @param       contact $contact object
     * @return      void
     *
     */
    public function sendMail($contact) {

        $email_to_adress = $contact->getEmail();
        $email_to_name = $contact->getName();
        $subject = $contact->getSubject();
        $message = $contact->getMessage();
        $salutation = $contact->getSalutation();

        //$baseurl = $this->getBaseUrl();
        //$config = $this->getConfig();
        $email_template = 'module/Contact/templates/send_contact_email.phtml';
        //Sender information
        $mail_sender_email = 'noreply@verzeilberg.nl';
        $mail_sender_name = 'Verzeilberg';
        //Reply infomrtaion
        $mail_reply_email = 'noreply@verzeilberg.nl';
        $mail_reply_name = 'Verzeilberg';
        //Mail subject
        $mail_subject = 'Contact met Verzeilberg: ' . $subject;

        ob_start();
        require_once($email_template);
        $email_body = ob_get_clean();

        $mail = new Mail\Message();
        $mail->setEncoding("UTF-8");

        $html = new MimePart($email_body);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));

        $mail->setBody($body);
        $mail->setFrom($mail_sender_email, $mail_sender_name);
        $mail->addReplyTo($mail_reply_email, $mail_reply_name);
        $mail->addTo($email_to_adress, $email_to_name);
        $mail->setSubject($mail_subject);


        $transport = new Mail\Transport\Sendmail();

        try {
            $transport->send($mail);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     * @param $query
     * @param int $currentPage
     * @param int $itemsPerPage
     * @return Paginator
     */
    public function getItemsForPagination($query, int $currentPage = 1, int $itemsPerPage = 10)
    {
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage($itemsPerPage);
        $paginator->setCurrentPageNumber($currentPage);
        return $paginator;
    }

    /**
     *
     * Get base url
     * 
     * @return      string
     *
     */
    public function getBaseUrl() {
        $helper = $this->getServerUrl();
        return $helper->__invoke($this->request->getBasePath());
    }

}
