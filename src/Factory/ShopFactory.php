<?php

namespace App\Factory;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use Faker\Factory;
use Faker\Provider\fr_FR\Address;
use Symfony\Component\HttpClient\HttpClient;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Shop|Proxy findOrCreate(array $attributes)
 * @method static Shop|Proxy random()
 * @method static Shop[]|Proxy[] randomSet(int $number)
 * @method static Shop[]|Proxy[] randomRange(int $min, int $max)
 * @method static ShopRepository|RepositoryProxy repository()
 * @method Shop|Proxy create($attributes = [])
 * @method Shop[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class ShopFactory extends ModelFactory
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
        $myFaker->addProvider(new Address($myFaker));

        // Requete avec HTTPClient afin de recuperer des images de l'API de Pexels
        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            $this->pexelsUrl . '/search?query=cbd&per_page=20', [
                'headers' => [
                    'Authorization' => $this->API_KEY
                ],
            ]);
        $pic = $response->toArray();

        return [
            'user' => UserFactory::new()->create(['roles' => ['ROLE_ADMIN']]),
            'description' => self::faker()->sentence(128),
            'name' => self::faker()->company,
            'picture' => $pic['photos'][random_int(0, 9)]['src']['medium'],
            'shop_address' => $myFaker->streetAddress,
            'shop_city' => $myFaker->city,
            'shop_zipcode' => self::faker()->numberBetween(10001, 96000),
            'SIRET' => $myFaker->siret()
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this// ->afterInstantiate(function(Shop $shop) {})
            ;
    }

    protected static function getClass(): string
    {
        return Shop::class;
    }
}
