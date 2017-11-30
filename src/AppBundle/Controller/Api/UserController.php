<?php

namespace AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{

  public function loginAction(Request $request)
  {
      $data['username'] = $request->get('_username');
      $data['password'] = $request->get('_password');

      $response = new JsonResponse($data);
      $response->headers->set('Authentication', 'Basic cG9wZW1fYXV0aDpCbGluazE4Mg==');

      return $response;
  }

}
