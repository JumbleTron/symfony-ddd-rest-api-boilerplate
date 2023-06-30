<?php

declare(strict_types=1);

namespace App\Common\Infrastructure;

use DateTimeInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Serializer;

final class SerializerFactory
{
    public function __invoke(): Serializer
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new UidNormalizer(),
            new ArrayDenormalizer(),
            new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM]),
            new PropertyNormalizer(
                new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())),
                new CamelCaseToSnakeCaseNameConverter(),
                new PhpDocExtractor(),
                null,
                null,
                [
                    AbstractObjectNormalizer::SKIP_UNINITIALIZED_VALUES => true,
                    AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
                ]
            ),
        ];

        return new Serializer($normalizers, $encoders);
    }
}
