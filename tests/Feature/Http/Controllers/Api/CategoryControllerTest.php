<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testInvalidationData()
    {
        $response = $this->json('POST', route('categories.store'), []);
        //dd($response->content());
        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'POST',
            route('categories.store'),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        );            
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $category = factory(Category::class)->create();
        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            []
        );
        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        );
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $this->assertInvalidationFields(
            $response, ['name'], 'required'
        );
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $this->assertInvalidationFields(
            $response, ['name'], 'max.string', ['max' => 255]
        );
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $this->assertInvalidationFields(
            $response, ['is_active'], 'boolean'
        );
    }

    public function testStore()
    {
        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test'
        ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());
        
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ]);
        $response
            ->assertJsonFragment([
                'description' => 'description',
                'is_active' => false
            ]);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'is_active' => false,
            'description' => 'description'
        ]);
        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => 'test',
                'is_active' => true
            ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'description' => 'test',
                'is_active' => true
            ]);
        
        // empty description is converted to null
        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => ''
            ]);
        
        $response->assertJsonFragment([
                'description' => null
        ]);

        $category->description = 'test';
        $category->save();

        // also testing null
        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => null
            ]);
        
        $response->assertJsonFragment([
                'description' => null
        ]);
    }

    public function testDestroy()
    {
        $category = factory(Category::class)->create();
        $response = $this->json(
            'DELETE',
            route('categories.destroy', ['category' => $category->id]));
        $response->assertStatus(204);
        
        $response = $this->get(route('categories.show', ['category' => $category->id]));
        $response->assertStatus(404);

        // fc2
        $category = factory(Category::class)->create();
        $response = $this->json('DELETE', route('categories.destroy', ['category' => $category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        $this->assertNotNull(Category::withTrashed()->find($category->id));
    }
}
