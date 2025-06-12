<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'label_attr' => [
                    'for' => 'orderProduct_quantity',
                ],
                'data' => 0,
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'id' => 'orderProduct_quantity',
                    'name' => 'orderProduct[quantity]',
                ]
            ])    
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderProduct::class,
        ]);
    }
}
