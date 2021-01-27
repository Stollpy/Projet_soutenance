<?php

namespace App\Factory;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Faker\Factory;
use Symfony\Component\HttpClient\HttpClient;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Product|Proxy findOrCreate(array $attributes)
 * @method static Product|Proxy random()
 * @method static Product[]|Proxy[] randomSet(int $number)
 * @method static Product[]|Proxy[] randomRange(int $min, int $max)
 * @method static ProductRepository|RepositoryProxy repository()
 * @method Product|Proxy create($attributes = [])
 * @method Product[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class ProductFactory extends ModelFactory
{
    private $pexelsUrl = 'https://api.pexels.com/v1';
    private $API_KEY = '563492ad6f917000010000013f05f95926b34f90a85765778f98f09b';

    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $myFaker = Factory::create('fr_FR');

        // Requete avec HTTPClient afin de recuperer des images de l'API de Pexels
        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            $this->pexelsUrl . '/search?query=cbd&per_page=20', [
            'headers' => [
                'Authorization' => $this->API_KEY
            ],
        ]);
        $picture = $response->toArray();

        $products = [
            'OG Kush Indoor 23.5%',
            'Amnesia Indoor 19.5%',
            'Great White Shark 12%',
            'Passion Outdoor 17.5%',
            'Nepal Cream 20%',
            'Morocco Kush 20%',
            'Morocco Strawberry 15%',
            'Creme visage 1%',
        ];

        $index = array_rand($products, 1);
        return [
            'name' => $products[$index],
            'description' => $myFaker->sentence(128),
            'shop' => ShopFactory::random(),
            'category' => CategoryFactory::random(),
            'photo' => $picture['photos'][random_int(10, 19)]['src']['medium'],
            'price' => random_int(19, 50),
            'measured' => (bool)random_int(0, 1)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Product $product) {})
        ;
    }

    protected static function getClass(): string
    {
        return Product::class;
    }


}
