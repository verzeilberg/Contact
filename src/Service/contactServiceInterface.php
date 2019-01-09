<?php

namespace Contact\Service;

interface contactServiceInterface {

    /**
     *
     * Get array of contacts
     *
     * @return      array
     *
     */
    public function getContacts();
    
    public function getContactFormById($id);
    
    public function deleteContactForm($contactForm);

    /**
     *
     * Send mail
     * 
     * @param       contact $contact object
     * @return      void
     *
     */
    public function sendMail($contact);

    /**
     *
     * Get base url
     * 
     * @return      string
     *
     */
    public function getBaseUrl();
}
