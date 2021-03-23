<?php 
if(!class_exists('Ph_Fedex_WeightPacketUtil')){
	class Ph_Fedex_WeightPacketUtil{		
		public function pack_items_into_weight_box($items,	$max_weight){
			$boxes		=	array();
			$unpacked	=	array();
			foreach($items as $item){
				$fitted			=	false;
				$item_weight	=	$item['weight'];
				foreach($boxes as $box_key	=>	$box){
					if(($max_weight-$box['weight'])	>=	$item_weight){
						$boxes[$box_key]['weight']				=	$boxes[$box_key]['weight']+$item_weight;
						$boxes[$box_key]['items'][]				=	$item['data'];
						$fitted=true;
						break;
					}
				}
				if(!$fitted){
					if($item_weight	<=	$max_weight){
						$boxes[]	=	array(
							'weight'				=>	$item_weight,
							'items'					=>	array($item['data']),
						);
					}else{
						$unpacked[]	=	array(
							'weight'				=>	$item_weight,
							'items'					=>	array($item['data']),
						);
					}					
				}
			}
			$result	=	new Ph_Fedex_WeightPackResult();
			$result->set_packed_boxes($boxes);
			$result->set_unpacked_items($unpacked);
			return $result;
		}
		
		public function pack_all_items_into_one_box($items){
			$boxes			=	array();
			$total_weight	=	0;
			$box_items			=	array();
			foreach($items as $item){
				$total_weight	=	$total_weight	+	$item['weight'];
				$box_items[]		=	$item['data'];
			}
			$boxes[]	=	array(
				'weight'	=>	$total_weight,
				'items'		=>	$box_items
			);
			$result	=	new Ph_Fedex_WeightPackResult();
			$result->set_packed_boxes($boxes);
			return $result;
		}
	}
}