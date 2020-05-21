<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovementSetActivityRepository")
 */
class MovementSetActivity extends AbstractActivity
{

}
