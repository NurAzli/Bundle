<?php

declare(strict_types=1);

namespace KnosTx\Bundle\item;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\SimpleInventory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

class Bundle extends Item implements InventoryHolder{
    private const MAX_CAPACITY = 64;
    private SimpleInventory $inventory;

    public function __construct(){
        parent::__construct(VanillaItems::BUNDLE()->getId(), 0, "Bundle");
        $this->inventory = new SimpleInventory(1); // Bundle memiliki 1 slot inventory internal
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
            return false; // Kapasitas penuh
        }

        $this->inventory->addItem($item);
        return true;
    }

    private function getItemSize(Item $item): int{
        return $item->getMaxStackSize() === 1 ? self::MAX_CAPACITY : (int) ceil(self::MAX_CAPACITY / $item->getMaxStackSize());
    }

    public function onSaveNBT(): CompoundTag{
        $nbt = new CompoundTag();
        $items = [];
        foreach ($this->inventory->getContents() as $item) {
            $items[] = $item->nbtSerialize();
        }
        $nbt->setTag("BundleItems", new ListTag($items));
        return $nbt;
    }

    public function onLoadNBT(CompoundTag $nbt): void{
        if ($nbt->getTag("BundleItems") !== null) {
            foreach ($nbt->getListTag("BundleItems")->getValue() as $itemTag) {
                $this->inventory->addItem(Item::nbtDeserialize($itemTag));
            }
        }
    }
}
