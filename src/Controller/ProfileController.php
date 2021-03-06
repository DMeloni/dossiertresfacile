<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProfileController
 *
 * @package App\Controller
 */
class ProfileController extends AbstractController
{
    /**
     * Returns the uniq profile page.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('profile.html.twig');
    }
}
