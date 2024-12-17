<?php

namespace KnosTx\Bundle;

use pocketmine\item\VanillaItems;

class VanillaItemsCustom extends VanillaItems{
    public static function registerCustom(){
        parent::register();
    }
}
