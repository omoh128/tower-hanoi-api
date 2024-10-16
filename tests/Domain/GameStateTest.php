<?php

namespace Tests\Domain;

use App\Domain\GameState;
use PHPUnit\Framework\TestCase;

class GameStateTest extends TestCase
{
    public function testInitialState(): void
    {
        $state = GameState::initial();
        $pegs = $state->getPegs();
        
        $this->assertEquals(range(7, 1), $pegs[0]);
        $this->assertEquals([], $pegs[1]);
        $this->assertEquals([], $pegs[2]);
    }

    public function testValidMove(): void
    {
        $state = GameState::initial();
        $result = $state->moveDisk(0, 1);

        // Pass both handlers to eval
        $newState = $result->eval(
            function($error) { 
                $this->fail("Expected success but got error: $error");
                return null; // Never reached, but makes PHP happy
            },
            function($success) { 
                return $success; 
            }
        );

        $newPegs = $newState->getPegs();
        $this->assertEquals(range(7, 2), $newPegs[0]);
        $this->assertEquals([1], $newPegs[1]);
        $this->assertEquals([], $newPegs[2]);
    }

    public function testInvalidMove(): void
    {
        $state = GameState::initial();
        $result = $state->moveDisk(1, 2); // Try to move from empty peg
        
        $errorMessage = $result->eval(
            function($error) { 
                return $error; 
            },
            function($success) { 
                $this->fail('Expected error but got success');
                return null; // Never reached, but makes PHP happy
            }
        );
        
        $this->assertEquals('No disk to move', $errorMessage);
    }

    public function testInvalidDiskPlacement(): void
    {
        $state = GameState::initial();
        
        // First make a valid move
        $intermediateState = $state->moveDisk(0, 1)->eval(
            function($error) { 
                $this->fail("First move should succeed, got: $error");
                return null;
            },
            function($success) { 
                return $success; 
            }
        );
        
        // Try to place a larger disk on top of a smaller one
        $result = $intermediateState->moveDisk(0, 1);
        
        $errorMessage = $result->eval(
            function($error) { 
                return $error; 
            },
            function($success) { 
                $this->fail('Expected error but got success');
                return null;
            }
        );
        
        $this->assertEquals('Cannot place larger disk on smaller one', $errorMessage);
    }

    public function testGameCompletion(): void
    {
        $state = GameState::initial();
        $this->assertFalse($state->isComplete());
    }
}