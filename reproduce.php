<?php

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Webmozart\Assert\Assert;

include __DIR__ .'/vendor/autoload.php';

class Inner {
    public $freshInstance = true;
    public $name;
}

class Outer {
    /** @var Inner */
    public $inner;
}

$inner = new Inner;

// this is not a fresh instance, we've pre-created it
$inner->freshInstance = false;

$outer = new Outer;
$outer->inner = $inner;

$serializer = new Serializer([new ObjectNormalizer(null, null, null, new PhpDocExtractor())], [new JsonEncoder()]);
$outer = $serializer->deserialize('{"inner": {"name": "Inner Name"}}', Outer::class, 'json', [
    ObjectNormalizer::OBJECT_TO_POPULATE => $outer
]);

Assert::eq($outer->inner->name, 'Inner Name');

// this fails
Assert::false($outer->inner->freshInstance);
