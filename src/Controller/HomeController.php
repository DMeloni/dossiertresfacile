<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 *
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * Returns the homepage.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}