<?php

declare(strict_types=1);

namespace DOHWI\Decorystal\Command;

use DOHWI\Decorystal\Decorystal;
use DOHWI\Decorystal\Entity\DecorystalEntity;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

final class SpawnDecorystalCommand extends Command
{
    private static string $spawnMessage;

    public function __construct()
    {
        $name = Decorystal::$config->getNested("COMMANDS.SPAWN_DECORYSTAL.NAME");
        $description = Decorystal::$config->getNested("COMMANDS.SPAWN_DECORYSTAL.DESCRIPTION");
        self::$spawnMessage = Decorystal::$prefix.Decorystal::$config->getNested("MESSAGES.SPAWN_DECORYSTAL");
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
        parent::__construct($name, $description);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;
        $decorystal = new DecorystalEntity($sender->getLocation());
        $decorystal->spawnToAll();
        $sender->sendMessage(self::$spawnMessage);
    }
}