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
use pocketmine\block\IronDoor;
use pocketmine\block\ItemFrame;
use pocketmine\block\Trapdoor;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerInteractEntityEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\entity\object\ArmorStand;

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
            $plot = $this->main->getPlot($e->getBlock());
            if ($plot !== null) {
                if ($plot->isGrouped()) {
                    $plot = $plot->getGroup()->getMasterPlot();
                }
                if ($plot->getOwner() == strtolower($player->getName()) || $plot->hasPermission($player->getName(), PermissionManager::PLOT_INTERACT_CHESTS) || $player->hasPermission("pa.staff") || PublicChest::getChest($block) !== null) {
                    if (($plot->isMember($player->getName()) || $plot->isOwner($player->getName())) && PublicChest::getChest($block) !== null) {
                        $player->sendMessage("§cOpgeast! §4U opent een openbare chest.");
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
            $plot = $this->main->getPlot($block);
            if ($plot !== null) {
                if ($plot->isGrouped()) {
                    $plot = $plot->getGroup()->getMasterPlot();
                }
                $player = $e->getPlayer();

                if ($plot->getOwner() == strtolower($player->getName()) || $plot->hasPermission($player->getName(), PermissionManager::PLOT_INTERACT_TRAPDOORS) || $player->hasPermission("pa.staff")) {
                    return;
                } else {
                    $player->sendPopup("§4U kan deze actie niet uitvoeren.");
                    Main::getInstance()->getScheduler()->scheduleDelayedTask(new blockMovementTask($this, $player), 10);
                    $this->cancelMovement[$e->getPlayer()->getName()] = true;
                    $e->setCancelled();
                }
            }
        }
    }

    public function doorInteraction(PlayerInteractEvent $e)
    {
        $block = $e->getBlock();
        $player = $e->getPlayer();
        if ($block instanceof Door) {
            $plot = $this->main->getPlot($e->getBlock());
            if ($plot !== null) {
                if ($plot->isGrouped()) {
                    $plot = $plot->getGroup()->getMasterPlot();
                }
                if ($plot->getOwner() == strtolower($player->getName()) || $plot->hasPermission($player->getName(), PermissionManager::PLOT_INTERACT_DOORS) || $player->hasPermission("pa.staff")) {
                    return;
                } else {
                    $player->sendPopup("§4U kan deze actie niet uitvoeren.");
                    Main::getInstance()->getScheduler()->scheduleDelayedTask(new blockMovementTask($this, $player), 10);
                    $this->cancelMovement[$e->getPlayer()->getName()] = true;
                    $e->setCancelled();
                }
            }
        }
    }

    public function gateInteraction(PlayerInteractEvent $e)
    {
        $block = $e->getBlock();
        $player = $e->getPlayer();
        if ($block instanceof FenceGate) {
            $plot = $this->main->getPlot($e->getBlock());
            if ($plot !== null) {
                if ($plot->isGrouped()) {
                    $plot = $plot->getGroup()->getMasterPlot();
                }
                if ($plot->getOwner() == strtolower($player->getName()) || $plot->hasPermission($player->getName(), PermissionManager::PLOT_INTERACT_GATES) || $player->hasPermission("pa.staff")) {
                    return;
                } else {
                    $player->sendPopup("§4U kan deze actie niet uitvoeren.");
                    Main::getInstance()->getScheduler()->scheduleDelayedTask(new blockMovementTask($this, $player), 10);
                    $this->cancelMovement[$e->getPlayer()->getName()] = true;
                    $e->setCancelled();
                }
            }
        }
    }

    public function ItemFrameInteraction(PlayerInteractEvent $e)
    {
        $block = $e->getBlock();
        if ($block instanceof ItemFrame) {
            $plot = $this->main->getPlot($block);
            if ($plot !== null) {
                if ($plot->isGrouped()) {
                    $plot = $plot->getGroup()->getMasterPlot();
                }

                if ($e->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    if ($plot->getOwner() == strtolower($e->getPlayer()->getName()) || $plot->hasPermission($e->getPlayer()->getName(), PermissionManager::PLOT_INTERACT_ITEMFRAMES) || $e->getPlayer()->hasPermission("pa.staff")) {
                        return;
                    } else {
                        $e->getPlayer()->sendPopup("§4U kan deze actie niet uitvoeren.");
                        $e->setCancelled();
                    }
                } elseif ($e->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK && !$e->getPlayer()->hasPermission("pa.staff")) {
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