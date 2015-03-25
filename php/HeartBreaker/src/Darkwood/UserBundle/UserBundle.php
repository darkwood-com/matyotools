<?php

namespace Darkwood\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class UserBundle
 *
 * @package Darkwood\UserBundle
 */
class UserBundle extends Bundle
{
    /**
     * Get parent bundle
     *
     * @return string
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
