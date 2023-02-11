<?php

declare(strict_types=1);

namespace DOHWI\Decorystal\Entity;

use DOHWI\Decorystal\Decorystal;
use pocketmine\color\Color;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\world\particle\DustParticle;
use function cos;
use function deg2rad;
use function microtime;
use function sin;

final class DecorystalEntity extends Entity
{
    private static string $removeMessage;
    private static Color $color;
    private static int $dustCircleRadius;
    private static float $plusCount;
    private float $coolTime = 0;

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
        self::$removeMessage = Decorystal::$prefix.Decorystal::$config->getNested("MESSAGES.REMOVE_DECORYSTAL");
        [$red, $green, $blue] = Decorystal::$config->get("DUST_COLOR");
        self::$color = new Color($red, $green, $blue);
        self::$plusCount = 360 / Decorystal::$config->get("DUST_COUNT");
        self::$dustCircleRadius = Decorystal::$config->get("DUST_CIRCLE_RADIUS");
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::ENDER_CRYSTAL;
    }

    public function attack(EntityDamageEvent $source): void
    {
        if(!$source instanceof EntityDamageByEntityEvent) return;
        $damager = $source->getDamager();
        if(!$damager instanceof Player) return;
        if($this->server->isOp($damager->getName()) && $damager->isSneaking()) {
            $this->close();
            $damager->sendMessage(self::$removeMessage);
        }
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.98, 0.98);
    }

    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        $now = microtime(true);
        if($this->coolTime < $now) {
            $position = $this->getPosition();
            $world = $position->getWorld();
            for($i = 0; $i < 360; $i += self::$plusCount) {
                $x = sin(deg2rad($i)) * self::$dustCircleRadius;
                $z = cos(deg2rad($i)) * self::$dustCircleRadius;
                $world->addParticle($position->add($x, 0, $z), new DustParticle(self::$color));
            }
            $this->coolTime = $now + 0.5;
        }
        return parent::entityBaseTick($tickDiff);
    }
}