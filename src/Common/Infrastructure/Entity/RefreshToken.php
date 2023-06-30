<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

/**
 * @ORM\Entity
 * @ORM\Table("refresh_token")
 */
class RefreshToken extends BaseRefreshToken
{
}
