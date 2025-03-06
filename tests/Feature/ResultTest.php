<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Result;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_new_result_when_it_does_not_exist()
    {
        // Vérifier que la base est vide au départ
        $this->assertDatabaseCount('results', 0);

        // Appel de la méthode
        $result = (new \App\Http\Controllers\ResultController())->createResult('Qualify', '2025-03-06 14:00:00', 90, 'Monza');

        // Vérifier que le résultat a bien été créé
        $this->assertDatabaseHas('results', [
            'type' => 'Qualify',
            'starting_at' => '2025-03-06 14:00:00',
            'duration' => 90,
            'track' => 'Monza',
        ]);

        $this->assertInstanceOf(Result::class, $result);
    }

    public function test_it_does_not_create_duplicate_results()
    {
        // Insérer un résultat en base
        Result::create([
            'type' => 'Race',
            'starting_at' => '2025-03-06 16:00:00',
            'duration' => 120,
            'track' => 'Silverstone',
        ]);

        // Vérifier que le résultat existe
        $this->assertDatabaseCount('results', 1);

        // Essayer de créer un doublon
        $result = (new \App\Http\Controllers\ResultController())->createResult('Race', '2025-03-06 16:00:00', 120, 'Silverstone');

        // Vérifier qu'aucun nouveau résultat n'a été ajouté
        $this->assertDatabaseCount('results', 1);
        $this->assertNull($result);
    }

    public function test_it_can_retrieve_an_existing_result()
    {
        // Insérer un résultat en base
        $expectedResult = Result::create([
            'type' => 'Practice',
            'starting_at' => '2025-03-06 10:00:00',
            'duration' => 60,
            'track' => 'Spa-Francorchamps',
        ]);

        // Vérifier qu'on peut récupérer le bon résultat
        $result = (new \App\Http\Controllers\ResultController())->getResult('Practice', '2025-03-06 10:00:00', 60, 'Spa-Francorchamps');

        $this->assertNotNull($result);
        $this->assertEquals($expectedResult->id, $result->id);
    }

    public function test_it_returns_null_for_non_existing_result()
    {
        $result = (new \App\Http\Controllers\ResultController())->getResult('Practice', '2025-03-06 10:00:00', 60, 'Suzuka');

        $this->assertNull($result);
    }
}
