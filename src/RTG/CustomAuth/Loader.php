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
    public $cids;
    public $auth;
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        
        $this->cfg = new Config($this->getDataFolder() . "config.yml");
        $this->bips = new Config($this->getDataFolder() . "bannedips.txt");
        $this->cids = new Config($this->getDataFolder() . "bannedcids.txt");
    }
    
    public function isAuthenticated(Player $p) {
        if(isset($this->auth[strtolower($p->getName())])) {
            $p->sendMessage("You have been authenticated!");
        }
        else {
            $p->sendMessage("You are not authenticated! Please login using /l [password]");
        }
    }
    
    public function addCID(Player $p, $sender) {
        
        $n = $p->getName();
        $cid = $p->getClientId();
        $this->cids = new Config($this->getDataFolder() . "bannedcids.txt");
        
            if($this->cids->get($cid) === true) {
                $sender->sendMessage("$n is already CID Banned!");
            }
            else {
                $this->cids->set($cid);
                $this->cids->save();
                $sender->sendMessage("You have CID Banned $n!");
            }
        
    }
    
    public function checkCID(Player $p, $event) {
        
        $cid = $p->getClientId();
        $this->cids = new Config($this->getDataFolder() . "bannedcids.txt");
        
            if($this->cids->get($cid) === true) {
               $event->setCancelled(); 
            }
        
    }
    
    public function checkIP(Player $p, $event) {
        
        $ip = $p->getAddress();
        $this->bips = new Config($this->getDataFolder() . "bannedips.txt");
        
            if($this->bips->get($ip) === true) {
                $event->setCancelled();
            }
        
    }
    
    public function onJoin(\pocketmine\event\player\PlayerPreLoginEvent $e) {
        
        $p = $e->getPlayer();
        $n = $p->getName();
            
            $this->checkCID($p, $e);
            $this->checkIP($p, $e);
        
    }
    
    
    public function onDisable() {
        $this->cids->save();
        $this->bips->save();
        $this->cfg->save();
        $this->getLogger()->warning("Saved all Configs and settings!");
    }
    
}