<?php

namespace App\Services;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Iterator;
use SimpleXMLElement;

/**
 * Class for import product entities.
 */
class ProductImporter
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Import product entities from the external XML-file to DB.
     *
     * @param string $url XML-file address.
     * @return int[] ['added' => 100, 'updated' => 200]; //todo: ProductImporterResult class
     */
    public function importFromExternalXml(string $url): array
    {
        //todo: error handling
        $filePath = $this->downLoadFile($url);
        $products = $this->parseXmlFile($filePath);

        $added = $updated = 0;
        foreach ($products as $product) {
            /** @var SimpleXMLElement $product */
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

        return [
            'added' => $added,
            'updated' => $updated,
        ];
    }

    /**
     * Download the file and save it to the internal storage.
     *
     * @param string $url File address.
     * @return string Absolute path to the downloaded file.
     */
    protected function downLoadFile(string $url): string
    {
        //todo: file downLoading service
        //todo: file validation

        $filePath = $this->getStorageFolderPath().DIRECTORY_SEPARATOR.$this->generateFileName();
        file_put_contents($filePath, fopen($url, 'r'));

        return $filePath;
    }

    /**
     * Parse the imported file with products.
     * 
     * @param string $filePath Absolute path to the XML-file.
     * @return SimpleXMLElement|Iterator Products list.
     */
    protected function parseXmlFile(string $filePath): Iterator
    {
        //todo: parsing service
        //todo: reading big files piece by piece

        $xml = simplexml_load_file($filePath);

        return $xml->products->product;
    }

    /**
     * Name for uploaded file saving.
     *
     * @return string
     */
    protected function generateFileName(): string
    {
        return date('Y-m-d H:i:s').'.xml';
    }

    /**
     * Absolute path to folder with downloaded files.
     *
     * @return string
     */
    protected function getStorageFolderPath(): string
    {
        //todo: config or alias for the storage folder

        return dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'storage/import-products';
    }
}