<?php

namespace Herytz\RcuBundle\Controller;

use Herytz\RcuBundle\Contract\OnCompletedInterface;
use Herytz\RcuBundle\Contract\StoreProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RcuController extends AbstractController
{
  public function __construct(
    private string $tmpDir,
    private string $outputDir,
    private StoreProviderInterface $store,
    private ?OnCompletedInterface $onCompleted
  ) {
  }

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