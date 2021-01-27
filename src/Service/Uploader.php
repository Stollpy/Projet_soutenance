<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Shop;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Uploader
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function uploadPostImage(Product $product, ?UploadedFile $file, string $uploadPath)
    {
        if ($file) {

            // Create unique filename on upload
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFileName = $this->slugger->slug($fileName) . '-' . uniqid() . '.' . $file->guessExtension();

            // Bind to post
            $product->setPhoto($newFileName);

            // Store the file
            $file->move($uploadPath, $newFileName);
        }
    }

    public function uploadShopImage(Shop $shop, ?UploadedFile $file, string $uploadPath)
    {
        if ($file) {

            // Create unique filename on upload
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFileName = $this->slugger->slug($fileName) . '-' . uniqid() . '.' . $file->guessExtension();

            // Bind to shop
            $shop->setPicture($newFileName);

            // Store the file
            $file->move($uploadPath, $newFileName);
        }
    }
}
