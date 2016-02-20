<?php

namespace Bolt\Extension\Bolt\Members\Form\Type;

use Bolt\Extension\Bolt\Members\Config\Config;
use Bolt\Translation\Translator as Trans;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Login type.
 *
 * Copyright (C) 2014-2016 Gawain Lynch
 *
 * @author    Gawain Lynch <gawain.lynch@gmail.com>
 * @copyright Copyright (c) 2014-2016, Gawain Lynch
 * @license   https://opensource.org/licenses/MIT MIT
 */
class LoginType extends AbstractType
{
    /** @var Config */
    protected $config;

    /**
     * Constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',       EmailType::class,   [
                'label'       => Trans::__($this->config->getLabel('email')),
                'data'        => $this->getData($options, 'email'),
                'attr'        => [
                    'placeholder' => $this->config->getPlaceholder('email'),
                ],
                'constraints' => new Assert\Email([
                    'message' => 'The address "{{ value }}" is not a valid email.',
                    'checkMX' => true,
                ]),
            ])
            ->add('password', PasswordType::class, [
                'label'       => Trans::__($this->config->getLabel('password_first')),
                'data'        => null,
                'attr'        => [
                    'placeholder' => $this->config->getPlaceholder('password_first'),
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 6]),
                ],
            ])
            ->add('submit',   SubmitType::class, [
                'label'   => Trans::__($this->config->getLabel('login')),
            ])
        ;
        $this->addProviderButtons($builder);
    }

    public function getName()
    {
        return 'login';
    }

    /**
     * Add any configured provider buttons to the form.
     *
     * @param FormBuilderInterface $builder
     */
    private function addProviderButtons(FormBuilderInterface $builder)
    {
        foreach ($this->config->getEnabledProviders() as $provider) {
            $name = strtolower($provider->getName());
            if ($name === 'local') {
                continue;
            }
            $builder->add(
                $name, ButtonType::class, [
                    'label' => $provider->getLabelSignIn(),
                    'attr'  => [
                        'class' => $this->getCssClass($name),
                        'href'  => sprintf('/%s/login/process?provider=%s', $this->config->getUrlAuthenticate(), $provider->getName()),
                    ],
                ]
            );
        }
    }

    /**
     * Determine a button's CSS class
     *
     * @param string $name
     *
     * @return string
     */
    private function getCssClass($name)
    {
        return $this->config->getAddOn('zocial') ? "members-oauth-provider zocial $name" : "members-oauth-provider $name";
    }
}
