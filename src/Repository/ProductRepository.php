<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     * @param $value
     * @return Product[]
     */
    public function findByNameAndShop($value, $id):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM product
                INNER JOIN category c on product.category_id = c.id
                WHERE shop_id = :id
                AND name LIKE :value';

        $statement = $conn->prepare($sql);
        $statement->execute([
            'id' => $id,
            'value' => '%'.$value.'%'
        ]);

        return $statement->fetchAllAssociative();
    }

}
