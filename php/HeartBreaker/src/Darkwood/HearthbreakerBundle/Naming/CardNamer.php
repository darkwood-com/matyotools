<?php

namespace Darkwood\HearthbreakerBundle\Naming;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

/**
 * CardNamer.
 */
class CardNamer implements NamerInterface
{
    /**
     * {@inheritDoc}
     */
    public function name($object, PropertyMapping $mapping)
    {
        $file = $mapping->getFile($object);

        /* @var $file UploadedFile */
        $name = $file->getClientOriginalName();

        if ($object instanceof Card) {
            $name = $object->getSource().'-'.$object->getSlug();
        }

        if ($extension = $this->getExtension($file)) {
            $name = sprintf('%s.%s', $name, $extension);
        }

        return $name;
    }

    protected function getExtension(UploadedFile $file)
    {
        $originalName = $file->getClientOriginalName();

        if ($extension = pathinfo($originalName, PATHINFO_EXTENSION)) {
            return $extension;
        }

        if ($extension = $file->guessExtension()) {
            return $extension;
        }

        return;
    }
}
