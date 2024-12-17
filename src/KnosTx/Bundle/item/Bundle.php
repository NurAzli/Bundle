<?php

declare(strict_types=1);

namespace KnosTx\Bundle\item;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\SimpleInventory;
use pocketmine\nbt\tag\CompoundTag;

public const BUNDLE = 99999;

class Bundle extends Item implements InventoryHolder{
    private const MAX_CAPACITY = 64;
    private SimpleInventory $inventory;

    public function __construct(){
        parent::__construct(new ItemIdentifier(self::BUNDLE, 0));
        $this->inventory = new SimpleInventory(1);
    }

    public function getInventory(): SimpleInventory{
        return $this->inventory;
    }

    public function addItem(Item $item): bool{
        $totalWeight = 0;
        foreach($this->inventory->getContents() as $content){
            $totalWeight += $this->getItemSize($content);
        }
        $totalWeight += $this->getItemSize($item);

        if($totalWeight > self::MAX_CAPACITY){
            return false;
        }

        $this->inventory->addItem($item);
        return true;
    }

    private function getItemSize(Item $item): int{
        return $item->getMaxStackSize() === 1 ? self::MAX_CAPACITY : (int) ceil(self::MAX_CAPACITY / $item->getMaxStackSize());
    }

    public function writeSaveData(CompoundTag $nbt): void{
        parent::writeSaveData($nbt);
        $items = [];
        foreach($this->inventory->getContents() as $item){
            $items[] = $item->nbtSerialize();
        }
        $nbt->setTag("BundleItems", CompoundTag::createFromArray($items));
    }

    public function readSaveData(CompoundTag $nbt): void{
        parent::readSaveData($nbt);
        if($nbt->hasTag("BundleItems")){
            foreach($nbt->getListTag("BundleItems")->getValue() as $itemTag){
                $this->inventory->addItem(Item::nbtDeserialize($itemTag));
            }
        }
    }
}
