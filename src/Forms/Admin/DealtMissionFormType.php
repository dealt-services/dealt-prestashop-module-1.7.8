<?php

namespace DealtModule\Forms\Admin;

use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\TextWithLengthCounterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DealtMissionFormType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('id_mission', HiddenType::class)
      ->add('title_mission', TextType::class, [
        'label' => 'Title',
        'required' => true
      ])
      ->add('dealt_id_mission', TextWithLengthCounterType::class, [
        'label' => 'Mission id',
        'required' => true,
        'max_length' => 36,
      ])
      ->add('ids_category', CategoryChoiceTreeType::class, [
        'multiple' => true,
        'required' => false,
        'label' => 'Categories',
        'disabled_values' => [] // should disable __dealt__ internal category
      ]);
  }
}
