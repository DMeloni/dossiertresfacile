<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController
{
    public function index(Request $request, $errorCode): Response
    {
        return $this->render('error.html.twig', ['errorCode' => $errorCode]);
    }
}