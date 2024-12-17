<?php

namespace KnosTx\Bundle;

use pocketmine\item\VanillaItems;

final class VanillaItemsCustom extends VanillaItems{
    public static function registerCustom(){
        parent::register();
    }
}
