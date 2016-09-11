<?php
namespace NawafPluginFrz;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;

class Freeze extends PluginBase implements Listener{
    
    public $frozens = [];
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        $cmd = strtolower($command->getName());
        if($cmd === "freeze" || $cmd === "unfreeze") {
            if(count($args) < 1) {
                    $sender->sendMessage("Usage: /freeze <player|@all>");
                    return true;
            }
            $target = $this->getTargets($args[0]);
            if(!$target) {
                $sender->sendMessage("Target {$args[0]} was not found.");
                return true;
            } elseif (empty($target)) {
                $sender->sendMessage("No players currently online.");
                return true;
            }
            switch($cmd)
            {
                case "freeze":
                    $this->freeze($target);
                    $sender->sendMessage((is_array($target) ? "Everyone" : $target->getName())." is now frozen.");
                    break;
                case "unfreeze":
                    $this->freeze($target);
                    $sender->sendMessage((is_array($target) ? "Everyone" : $target->getName())." is now unfrozen.");
                    break;
            }
        }
        return true;
    }
    
    /**
     * @param string $identifier player name or @all
     * @return Player|Player[]|null
     */
    public function getTargets(string $identifier) {
        if(strtolower($identifier) === "@all") return $this->getServer()->getOnlinePlayers();
        return $this->getServer()->getPlayer($identifier);
    }
    
    public function freeze($target) {
        if(!is_array($target)) $target = [$target];
        foreach($target as $t) {
            if($t instanceof Player) 
            {
                $this->frozens[$t->getName()] = true;
            }
        }
    }
    
    public function unfreeze($target) {
        if(!is_array($target)) $target = [$target];
        foreach($target as $t) {
            if($t instanceof Player) 
            {
                unset($this->frozens[$t->getName()]);
            }
        }
    }
    
    public function onMove(PlayerMoveEvent $ev){
        if(isset($this->frozens[$ev->getPlayer()->getName()])){
            $ev->setCancelled(true);
        }
    }
}
