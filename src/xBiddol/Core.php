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
use pocketmine\block\Lava;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\EnumTag;

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

use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;


use xBiddol\Cooldown;


class Core extends PluginBase implements Listener {
	
	/**@var Item*/
	private $item;
	/**@var int*/
	protected $damage = 0;
	
	public $inv = [];
    public $inventories;

    public $tntCooldown = [ ];
	public $tntCooldownTime = [ ];
	public $lsCooldownTime = [ ];
	public $lsCooldown = [ ];
	public $sbCooldown = [ ];
	public $sbCooldownTime = [ ];
	
	public function onEnable(){
		$this->getLogger()->info("Plugin LobbyCore by xBiddol Enable!");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getScheduler()->scheduleRepeatingTask(new Cooldown($this), 20);
	}
	
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$player->setHealth(20);
		$player->setFood(20);
		$player->setGamemode(0);
		$player->setScale(1.0);
		$player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
		$player->addTitle("Welcome {$name} Plugin By xBiddol!");
		$player->getInventory()->setItem(1, Item::get(288, 0, 1)->setCustomName("§c§lFly"));
		$player->getInventory()->setItem(3, Item::get(369, 0, 1)->setCustomName("§e§lKekuatan"));
		$player->getInventory()->setItem(5, Item::get(378, 0, 1)->setCustomName("§a§lSize"));
		$player->getInventory()->setItem(7, Item::get(399, 0, 1)->setCustomName("§d§lGamemode"));
	}
	
	public function onRespawn(PlayerRespawnEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$player->getInventory()->setItem(1, Item::get(288, 0, 1)->setCustomName("§c§lFly"));
		$player->getInventory()->setItem(3, Item::get(369, 0, 1)->setCustomName("§e§lKekuatan"));
		$player->getInventory()->setItem(5, Item::get(378, 0, 1)->setCustomName("§a§lSize"));
		$player->getInventory()->setItem(7, Item::get(399, 0, 1)->setCustomName("§d§lGamemode"));
	}
	
	public function onPlace(BlockPlaceEvent $event) {
		$player = $event->getPlayer();
		if($player->getLevel()->getFolderName() == $this->getServer()->getDefaultLevel()->getFolderName()) {
			if($player->hasPermission("core.build")) {
				if($player->getGamemode() == 2 or $player->getGamemode() == 0) {
 					$event->setCancelled();
				}
			} elseif(!$player->hasPermission("core.build")) {
 				$event->setCancelled();
			}
		}
	}
	public function onBreak(BlockBreakEvent $event) {
		$player = $event->getPlayer();
		if($player->getLevel()->getFolderName() == $this->getServer()->getDefaultLevel()->getFolderName()) {
			if($player->hasPermission("core.build")) {
				if($player->getGamemode() == 2 or $player->getGamemode() == 0) {
 					$event->setCancelled();
				}
			} elseif(!$player->hasPermission("core.build")) {
 				$event->setCancelled();
			}
		}
	}
	
	public function ExplosionPrimeEvent(ExplosionPrimeEvent $event){
		$event->setBlockBreaking(false);
	}
	
	public function onDrop(PlayerDropItemEvent $event){
		$player = $event->getPlayer();
		if($player->getLevel()->getFolderName() == $this->getServer()->getDefaultLevel()->getFolderName()) {
			$event->setCancelled();
		}
	}
    public function onPickup(InventoryPickupItemEvent $event){
		$player = $event->getInventory()->getHolder();
		if($player->getLevel()->getFolderName() == $this->getServer()->getDefaultLevel()->getFolderName()) {
			$event->setCancelled();
		}
	}
	
    public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		if ($event->getItem()->getId() == 288 and $event->getItem()->getCustomName() == "§c§lFly"){
			if ($player->hasPermission("fly.core")){
				$player->getInventory()->clearAll();
			    $player->getInventory()->setItem(0, Item::get(352, 0, 1)->setCustomName("§b§lEnable Fly"));
			    $player->getInventory()->setItem(8, Item::get(355, 0, 1)->setCustomName("§c§lKeluar"));
			} else {
				$player->sendMessage("§cYou can't own a fly without buying a Rank!");
			}
		}
		if ($event->getItem()->getId() == 352 and $event->getItem()->getCustomName() == "§b§lEnable Fly"){
			if ($player->getAllowFlight()){
				$player->sendMessage("§d[§b§lLobbyCore§r§d]§o§cFly Disabled!");
				$player->setFlying(false);
				$player->setAllowFlight(false);
			} else {
				$player->sendMessage("§d[§b§lLobbyCore§r§d]§o§aFly Enabled!");
				$player->setAllowFlight(true);
			}
		}
		if ($event->getItem()->getId() == 355 and $event->getItem()->getCustomName() == "§c§lKeluar"){
			$player->getInventory()->clearAll();
			$player->getInventory()->setItem(1, Item::get(288, 0, 1)->setCustomName("§c§lFly"));
		    $player->getInventory()->setItem(3, Item::get(369, 0, 1)->setCustomName("§e§lKekuatan"));
		    $player->getInventory()->setItem(5, Item::get(378, 0, 1)->setCustomName("§a§lSize"));
		    $player->getInventory()->setItem(7, Item::get(399, 0, 1)->setCustomName("§d§lGamemode"));
		}
		if ($event->getItem()->getId() == 369 and $event->getItem()->getCustomName() == "§e§lKekuatan"){
			if ($player->hasPermission("kekuatan.core")){
				$player->getInventory()->clearAll();
			    $player->getInventory()->setItem(0, Item::get(378, 0, 1)->setCustomName("§b§lTntLauncher"));
			    $player->getInventory()->setItem(3, Item::get(378, 0, 1)->setCustomName("§b§lLoncat"));
			    $player->getInventory()->setItem(6, Item::get(378, 0, 1)->setCustomName("§b§lSmokeBomb"));
			    $player->getInventory()->setItem(8, Item::get(355, 0, 1)->setCustomName("§c§lKeluar"));
			} else {
				$player->sendMessage("§cYou can't own a fly without buying a Rank!");
			}
		}
        if ($event->getItem()->getId() == 378 and $event->getItem()->getCustomName() == "§b§lTntLauncher"){
        	if (!isset($this->tntCooldown[$player->getName()])){
        	    $nbt = new CompoundTag("", [
                    "Pos" => new ListTag("Pos", [
                        new DoubleTag("", $player->x),
                        new DoubleTag("", $player->y + $player->getEyeHeight()),
                        new DoubleTag("", $player->z)
                    ]),
                    "Motion" => new ListTag("Motion", [
                        new DoubleTag("", -sin($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)),
                        new DoubleTag("", -sin($player->pitch / 180 * M_PI)),
                        new DoubleTag("", cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI))
                    ]),
                    "Rotation" => new ListTag("Rotation", [
                        new FloatTag("", $player->yaw),
                        new FloatTag("", $player->pitch)
                    ]),
                ]);
                $tnt = Entity::createEntity("PrimedTNT", $player->getLevel(), $nbt, null);
                $tnt->setMotion($tnt->getMotion()->multiply(2));
                $tnt->spawnTo($player);
                $this->tntCooldown[$player->getName()] = $player->getName();
                $time = "25";
                $this->tntCooldownTime[$player->getName()] = $time;

            } else {
                $player->sendPopup("§cYou can't use the TNT-Launcher for another ".$this->tntCooldownTime[$player->getName()]." seconds.");
            }
        }
        if ($event->getItem()->getId() == 378 and $event->getItem()->getCustomName() == "§b§lLoncat"){
        	if($player->hasPermission("cosmetic.gadgets.leaper")) {
				
           $yaw = $player->yaw;
           if (0 <= $yaw and $yaw < 22.5) {
			        $player->knockBack($player, 0, 0, 1, 1.5);
           } elseif (22.5 <= $yaw and $yaw < 67.5) {
                    $player->knockBack($player, 0, -1, 1, 1.5);
           } elseif (67.5 <= $yaw and $yaw < 112.5) {
                    $player->knockBack($player, 0, -1, 0, 1.5);
           } elseif (112.5 <= $yaw and $yaw < 157.5) {
                    $player->knockBack($player, 0, -1, -1, 1.5);
           } elseif (157.5 <= $yaw and $yaw < 202.5) {
                    $player->knockBack($player, 0, 0, -1, 1.5);
           } elseif (202.5 <= $yaw and $yaw < 247.5) {
                    $player->knockBack($player, 0, 1, -1, 1.5);
           } elseif (247.5 <= $yaw and $yaw < 292.5) {
                    $player->knockBack($player, 0, 1, 0, 1.5);
           } elseif (292.5 <= $yaw and $yaw < 337.5) {
                    $player->knockBack($player, 0, 1, 1, 1.5);
           } elseif (337.5 <= $yaw and $yaw < 360.0) {
                    $player->knockBack($player, 0, 0, 1, 1.5);
           }
           $player->sendPopup("§aMenujuTakTerbatasDanMelampauinya");
		   
		    } else {
				
				$player->sendMessage("You don't have permission to use Lompat!");
				
			}
        }
        if ($event->getItem()->getId() == 378 and $event->getItem()->getCustomName() == "§b§lSmokeBomb"){
            if (!isset($this->sbCooldown[$player->getName()])){
		       $nbt = new CompoundTag ("", [
					"Pos" => new ListTag ("Pos", [
					    new DoubleTag ("", $player->x),
						new DoubleTag ("", $player->y + $player->getEyeHeight()),
						new DoubleTag ("", $player->z)
					]),
					"Motion" => new ListTag ("Motion", [
						new DoubleTag ("", -\sin($player->yaw / 180 * M_PI) * \cos($player->pitch / 180 * M_PI)),
						new DoubleTag ("", -\sin($player->pitch / 180 * M_PI)),
						new DoubleTag ("", \cos($player->yaw / 180 * M_PI) * \cos($player->pitch / 180 * M_PI))
					]),
					"Rotation" => new ListTag ("Rotation", [
						new FloatTag ("", $player->yaw),
						new FloatTag ("", $player->pitch)
					])
				]);
				$f = 1.5;
				$snowball = Entity::createEntity("Snowball", $player->getLevel(), $nbt, $player);
				$snowball->setMotion($snowball->getMotion()->multiply($f));
				$snowball->spawnToAll();
				$this->sbCooldown[$player->getName()] = $player->getName();
                $time = "10";
                $this->sbCooldownTime[$player->getName()] = $time;

            }else{
               $player->sendPopup("§cYou can't use the SmokeBomb for another ".$this->sbCooldownTime[$player->getName()]." seconds.");
            }
        }
        if ($event->getItem()->getId() == 378 and $event->getItem()->getCustomName() == "§a§lSize"){
			if ($player->hasPermission("size.core")){
				$player->getInventory()->clearAll();
			    $player->getInventory()->setItem(0, Item::get(351, 1, 1)->setCustomName("§b§lUkuran Besar"));
			    $player->getInventory()->setItem(3, Item::get(351, 11, 1)->setCustomName("§b§lUkuran Normal"));
			    $player->getInventory()->setItem(6, Item::get(351, 10, 1)->setCustomName("§b§lUkuran Kecil"));
			    $player->getInventory()->setItem(8, Item::get(355, 0, 1)->setCustomName("§c§lKeluar"));
			} else {
				$player->sendMessage("§cYou can't own a fly without buying a Rank!");
			}
		}
		if ($event->getItem()->getId() == 351 and $event->getItem()->getCustomName() == "§b§lUkuran Besar"){
			$player->sendMessage("§d[§b§lLobbyCore§r§d]§o§aBerhasil Berubah Ukuran Ke Ukuran Besar!");
			$player->setScale(1.5);
		}
		if ($event->getItem()->getId() == 351 and $event->getItem()->getCustomName() == "§b§lUkuran Normal"){
			$player->sendMessage("§d[§b§lLobbyCore§r§d]§o§aBerhasil Berubah Ukuran Ke Ukuran Normal!");
			$player->setScale(1.0);
		}
		if ($event->getItem()->getId() == 351 and $event->getItem()->getCustomName() == "§b§lUkuran Kecil"){
			$player->sendMessage("§d[§b§lLobbyCore§r§d]§o§aBerhasil Berubah Ukuran Ke Ukuran Kecil!");
			$player->setScale(0.5);
		}
		if ($event->getItem()->getId() == 355 and $event->getItem()->getCustomName() == "§c§lKeluar"){
			$player->getInventory()->clearAll();
			$player->getInventory()->setItem(1, Item::get(288, 0, 1)->setCustomName("§c§lFly"));
		    $player->getInventory()->setItem(3, Item::get(369, 0, 1)->setCustomName("§e§lKekuatan"));
		    $player->getInventory()->setItem(5, Item::get(378, 0, 1)->setCustomName("§a§lSize"));
		    $player->getInventory()->setItem(7, Item::get(399, 0, 1)->setCustomName("§d§lGamemode"));
		}
		if ($event->getItem()->getId() == 399 and $event->getItem()->getCustomName() == "§d§lGamemode"){
			$player->getInventory()->clearAll();
			$player->getInventory()->setItem(0, Item::get(288, 0, 1)->setCustomName("§b§lGamemode Creative"));
			$player->getInventory()->setItem(3, Item::get(288, 0, 1)->setCustomName("§b§lGamemode Survival"));
			$player->getInventory()->setItem(8, Item::get(355, 0, 1)->setCustomName("§c§lKeluar"));
		}
		if ($event->getItem()->getId() == 288 and $event->getItem()->getCustomName() == "§b§lGamemode Creative"){
			$player->sendMessage("§d[§b§lLobbyCore§r§d]§o§aBerhasil Mengganti Gamemode Ke Creative!");
			$player->setGamemode(1);
		}
		if ($event->getItem()->getId() == 288 and $event->getItem()->getCustomName() == "§b§lGamemode Survival"){
			$player->sendMessage("§d[§b§lLobbyCore§r§d]§o§aBerhasil Mengganti Gamemode Ke Survival!");
			$player->setGamemode(0);
		}
		if ($event->getItem()->getId() == 355 and $event->getItem()->getCustomName() == "§c§lKeluar"){
			$player->getInventory()->clearAll();
			$player->getInventory()->setItem(1, Item::get(288, 0, 1)->setCustomName("§c§lFly"));
		    $player->getInventory()->setItem(3, Item::get(369, 0, 1)->setCustomName("§e§lKekuatan"));
		    $player->getInventory()->setItem(5, Item::get(378, 0, 1)->setCustomName("§a§lSize"));
		    $player->getInventory()->setItem(7, Item::get(399, 0, 1)->setCustomName("§d§lGamemode"));
		}
    }
    
    public function onItemSpawn(ItemSpawnEvent $event) {
        $item = $event->getEntity();
        $delay = 5;  
        $this->getScheduler()->scheduleDelayedTask(new class($item) extends PluginTask {
            public $itemEntity;
            
            public function __construct(ItemEntity $itemEntity)
            {
                $this->itemEntity = $itemEntity;
            }

            public function onRun(int $currentTick)
            {
                if(!$this->itemEntity->isFlaggedForDespawn()) $this->itemEntity->flagForDespawn();
            }
            
        }, 5*$delay);
    }
    public function onSnowballDown(EntityDespawnEvent $event) {
            if($event->getType() === 81){
        	   $entity = $event->getEntity();
               $shooter = $entity->getOwningEntity();
               $x = $entity->getX();
               $y = $entity->getY();
               $z = $entity->getZ();
               $level = $entity->getLevel();
               for ($i = 1; $i < 4; $i++) {
                   $v0 = new Vector3($x + 1, $y + $i, $z + 1);
                   $v1 = new Vector3($x - 1, $y + $i, $z - 1);
                     $v2 = new Vector3($x + 1, $y + $i, $z - 1);
                   $v3 = new Vector3($x - 1, $y + $i, $z + 1);
                   $v4 = new Vector3($x + 1, $y + $i, $z);
                   $v5 = new Vector3($x - 1, $y + $i, $z);
                   $v6 = new Vector3($x, $y + $i, $z + 1);
                   $v7 = new Vector3($x, $y + $i, $z - 1);
                   $v8 = new Vector3($x, $y + $i, $z);
                   $level->addParticle(new MobSpawnParticle($v0));
                   $level->addParticle(new MobSpawnParticle($v1));
                   $level->addParticle(new MobSpawnParticle($v2));
                   $level->addParticle(new MobSpawnParticle($v3));
                   $level->addParticle(new MobSpawnParticle($v4));
                   $level->addParticle(new MobSpawnParticle($v5));
                   $level->addParticle(new MobSpawnParticle($v6));
                   $level->addParticle(new MobSpawnParticle($v7));
                   $level->addParticle(new MobSpawnParticle($v8));
               }    
            }
    }
}
