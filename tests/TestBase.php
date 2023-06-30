<?php

namespace App\Tests;

use App\Customer\Infrastructure\Repository\BookerDoctrineRepository;
use App\User\Infrastructure\Repository\UserDoctrineRepository;
use ApiTestCase\JsonApiTestCase;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use DateTime;

class TestBase extends JsonApiTestCase
{
    protected function login(): void
    {
        $userRepository = static::getContainer()->get(UserDoctrineRepository::class);
        $user = $userRepository->findOneBy([]);
        $this->client->loginUser($user);
    }

    protected function requestWithJsonBody(string $method, string $uri, array $jsonBody): void
    {
        $this->client->request(
            $method,
            $uri,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($jsonBody)
        );
    }

    protected function getBirthDate(): DateTime
    {
        return (new DateTime())->modify(sprintf('-%d years', rand(20, 60)));
    }

    protected function post(string $uri, array $jsonBody): void
    {
        $this->requestWithJsonBody('POST', $uri, $jsonBody);
    }

    protected function patch(string $uri, array $jsonBody): void
    {
        $this->requestWithJsonBody('PATCH', $uri, $jsonBody);
    }

    protected function put(string $uri, array $jsonBody): void
    {
        $this->requestWithJsonBody('PUT', $uri, $jsonBody);
    }

    protected function delete(string $uri, ?array $jsonBody = null): void
    {
        $this->client->request(
            'DELETE',
            $uri,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonBody !== null ? json_encode($jsonBody) : null
        );
    }
}
