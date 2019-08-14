<?php 
class Ecomwise_MshopB2B_Block_Renderers_mamutContactRenderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
	public function render(Varien_Object $row)
	{
		
		$contactGroups = $row['mamut_contact_groups'];
		$contactGroupsHtml = '';
		$groups = explode(',',$contactGroups);
		
		foreach ($groups as $group){
			if($group){
				$contactGroupsHtml .=  '<div style="font-size: 90%; margin-bottom: 8px; border-bottom: 1px dotted #bcbcbc;">' . $group . '</div>';
			}
		}
		return $contactGroupsHtml;
	}
}

