<?php
class Infinity_ProductAttributes_Model_Resource_Setup extends Mage_Catalog_Model_Resource_Setup
{
    const ATTRIBUTE_CODE_MAX_LENGTH                 = 100;

    protected function _validateAttributeData($data)
    {
        $attributeCodeMaxLength = self::ATTRIBUTE_CODE_MAX_LENGTH;

        if (isset($data['attribute_code']) &&
            !Zend_Validate::is($data['attribute_code'], 'StringLength', array('max' => $attributeCodeMaxLength)))
        {
            throw Mage::exception('Mage_Eav',
                Mage::helper('eav')->__('Maximum length of attribute code must be less then %s symbols', $attributeCodeMaxLength)
            );
        }

        return true;
    }

    public function addAttributeSet($entityTypeId, $name, $sortOrder = null, $skeletonName="Default")
    {
        $helper = Mage::helper('core');
        $name = $helper->stripTags($name);
        $name = trim($name);

        $setId = $this->getAttributeSet($entityTypeId, $name, 'attribute_set_id');
        $entityTypeId = $this->getEntityTypeId($entityTypeId);
        /* @var $model Mage_Eav_Model_Entity_Attribute_Set */
        $model  = Mage::getModel('eav/entity_attribute_set')
            ->setEntityTypeId($entityTypeId);
            
        $skeletonSetId = $this->getAttributeSet($entityTypeId, $skeletonName, 'attribute_set_id');

        if (!$setId) {
            $model->setAttributeSetName($name);
        } else {
            $model->load($setId);
            $data = array(
                'entity_type_id'        => $entityTypeId,
                'attribute_set_name'    => $name,
                'sort_order'            => $this->getAttributeSetSortOrder($entityTypeId, $sortOrder),
            );
            $model->organizeData($data);
        }

        $model->validate();
        $model->save();
        $model->initFromSkeleton($skeletonSetId);

        $model->save();
    }
}