<?php

namespace mohagames\PlotArea\listener;

use mohagames\PlotArea\Main;
use mohagames\PlotArea\tasks\blockMovementTask;
use mohagames\PlotArea\utils\PermissionManager;
use mohagames\PlotArea\utils\Plot;
use mohagames\PlotArea\utils\PublicChest;
use pocketmine\block\Chest;
use pocketmine\block\Door;
use pocketmine\block\FenceGate;
use pocketmine\block\ItemFrame;
use pocketmine\block\Trapdoor;
use pocketmine\entity\object\ArmorStand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEntityEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;

class EventListener implements Listener
{

    private $main;
    public $cancelMovement;

    public function __construct()
    {
        $this->main = Main::getInstance();
        Main::getInstance()->getServer()->getPluginManager()->registerEvents($this, Main::getInstance());

    }

    public function chestInteraction(PlayerInteractEvent $e)
    {
        $block = $e->getBlock();
        $player = $e->getPlayer();
        if ($block instanceof Chest) {
            $plot = Plot::get($e->getBlock());
            if ($plot !== null) {
                if ($plot->hasPermission($player->getName(), PermissionManager::PLOT_INTERACT_CHESTS) || $player->hasPermission("pa.staff.interactbypass") || PublicChest::getChest($block) !== null) {
                    if (($plot->isMember($player->getName()) || $plot->isOwner($player->getName())) && PublicChest::getChest($block) !== null) {
                        $player->sendMessage("§cOpgepast! §4U opent een openbare chest.");
                    }
                    return;
                } else {
                    $player->sendPopup("§4U kan deze actie niet uitvoeren.");
                    $e->setCancelled();
                }
            }
        }
    }

    public function trapdoorInteraction(PlayerInteractEvent $e)
    {
        $block = $e->getBlock();
        if ($block instanceof Trapdoor) {
            $plot = Plot::get($block);
            if ($plot !== null) {
                $player = $e->getPlayer();

                if ($plot->getOwner() == strtolower($player->getName()) || $plot->hasPermission($player->getName(), PermissionManager::PLOT_INTERACT_TRAPDOORS) || $player->hasPermission("pa.staff.interactbypass")) {
                    return;
                }
                $player->sendPopup("§4U kan deze actie niet uitvoeren.");
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new blockMovementTask($this, $player), 10);
                $this->cancelMovement[$e->getPlayer()->getName()] = true;
                $e->setCancelled();
            }
        }
    }

    public function doorInteraction(PlayerInteractEvent $e)
    {
        $block = $e->getBlock();
        $player = $e->getPlayer();
        if ($block instanceof Door) {
            $plot = Plot::get($block);
            if ($plot !== null) {
                if ($plot->getOwner() == strtolower($player->getName()) || $plot->hasPermission($player->getName(), PermissionManager::PLOT_INTERACT_DOORS) || $player->hasPermission("pa.staff.interactbypass")) {
                    return;
                }
                $player->sendPopup("§4U kan deze actie niet uitvoeren.");
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new blockMovementTask($this, $player), 10);
                $this->cancelMovement[$e->getPlayer()->getName()] = true;
                $e->setCancelled();
            }
        }
    }

    public function gateInteraction(PlayerInteractEvent $e)
    {
        $block = $e->getBlock();
        $player = $e->getPlayer();
        if ($block instanceof FenceGate) {
            $plot = Plot::get($block);
            if ($plot !== null) {
                if ($plot->getOwner() == strtolower($player->getName()) || $plot->hasPermission($player->getName(), PermissionManager::PLOT_INTERACT_GATES) || $player->hasPermission("pa.staff.interactbypass")) {
                    return;
                }
                $player->sendPopup("§4U kan deze actie niet uitvoeren.");
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new blockMovementTask($this, $player), 10);
                $this->cancelMovement[$e->getPlayer()->getName()] = true;
                $e->setCancelled();
            }
        }
    }

    public function ItemFrameInteraction(PlayerInteractEvent $e)
    {
        $block = $e->getBlock();
        if ($block instanceof ItemFrame) {
            $plot = Plot::get($block);
            if ($plot !== null) {
                if ($e->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    if ($plot->getOwner() == strtolower($e->getPlayer()->getName()) || $plot->hasPermission($e->getPlayer()->getName(), PermissionManager::PLOT_INTERACT_ITEMFRAMES) || $e->getPlayer()->hasPermission("pa.staff.interactbypass")) {
                        return;
                    }
                    $e->getPlayer()->sendPopup("§4U kan deze actie niet uitvoeren.");
                    $e->setCancelled();
                } elseif ($e->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK && !$e->getPlayer()->hasPermission("pa.staff.interactbypass")) {
                    $e->getPlayer()->sendPopup("§4U kan deze actie niet uitvoeren.");
                    $e->setCancelled();
                }
            }
        }
    }


    public function blockMovement(PlayerMoveEvent $e)
    {
        if (isset($this->cancelMovement[$e->getPlayer()->getName()])) {
            $e->getPlayer()->teleport($e->getFrom());
        }
    }
}