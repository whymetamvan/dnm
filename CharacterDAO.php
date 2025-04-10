<?php

require_once "library/db_db.php";
require_once "library/client_library.php";

class CharacterDAO{
	public static function getCharactersList($sesKey){
		try{
			try{
				
				$pdo = db_db::getConnection();
				
				$tsql = "SELECT acc_type FROM `account_tbl` WHERE `acc_id` = (SELECT `acc_id` FROM `account_tbl` WHERE `sessionkey` =:sesKey LIMIT 1) LIMIT 1";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $sesKey, PDO::PARAM_STR);
				$stmt->execute();
				//ACCOUNT VIP = 2, FREE 1
				$resq = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$acc_type = $resq[0]['acc_type'];
				
				$tsql = "SELECT `character_id`,`character_name`,`character_level`,`character_gender` FROM `character_list` WHERE `acc_id` = (SELECT `acc_id` FROM `account_tbl` WHERE `sessionkey` =:sesKey)";
				if($acc_type == 1){
					$tsql = "SELECT `character_id`,`character_name`,`character_level`,`character_gender` FROM `character_list` WHERE `acc_id` = (SELECT `acc_id` FROM `account_tbl` WHERE `sessionkey` =:sesKey) LIMIT 1";
				}
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $sesKey, PDO::PARAM_STR);
				$stmt->execute();
				$resq = $stmt->fetchAll(PDO::FETCH_NUM);
				
				$tsql = "SELECT GROUP_CONCAT(`character_id`)'c_ID',GROUP_CONCAT(`character_name`)'c_Name' FROM `character_list` WHERE `acc_id` = (SELECT `acc_id` FROM `account_tbl` WHERE `sessionkey` =:sesKey)";
				if($acc_type == 1){
					$tsql = "SELECT GROUP_CONCAT(`character_id`)'c_ID',GROUP_CONCAT(`character_name`)'c_Name' FROM `character_list` WHERE `acc_id` = (SELECT `acc_id` FROM `account_tbl` WHERE `sessionkey` =:sesKey) LIMIT 1";
				}
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $sesKey, PDO::PARAM_STR);
				$stmt->execute();
				$resqN = $stmt->fetchAll(PDO::FETCH_ASSOC);
								
				$result = array('status' =>1, 'error' => 0,
								'result' => $resq,
								'initial_char_list' => 'mc',
								'c_name' => $resqN[0]['c_Name'],
								'character_list' => $resqN[0]['c_ID'],
								//'c_name' => '',
								//'character_list' => '',
								'char_list' => 'mc',
								'login_per_day' => 0);
				
				return $result;
			}catch(Exception $e){
				$result = array('status' =>0,'error' =>105, 'error_code' => $e->getMessage());
				return $result;
			}
		}catch(Exception $e){
			$result = array('status' =>0,'error' =>105, 'error_code' =>  $e->getMessage());
			return $result;
		}
	}
	
	public static function getCharacterById($seskey, $char_id){
		try{
			try{
				
				$pdo = db_db::getConnection();
				
				//UPDATE SESSIONKEY
				$tsql = "UPDATE `character_list` SET `sessionkey` =:sesKey WHERE `character_id` =:char_id LIMIT 1 ";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				
				//GET DATA
				
				$tsql = "SELECT * FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$tsql = "SELECT GROUP_CONCAT(`character_equipped_skills`)`character_equipped_skills` FROM `character_equipped_skills` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_equipped_skills = "";
				if($stmt->rowCount() > 0){
					$character_equipped_skills_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_equipped_skills = $character_equipped_skills_res[0]['character_equipped_skills'];
				}
				
				$tsql = "SELECT `character_equipped_weapon` FROM `character_equipped_weapon` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_equipped_weapon = "";
				if($stmt->rowCount() > 0){
					$character_equipped_weapon_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_equipped_weapon = $character_equipped_weapon_res[0]['character_equipped_weapon'];
				}
				
				$tsql = "SELECT `character_equipped_body_set` FROM `character_equipped_body_set` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_equipped_body_set = "";
				if($stmt->rowCount() > 0){
					$character_equipped_body_set_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_equipped_body_set = $character_equipped_body_set_res[0]['character_equipped_body_set'];
				}
				
				$tsql = "SELECT `character_equipped_back_item` FROM `character_equipped_back_item` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_equipped_back_item = "";
				if($stmt->rowCount() > 0){
					$character_equipped_back_item_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_equipped_back_item = $character_equipped_back_item_res[0]['character_equipped_back_item'];
				}

				$tsql = "SELECT `character_equipped_accessory` FROM `character_equipped_accessory` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_equipped_accessory = "0";
				if($stmt->rowCount() > 0){
					$character_equipped_accessory_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_equipped_accessory = $character_equipped_accessory_res[0]['character_equipped_accessory'];
				}
				
				$tsql = "SELECT GROUP_CONCAT(`character_body_set`)`character_body_set` FROM `character_body_set` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_body_set = "";
				if($stmt->rowCount() > 0){
					$character_body_set_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_body_set = $character_body_set_res[0]['character_body_set'];
				}
				
				$tsql = "SELECT GROUP_CONCAT(`character_weapon`)`character_weapon` FROM `character_weapon` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_weapon = "";
				if($stmt->rowCount() > 0){
					$character_weapon_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_weapon = $character_weapon_res[0]['character_weapon'];
				}
				
				$tsql = "SELECT GROUP_CONCAT(`character_skill`)`character_skill` FROM `character_skill` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_skill = "";
				if($stmt->rowCount() > 0){
					$character_skill_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_skill = $character_skill_res[0]['character_skill'];
				}
				
				$tsql = "SELECT GROUP_CONCAT(`character_back_item`)`character_back_item` FROM `character_back_item` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_back_item = "";
				if($stmt->rowCount() > 0){
					$character_back_item_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_back_item = $character_back_item_res[0]['character_back_item'];
				}

				$tsql = "SELECT GROUP_CONCAT(`character_accessory`)`character_accessory` FROM `character_accessory` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_accessory = "";
				if($stmt->rowCount() > 0){
					$character_accessory_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_accessory = $character_accessory_res[0]['character_accessory'];
				}
				
				$tsql = "SELECT * FROM `character_body_style` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1) LIMIT 1";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_body_style = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$tsql = "SELECT * FROM `character_element_points` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1) LIMIT 1";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_element_points = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$tsql = "SELECT GROUP_CONCAT(`character_inv_hair`)`character_inv_hair` FROM `character_inv_hair` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_inv_hair = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$tsql = "SELECT GROUP_CONCAT(CONCAT(mission_id, ':' , success, ':' , failure, ':' , time_stamp))'character_mission' FROM `character_mission` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_mission = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$tsql = "SELECT GROUP_CONCAT(`character_item`)`character_item` FROM `character_item` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_item = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$tsql = "SELECT GROUP_CONCAT(`character_ninja_essence`)`character_ninja_essence` FROM `character_ninja_essence` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_ninja_essence = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$tsql = "SELECT GROUP_CONCAT(CONCAT(material_id, ':' , total))`character_material` FROM `character_material` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$stmt->execute();
				$character_material = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
								
				$result = array('status' =>1, 'error' => 0,
								'result' => array(
												  'character_id' => $character_list[0]['character_id'],
												  'character_name' => $character_list[0]['character_name'],
												  'character_level' => $character_list[0]['character_level'],
												  'character_xp' => $character_list[0]['character_xp'],
												  'character_rank' => $character_list[0]['character_rank'],
												  'character_gold' => $character_list[0]['character_gold'],
												  'character_hp' => $character_list[0]['character_hp'],
												  'character_cp' => $character_list[0]['character_cp'],
												  'character_armor' => $character_list[0]['character_armor'],
												  'character_gender' => $character_list[0]['character_gender'],
												  'character_equipped_skills' => $character_equipped_skills,
												  'character_equipped_weapon' => $character_equipped_weapon,
												  'character_equipped_body_set' => $character_equipped_body_set,
												  'character_equipped_back_item' => $character_equipped_back_item,
												  'character_equipped_accessory' => $character_equipped_accessory,
												  'character_body_set' => $character_body_set,
												  'character_weapon' => $character_weapon,
												  'character_skill' => $character_skill,
												  'character_back_item' => $character_back_item,
												  'character_accessory' => $character_accessory,
												  'character_npc' => '',
												  'character_magatama' => '2:100,1:100,4:100',
												  'character_friends' => '',
												  'character_item' => $character_item[0]['character_item'],
												  'character_ninja_essence' => $character_ninja_essence[0]['character_ninja_essence'],
												  'character_material' => $character_material[0]['character_material'],
												  'character_hair_color' => $character_body_style[0]['character_hair_color'],
												  'character_skin_color' => $character_body_style[0]['character_skin_color'],
												  'character_face' => $character_body_style[0]['character_face'],
												  'character_hair' => $character_body_style[0]['character_hair'],
												  'character_fire' => $character_element_points[0]['character_fire'],
												  'character_water' => $character_element_points[0]['character_water'],
												  'character_wind' => $character_element_points[0]['character_wind'],
												  'character_earth' => $character_element_points[0]['character_earth'],
												  'character_lightning' => $character_element_points[0]['character_lightning'],
												  'character_taijutsu' => $character_element_points[0]['character_taijutsu'],
												  'character_genjutsu' => $character_element_points[0]['character_genjutsu'],
												  'character_summon' => $character_element_points[0]['character_summon'],
												  'character_control' => '', //SA LVL 60 CLASS
												  'character_bloodline' => '300',
												  'character_inv_hair' => $character_inv_hair[0]['character_inv_hair'],
												  'character_common_currency' => '',
												  'character_mission' => $character_mission[0]['character_mission'],
												  'expiry_data' => array(
																		'remove_inv_arr' => array(),
																		'add_inv_arr' => [],
																		'equip_arr' => [],
																		'current_expiry_arr' => [],
																		'remove_equip_arr' => [],
																		'expiry_pet_data' => [],
																		'expiry_hash' => '8b47fef9c6fa'
																		),
												  'daily' => null,
												  'character_hash' => 'b507d2702514',
												  'character_pre_hash' => '691d1042661d',
												  'recruit_friend_data' => [],
												  'character_inv_slots' => array(
																				'weapon' => '0',
																				'body_set' => '0',
																				'item' => '0',
																				'essence' => '0',
																				'material' => '0',
																				'back' => '0',
																				'accessory' => '0',
																				'pet' => '0'),
												   'senjutsu_spirit' => 1000,
												   'character_senjutsu_ss' => 1000,
												   'character_trade_item' => '',
												   'character_equipped_trade_weapon' => '',
												   'character_equipped_trade_back_item' => '',
												   'character_equipped_trade_body_set' => '',
												   "stateInv" => [],
												  ));
				
				return $result;
			}catch(Exception $e){
				$result = array('status' =>0,'error' =>105, 'error_code' => $e->getMessage());
				return $result;
			}
		}catch(Exception $e){
			$result = array('status' =>0,'error' =>105, 'error_code' =>  $e->getMessage());
			return $result;
		}
	}
	
	public static function getExtraData($sesKey, $hash, $char_id){
		try{
			
			$pdo = db_db::getConnection();
			
			// $tsql = "SELECT a.`character_pet_id` as id, b.name, b.swfName, b.clsName, a.`ep`, a.`ep_max`, a.`level`, a.`xp`, a.`equipped`, a.`maturity` FROM `character_pet` a, `pet_list` b WHERE a.`character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1 ) AND a.`pet_id` = b.`pet_id`";
			// $stmt = $pdo->prepare($tsql);
			// $stmt->bindParam(':sesKey', $sesKey, PDO::PARAM_STR);
			// $stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
			// $stmt->execute();
			// $char_pet_row = $stmt->rowCount();
			// $char_pet = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$char_pet = [];
			// if($char_pet_row > 0){
			// 	$ii=0;
			// 	while($ii < $char_pet_row){
			// 		$tsql = "SELECT skills FROM `character_pet_skills` WHERE `character_pet_id` =:character_pet_id ";
			// 		$stmt = $pdo->prepare($tsql);
			// 		$stmt->bindParam(':character_pet_id', $char_pet[$ii]['id'], PDO::PARAM_STR);
			// 		$stmt->execute();
			// 		$res_char_skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
			// 		$tmp_arr = [];
			// 		foreach ($res_char_skills as $row){
			// 			array_push($tmp_arr, $row['skills']);
			// 		}
			// 		$char_pet[$ii] += ['skills' => $tmp_arr];
			// 		$char_pet[$ii] += ['train_status' => array()];
			// 		$ii++;
			// 	}
			// }
			
			
			
			// $tsql = "SELECT `character_pet_id` FROM `character_pet` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1 ) AND `equipped` = 'true' LIMIT 1";
			// $stmt = $pdo->prepare($tsql);
			// $stmt->bindParam(':sesKey', $sesKey, PDO::PARAM_STR);
			// $stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
			// $stmt->execute();
			$pet_id = '';
			// if($stmt->rowCount() > 0){
			// 	$respet = $stmt->fetchAll(PDO::FETCH_ASSOC);
			// 	$pet_id = $respet[0]['character_pet_id'];
			// }

			$tsql = "SELECT `bloodline_id` FROM `character_bloodline` WHERE `char_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:sesKey AND `character_id` =:char_id LIMIT 1 )";
			$stmt = $pdo->prepare($tsql);
			$stmt->bindParam(':sesKey', $sesKey, PDO::PARAM_STR);
			$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
			$stmt->execute();
			
			$bloodline = [
				(object) array("bloodline_id"=>"1","skill_id"=> "1019","level"=> "10"),
				(object) array("bloodline_id"=>"1","skill_id"=> "1020","level"=> "10"),
				(object) array("bloodline_id"=>"1","skill_id"=> "1021","level"=> "10"),
				(object) array("bloodline_id"=>"1","skill_id"=> "1022","level"=> "10"),
				(object) array("bloodline_id"=>"1","skill_id"=> "1023","level"=> "10"),
				(object) array("bloodline_id"=>"1","skill_id"=> "1024","level"=> "10"),
				
				// (object) array("bloodline_id"=>"2","skill_id"=> "1007","level"=> "10"),
				// (object) array("bloodline_id"=>"2","skill_id"=> "1008","level"=> "10"),
				// (object) array("bloodline_id"=>"2","skill_id"=> "1009","level"=> "10"),
				// (object) array("bloodline_id"=>"2","skill_id"=> "1010","level"=> "10"),
				// (object) array("bloodline_id"=>"2","skill_id"=> "1025","level"=> "10"),
				// (object) array("bloodline_id"=>"2","skill_id"=> "1026","level"=> "10"),
				
				// (object) array("bloodline_id"=>"3","skill_id"=> "1004","level"=> "10"),
				// (object) array("bloodline_id"=>"3","skill_id"=> "1005","level"=> "10"),
				// (object) array("bloodline_id"=>"3","skill_id"=> "1006","level"=> "10"),
				// (object) array("bloodline_id"=>"3","skill_id"=> "1027","level"=> "10"),
				// (object) array("bloodline_id"=>"3","skill_id"=> "1028","level"=> "10"),
				// (object) array("bloodline_id"=>"3","skill_id"=> "1029","level"=> "10"),
				
				// (object) array("bloodline_id"=>"4","skill_id"=> "1015","level"=> "10"),
				// (object) array("bloodline_id"=>"4","skill_id"=> "1016","level"=> "10"),
				// (object) array("bloodline_id"=>"4","skill_id"=> "1017","level"=> "10"),
				// (object) array("bloodline_id"=>"4","skill_id"=> "1018","level"=> "10"),
				// (object) array("bloodline_id"=>"4","skill_id"=> "1030","level"=> "10"),
				// (object) array("bloodline_id"=>"4","skill_id"=> "1031","level"=> "10"),

				// (object) array("bloodline_id"=>"10","skill_id"=> "1041","level"=> "10"),
				// (object) array("bloodline_id"=>"10","skill_id"=> "1042","level"=> "10"),
				// (object) array("bloodline_id"=>"10","skill_id"=> "1043","level"=> "10"),
				// (object) array("bloodline_id"=>"10","skill_id"=> "1044","level"=> "10"),
				// (object) array("bloodline_id"=>"10","skill_id"=> "1045","level"=> "10"),
				// (object) array("bloodline_id"=>"10","skill_id"=> "1046","level"=> "10"),

				// (object) array("bloodline_id"=>"11","skill_id"=> "1047","level"=> "10"),
				// (object) array("bloodline_id"=>"11","skill_id"=> "1048","level"=> "10"),
				// (object) array("bloodline_id"=>"11","skill_id"=> "1049","level"=> "10"),
				// (object) array("bloodline_id"=>"11","skill_id"=> "1050","level"=> "10"),
				// (object) array("bloodline_id"=>"11","skill_id"=> "1051","level"=> "10"),
				// (object) array("bloodline_id"=>"11","skill_id"=> "1052","level"=> "10"),
				
				// (object) array("bloodline_id"=>"5","skill_id"=> "1013","level"=> "10"),
				// (object) array("bloodline_id"=>"5","skill_id"=> "1014","level"=> "10"),
				// (object) array("bloodline_id"=>"5","skill_id"=> "1032","level"=> "10"),
				
				// (object) array("bloodline_id"=>"6","skill_id"=> "1002","level"=> "10"),
				// (object) array("bloodline_id"=>"6","skill_id"=> "1003","level"=> "10"),
				// (object) array("bloodline_id"=>"6","skill_id"=> "1033","level"=> "10"),

				(object) array("bloodline_id"=>"7","skill_id"=> "1011","level"=> "10"),
				(object) array("bloodline_id"=>"7","skill_id"=> "1012","level"=> "10"),
				(object) array("bloodline_id"=>"7","skill_id"=> "1034","level"=> "10"),

				(object) array("bloodline_id"=>"8","skill_id"=> "1000","level"=> "10"),
				(object) array("bloodline_id"=>"8","skill_id"=> "1001","level"=> "10"),
				(object) array("bloodline_id"=>"8","skill_id"=> "1035","level"=> "10"),
				
				// (object) array("bloodline_id"=>"9","skill_id"=> "1036","level"=> "10"),
				// (object) array("bloodline_id"=>"9","skill_id"=> "1037","level"=> "10"),
				// (object) array("bloodline_id"=>"9","skill_id"=> "1038","level"=> "10"),

				
			];

			$senjutsu = [
				(object) array("senjutsu_id"=>"1","skill_id"=> "3000","level"=> "1"),
				(object) array("senjutsu_id"=>"1","skill_id"=> "3500","level"=> "1"),
				
				(object) array("senjutsu_id"=>"2","skill_id"=> "3001","level"=> "10"),
				(object) array("senjutsu_id"=>"2","skill_id"=> "3002","level"=> "10"),
				(object) array("senjutsu_id"=>"2","skill_id"=> "3003","level"=> "10"),
				(object) array("senjutsu_id"=>"2","skill_id"=> "3004","level"=> "10"),
				(object) array("senjutsu_id"=>"2","skill_id"=> "3005","level"=> "10"),
				(object) array("senjutsu_id"=>"2","skill_id"=> "3006","level"=> "10"),
				(object) array("senjutsu_id"=>"2","skill_id"=> "3007","level"=> "10"),
				(object) array("senjutsu_id"=>"2","skill_id"=> "3008","level"=> "10"),
				(object) array("senjutsu_id"=>"2","skill_id"=> "3009","level"=> "10"),
				(object) array("senjutsu_id"=>"2","skill_id"=> "3010","level"=> "10"),
				
				(object) array("senjutsu_id"=>"3","skill_id"=> "3101","level"=> "10"),
				(object) array("senjutsu_id"=>"3","skill_id"=> "3102","level"=> "10"),
				(object) array("senjutsu_id"=>"3","skill_id"=> "3103","level"=> "10"),
				(object) array("senjutsu_id"=>"3","skill_id"=> "3104","level"=> "10"),
				(object) array("senjutsu_id"=>"3","skill_id"=> "3105","level"=> "10"),
				(object) array("senjutsu_id"=>"3","skill_id"=> "3106","level"=> "10"),
				(object) array("senjutsu_id"=>"3","skill_id"=> "3107","level"=> "10"),
				(object) array("senjutsu_id"=>"3","skill_id"=> "3108","level"=> "10"),
				(object) array("senjutsu_id"=>"3","skill_id"=> "3109","level"=> "10"),
				(object) array("senjutsu_id"=>"3","skill_id"=> "3110","level"=> "10")
			];
			
			
			$result = array('status' =>1, 'error' => 0,
							'result' => array(
												'easter2017_icon_new' => false,
												'legendary_limit_skill_package' => true,
												'newDailyLoginStamp2017_iconZh' => '2月登入獎勵',
												'map_key' =>	'21',
												'combine_boost_time_period_start_left' =>	-99556,
												'limited_offer_201706_new' =>	true,
												'get_hunting_passport' =>	false,
												'sje_end_date' =>	0,
												'bank_system_tutorial' =>	false,
												'lucky_spin_show_wheel' =>	1,
												'dailyRewardClaimMaintenance20170830Btn' =>	false,
												'special_mini_game' =>	0,
												'senjutsu_system' =>	2,//3,
												'training_skill' =>	Null,
												'veteran_return_fk_accepted' =>	0,
												'memorial_day_2016' =>	false,
												'special_november_2016_package' =>	false,
												'level_80_exam_reward_list_read' =>	1,
												'newsId' =>	-1,
												'newsfeed_material_posted' =>	false,
												'showPveComingsoon' =>	false,
												'invite_accepted' =>	0,
												'totalToken' =>	10000000,
												'combine_boost_time_in_period' =>	1,
												'limited_offer_201710' =>	false,
												'secret1' =>	Null	,
												'special_july_2016_package_new' =>	true,
												'VDay201703Btn' =>	false,
												'daily_login' =>	true,
												'claim_fowte' =>	0,
												'dailygift_request_src' =>	'ms',
												'limited_offer_201711_new' =>	true,
												'special_september_2016_package' =>	false,
												'limited_offer_201706' =>	false,
												'is_fan' =>	true,
												'anni_8th_design_enabled' =>	false,
												'limited_offer_201712' =>	false,
												'secret2' =>	Null	,
												'se_day_count_open' =>	1,
												'FatherDays2017_icon' =>	false,
												'crew_id' => '748',
												'christmas_coin' =>	'4',
												'new_mail' =>	false,
												'roulette_allowed' =>	false,
												'easter_rank_end' =>	true,
												'double_exp' =>	Null	,
												'dragon_pet_christmas' =>	1,
												'achievement_point' =>	'1830',
												'se_end_date_notice' =>	1,
												'special_apr_2016_package' =>	false,
												'reaming_pet' =>	Null	,
												'sparta2017packageBtn' =>	false,
												'limitOffer201701Btn' =>	false,
												'character_create_date' =>	'2010-01-22 07:56:41',
												'delete_request' =>	[]	,
												'limited_offer_201711' =>	false,
												'showPveNew' =>	true,
												'olympus_package_2016' =>	false,
												'prestige' =>	1000000000,
												'extra_roulette_times' =>	0,
												'is_hard_mode_locked' =>	0,
												'clan_id' =>	'',
												'sumatra_event_2016' =>	false,
												'special_november_2016_package_new' =>	true,
												'limited_offer_201802' =>	true,
												'showFanPage' =>	false,
												'motherDays2017_icon_new' =>	false,
												'newDailyLoginStamp201708_icon' =>	false,
												'newDailyLoginStamp2017_icon' =>	true,
												'emblem_type' =>	2,
												'FatherDays2017_icon_new' =>	false,
												'lucky_spin_remaining_spin' =>	2,
												'limited_offer_201612_new' =>	true,
												'Sevenene560EventBtn_new' =>	false,
												'special_august_2016_package_new' =>	true,
												'sje_end_date_notice' =>	1,
												'remaining_skill' =>	0,
												'special_apr_2016_package_new' =>	true,
												'limitOffer201701Btn_new' =>	true,
												'sumatra_event_2016_new' =>	true,
												'trial_emblem_expire_time' =>	Null	,
												'crew_merit' =>	100000000,
												'dailyRewardClaimToken201801Btn' =>	false,
												'combine_boost_time_period_end_left' =>	505243,
												'newsfeed_easter_2014_posted' =>	false,
												'minigame_burger' =>	true,
												'special_october_2016_package_new' =>	true,
												'limited_offer_201612' =>	false,
												'thanks2016' =>	false,
												'limited_offer_201710_new' =>	true,
												'Easter2017SkillPackage2_icon_new' =>	false,
												'premium_claim_skill_set' =>	false,
												'special_october_2016_package' =>	false,
												'premium_daily_token' =>	true,
												'activeEnemy' =>	'enemy460,enemy514,enemy515,enemy516,enemy517,enemy518,enemy519,enemy552,enemy553,enemy554,enemy555,enemy556',
												'newDailyLoginStamp2017_iconisNew' =>	true,
												'newDailyLoginStamp2017_iconEn' =>	'February Login Reward',
												'trial_emblem_expire_date_14' =>	Null	,
												'se_end_date' =>	0,
												'anni_8th_ice_pop_time' =>	1,
												'consecutive_days' =>	5,
												'emblem_xp_bonus_times' =>	0,
												'seasonNumber' =>	'98',
												'special_july_2016_package' =>	false,
												'dailygift_request_date' =>	'2018-02-25 03:39:16',
												'special_jane_2016_package_new' =>	true,
												'special_september_2016_package_new' =>	true,
												'lucky_spin_consecutive_day' =>	5,
												'dailyRewardClaimToken201802Btn' =>	false,
												'DailyRewardClaimMcoin201711' =>	false,
												'seasonNumber_crew' =>	'28',
												'anni8thDoubleExpGold_times' =>	0,
												'special_feb_2016_package' =>	false,
												'limited_offer_201802_new' =>	true,
												'claim_remain' =>	5,
												'emblem_xp_bonus_claim' =>	true,
												'limitOffer201703Btn' =>	false,
												'isGraphic' =>	false,
												'dailygift_request_limit' =>	3,
												'char_login_per_day' =>	0,
												'get_learning_status' =>	[],
												'christmasEvent2016' =>	false,
												'wolverine_icon_new' =>	false,
												'limitOffer201703Btn_new' =>	true,
												'pvp_invite' =>	false,
												'premium_claim_level' =>	true,
												'limited_offer_201712_new' =>	true,
												'weeklyRewardStatus201710' =>	false,
												'special_august_2016_package' =>	false,
												'scare2016' =>	false,
												'extra_data_hash' =>	'810d68253427',
												'special_jane_2016_package' =>	false,
												'tutorial_expiry_item' =>	true,
												'combine_boost_time' =>	0,
												'newsArr' => array('news1'),
												'petid' => $pet_id,
												'bloodline' => $bloodline,
												'senjutsu' => $senjutsu,
												'player_pet' => $char_pet,
												'totalMCoin' => 0,
												'MCoin' => 0,
												'pvp_record' => array('pvp_currency' => 10000000),
												'showPveComingsoon' => false,
												'showPveNew' => false

							),
					'cur_date'	=>	time(),
					'temp_bloodline_str'	=>	''
			
			);
			
			
			return $result;
		}catch(Exception $e){
			$result = array('status' =>0,'error' =>105, 'error_code' => $e->getMessage());
			return $result;
		}
	}
	
	public static function createCharacter($seskey, $charName, $charGender, $hairColor, $skinColor, $hairID, $faceID){
		
		$pdo = db_db::getConnection();
		//REGISTER
		
				$tsql = "SELECT `acc_id` FROM `account_tbl` WHERE `sessionkey` =:sesKey LIMIT 1";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':sesKey', $seskey, PDO::PARAM_STR);
				$stmt->execute();
				$r_acc = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$acc_id = $r_acc[0]['acc_id'];
				
				$d_lvl = 1;
				$d_xp = 130;
				$d_rank = 1;
				$d_gold = 550;
				$d_hp = 100;
				$d_cp = 100;
				$d_armor = 0;
								
				$tsql = "INSERT INTO `character_list` VALUES (NULL,:character_name,:character_level,:character_xp,:character_rank,:character_gold,:character_hp,:character_cp,:character_armor,:character_gender,:acc_id,:seskey)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_name', $charName, PDO::PARAM_STR);
				$stmt->bindParam(':character_level', $d_lvl, PDO::PARAM_STR);
				$stmt->bindParam(':character_xp', $d_xp, PDO::PARAM_STR);
				$stmt->bindParam(':character_rank', $d_rank, PDO::PARAM_STR);
				$stmt->bindParam(':character_gold', $d_gold, PDO::PARAM_STR);
				$stmt->bindParam(':character_hp', $d_hp, PDO::PARAM_STR);
				$stmt->bindParam(':character_cp', $d_cp, PDO::PARAM_STR);
				$stmt->bindParam(':character_armor', $d_armor, PDO::PARAM_STR);
				$stmt->bindParam(':character_gender', $charGender, PDO::PARAM_STR);
				$stmt->bindParam(':acc_id', $acc_id, PDO::PARAM_STR);
				$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
				$stmt->execute();
				$insertId = $pdo->lastInsertId();
				
				$tsql = "INSERT INTO `character_body_style` VALUES (NULL,:character_id,:character_hair_color,:character_skin_color,:character_face,:character_hair)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->bindParam(':character_hair_color', $hairColor, PDO::PARAM_STR);
				$stmt->bindParam(':character_skin_color', $skinColor, PDO::PARAM_STR);
				$stmt->bindParam(':character_face', $faceID, PDO::PARAM_STR);
				$stmt->bindParam(':character_hair', $hairID, PDO::PARAM_STR);
				$stmt->execute();
				
				/* $tsql = "INSERT INTO `character_back_item` VALUES (NULL,:character_id,'')";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->execute(); */
				
				$tsql = "INSERT INTO `character_body_set` VALUES (NULL,:character_id, 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->execute();
				
				$tsql = "INSERT INTO `character_element_points` VALUES (NULL,:character_id,0,0,0,0,0,0,0,0)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->execute();
				
				$tsql = "INSERT INTO `character_equipped_back_item` VALUES (NULL,'',:character_id)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->execute();
				
				$tsql = "INSERT INTO `character_equipped_body_set` VALUES (NULL,1,:character_id)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->execute();
				
				$tsql = "INSERT INTO `character_equipped_weapon` VALUES (NULL,1,:character_id)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->execute();
				
				$tsql = "INSERT INTO `character_inv_hair`(`id`, `character_inv_hair`, `character_id`) VALUES (NULL,:hairID,:character_id)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->bindParam(':hairID', $hairID, PDO::PARAM_STR);
				$stmt->execute();
				
				$tsql = "INSERT INTO `character_inv_slots`(`id`, `character_id`) VALUES (NULL,:character_id)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->execute();
				
				$tsql = "INSERT INTO `character_mission`  VALUES (NULL,:character_id,0,1,0,UNIX_TIMESTAMP())";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->execute();
				
				$tsql = "INSERT INTO `character_weapon`(`id`, `character_id`, `character_weapon`) VALUES (null,:character_id,1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->execute();
				
				$tsql = "INSERT INTO `character_item` VALUES (NULL,1,:character_id);INSERT INTO `character_item` VALUES (NULL,2,:character_id);INSERT INTO `character_item` VALUES (NULL,3,:character_id)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':character_id', $insertId, PDO::PARAM_STR);
				$stmt->execute();
				
				$tsql = "INSERT INTO `account_tokens`(`id`, `acc_id`, `tokens`) VALUES (NULL,:acc_id,100000000)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':acc_id', $acc_id, PDO::PARAM_STR);
				$stmt->execute();
					
		$result = array('status' =>1, 'error' => 0, "character_id" => $insertId);
		return $result;
	}
	
	
	public static function deleteCharacter($seskey, $char_id){
		$pdo = db_db::getConnection();
		
		$tsql = "DELETE FROM character_accessory WHERE `character_id` =:char_id;
				DELETE FROM character_back_item WHERE `character_id` =:char_id;
				DELETE FROM character_bloodline WHERE `character_id` =:char_id;
				DELETE FROM character_body_set WHERE `character_id` =:char_id; 
				DELETE FROM character_body_style WHERE `character_id` =:char_id;
				DELETE FROM character_element_points WHERE `character_id` =:char_id;
				DELETE FROM character_equipped_back_item WHERE `character_id` =:char_id;
				DELETE FROM character_equipped_body_set WHERE `character_id` =:char_id;
				DELETE FROM character_equipped_skills WHERE `character_id` =:char_id;
				DELETE FROM character_equipped_weapon WHERE `character_id` =:char_id;
				DELETE FROM character_equipped_accessory WHERE `character_id` =:char_id;
				DELETE FROM character_inv_hair WHERE `character_id` =:char_id;
				DELETE FROM character_inv_slots WHERE `character_id` =:char_id;
				DELETE FROM character_item WHERE `character_id` =:char_id;
				DELETE FROM character_list WHERE `character_id` =:char_id;
				DELETE FROM character_mission WHERE `character_id` =:char_id;
				DELETE FROM character_skill WHERE `character_id` =:char_id;
				DELETE FROM character_weapon WHERE `character_id` =:char_id; ";
		//$tsql = "DELETE FROM character_back_item WHERE `character_id` =:char_id;
		//		DELETE FROM character_bloodline WHERE `character_id` =:char_id;
		//		DELETE FROM character_body_set WHERE `character_id` =:char_id; 
		//		DELETE FROM character_body_style WHERE `character_id` =:char_id;
		//		DELETE FROM character_common_currency WHERE `character_id` =:char_id;
		//		DELETE FROM character_element_points WHERE `character_id` =:char_id;
		//		DELETE FROM character_equipped_back_item WHERE `character_id` =:char_id;
		//		DELETE FROM character_equipped_body_set WHERE `character_id` =:char_id;
		//		DELETE FROM character_equipped_skills WHERE `character_id` =:char_id;
		//		DELETE FROM character_equipped_weapon WHERE `character_id` =:char_id;
		//		DELETE FROM character_inv_hair WHERE `character_id` =:char_id;
		//		DELETE FROM character_inv_slots WHERE `character_id` =:char_id;
		//		DELETE FROM character_item WHERE `character_id` =:char_id;
		//		DELETE FROM character_list WHERE `character_id` =:char_id;
		//		DELETE FROM character_material WHERE `character_id` =:char_id;
		//		DELETE FROM character_mission WHERE `character_id` =:char_id;
		//		DELETE FROM character_skill WHERE `character_id` =:char_id;
		//		DELETE FROM character_weapon WHERE `character_id` =:char_id; ";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->execute();
		
		$result = array('status' =>1, 'error' => 0);
		
		return $result;
		
	}
	
	public static function updateAP($seskey, $char_id, $apArr){
		$pdo = db_db::getConnection();
		
		$tsql = "UPDATE `character_element_points` SET `character_fire`=:character_fire,`character_water`=:character_water,`character_wind`=:character_wind,`character_earth`=:character_earth,`character_lightning`=:character_lightning WHERE character_id = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id AND `sessionkey` =:seskey )";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
		$stmt->bindParam(':character_fire', $apArr[0], PDO::PARAM_STR);
		$stmt->bindParam(':character_water', $apArr[1], PDO::PARAM_STR);
		$stmt->bindParam(':character_wind', $apArr[2], PDO::PARAM_STR);
		$stmt->bindParam(':character_earth', $apArr[3], PDO::PARAM_STR);
		$stmt->bindParam(':character_lightning', $apArr[4], PDO::PARAM_STR);
		$stmt->execute();
		
		$result = array('status' =>1, 'error' => 0);
		return $result;
	}
	
	public static function equipCharacter($seskey, $char_id, $getBodySet, $getWeapon, $equippedSkills, $getBackItem, $getAccessory){
		$pdo = db_db::getConnection();
		
		$tsql = "UPDATE `character_equipped_back_item` SET `character_equipped_back_item`=:character_equipped_back_item WHERE character_id = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id AND `sessionkey` =:seskey );
				UPDATE `character_equipped_body_set` SET `character_equipped_body_set`=:character_equipped_body_set WHERE character_id = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id AND `sessionkey` =:seskey );
				UPDATE `character_equipped_weapon` SET `character_equipped_weapon`=:character_equipped_weapon WHERE character_id = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id AND `sessionkey` =:seskey );
				UPDATE `character_equipped_accessory` SET `character_equipped_accessory`=:character_equipped_accessory WHERE character_id = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id AND `sessionkey` =:seskey );
				 ";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
		
		$getBodySet = str_replace("set","",$getBodySet);
		$getWeapon = str_replace("wpn","",$getWeapon);
		$getBackItem = str_replace("back","",$getBackItem);
		$getAccessory = str_replace("acsy","",$getAccessory);
		
		$stmt->bindParam(':character_equipped_back_item', $getBackItem, PDO::PARAM_STR);
		$stmt->bindParam(':character_equipped_body_set', $getBodySet, PDO::PARAM_STR);
		$stmt->bindParam(':character_equipped_weapon', $getWeapon, PDO::PARAM_STR);
		$stmt->bindParam(':character_equipped_accessory', $getAccessory, PDO::PARAM_STR);
		$stmt->execute();
		
		//FOR UPDATE EQUIPED SKILLS
			$tsql = "DELETE FROM `character_equipped_skills` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey AND `character_id` =:char_id) ";
			$stmt = $pdo->prepare($tsql);
			$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
			$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
			$stmt->execute();
		if($equippedSkills != null || sizeof($equippedSkills) > 0){			
			$i=0;
			while($i<sizeof($equippedSkills)){
				$tsql = "INSERT INTO `character_equipped_skills`(`character_id`, `character_equipped_skills`) VALUES ((SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey AND `character_id` =:char_id),:skillID)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
				$stmt->bindParam(':char_id', $char_id, PDO::PARAM_STR);
				$skillIDs =  str_replace("skill","",$equippedSkills[$i]);
				$stmt->bindParam(':skillID', $skillIDs, PDO::PARAM_STR);
				$stmt->execute();
				$i++;
			}
		}
		
		
		$result = array('status' =>1, 'error' => 0);
		return $result;
	}
	
	public static function trainSkill($seskey, $skillid, $charid, $updateSequence){
		$pdo = db_db::getConnection();
		
		$skillid =  str_replace("skill","",$skillid);
		
		$tsql = "INSERT INTO `character_skill`(`id`, `character_id`, `character_skill`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` = :seskey AND `character_id` = :charid LIMIT 1),:skillid)";
		//$tsql = "INSERT INTO `character_skill`(`id`, `character_id`, `character_skill`) VALUES (NULL,:charid,:skillid)";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':skillid', $skillid, PDO::PARAM_STR);
		$stmt->bindParam(':charid', $charid, PDO::PARAM_STR);
		$stmt->execute();
		
		$result = array('status' =>1, 'error' => 0, 'result' => 0);
		return $result;
	}
	
	public static function buyItem($seskey, $buyItem, $qty){
		$pdo = db_db::getConnection();
		
		if (strpos($buyItem, 'wpn') !== false){
			$buyItem =  str_replace("wpn","",$buyItem);
			$tsql = "INSERT INTO `character_weapon` (`id`, `character_id`, `character_weapon`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'set') !== false){
			$buyItem =  str_replace("set","",$buyItem);
			$tsql = "INSERT INTO `character_body_set` (`id`, `character_id`, `character_body_set`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'back') !== false){
			$buyItem =  str_replace("back","",$buyItem);
			$tsql = "INSERT INTO `character_back_item` (`id`, `character_id`, `character_back_item`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'acsy') !== false){
			$buyItem =  str_replace("acsy","",$buyItem);
			$tsql = "INSERT INTO `character_accessory` (`id`, `character_id`, `character_accessory`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'item') !== false){
			$buyItem =  str_replace("item","",$buyItem);
			$tsql = "INSERT INTO `character_item` (`id`, `character_id`, `character_item`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':buyItem', $buyItem, PDO::PARAM_STR);
		$stmt->execute();
				
		$result = array('status' =>1, 'error' => 0, 'result' => 0);
		return $result;
	}

	public static function buyItemWithPvpPoint($seskey, $buyItem, $qty){
		$pdo = db_db::getConnection();
		
		if (strpos($buyItem, 'wpn') !== false){
			$buyItem =  str_replace("wpn","",$buyItem);
			$tsql = "INSERT INTO `character_weapon` (`id`, `character_id`, `character_weapon`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'set') !== false){
			$buyItem =  str_replace("set","",$buyItem);
			$tsql = "INSERT INTO `character_body_set` (`id`, `character_id`, `character_body_set`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'back') !== false){
			$buyItem =  str_replace("back","",$buyItem);
			$tsql = "INSERT INTO `character_back_item` (`id`, `character_id`, `character_back_item`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'acsy') !== false){
			$buyItem =  str_replace("acsy","",$buyItem);
			$tsql = "INSERT INTO `character_accessory` (`id`, `character_id`, `character_accessory`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'item') !== false){
			$buyItem =  str_replace("item","",$buyItem);
			$tsql = "INSERT INTO `character_item` (`id`, `character_id`, `character_item`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':buyItem', $buyItem, PDO::PARAM_STR);
		$stmt->execute();
				
		$result = array('status' =>1, 'error' => 0, 'result' => 0);
		return $result;
	}

	public static function buyItemWithPrestige($seskey, $buyItem, $qty){
		$pdo = db_db::getConnection();

		if (strpos($buyItem, 'wpn') !== false){
			$buyItem =  str_replace("wpn","",$buyItem);
			$tsql = "INSERT INTO `character_weapon` (`id`, `character_id`, `character_weapon`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'set') !== false){
			$buyItem =  str_replace("set","",$buyItem);
			$tsql = "INSERT INTO `character_body_set` (`id`, `character_id`, `character_body_set`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'back') !== false){
			$buyItem =  str_replace("back","",$buyItem);
			$tsql = "INSERT INTO `character_back_item` (`id`, `character_id`, `character_back_item`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'acsy') !== false){
			$buyItem =  str_replace("acsy","",$buyItem);
			$tsql = "INSERT INTO `character_accessory` (`id`, `character_id`, `character_accessory`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'item') !== false){
			$buyItem =  str_replace("item","",$buyItem);
			$tsql = "INSERT INTO `character_item` (`id`, `character_id`, `character_item`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':buyItem', $buyItem, PDO::PARAM_STR);
		$stmt->execute();
				
		$result = array('status' =>1, 'error' => 0, 'result' => 0);
		return $result;
	}

	public static function buyItemWithMerit($seskey, $buyItem, $qty){
		$pdo = db_db::getConnection();
		
		if (strpos($buyItem, 'wpn') !== false){
			$buyItem =  str_replace("wpn","",$buyItem);
			$tsql = "INSERT INTO `character_weapon` (`id`, `character_id`, `character_weapon`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'set') !== false){
			$buyItem =  str_replace("set","",$buyItem);
			$tsql = "INSERT INTO `character_body_set` (`id`, `character_id`, `character_body_set`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'back') !== false){
			$buyItem =  str_replace("back","",$buyItem);
			$tsql = "INSERT INTO `character_back_item` (`id`, `character_id`, `character_back_item`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'acsy') !== false){
			$buyItem =  str_replace("acsy","",$buyItem);
			$tsql = "INSERT INTO `character_accessory` (`id`, `character_id`, `character_accessory`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		else if (strpos($buyItem, 'item') !== false){
			$buyItem =  str_replace("item","",$buyItem);
			$tsql = "INSERT INTO `character_item` (`id`, `character_id`, `character_item`) VALUES (NULL,(SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey LIMIT 1),:buyItem)";
		}
		
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':buyItem', $buyItem, PDO::PARAM_STR);
		$stmt->execute();
				
		$result = array('status' =>1, 'error' => 0, 'result' => 0);
		return $result;
	}

	public static function rankup($seskey){
		$pdo = db_db::getConnection();
		
		$tsql = "UPDATE `character_list` SET `character_rank`=(character_rank + 1) WHERE `sessionkey` =:seskey LIMIT 1";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->execute();
		
		$result = array('status' =>1, 'error' => 0);
		
		return $result;
	}
	
	public static function deactivatePet($seskey, $pet_id){
		$pdo = db_db::getConnection();
		
		$tsql = "UPDATE `character_pet` SET `equipped`= 'false' WHERE `character_pet_id` =:pet_id AND `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey) LIMIT 1";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':pet_id', $pet_id, PDO::PARAM_STR);
		$stmt->execute();
		
		$result = array('status' =>1, 'error' => 0, 'result' => null);
		
		return $result;
	}
	
	public static function activatePet($seskey, $pet_id){
		$pdo = db_db::getConnection();
		
		$tsql = "UPDATE `character_pet` SET `equipped`= 'false' WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey);UPDATE `character_pet` SET `equipped`= 'true' WHERE `character_pet_id` =:pet_id AND `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey) LIMIT 1";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':pet_id', $pet_id, PDO::PARAM_STR);
		$stmt->execute();
		
		$result = array('status' =>1, 'error' => 0, 'result' => null);
		
		return $result;
	}
	
	public static function deletePet($seskey, $pet_id){
		$pdo = db_db::getConnection();
		
		$tsql = "DELETE FROM `character_pet` WHERE `character_pet_id` =:pet_id AND character_id = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey) LIMIT 1; DELETE FROM `character_pet_skills` WHERE `character_pet_id` =:pet_id; DELETE FROM `character_pet_train_status` WHERE `character_pet_id` =:pet_id;";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':pet_id', $pet_id, PDO::PARAM_STR);
		$stmt->execute();
		
		$result = array('status' =>1, 'error' => 0, 'result' => null);
		
		return $result;
	}
	
	public static function buyPet($seskey, $pet_id, $pet_name, $appLang){
		$pdo = db_db::getConnection();
		
		//CHECK FIRST IF PET ID EXIST IN DATABASE
		
		$tsql = "UPDATE `character_pet` SET `equipped`= 'false' where `character_id` = (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey)";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->execute();
		
		$tsql = "INSERT INTO `character_pet` (`character_pet_id`, `character_id`, `pet_id`, `ep`, `ep_max`, `level`, `xp`, `equipped`, `skills`, `train_status`, `maturity`) VALUES (NULL, (SELECT `character_id` FROM `character_list` WHERE `sessionkey` =:seskey), :pet_id, '0', '0', '1', '0', 'true', '', '', '0');";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->bindParam(':pet_id', $pet_id, PDO::PARAM_STR);
		$stmt->execute();
		$insertId = $pdo->lastInsertId();
		
		$tsql = "INSERT INTO `character_pet_skills`(`id`, `character_pet_id`, `skills`) VALUES (NULL,:insertId,0)";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':insertId', $insertId, PDO::PARAM_STR);
		$stmt->execute();
		
		//GET PET INFO
		$tsql = "SELECT b.character_pet_id'id', a.`name`, a.swfName, a.clsName, b.level, b.xp, b.equipped  FROM `pet_list` a, character_pet b WHERE b.pet_id = a.pet_id AND b.character_pet_id =:insertId LIMIT 1";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':insertId', $insertId, PDO::PARAM_STR);
		$stmt->execute();
		$char_pet = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$rs = $char_pet[0];
		$hash = client_library::getHash($seskey, $rs['id'].",".$rs['swfName'].",".$rs['clsName'].",".$rs['xp'].",".$rs['level']);
		
		$char_pet[0] += ['hash' => $hash];
		
		$result = array('status' =>1, 'error' => 0, 'result' => $char_pet[0]);
		
		return $result;
	}

	public static function getCharacterProfileById($seskey, $friendId, $isRecruit = false){
		try{
			try{
				$pdo = db_db::getConnection();
				
				//GET DATA
				$tsql = "SELECT * FROM `character_list` WHERE `character_id` =:char_id LIMIT 1";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$tsql = "SELECT GROUP_CONCAT(`character_equipped_skills`)`character_equipped_skills` FROM `character_equipped_skills` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_equipped_skills = "";
				if($stmt->rowCount() > 0){
					$character_equipped_skills_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_equipped_skills = $character_equipped_skills_res[0]['character_equipped_skills'];
				}
				
				$tsql = "SELECT `character_equipped_weapon` FROM `character_equipped_weapon` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_equipped_weapon = "";
				if($stmt->rowCount() > 0){
					$character_equipped_weapon_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_equipped_weapon = $character_equipped_weapon_res[0]['character_equipped_weapon'];
				}
				
				$tsql = "SELECT `character_equipped_body_set` FROM `character_equipped_body_set` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_equipped_body_set = "";
				if($stmt->rowCount() > 0){
					$character_equipped_body_set_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_equipped_body_set = $character_equipped_body_set_res[0]['character_equipped_body_set'];
				}
				
				$tsql = "SELECT `character_equipped_back_item` FROM `character_equipped_back_item` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_equipped_back_item = "";
				if($stmt->rowCount() > 0){
					$character_equipped_back_item_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_equipped_back_item = $character_equipped_back_item_res[0]['character_equipped_back_item'];
				}
				
				$tsql = "SELECT GROUP_CONCAT(`character_body_set`)`character_body_set` FROM `character_body_set` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_body_set = "";
				if($stmt->rowCount() > 0){
					$character_body_set_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_body_set = $character_body_set_res[0]['character_body_set'];
				}
				
				$tsql = "SELECT GROUP_CONCAT(`character_weapon`)`character_weapon` FROM `character_weapon` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_weapon = "";
				if($stmt->rowCount() > 0){
					$character_weapon_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_weapon = $character_weapon_res[0]['character_weapon'];
				}
				
				$tsql = "SELECT GROUP_CONCAT(`character_skill`)`character_skill` FROM `character_skill` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_skill = "";
				if($stmt->rowCount() > 0){
					$character_skill_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_skill = $character_skill_res[0]['character_skill'];
				}
				
				$tsql = "SELECT GROUP_CONCAT(`character_back_item`)`character_back_item` FROM `character_back_item` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_back_item = "";
				if($stmt->rowCount() > 0){
					$character_back_item_res = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$character_back_item = $character_back_item_res[0]['character_back_item'];
				}
				
				$tsql = "SELECT * FROM `character_body_style` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1) LIMIT 1";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_body_style = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$tsql = "SELECT * FROM `character_element_points` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1) LIMIT 1";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_element_points = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$tsql = "SELECT GROUP_CONCAT(`character_inv_hair`)`character_inv_hair` FROM `character_inv_hair` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_inv_hair = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$tsql = "SELECT GROUP_CONCAT(CONCAT(mission_id, ':' , success, ':' , failure, ':' , time_stamp))'character_mission' FROM `character_mission` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_mission = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$tsql = "SELECT GROUP_CONCAT(`character_item`)`character_item` FROM `character_item` WHERE `character_id` = (SELECT `character_id` FROM `character_list` WHERE `character_id` =:char_id LIMIT 1)";
				$stmt = $pdo->prepare($tsql);
				$stmt->bindParam(':char_id', $friendId, PDO::PARAM_STR);
				$stmt->execute();
				$character_item = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
								
				$result = array('status' =>1, 'error' => 0,
								'result' => array(
												  'character_id' => $character_list[0]['character_id'],
												  'character_name' => $character_list[0]['character_name'],
												  'character_level' => $character_list[0]['character_level'],
												  'character_xp' => $character_list[0]['character_xp'],
												  'character_rank' => $character_list[0]['character_rank'],
												  'character_gold' => $character_list[0]['character_gold'],
												  'character_hp' => $character_list[0]['character_hp'],
												  'character_cp' => $character_list[0]['character_cp'],
												  'character_armor' => $character_list[0]['character_armor'],
												  'character_gender' => $character_list[0]['character_gender'],
												  'character_equipped_skills' => $character_equipped_skills,
												  'character_equipped_weapon' => $character_equipped_weapon,
												  'character_equipped_body_set' => $character_equipped_body_set,
												  'character_equipped_back_item' => $character_equipped_back_item,
												  'character_equipped_accessory' => '0',
												  'character_body_set' => $character_body_set,
												  'character_weapon' => $character_weapon,
												  'character_skill' => $character_skill,
												  'character_back_item' => $character_back_item,
												  'character_npc' => '',
												  'character_magatama' => '',
												  'character_friends' => '',
												  'character_item' => $character_item[0]['character_item'],
												  'character_accessory' => '',
												  'character_ninja_essence' => '',
												  'character_material' => '',
												  'character_hair_color' => $character_body_style[0]['character_hair_color'],
												  'character_skin_color' => $character_body_style[0]['character_skin_color'],
												  'character_face' => $character_body_style[0]['character_face'],
												  'character_hair' => $character_body_style[0]['character_hair'],
												  'character_fire' => $character_element_points[0]['character_fire'],
												  'character_water' => $character_element_points[0]['character_water'],
												  'character_wind' => $character_element_points[0]['character_wind'],
												  'character_earth' => $character_element_points[0]['character_earth'],
												  'character_lightning' => $character_element_points[0]['character_lightning'],
												  'character_taijutsu' => $character_element_points[0]['character_taijutsu'],
												  'character_genjutsu' => $character_element_points[0]['character_genjutsu'],
												  'character_summon' => $character_element_points[0]['character_summon'],
												  'character_control' => '', //SA LVL 60 CLASS
												  'character_bloodline' => '299',
												  'character_inv_hair' => $character_inv_hair[0]['character_inv_hair'],
												  'character_common_currency' => '',
												  'character_mission' => $character_mission[0]['character_mission'],
												  'expiry_data' => array(
																		'remove_inv_arr' => array(),
																		'add_inv_arr' => [],
																		'equip_arr' => [],
																		'current_expiry_arr' => [],
																		'remove_equip_arr' => [],
																		'expiry_pet_data' => [],
																		'expiry_hash' => '8b47fef9c6fa'
																		),
												  'daily' => null,
												  'character_hash' => 'b507d2702514',
												  'character_pre_hash' => '691d1042661d',
												  'recruit_friend_data' => [],
												  'character_inv_slots' => array(
																				'weapon' => '0',
																				'body_set' => '0',
																				'item' => '0',
																				'essence' => '0',
																				'material' => '0',
																				'back' => '0',
																				'accessory' => '0',
																				'pet' => '0'),
												   'senjutsu_spirit' => 100000000,
												   'character_trade_item' => '',
												   'character_equipped_trade_weapon' => '',
												   'character_equipped_trade_back_item' => '',
												   'character_equipped_trade_body_set' => ''
												  ));
				
				return $result;
			}catch(Exception $e){
				$result = array('status' =>0,'error' =>105, 'error_code' => $e->getMessage());
				return $result;
			}
		}catch(Exception $e){
			$result = array('status' =>0,'error' =>105, 'error_code' =>  $e->getMessage());
			return $result;
		}
		
		return $result;
	}

	public static function recruitNpc($seskey, $npcId, $isRecruit){
		$pdo = db_db::getConnection();
		$tsql = "SELECT tokens FROM `account_tokens` WHERE `acc_id` = (SELECT `acc_id` FROM `account_tbl` WHERE `sessionkey` =:seskey LIMIT 1) LIMIT 1";
		$stmt = $pdo->prepare($tsql);
		$stmt->bindParam(':seskey', $seskey, PDO::PARAM_STR);
		$stmt->execute();
		//ACCOUNT VIP = 2, FREE 1
		$resq = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$token = $resq[0]['tokens'];
		$str = "1"+$npcId+$token;
		//$signature = client_library::getHash($seskey, $str);
		$signature = 'b8b021f1d6f6';
		$result = array('status' =>1, 'error' => 0, 'signature' => $signature, 'npc_id' => $npcId, 'old_recruit' => false);
		return $result;
	}

	public static function resetAPbyToken($seskey, $token){
		$results = array('status' => 1, 'error' => 0, 'result' => null);
		return $results;
	}

	public static function resetSkill($seskey, $skilltype){
		//remove skill from db
		$results = array('status' => 1, 'error' => 0, 'result' => null);
		return $results;
	}

	public static function startHunting($seskey, $dataloh, $rand, $hash){
		$results = array('status' => 1, 'error' => 0, 'result' => null);
		
		return $results;
	}

	public static function finishHunting($seskey, $dataloh, $rand, $hash){
		$results = array('status' => 1, 'error' => 0, 'result' => null);
		
		return $results;
	}

	public static function startMission($seskey, $mission_id, $hash){
		$results = array('status' => 1, 'error' => 0);
		return $results;
	}

	public static function finishMission($seskey, $mission_id, $item, $times, $hash){
		$results = array('status' => 1, 'error' => 0);
		return $results;
	}

	
}

?>