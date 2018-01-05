<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 05/01/18
 * Time: 18:02.
 */

namespace AppBundle\Form;

use AppBundle\Controller\Api\ApiController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountValidateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $api = new ApiController();

        $info['broker'] = $api->doRequest(
            'GET',
            $this->container->getParameter('api_target').'/broker-list'
        );

        $brokerList = [];
        foreach ($info['broker']['data']['data'] as $key) {
            $brokerList[$key['broker_name']] = $key['broker_id'];
        }

        $builder
            ->add('username')
            ->add('password');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
    }
}
