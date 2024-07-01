<?php

namespace Herytz\RcuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RcuController extends AbstractController
{
  #[Route('%rcu.upload_status_path%', name: 'rcu_upload_status_path', methods: "GET")]
  public function uploadStatus(): Response
  {
    return $this->json(['message' => 'Welcome to your rcu controller::uploadStatus!']);
  }

  #[Route('%rcu.upload_path%', name: 'rcu_upload_path', methods: "POST")]
  public function upload(): Response
  {
    return $this->json(['message' => 'Welcome to your rcu controller::upload!']);
  }
}
