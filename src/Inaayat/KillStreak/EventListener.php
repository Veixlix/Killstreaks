<?php

namespace Inaayat\KillStreak;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\Server;

class EventListener implements Listener {

    public $plugin;

    public function __construct(KillStreak $plugin){
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        if(!$this->plugin->getProvider()->playerExists($player)){
            $this->plugin->getProvider()->registerPlayer($player);
        }
    }

    public function onPlayerKill(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        if($player instanceof Player){
            $this->plugin->getProvider()->resetKSPoints($player);
        }
        $cause = $player->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent){
            $d = $cause->getDamager();
            if($d instanceof Player){
                $this->plugin->getProvider()->addKSPoints($d, (int)"1");
                $ks = $this->plugin->getProvider()->getPlayerKSPoints($d);
                $announcment = KillStreak::getInstance()->getAnnouncmentSettings();
                /** @var array $ksWhereItSendsMessage */
				$ksWhereItSendsMessage = $announcment->get("killstreaks-where-it-sends-message");
				$message = $announcment->get("message");
				$name = $d->getName();
				$messageEnhanced = str_replace(["{player}", "{killstreak}"], [$name, $ks], $message);
				if(in_array($ks, $ksWhereItSendsMessage))
					Server::getInstance()->broadcastMessage($messageEnhanced);
            }
        }
    }
}
