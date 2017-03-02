<?php

/* 
 * Copyright (C) 2017 RTGDaCoder
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace RTG\CustomAuth;

/* Essentials */
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\Config;

class Loader extends PluginBase implements Listener {
    
    public $cfg;
    public $bips;
    public $auth;
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->saveResource("bannedips.txt");
        
        $this->cfg = new Config($this->getDataFolder() . "config.yml");
        $this->bips = new Config($this->getDataFolder() . "bannedips.txt");
    }
    
    public function isAuthenticated(Player $p) {
        if(isset($this->auth[strtolower($p->getName())])) {
            $p->sendMessage("You have been authenticated!");
        }
        else {
            $p->sendMessage("You are not authenticated! Please login using /l [password]");
        }
    }
    
    public function onJoin(\pocketmine\event\player\PlayerPreLoginEvent $e) {
        
        $p = $e->getPlayer();
        $n = $p->getName();
        $cid = $p->getClientId();
        $ip = $p->getAddress();
        
        $this->bips = new Config($this->getDataFolder() . "bannedips.txt");
        $this->cfg = new Config($this->getDataFolder() . "config.yml");
        
            if(!in_array($ip, $this->bips)) {
                if($this->cfg->get("ipcheck")["enable"] === true) {
                    $this->getLogger()->info("$n passed the IP Check!");
                }
            }
            else {
                $e->setCancelled();
            }
        
    }
    
    
    public function onDisable() {
        parent::onDisable();
    }
    
}