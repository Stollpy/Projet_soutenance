<?php

namespace App\Factory;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Category|Proxy findOrCreate(array $attributes)
 * @method static Category|Proxy random()
 * @method static Category[]|Proxy[] randomSet(int $number)
 * @method static Category[]|Proxy[] randomRange(int $min, int $max)
 * @method static CategoryRepository|RepositoryProxy repository()
 * @method Category|Proxy create($attributes = [])
 * @method Category[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class CategoryFactory extends ModelFactory
{
    static $counter = 0;

    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $categories = [
            0 => ['label' => 'Accessoires', 'glyph' => 'accessoires.png'],
            1 => ['label' => 'Alimentation', 'glyph' => 'alimentation.png'],
            2 => ['label' => 'Beauté', 'glyph' => 'beaute.png'],
            3 => ['label' => 'Bonbons', 'glyph' => 'bonbons.png'],
            4 => ['label' => 'Fleurs', 'glyph' => 'fleurs.png'],
            5 => ['label' => 'Huiles', 'glyph' => 'huiles.png'],
            6 => ['label' => 'Résines', 'glyph' => 'resines.png'],
            7 => ['label' => 'Santé', 'glyph' => 'sante.png'],
        ];

        $Cat = $categories[self::$counter];
        self::$counter++;
        return [
            'label' => $Cat['label'],
            'glyph' => $Cat['glyph']
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Category $category) {})
        ;
    }

    protected static function getClass(): string
    {
        return Category::class;
    }
}
