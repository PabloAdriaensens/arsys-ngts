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
     * @Route("/transport", name="app_transport")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $from = $request->query->get('origin');
        $to = $request->query->get('destination') ?? '';

        if (empty($from)) {
            return (new JsonResponseFactory())->error('Add a valid city for the origin', Response::HTTP_BAD_REQUEST);
        }

        $routeData = (new TransportService())->getRouteData($from, $to);

        if (!is_string($routeData[0])) {
            return (new JsonResponseFactory())->success($routeData);
        }

        [$errorMessage, $errorCode] = $routeData;
        return (new JsonResponseFactory())->error($errorMessage, $errorCode);
    }
}
