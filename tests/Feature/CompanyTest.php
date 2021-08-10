<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CompanyTest extends TestCase
{

    use WithFaker;

    protected $endpoint = "/companies";


    /**
     * Get All Company.
     *
     * @return void
     */
    public function test_get_all_companies()
    {

        Company::factory()->count(6)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertJsonCount(6, 'data');

        // $response->dump();

        $response->assertStatus(200);
    }


    /**
     * Error Single Company.
     *
     * @return void
     */
    public function test_error_get_single_company()
    {
        $company = 'fake-uuid';

        $response = $this->getJson("{$this->endpoint}/{$company}");

        // $response->dump();

        $response->assertStatus(404);
    }

    /**
     * Get Single Company.
     *
     * @return void
     */
    public function test_get_single_company()
    {
        $company = Company::factory()->create();

        $response = $this->getJson("{$this->endpoint}/{$company->identify}");

        // $response->dump();

        $response->assertStatus(200);
    }

    /**
     * Validation Store Company
     *
     * @return void
     */
    public function test_validation_store_company()
    {

        $response = $this->postJson($this->endpoint, [
            'name' => '',
            'category_id' => '',
            'whatsapp' => '',
            'email' => '',
            'image' => ''
        ]);

        // $response->dump();

        $response->assertStatus(422);
    }

    /**
     * Store Company
     *
     * @return void
     */
    public function test_store_company()
    {

        $category = Category::factory()->create();

        $fakeImage = UploadedFile::fake()->image('laravel.png');

        $data = [
            'category_id' => $category->id,
            'name' => $this->faker()->unique()->name(),
            'email' => $this->faker()->unique()->email(),
            'whatsapp' => $this->faker->unique()->numberBetween(10000000000, 99999999999)
        ];

        $response = $this->call(
            'POST',
            $this->endpoint,
            $data,
            [],
            ['image' => $fakeImage]
        );

        // $response->dump();

        $response->assertStatus(201);
    }


    /**
     * Update Company
     *
     * @return void
     */
    public function test_update_company()
    {
        $company = Company::factory()->create();
        $category = Category::factory()->create();

        $data = [
            'category_id' => $category->id,
            'name' => $this->faker()->unique()->name(),
            'email' => $this->faker()->unique()->email(),
            'whatsapp' => $this->faker->unique()->numberBetween(10000000000, 99999999999)
        ];

        // Url not exists
        $response = $this->putJson(
            "{$this->endpoint}/fake-company",
            $data
        );
        $response->assertStatus(404);

        // Url not exists and validation failed
        $response = $this->putJson(
            "{$this->endpoint}/fake-company",
            []
        );
        $response->assertStatus(422);

        // Url exists and validation failed
        $response = $this->putJson(
            "{$this->endpoint}/{$company->uuid}",
            []
        );
        $response->assertStatus(422);

        // Update Category
        $response = $this->putJson(
            "{$this->endpoint}/{$company->uuid}",
            $data
        );
        $response->assertStatus(200);
    }


    /**
     * Delete Company
     *
     * @return void
     */
    public function test_delete_company()
    {

        $company = Company::factory()->create();

        // Url not found
        $response = $this->deleteJson("{$this->endpoint}/fake-url");
        $response->assertStatus(404);

        // Delete Category
        $response = $this->deleteJson("{$this->endpoint}/{$company->uuid}");
        $response->assertStatus(204);
    }
}
