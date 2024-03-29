<?php

namespace Contact\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ContactController extends AbstractActionController {

    protected $vhm;
    protected $em;
    protected $contactService;

    public function __construct($vhm, $em, $contactService) {
        $this->vhm = $vhm;
        $this->em = $em;
        $this->contactService = $contactService;
    }

    /**
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        $this->layout('layout/beheer');
        $page = $this->params()->fromQuery('page', 1);
        $query = $this->contactService->getContacts();
        $contactForms = $this->contactService->getItemsForPagination($query, $page, 10);

        return new ViewModel(
                array(
            'contactForms' => $contactForms,
                )
        );
    }

    /**
     * 
     * Action to set delete a blog
     */
    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('beheer/contact');
        }
        $contactForm = $this->contactService->getContactFormById($id);
        if (empty($contactForm)) {
            return $this->redirect()->toRoute('beheer/contact');
        }
        // Remove blog
        $this->contactService->deleteContactForm($contactForm);
        $this->flashMessenger()->addSuccessMessage('Contact verwijderd');
        return $this->redirect()->toRoute('beheer/contact');
    }
}
