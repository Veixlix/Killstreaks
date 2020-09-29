<?php

namespace Inaayat\KillStreak;

use Inaayat\KillStreak\provider\ProviderInterface;
use Inaayat\KillStreak\provider\SQLiteProvider;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class KillStreak extends PluginBase{

    private static $instance;
    private $provider;
    /** @var Config */
    private $announcment;

    public static function getInstance(): KillStreak{
        return self::$instance;
    }

    public function onLoad(): void{
        self::$instance = $this;
    }

    public function onEnable(): void{
        $provider = new SQLiteProvider();
        if($provider instanceof ProviderInterface){
            $this->provider = $provider;
        }
        $this->saveDefaultConfig();
        $this->getProvider()->prepare();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        // Igancio's Announcment
		$this->announcment = new Config($this->getDataFolder()."announcment.yml", Config::YAML, [
			"killstreaks-where-it-sends-message" => [
				5,
				10,
				15,
				20,
				25,
				30
			],
			"message" => "Player {player} has reached {killstreak} killstreak!"
		]);
		$this->announcment->save();
    }

    public function getProvider(): ProviderInterface {
        return $this->provider;
    }
    
    public function onDisable(){
        $this->getProvider()->close();
    }

    public function getAnnouncmentSettings() : Config{
    	return $this->announcment;
	}
}
