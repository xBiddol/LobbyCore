<?php

namespace xBiddol;

use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\utils\Terminal;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerRespawnEvent; 
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\entity\Snowball;
use pocketmine\entity\Egg;
use pocketmine\level\Explosion;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\entity\EffectInstance;;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\entity\Item as ItemE;
use pocketmine\math\Vector3;
use pocketmine\math\Vector2;
use pocketmine\scheduler\Task as PluginTask;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\entity\object\ItemEntity;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;

use pocketmine\level\particle\AngryVillagerParticle;
use pocketmine\level\particle\BlockForceFieldParticle;//
use pocketmine\level\particle\BubbleParticle;//
use pocketmine\level\particle\CriticalParticle;//
use pocketmine\level\particle\DestroyBlockParticle;//
use pocketmine\level\particle\DustParticle;//
use pocketmine\level\particle\EnchantParticle;//
use pocketmine\level\particle\EnchantmentTableParticle;//
use pocketmine\level\particle\EntityFlameParticle;//
use pocketmine\level\particle\ExplodeParticle;//
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\FloatingTextParticle;//
use pocketmine\level\particle\GenericParticle;//
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\level\particle\HugeExplodeSeedParticle;//
use pocketmine\level\particle\InkParticle;//
use pocketmine\level\particle\InstantEnchantParticle;
use pocketmine\level\particle\ItemBreakParticle;//-//
use pocketmine\level\particle\LavaDripParticle;
use pocketmine\level\particle\LavaParticle;//
use pocketmine\level\particle\MobSpawnParticle;
use pocketmine\level\particle\Particle;//
use pocketmine\level\particle\PortalParticle;//
use pocketmine\level\particle\RainSplashParticle;
use pocketmine\level\particle\RedstoneParticle;//
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\particle\SnowballPoofParticle;
use pocketmine\level\particle\SplashParticle;//
use pocketmine\level\particle\SporeParticle;//
use pocketmine\level\particle\TerrainParticle;//
use pocketmine\level\particle\WaterDripParticle;
use pocketmine\level\particle\WaterParticle;//

/* Will be used later on
use pocketmine\level\sound\PopSound;
use pocketmine\level\sound\GhastSound;
use pocketmine\level\sound\BlazeShootSound;
*/

use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class Cooldown extends PluginTask{

  //  public $cooldown = [ ];
  //  public $cooldownTime = [ ];


    public function __construct($plugin) {
        $this->plugin = $plugin;
    }
  
    public function onRun($tick) {
        foreach($this->plugin->tntCooldown as $player) {
	    if($this->plugin->tntCooldownTime[$player] <= 0) {
	        unset($this->plugin->tntCooldown[$player]);
	        unset($this->plugin->tntCooldownTime[$player]);
        }
        else {
	        $this->plugin->tntCooldownTime[$player]--;
	    }
        }  
        foreach($this->plugin->lsCooldown as $player) {     
        if($this->plugin->lsCooldownTime[$player] <= 0) {
            unset($this->plugin->lsCooldown[$player]);
            unset($this->plugin->lsCooldownTime[$player]);
        }
        else {
            $this->plugin->lsCooldownTime[$player]--;
        }
        }
        foreach($this->plugin->sbCooldown as $player) {     
        if($this->plugin->sbCooldownTime[$player] <= 0) {
            unset($this->plugin->sbCooldown[$player]);
            unset($this->plugin->sbCooldownTime[$player]);
        }
        else {
            $this->plugin->sbCooldownTime[$player]--;
        }
        }
    }
}
