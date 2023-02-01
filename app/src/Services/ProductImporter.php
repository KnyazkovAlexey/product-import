<?php

namespace App\Services;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductImporter
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $url
     * @return int[]
     */
    public function import(string $url): array
    {
        $filePath = $this->downLoadFile($url);
        $products = $this->parseFile($filePath);

        $added = $updated = 0;
        foreach ($products as $product) {
            $productId = (int)$product->product_id;

            //todo: optimization (getting product_ids at the beginning)
            $entity = $this->entityManager->getRepository(Product::class)->findOneByProductId($productId);
            if ($isNew = $entity === null) {
                $entity = new Product();
            }

            $entity->setProductId($productId);
            $entity->setTitle((string)$product->title);
            $entity->setDescription((string)$product->description);
            $entity->setRating((int)$product->rating);
            $entity->setPrice((float)$product->price);
            $entity->setInetPrice((float)$product->price);
            $entity->setImage((string)$product->image);

            $this->entityManager->persist($entity);

            $isNew ? $added++ : $updated++;
        }

        $this->entityManager->flush();

        //todo: ProductImporterResult class
        return [
            'added' => $added,
            'updated' => $updated,
        ];
    }

    protected function downLoadFile(string $url): string
    {
        //todo: file downLoading service
        //todo: file validation

        $filePath = $this->generateFilePath();
        file_put_contents($filePath, fopen($url, 'r'));

        return $filePath;
    }

    protected function parseFile(string $filePath): mixed
    {
        //todo: parsing service
        //todo: reading big files piece by piece

        $xml = simplexml_load_file($filePath);
        $products = $xml->products->product;

        return $products;
    }

    protected function generateFilePath(): string
    {
        $fileName = date('Y-m-d H:i:s').'.xml';

        return $this->getStoragePath().DIRECTORY_SEPARATOR.$fileName;
    }

    protected function getStoragePath(): string
    {
        //todo: config or alias to storage dir

        return dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'storage/import-products';
    }
}