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
    /**
     * @var JsonResponseFactory
     */
    private JsonResponseFactory $jsonResponseFactory;

    public function __construct()
    {
        $this->jsonResponseFactory = new JsonResponseFactory();
    }

    /**
     * @Route("/transport", name="app_transport")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $routeData = (new TransportService())->getRouteData(
            $request->query->get('origin'),
            $request->query->get('destination')
        );

        if (count($routeData) > 1) {
            return $this->jsonResponseFactory->error($routeData[0], (int)$routeData[1]);
        }

        return $this->jsonResponseFactory->success($routeData);
    }
}
