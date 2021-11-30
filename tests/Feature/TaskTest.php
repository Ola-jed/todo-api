<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    /**
     * Testing the creation of some tasks
     */
    public function testTaskCreation(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $task = [
            'title'       => $this->faker->sentence,
            'description' => $this->faker->realText(),
            'date_limit'  => Carbon::now()->addDays($this->faker->numberBetween(1, 50))->format('d/m/Y'),
            'has_steps'   => $this->faker->boolean(),
            'priority'    => $this->faker->numberBetween(1, 10)
        ];
        $this->postJson('/api/tasks',$task)
            ->assertOk()
            ->assertJsonStructure(['message','data'])
            ->assertJsonFragment(['message' => 'Task created']);
        $this->postJson('/api/tasks',[])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Testing retrieving all tasks
     * We first create fake tasks for our user
     */
    public function testGetAll(): void
    {
        $user = User::factory()->create();
        Task::factory(10)->create();
        Sanctum::actingAs($user);
        $this->get('/api/tasks')
            ->assertOk()
            ->assertJsonStructure(['data','count','remaining'])
            ->assertJsonFragment(['count' => 10,'remaining' => 0]);
        $this->get('/api/tasks?limit=5')
            ->assertOk()
            ->assertJsonStructure(['data','count','remaining'])
            ->assertJsonFragment(['count' => 5,'remaining' => 5]);
    }

}
