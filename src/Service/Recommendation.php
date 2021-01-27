<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class Recommendation {

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function mapLists(User $user)
    {
        // Recuperer les evaluations de l'utilisateur et les produits correspondants
        $u = $user->getRatings();

        $p = array_map(function ($rateObject) {
            return $rateObject['product_id'];
        }, $u);
        $r = array_map(function ($rateObject) {
            return $rateObject['rate'];
        }, $u);

        return array_combine($p, $r);
    }

    public function pearsonSim(User $user1, User $user2)
    {
        /**
         * Le coefficient de correlation (Pearson) c'est la covariance de deux variables
         * divisee par le produit de leur deviation standard.
         * Utile pour se donner une idee de la similitude de deux jeux de donnes
         */
        $assoc1 = $this->mapLists($user1);
        $assoc2 = $this->mapLists($user2);

        $p1 = array_keys($assoc1);
        $p2 = array_keys($assoc2);

        // Creer une liste avec les produits mutuellement notes par les 2 utilisateurs
        $similar = [];
        foreach ($p1 as $product_id) {
            if (in_array($product_id, $p2)) {
                array_push($similar, $product_id);
            }
        }

        // Calculer le total d'elements communs
        // (s'il est nul alors on arrete le programme)
        $n = count($similar);
        if ($n == 0) {
            return 0;
        }

        $s1 = 0;
        $s2 = 0;
        $s1squared = 0;
        $s2squared = 0;
        $pSum = 0;
        foreach ($similar as $item) {
            // Somme des notes donnes
            $s1 += $assoc1[$item];
            $s2 += $assoc2[$item];

            // Somme des carres des notes donnees
            $s1squared += pow($assoc1[$item], 2);
            $s2squared += pow($assoc2[$item], 2);

            // Somme des produits des deux jeux
            $pSum += $assoc1[$item] * $assoc2[$item];
        }

        // 1.) Calcul du numerateur de l'equation
        $numerator = $pSum - ($s1 * $s2 / $n);

        // 2.) Calcul du denominateur de l'equation
        $d1 = $s1squared - pow($s1, 2) / $n;
        $d2 = $s2squared - pow($s2, 2) / $n;
        $denominator = sqrt($d1 * $d2);

        if ($denominator == 0) {
            return 0;
        }

        // On retourne le coefficient de correlation de Pearson
        return $numerator / $denominator;
    }

    public function getRecommendations(User $user)
    {
        $allUsers = $this->userRepository->findAll();
        $users = [];

        // Exclure les utlisateurs qui n'ont pas encore donne d'avis
        for ($i = 0; $i < count($allUsers); $i++) {
            if (!empty($allUsers[$i]->getRatings())) {
                array_push($users, $allUsers[$i]);
            }
        }

        $totals = [];
        $simSums = [];
        foreach ($users as $other) {
            // Ne pas comparer avec l'utilsateur en session (lui meme)
            if ($other === $user) {
                continue;
            }

            $coeff = $this->pearsonSim($user, $other);

            // Coefficient egal ou inferieur a zero est ignore
            if ($coeff <= 0) {
                continue;
            }

            $ratings = $this->mapLists($other);
            $currentUserRatings = $this->mapLists($user);

            foreach ($ratings as $product => $rate) {
                if(!key_exists($product, $currentUserRatings)) {
                    $totals += array($product => $rate * $coeff);
                    $simSums += array($product => 0);
                    $simSums[$product] += $coeff;
                }
            }
        }

        // Creer la liste avec les notes normalisees
        $rankings = [];
        foreach ($totals as $productId => $pRate) {
            $rankings += array($productId => $pRate / $simSums[$productId]);
        }
        arsort($rankings);
        return $rankings;
    }

}