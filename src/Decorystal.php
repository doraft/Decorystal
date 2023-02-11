<?php

declare(strict_types=1);

namespace DOHWI\Decorystal;

use DOHWI\Decorystal\Command\SpawnDecorystalCommand;
use DOHWI\Decorystal\Entity\DecorystalEntity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\world\World;
use Symfony\Component\Filesystem\Path;

final class Decorystal extends PluginBase
{
    public static Config $config;
    public static string $prefix;

    protected function onEnable(): void
    {
        $this->initConfig();
        $this->getServer()->getCommandMap()->register($this->getName(), new SpawnDecorystalCommand());
        EntityFactory::getInstance()->register(DecorystalEntity::class, static function(World $world, CompoundTag $nbt): DecorystalEntity
        {
            return new DecorystalEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["DecorystalEntity"]);
    }

    private function initConfig(): void
    {
        $this->saveDefaultConfig();
        $lang = $this->getConfig()->get("language");
        $this->saveResource("$lang.json");
        $file = Path::join($this->getDataFolder(), "$lang.json");
        self::$config = new Config($file, Config::JSON);
        self::$prefix = self::$config->get("MESSAGE_PREFIX");
    }
}