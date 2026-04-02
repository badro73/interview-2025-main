<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;

class HealthCheckController extends AbstractController
{
    #[Route('/health', name: 'health_check', methods: ['GET'])]
    public function index(EntityManagerInterface $em, CacheInterface $cache): JsonResponse
    {
        $checks = [
            'database' => false,
            'cache' => false,
        ];

        try {
            $em->getConnection()->executeQuery('SELECT 1');
            $checks['database'] = true;
        } catch (\Throwable) {
        }

        try {
            $cache->get('health_check', fn () => true);
            $checks['cache'] = true;
        } catch (\Throwable) {
        }

        $healthy = !in_array(false, $checks, true);

        return new JsonResponse([
            'status' => $healthy ? 'ok' : 'error',
            'checks' => $checks,
            'time' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ], Response::HTTP_OK);
    }
}