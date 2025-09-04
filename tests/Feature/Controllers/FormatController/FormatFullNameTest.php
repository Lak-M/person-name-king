<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\FormatController;

use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;
use Lakm\PersonName\Enums\Country;
use Lakm\PersonName\Enums\Ethnicity;
use Tests\TestCase;

final class FormatFullNameTest extends TestCase
{
    public function test_it_returns_validation_error_message_when_empty_full_name_field_provided()
    {
        $response = $this->sendRequest(['full_name' => ''], 'full');

        $response->assertInvalid(['full_name' => ['required']]);
    }

    public function test_it_returns_validation_error_message_when_empty_first_name_field_provided()
    {
        $response = $this->sendRequest(['first_name' => ''], 'parts');

        $response->assertInvalid(['first_name' => ['required']]);
    }

    public function test_it_returns_validation_error_message_when_name_parts_are_invalid()
    {
        $this->sendRequest(['first_name' => '@henry'], 'parts')
            ->assertInvalid(['name']);

        $this->sendRequest(['first_name' => 'henry', 'middle_name' => '@henry'], 'parts')
            ->assertInvalid(['name']);

        $this->sendRequest(['first_name' => 'henry', 'last_name' => '@henry'], 'parts')
            ->assertInvalid(['name']);
    }

    public function test_it_returns_validation_error_message_when_invalid_country_field_provided()
    {
        $this->sendRequest(['full_name' => '', 'country' => 'InvalidCountry'], 'full')
            ->assertInvalid([
                'full_name' => ['required'],
                'country',
            ]);

        $this->sendRequest(['first_name' => '', 'country' => 'InvalidCountry'], 'parts')
            ->assertInvalid([
                'first_name' => ['required'],
                'country',
            ]);
    }

    public function test_it_returns_formatted_full_name()
    {
        $fullNameResponse = $this->sendRequest(['full_name' => 'Prof. Dr. Maria Anna de la Vega III PhD'], 'full');
        $namePartsResponse = $this->sendRequest([
            'first_name' => 'Maria',
            'middle_name' => 'Anna',
            'last_name' => 'de la Vega',
            'prefix' => 'Dr. Prof.',
            'suffix' => 'III PhD',
        ], 'parts');

        foreach ([$fullNameResponse, $namePartsResponse] as $response) {
            $response->assertOk()->assertSessionHasNoErrors();

            $response->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->has('name', fn (Assert $page) => $page
                    ->where('full', 'Prof. Dr. Maria Anna de la Vega III PhD')
                    ->where('first', 'Maria')
                    ->where('middle', 'Anna')
                    ->where('last', 'de la Vega')
                    ->where('sorted', 'de la Vega, Maria Anna')
                    ->where('possessive', 'Maria\'s')
                    ->where('prefix', 'Dr. Prof.')
                    ->where('suffix', 'III PhD')
                    ->where('redated', 'Mar*****')
                    ->where('mentionable', '@Maria')
                    ->where('family', 'de la Vega')
                    ->where('abbreviations', [
                        'M. de la Vega',
                        'M. A. de la Vega',
                        'Maria d. l. V.',
                        'Maria A. de la Vega',
                        'M. A. d. l. V.',
                    ])
                )
            );
        }
    }

    public function test_it_returns_country_specific_options()
    {
        $fullNameResponse = $this->sendRequest(['full_name' => 'Omar Hassan Ahmad Al-Bashir', 'country' => Ethnicity::Arab->value], 'full');
        $namePartsResponse = $this->sendRequest([
            'first_name' => 'Omar',
            'middle_name' => 'Hassan Ahmad',
            'last_name' => 'Al-Bashir',
            'country' => Ethnicity::Arab->value,
        ], 'parts');

        foreach ([$fullNameResponse, $namePartsResponse] as $response) {
            $response->assertOk()->assertSessionHasNoErrors();
            $response->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->has('name', fn (Assert $page) => $page
                    ->where('father', 'Hassan')
                    ->where('grand_father', 'Ahmad')
                    ->etc()
                )
            );
        }
    }

    public function test_it_returns_null_when_option_is_not_supported()
    {
        $fullNameResponse = $this->sendRequest(['full_name' => '李小龙', 'country' => Country::CHINA->value], 'full');
        $namePartsResponse = $this->sendRequest([
            'first_name' => '小',
            'middle_name' => '龙',
            'last_name' => '李',
            'country' => Country::CHINA->value,
        ], 'parts');

        foreach ([$fullNameResponse, $namePartsResponse] as $response) {
            $response->assertOk()->assertSessionHasNoErrors();
            $response->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->has('name', fn (Assert $page) => $page
                    ->where('suffix', 'Not Supported')
                    ->where('abbreviations', 'Not Supported')
                    ->etc()
                )
            );
        }
    }

    private function sendRequest(array $data, string $type): TestResponse
    {
        if ($type === 'full') {
            $route = route('format-full-name');
        } else {
            $route = route('format-name-parts');
        }

        return $this->post($route, $data);
    }
}
