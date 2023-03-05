<?php

namespace App\Infrastructure\Http;

use App\Application\Factory\JsonResponseFactory;
use App\Application\Service\TransportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransportController extends AbstractController
{
    private $jsonResponseFactory;
    private $transportService;

    public function __construct(JsonResponseFactory $jsonResponseFactory, TransportService $transportService)
    {
        $this->jsonResponseFactory = $jsonResponseFactory;
        $this->transportService = $transportService;
    }

    /**
     * @Route("/transport", name="app_transport")
     * @param Request $request
     * @return Response
     * @throws \JsonException
     */
    public function index(Request $request): Response
    {
        $from = $request->query->get('origin');
        $to = $request->query->get('destination') ?: '';

        if (!$from) {
            return $this->jsonResponseFactory->error('Add a valid city for the origin', Response::HTTP_BAD_REQUEST);
        }

        $routeData = $this->transportService->getRouteData($from, $to);

        if (!is_string($routeData[0])) {
            return $this->jsonResponseFactory->success($routeData);
        }

        [$errorMessage, $errorCode] = $routeData;
        return $this->jsonResponseFactory->error($errorMessage, $errorCode);
    }
}
