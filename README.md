Tower of Hanoi API

This is a simple HTTP API for the 7-disk Tower of Hanoi game.

Requirements

PHP >= 8.1
Composer
PHPUnit (for testing)
Setup

Clone the repository:
git clone https://github.com/omoh128/tower-hanoi-api
cd tower-hanoi-api
Install dependencies:
composer install
Start the PHP built-in server:
php -S localhost:8000 -t public
Access the API

The API will be available at http://localhost:8000.

API Endpoints

Get the current game state:
Method: GET
Endpoint: /state
Response: Returns the current state of the pegs and disks.

curl http://localhost:8000/state
Move a disk from one peg to another:
Method: POST
Endpoint: /move/{from}/{to}
Parameters:
* from: The peg number to move the disk from (0, 1, or 2).
* to: The peg number to move the disk to (0, 1, or 2).
Response: Returns the new game state or an error message.

Example request to move a disk from peg 1 to peg 2:

curl -X POST http://localhost:8000/move/1/2
Testing

To run the tests, execute the following command:

phpunit
(These tests verify basic functionality of the API.)

composer run test
