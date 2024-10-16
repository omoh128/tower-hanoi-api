<?php
// src/Application/GameHandler.php

namespace App\Application;

use App\Domain\GameState;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GameHandler
{
    private GameState $gameState;

    public function __construct()
    {
        $this->gameState = GameState::initial();
    }

    public function getState(Request $request, Response $response): Response
    {
        $state = [
            'state' => $this->gameState->getPegs(),
            'finished' => $this->gameState->isComplete()
        ];
        
        $response->getBody()->write(json_encode($state));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function move(Request $request, Response $response, array $args): Response
    {
        $from = (int)$args['from'] - 1;
        $to = (int)$args['to'] - 1;

        $result = $this->gameState->moveDisk($from, $to);
        
        return $result->fold(
            fn(string $error) => $this->errorResponse($response, $error),
            fn(GameState $newState) => $this->successResponse($response, $newState)
        );
    }

    private function errorResponse(Response $response, string $error): Response
    {
        $response->getBody()->write(json_encode(['error' => $error]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    private function successResponse(Response $response, GameState $state): Response
    {
        $response->getBody()->write(json_encode([
            'state' => $state->getPegs(),
            'finished' => $state->isComplete()
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}