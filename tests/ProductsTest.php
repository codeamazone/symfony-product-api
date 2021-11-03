<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Product;

class ProductsTest extends ApiTestCase
{
    public function testGetAllProducts(): void
    {
        $client = static::createClient();
        $client->request('GET', 'api/products');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Product::class);
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 50,            
        ]);       
    }

    public function testGetOneProduct(): void
    {
        $client = static::createClient();
        $id = $this->findIriBy(Product::class, ['name' => 'product0']);
        $client->request('GET', $id);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@type' => 'Product',
            'name' => 'product0',
            'color' => 'color0',
        ]);
    }

    public function testCreateValidProduct(): void
    {
        $client = static::createClient();
        $client->request('POST', 'api/products', ['json' => [
            'name' => 'myproduct',
            'gtin' => '12345678901234',
            'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
             Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus
             et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, 
             ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. 
             Donec pede justo, fringilla vel, aliquet nec, vulputate',
             'color' => 'green',
             'price' => 299.9
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@type' => 'Product',
            'name' => 'myproduct',
            'gtin' => '12345678901234',
            'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
             Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus
             et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, 
             ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. 
             Donec pede justo, fringilla vel, aliquet nec, vulputate',
             'color' => 'green',
             'price' => 299.9           
        ]);
        $this->assertMatchesResourceItemJsonSchema(Product::class);
    }

    public function testCreateProductGtinWrongLength(): void
    {
        /**
         * Make sure that entering a GTIN of the wrong length causes an error and the display
         * of the appropriate error message
        */
        $client = static::createClient();
        $response = $client->request('POST', 'api/products', ['json' => [
            'name' => 'myproduct',
            'gtin' => '123',
            'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
             Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus
             et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, 
             ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. 
             Donec pede justo, fringilla vel, aliquet nec, vulputate',
             'color' => 'green',
             'price' => 299.9
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'gtin: The GTIN should consist of exactly 14 digits.'           
        ]);
    }

    public function testCreateProductGtinNonnumeric(): void
    {
        /** Make sure that entering nonnumeric characters for the GTIN causes an error and the
         * appropriate error message is displayed
         */
        
        $client = static::createClient();
        $response = $client->request('POST', 'api/products', ['json' => [
            'name' => 'myproduct',
            'gtin' => 'qwertzuiopasdf',
            'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
             Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus
             et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, 
             ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. 
             Donec pede justo, fringilla vel, aliquet nec, vulputate',
             'color' => 'green',
             'price' => 299.9
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'gtin: The GTIN should only consist of digits.'           
        ]);
    }

    public function testCreateProductWithBlanks():void
    {
        /**
         * Make sure that sending a POST request with empty fields fails and returns
         * the appropriate error messages except for the GTIN field, which can be blank 
        */

        $client = static::createClient();
        $client->request('POST', 'api/products', ['json' => []]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'name: This value should not be blank.
description: This value should not be blank.
color: This value should not be blank.
price: This value should not be blank.',           
        ]);
    }

    public function testUpdateProduct(): void
    {
        $client = static::createClient();
        $id = $this->findIriBy(Product::class, ['name' => 'product6']);
        $client->request('PUT', $id, ['json' => [
            'name' => 'product6',            
            'color' => 'new color',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@id' => $id,
            'name' => 'product6',
            'color' => 'new color',
        ]);
    }

    public function testDeleteProduct(): void
    {
        $client = static::createClient();
        $id = $this->findIriBy(Product::class, ['name' => 'product6']);
        $client->request('DELETE', $id);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Product::class)->findOneBy([
                'id' => $id
            ])
        );
    }
}
