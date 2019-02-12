<?php

/**
 * @name AvasProtectedItemFrame
 * @version 1.0
 * @main Avas\ProtectedItemFrame
 * @api 3.6.1
 * @author AvasKR
 */
 
namespace Avas;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\tile\ItemFrame;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;

class ProtectedItemFrame extends PluginBase implements Listener{
   public $packetId = 0x47;
   
   public function onEnable(){
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
   }
   
   public function onPacket(DataPacketReceiveEvent $ev){
      $pack = $ev->getPacket();
      if($pack->pid() == $this->packetId){
         $player = $ev->getPlayer();
         $pos = new Position($pack->x, $pack->y, $pack->z, $player->getLevel());
         $tile = $player->getLevel()->getTile($pos);
         if($tile instanceof ItemFrame){
            if(!$player->isOp()){
               $player->sendMessage("§c• 액자를 터치하거나 부술 수 없습니다.");
               $ev->setCancelled();
            }
         }
      }
   }
   public function onTouch(PlayerInteractEvent $ev){
      $player = $ev->getPlayer();
      $action = $ev->getAction();
      if($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
         $block = $ev->getBlock();
         if($block->getId() === Block::ITEM_FRAME_BLOCK){
            if(!$player->isOp())
               $ev->setCancelled();
         }
      }
   }
   public function onBreak(BlockBreakEvent $ev){
      $block = $ev->getBlock();
      $player = $ev->getPlayer();
		if($block->getId() == Block::ITEM_FRAME_BLOCK){
			if($player->isOp()){
            $tile = $player->getLevel()->getTile($block);
            $tile->setItem(null);
            $player->sendMessage("§b• 액자 아이템을 제거하셨습니다.");
			} else {
            $ev->setCancelled();
            $player->sendMessage("§c• 당신은 액자를 부술 권한이 없습니다.");
			}
		}
   }
}
